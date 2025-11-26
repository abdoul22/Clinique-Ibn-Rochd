<?php

namespace App\Http\Controllers;

use App\Models\Hospitalisation;
use App\Models\GestionPatient;
use App\Models\Medecin;
use App\Models\Service;
use App\Models\Chambre;
use App\Models\Lit;
use App\Models\HospitalisationCharge;
use App\Models\Examen;
use App\Models\Caisse;
use App\Models\EtatCaisse;
use App\Models\ModePaiement;
use App\Models\Pharmacie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class HospitalisationController extends Controller
{
    public function index()
    {
        $hospitalisations = Hospitalisation::with(['patient', 'medecin', 'service', 'lit.chambre', 'annulator'])
            ->orderByDesc('created_at')
            ->paginate(15);

        // Calculer les statistiques pour toutes les hospitalisations (pas seulement la page actuelle)
        $allHospitalisations = Hospitalisation::all();

        return view('hospitalisations.index', compact('hospitalisations'));
    }

    public function create()
    {
        $patients = GestionPatient::all();
        // Organiser les médecins par fonction : Pr, Dr, Tss, SGF, IDE
        $medecins = Medecin::where('statut', 'actif')
            ->orderByRaw("FIELD(fonction, 'Pr', 'Dr', 'Tss', 'SGF', 'IDE')")
            ->orderBy('nom')
            ->get();
        $services = Service::all();
        $chambres = Chambre::active()->with(['lits' => function ($q) {
            $q->where('statut', 'libre')->orderBy('numero');
        }])->get();

        // Trouver le service HOSPITALISATION par défaut
        $serviceHospitalisation = Service::where('type_service', 'HOSPITALISATION')->first();
        $defaultServiceId = $serviceHospitalisation ? $serviceHospitalisation->id : null;

        // Récupérer les examens associés au service HOSPITALISATION
        $examensHospitalisation = [];
        if ($serviceHospitalisation) {
            $examensHospitalisation = Examen::where('idsvc', $serviceHospitalisation->id)
                ->orderBy('nom')
                ->get();
        }

        // Préparer les données des lits pour le JavaScript
        $litsParChambre = [];
        foreach ($chambres as $chambre) {
            $litsParChambre[$chambre->id] = $chambre->lits->map(function ($lit) use ($chambre) {
                return [
                    'id' => $lit->id,
                    'numero' => $lit->numero,
                    'nom_complet' => $chambre->nom . ' - Lit ' . $lit->numero
                ];
            })->toArray();
        }

        // Préparer les données des chambres avec prix pour le JavaScript
        $chambresData = [];
        foreach ($chambres as $chambre) {
            $chambresData[$chambre->id] = [
                'id' => $chambre->id,
                'nom' => $chambre->nom,
                'prix_par_jour' => $chambre->tarif_journalier ?? 5000,
                'lits_count' => $chambre->lits->count()
            ];
        }

        return view('hospitalisations.create', compact(
            'patients',
            'medecins',
            'services',
            'chambres',
            'litsParChambre',
            'chambresData',
            'defaultServiceId',
            'examensHospitalisation'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'nullable|exists:medecins,id',
            'service_id' => 'required|exists:services,id',
            'chambre_id' => 'required|exists:chambres,id',
            'lit_id' => 'required|exists:lits,id',
            'date_entree' => 'required|date|before_or_equal:today',
            'date_sortie' => 'nullable|date|after_or_equal:date_entree',
            'motif' => 'nullable|string|max:500',
            'statut' => 'required|in:en cours,terminé,annulé',
            'montant_total' => 'nullable|numeric|min:0',
            'observation' => 'nullable|string|max:1000',
            'couverture' => 'nullable|numeric|min:0|max:100',
            'assurance_id' => 'nullable|exists:assurances,id',
        ]);

        // Validation personnalisée pour vérifier que la chambre a des lits libres
        $chambre = Chambre::with('lits')->findOrFail($request->chambre_id);
        $litsLibres = $chambre->lits()->where('statut', 'libre')->count();

        if ($litsLibres === 0) {
            throw ValidationException::withMessages([
                'chambre_id' => 'Cette chambre n\'a aucun lit libre disponible. Veuillez choisir une autre chambre.'
            ]);
        }

        DB::transaction(function () use ($request) {
            // Vérifier que le lit est disponible avec verrou pessimiste
            $lit = Lit::lockForUpdate()->findOrFail($request->lit_id);
            if (!$lit->est_libre) {
                throw ValidationException::withMessages([
                    'lit_id' => 'Ce lit n\'est pas disponible.'
                ]);
            }

            // Vérifier que le lit appartient bien à la chambre sélectionnée
            if ($lit->chambre_id != $request->chambre_id) {
                throw ValidationException::withMessages([
                    'lit_id' => 'Le lit sélectionné n\'appartient pas à la chambre choisie.'
                ]);
            }

            // Créer l'hospitalisation - utiliser uniquement date_entree
            $data = $request->all();
            $dateEntree = Carbon::parse($request->date_entree);
            $data['date_entree'] = $dateEntree->toDateString();

            // Supprimer les champs admission_at et next_charge_due_at pour standardiser sur date_entree
            unset($data['admission_at'], $data['next_charge_due_at'], $data['chambre_id']);

            $hospitalisation = Hospitalisation::create($data);

            // Créer la période de chambre courante
            $chambre = $lit->chambre;
            if ($chambre) {
                \App\Models\HospitalizationRoomStay::create([
                    'hospitalisation_id' => $hospitalisation->id,
                    'chambre_id' => $chambre->id,
                    'start_at' => $dateEntree,
                ]);
            }

            // Marquer le lit comme occupé
            $lit->occuper();
        });

        // Redirection en fonction du rôle de l'utilisateur
        $route = Auth::user()->role?->name === 'admin' ? 'admin.hospitalisations.index' : 'hospitalisations.index';
        return redirect()->route($route)->with('success', 'Hospitalisation ajoutée avec succès !');
    }

    public function show($id)
    {
        try {
            Log::info("HospitalisationController@show - START");
            Log::info("Hospitalisation ID: " . $id);

            $hospitalisation = Hospitalisation::with([
                'patient',
                'medecin',
                'pharmacien',
                'service',
                'lit.chambre',
                'roomStays' => function ($q) {
                    $q->orderBy('start_at', 'desc');
                },
                'roomStays.chambre',
                'charges' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                }
            ])->findOrFail($id);

            Log::info("Hospitalisation loaded successfully");

            // Générer les charges de chambre automatiques si en cours
            if ($hospitalisation->statut === 'en cours') {
                $this->generateRoomCharges($hospitalisation);
            }

            $chargesNonFacturees = $hospitalisation->charges()->where('is_billed', false)->get();
            $chargesFacturees = $hospitalisation->charges()->where('is_billed', true)->get();

            $totaux = [
                'room_day' => $chargesNonFacturees->where('type', 'room_day')->sum('total_price'),
                'examens' => $chargesNonFacturees->where('type', 'examen')->sum('total_price'),
                'services' => $chargesNonFacturees->where('type', 'service')->sum('total_price'),
                'pharmacie' => $chargesNonFacturees->where('type', 'pharmacy')->sum('total_price'),
                'part_medecin' => $chargesNonFacturees->sum('part_medecin'),
                'part_cabinet' => $chargesNonFacturees->sum('part_cabinet'),
                'total' => $chargesNonFacturees->sum('total_price'),
                'facturees' => $chargesFacturees->sum('total_price'),
                'montant_total' => $chargesNonFacturees->sum('total_price') + $chargesFacturees->sum('total_price'),
            ];

            // Calculer la durée d'hospitalisation en format lisible
            $dateEntree = Carbon::parse($hospitalisation->date_entree);
            $dateFin = $hospitalisation->date_sortie ? Carbon::parse($hospitalisation->date_sortie) : Carbon::now();

            // Utiliser la même logique que generateRoomCharges pour cohérence
            if ($dateEntree->isToday()) {
                $joursEcoules = 0; // Premier jour = 0 jours écoulés
            } else {
                $joursEcoules = intval($dateEntree->diffInDays($dateFin));
            }

            // Le nombre de jours d'hospitalisation = jours écoulés + 1 (jour d'entrée)
            $jours = $joursEcoules + 1;

            // Calculer les heures et minutes restantes
            $diffInSeconds = $dateFin->diffInSeconds($dateEntree);
            $heures = floor(($diffInSeconds % 86400) / 3600);
            $minutes = floor(($diffInSeconds % 3600) / 60);

            // Formatter la durée
            $dureeSejour = '';
            if ($jours > 0) {
                $dureeSejour .= $jours . ' jour' . ($jours > 1 ? 's' : '');
            }
            if ($heures > 0) {
                $dureeSejour .= ($dureeSejour ? ' ' : '') . $heures . 'h';
            }
            if ($minutes > 0) {
                $dureeSejour .= ($dureeSejour ? ' ' : '') . $minutes . 'mn';
            }

            // Si aucune durée calculée, afficher au minimum 1 jour
            if (empty($dureeSejour)) {
                $dureeSejour = '1 jour';
            }

            // Garder aussi la valeur numérique pour la compatibilité
            $joursHospitalisation = max(1, $jours);

            // Ajouter les variables manquantes pour la vue - Exclure les examens d'hospitalisation automatiques
            // Filtrer les examens pour exclure ceux liés à des services de type pharmacie
            $examens = Examen::with('service')
                ->where('nom', 'NOT LIKE', 'Hospitalisation - %')
                ->where('nom', '!=', 'Hospitalisation')
                ->whereHas('service', function ($query) {
                    $query->where('type_service', '!=', 'PHARMACIE')
                        ->where('type_service', '!=', 'pharmacie')
                        ->where('type_service', '!=', 'medicament');
                })
                ->orderBy('nom')
                ->get();
            $medicaments = Pharmacie::where('statut', 'actif')
                ->where('stock', '>', 0)
                ->orderBy('nom_medicament')
                ->get();
            $medecins = Medecin::where('statut', 'actif')->orderBy('nom')->get();

            // Récupérer uniquement les pharmaciens (médecins avec fonction Pharmacien)
            $pharmaciens = Medecin::where('fonction', 'Phr')
                ->where('statut', 'actif')
                ->orderBy('nom')
                ->get();

            // Récupérer tous les médecins impliqués dans cette hospitalisation
            $medecinsImpliques = $hospitalisation->getAllInvolvedDoctors();

            return view('hospitalisations.show', compact(
                'hospitalisation',
                'chargesNonFacturees',
                'chargesFacturees',
                'totaux',
                'examens',
                'medicaments',
                'medecins',
                'pharmaciens',
                'medecinsImpliques',
                'joursHospitalisation',
                'dureeSejour'
            ));
        } catch (\Exception $e) {
            Log::error("HospitalisationController@show - Error: " . $e->getMessage());
            Log::error("Trace: " . $e->getTraceAsString());
            return response()->view('errors.500', [], 500);
        }
    }

    private function generateRoomCharges($hospitalisation)
    {
        if (!$hospitalisation->lit || !$hospitalisation->lit->chambre) {
            return;
        }

        $chambre = $hospitalisation->lit->chambre;
        $dateEntree = Carbon::parse($hospitalisation->date_entree);
        $maintenant = Carbon::now();

        // Calculer le nombre de jours depuis l'entrée - corriger le calcul
        // Si l'hospitalisation vient d'être créée aujourd'hui, c'est 0 jours écoulés (premier jour)
        if ($dateEntree->isToday()) {
            $joursEcoules = 0; // Premier jour = 0 jours écoulés
        } else {
            $joursEcoules = $dateEntree->diffInDays($maintenant);
        }

        // Vérifier combien de charges de chambre existent déjà
        $chargesExistantes = HospitalisationCharge::where('hospitalisation_id', $hospitalisation->id)
            ->where('type', 'room_day')
            ->count();

        // Créer les charges manquantes (une par jour)
        for ($jour = $chargesExistantes; $jour <= $joursEcoules; $jour++) {
            $dateCharge = $dateEntree->copy()->addDays($jour);

            // Ne pas créer de charge pour le futur
            if ($dateCharge->isAfter($maintenant)) {
                break;
            }

            $charge = new HospitalisationCharge([
                'hospitalisation_id' => $hospitalisation->id,
                'type' => 'room_day',
                'source_id' => $chambre->id,
                'description_snapshot' => "Chambre {$chambre->nom} - Jour " . ($jour + 1),
                'unit_price' => $chambre->tarif_journalier ?? 5000, // Prix par défaut si non défini
                'quantity' => 1,
                'total_price' => $chambre->tarif_journalier ?? 5000,
                'part_medecin' => 0,
                'part_cabinet' => $chambre->tarif_journalier ?? 5000,
                'is_pharmacy' => false,
            ]);

            // Forcer la date de création à la date calculée
            $charge->created_at = $dateCharge;
            $charge->save();
        }
    }

    public function facturer(Request $request, $id)
    {
        $hospitalisation = Hospitalisation::with(['patient', 'medecin', 'lit.chambre'])->findOrFail($id);

        $request->validate([
            'charge_ids' => 'required|array|min:1',
            'charge_ids.*' => 'integer|exists:hospitalisation_charges,id',
            'type' => 'required|string|in:espèces,bankily,masrivi,sedad,carte,virement',
        ]);

        $chargeIds = $request->charge_ids;

        DB::transaction(function () use ($request, $hospitalisation, $chargeIds) {
            // Récupérer les charges avec verrou
            $charges = HospitalisationCharge::lockForUpdate()
                ->whereIn('id', $chargeIds)
                ->where('hospitalisation_id', $hospitalisation->id)
                ->where('is_billed', false)
                ->get();

            if ($charges->isEmpty()) {
                throw ValidationException::withMessages([
                    'charge_ids' => 'Aucune charge valide à facturer.'
                ]);
            }

            $total = (float) $charges->sum('total_price');
            $partCabinet = (float) $charges->sum('part_cabinet');
            $partMedecin = (float) $charges->sum('part_medecin');

            $assuranceId = $hospitalisation->assurance_id;
            $couverture = (float) ($hospitalisation->couverture ?? 0);
            // CORRECTION: Le montant du mode de paiement = total des charges (sans déduire la part médecin)
            // La part médecin sera déduite plus tard lors de la validation dans etatcaisse
            $montantPaiement = $assuranceId ? $total * (1 - ($couverture / 100)) : $total;

            // Préparer numeros
            $dernierNumero = Caisse::max('numero_facture') ?? 0;
            $prochainNumero = $dernierNumero + 1;
            $medecinId = $hospitalisation->medecin_id;
            $numeroEntree = (new CaisseController)->getNextNumeroEntree($medecinId);

            // Utiliser ou créer un examen générique d'hospitalisation
            $serviceHosp = Service::where('type_service', 'HOSPITALISATION')->first() ?? Service::first();
            $examen = Examen::where('idsvc', $serviceHosp?->id)
                ->where('nom', 'Hospitalisation')
                ->where('medecin_id', $medecinId) // Chercher par médecin aussi
                ->first();

            if (!$examen) {
                $examen = Examen::create([
                    'nom' => 'Hospitalisation',
                    'idsvc' => $serviceHosp?->id,
                    'medecin_id' => $medecinId, // Lier l'examen au médecin
                    'tarif' => 0, // Tarif dynamique selon les charges
                    'part_cabinet' => $partCabinet, // Utiliser les parts réelles
                    'part_medecin' => $partMedecin, // Utiliser les parts réelles
                ]);
            } else {
                // Mettre à jour l'examen existant avec les parts réelles
                $examen->update([
                    'part_cabinet' => $partCabinet,
                    'part_medecin' => $partMedecin,
                ]);
            }

            // Construire examens_data à partir des charges pour permettre la décomposition par service
            $examensData = [];
            $examensMap = []; // Pour éviter les doublons d'examens

            foreach ($charges as $charge) {
                $examenId = null;
                $quantite = $charge->quantity ?? 1;

                if ($charge->is_pharmacy) {
                    // Charge de pharmacie : trouver ou créer un Examen pour le médicament
                    $pharmacie = Pharmacie::find($charge->source_id);
                    if ($pharmacie) {
                        $examenPharma = $this->findOrCreateExamenForPharmacy($pharmacie, $medecinId);
                        $examenId = $examenPharma->id;
                    }
                } elseif ($charge->type === 'room_day') {
                    // Charge de chambre : utiliser l'examen Hospitalisation
                    $examenId = $examen->id;
                } else {
                    // Charge d'examen/service : utiliser l'examen existant
                    $examenId = $charge->source_id;
                }

                if ($examenId) {
                    // Regrouper les quantités pour le même examen
                    if (isset($examensMap[$examenId])) {
                        $examensMap[$examenId] += $quantite;
                    } else {
                        $examensMap[$examenId] = $quantite;
                    }
                }
            }

            // Construire le tableau examens_data au format JSON
            foreach ($examensMap as $examenId => $quantite) {
                $examensData[] = [
                    'id' => $examenId,
                    'quantite' => $quantite
                ];
            }

            // Créer la caisse avec examens_data
            $caisse = Caisse::create([
                'numero_facture' => $prochainNumero,
                'numero_entre' => $numeroEntree,
                'gestion_patient_id' => $hospitalisation->gestion_patient_id,
                'medecin_id' => $medecinId,
                'prescripteur_id' => null,
                'examen_id' => $examen->id,
                'service_id' => $serviceHosp?->id ?? $examen->idsvc,
                'date_examen' => Carbon::now()->toDateString(),
                'total' => $total,
                'nom_caissier' => \Illuminate\Support\Facades\Auth::user()->name,
                'assurance_id' => $assuranceId,
                'couverture' => $assuranceId ? (int) $couverture : null,
                'examens_data' => !empty($examensData) ? json_encode($examensData) : null,
            ]);

            // Etat de caisse - La recette = montant du paiement (total des charges)
            EtatCaisse::create([
                'caisse_id' => $caisse->id,
                'designation' => 'Facture Hospitalisation N°' . $caisse->numero_facture,
                'recette' => $montantPaiement, // Total des charges, pas déduction de la part médecin
                'part_medecin' => $partMedecin,
                'part_clinique' => $partCabinet,
                'assurance_id' => $assuranceId,
                'medecin_id' => $medecinId,
            ]);

            // Log de vérification pour débogage
            Log::info("=== DÉBOGAGE PAIEMENT HOSPITALISATION (facturer) ===");
            Log::info("Hospitalisation ID: {$hospitalisation->id}");
            Log::info("Caisse ID: {$caisse->id}");
            Log::info("Total charges: {$total} MRU");
            Log::info("Part Cabinet: {$partCabinet} MRU");
            Log::info("Part Médecin: {$partMedecin} MRU");
            Log::info("Montant Paiement (à enregistrer): {$montantPaiement} MRU");
            Log::info("Assurance ID: " . ($assuranceId ?: 'Aucune'));
            Log::info("Couverture: {$couverture}%");
            Log::info("================================================");

            ModePaiement::create([
                'caisse_id' => $caisse->id,
                'type' => $request->type,
                'montant' => $montantPaiement,
                'source' => 'caisse',
            ]);

            // Marquer charges comme facturées
            $charges->each(function ($charge) use ($caisse) {
                $charge->update([
                    'is_billed' => true,
                    'billed_at' => Carbon::now(),
                    'caisse_id' => $caisse->id,
                ]);
            });
        });

        return redirect()->route('hospitalisations.show', $hospitalisation->id)
            ->with('success', 'Facture créée avec succès !');
    }

    public function edit($id)
    {
        $hospitalisation = Hospitalisation::with(['lit.chambre'])->findOrFail($id);
        $patients = GestionPatient::all();
        $medecins = Medecin::where('statut', 'actif')->get();
        $services = Service::all();
        $chambres = Chambre::active()->with(['lits' => function ($q) use ($hospitalisation) {
            // Inclure le lit actuel de l'hospitalisation même s'il n'est pas libre
            $q->where(function ($query) use ($hospitalisation) {
                $query->where('statut', 'libre')
                    ->orWhere('id', $hospitalisation->lit_id);
            })->orderBy('numero');
        }])->get();

        // Préparer les données des lits pour le JavaScript
        $litsParChambre = [];
        $chambresData = [];
        foreach ($chambres as $chambre) {
            // Compter les lits libres (sans compter le lit actuel)
            $litsLibres = $chambre->lits->filter(function ($lit) use ($hospitalisation) {
                return $lit->statut === 'libre' || $lit->id === $hospitalisation->lit_id;
            });

            $litsParChambre[$chambre->id] = $litsLibres->map(function ($lit) use ($chambre) {
                return [
                    'id' => $lit->id,
                    'numero' => $lit->numero,
                    'nom_complet' => $chambre->nom . ' - Lit ' . $lit->numero,
                    'statut' => $lit->statut
                ];
            })->toArray();

            // Ajouter les données de prix des chambres avec nombre de lits libres
            $chambresData[$chambre->id] = [
                'prix_par_jour' => $chambre->tarif_journalier ?? 5000,
                'nom' => $chambre->nom,
                'lits_libres_count' => $litsLibres->where('statut', 'libre')->count()
            ];
        }

        return view('hospitalisations.edit', compact('hospitalisation', 'patients', 'medecins', 'services', 'chambres', 'litsParChambre', 'chambresData'));
    }

    public function update(Request $request, $id)
    {
        $hospitalisation = Hospitalisation::findOrFail($id);

        $request->validate([
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'service_id' => 'required|exists:services,id',
            'lit_id' => 'required|exists:lits,id',
            'date_entree' => 'required|date|before_or_equal:today',
            'date_sortie' => 'nullable|date|after_or_equal:date_entree',
            'motif' => 'nullable|string|max:500',
            'statut' => 'required|in:en cours,terminé,annulé',
            'montant_total' => 'nullable|numeric|min:0',
            'observation' => 'nullable|string|max:1000',
            'couverture' => 'nullable|numeric|min:0|max:100',
            'assurance_id' => 'nullable|exists:assurances,id',
        ]);

        DB::transaction(function () use ($request, $hospitalisation) {
            $ancienLitId = $hospitalisation->lit_id;
            $nouveauLitId = $request->lit_id;

            // Si changement de lit
            if ($ancienLitId != $nouveauLitId) {
                // Libérer l'ancien lit
                if ($ancienLitId) {
                    $ancienLit = Lit::find($ancienLitId);
                    if ($ancienLit) {
                        $ancienLit->liberer();
                    }
                    // Clôturer le séjour de chambre en cours
                    $currentStay = \App\Models\HospitalizationRoomStay::where('hospitalisation_id', $hospitalisation->id)
                        ->whereNull('end_at')
                        ->latest('start_at')
                        ->first();
                    if ($currentStay) {
                        $currentStay->update(['end_at' => Carbon::now()]);
                    }
                }

                // Vérifier que le nouveau lit est disponible avec verrou
                $nouveauLit = Lit::lockForUpdate()->findOrFail($nouveauLitId);
                if (!$nouveauLit->est_libre) {
                    throw ValidationException::withMessages([
                        'lit_id' => 'Ce lit n\'est pas disponible.'
                    ]);
                }

                // Occuper le nouveau lit
                $nouveauLit->occuper();

                // Démarrer un nouveau séjour pour la nouvelle chambre
                $chambre = $nouveauLit->chambre;
                if ($chambre) {
                    \App\Models\HospitalizationRoomStay::create([
                        'hospitalisation_id' => $hospitalisation->id,
                        'chambre_id' => $chambre->id,
                        'start_at' => Carbon::now(),
                    ]);
                }
            }

            // Mettre à jour l'hospitalisation - standardiser sur date_entree
            $data = $request->all();
            $data['date_entree'] = Carbon::parse($request->date_entree)->toDateString();
            if ($request->date_sortie) {
                $data['date_sortie'] = Carbon::parse($request->date_sortie)->toDateString();
            }

            $hospitalisation->update($data);

            // Si l'hospitalisation est terminée ou annulée, libérer le lit
            if (in_array($request->statut, ['terminé', 'annulé']) && $nouveauLitId) {
                $lit = Lit::find($nouveauLitId);
                if ($lit) {
                    $lit->liberer();
                }
                // Clôturer le séjour en cours
                $currentStay = \App\Models\HospitalizationRoomStay::where('hospitalisation_id', $hospitalisation->id)
                    ->whereNull('end_at')
                    ->latest('start_at')
                    ->first();
                if ($currentStay) {
                    $currentStay->update(['end_at' => Carbon::now()]);
                }
            }
        });

        // Redirection en fonction du rôle de l'utilisateur
        $route = Auth::user()->role?->name === 'admin' ? 'admin.hospitalisations.show' : 'hospitalisations.show';
        return redirect()->route($route, $hospitalisation->id)
            ->with('success', 'Hospitalisation modifiée avec succès !');
    }

    public function destroy($id)
    {
        $hospitalisation = Hospitalisation::findOrFail($id);

        DB::transaction(function () use ($hospitalisation) {
            // Vérifier qu'il n'y a pas de charges facturées
            $chargesFacturees = $hospitalisation->charges()->where('is_billed', true)->count();
            if ($chargesFacturees > 0) {
                throw ValidationException::withMessages([
                    'hospitalisation' => 'Impossible de supprimer une hospitalisation avec des charges facturées.'
                ]);
            }

            // Supprimer les charges non facturées
            $hospitalisation->charges()->where('is_billed', false)->delete();

            // Libérer le lit si il y en a un
            if ($hospitalisation->lit_id) {
                $lit = Lit::find($hospitalisation->lit_id);
                if ($lit) {
                    $lit->liberer();
                }
            }

            // Supprimer les séjours de chambre
            $hospitalisation->roomStays()->delete();

            $hospitalisation->delete();
        });

        // Redirection en fonction du rôle de l'utilisateur
        $route = Auth::user()->role?->name === 'admin' ? 'admin.hospitalisations.index' : 'hospitalisations.index';
        return redirect()->route($route)
            ->with('success', 'Hospitalisation supprimée avec succès !');
    }

    // API pour obtenir les lits disponibles d'une chambre
    public function getLitsDisponibles(Request $request)
    {
        $request->validate([
            'chambre_id' => 'required|exists:chambres,id'
        ]);

        $chambreId = $request->chambre_id;
        $lits = Lit::where('chambre_id', $chambreId)
            ->where('statut', 'libre')
            ->orderBy('numero')
            ->get()
            ->map(function ($lit) {
                return [
                    'id' => $lit->id,
                    'numero' => $lit->numero,
                    'nom_complet' => $lit->nom_complet,
                ];
            });

        return response()->json($lits);
    }

    public function addCharge(Request $request, $id)
    {
        $hospitalisation = Hospitalisation::with('lit.chambre')->findOrFail($id);

        // Vérifier que l'hospitalisation n'est pas annulée
        if ($hospitalisation->statut === 'annulé') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'ajouter une charge à une hospitalisation annulée'
            ], 403);
        }

        $request->validate([
            'charge_type' => 'required|in:examen,service,pharmacy',
            'examen_id' => 'required_if:charge_type,examen,service|nullable|exists:examens,id',
            'medicament_id' => 'required_if:charge_type,pharmacy|nullable|exists:pharmacies,id',
            'medecin_id' => 'nullable|exists:medecins,id',
            'pharmacien_id' => 'nullable|exists:medecins,id',
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        DB::transaction(function () use ($request, $hospitalisation) {
            if ($request->charge_type === 'pharmacy') {
                $med = Pharmacie::lockForUpdate()->findOrFail($request->medicament_id);
                $qty = (int) $request->quantity;

                // Vérifier le stock
                if (!$med->stockSuffisant($qty)) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Stock insuffisant pour ' . $med->nom_medicament . '. Stock disponible: ' . $med->stock
                    ]);
                }

                // Déduire le stock
                $med->deduireStock($qty);

                // Si c'est le premier médicament et qu'un pharmacien est sélectionné, le stocker dans l'hospitalisation
                if ($request->pharmacien_id && !$hospitalisation->pharmacien_id) {
                    $hospitalisation->update(['pharmacien_id' => $request->pharmacien_id]);
                }

                HospitalisationCharge::create([
                    'hospitalisation_id' => $hospitalisation->id,
                    'type' => 'pharmacy',
                    'source_id' => $med->id,
                    'description_snapshot' => $med->nom_medicament,
                    'unit_price' => $med->prix_vente,
                    'quantity' => $qty,
                    'total_price' => $med->prix_vente * $qty,
                    'part_medecin' => 0,
                    'part_cabinet' => $med->prix_vente * $qty,
                    'is_pharmacy' => true,
                ]);
            } else {
                $ex = Examen::findOrFail($request->examen_id);
                $qty = (int) $request->quantity;

                // Utiliser l'examen original, mais modifier la description si un médecin différent est sélectionné
                $examenId = $ex->id;
                $description = $ex->nom;

                if ($request->medecin_id && $request->medecin_id != $ex->medecin_id) {
                    $medecin = \App\Models\Medecin::findOrFail($request->medecin_id);
                    $description = $ex->nom . ' (' . $medecin->nom_complet_avec_prenom . ')';
                }

                HospitalisationCharge::create([
                    'hospitalisation_id' => $hospitalisation->id,
                    'type' => $request->charge_type,
                    'source_id' => $examenId,
                    'description_snapshot' => $description,
                    'unit_price' => $ex->tarif,
                    'quantity' => $qty,
                    'total_price' => $ex->tarif * $qty,
                    'part_medecin' => ($ex->part_medecin ?? 0) * $qty,
                    'part_cabinet' => ($ex->part_cabinet ?? 0) * $qty,
                    'is_pharmacy' => false,
                ]);
            }
        });

        // Récupérer la charge créée pour la retourner
        $lastCharge = HospitalisationCharge::where('hospitalisation_id', $hospitalisation->id)
            ->where('is_billed', false)
            ->latest()
            ->first();

        // Recalculer les totaux
        $chargesNonFacturees = HospitalisationCharge::where('hospitalisation_id', $hospitalisation->id)
            ->where('is_billed', false)
            ->get();

        $total = $chargesNonFacturees->sum('total_price');
        $partCabinet = $chargesNonFacturees->sum('part_cabinet');
        $partMedecin = $chargesNonFacturees->sum('part_medecin');

        return response()->json([
            'success' => true,
            'message' => 'Charge ajoutée avec succès',
            'charge' => [
                'id' => $lastCharge->id,
                'type' => $lastCharge->type,
                'description' => $lastCharge->description_snapshot,
                'total_price' => $lastCharge->total_price,
                'created_at' => $lastCharge->created_at->format('d/m/Y H:i')
            ],
            'totals' => [
                'total' => $total,
                'part_cabinet' => $partCabinet,
                'part_medecin' => $partMedecin,
                'count' => $chargesNonFacturees->count()
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $hospitalisation = Hospitalisation::findOrFail($id);

        $request->validate([
            'statut' => 'required|in:en cours,terminé,annulé',
        ]);

        DB::transaction(function () use ($request, $hospitalisation) {
            $ancienStatut = $hospitalisation->statut;
            $nouveauStatut = $request->statut;

            // Si on annule, enregistrer l'utilisateur qui annule
            if ($nouveauStatut === 'annulé' && $hospitalisation->statut !== 'annulé') {
                // Restaurer le stock des médicaments non facturés
                $chargesPharmaNonFacturees = HospitalisationCharge::where('hospitalisation_id', $hospitalisation->id)
                    ->where('type', 'pharmacy')
                    ->where('is_billed', false)
                    ->get();

                foreach ($chargesPharmaNonFacturees as $charge) {
                    if ($charge->source_id) {
                        $medicament = Pharmacie::find($charge->source_id);
                        if ($medicament) {
                            $medicament->ajouterStock($charge->quantity);
                        }
                    }
                }

                $hospitalisation->update([
                    'statut' => $nouveauStatut,
                    'annulated_by' => Auth::id(),
                ]);
            } else {
                // Interdire tout changement de statut si déjà annulé
                if ($hospitalisation->statut === 'annulé') {
                    throw ValidationException::withMessages([
                        'statut' => 'Le statut "annulé" est définitif et ne peut plus être modifié.'
                    ]);
                }
                $hospitalisation->update(['statut' => $nouveauStatut]);
            }

            // Si l'hospitalisation est terminée ou annulée, libérer le lit et clôturer le séjour
            if (in_array($nouveauStatut, ['terminé', 'annulé']) && $ancienStatut === 'en cours') {
                if ($hospitalisation->lit_id) {
                    $lit = Lit::find($hospitalisation->lit_id);
                    if ($lit) {
                        $lit->liberer();
                    }
                }

                // Clôturer le séjour en cours
                $currentStay = \App\Models\HospitalizationRoomStay::where('hospitalisation_id', $hospitalisation->id)
                    ->whereNull('end_at')
                    ->latest('start_at')
                    ->first();
                if ($currentStay) {
                    $currentStay->update(['end_at' => Carbon::now()]);
                }

                // Si terminé, définir la date de sortie si elle n'existe pas
                if ($nouveauStatut === 'terminé' && !$hospitalisation->date_sortie) {
                    $hospitalisation->update(['date_sortie' => Carbon::now()->toDateString()]);
                }
            }

            // Si on remet en cours depuis terminé/annulé, réoccuper le lit
            // Désormais, on interdit tout changement de statut si déjà annulé
            if ($hospitalisation->statut === 'annulé') {
                return;
            }
            if ($nouveauStatut === 'en cours' && in_array($ancienStatut, ['terminé', 'annulé'])) {
                if ($hospitalisation->lit_id) {
                    $lit = Lit::lockForUpdate()->find($hospitalisation->lit_id);
                    if ($lit && $lit->est_libre) {
                        $lit->occuper();

                        // Redémarrer un séjour
                        $chambre = $lit->chambre;
                        if ($chambre) {
                            \App\Models\HospitalizationRoomStay::create([
                                'hospitalisation_id' => $hospitalisation->id,
                                'chambre_id' => $chambre->id,
                                'start_at' => Carbon::now(),
                            ]);
                        }
                    }
                }

                // Supprimer la date de sortie si on remet en cours
                $hospitalisation->update(['date_sortie' => null]);
            }
        });

        return back()->with('success', 'Statut mis à jour avec succès.');
    }

    public function payerTout(Request $request, $id)
    {
        $hospitalisation = Hospitalisation::with(['patient', 'medecin', 'lit.chambre'])->findOrFail($id);

        $request->validate([
            'type' => 'required|string|in:espèces,bankily,masrivi,sedad,carte,virement',
        ]);

        $numeroFacture = null;

        DB::transaction(function () use ($request, $hospitalisation, &$numeroFacture) {
            // Récupérer toutes les charges non facturées
            $charges = HospitalisationCharge::lockForUpdate()
                ->where('hospitalisation_id', $hospitalisation->id)
                ->where('is_billed', false)
                ->get();

            if ($charges->isEmpty()) {
                throw ValidationException::withMessages([
                    'charges' => 'Aucune charge à facturer.'
                ]);
            }

            $total = (float) $charges->sum('total_price');
            $partCabinet = (float) $charges->sum('part_cabinet');
            $partMedecin = (float) $charges->sum('part_medecin');

            $assuranceId = $hospitalisation->assurance_id;
            $couverture = (float) ($hospitalisation->couverture ?? 0);
            // CORRECTION: Le montant du mode de paiement = total des charges (sans déduire la part médecin)
            // La part médecin sera déduite plus tard lors de la validation dans etatcaisse
            $montantPaiement = $assuranceId ? $total * (1 - ($couverture / 100)) : $total;

            // Préparer numeros
            $dernierNumero = Caisse::max('numero_facture') ?? 0;
            $prochainNumero = $dernierNumero + 1;
            $numeroFacture = $prochainNumero; // Stocker pour utilisation en dehors de la transaction

            // Déterminer le médecin principal pour la facturation
            $medecinId = $hospitalisation->medecin_id;

            // Si pas de médecin traitant, utiliser le médecin le plus impliqué dans les examens
            if (!$medecinId) {
                $medecinsParts = [];

                // Analyser tous les examens pour trouver le médecin avec la plus grande part
                foreach ($charges->where('type', 'examen') as $charge) {
                    if ($charge->source_id) {
                        $examen = \App\Models\Examen::find($charge->source_id);
                        if ($examen && $examen->medecin_id) {
                            $medecinId = $examen->medecin_id;
                            if (!isset($medecinsParts[$medecinId])) {
                                $medecinsParts[$medecinId] = 0;
                            }
                            $medecinsParts[$medecinId] += $charge->part_medecin;
                        }
                    }
                }

                // Prendre le médecin avec la plus grande part
                if (!empty($medecinsParts)) {
                    $medecinId = array_keys($medecinsParts, max($medecinsParts))[0];
                }
            }

            // Si toujours pas de médecin, utiliser le premier médecin disponible
            if (!$medecinId) {
                $medecinId = \App\Models\Medecin::first()?->id;
            }

            // Si vraiment aucun médecin, on ne peut pas continuer
            if (!$medecinId) {
                throw ValidationException::withMessages([
                    'medecin' => 'Aucun médecin disponible pour la facturation.'
                ]);
            }

            $numeroEntree = (new CaisseController)->getNextNumeroEntree($medecinId);

            // Utiliser ou créer un examen générique d'hospitalisation
            $serviceHosp = Service::where('type_service', 'HOSPITALISATION')->first() ?? Service::first();
            $examen = Examen::where('idsvc', $serviceHosp?->id)
                ->where('nom', 'Hospitalisation')
                ->where('medecin_id', $medecinId) // Chercher par médecin aussi
                ->first();

            if (!$examen) {
                $examen = Examen::create([
                    'nom' => 'Hospitalisation',
                    'idsvc' => $serviceHosp?->id,
                    'medecin_id' => $medecinId, // Lier l'examen au médecin
                    'tarif' => 0, // Tarif dynamique selon les charges
                    'part_cabinet' => $partCabinet, // Utiliser les parts réelles
                    'part_medecin' => $partMedecin, // Utiliser les parts réelles
                ]);
            } else {
                // Mettre à jour l'examen existant avec les parts réelles
                $examen->update([
                    'part_cabinet' => $partCabinet,
                    'part_medecin' => $partMedecin,
                ]);
            }

            // Créer la caisse
            $caisse = Caisse::create([
                'numero_facture' => $prochainNumero,
                'numero_entre' => $numeroEntree,
                'gestion_patient_id' => $hospitalisation->gestion_patient_id,
                'medecin_id' => $medecinId,
                'prescripteur_id' => null,
                'examen_id' => $examen->id,
                'service_id' => $serviceHosp?->id ?? $examen->idsvc,
                'date_examen' => Carbon::now()->toDateString(),
                'total' => $total,
                'nom_caissier' => \Illuminate\Support\Facades\Auth::user()->name,
                'assurance_id' => $assuranceId,
                'couverture' => $assuranceId ? (int) $couverture : null,
            ]);

            // Etat de caisse - La recette = montant du paiement (total des charges)
            EtatCaisse::create([
                'caisse_id' => $caisse->id,
                'designation' => 'Paiement Total Hospitalisation N°' . $caisse->numero_facture,
                'recette' => $montantPaiement, // Total des charges, pas déduction de la part médecin
                'part_medecin' => $partMedecin,
                'part_clinique' => $partCabinet,
                'assurance_id' => $assuranceId,
                'medecin_id' => $medecinId,
            ]);

            // Log de vérification pour débogage
            Log::info("=== DÉBOGAGE PAIEMENT HOSPITALISATION ===");
            Log::info("Hospitalisation ID: {$hospitalisation->id}");
            Log::info("Caisse ID: {$caisse->id}");
            Log::info("Total charges: {$total} MRU");
            Log::info("Part Cabinet: {$partCabinet} MRU");
            Log::info("Part Médecin: {$partMedecin} MRU");
            Log::info("Montant Paiement (à enregistrer): {$montantPaiement} MRU");
            Log::info("Assurance ID: " . ($assuranceId ?: 'Aucune'));
            Log::info("Couverture: {$couverture}%");
            Log::info("==========================================");

            ModePaiement::create([
                'caisse_id' => $caisse->id,
                'type' => $request->type,
                'montant' => $montantPaiement,
                'source' => 'caisse',
            ]);

            // Marquer toutes les charges comme facturées
            $charges->each(function ($charge) use ($caisse) {
                $charge->update([
                    'is_billed' => true,
                    'billed_at' => Carbon::now(),
                    'caisse_id' => $caisse->id,
                ]);
            });

            // Mettre le statut à "terminé" et le rendre non modifiable
            $updateData = ['statut' => 'terminé'];
            if (!$hospitalisation->date_sortie) {
                $updateData['date_sortie'] = Carbon::now()->toDateString();
            }
            $hospitalisation->update($updateData);

            // Libérer le lit et clôturer le séjour de chambre
            if ($hospitalisation->lit_id) {
                $lit = \App\Models\Lit::find($hospitalisation->lit_id);
                if ($lit) {
                    $lit->liberer();
                }
            }
            $currentStay = \App\Models\HospitalizationRoomStay::where('hospitalisation_id', $hospitalisation->id)
                ->whereNull('end_at')
                ->latest('start_at')
                ->first();
            if ($currentStay) {
                $currentStay->update(['end_at' => Carbon::now()]);
            }
        });

        // Redirection en fonction du rôle de l'utilisateur
        $route = Auth::user()->role?->name === 'admin' ? 'admin.hospitalisations.show' : 'hospitalisations.show';
        return redirect()->route($route, $hospitalisation->id)
            ->with('success', 'Paiement effectué avec succès ! Facture #' . $numeroFacture . ' créée. Statut hospitalisation : Terminé.');
    }

    /**
     * Afficher les détails des médecins d'une hospitalisation
     */
    public function showDoctors($id)
    {
        $hospitalisation = Hospitalisation::with(['patient', 'medecin', 'pharmacien', 'service', 'lit', 'chambre'])
            ->findOrFail($id);

        $doctors = $hospitalisation->getAllInvolvedDoctors();

        // Calculer les totaux
        $totalPartMedecin = $doctors->sum('part_medecin');
        $totalExamens = $doctors->sum(function ($doctor) {
            return count($doctor['examens']);
        });

        return view('hospitalisations.doctors', compact(
            'hospitalisation',
            'doctors',
            'totalPartMedecin',
            'totalExamens'
        ));
    }

    public function showDoctorsByDate($date)
    {
        // Convertir la date en format Carbon
        $targetDate = Carbon::parse($date)->startOfDay();
        $endDate = $targetDate->copy()->endOfDay();

        // Récupérer toutes les hospitalisations de cette date
        $hospitalisations = Hospitalisation::with(['patient', 'medecin', 'service', 'lit.chambre', 'chambre'])
            ->whereBetween('created_at', [$targetDate, $endDate])
            ->get();

        // Collecter tous les médecins impliqués dans toutes les hospitalisations de ce jour
        $allDoctors = collect();
        $totalPartMedecin = 0;
        $totalExamens = 0;

        foreach ($hospitalisations as $hospitalisation) {
            $doctors = $hospitalisation->getAllInvolvedDoctors();

            foreach ($doctors as $doctor) {
                // Vérifier si ce médecin existe déjà dans la collection
                $existingDoctor = $allDoctors->firstWhere('medecin.id', $doctor['medecin']->id);

                if ($existingDoctor) {
                    // Fusionner les données du médecin
                    $existingDoctor['part_medecin'] += $doctor['part_medecin'];
                    $existingDoctor['examens'] = array_merge($existingDoctor['examens'], $doctor['examens']);
                    $existingDoctor['hospitalisations'][] = $hospitalisation->id;
                } else {
                    // Ajouter le médecin avec les informations de l'hospitalisation
                    $doctor['hospitalisations'] = [$hospitalisation->id];
                    $allDoctors->push($doctor);
                }

                $totalPartMedecin += $doctor['part_medecin'];
                $totalExamens += count($doctor['examens']);
            }
        }

        return view('hospitalisations.doctors-by-date', compact(
            'hospitalisations',
            'allDoctors',
            'totalPartMedecin',
            'totalExamens',
            'date'
        ));
    }

    /**
     * Afficher la page d'impression pour une hospitalisation
     */
    public function print($id)
    {
        $hospitalisation = Hospitalisation::with([
            'patient',
            'medecin',
            'service',
            'lit.chambre',
            'charges' => function ($q) {
                $q->where('is_billed', true)
                    ->orderBy('created_at', 'asc');
            }
        ])->findOrFail($id);

        $chargesFacturees = $hospitalisation->charges;

        // Calculer le total des charges facturées
        $totalCharges = $chargesFacturees->sum('total_price');

        // Déterminer l'URL de retour en fonction du rôle
        $backUrl = auth()->user()->role->name === 'admin' 
            ? route('admin.hospitalisations.index') 
            : route('hospitalisations.index');

        return view('hospitalisations.print', compact(
            'hospitalisation',
            'chargesFacturees',
            'totalCharges',
            'backUrl'
        ));
    }

    public function removeCharge(Request $request, $id, $chargeId)
    {
        try {
            $hospitalisation = Hospitalisation::findOrFail($id);

            // Vérifier que l'hospitalisation n'est pas annulée
            if ($hospitalisation->statut === 'annulé') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une charge d\'une hospitalisation annulée'
                ], 403);
            }

            $charge = HospitalisationCharge::where('hospitalisation_id', $id)
                ->where('id', $chargeId)
                ->where('is_billed', false) // Ne peut supprimer que les charges non facturées
                ->firstOrFail();

            // Vérifier que ce n'est pas un ROOM_DAY
            if ($charge->type === 'room_day') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer le ROOM_DAY - Cette charge est protégée'
                ], 403);
            }

            // Restaurer le stock si c'est un médicament
            if ($charge->type === 'pharmacy' && $charge->source_id) {
                $medicament = Pharmacie::find($charge->source_id);
                if ($medicament) {
                    $medicament->ajouterStock($charge->quantity);
                }
            }

            $charge->delete();

            // Recalculer les totaux
            $chargesNonFacturees = HospitalisationCharge::where('hospitalisation_id', $id)
                ->where('is_billed', false)
                ->get();

            $total = $chargesNonFacturees->sum('total_price');
            $partCabinet = $chargesNonFacturees->sum('part_cabinet');
            $partMedecin = $chargesNonFacturees->sum('part_medecin');

            return response()->json([
                'success' => true,
                'message' => 'Charge supprimée avec succès',
                'totals' => [
                    'total' => $total,
                    'part_cabinet' => $partCabinet,
                    'part_medecin' => $partMedecin,
                    'count' => $chargesNonFacturees->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la charge: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche de patients par numéro de téléphone
     */
    public function searchPatientsByPhone(Request $request)
    {
        $phone = $request->get('phone', '');

        if (empty($phone)) {
            // Si pas de téléphone, retourner tous les patients
            $patients = GestionPatient::select('id', 'first_name', 'last_name', 'phone')
                ->orderBy('first_name')
                ->get();
        } else {
            // Rechercher les patients dont le téléphone contient la chaîne recherchée
            $patients = GestionPatient::select('id', 'first_name', 'last_name', 'phone')
                ->where('phone', 'LIKE', '%' . $phone . '%')
                ->orderBy('first_name')
                ->get();
        }

        return response()->json([
            'success' => true,
            'patients' => $patients
        ]);
    }

    /**
     * Trouve ou crée un Examen pour un médicament de pharmacie
     * 
     * @param \App\Models\Pharmacie $pharmacie
     * @param int $medecinId
     * @return \App\Models\Examen
     */
    private function findOrCreateExamenForPharmacy($pharmacie, $medecinId)
    {
        // Chercher un service de type PHARMACIE lié à ce médicament
        $servicePharmacie = Service::where('pharmacie_id', $pharmacie->id)
            ->where('type_service', 'PHARMACIE')
            ->first();

        // Si pas de service, chercher un service PHARMACIE générique
        if (!$servicePharmacie) {
            $servicePharmacie = Service::where('type_service', 'PHARMACIE')
                ->whereNull('pharmacie_id')
                ->first();
        }

        // Si toujours pas de service, créer un service PHARMACIE générique
        if (!$servicePharmacie) {
            $servicePharmacie = Service::create([
                'nom' => 'PHARMACIE',
                'type_service' => 'PHARMACIE',
                'pharmacie_id' => null,
            ]);
        }

        // Chercher un examen existant pour ce médicament
        $examen = Examen::where('nom', $pharmacie->nom_medicament)
            ->where('idsvc', $servicePharmacie->id)
            ->first();

        // Si pas d'examen, en créer un
        if (!$examen) {
            $examen = Examen::create([
                'nom' => $pharmacie->nom_medicament,
                'idsvc' => $servicePharmacie->id,
                'medecin_id' => $medecinId,
                'tarif' => $pharmacie->prix_vente,
                'part_cabinet' => $pharmacie->prix_vente,
                'part_medecin' => 0, // PHARMACIE: pas de part médecin
            ]);
        }

        return $examen;
    }
}
