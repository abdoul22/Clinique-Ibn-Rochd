<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\ModePaiement;
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
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        $paiementsQuery = \App\Models\ModePaiement::query();
        $depensesQuery = \App\Models\Depense::query();
        $creditsQuery = \App\Models\Credit::query();

        if ($period === 'day' && $date) {
            $paiementsQuery->whereDate('created_at', $date);
            $depensesQuery->whereDate('created_at', $date);
            $creditsQuery->whereDate('created_at', $date);
        } elseif ($period === 'week' && $week) {
            // $week format: 2023-W23
            $parts = explode('-W', $week);
            if (count($parts) === 2) {
                $yearW = (int)$parts[0];
                $weekW = (int)$parts[1];
                $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                $paiementsQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $depensesQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $creditsQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            }
        } elseif ($period === 'month' && $month) {
            // $month format: 2023-06
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                $yearM = (int)$parts[0];
                $monthM = (int)$parts[1];
                $paiementsQuery->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
                $depensesQuery->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
                $creditsQuery->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
            }
        } elseif ($period === 'year' && $year) {
            $paiementsQuery->whereYear('created_at', $year);
            $depensesQuery->whereYear('created_at', $year);
            $creditsQuery->whereYear('created_at', $year);
        } elseif ($period === 'range' && $dateStart && $dateEnd) {
            $paiementsQuery->whereBetween('created_at', [$dateStart, $dateEnd]);
            $depensesQuery->whereBetween('created_at', [$dateStart, $dateEnd]);
            $creditsQuery->whereBetween('created_at', [$dateStart, $dateEnd]);
        }

        $paiements = $paiementsQuery->get();
        $depenses = $depensesQuery->get();
        $credits = $creditsQuery->get();

        $modes = ['espèces', 'bankily', 'masrivi', 'sedad'];
        $entrees = array_fill_keys($modes, 0);
        $sorties = array_fill_keys($modes, 0);

        foreach ($paiements as $paiement) {
            if (in_array($paiement->type, $modes)) {
                $entrees[$paiement->type] += $paiement->montant;
            }
        }
        foreach ($depenses as $depense) {
            if ($depense->mode_paiement_id && in_array($depense->mode_paiement_id, $modes)) {
                $sorties[$depense->mode_paiement_id] += $depense->montant;
            }
        }
        foreach ($credits as $credit) {
            if ($credit->mode_paiement_id && in_array($credit->mode_paiement_id, $modes)) {
                $sorties[$credit->mode_paiement_id] += $credit->montant;
            }
        }

        $data = [];
        $totalGlobal = 0;
        $chartLabels = [];
        $chartEntrees = [];
        $chartSorties = [];
        $chartSoldes = [];

        foreach ($modes as $mode) {
            $entree = $entrees[$mode] ?? 0;
            $sortie = $sorties[$mode] ?? 0;
            $solde = $entree - $sortie;
            $totalGlobal += $solde;

            $data[] = [
                'mode' => ucfirst($mode),
                'entree' => $entree,
                'sortie' => $sortie,
                'solde' => $solde,
            ];
            $chartLabels[] = ucfirst($mode);
            $chartEntrees[] = $entree;
            $chartSorties[] = $sortie;
            $chartSoldes[] = $solde;
        }

        return view('modepaiements.dashboard', compact('data', 'totalGlobal', 'date', 'chartLabels', 'chartEntrees', 'chartSorties', 'chartSoldes'));
    }

    public function historique(Request $request)
    {
        // Récupérer les paramètres de période
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        // Fonctions de filtrage par période
        $filterByPeriod = function ($query) use ($period, $date, $week, $month, $year, $dateStart, $dateEnd) {
            if ($period === 'day' && $date) {
                $query->whereDate('created_at', $date);
            } elseif ($period === 'week' && $week) {
                $parts = explode('-W', $week);
                if (count($parts) === 2) {
                    $yearW = (int)$parts[0];
                    $weekW = (int)$parts[1];
                    $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                    $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                    $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                }
            } elseif ($period === 'month' && $month) {
                $parts = explode('-', $month);
                if (count($parts) === 2) {
                    $yearM = (int)$parts[0];
                    $monthM = (int)$parts[1];
                    $query->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
                }
            } elseif ($period === 'year' && $year) {
                $query->whereYear('created_at', $year);
            } elseif ($period === 'range' && $dateStart && $dateEnd) {
                $query->whereBetween('created_at', [$dateStart, $dateEnd]);
            }
        };

        // 🔼 ENTREES RÉELLES
        $recettesCaisses = \App\Models\ModePaiement::with('caisse.patient')
            ->when(true, $filterByPeriod)
            ->get()
            ->map(function ($paiement) {
                $patientNom = $paiement->caisse && $paiement->caisse->patient ? $paiement->caisse->patient->nom : 'N/A';
                return [
                    'date' => $paiement->created_at,
                    'type_operation' => 'Recette Caisse',
                    'description' => "Paiement patient: {$patientNom} - Facture #{$paiement->caisse->numero_facture}",
                    'montant' => $paiement->montant,
                    'mode_paiement' => $paiement->type,
                    'source' => 'Caisse',
                    'operation' => 'entree'
                ];
            });

        $remboursements = \App\Models\Credit::where('montant_paye', '>', 0)
            ->when(true, $filterByPeriod)
            ->get()
            ->map(function ($credit) {
                $sourceType = class_basename($credit->source_type);
                $sourceNom = $credit->source ? $credit->source->nom : 'N/A';

                return [
                    'date' => $credit->updated_at,
                    'type_operation' => "Remboursement {$sourceType}",
                    'description' => "Remboursement de {$sourceNom}",
                    'montant' => $credit->montant_paye,
                    'mode_paiement' => $credit->mode_paiement_id,
                    'source' => $sourceNom,
                    'operation' => 'entree'
                ];
            });

        // 🔽 SORTIES
        $depenses = \App\Models\Depense::query()
            ->when(true, $filterByPeriod)
            ->get()
            ->map(function ($depense) {
                return [
                    'date' => $depense->created_at,
                    'type_operation' => 'Dépense',
                    'description' => $depense->nom,
                    'montant' => $depense->montant,
                    'mode_paiement' => $depense->mode_paiement_id,
                    'source' => $depense->source,
                    'operation' => 'sortie'
                ];
            });

        $creditsAccordes = \App\Models\Credit::query()
            ->when(true, $filterByPeriod)
            ->get()
            ->map(function ($credit) {
                $sourceType = class_basename($credit->source_type);
                $sourceNom = $credit->source ? $credit->source->nom : 'N/A';

                return [
                    'date' => $credit->created_at,
                    'type_operation' => "Crédit {$sourceType}",
                    'description' => "Crédit accordé à {$sourceNom}",
                    'montant' => $credit->montant,
                    'mode_paiement' => $credit->mode_paiement_id,
                    'source' => $sourceNom,
                    'operation' => 'sortie'
                ];
            });

        // Combiner et trier par date
        $historique = $recettesCaisses
            ->concat($remboursements)
            ->concat($depenses)
            ->concat($creditsAccordes)
            ->sortByDesc('date')
            ->values();

        // Pagination manuelle sur la collection (15 par page)
        $perPage = 15;
        $page = request()->input('page', 1);
        $historiquePaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $historique->forPage($page, $perPage),
            $historique->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('modepaiements.historique', compact('historiquePaginated'));
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
