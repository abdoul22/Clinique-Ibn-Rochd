<?php

namespace App\Http\Controllers;


use App\Models\Personnel;
use App\Models\Assurance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EtatCaisse;
use App\Models\Caisse;
use App\Models\Depense;
use App\Models\Examen;

class EtatCaisseController extends Controller
{
    public function index(Request $request)
    {

        $etatcaisses = EtatCaisse::with(['caisse', 'caisse.paiements', 'personnel', 'assurance', 'medecin'])
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->when($request->designation, fn($q) => $q->where('designation', 'like', "%{$request->designation}%"))
            ->when($request->personnel_id, fn($q) => $q->where('personnel_id', $request->personnel_id))
            ->latest()->paginate(10);

        $personnels = Personnel::all();
        $caisse = Caisse::all();

        // Date filtrée
        $date = $request->date;

        // Filtres dynamiques sur les modules liés
        $recetteCaisse = Caisse::when($date, fn($q) => $q->whereDate('created_at', $date))->sum('total');
        $partMedecin = Examen::when($date, fn($q) => $q->whereDate('created_at', $date))->sum('part_medecin');
        $partCabinet = Examen::when($date, fn($q) => $q->whereDate('created_at', $date))->sum('part_cabinet');
        $depense = Depense::when($date, fn($q) => $q->whereDate('created_at', $date))->sum('montant');
        $creditPersonnel = Personnel::when($date, fn($q) => $q->whereDate('created_at', $date))->sum('credit');
        $assurances = Assurance::all(); // tu peux aussi filtrer si tu as une table pivot ou des paiements liés à des dates

        // ⚪ Résumé global (sans date)
        $resumeFiltre = [
            'recette' => $recetteCaisse,
            'part_medecin' => $partMedecin,
            'part_cabinet' => $partCabinet,
            'depense' => $depense,
            'personnel_id' => $request->personnel_id,
            'credit_assurance' => Assurance::when($date, fn($q) => $q->whereDate('created_at', $date))->sum('credit'),
        ];
        // Résumé global
        $resumeGlobal = [
            'recette' => Caisse::sum('total'),
            'part_medecin' => Examen::sum('part_medecin'),
            'part_cabinet' => Examen::sum('part_cabinet'),
            'depense' => Depense::sum('montant'),
            'credit_assurance' => Assurance::sum('credit'),
        ];
        $chartFiltreData = request('date') ? [
            $resumeFiltre['recette'],
            $resumeFiltre['part_medecin'],
            $resumeFiltre['part_cabinet'],
            $resumeFiltre['depense'],
            $creditPersonnel,
        ] : [];

        $chartGlobalData = [
            $resumeGlobal['recette'],
            $resumeGlobal['part_medecin'],
            $resumeGlobal['part_cabinet'],
            $resumeGlobal['depense'],
            $creditPersonnel,
        ];

        return view('etatcaisse.index', compact(
            'etatcaisses',
            'personnels',
            'resumeFiltre',
            'resumeGlobal',
            'recetteCaisse',
            'partMedecin',
            'partCabinet',
            'depense',
            'caisse',
            'assurances',
            'chartFiltreData',
            'chartGlobalData'
        ));
    }

    public function create()
    {
        $personnels = Personnel::all();
        $assurances = Assurance::all();

        $totaux = Examen::getTotaux(); // <--- Ici

        return view('etatcaisse.create', compact('personnels', 'assurances', 'totaux'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recette' => 'required|numeric',
            'part_medecin' => 'nullable|numeric',
            'part_clinique' => 'nullable|numeric',
            'depense' => 'nullable|numeric',
            'personnel_id' => 'nullable|exists:personnels,id',
            'assurance_id' => 'nullable|exists:assurances,id',
            'caisse_id' => 'nullable|exists:caisses,id',
        ]);

        $caisse = null;
        $examen = null;

        if (!empty($validated['caisse_id'])) {
            $caisse = \App\Models\Caisse::with('examen')->find($validated['caisse_id']);
            $examen = $caisse?->examen;

            // On récupère les parts automatiquement si un examen est lié
            if ($examen) {
                $validated['part_medecin'] = $examen->part_medecin;
                $validated['part_clinique'] = $examen->part_cabinet;
            }
        }

        $etat = EtatCaisse::create([
            'designation'    => 'Nouvelle facture',
            'recette'        => $validated['recette'],
            'part_medecin'   => $validated['part_medecin'] ?? 0,
            'part_clinique'  => $validated['part_clinique'] ?? 0,
            'depense'        => $validated['depense'] ?? 0,
            'personnel_id'   => $validated['personnel_id'] ?? null,
            // ✅ Enregistre toujours l'assurance si présente
            'assurance_id'   => $validated['assurance_id'] ?? $caisse?->assurance_id,
            'caisse_id'      => $validated['caisse_id'] ?? null,

            // ✅ Corrige le medecin_id en s'assurant qu'il existe dans la caisse
            'medecin_id'     => $caisse?->medecin_id ?? null,

            'examen_id'      => $caisse?->examen_id ?? null,
        ]);

        $etat->load('personnel', 'assurance', 'medecin', 'caisse.paiements');

        return response()->json([
            'etat' => $etat,
            'view' => view('etatcaisse.partials.row', compact('etat'))->render()
        ]);
    }



