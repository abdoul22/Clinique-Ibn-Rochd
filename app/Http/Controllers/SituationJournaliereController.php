<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\ModePaiement;
use App\Models\Credit;
use App\Models\EtatCaisse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SituationJournaliereController extends Controller
{
    public function index(Request $request)
    {
        // Default to today if no date specified
        $date = $request->input('date', now()->format('Y-m-d'));
        $dateCarbon = Carbon::parse($date);

        // Get caisses for the selected date with all relationships
        // Charger aussi les examens avec leurs services pour éviter les requêtes N+1
        $caisses = Caisse::whereDate('date_examen', $date)
            ->with(['service', 'examen.service', 'medecin', 'mode_paiements'])
            ->get();
        
        // Précharger tous les examens qui seront utilisés depuis examens_data pour éviter les requêtes N+1
        $examensIds = [];
        foreach ($caisses as $caisse) {
            if ($caisse->examens_data) {
                $examensData = is_string($caisse->examens_data) ? json_decode($caisse->examens_data, true) : $caisse->examens_data;
                foreach ($examensData as $examenData) {
                    if (isset($examenData['id'])) {
                        $examensIds[] = $examenData['id'];
                    }
                }
            }
        }
        
        // Charger tous les examens avec leurs services en une seule requête et les mettre en cache
        $examensMap = [];
        if (!empty($examensIds)) {
            $examens = \App\Models\Examen::whereIn('id', array_unique($examensIds))->with('service')->get();
            foreach ($examens as $examen) {
                $examensMap[$examen->id] = $examen;
            }
        }

        // Build data structure grouped by actual service
        $servicesData = [];

        foreach ($caisses as $caisse) {
            $medecin = $caisse->medecin;
            
            if (!$medecin) {
                continue;
            }

            $medecinId = $medecin->id;

            // Check if this caisse has multiple exams (examens_data)
            if ($caisse->examens_data) {
                // Mode examens multiples - traiter chaque examen séparément
                $examensData = is_string($caisse->examens_data) ? json_decode($caisse->examens_data, true) : $caisse->examens_data;
                
                foreach ($examensData as $examenData) {
                    // Utiliser la map préchargée si disponible, sinon faire une requête
                    $examen = isset($examensMap[$examenData['id']]) 
                        ? $examensMap[$examenData['id']] 
                        : \App\Models\Examen::find($examenData['id']);
                    
                    if (!$examen) {
                        continue;
                    }

                    // Get the service for this examen
                    $service = $examen->service;
                    
                    if (!$service) {
                        continue;
                    }

                    // Determine service name and ID
                    $serviceName = $service->nom;
                    
                    // Use the centralized method to detect PHARMACIE service
                    if ($this->isPharmacieService($service, $examen)) {
                        $serviceName = 'PHARMACIE';
                        // Use a consistent service ID for all pharmacy items
                        $serviceId = 'pharmacie-group';
                    } else {
                        $serviceId = $service->id;
                    }

                    // Initialize service if not exists
                    if (!isset($servicesData[$serviceId])) {
                        $servicesData[$serviceId] = [
                            'service_name' => $serviceName,
                            'medecins' => [],
                            'total_actes' => 0,
                        ];
                    }

                    // Initialize medecin if not exists
                    if (!isset($servicesData[$serviceId]['medecins'][$medecinId])) {
                        $servicesData[$serviceId]['medecins'][$medecinId] = [
                            'nom' => $medecin->nomcomplet,
                            'examens' => [],
                            'nombre_actes' => 0,
                        ];
                    }

                    // Track examen with quantity
                    $examenNom = $examen->nom;
                    $quantite = $examenData['quantite'] ?? 1;
                    
                    if (!isset($servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom])) {
                        $servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom] = 0;
                    }
                    $servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom] += $quantite;

                    // Count acts
                    $servicesData[$serviceId]['medecins'][$medecinId]['nombre_actes'] += $quantite;
                    $servicesData[$serviceId]['total_actes'] += $quantite;
                }
            } else {
                // Mode examen unique (ancien format) - compatibilité avec les anciennes factures
                
                // Vérifier si c'est une hospitalisation sans examens_data
                $examen = $caisse->examen;
                if ($examen && strtolower($examen->nom) === 'hospitalisation' && !$caisse->examens_data) {
                    // Décomposer l'hospitalisation en charges individuelles
                    $this->decomposeHospitalisationCharges($caisse, $servicesData, $medecinId, $medecin);
                    continue; // Passer à la caisse suivante
                }

                // Get the actual service - PRIORITY:
                // 1. From examen->service (via idsvc) - THIS IS THE REAL SERVICE
                // 2. From caisse->service (direct relationship) - FALLBACK
                $service = null;

                // First try to get from examen (most reliable)
                if ($examen && $examen->service) {
                    $service = $examen->service;
                }
                // Fallback to caisse service
                elseif ($caisse->service) {
                    $service = $caisse->service;
                }

                // Skip if no service found
                if (!$service) {
                    continue;
                }

                // Check if this is a pharmaceutical product
                // Group all pharmacy items under "PHARMACIE" service
                $serviceName = $service->nom;

                // Use the centralized method to detect PHARMACIE service
                if ($this->isPharmacieService($service, $examen)) {
                    $serviceName = 'PHARMACIE';
                    // Use a consistent service ID for all pharmacy items
                    $serviceId = 'pharmacie-group';
                } else {
                    $serviceId = $service->id;
                }

                // Initialize service if not exists
                if (!isset($servicesData[$serviceId])) {
                    $servicesData[$serviceId] = [
                        'service_name' => $serviceName,
                        'medecins' => [],
                        'total_actes' => 0,
                    ];
                }

                // Initialize medecin if not exists
                if (!isset($servicesData[$serviceId]['medecins'][$medecinId])) {
                    $servicesData[$serviceId]['medecins'][$medecinId] = [
                        'nom' => $medecin->nomcomplet,
                        'examens' => [],
                        'nombre_actes' => 0,
                    ];
                }

                // Track examen
                $examenNom = $examen ? $examen->nom : 'Examen inconnu';
                if (!isset($servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom])) {
                    $servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom] = 0;
                }
                $servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom] += 1;

                // Count acts
                $servicesData[$serviceId]['medecins'][$medecinId]['nombre_actes'] += 1;
                $servicesData[$serviceId]['total_actes'] += 1;
            }
        }

        // Convert medecins arrays to indexed arrays for the view
        foreach ($servicesData as &$service) {
            $service['medecins'] = array_values($service['medecins']);
        }

        // Calculate only total acts
        $totalActes = array_sum(array_column($servicesData, 'total_actes'));

        // Get payment modes used for this date (only online payments: bankily, masrivi, sedad)
        // Utiliser EtatCaisse->recette pour les factures pour éviter les doublons
        $paiementsEnLigne = collect();
        
        // Récupérer les caisses_id uniques pour cette date
        $caissesIds = Caisse::whereDate('date_examen', $date)->pluck('id');
        
        if ($caissesIds->count() > 0) {
            // Pour chaque type de paiement en ligne, calculer la somme depuis EtatCaisse
            foreach (['bankily', 'masrivi', 'sedad'] as $type) {
                $total = EtatCaisse::whereNotNull('caisse_id')
                    ->whereIn('caisse_id', $caissesIds)
                    ->whereHas('caisse.mode_paiements', function ($query) use ($type) {
                        $query->where('type', $type);
                    })
                    ->sum('recette');
                
                if ($total > 0) {
                    $paiementsEnLigne->put($type, (object)['type' => $type, 'total' => $total]);
                }
            }
        }

        // Check if there are any online payments
        $hasOnlinePayments = $paiementsEnLigne->isNotEmpty();
        
        // Calculer le total des paiements en ligne
        $totalPaiementsEnLigne = $paiementsEnLigne->sum(function($item) {
            return $item->total ?? 0;
        });

        // Get credits for this date (either linked to caisse of that date OR created on that date)
        $creditsPersonnel = Credit::where('source_type', 'App\Models\Personnel')
            ->where('status', '!=', 'payé')
            ->where(function ($query) use ($date) {
                $query->whereHas('caisse', function ($q) use ($date) {
                    $q->whereDate('date_examen', $date);
                })
                    ->orWhereDate('created_at', $date);
            })
            ->sum('montant');

        $creditsAssurance = Credit::where('source_type', 'App\Models\Assurance')
            ->where('status', '!=', 'payé')
            ->where(function ($query) use ($date) {
                $query->whereHas('caisse', function ($q) use ($date) {
                    $q->whereDate('date_examen', $date);
                })
                    ->orWhereDate('created_at', $date);
            })
            ->sum('montant');

        // Calculate total part médecin for the date
        $totalPartMedecin = $caisses->sum(function ($caisse) {
            return $caisse->mode_paiements->where('source', 'part_medecin')->sum('montant');
        });

        // Get total pharmacie for the date
        $totalPharmacie = $caisses->sum(function ($caisse) {
            $service = $caisse->examen && $caisse->examen->service
                ? $caisse->examen->service
                : $caisse->service;

            if (!$service) {
                return 0;
            }

            // Use the centralized method to detect PHARMACIE service
            if ($this->isPharmacieService($service, $caisse->examen)) {
                return $caisse->total ?? 0;
            }

            return 0;
        });

        return view('situation-journaliere.index', compact(
            'date',
            'dateCarbon',
            'servicesData',
            'totalActes',
            'paiementsEnLigne',
            'hasOnlinePayments',
            'totalPaiementsEnLigne',
            'creditsPersonnel',
            'creditsAssurance',
            'totalPartMedecin',
            'totalPharmacie'
        ));
    }

    public function print(Request $request)
    {
        return $this->index($request);
    }

    public function exportPdf(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Détermine si un service/examen est de type PHARMACIE
     * Aligné avec la logique de RecapitulatifServiceJournalierController::determineServiceKey()
     * 
     * @param \App\Models\Service|null $service
     * @param \App\Models\Examen|null $examen
     * @return bool
     */
    private function isPharmacieService($service, $examen = null)
    {
        // Vérifier d'abord par le nom de l'examen (comme dans determineServiceKey)
        if ($examen && $examen->nom) {
            $nomExamen = strtolower($examen->nom);
            
            // Identifier les médicaments par leur nom (patterns communs)
            $pharmaciePatterns = [
                'flagyl',
                'novalgin',
                'ssi',
                'kit',
                'bande',
                'velpo',
                'gant',
                'steril',
                'mg'
            ];
            
            foreach ($pharmaciePatterns as $pattern) {
                if (strpos($nomExamen, $pattern) !== false) {
                    return true;
                }
            }
        }

        // Si pas de service, retourner false
        if (!$service) {
            return false;
        }

        // Vérifier par le type_service du service
        $serviceType = strtoupper($service->type_service ?? '');
        if ($serviceType === 'PHARMACIE') {
            return true;
        }

        // Vérifier par le nom du service
        if (strtoupper($service->nom ?? '') === 'PHARMACIE') {
            return true;
        }

        // Vérifier si le service est lié à un médicament (pharmacie_id)
        if ($service->pharmacie_id !== null) {
            return true;
        }

        return false;
    }

    /**
     * Décompose une hospitalisation en charges individuelles pour la situation journalière
     * Utilisé comme fallback pour les anciennes factures sans examens_data
     * 
     * @param \App\Models\Caisse $caisse
     * @param array &$servicesData
     * @param int $medecinId
     * @param \App\Models\Medecin $medecin
     * @return void
     */
    private function decomposeHospitalisationCharges($caisse, &$servicesData, $medecinId, $medecin)
    {
        // Récupérer l'hospitalisation associée à cette caisse
        $hospitalisation = \App\Models\Hospitalisation::where('gestion_patient_id', $caisse->gestion_patient_id)
            ->whereDate('date_entree', $caisse->date_examen)
            ->first();

        if (!$hospitalisation) {
            // Si pas d'hospitalisation trouvée, traiter comme un examen normal HOSPITALISATION
            $serviceId = 'HOSPITALISATION';
            if (!isset($servicesData[$serviceId])) {
                $servicesData[$serviceId] = [
                    'service_name' => 'HOSPITALISATION',
                    'medecins' => [],
                    'total_actes' => 0,
                ];
            }
            if (!isset($servicesData[$serviceId]['medecins'][$medecinId])) {
                $servicesData[$serviceId]['medecins'][$medecinId] = [
                    'nom' => $medecin->nomcomplet,
                    'examens' => [],
                    'nombre_actes' => 0,
                ];
            }
            $servicesData[$serviceId]['medecins'][$medecinId]['examens']['Hospitalisation'] = 1;
            $servicesData[$serviceId]['medecins'][$medecinId]['nombre_actes'] += 1;
            $servicesData[$serviceId]['total_actes'] += 1;
            return;
        }

        // Récupérer les charges facturées de cette hospitalisation liées à cette caisse
        $charges = \App\Models\HospitalisationCharge::where('hospitalisation_id', $hospitalisation->id)
            ->where('caisse_id', $caisse->id)
            ->where('is_billed', true)
            ->get();

        // Si pas de charges trouvées via caisse_id, essayer de récupérer toutes les charges facturées
        if ($charges->isEmpty()) {
            $charges = \App\Models\HospitalisationCharge::where('hospitalisation_id', $hospitalisation->id)
                ->where('is_billed', true)
                ->whereDate('billed_at', $caisse->date_examen)
                ->get();
        }

        foreach ($charges as $charge) {
            // Classifier la charge selon son type
            $serviceKey = $this->classifyChargeForService($charge);
            
            // Déterminer le service ID
            if ($serviceKey === 'PHARMACIE') {
                $serviceId = 'pharmacie-group';
            } else {
                // Pour les autres services, utiliser le service ID réel si disponible
                $serviceId = $serviceKey;
            }

            // Initialiser le service si nécessaire
            if (!isset($servicesData[$serviceId])) {
                $servicesData[$serviceId] = [
                    'service_name' => $serviceKey,
                    'medecins' => [],
                    'total_actes' => 0,
                ];
            }

            // Initialiser le médecin si nécessaire
            if (!isset($servicesData[$serviceId]['medecins'][$medecinId])) {
                $servicesData[$serviceId]['medecins'][$medecinId] = [
                    'nom' => $medecin->nomcomplet,
                    'examens' => [],
                    'nombre_actes' => 0,
                ];
            }

            // Ajouter l'examen/charge
            $examenNom = $charge->description_snapshot;
            $quantite = $charge->quantity ?? 1;

            if (!isset($servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom])) {
                $servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom] = 0;
            }
            $servicesData[$serviceId]['medecins'][$medecinId]['examens'][$examenNom] += $quantite;

            // Compter les actes
            $servicesData[$serviceId]['medecins'][$medecinId]['nombre_actes'] += $quantite;
            $servicesData[$serviceId]['total_actes'] += $quantite;
        }
    }

    /**
     * Classifie une charge d'hospitalisation selon son type de service
     * Aligné avec la logique de RecapitulatifServiceJournalierController::classifyCharge()
     * 
     * @param \App\Models\HospitalisationCharge $charge
     * @return string
     */
    private function classifyChargeForService($charge)
    {
        $description = strtolower($charge->description_snapshot);

        // Identifier les médicaments
        if (
            $charge->is_pharmacy ||
            $charge->type === 'pharmacy' ||
            strpos($description, 'flagyl') !== false ||
            strpos($description, 'novalgin') !== false ||
            strpos($description, 'ssi') !== false ||
            strpos($description, 'kit') !== false ||
            strpos($description, 'mg') !== false
        ) {
            return 'PHARMACIE';
        }

        // Identifier les examens d'exploration fonctionnelle
        if (
            strpos($description, 'ecg') !== false ||
            strpos($description, 'echo') !== false ||
            strpos($description, 'radiologie') !== false ||
            strpos($description, 'scanner') !== false ||
            strpos($description, 'eeg') !== false
        ) {
            return 'EXPLORATIONS FONCTIONNELLES';
        }

        // Identifier les consultations
        if (
            strpos($description, 'consultation') !== false ||
            strpos($description, 'cs') !== false ||
            strpos($description, 'dr.') !== false
        ) {
            return 'CONSULTATIONS EXTERNES';
        }

        // Identifier les chambres (ROOM_DAY)
        if (
            $charge->type === 'room_day' ||
            strpos($description, 'chambre') !== false ||
            strpos($description, 'room_day') !== false ||
            strpos($description, 'lit') !== false
        ) {
            return 'HOSPITALISATION';
        }

        // Par défaut, retourner le type de charge en majuscules
        return strtoupper($charge->type ?? 'HOSPITALISATION');
    }
}
