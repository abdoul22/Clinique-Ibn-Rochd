<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\ModePaiement;
use App\Models\Depense;
use Illuminate\Http\Request;

class ModePaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paiements = ModePaiement::with('caisse')->latest()->paginate(10);

        return view('modepaiements.index', compact('paiements'));
    }

    public function dashboard(Request $request)
    {
        // Récupérer les types de modes de paiement uniques
        $typesModes = ModePaiement::distinct()->pluck('type')->toArray();

        $totalCaisse = Caisse::sum('total');

        // Calculer les dépenses (exclure les crédits personnel car ils sont payés par déduction salaire)
        $totalDepenses = Depense::where(function ($q) {
            $q->whereNull('credit_id')
                ->orWhereHas('credit', function ($creditQuery) {
                    $creditQuery->where('source_type', '!=', \App\Models\Personnel::class);
                });
        })->sum('montant');

        // Ajouter les paiements de crédits d'assurance comme entrées de trésorerie
        $paiementsCreditsAssurance = ModePaiement::whereNull('caisse_id')->sum('montant');
        $totalCaisse += $paiementsCreditsAssurance;

        $soldeDisponible = $totalCaisse - $totalDepenses;

        // Préparer les données pour la vue
        $data = [];
        $chartLabels = [];
        $chartEntrees = [];
        $chartSorties = [];
        $chartSoldes = [];

        foreach ($typesModes as $type) {
            // Récupérer le montant total pour ce type de mode de paiement
            $montantTotal = ModePaiement::where('type', $type)->sum('montant');

            // Calculer les entrées (recettes) pour ce mode de paiement
            $entree = Caisse::whereHas('mode_paiements', function ($query) use ($type) {
                $query->where('type', $type);
            })->sum('total');

            // Ajouter les paiements de crédits d'assurance pour ce mode de paiement
            $entree += ModePaiement::where('type', $type)
                ->whereNull('caisse_id')
                ->sum('montant');

            // Calculer les sorties (dépenses) pour ce mode de paiement
            $sortie = Depense::where('mode_paiement_id', $type)
                ->where(function ($q) {
                    $q->whereNull('credit_id')
                        ->orWhereHas('credit', function ($creditQuery) {
                            $creditQuery->where('source_type', '!=', \App\Models\Personnel::class);
                        });
                })
                ->sum('montant');

            $solde = $entree - $sortie;

            $data[] = [
                'mode' => ucfirst($type),
                'entree' => $entree,
                'sortie' => $sortie,
                'solde' => $solde
            ];

            $chartLabels[] = ucfirst($type);
            $chartEntrees[] = $entree;
            $chartSorties[] = $sortie;
            $chartSoldes[] = $solde;
        }

        $totalGlobal = $soldeDisponible;

        return view('modepaiements.dashboard', compact(
            'data',
            'totalGlobal',
            'chartLabels',
            'chartEntrees',
            'chartSorties',
            'chartSoldes'
        ));
    }

    public function historique(Request $request)
    {
        // Récupérer les dépenses
        $queryDepenses = Depense::with(['modePaiement', 'credit']);

        // Exclure les crédits personnel (ils sont payés par déduction salaire, pas par sortie de caisse)
        $queryDepenses->where(function ($q) {
            $q->whereNull('credit_id')
                ->orWhereHas('credit', function ($creditQuery) {
                    $creditQuery->where('source_type', '!=', \App\Models\Personnel::class);
                });
        });

        // Filtres pour les dépenses
        if ($request->filled('date_debut')) {
            $queryDepenses->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $queryDepenses->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('mode_paiement')) {
            $queryDepenses->where('mode_paiement_id', $request->mode_paiement);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $queryDepenses->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('montant', 'like', "%{$search}%");
            });
        }

        $depenses = $queryDepenses->orderBy('created_at', 'desc')->get();

        // Récupérer les recettes (entrées) de la caisse
        $queryRecettes = Caisse::with(['mode_paiements', 'patient', 'medecin', 'examen']);

        // Filtres pour les recettes
        if ($request->filled('date_debut')) {
            $queryRecettes->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $queryRecettes->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('mode_paiement')) {
            $queryRecettes->whereHas('mode_paiements', function ($query) use ($request) {
                $query->where('type', $request->mode_paiement);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $queryRecettes->where(function ($q) use ($search) {
                $q->where('numero_facture', 'like', "%{$search}%")
                    ->orWhere('total', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('nom', 'like', "%{$search}%");
                    });
            });
        }

        $recettes = $queryRecettes->orderBy('created_at', 'desc')->get();

        // Récupérer les paiements de crédits d'assurance
        $queryPaiementsCredits = ModePaiement::whereNull('caisse_id');

        // Filtres pour les paiements de crédits
        if ($request->filled('date_debut')) {
            $queryPaiementsCredits->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $queryPaiementsCredits->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('mode_paiement')) {
            $queryPaiementsCredits->where('type', $request->mode_paiement);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $queryPaiementsCredits->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                    ->orWhere('montant', 'like', "%{$search}%");
            });
        }

        $paiementsCredits = $queryPaiementsCredits->orderBy('created_at', 'desc')->get();

        // Combiner et trier les opérations
        $operations = collect();

        // Ajouter les dépenses comme sorties
        foreach ($depenses as $depense) {
            $operations->push([
                'type' => 'depense',
                'date' => $depense->created_at,
                'description' => $depense->nom,
                'montant' => $depense->montant,
                'mode_paiement' => $depense->mode_paiement_id,
                'source' => $depense->source,
                'operation' => 'sortie',
                'data' => $depense
            ]);
        }

        // Ajouter les recettes comme entrées
        foreach ($recettes as $recette) {
            $modePaiement = $recette->mode_paiements->first();
            $operations->push([
                'type' => 'recette',
                'date' => $recette->created_at,
                'description' => "Facture #{$recette->numero_facture} - " . ($recette->patient ? $recette->patient->nom : 'Patient inconnu'),
                'montant' => $recette->total,
                'mode_paiement' => $modePaiement ? $modePaiement->type : 'N/A',
                'source' => 'caisse',
                'operation' => 'entree',
                'data' => $recette
            ]);
        }

        // Ajouter les paiements de crédits d'assurance comme entrées
        foreach ($paiementsCredits as $paiement) {
            $operations->push([
                'type' => 'paiement_credit_assurance',
                'date' => $paiement->created_at,
                'description' => "Paiement crédit assurance - {$paiement->type}",
                'montant' => $paiement->montant,
                'mode_paiement' => $paiement->type,
                'source' => 'credit_assurance',
                'operation' => 'entree',
                'data' => $paiement
            ]);
        }

        // Trier par date décroissante
        $operations = $operations->sortByDesc('date');

        // Pagination manuelle
        $page = $request->get('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        $historiquePaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $operations->slice($offset, $perPage),
            $operations->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Calculer les totaux
        $totalDepenses = $depenses->sum('montant');
        $totalRecettes = $recettes->sum('total');
        $totalPaiementsCredits = $paiementsCredits->sum('montant');
        $totalRecettesAvecCredits = $totalRecettes + $totalPaiementsCredits;
        $totalOperations = $totalRecettesAvecCredits - $totalDepenses;

        $modes = ModePaiement::all();

        return view('modepaiements.historique', [
            'historiquePaginated' => $historiquePaginated,
            'totalDepenses' => $totalDepenses,
            'totalRecettes' => $totalRecettes,
            'totalRecettesAvecCredits' => $totalRecettesAvecCredits,
            'totalOperations' => $totalOperations,
            'modes' => $modes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $caisses = Caisse::latest()->get(); // Pour afficher dans la liste déroulante
        return view('modepaiements.create', compact('caisses'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'caisse_id' => 'required|exists:caisses,id',
            'type' => 'required|in:espèces,bankily,masrivi',
            'montant' => 'required|numeric|min:0',
        ]);

        ModePaiement::create($validated);

        return redirect()->back()->with('success', 'Paiement enregistré.');
    }


    /**
     * Display the specified resource.
     */
    public function show(ModePaiement $modePaiement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ModePaiement $modePaiement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModePaiement $modePaiement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModePaiement $modePaiement)
    {
        //
    }
}
