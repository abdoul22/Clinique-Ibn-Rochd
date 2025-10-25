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
    public function index(Request $request)
    {
        // Construire la requête de base
        $query = ModePaiement::with('caisse', 'depense');

        // Récupérer les types de modes de paiement pour le filtre
        $typesModes = ModePaiement::getTypes();

        // Filtrage par type de paiement
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtrage par source (facture, depense, part_medecin, credit_assurance)
        if ($request->filled('source')) {
            if ($request->source === 'facture') {
                // Factures = ceux qui ont un caisse_id
                $query->whereNotNull('caisse_id');
            } elseif ($request->source === 'depense') {
                $query->where('source', 'depense');
            } elseif ($request->source === 'part_medecin') {
                $query->where('source', 'part_medecin');
            } elseif ($request->source === 'credit_assurance') {
                $query->where('source', 'credit_assurance');
            }
        }

        // Filtrage par période
        $period = $request->get('period', null);

        if ($period === 'day' && $request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $query->whereBetween('created_at', [$start, $end]);
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->whereYear('created_at', $parts[0])
                    ->whereMonth('created_at', $parts[1]);
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('created_at', [$request->date_start, $request->date_end]);
        }

        // Cloner la requête AVANT la pagination pour calculer les totaux globaux
        $queryAllResults = clone $query;
        $allResults = $queryAllResults->get();

        // Calculer les totaux pour TOUS les résultats (avant pagination)
        $totalPaiements = $allResults->sum('montant');
        $totalEspeces = $allResults->where('type', 'espèces')->sum('montant');
        $totalNumeriques = $allResults->whereIn('type', ['bankily', 'masrivi', 'sedad'])->sum('montant');
        $totalDepenses = abs($allResults->where('source', 'depense')->sum('montant'));

        // Calculer Part Médecin avec les mêmes filtres de date
        $queryPartMedecin = \App\Models\EtatCaisse::where('validated', true);
        if ($period === 'day' && $request->filled('date')) {
            $queryPartMedecin->whereDate('created_at', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $queryPartMedecin->whereBetween('created_at', [$start, $end]);
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $queryPartMedecin->whereYear('created_at', $parts[0])
                    ->whereMonth('created_at', $parts[1]);
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $queryPartMedecin->whereYear('created_at', $request->year);
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            $queryPartMedecin->whereBetween('created_at', [$request->date_start, $request->date_end]);
        }
        $totalPartMedecin = $queryPartMedecin->sum('part_medecin');

        // Paginer les résultats
        $paiements = $query->latest()->paginate(10)->withQueryString();

        return view('modepaiements.index', compact('paiements', 'typesModes', 'totalPaiements', 'totalEspeces', 'totalNumeriques', 'totalDepenses', 'totalPartMedecin'));
    }

    public function dashboard(Request $request)
    {
        // Récupérer les types de modes de paiement uniques (avec fallback)
        $typesModes = ModePaiement::getTypes();

        // Gérer le filtrage par période
        $period = $request->get('period', null);
        $dateConstraints = $this->getDateConstraints($request, $period);

        // Ne compter que les recettes liées à des factures (caisse_id non null)
        $queryTotalCaisse = EtatCaisse::whereNotNull('caisse_id');
        $this->applyDateFilter($queryTotalCaisse, $dateConstraints);
        $totalCaisse = $queryTotalCaisse->sum('recette');

        // Calculer les dépenses (exclure les crédits personnel car ils sont payés par déduction salaire)
        $queryTotalDepenses = Depense::where('rembourse', false);
        $this->applyDateFilter($queryTotalDepenses, $dateConstraints);
        $totalDepenses = $queryTotalDepenses->sum('montant');

        // Ajouter SEULEMENT les paiements de crédits d'assurance comme entrées de trésorerie (vraies recettes)
        $queryPaiementsCreditsAssurance = ModePaiement::whereNull('caisse_id')
            ->where('source', 'credit_assurance');
        $this->applyDateFilter($queryPaiementsCreditsAssurance, $dateConstraints);
        $paiementsCreditsAssurance = $queryPaiementsCreditsAssurance->sum('montant');
        $totalCaisse += $paiementsCreditsAssurance;

        $soldeDisponible = $totalCaisse - $totalDepenses;

        // Préparer les données pour la vue
        $data = [];
        $chartLabels = [];
        $chartEntrees = [];
        $chartSorties = [];
        $chartSoldes = [];

        foreach ($typesModes as $type) {
            // Calculer les entrées (recettes) pour ce mode de paiement
            $queryEntree = EtatCaisse::whereNotNull('caisse_id')->whereHas('caisse.mode_paiements', function ($query) use ($type) {
                $query->where('type', $type);
            });
            $this->applyDateFilter($queryEntree, $dateConstraints);
            $entree = $queryEntree->sum('recette'); // Utiliser la recette réelle d'EtatCaisse

            // Ajouter SEULEMENT les paiements de crédits d'assurance (vraies recettes)
            $queryEntreeCredits = ModePaiement::where('type', $type)
                ->whereNull('caisse_id')
                ->where('source', 'credit_assurance');
            $this->applyDateFilter($queryEntreeCredits, $dateConstraints);
            $entree += $queryEntreeCredits->sum('montant');

            // Calculer les sorties (dépenses) pour ce mode de paiement
            $querySortie = Depense::where('mode_paiement_id', $type)
                ->where('rembourse', false);
            $this->applyDateFilter($querySortie, $dateConstraints);
            $sortie = $querySortie->sum('montant');

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
        // Gérer le filtrage par période
        $period = $request->get('period', null);
        $dateConstraints = $this->getDateConstraints($request, $period);

        // Récupérer les dépenses
        $queryDepenses = Depense::with(['modePaiement', 'credit']);

        // Suppression de l'exclusion : on affiche toutes les dépenses
        $queryDepenses->where('rembourse', false);

        // Appliquer le filtre de date
        $this->applyDateFilter($queryDepenses, $dateConstraints);

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

        // Appliquer le filtre de date
        $this->applyDateFilter($queryRecettes, $dateConstraints);

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

        // Récupérer les paiements de crédits (assurance et personnel, hors caisse)
        $queryPaiementsCredits = ModePaiement::whereNull('caisse_id')
            ->whereIn('source', ['credit_assurance', 'credit_personnel']);

        // Appliquer le filtre de date
        $this->applyDateFilter($queryPaiementsCredits, $dateConstraints);

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

        // Ajouter les paiements de crédits comme entrées
        foreach ($paiementsCredits as $paiement) {
            $isPersonnel = $paiement->source === 'credit_personnel';
            $operations->push([
                'type' => $isPersonnel ? 'paiement_credit_personnel' : 'paiement_credit_assurance',
                'date' => $paiement->created_at,
                'description' => $isPersonnel
                    ? "Paiement crédit personnel - {$paiement->type}"
                    : "Paiement crédit assurance - {$paiement->type}",
                'montant' => $paiement->montant,
                'mode_paiement' => $paiement->type,
                'source' => $isPersonnel ? 'credit_personnel' : 'credit_assurance',
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
        $totalRecettes = $recettes->sum('recette'); // Utiliser la recette réelle

        // Totaux des paiements de crédits
        $totalPaiementsCreditsAssurance = $paiementsCredits->where('source', 'credit_assurance')->sum('montant');
        $totalRemboursementsPersonnel = $paiementsCredits->where('source', 'credit_personnel')->sum('montant');

        // Total recettes incluant les paiements de crédits
        $totalRecettesAvecCredits = $totalRecettes + $totalPaiementsCreditsAssurance + $totalRemboursementsPersonnel;
        $totalOperations = $totalRecettesAvecCredits - $totalDepenses;

        $modes = ModePaiement::all();

        return view('modepaiements.historique', [
            'historiquePaginated' => $historiquePaginated,
            'totalDepenses' => $totalDepenses,
            'totalRecettes' => $totalRecettes,
            'totalRecettesAvecCredits' => $totalRecettesAvecCredits,
            'totalOperations' => $totalOperations,
            'totalPaiementsCreditsAssurance' => $totalPaiementsCreditsAssurance,
            'totalRemboursementsPersonnel' => $totalRemboursementsPersonnel,
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
            'type' => 'required|in:espèces,bankily,masrivi,sedad',
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

    /**
     * Extraire les contraintes de date selon le type de période
     */
    private function getDateConstraints(Request $request, $period)
    {
        if ($period === 'day' && $request->filled('date')) {
            return ['type' => 'day', 'value' => $request->date];
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                return ['type' => 'range', 'start' => $start, 'end' => $end];
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                return ['type' => 'month', 'year' => $parts[0], 'month' => $parts[1]];
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            return ['type' => 'year', 'value' => $request->year];
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            return ['type' => 'range', 'start' => $request->date_start, 'end' => $request->date_end];
        }

        return null;
    }

    /**
     * Appliquer les filtres de date à une requête
     */
    private function applyDateFilter($query, $dateConstraints)
    {
        if (!$dateConstraints) {
            return;
        }

        if ($dateConstraints['type'] === 'day') {
            $query->whereDate('created_at', $dateConstraints['value']);
        } elseif ($dateConstraints['type'] === 'month') {
            $query->whereYear('created_at', $dateConstraints['year'])
                ->whereMonth('created_at', $dateConstraints['month']);
        } elseif ($dateConstraints['type'] === 'year') {
            $query->whereYear('created_at', $dateConstraints['value']);
        } elseif ($dateConstraints['type'] === 'range') {
            $query->whereBetween('created_at', [$dateConstraints['start'], $dateConstraints['end']]);
        }
    }
}
