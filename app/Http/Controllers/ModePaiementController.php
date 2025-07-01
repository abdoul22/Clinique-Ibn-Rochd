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
        $date = $request->input('date');
        $paiementsQuery = \App\Models\ModePaiement::query();
        $depensesQuery = \App\Models\Depense::query();
        $creditsQuery = \App\Models\Credit::query();

        if ($date) {
            $paiementsQuery->whereDate('created_at', $date);
            $depensesQuery->whereDate('created_at', $date);
            $creditsQuery->whereDate('created_at', $date);
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

    public function historique()
    {
        $historique = collect();

        // 🔼 ENTREES RÉELLES

        // ➤ 1. Recettes des caisses (ModePaiement) - SEULES ENTREES RÉELLES
        $recettesCaisses = \App\Models\ModePaiement::with('caisse.patient')->get()->map(function ($paiement) {
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

        // ➤ 2. Remboursements de crédits (entrées)
        $remboursements = \App\Models\Credit::where('montant_paye', '>', 0)->get()->map(function ($credit) {
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

        // ➤ 3. Dépenses
        $depenses = \App\Models\Depense::all()->map(function ($depense) {
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

        // ➤ 4. Crédits accordés (personnel et assurance)
        $creditsAccordes = \App\Models\Credit::all()->map(function ($credit) {
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

        return view('modepaiements.historique', compact('historique'));
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
