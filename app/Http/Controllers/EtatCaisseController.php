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
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        $etatcaisses = EtatCaisse::with(['caisse', 'caisse.paiements', 'personnel', 'assurance', 'medecin'])
            ->when($period === 'day' && $date, fn($q) => $q->whereDate('created_at', $date))
            ->when($period === 'week' && $week, function ($q) use ($week) {
                $parts = explode('-W', $week);
                if (count($parts) === 2) {
                    $yearW = (int)$parts[0];
                    $weekW = (int)$parts[1];
                    $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                    $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                    $q->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                }
            })
            ->when($period === 'month' && $month, function ($q) use ($month) {
                $parts = explode('-', $month);
                if (count($parts) === 2) {
                    $yearM = (int)$parts[0];
                    $monthM = (int)$parts[1];
                    $q->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
                }
            })
            ->when($period === 'year' && $year, fn($q) => $q->whereYear('created_at', $year))
            ->when($period === 'range' && $dateStart && $dateEnd, fn($q) => $q->whereBetween('created_at', [$dateStart, $dateEnd]))
            ->when($request->designation, fn($q) => $q->where('designation', 'like', "%{$request->designation}%"))
            ->when($request->personnel_id, fn($q) => $q->where('personnel_id', $request->personnel_id))
            ->when($request->medecin_id, fn($q) => $q->where('medecin_id', $request->medecin_id))
            ->latest()->paginate(10);

        $personnels = Personnel::all();
        $caisse = Caisse::all();

        // 🔹 Résumé GLOBAL (toutes dates)
        $totalRecette = EtatCaisse::sum('recette'); // Utiliser EtatCaisse au lieu de Caisse
        $totalPartMedecin = EtatCaisse::where('validated', true)->sum('part_medecin');
        $totalPartCabinet = $totalRecette - $totalPartMedecin;
        $totalDepense = Depense::where('rembourse', false)->sum('montant');

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
            $recetteCaisse = EtatCaisse::whereDate('created_at', $date)->sum('recette'); // Utiliser EtatCaisse
            $partMedecin = EtatCaisse::whereDate('created_at', $date)->where('validated', true)->sum('part_medecin');
            $partCabinet = $recetteCaisse - $partMedecin;
            $depense = Depense::whereDate('created_at', $date)
                ->where('rembourse', false)
                ->sum('montant');

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
            $chartFiltreData = $chartGlobalData;
        }

        // Assurances utilisées dans les etatcaisses (filtrées par date si besoin)
        if ($date) {
            $assurances = EtatCaisse::whereDate('created_at', $date)
                ->with('assurance')
                ->get()
                ->map(function ($etat) {
                    return $etat->assurance;
                })
                ->unique(function ($item) {
                    return $item ? $item->id : null;
                })
                ->values();
        } else {
            $assurances = EtatCaisse::with('assurance')
                ->get()
                ->map(function ($etat) {
                    return $etat->assurance;
                })
                ->unique(function ($item) {
                    return $item ? $item->id : null;
                })
                ->values();
        }

        $medecins = \App\Models\Medecin::all();

        return view('etatcaisse.index', compact(
            'etatcaisses',
            'personnels',
            'caisse',
            'resumeGlobal',
            'resumeFiltre',
            'chartGlobalData',
            'chartFiltreData',
            'assurances',
            'medecins'
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

        // Suppression: la création du crédit assurance est gérée par l'observer d' EtatCaisse (Model)

        // Validation automatique si la part médecin est 0
        if ($etat->part_medecin == 0) {
            $etat->validated = true;
            $etat->save();
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

        // Créer un enregistrement ModePaiement pour la sortie (montant négatif)
        \App\Models\ModePaiement::create([
            'type' => $modePaiementType,
            'montant' => -$etat->part_medecin, // Montant négatif pour sortie
            'source' => 'depense'
        ]);

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

    public function valider(Request $request, $id)
    {
        $etat = EtatCaisse::findOrFail($id);

        if ($etat->validated) {
            return back()->with('error', 'Part déjà validée.');
        }

        // Valider le mode de paiement sélectionné
        $request->validate([
            'mode_paiement' => 'required|in:especes,bankily,masrivi,sedad'
        ]);

        // Utiliser le mode de paiement sélectionné depuis la modale
        $modePaiementType = $request->mode_paiement;

        // Créer un enregistrement ModePaiement pour la sortie (montant négatif)
        \App\Models\ModePaiement::create([
            'type' => $modePaiementType,
            'montant' => -$etat->part_medecin, // Montant négatif pour sortie
            'source' => 'depense'
        ]);

        $depense = Depense::create([
            'nom' => 'Part médecin - ' . $etat->medecin?->nom . ' (' . ucfirst($modePaiementType) . ')',
            'montant' => $etat->part_medecin,
            'etat_caisse_id' => $etat->id, // Lien direct
            'source' => 'générée', // pour le filtre
            'mode_paiement_id' => $modePaiementType,
        ]);

        $etat->validated = true;
        $etat->depense = $etat->part_medecin;
        $etat->save();

        return back()->with('success', 'Part médecin validée et dépense créée avec le mode de paiement: ' . ucfirst($modePaiementType));
    }
    public function annulerValidation($id)
    {
        $etat = EtatCaisse::findOrFail($id);

        if (!$etat->validated) {
            return back()->with('error', 'Cette part n\'est pas validée.');
        }

        // Supprimer la dépense liée à cette validation
        if ($etat->depense) {
            $depense = $etat->depense;

            // Vérifier que mode_paiement_id n'est pas null
            if (!empty($depense->mode_paiement_id)) {
                // Supprimer l'enregistrement ModePaiement correspondant (montant négatif pour sortie)
                \App\Models\ModePaiement::where('type', $depense->mode_paiement_id)
                    ->where('montant', -$depense->montant)
                    ->where('source', 'depense')
                    ->delete();
            }

            // Supprimer la dépense
            $depense->delete();
        }

        // Remettre le montant dans le mode de paiement si c'était une validation automatique
        $modePaiement = $etat->caisse?->mode_paiements()->latest()->first();
        if ($modePaiement && $etat->part_medecin > 0) {
            $modePaiement->increment('montant', $etat->part_medecin);
        }

        $etat->validated = false;
        $etat->depense = 0; // Remettre la dépense à 0
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
        $depense = $etat->depense;
        if ($depense && is_object($depense)) {
            // Vérifier que mode_paiement_id n'est pas null
            if (!empty($depense->mode_paiement_id)) {
                // Supprimer l'enregistrement ModePaiement correspondant (montant négatif pour sortie)
                \App\Models\ModePaiement::where('type', $depense->mode_paiement_id)
                    ->where('montant', -$depense->montant)
                    ->where('source', 'depense')
                    ->delete();
            }

            // Supprimer la dépense
            $depense->delete();
        } else {
            // Fallback pour les anciennes dépenses sans relation directe
            $nom = 'Part médecin - ' . $etat->medecin?->nom;
            $depenses = Depense::where('nom', 'like', '%' . $nom . '%')->where('montant', $etat->part_medecin)->get();

            foreach ($depenses as $depense) {
                // Vérifier que mode_paiement_id n'est pas null
                if (!empty($depense->mode_paiement_id)) {
                    // Supprimer l'enregistrement ModePaiement correspondant
                    \App\Models\ModePaiement::where('type', $depense->mode_paiement_id)
                        ->where('montant', -$depense->montant)
                        ->where('source', 'depense')
                        ->delete();
                }

                $depense->delete();
            }
        }

        // Remettre le montant dans le mode de paiement si c'était une validation automatique
        $modePaiement = $etat->caisse?->mode_paiements()->latest()->first();
        if ($modePaiement && $etat->part_medecin > 0) {
            $modePaiement->increment('montant', $etat->part_medecin);
        }

        $etat->validated = false;
        $etat->depense = 0; // Remettre la dépense à 0
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

        // Dépenses totales (exclure les crédits personnel)
        $depense = Depense::where(function ($q) {
            $q->whereNull('credit_id')
                ->orWhereHas('credit', function ($creditQuery) {
                    $creditQuery->where('source_type', '!=', \App\Models\Personnel::class);
                });
        })->sum('montant');

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
        $etatcaisse = EtatCaisse::with(['personnel', 'assurance', 'caisse', 'medecin'])->findOrFail($id);
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

    public function exportPdf(Request $request)
    {
        // Appliquer les mêmes filtres que la méthode index
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        $etatcaisses = EtatCaisse::with(['caisse', 'caisse.paiements', 'personnel', 'assurance', 'medecin'])
            ->when($period === 'day' && $date, fn($q) => $q->whereDate('created_at', $date))
            ->when($period === 'week' && $week, function ($q) use ($week) {
                $parts = explode('-W', $week);
                if (count($parts) === 2) {
                    $yearW = (int)$parts[0];
                    $weekW = (int)$parts[1];
                    $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                    $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                    $q->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                }
            })
            ->when($period === 'month' && $month, function ($q) use ($month) {
                $parts = explode('-', $month);
                if (count($parts) === 2) {
                    $yearM = (int)$parts[0];
                    $monthM = (int)$parts[1];
                    $q->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
                }
            })
            ->when($period === 'year' && $year, fn($q) => $q->whereYear('created_at', $year))
            ->when($period === 'range' && $dateStart && $dateEnd, fn($q) => $q->whereBetween('created_at', [$dateStart, $dateEnd]))
            ->when($request->designation, fn($q) => $q->where('designation', 'like', "%{$request->designation}%"))
            ->when($request->personnel_id, fn($q) => $q->where('personnel_id', $request->personnel_id))
            ->when($request->medecin_id, fn($q) => $q->where('medecin_id', $request->medecin_id))
            ->latest()->get(); // Pas de pagination pour le PDF

        // Calculer les résumés avec filtres
        $resumeFiltre = $this->calculateResumeForPeriod($request);

        // Générer la description de la période filtrée
        $periodDescription = $this->generatePeriodDescription($request);

        $pdf = Pdf::loadView('etatcaisse.export_pdf', compact('etatcaisses', 'resumeFiltre', 'periodDescription'));

        $filename = 'etat_de_caisse';
        if ($date) {
            $filename .= '_' . \Carbon\Carbon::parse($date)->format('Y-m-d');
        } elseif ($period !== 'day') {
            $filename .= '_' . $period;
        }

        return $pdf->download($filename . '.pdf');
    }

    public function print(Request $request)
    {
        // Appliquer les mêmes filtres que la méthode index
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        $etatcaisses = EtatCaisse::with(['caisse', 'caisse.paiements', 'personnel', 'assurance', 'medecin'])
            ->when($period === 'day' && $date, fn($q) => $q->whereDate('created_at', $date))
            ->when($period === 'week' && $week, function ($q) use ($week) {
                $parts = explode('-W', $week);
                if (count($parts) === 2) {
                    $yearW = (int)$parts[0];
                    $weekW = (int)$parts[1];
                    $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                    $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                    $q->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                }
            })
            ->when($period === 'month' && $month, function ($q) use ($month) {
                $parts = explode('-', $month);
                if (count($parts) === 2) {
                    $yearM = (int)$parts[0];
                    $monthM = (int)$parts[1];
                    $q->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
                }
            })
            ->when($period === 'year' && $year, fn($q) => $q->whereYear('created_at', $year))
            ->when($period === 'range' && $dateStart && $dateEnd, fn($q) => $q->whereBetween('created_at', [$dateStart, $dateEnd]))
            ->when($request->designation, fn($q) => $q->where('designation', 'like', "%{$request->designation}%"))
            ->when($request->personnel_id, fn($q) => $q->where('personnel_id', $request->personnel_id))
            ->when($request->medecin_id, fn($q) => $q->where('medecin_id', $request->medecin_id))
            ->latest()->get(); // Pas de pagination pour l'impression

        // Calculer les résumés avec filtres
        $resumeFiltre = $this->calculateResumeForPeriod($request);

        // Générer la description de la période filtrée
        $periodDescription = $this->generatePeriodDescription($request);

        return view('etatcaisse.print', compact('etatcaisses', 'resumeFiltre', 'periodDescription'));
    }

    private function calculateResumeForPeriod(Request $request)
    {
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $medecinId = $request->input('medecin_id');

        // Construire la requête de base pour les résumés
        $etatCaisseQuery = EtatCaisse::query();
        $depenseQuery = Depense::query();
        $creditPersonnelQuery = Credit::where('source_type', \App\Models\Personnel::class);
        $creditAssuranceQuery = Credit::where('source_type', \App\Models\Assurance::class);

        // Filtrer par médecin si fourni
        if ($medecinId) {
            $etatCaisseQuery->where('medecin_id', $medecinId);
        }

        // Appliquer les filtres de date
        if ($period === 'day' && $date) {
            $etatCaisseQuery->whereDate('created_at', $date);
            $depenseQuery->whereDate('created_at', $date);
            $creditPersonnelQuery->whereDate('created_at', $date);
            $creditAssuranceQuery->whereDate('created_at', $date);
        } elseif ($period === 'week' && $week) {
            $parts = explode('-W', $week);
            if (count($parts) === 2) {
                $yearW = (int)$parts[0];
                $weekW = (int)$parts[1];
                $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                $etatCaisseQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $depenseQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $creditPersonnelQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $creditAssuranceQuery->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            }
        } elseif ($period === 'month' && $month) {
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                $yearM = (int)$parts[0];
                $monthM = (int)$parts[1];
                $etatCaisseQuery->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
                $depenseQuery->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
                $creditPersonnelQuery->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
                $creditAssuranceQuery->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
            }
        } elseif ($period === 'year' && $year) {
            $etatCaisseQuery->whereYear('created_at', $year);
            $depenseQuery->whereYear('created_at', $year);
            $creditPersonnelQuery->whereYear('created_at', $year);
            $creditAssuranceQuery->whereYear('created_at', $year);
        } elseif ($period === 'range' && $dateStart && $dateEnd) {
            $etatCaisseQuery->whereBetween('created_at', [$dateStart, $dateEnd]);
            $depenseQuery->whereBetween('created_at', [$dateStart, $dateEnd]);
            $creditPersonnelQuery->whereBetween('created_at', [$dateStart, $dateEnd]);
            $creditAssuranceQuery->whereBetween('created_at', [$dateStart, $dateEnd]);
        }

        // Calculer les totaux
        $recette = $etatCaisseQuery->sum('recette');
        $partMedecin = $etatCaisseQuery->where('validated', true)->sum('part_medecin');
        $partCabinet = $recette - $partMedecin;
        $depense = $depenseQuery->where('rembourse', false)->sum('montant');
        $creditPersonnel = max($creditPersonnelQuery->sum('montant') - $creditPersonnelQuery->sum('montant_paye'), 0);
        $creditAssurance = max($creditAssuranceQuery->sum('montant') - $creditAssuranceQuery->sum('montant_paye'), 0);

        return [
            'recette' => $recette,
            'part_medecin' => $partMedecin,
            'part_cabinet' => $partCabinet,
            'depense' => $depense,
            'credit_personnel' => $creditPersonnel,
            'credit_assurance' => $creditAssurance,
        ];
    }

    private function generatePeriodDescription(Request $request)
    {
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        if ($period === 'day' && $date) {
            return 'Filtré sur le jour du ' . \Carbon\Carbon::parse($date)->translatedFormat('d F Y');
        } elseif ($period === 'week' && $week) {
            $parts = explode('-W', $week);
            if (count($parts) === 2) {
                $start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                return 'Filtré sur la semaine du ' . $start->translatedFormat('d F Y') . ' au ' . $end->translatedFormat('d F Y');
            }
        } elseif ($period === 'month' && $month) {
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                return 'Filtré sur le mois de ' . \Carbon\Carbon::create($parts[0], $parts[1])->translatedFormat('F Y');
            }
        } elseif ($period === 'year' && $year) {
            return 'Filtré sur l\'année ' . $year;
        } elseif ($period === 'range' && $dateStart && $dateEnd) {
            return 'Filtré sur la période du ' . \Carbon\Carbon::parse($dateStart)->translatedFormat('d F Y') . ' au ' . \Carbon\Carbon::parse($dateEnd)->translatedFormat('d F Y');
        }

        return 'Toutes les données';
    }
}
