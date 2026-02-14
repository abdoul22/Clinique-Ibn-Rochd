<?php

namespace App\Http\Controllers;

use App\Models\{Caisse, EtatCaisse, GestionPatient, Medecin, Prescripteur, Examen, Service, ModePaiement};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaisseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $patientFilter = $request->input('patient_filter');
        $numeroPatientFilter = $request->input('numero_patient_filter');
        $medecinFilter = $request->input('medecin_filter');

        $query = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service', 'etatCaisse']);

        // Filtrage par recherche générale
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_entre', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('medecin', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%");
                    });
            });
        }

        // Filtrage par patient spécifique
        if ($patientFilter) {
            $query->whereHas('patient', function ($q) use ($patientFilter) {
                $q->where('first_name', 'like', "%{$patientFilter}%")
                    ->orWhere('last_name', 'like', "%{$patientFilter}%");
            });
        }

        // Filtrage par numéro de patient
        if ($numeroPatientFilter) {
            $query->whereHas('patient', function ($q) use ($numeroPatientFilter) {
                $q->where('phone', 'like', "%{$numeroPatientFilter}%");
            });
        }

        // Filtrage par médecin spécifique
        if ($medecinFilter) {
            $query->whereHas('medecin', function ($q) use ($medecinFilter) {
                $q->where('nom', 'like', "%{$medecinFilter}%");
            });
        }

        // Filtrage par période
        if ($period === 'day' && $date) {
            $query->whereDate('date_examen', $date);
        } elseif ($period === 'week' && $week) {
            $parts = explode('-W', $week);
            if (count($parts) === 2) {
                $yearW = (int)$parts[0];
                $weekW = (int)$parts[1];
                $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                $query->whereBetween('date_examen', [$startOfWeek, $endOfWeek]);
            }
        } elseif ($period === 'month' && $month) {
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                $yearM = (int)$parts[0];
                $monthM = (int)$parts[1];
                $query->whereYear('date_examen', $yearM)->whereMonth('date_examen', $monthM);
            }
        } elseif ($period === 'year' && $year) {
            $query->whereYear('date_examen', $year);
        } elseif ($period === 'range' && $dateStart && $dateEnd) {
            $query->whereBetween('date_examen', [$dateStart, $dateEnd]);
        }

        $caisses = $query->orderBy('created_at', 'desc')->paginate(10);

        // Récupérer les listes pour les filtres
        $patients = GestionPatient::select('id', 'first_name', 'last_name', 'phone')
            ->orderBy('first_name')
            ->get();
        $medecins = Medecin::select('id', 'nom')
            ->orderBy('nom')
            ->get();

        return view('caisses.index', compact('caisses', 'patients', 'medecins'));
    }

    public function create(Request $request)
    {
        $patients = GestionPatient::all();
        // Récupérer seulement les médecins actifs organisés par fonction
        $medecins = Medecin::where('statut', 'actif')
            ->select('id', 'nom', 'fonction', 'prenom', 'specialite')
            ->orderByRaw("FIELD(fonction, 'Pr', 'Dr', 'Tss', 'SGF', 'IDE')")
            ->orderBy('nom')
            ->get();
        $prescripteurs = Prescripteur::all();
        $services = Service::all();
        // Exclure les examens d'hospitalisation automatiques des listes de sélection
        $exam_types = Examen::with('service.pharmacie')
            ->where('nom', 'NOT LIKE', 'Hospitalisation - %')
            ->whereHas('service', function ($query) {
                $query->where('type_service', '!=', 'HOSPITALISATION');
            })
            ->get();
        $assurances = \App\Models\Assurance::all();

        // Variables pour pré-remplissage depuis un rendez-vous
        $fromRdv = null;
        $prefilledPatient = null;
        $prefilledMedecin = null;
        $prefilledNumeroEntree = null;
        $prefilledDateExamen = null;

        // Si on vient d'un rendez-vous, récupérer les données
        if ($request->has('from_rdv')) {
            $fromRdv = \App\Models\RendezVous::find($request->from_rdv);
            if ($fromRdv) {
                // Vérifier que le RDV peut être payé (pas annulé, pas expiré, pas déjà payé)
                if (!$fromRdv->canBePaid()) {
                    $routeName = auth()->user()->role?->name === 'admin' ? 'admin.rendezvous.show' : 'rendezvous.show';
                    if ($fromRdv->statut === 'annule') {
                        return redirect()->route($routeName, $fromRdv->id)
                            ->with('error', 'Ce rendez-vous ne peut pas être payé car il a été annulé.');
                    } elseif ($fromRdv->isExpired()) {
                        return redirect()->route($routeName, $fromRdv->id)
                            ->with('error', 'Ce rendez-vous ne peut pas être payé car il a expiré.');
                    } elseif ($fromRdv->isPaid()) {
                        return redirect()->route($routeName, $fromRdv->id)
                            ->with('error', 'Ce rendez-vous a déjà été payé.');
                    }
                }
                
                $prefilledPatient = $fromRdv->patient;
                $prefilledMedecin = $fromRdv->medecin;
                $prefilledNumeroEntree = $fromRdv->numero_entree;
                $prefilledDateExamen = $fromRdv->date_rdv;
            }
        }

        // Calculer le numéro prévu pour chaque médecin (par jour, tous les numéros utilisés)
        // Utiliser la date d'examen si fournie, sinon la date du rendez-vous, sinon la date actuelle
        $dateReference = $request->get('date_examen')
            ? \Carbon\Carbon::parse($request->get('date_examen'))->startOfDay()
            : ($prefilledDateExamen ? $prefilledDateExamen->startOfDay() : now()->startOfDay());
        $numeros_par_medecin = [];
        foreach ($medecins as $medecin) {
            // Récupérer tous les numéros d'entrée utilisés pour ce médecin à cette date
            $numerosCaisses = Caisse::where('medecin_id', $medecin->id)
                ->whereDate('date_examen', $dateReference)
                ->pluck('numero_entre')
                ->toArray();

            $numerosRendezVous = \App\Models\RendezVous::where('medecin_id', $medecin->id)
                ->whereDate('date_rdv', $dateReference)
                ->where('statut', '=', 'confirme')
                ->pluck('numero_entree')
                ->toArray();

            // Fusionner et trier tous les numéros utilisés
            $numerosUtilises = array_merge($numerosCaisses, $numerosRendezVous);
            sort($numerosUtilises);

            // Trouver le prochain numéro disponible
            $prochainNumero = 1;
            foreach ($numerosUtilises as $numero) {
                if ($numero >= $prochainNumero) {
                    $prochainNumero = $numero + 1;
                }
            }

            $numeros_par_medecin[$medecin->id] = $prochainNumero;
        }

        // Numéro par défaut
        $numero_prevu = $prefilledNumeroEntree ?? 1;

        return view('caisses.create', compact(
            'numero_prevu',
            'numeros_par_medecin',
            'patients',
            'medecins',
            'prescripteurs',
            'exam_types',
            'services',
            'assurances',
            'fromRdv',
            'prefilledPatient',
            'prefilledMedecin',
            'prefilledNumeroEntree',
            'prefilledDateExamen'
        ));
    }

    public function store(Request $request)
    {
        // Validation de base
        $rules = [
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'prescripteur_id' => 'nullable|string',
            'date_examen' => 'required|date',
            'total' => 'required|numeric',
            // accepter temporairement 'especes' puis normaliser
            'type' => 'nullable|string|in:espèces,especes,bankily,masrivi,sedad',
            'assurance_id' => 'nullable|exists:assurances,id',
            'couverture' => 'nullable|numeric|min:0|max:100',
        ];

        // Validation conditionnelle : si une assurance est sélectionnée, la couverture est obligatoire
        if ($request->filled('assurance_id')) {
            $rules['couverture'] = 'required|numeric|min:0|max:100';
        }

        // Si examens multiples, valider les données JSON, sinon valider l'examen unique
        if ($request->examens_multiple === 'true' && $request->filled('examens_data')) {
            $rules['examens_data'] = 'required|json';
        } else {
            $rules['examen_id'] = 'required|exists:examens,id';
            $rules['quantite_medicament'] = 'nullable|integer|min:1';
        }

        $request->validate($rules);

        // Validation stricte du total soumis (avec prise en compte des tarifs assurance)
        $assuranceIdValidation = $request->filled('assurance_id') ? $request->assurance_id : null;
        
        if ($request->examens_multiple === 'true' && $request->filled('examens_data')) {
            $examensData = json_decode($request->examens_data, true);
            $totalCalcule = 0;
            foreach ($examensData as $examenData) {
                $examen = Examen::find($examenData['id']);
                // Utiliser le tarif assurance si applicable
                $tarifUtilise = $examen->getTarifPourAssurance($assuranceIdValidation);
                $totalCalcule += ($tarifUtilise * $examenData['quantite']);
            }

            $tolerance = 0.01; // Tolérance de 1 centime pour les arrondis
            if (abs($request->total - $totalCalcule) > $tolerance) {
                return back()->withErrors([
                    'total' => "Erreur de calcul détectée. Total soumis : {$request->total} MRU, Total calculé : {$totalCalcule} MRU. Veuillez réessayer."
                ])->withInput();
            }
        } else {
            // Validation pour examen unique
            $examen = Examen::findOrFail($request->examen_id);
            $quantite = $request->quantite_medicament ?? 1;
            // Utiliser le tarif assurance si applicable
            $tarifUtilise = $examen->getTarifPourAssurance($assuranceIdValidation);
            $totalCalcule = $tarifUtilise * $quantite;

            $tolerance = 0.01; // Tolérance de 1 centime pour les arrondis
            if (abs($request->total - $totalCalcule) > $tolerance) {
                return back()->withErrors([
                    'total' => "Erreur de calcul détectée. Total soumis : {$request->total} MRU, Total calculé : {$totalCalcule} MRU. Veuillez réessayer."
                ])->withInput();
            }
        }

        $dernierNumero = Caisse::max('numero_facture') ?? 0;
        $prochainNumero = $dernierNumero + 1;

        // Déterminer le numéro d'entrée
        $numeroEntree = null;

        // Si on vient d'un rendez-vous, utiliser le numéro d'entrée du rendez-vous
        if ($request->filled('from_rdv')) {
            $rendezVous = \App\Models\RendezVous::find($request->from_rdv);
            if ($rendezVous) {
                $numeroEntree = $rendezVous->numero_entree;
            }
        }

        // Si pas de numéro d'entrée du rendez-vous, générer un nouveau
        if (!$numeroEntree) {
            $numeroEntree = $this->getNextNumeroEntree($request->medecin_id, $request->date_examen);
        }

        // Préparer les données de base de la facture
        $data = $request->only([
            'gestion_patient_id',
            'medecin_id',
            'prescripteur_id',
            'date_examen',
            'total',
            'assurance_id',
            'couverture'
        ]);
        $data['numero_entre'] = $numeroEntree;
        $data['nom_caissier'] = Auth::user()->name;
        $data['numero_facture'] = $prochainNumero;
        $data['couverture'] = $request->couverture ?? 0;
        $data['assurance_id'] = $request->filled('assurance_id') ? $request->assurance_id : null;

        // Gérer la valeur "extern" pour prescripteur_id
        if ($request->prescripteur_id === 'extern') {
            $data['prescripteur_id'] = null; // Stocker comme null dans la DB
        }

        // Gérer les examens (simple ou multiple)
        if ($request->examens_multiple === 'true' && $request->filled('examens_data')) {
            // Mode examens multiples
            $examensData = json_decode($request->examens_data, true);
            if (empty($examensData)) {
                return back()->withErrors(['examens_data' => 'Aucun examen sélectionné.']);
            }

            // Prendre le premier examen pour la relation principale (compatibilité)
            $premierExamen = Examen::find($examensData[0]['id']);
            $data['examen_id'] = $premierExamen->id;
            $data['service_id'] = $premierExamen->idsvc;

            // Stocker les examens multiples en JSON dans un champ dédié
            $data['examens_data'] = $request->examens_data;
        } else {
            // Mode examen unique (existant)
            $examen = Examen::findOrFail($request->examen_id);
            $data['examen_id'] = $examen->id;
            $data['service_id'] = $examen->idsvc ?? $examen->service_id ?? null;
        }

        $caisse = Caisse::create($data);

        // Recalculer le total réel côté serveur pour éviter les manipulations
        if ($request->examens_multiple === 'true' && $request->filled('examens_data')) {
            $examensData = json_decode($request->examens_data, true);
            $totalReel = 0;
            $assuranceId = $caisse->assurance_id;
            
            foreach ($examensData as $examenData) {
                $examen = Examen::find($examenData['id']);
                
                // Obtenir le tarif correct (assurance ou normal)
                $tarifUtilise = $examen->getTarifPourAssurance($assuranceId);
                
                $totalReel += ($tarifUtilise * $examenData['quantite']);
            }
        } else {
            $examen = Examen::findOrFail($request->examen_id);
            $quantite = $request->quantite_medicament ?? 1;
            $assuranceId = $caisse->assurance_id;
            
            // Obtenir le tarif correct (assurance ou normal)
            $tarifUtilise = $examen->getTarifPourAssurance($assuranceId);
            
            $totalReel = $tarifUtilise * $quantite;
        }

        // Mettre à jour la caisse avec le total recalculé
        $caisse->total = $totalReel;
        $caisse->save();

        // Gestion de la déduction du stock de pharmacie
        if ($request->examens_multiple === 'true' && $request->filled('examens_data')) {
            // Déduire les stocks pour chaque médicament
            $examensData = json_decode($request->examens_data, true);
            foreach ($examensData as $examenData) {
                $examen = Examen::find($examenData['id']);
                $service = Service::find($examen->idsvc);

                if ($service && (in_array($service->type_service, ['medicament', 'PHARMACIE']) || $service->pharmacie_id) && $service->pharmacie_id && $examenData['quantite'] > 0) {
                    $medicament = \App\Models\Pharmacie::find($service->pharmacie_id);

                    if ($medicament && $medicament->stockSuffisant($examenData['quantite'])) {
                        $medicament->deduireStock($examenData['quantite']);
                    } else {
                        $caisse->delete();
                        return back()->withErrors(['examens_data' => "Stock insuffisant pour {$examen->nom}. Stock disponible: " . ($medicament ? $medicament->stock : 0)]);
                    }
                }
            }
        } else {
            // Déduire le stock pour un seul médicament
            $examen = Examen::findOrFail($request->examen_id);
            $service = Service::find($examen->idsvc);
            $quantite = $request->quantite_medicament ?? 1;

            if ($service && (in_array($service->type_service, ['medicament', 'PHARMACIE']) || $service->pharmacie_id) && $service->pharmacie_id && $quantite > 0) {
                $medicament = \App\Models\Pharmacie::find($service->pharmacie_id);

                if ($medicament && $medicament->stockSuffisant($quantite)) {
                    $medicament->deduireStock($quantite);
                } else {
                    $caisse->delete();
                    return back()->withErrors(['quantite_medicament' => "Stock insuffisant pour {$examen->nom}. Stock disponible: " . ($medicament ? $medicament->stock : 0)]);
                }
            }
        }

        // Calculer les parts cabinet et médecin
        $part_cabinet = 0;
        $part_medecin = 0;
        $assuranceId = $caisse->assurance_id;

        if ($request->examens_multiple === 'true' && $request->filled('examens_data')) {
            $examensData = json_decode($request->examens_data, true);
            foreach ($examensData as $examenData) {
                $examen = Examen::find($examenData['id']);
                $quantite = $examenData['quantite'];
                
                // Part médecin: toujours basée sur le tarif standard (fixe)
                $partMedecinExamen = ($examen->part_medecin ?? 0) * $quantite;
                $part_medecin += $partMedecinExamen;
                
                // Part cabinet: tarif utilisé - part médecin standard
                $tarifUtilise = $examen->getTarifPourAssurance($assuranceId);
                $part_cabinet += (($tarifUtilise * $quantite) - $partMedecinExamen);
            }
        } else {
            $examen = Examen::findOrFail($request->examen_id);
            $quantite = $request->quantite_medicament ?? 1;
            
            // Part médecin: toujours basée sur le tarif standard (fixe)
            $partMedecinExamen = ($examen->part_medecin ?? 0) * $quantite;
            $part_medecin = $partMedecinExamen;
            
            // Part cabinet: tarif utilisé - part médecin standard
            $tarifUtilise = $examen->getTarifPourAssurance($assuranceId);
            $part_cabinet = ($tarifUtilise * $quantite) - $partMedecinExamen;
        }

        // Normaliser le type de paiement
        $typePaiement = $request->type;
        if ($typePaiement === 'especes') {
            $typePaiement = 'espèces';
        }

        // Calculer répartition patient/assurance
        $montantTotal = $caisse->total;
        $couverture = $caisse->couverture ?? 0;
        $montantAssurance = $caisse->assurance_id ? ($montantTotal * ($couverture / 100)) : 0;
        $montantPatient = $montantTotal - $montantAssurance;

        // Créer l'état de caisse avec la recette côté patient (ce qui entre en caisse immédiatement)
        // Les parts médecin et clinique restent toujours selon l'examen, peu importe l'assurance
        $etatCaisse = EtatCaisse::create([
            'caisse_id' => $caisse->id,
            'designation' => 'Facture N°' . $caisse->numero_facture,
            'recette' => $montantPatient, // Seule la recette change selon l'assurance
            'part_medecin' => $part_medecin, // Part médecin selon l'examen
            'part_clinique' => $part_cabinet, // Part clinique selon l'examen
            'assurance_id' => $caisse->assurance_id,
            'medecin_id' => $caisse->medecin_id,
        ]);

        // Créer le paiement (uniquement la part patient)
        ModePaiement::create([
            'caisse_id' => $caisse->id,
            'type' => $typePaiement ?? 'espèces',
            'montant' => $montantPatient,
            'source' => 'caisse'
        ]);

        return redirect()->route('caisses.show', $caisse->id)->with('success', 'Facture et état de caisse créés avec succès.');
    }

    public function show($id)
    {
        $caisse = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service', 'etatCaisse', 'mode_paiements', 'modifier'])->find($id);

        if (!$caisse) {
            abort(404, 'Caisse non trouvée.');
        }

        return view('caisses.show', compact('caisse'));
    }

    public function edit($id)
    {
        // Charger la caisse manuellement avec findOrFail
        $caisse = Caisse::findOrFail($id);
        
        // Charger les relations nécessaires
        $caisse->load(['patient', 'medecin', 'prescripteur', 'examen', 'service', 'assurance', 'modifier', 'mode_paiements', 'paiements']);
        
        // Vérifier si la facture a un état de caisse validé
        $etatCaisse = \App\Models\EtatCaisse::where('caisse_id', $caisse->id)->first();
        
        if ($etatCaisse && $etatCaisse->validated) {
            // Rediriger avec message d'erreur si la part médecin est validée
            $role = Auth::user()->role->name;
            $routeName = $role === 'superadmin' || $role === 'admin' ? $role . '.caisses.index' : 'caisses.index';
            
            return redirect()->route($routeName)
                ->with('error', 'Impossible de modifier cette facture. La part médecin a été validée. Veuillez d\'abord annuler la validation dans l\'état de caisse.');
        }
        
        $patients = GestionPatient::all();
        $medecins = Medecin::where('statut', 'actif')
            ->select('id', 'nom', 'fonction', 'prenom', 'specialite')
            ->orderByRaw("FIELD(fonction, 'Pr', 'Dr', 'Tss', 'SGF', 'IDE')")
            ->orderBy('nom')
            ->get();
        $prescripteurs = Prescripteur::all();
        $services = \App\Models\Service::all();
        // Exclure les examens d'hospitalisation automatiques des listes de sélection
        $exam_types = Examen::with('service.pharmacie')
            ->where('nom', 'NOT LIKE', 'Hospitalisation - %')
            ->whereHas('service', function ($query) {
                $query->where('type_service', '!=', 'HOSPITALISATION');
            })
            ->get();
        $assurances = \App\Models\Assurance::all();
        $page = request('page', 1); // Récupérer le paramètre page

        // Convertir une facture en mode simple vers le format examens multiples pour l'édition
        if (!$caisse->examens_data && $caisse->examen_id) {
            // Facture avec un seul examen (mode ancien)
            $examen = $caisse->examen;
            if ($examen) {
                // Déterminer le tarif utilisé (assurance ou standard)
                $tarifUtilise = $examen->getTarifPourAssurance($caisse->assurance_id);
                
                // Créer le tableau examens_data
                $caisse->examens_data = [
                    [
                        'id' => $examen->id,
                        'nom' => $examen->nom,
                        'tarif' => $tarifUtilise,
                        'quantite' => 1,
                        'total' => $tarifUtilise,
                        'isPharmacie' => $examen->service && $examen->service->type_service === 'PHARMACIE'
                    ]
                ];
            }
        }

        return view('caisses.edit', compact(
            'caisse',
            'patients',
            'medecins',
            'prescripteurs',
            'services',
            'exam_types',
            'assurances',
            'page'
        ));
    }

    public function update(Request $request, $id)
    {
        // Charger la caisse manuellement avec findOrFail
        $caisse = Caisse::findOrFail($id);
        
        // Debug: logger les données reçues
        \Log::info('=== UPDATE CAISSE #' . $caisse->id . ' ===');
        \Log::info('examens_multiple: ' . $request->input('examens_multiple'));
        \Log::info('examens_data: ' . $request->input('examens_data'));
        \Log::info('total: ' . $request->input('total'));
        \Log::info('gestion_patient_id: ' . $request->input('gestion_patient_id'));
        \Log::info('medecin_id: ' . $request->input('medecin_id'));
        
        // Vérifier si c'est un mode examens multiples
        $isMultipleExamens = $request->input('examens_multiple') === 'true';

        $validationRules = [
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'prescripteur_id' => 'nullable|exists:prescripteurs,id',
            'date_examen' => 'required|date',
            'total' => 'required|numeric|min:0',
            'assurance_id' => 'nullable|exists:assurances,id',
            'couverture' => 'nullable|numeric|min:0|max:100',
            'numero_entre' => 'nullable|integer|min:1',
        ];

        // Si ce n'est pas un mode multiple, examen_id est requis
        if (!$isMultipleExamens) {
            $validationRules['examen_id'] = 'required|exists:examens,id';
        } else {
            // En mode examens multiples, vérifier qu'il y a au moins un examen
            if (!$request->filled('examens_data') || empty(json_decode($request->examens_data, true))) {
                return back()->withErrors([
                    'examens_data' => 'Vous devez sélectionner au moins un examen.'
                ])->withInput();
            }
        }

        // Normaliser prescripteur_id (Externe = null) avant validation
        if (in_array($request->prescripteur_id, ['', 'extern', null], true)) {
            $request->merge(['prescripteur_id' => null]);
        }

        $request->validate($validationRules);

        // Validation stricte du total soumis (avec prise en compte des tarifs assurance)
        $assuranceIdValidation = $request->filled('assurance_id') ? $request->assurance_id : null;
        
        if ($isMultipleExamens && $request->filled('examens_data')) {
            $examensData = json_decode($request->examens_data, true);
            $totalCalcule = 0;
            foreach ($examensData as $examenData) {
                $examen = Examen::find($examenData['id']);
                // Utiliser le tarif assurance si applicable
                $tarifUtilise = $examen->getTarifPourAssurance($assuranceIdValidation);
                $totalCalcule += ($tarifUtilise * $examenData['quantite']);
            }

            $tolerance = 0.01; // Tolérance de 1 centime pour les arrondis
            if (abs($request->total - $totalCalcule) > $tolerance) {
                return back()->withErrors([
                    'total' => "Erreur de calcul détectée. Total soumis : {$request->total} MRU, Total calculé : {$totalCalcule} MRU. Veuillez réessayer."
                ])->withInput();
            }
        } elseif (!$isMultipleExamens && $request->filled('examen_id')) {
            // Validation pour examen unique
            $examen = Examen::findOrFail($request->examen_id);
            $quantite = $request->quantite_medicament ?? 1;
            // Utiliser le tarif assurance si applicable
            $tarifUtilise = $examen->getTarifPourAssurance($assuranceIdValidation);
            $totalCalcule = $tarifUtilise * $quantite;

            $tolerance = 0.01; // Tolérance de 1 centime pour les arrondis
            if (abs($request->total - $totalCalcule) > $tolerance) {
                return back()->withErrors([
                    'total' => "Erreur de calcul détectée. Total soumis : {$request->total} MRU, Total calculé : {$totalCalcule} MRU. Veuillez réessayer."
                ])->withInput();
            }
        }

        // Filtrer les données à sauvegarder
        $data = $request->only([
            'gestion_patient_id',
            'medecin_id',
            'prescripteur_id',
            'date_examen',
            'total',
            'assurance_id',
            'couverture',
            'numero_entre'
        ]);

        // Gestion du mode examens multiples
        if ($isMultipleExamens && $request->filled('examens_data')) {
            \Log::info('Mode examens multiples détecté');
            \Log::info('examens_data brut: ' . $request->examens_data);
            
            $examensData = json_decode($request->examens_data, true);
            \Log::info('examens_data décodé: ' . json_encode($examensData));
            \Log::info('Type: ' . gettype($examensData));
            \Log::info('Est array: ' . (is_array($examensData) ? 'oui' : 'non'));
            \Log::info('Count: ' . (is_array($examensData) ? count($examensData) : 0));
            
            if (!empty($examensData)) {
                // Prendre le premier examen comme examen principal
                $premierExamen = $examensData[0];
                $data['examen_id'] = $premierExamen['id'];
                
                // NE PAS encoder - Laravel le fera automatiquement pour les colonnes JSON
                $data['examens_data'] = $examensData; // Array, pas string !
                
                \Log::info('examens_data à sauvegarder (array): ' . json_encode($data['examens_data']));
                
                // Service du premier examen
                $examen = Examen::findOrFail($premierExamen['id']);
                $data['service_id'] = $examen->idsvc ?? $examen->service_id ?? null;
            } else {
                \Log::warning('examens_data vide après décodage!');
            }
        } else {
            \Log::info('Mode examen simple');
            // Mode examen simple
            $data['examen_id'] = $request->examen_id;
            $examen = Examen::findOrFail($request->examen_id);
            $data['service_id'] = $examen->idsvc ?? $examen->service_id ?? null;
            $data['examens_data'] = null; // Réinitialiser examens_data si mode simple
        }

        $data['assurance_id'] = $request->filled('assurance_id') ? $request->assurance_id : null;
        $data['couverture'] = $request->couverture ?? 0;
        
        // Ajouter le modificateur (ne pas modifier nom_caissier original)
        $data['modified_by'] = Auth::id();

        // Si le médecin a changé et qu'un nouveau numéro d'entrée est fourni, l'utiliser
        if ($request->filled('numero_entre')) {
            $data['numero_entre'] = $request->numero_entre;
        }

        // FORCER la sauvegarde en utilisant DB::table pour TOUT (bypass Eloquent)
        if (isset($data['examens_data']) && is_array($data['examens_data'])) {
            \Log::info('🔧 Sauvegarde COMPLÈTE via DB::table (bypass Eloquent)');
            
            $examensDataJson = json_encode($data['examens_data']);
            $data['examens_data'] = $examensDataJson; // Remplacer l'array par la string JSON
            $data['updated_at'] = now(); // Force updated_at
            
            \Log::info('Données à sauvegarder: total=' . $data['total'] . ', examens=' . substr($examensDataJson, 0, 100) . '...');
            
            // Mettre à jour TOUT via DB::table
            $rowsAffected = \DB::table('caisses')
                ->where('id', $caisse->id)
                ->update($data);
            
            \Log::info('✅ Rows affected: ' . $rowsAffected);
            
            // Recharger le modèle
            $caisse = Caisse::findOrFail($caisse->id);
            \Log::info('Total après update: ' . $caisse->total);
        } else {
            // Pas d'examens_data, update normal
            \Log::info('Update normal sans examens_data');
            $caisse->update($data);
        }
        
        // Recharger depuis la DB pour vérifier ce qui a été sauvegardé
        $caisse->refresh();
        \Log::info('=== APRÈS SAUVEGARDE ===');
        \Log::info('Caisse #' . $caisse->id . ' total: ' . $caisse->total);
        \Log::info('examens_data type: ' . gettype($caisse->examens_data));
        \Log::info('examens_data is_array: ' . (is_array($caisse->examens_data) ? 'oui' : 'non'));
        \Log::info('examens_data contenu: ' . json_encode($caisse->examens_data));

        // Recalculer le total pour validation avec les tarifs assurance si applicable
        $totalRecalcule = 0;
        $assuranceId = $caisse->assurance_id;
        
        if ($isMultipleExamens && $caisse->examens_data) {
            $examensData = is_array($caisse->examens_data) ? $caisse->examens_data : json_decode($caisse->examens_data, true);
            foreach ($examensData as $examenData) {
                $examen = Examen::find($examenData['id']);
                if ($examen) {
                    $tarifUtilise = $examen->getTarifPourAssurance($assuranceId);
                    $totalRecalcule += ($tarifUtilise * $examenData['quantite']);
                }
            }
        } else if ($caisse->examen_id) {
            $examen = Examen::find($caisse->examen_id);
            if ($examen) {
                $tarifUtilise = $examen->getTarifPourAssurance($assuranceId);
                $totalRecalcule = $tarifUtilise;
            }
        }
        
        // Mettre à jour le total si recalculé différent (sécurité)
        if ($totalRecalcule > 0 && abs($caisse->total - $totalRecalcule) > 0.01) {
            \Log::warning("Total recalculé différent: {$caisse->total} vs {$totalRecalcule}");
            $caisse->total = $totalRecalcule;
            $caisse->save();
        }

        // Recalculer les parts médecin et clinique selon le/les examen(s)
        $totalPartMedecin = 0;
        $totalPartCabinet = 0;

        if ($isMultipleExamens && $request->filled('examens_data')) {
            $examensData = json_decode($request->examens_data, true);
            foreach ($examensData as $examenData) {
                $examen = Examen::find($examenData['id']);
                if ($examen) {
                    $quantite = $examenData['quantite'] ?? 1;
                    
                    // Part médecin: toujours basée sur le tarif standard (fixe)
                    $partMedecinExamen = ($examen->part_medecin ?? 0) * $quantite;
                    $totalPartMedecin += $partMedecinExamen;
                    
                    // Part cabinet: tarif utilisé - part médecin standard
                    $tarifUtilise = $examen->getTarifPourAssurance($assuranceId);
                    $totalPartCabinet += (($tarifUtilise * $quantite) - $partMedecinExamen);
                }
            }
        } else {
            $examen = Examen::find($data['examen_id']);
            if ($examen) {
                // Part médecin: toujours basée sur le tarif standard (fixe)
                $partMedecinExamen = $examen->part_medecin ?? 0;
                $totalPartMedecin = $partMedecinExamen;
                
                // Part cabinet: tarif utilisé - part médecin standard
                $tarifUtilise = $examen->getTarifPourAssurance($assuranceId);
                $totalPartCabinet = $tarifUtilise - $partMedecinExamen;
            }
        }

        // Mettre à jour l'état de caisse correspondant s'il existe
        $etatCaisse = \App\Models\EtatCaisse::where('caisse_id', $caisse->id)->first();

        if ($etatCaisse) {
            // Calculer la part patient en fonction de l'assurance
            $montantTotal = $caisse->total;
            $couverture = $caisse->couverture ?? 0;
            $montantAssurance = $montantTotal * ($couverture / 100);
            $montantPatient = $montantTotal - $montantAssurance;

            $etatCaisse->update([
                'designation' => 'Facture N°' . $caisse->numero_facture,
                'recette' => $montantPatient,
                'part_medecin' => $totalPartMedecin,
                'part_clinique' => $totalPartCabinet,
                'assurance_id' => $caisse->assurance_id && $caisse->couverture > 0 ? $caisse->assurance_id : null,
                'medecin_id' => $caisse->medecin_id,
            ]);
        }

        // Mettre à jour le ModePaiement correspondant
        $modePaiement = \App\Models\ModePaiement::where('caisse_id', $caisse->id)
            ->where('source', 'caisse')
            ->first();

        if ($modePaiement) {
            // Recalculer le montant patient (ce qui entre réellement en caisse)
            $montantTotal = $caisse->total;
            $couverture = $caisse->couverture ?? 0;
            $montantPatient = $montantTotal - ($montantTotal * ($couverture / 100));
            
            $modePaiement->update([
                'montant' => $montantPatient,
                'type' => $request->input('type', $modePaiement->type), // Garder l'ancien type si non fourni
            ]);
            
            \Log::info("ModePaiement #{$modePaiement->id} mis à jour: {$montantPatient} MRU (type: {$modePaiement->type})");
        }

        // Gérer les crédits d'assurance
        if ($etatCaisse) {
            // Récupérer et supprimer l'ancien crédit lié à cette caisse (s'il existe)
            $oldCredit = \App\Models\Credit::where('caisse_id', $caisse->id)->first();
            
            if ($oldCredit) {
                \Log::info("Suppression ancien crédit assurance #{$oldCredit->id}");
                $oldAssuranceId = $oldCredit->source_id;
                $oldCredit->delete();
                
                // Recalculer le crédit total de l'ancienne assurance
                $oldAssurance = \App\Models\Assurance::find($oldAssuranceId);
                if ($oldAssurance) {
                    $oldAssurance->updateCredit();
                }
            }
            
            // Créer le nouveau crédit si une assurance est définie
            if ($caisse->assurance_id && $caisse->couverture > 0) {
                $montantTotal = $caisse->total;
                $couverture = $caisse->couverture;
                $montantAssurance = $montantTotal * ($couverture / 100);
                
                if ($montantAssurance > 0) {
                    // Créer le nouveau crédit (même logique que l'Observer)
                    $newCredit = \App\Models\Credit::create([
                        'source_type' => \App\Models\Assurance::class,
                        'source_id' => $caisse->assurance_id,
                        'montant' => $montantAssurance,
                        'montant_paye' => 0,
                        'status' => 'non payé',
                        'statut' => 'Non payé',
                        'caisse_id' => $caisse->id,
                    ]);
                    
                    \Log::info("Nouveau crédit assurance #{$newCredit->id} créé: {$montantAssurance} MRU");
                    
                    // Mettre à jour le crédit total de la nouvelle assurance
                    $assurance = $caisse->assurance;
                    if ($assurance) {
                        $assurance->updateCredit();
                    }
                }
            }
        }

        // Mettre à jour le mode de paiement si fourni
        if ($request->filled('type')) {
            $modePaiement = ModePaiement::where('caisse_id', $caisse->id)
                ->where('source', 'caisse')
                ->first();
            
            if ($modePaiement) {
                $modePaiement->update(['type' => $request->type]);
            } else {
                // Si le mode de paiement n'existe pas encore, le créer
                ModePaiement::create([
                    'caisse_id' => $caisse->id,
                    'type' => $request->type,
                    'montant' => $caisse->total - ($caisse->assurance_id ? ($caisse->total * ($caisse->couverture / 100)) : 0),
                    'source' => 'caisse'
                ]);
            }
        }

        // Caisse mise à jour avec succès

        // Redirection selon le rôle de l'utilisateur
        $role = Auth::user()->role->name;
        $routeName = $role === 'superadmin' || $role === 'admin' ? $role . '.caisses.index' : 'caisses.index';

        // Conserver TOUS les paramètres de requête (filtres, pagination, etc.)
        $queryParams = $request->only(['page', 'search', 'period', 'date', 'week', 'month', 'year', 'date_start', 'date_end', 'patient_filter', 'numero_patient_filter', 'medecin_filter']);
        
        // Si return_page est défini, l'utiliser comme page
        if ($request->filled('return_page')) {
            $queryParams['page'] = $request->input('return_page');
        }

        // Force le navigateur à ne pas utiliser le cache pour cette redirection
        return redirect()->route($routeName, $queryParams)
            ->with('success', 'Facture mise à jour avec succès.')
            ->with('timestamp', time()); // Force refresh avec timestamp
    }

    public function destroy($id)
    {
        // Charger la caisse manuellement avec findOrFail
        $caisse = Caisse::findOrFail($id);
        
        // Vérifier que seul le superadmin peut supprimer une facture
        $role = Auth::user()->role->name;

        if ($role !== 'superadmin') {
            return back()->with('error', 'Vous n\'avez pas la permission de supprimer une facture.');
        }

        if ($caisse->delete()) {
            return redirect()->route('superadmin.caisses.index')->with('success', 'Facture supprimée !');
        }

        return back()->with('error', 'Erreur lors de la suppression');
    }

    public function exportPdf(Caisse $caisse)
    {
        // Cette méthode utilise un paramètre {caisse} personnalisé dans la route
        // donc on garde le nom $caisse ici
        
        // Charger les relations nécessaires
        $caisse->load(['patient', 'medecin', 'prescripteur', 'examen', 'service']);

        $pdf = Pdf::loadView('caisses.export', compact('caisse'));
        return $pdf->download('examen-' . $caisse->numero_entre . '.pdf');
    }

    public function print()
    {
        $caisses = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service'])->get();
        return view('caisses.print', compact('caisses'));
    }

    public function printSingle($id)
    {
        $caisse = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen.service.pharmacie'])->findOrFail($id);
        return view('caisses.print', compact('caisse'));
    }

    public function getNextNumeroEntree($medecinId, $dateExamen = null)
    {
        // Utiliser la date d'examen si fournie, sinon la date actuelle
        $dateReference = $dateExamen ? \Carbon\Carbon::parse($dateExamen)->startOfDay() : now()->startOfDay();

        // Récupérer tous les numéros d'entrée utilisés pour ce médecin à cette date
        // (caisses + rendez-vous) pour éviter les doublons
        $numerosCaisses = Caisse::where('medecin_id', $medecinId)
            ->whereDate('date_examen', $dateReference)
            ->pluck('numero_entre')
            ->toArray();

        $numerosRendezVous = \App\Models\RendezVous::where('medecin_id', $medecinId)
            ->whereDate('date_rdv', $dateReference)
            ->where('statut', '=', 'confirme')
            ->pluck('numero_entree')
            ->toArray();

        // Fusionner et trier tous les numéros utilisés
        $numerosUtilises = array_merge($numerosCaisses, $numerosRendezVous);
        sort($numerosUtilises);

        // Trouver le prochain numéro disponible
        $prochainNumero = 1;
        foreach ($numerosUtilises as $numero) {
            if ($numero >= $prochainNumero) {
                $prochainNumero = $numero + 1;
            }
        }

        return $prochainNumero;
    }

    public function getNextNumeroEntreeApi(Request $request)
    {
        $medecinId = $request->get('medecin_id');
        $dateExamen = $request->get('date_examen');

        if (!$medecinId) {
            return response()->json(['error' => 'Médecin requis'], 400);
        }

        $numero = $this->getNextNumeroEntree($medecinId, $dateExamen);

        return response()->json(['numero' => $numero]);
    }
}