    public function generateForPersonnel($id)
    {
        $personnel = Personnel::findOrFail($id);

        $etat = EtatCaisse::create([
            'designation'       => 'Crédit: ' . $personnel->nom,
            'recette'           => 0,
            'part_medecin'      => 0,
            'part_clinique'     => 0,
            'depense'           => 0,
            'personnel_id'      => $personnel->id,
            'assurance_id'      => null,
        ]);

        return redirect()->route('etatcaisse.index')->with('success', 'État du personnel généré.');
    }

    public function generateAllPersonnelCredits()
    {
        $personnels = Personnel::where('credit', '>', 0)->get();

        foreach ($personnels as $personnel) {
            EtatCaisse::create([
                'designation' => 'Crédit personnel : ' . $personnel->nom,
                'recette' => 0,
                'part_medecin' => 0,
                'part_clinique' => 0,
                'depense' => 0,
                'personnel_id' => $personnel->id,
                'assurance_id' => null,
            ]);
        }

        return back()->with('success', 'États de crédit pour tout le personnel générés avec succès.');
    }
    public function generateFromAssurance($id)
    {
        $assurance = Assurance::findOrFail($id);

        $recette = Caisse::where('assurance_id', $id)->sum('total');

        $etat = EtatCaisse::create([
            'designation'       => 'Recette: ' . $assurance->nom,
            'recette'           => $recette,
            'part_medecin'      => 0,
            'part_clinique'     => 0,
            'depense'           => 0,
            'personnel_id'      => null,
            'assurance_id'      => $assurance->id,
        ]);

        return redirect()->route('etatcaisse.index')->with('success', 'État de l’assurance généré.');
    }

    public function generateAllAssuranceEtats()
    {
        $assurances = Assurance::all();

        foreach ($assurances as $assurance) {
            EtatCaisse::create([
                'designation' => null,
                'recette' => 0,
                'part_medecin' => 0,
                'part_clinique' => 0,
                'depense' => 0,
                'assurance_id' => $assurance->id,
            ]);
        }

        return back()->with('success', 'États pour toutes les assurances générés avec succès.');
    }


    public function genererEtatGeneral()
    {
        $etat = EtatCaisse::create([
            'designation' => 'État Général du ' . now()->format('d/m/Y'),
            'recette' => Caisse::sum('total'),
            'part_medecin' => 0, // À entrer manuellement dans ton form
            'part_clinique' => 0, // idem
            'depense' => Depense::sum('montant'),
            'assurance_id' => null,
            'personnel_id' => null,
        ]);

        return redirect()->route('etatcaisse.index')->with('success', 'État généré avec succès');
    }


    public function generateGeneral()
    {
        // Récupération des recettes totales (caisse)
        $recette = Caisse::sum('total');

        // Récupération de la part médecin (calculée à partir de la caisse)
        $part_medecin = Caisse::sum('total');

        // Récupération de la part clinique
        $part_clinique = Examen::sum('part_cabinet');

        // Dépenses totales
        $depense = Depense::sum('montant');

        // Total des crédits du personnel
        $credit_personnel = Personnel::sum('credit');

        $totaux = [
            'prix_total' => Examen::sum('prix'),
            'part_cabinet_total' => Examen::sum('part_cabinet'),
            'part_medecin_total' => Examen::sum('part_medecin'),
        ];
        // Création d’un nouvel état de caisse
        $etat = EtatCaisse::create([
            'designation'       => 'État général',
            'recette'           => $recette,
            'part_medecin'      => $part_medecin,
            'part_clinique'     => $totaux['part_cabinet_total'],
            'depense'           => $depense,
            'assurance_id'      => null,
            'personnel_id'      => null,
        ]);

        return redirect()->route('etatcaisse.index')->with('success', 'État général généré avec succès.');
    }


    public function show($id)
    {

        $etatcaisse = EtatCaisse::with(['personnel', 'assurance'])->findOrFail($id);
        return view('etatcaisse.show', compact('etatcaisse'));
    }

    public function edit($id)
    {
        $etatcaisse = EtatCaisse::findOrFail($id);
        $personnels = Personnel::all();
        $assurances = Assurance::all();
        return view('etatcaisse.edit', compact('etatcaisse', 'personnels', 'assurances'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'designation' => 'required|string|max:255',
            'recette' => 'required|numeric',
            'part_medecin' => 'required|numeric',
            'part_clinique' => 'required|numeric',
            'depense' => 'required|numeric', // Soit le supprimer complètement :
            'personnel_id' => 'nullable|exists:personnels,id',
            'assurance_id' => 'nullable|exists:assurances,id',
        ]);

        $etatcaisse = EtatCaisse::findOrFail($id);
        $etatcaisse->update($request->all());

        return redirect()->route('etatcaisse.index')->with('success', 'État de caisse mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $etatcaisse = EtatCaisse::findOrFail($id);
        $etatcaisse->delete();
        return redirect()->route('etatcaisse.index')->with('success', 'État de caisse supprimé.');
    }

    public function exportPdf()
    {
        $etatcaisses = EtatCaisse::with(['personnel', 'assurance'])->get();
        $pdf = Pdf::loadView('etatcaisse.export_pdf', compact('etatcaisses'));
        return $pdf->download('etat_de_caisse.pdf');
    }

    public function print()
    {
        $etatcaisses = EtatCaisse::with(['personnel', 'assurance'])->get();
        return view('etatcaisse.print', compact('etatcaisses'));
    }
}
