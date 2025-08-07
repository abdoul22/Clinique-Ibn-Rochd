<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\ModePaiement;
use App\Models\Depense;
use Illuminate\Http\Request;
use App\Models\EtatCaisse; // Added this import

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

        $totalCaisse = EtatCaisse::sum('recette'); // Utiliser EtatCaisse au lieu de Caisse

        // Calculer les dépenses (exclure les crédits personnel car ils sont payés par déduction salaire)
        $totalDepenses = Depense::where('rembourse', false)->sum('montant');

        // Ajouter SEULEMENT les paiements de crédits d'assurance comme entrées de trésorerie (vraies recettes)
        $paiementsCreditsAssurance = ModePaiement::whereNull('caisse_id')
            ->where('source', 'credit_assurance')
            ->sum('montant');
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
            $entree = EtatCaisse::whereHas('caisse.mode_paiements', function ($query) use ($type) {
                $query->where('type', $type);
            })->sum('recette'); // Utiliser la recette réelle d'EtatCaisse

            // Ajouter SEULEMENT les paiements de crédits d'assurance (vraies recettes)
            $entree += ModePaiement::where('type', $type)
                ->whereNull('caisse_id')
                ->where('source', 'credit_assurance')
                ->sum('montant');

            // Calculer les sorties (dépenses) pour ce mode de paiement
            $sortie = Depense::where('mode_paiement_id', $type)
                ->where('rembourse', false)
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

        // Suppression de l'exclusion : on affiche toutes les dépenses
        $queryDepenses->where('rembourse', false);

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
        $queryRecettes = EtatCaisse::with(['caisse.mode_paiements', 'caisse.patient', 'medecin', 'caisse.examen']);

        // Filtres pour les recettes
        if ($request->filled('date_debut')) {
            $queryRecettes->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $queryRecettes->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('mode_paiement')) {
            $queryRecettes->whereHas('caisse.mode_paiements', function ($query) use ($request) {
                $query->where('type', $request->mode_paiement);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $queryRecettes->where(function ($q) use ($search) {
                $q->whereHas('caisse', function ($caisseQuery) use ($search) {
                    $caisseQuery->where('numero_facture', 'like', "%{$search}%")
                        ->orWhereHas('patient', function ($patientQuery) use ($search) {
                            $patientQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
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
            $modePaiement = $recette->caisse?->mode_paiements?->first();
            $operations->push([
                'type' => 'recette',
                'date' => $recette->created_at,
                'description' => "Facture #{$recette->caisse?->numero_facture} - " . ($recette->caisse?->patient ? $recette->caisse->patient->first_name . ' ' . $recette->caisse->patient->last_name : 'Patient inconnu'),
                'montant' => $recette->recette, // Utiliser la recette réelle
                'mode_paiement' => $modePaiement ? $modePaiement->type : 'N/A',
                'source' => 'caisse',
                'operation' => 'entree',
                'data' => $recette
            ]);
        }

        // Ajouter les paiements de crédits d'assurance et personnel comme entrées
        // COMMENTÉ : Les paiements de crédits ne doivent apparaître que dans le module /credits
        // foreach ($paiementsCredits as $paiement) {
        //     $isPersonnel = $paiement->source === 'credit_personnel';
        //     $operations->push([
        //         'type' => $isPersonnel ? 'paiement_credit_personnel' : 'paiement_credit_assurance',
        //         'date' => $paiement->created_at,
        //         'description' => $isPersonnel
        //             ? "Paiement crédit personnel - {$paiement->type}"
        //             : "Paiement crédit assurance - {$paiement->type}",
        //         'montant' => $paiement->montant,
        //         'mode_paiement' => $paiement->type,
        //         'source' => $isPersonnel ? 'credit_personnel' : 'credit_assurance',
        //         'operation' => 'entree',
        //         'data' => $paiement
        //     ]);
        // }

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
        $totalRecettes = $recettes->sum('recette'); // Utiliser la recette réelle

        // COMMENTÉ : Les paiements de crédits ne sont plus affichés dans l'historique général
        // Séparer les vrais paiements de crédits (assurance) des remboursements de dettes (personnel)
        // $totalPaiementsCreditsAssurance = $paiementsCredits->where('source', 'credit_assurance')->sum('montant');
        // $totalRemboursementsPersonnel = $paiementsCredits->where('source', 'credit_personnel')->sum('montant');

        // $totalRecettesAvecCredits = $totalRecettes + $totalPaiementsCreditsAssurance;
        $totalOperations = $totalRecettes - $totalDepenses;

        $modes = ModePaiement::all();

        return view('modepaiements.historique', [
            'historiquePaginated' => $historiquePaginated,
            'totalDepenses' => $totalDepenses,
            'totalRecettes' => $totalRecettes,
            'totalRecettesAvecCredits' => $totalRecettes, // This total is no longer relevant as credits are not shown
            'totalOperations' => $totalOperations,
            'totalPaiementsCreditsAssurance' => 0, // No longer applicable
            'totalRemboursementsPersonnel' => 0, // No longer applicable
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
