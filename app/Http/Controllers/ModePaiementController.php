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

    public function dashboard()
    {
        $paiements = \App\Models\ModePaiement::all();

        // Entrées : groupées par type (recettes)
        $entrees = $paiements->groupBy('type')->map(function ($rows) {
            return $rows->sum('montant');
        });

        // 🔽 Sorties réelles par mode
        $sorties = [
            'espèces' => 0,
            'bankily' => 0,
            'masrivi' => 0,
            'sedad' => 0,
        ];

        // ➤ Dépenses avec mode de paiement
        foreach (\App\Models\Depense::with('mode_paiement')->get() as $depense) {
            if ($depense->mode_paiement) {
                $type = $depense->mode_paiement->type;
                $sorties[$type] += $depense->montant;
            }
        }

        // ➤ Crédits payés avec mode de paiement
        foreach (\App\Models\Credit::with('mode_paiement')->get() as $credit) {
            if ($credit->mode_paiement) {
                $type = $credit->mode_paiement->type;
                $sorties[$type] += $credit->montant_paye;
            }
        }

        // ➤ Parts médecin validées (via EtatCaisse) → ajouter uniquement si tu veux les considérer comme dépenses manuelles
        // ➤ Si tu ne passes PAS par Depense, ignore cette section (elle serait alors redondante)

        // Total net par mode
        $modes = ['espèces', 'bankily', 'masrivi', 'sedad'];
        $data = [];
        $totalGlobal = 0;

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
        }

        return view('modepaiements.dashboard', compact('data', 'totalGlobal'));
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
