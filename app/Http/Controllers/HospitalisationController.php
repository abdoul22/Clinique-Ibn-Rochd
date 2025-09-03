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
        $medecins = Medecin::orderByRaw("FIELD(fonction, 'Pr', 'Dr', 'Tss', 'SGF', 'IDE')")
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
            'medecin_id' => 'required|exists:medecins,id',
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
        $hospitalisation = Hospitalisation::with([
            'patient',
            'medecin',
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
        $examens = Examen::where('nom', 'NOT LIKE', 'Hospitalisation - %')
            ->orderBy('nom')
            ->get();
        $medicaments = Pharmacie::where('statut', 'actif')
            ->where('stock', '>', 0)
            ->orderBy('nom_medicament')
            ->get();

        return view('hospitalisations.show', compact(
            'hospitalisation',
            'chargesNonFacturees',
            'chargesFacturees',
            'totaux',
            'examens',
            'medicaments',
            'joursHospitalisation',
            'dureeSejour'
        ));
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

            HospitalisationCharge::create([
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
                'created_at' => $dateCharge,
            ]);
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
            $patientPart = $assuranceId ? $total * (1 - ($couverture / 100)) : $total;

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

            // Etat de caisse (recette = part patient)
            EtatCaisse::create([
                'caisse_id' => $caisse->id,
                'designation' => 'Facture Hospitalisation N°' . $caisse->numero_facture,
                'recette' => $patientPart,
                'part_medecin' => $partMedecin,
                'part_clinique' => $partCabinet,
                'assurance_id' => $assuranceId,
                'medecin_id' => $medecinId,
            ]);

            // Paiement (uniquement part patient)
            ModePaiement::create([
                'caisse_id' => $caisse->id,
                'type' => $request->type,
                'montant' => $patientPart,
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
        $medecins = Medecin::all();
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

        $request->validate([
            'charge_type' => 'required|in:examen,service,pharmacy',
            'examen_id' => 'required_if:charge_type,examen,service|nullable|exists:examens,id',
            'medicament_id' => 'required_if:charge_type,pharmacy|nullable|exists:pharmacies,id',
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

                HospitalisationCharge::create([
                    'hospitalisation_id' => $hospitalisation->id,
                    'type' => $request->charge_type,
                    'source_id' => $ex->id,
                    'description_snapshot' => $ex->nom,
                    'unit_price' => $ex->tarif,
                    'quantity' => $qty,
                    'total_price' => $ex->tarif * $qty,
                    'part_medecin' => ($ex->part_medecin ?? 0) * $qty,
                    'part_cabinet' => ($ex->part_cabinet ?? 0) * $qty,
                    'is_pharmacy' => false,
                ]);
            }
        });

        return back()->with('success', 'Charge ajoutée avec succès.');
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
            $patientPart = $assuranceId ? $total * (1 - ($couverture / 100)) : $total;

            // Préparer numeros
            $dernierNumero = Caisse::max('numero_facture') ?? 0;
            $prochainNumero = $dernierNumero + 1;
            $numeroFacture = $prochainNumero; // Stocker pour utilisation en dehors de la transaction
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

            // Etat de caisse
            EtatCaisse::create([
                'caisse_id' => $caisse->id,
                'designation' => 'Paiement Total Hospitalisation N°' . $caisse->numero_facture,
                'recette' => $patientPart,
                'part_medecin' => $partMedecin,
                'part_clinique' => $partCabinet,
                'assurance_id' => $assuranceId,
                'medecin_id' => $medecinId,
            ]);

            // Paiement
            ModePaiement::create([
                'caisse_id' => $caisse->id,
                'type' => $request->type,
                'montant' => $patientPart,
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
}
