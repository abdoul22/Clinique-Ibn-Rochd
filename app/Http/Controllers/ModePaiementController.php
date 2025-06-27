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

        // Entrées : groupées par type
        $entrees = $paiements->groupBy('type')->map(function ($rows) {
            return $rows->sum('montant');
        });

        // Sorties : dépenses validées
        $depenses = \App\Models\EtatCaisse::whereNotNull('depense')->get();
        $sorties = [
            'espèces' => $depenses->sum('depense'), // on suppose qu'elles sont toujours en espèces
            // si tu veux affecter à d'autres types, il faut ajouter un champ "type" dans EtatCaisse
        ];

        // Total par type
        $modes = ['espèces', 'bankily', 'masrivi', 'sedad'];
        $data = [];
        $totalGlobal = 0;

        foreach ($modes as $mode) {
            $entree = $entrees[$mode] ?? 0;
            $sortie = $sorties[$mode] ?? 0;
            $net = $entree - $sortie;
            $totalGlobal += $net;

            $data[] = [
                'mode' => ucfirst($mode),
                'entree' => $entree,
                'sortie' => $sortie,
                'solde' => $net,
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
