<?php

namespace App\Http\Controllers;


use App\Models\Personnel;
use App\Models\Assurance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EtatCaisse;
use App\Models\Caisse;
use App\Models\Credit;
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

        // 🔹 Résumé GLOBAL (toutes dates)
        $totalRecette = Caisse::sum('total');
        $totalPartMedecin = EtatCaisse::where('validated', true)->sum('part_medecin');
        $totalPartCabinet = $totalRecette - $totalPartMedecin;
        $totalDepense = Depense::sum('montant');

        $totalCreditPersonnel = max(
            Credit::where('source_type', \App\Models\Personnel::class)->sum('montant') -
                Credit::where('source_type', \App\Models\Personnel::class)->sum('montant_paye'),
            0
        );

        $totalCreditAssurance = max(
            Credit::where('source_type', \App\Models\Assurance::class)->sum('montant') -
                Credit::where('source_type', \App\Models\Assurance::class)->sum('montant_paye'),
            0
        );

        $resumeGlobal = [
            'recette' => $totalRecette,
            'part_medecin' => $totalPartMedecin,
            'part_cabinet' => $totalPartCabinet,
            'depense' => $totalDepense,
            'credit_personnel' => $totalCreditPersonnel,
            'credit_assurance' => $totalCreditAssurance,
        ];

        $chartGlobalData = [
            $totalRecette,
            $totalPartMedecin,
            $totalPartCabinet,
            $totalDepense,
            $totalCreditPersonnel,
            $totalCreditAssurance,
        ];

        // 🔹 Résumé FILTRÉ (si une date est fournie)
        $date = $request->date;
        if ($date) {
            $recetteCaisse = Caisse::whereDate('created_at', $date)->sum('total');
            $partMedecin = EtatCaisse::whereDate('created_at', $date)->where('validated', true)->sum('part_medecin');
            $partCabinet = $recetteCaisse - $partMedecin;
            $depense = Depense::whereDate('created_at', $date)->sum('montant');

            $creditPersonnel = max(
                Credit::where('source_type', \App\Models\Personnel::class)->whereDate('created_at', $date)->sum('montant') -
                    Credit::where('source_type', \App\Models\Personnel::class)->whereDate('created_at', $date)->sum('montant_paye'),
                0
            );

            $creditAssurance = max(
                Credit::where('source_type', \App\Models\Assurance::class)->whereDate('created_at', $date)->sum('montant') -
                    Credit::where('source_type', \App\Models\Assurance::class)->whereDate('created_at', $date)->sum('montant_paye'),
                0
            );

            $resumeFiltre = [
                'recette' => $recetteCaisse,
                'part_medecin' => $partMedecin,
                'part_cabinet' => $partCabinet,
                'depense' => $depense,
                'credit_personnel' => $creditPersonnel,
                'credit_assurance' => $creditAssurance,
            ];

            $chartFiltreData = [
                $recetteCaisse,
                $partMedecin,
                $partCabinet,
                $depense,
                $creditPersonnel,
                $creditAssurance,
            ];
        } else {
            $resumeFiltre = null;
            $chartFiltreData = [];
        }

        return view('etatcaisse.index', compact(
            'etatcaisses',
            'personnels',
            'caisse',
            'resumeGlobal',
            'resumeFiltre',
            'chartGlobalData',
            'chartFiltreData'
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
            'assurance_id'   => $validated['assurance_id'] ?? $caisse?->assurance_id,
            'caisse_id'      => $validated['caisse_id'] ?? null,
            'medecin_id'     => $caisse?->medecin_id ?? null,
            'examen_id'      => $caisse?->examen_id ?? null,
        ]);


        $etat->load('personnel', 'assurance', 'medecin', 'caisse.paiements');
        // Création automatique du crédit assurance si une assurance est liée
        if ($etat->assurance_id) {
            Credit::create([
                'source_type' => \App\Models\Assurance::class,
                'source_id' => $etat->assurance_id,
                'montant' => $etat->recette,
                'montant_paye' => 0,
                'status' => 'non payé',
                'statut' => 'non payé',
                'caisse_id' => $etat->caisse_id,
            ]);
        }
        return response()->json([
            'etat' => $etat,
            'view' => view('etatcaisse.partials.row', compact('etat'))->render()
        ]);
    }
    public function validerPartMedecin($id)
    {
        $etat = EtatCaisse::findOrFail($id);

        // Vérifie si déjà validé
        if ($etat->validated) {
            return back()->with('info', 'Part déjà validée.');
        }

        // Récupérer le mode de paiement de la caisse
        $modePaiement = $etat->caisse?->mode_paiements()->latest()->first();
        $modePaiementType = $modePaiement?->type ?? 'espèces';

        // Vérifier si le mode de paiement a suffisamment de fonds
        if ($modePaiement && $modePaiement->montant < $etat->part_medecin) {
            return back()->with('error', "Fonds insuffisants dans le mode de paiement {$modePaiementType}. Solde disponible : {$modePaiement->montant} MRU");
        }

        // Déduire le montant du mode de paiement
        if ($modePaiement) {
            $modePaiement->decrement('montant', $etat->part_medecin);
        }

        $etat->validated = true;
        $etat->depense = $etat->part_medecin;
        $etat->save();

        // Créer la dépense avec le bon mode de paiement
        \App\Models\Depense::create([
            'nom' => 'Part médecin - ' . ($etat->medecin?->nom ?? 'N/A'),
            'montant' => $etat->part_medecin,
            'source' => 'automatique',
            'etat_caisse_id' => $etat->id,
            'mode_paiement_id' => $modePaiementType, // Utiliser le type, pas l'ID
        ]);

        return back()->with('success', 'Part validée avec succès.');
    }

    public function valider($id)
    {
        $etat = EtatCaisse::findOrFail($id);

        if ($etat->validated) {
            return back()->with('error', 'Part déjà validée.');
        }

        // Chercher le mode de paiement utilisé par le patient à la caisse
        $modePaiement = $etat->caisse?->mode_paiements()->latest()->first();
        $modePaiementType = $modePaiement?->type ?? 'espèces';

        $depense = Depense::create([
            'nom' => 'Part médecin - ' . $etat->medecin?->nom,
            'montant' => $etat->part_medecin,
            'etat_caisse_id' => $etat->id, // Lien direct
            'source' => 'générée', // pour le filtre
            'mode_paiement_id' => $modePaiementType,
        ]);

        $etat->validated = true;
        $etat->save();

        return back()->with('success', 'Part médecin validée et dépense créée.');
    }
    public function annulerValidation($id)
    {
        $etat = EtatCaisse::findOrFail($id);

        if (!$etat->validated) {
            return back()->with('error', 'Cette part n\'est pas validée.');
        }

        // Supprimer uniquement la dépense liée à cette validation
        if ($etat->depense) {
            $etat->depense->delete();
        }

        $etat->validated = false;
        $etat->save();

        return back()->with('success', 'Validation annulée et dépense supprimée.');
    }

    public function unvalider($id)
    {
        $etat = EtatCaisse::findOrFail($id);

        if (!$etat->validated) {
            return back()->with('error', 'Part non encore validée.');
        }

        // Supprimer la dépense liée si elle existe
        $nom = 'Part médecin - ' . $etat->medecin?->nom;
        Depense::where('nom', $nom)->where('montant', $etat->part_medecin)->delete();

        $etat->validated = false;
        $etat->save();

        return back()->with('success', 'Validation annulée avec succès.');
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

        return redirect()->route('etatcaisse.index')->with('success', 'État de l\'assurance généré.');
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

        return redirect()->route('etatcaisse.index')->with('success', 'État général généré avec succès.');
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
        // Création d'un nouvel état de caisse
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
