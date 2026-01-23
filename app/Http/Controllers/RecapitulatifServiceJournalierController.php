<?php

namespace App\Http\Controllers;

use App\Models\RecapitulatifServiceJournalier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Caisse;
use Carbon\Carbon;

class RecapitulatifServiceJournalierController extends Controller
{
    public function index(Request $request)
    {
        // Construire la requête de base avec filtres
        $query = Caisse::with(['service', 'examen']);

        // Appliquer les filtres de période
        $period = $request->get('period', 'all');

        if ($period === 'day' && $request->filled('date')) {
            $query->whereDate('date_examen', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $query->whereBetween('date_examen', [$start, $end]);
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->whereYear('date_examen', $parts[0])
                    ->whereMonth('date_examen', $parts[1]);
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $query->whereYear('date_examen', $request->year);
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('date_examen', [$request->date_start, $request->date_end]);
        }

        // Filtre par service spécifique
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // Récupérer les caisses filtrées
        $caisses = $query->get();

        // Décomposer les examens multiples et grouper par service
        $recapParService = [];

        foreach ($caisses as $caisse) {
            $jour = $caisse->date_examen->format('Y-m-d');

            if ($caisse->examens_data) {
                // Mode examens multiples
                $examensData = is_string($caisse->examens_data) ? json_decode($caisse->examens_data, true) : $caisse->examens_data;
                foreach ($examensData as $examenData) {
                    $examen = \App\Models\Examen::find($examenData['id']);
                    if ($examen) {
                        // Déterminer le service selon le type d'examen
                        $serviceId = $examen->idsvc;
                        $service = \App\Models\Service::find($serviceId);

                        // Déterminer le service basé sur le nom de l'examen et le service
                        $serviceKey = $this->determineServiceKey($examen, $service, $serviceId);

                        if (!isset($recapParService[$serviceKey])) {
                            $recapParService[$serviceKey] = [];
                        }
                        if (!isset($recapParService[$serviceKey][$jour])) {
                            $recapParService[$serviceKey][$jour] = [
                                'total' => 0,
                                'nombre' => 0
                            ];
                        }

                        $montant = $examen->tarif * $examenData['quantite'];
                        $recapParService[$serviceKey][$jour]['total'] += $montant;
                        $recapParService[$serviceKey][$jour]['nombre'] += $examenData['quantite'];
                    }
                }
            } else {
                // Mode examen unique
                $examen = $caisse->examen;

                // Vérifier si c'est une hospitalisation
                if ($examen && strtolower($examen->nom) === 'hospitalisation') {
                    // Décomposer l'hospitalisation en charges individuelles
                    $this->decomposeHospitalisation($caisse, $recapParService, $jour);
                } else {
                    // Examen normal
                    $service = $caisse->service;
                    
                    // Si pas de service direct, essayer via l'examen
                    if (!$service && $examen) {
                        $service = $examen->service;
                    }
                    
                    // Déterminer le serviceId et serviceKey
                    if ($service) {
                        $serviceId = $service->id;
                        // Utiliser la méthode determineServiceKey pour cohérence
                        $serviceKey = $this->determineServiceKey($examen ?? new \App\Models\Examen(), $service, $serviceId);
                    } else {
                        // Fallback: si vraiment aucun service trouvé, utiliser un identifiant unique
                        $serviceKey = 'SANS_SERVICE_' . ($caisse->id ?? 'unknown');
                        \Illuminate\Support\Facades\Log::warning("Caisse {$caisse->id} sans service trouvé");
                    }

                    if (!isset($recapParService[$serviceKey])) {
                        $recapParService[$serviceKey] = [];
                    }
                    if (!isset($recapParService[$serviceKey][$jour])) {
                        $recapParService[$serviceKey][$jour] = [
                            'total' => 0,
                            'nombre' => 0
                        ];
                    }

                    $recapParService[$serviceKey][$jour]['total'] += $caisse->total;
                    $recapParService[$serviceKey][$jour]['nombre'] += 1;
                }
            }
        }

        // Convertir en format compatible avec la vue existante
        $recaps = collect();
        foreach ($recapParService as $serviceKey => $jours) {
            foreach ($jours as $jour => $data) {
                $recaps->push((object)[
                    'service_key' => $serviceKey,
                    'jour' => $jour,
                    'total' => $data['total'],
                    'nombre' => $data['nombre']
                ]);
            }
        }

        // Trier uniquement par date décroissante (plus récent au plus ancien)
        $recaps = $recaps->sortByDesc(function ($item) {
            try {
                return $item->jour ? Carbon::parse($item->jour)->timestamp : 0;
            } catch (\Exception $e) {
                return 0;
            }
        })->values();

        // Pagination manuelle
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedRecaps = $recaps->slice($offset, $perPage);

        // Créer un objet de pagination personnalisé
        $recaps = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedRecaps,
            $recaps->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );

        // Charger les services séparément pour éviter les problèmes de relations
        $serviceIds = $recaps->pluck('service_key')->unique()->filter();
        $services = [];
        if ($serviceIds->count() > 0) {
            $numericIds = $serviceIds->filter(fn($k) => is_numeric($k))->values();
            $map = [];
            if ($numericIds->count() > 0) {
                $map = \App\Models\Service::whereIn('id', $numericIds)->pluck('nom', 'id')->toArray();
            }
            foreach ($serviceIds as $key) {
                if ($key === 'PHARMACIE') {
                    $services[$key] = 'PHARMACIE';
                } elseif ($key === 'HOSPITALISATION') {
                    $services[$key] = 'HOSPITALISATION';
                } elseif ($key === 'EXPLORATIONS_FONCTIONNELLES') {
                    $services[$key] = 'EXPLORATIONS FONCTIONNELLES';
                } elseif ($key === 'CONSULTATIONS_EXTERNES') {
                    $services[$key] = 'CONSULTATIONS EXTERNES';
                } elseif (isset($map[$key])) {
                    $services[$key] = $map[$key];
                } else {
                    // Essayer de récupérer le service depuis la base de données
                    $serviceModel = \App\Models\Service::find($key);
                    if ($serviceModel) {
                        $services[$key] = $serviceModel->nom;
                    } else {
                        // Fallback: utiliser la clé elle-même
                        $services[$key] = $key;
                    }
                }
            }
        }

        // Calculer les totaux pour le résumé à partir des données filtrées
        $totalActes = $recaps->sum('nombre');
        $totalRecettes = $recaps->sum('total');

        $resume = [
            'total_actes' => $totalActes,
            'total_recettes' => $totalRecettes,
        ];

        // Récupérer tous les services pour le filtre
        $allServices = \App\Models\Service::orderBy('nom')->get();

        return view('recap-services.index', compact('recaps', 'services', 'resume', 'allServices'));
    }

    public function show($id)
    {
        $recap = RecapitulatifServiceJournalier::with('service')->findOrFail($id);
        return view('recap-services.show', compact('recap'));
    }

    public function print(Request $request)
    {
        // Construire la requête de base (même logique que index)
        $query = Caisse::with('service')
            ->select([
                'service_id',
                DB::raw('DATE(CONVERT_TZ(date_examen, "+00:00", "+00:00")) as jour'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as nombre'),
            ])
            ->groupBy('service_id', DB::raw('DATE(CONVERT_TZ(date_examen, "+00:00", "+00:00"))'));

        // Filtrage par période
        $period = $request->get('period', 'all');

        if ($period === 'day' && $request->filled('date')) {
            $query->whereDate('date_examen', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $query->whereBetween('date_examen', [$start, $end]);
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->whereYear('date_examen', $parts[0])
                    ->whereMonth('date_examen', $parts[1]);
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $query->whereYear('date_examen', $request->year);
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('date_examen', [$request->date_start, $request->date_end]);
        }

        // Filtre de recherche par nom de service
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('service', function ($q) use ($searchTerm) {
                $q->where('nom', 'like', "%{$searchTerm}%");
            });
        }

        // Filtre par service spécifique
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $recaps = $query->orderBy('jour', 'desc')
            ->orderBy('service_id')
            ->get(); // Pas de pagination pour l'impression

        // Charger les services séparément
        $serviceIds = $recaps->pluck('service_id')->unique()->filter();
        $services = [];
        if ($serviceIds->count() > 0) {
            $services = \App\Models\Service::whereIn('id', $serviceIds)
                ->get()
                ->mapWithKeys(function ($s) {
                    $label = $s->type_service === 'PHARMACIE' ? 'PHARMACIE' : $s->nom;
                    return [$s->id => $label];
                })->toArray();
        }

        // Calculer les totaux pour le résumé - Version simplifiée
        $totalActes = $recaps->sum('nombre');
        $totalRecettes = $recaps->sum('total');

        $resume = [
            'total_actes' => $totalActes,
            'total_recettes' => $totalRecettes,
        ];

        // Debug temporaire
        Log::info('Print - Totaux calculés:', [
            'total_actes' => $resume['total_actes'],
            'total_recettes' => $resume['total_recettes'],
            'recaps_count' => $recaps->count(),
            'services_count' => count($services)
        ]);

        return view('recap-services.print', compact('recaps', 'services', 'resume'));
    }

    public function exportPdf(Request $request)
    {
        // Construire la requête de base (même logique que index)
        $query = Caisse::with('service')
            ->select([
                'service_id',
                DB::raw('DATE(CONVERT_TZ(date_examen, "+00:00", "+00:00")) as jour'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as nombre'),
            ])
            ->groupBy('service_id', DB::raw('DATE(CONVERT_TZ(date_examen, "+00:00", "+00:00"))'));

        // Filtrage par période
        $period = $request->get('period', 'all');

        if ($period === 'day' && $request->filled('date')) {
            $query->whereDate('date_examen', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $query->whereBetween('date_examen', [$start, $end]);
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->whereYear('date_examen', $parts[0])
                    ->whereMonth('date_examen', $parts[1]);
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $query->whereYear('date_examen', $request->year);
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('date_examen', [$request->date_start, $request->date_end]);
        }

        // Filtre de recherche par nom de service
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('service', function ($q) use ($searchTerm) {
                $q->where('nom', 'like', "%{$searchTerm}%");
            });
        }

        // Filtre par service spécifique
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $recaps = $query->orderBy('jour', 'desc')
            ->orderBy('service_id')
            ->get();

        // Charger les services séparément
        $serviceIds = $recaps->pluck('service_id')->unique()->filter();
        $services = [];
        if ($serviceIds->count() > 0) {
            $services = \App\Models\Service::whereIn('id', $serviceIds)->pluck('nom', 'id')->toArray();
        }

        // Calculer les totaux pour le résumé - Version simplifiée
        $totalActes = $recaps->sum('nombre');
        $totalRecettes = $recaps->sum('total');

        $resume = [
            'total_actes' => $totalActes,
            'total_recettes' => $totalRecettes,
        ];

        $pdf = Pdf::loadView('recap-services.export_pdf', compact('recaps', 'services', 'resume'));
        return $pdf->download('recap-services.pdf');
    }

    /**
     * Détermine la clé de service basée sur le type du service
     */
    private function determineServiceKey($examen, $service, $serviceId)
    {
        $nomExamen = strtolower($examen->nom);

        // DEBUG: Log pour voir ce qui se passe
        Log::info("Classification examen: {$examen->nom} -> Service: " . ($service ? $service->nom : 'N/A') . " (Type: " . ($service ? $service->type_service : 'N/A') . ")");

        // Seuls les examens ROOM_DAY ou Hospitalisation avec "Chambre" sont comptabilisés comme hospitalisation
        if (
            strpos($nomExamen, 'room_day') !== false ||
            (strpos($nomExamen, 'hospitalisation') !== false && strpos($nomExamen, 'chambre') !== false)
        ) {
            Log::info("  -> Classé comme HOSPITALISATION");
            return 'HOSPITALISATION';
        }

        // Identifier les médicaments par leur nom
        if (
            strpos($nomExamen, 'flagyl') !== false ||
            strpos($nomExamen, 'novalgin') !== false ||
            strpos($nomExamen, 'ssi') !== false ||
            strpos($nomExamen, 'kit') !== false ||
            strpos($nomExamen, 'mg') !== false
        ) {
            Log::info("  -> Classé comme PHARMACIE");
            return 'PHARMACIE';
        }

        // Si le service existe, utiliser son type_service plutôt que de deviner par le nom
        if ($service) {
            $serviceType = strtoupper($service->type_service);
            Log::info("  -> Utilisant le type du service: {$serviceType}");
            
            // Retourner l'ID du service pour les types spécifiques (au lieu de les hardcoder)
            // Cela permet à chaque service d'avoir son propre groupement
            if (in_array($serviceType, ['IMAGERIE MEDICALE', 'LABORATOIRE', 'MEDECINE DENTAIRE', 'BLOC OPERATOIRE', 'INFIRMERIE'])) {
                Log::info("  -> Retournant service ID {$serviceId} pour type {$serviceType}");
                return $serviceId;
            }
            
            // Pour les types génériques, utiliser le type lui-même
            if ($serviceType === 'PHARMACIE') {
                Log::info("  -> Classé comme PHARMACIE");
                return 'PHARMACIE';
            }
            
            if ($serviceType === 'HOSPITALISATION') {
                Log::info("  -> Classé comme HOSPITALISATION");
                return 'HOSPITALISATION';
            }
            
            if ($serviceType === 'CONSULTATIONS EXTERNES' || $serviceType === 'CONSULTATIONS_EXTERNES') {
                Log::info("  -> Classé comme CONSULTATIONS_EXTERNES");
                return 'CONSULTATIONS_EXTERNES';
            }
            
            // Pour les explorations fonctionnelles (si le service a ce type)
            if ($serviceType === 'EXPLORATIONS FONCTIONNELLES' || $serviceType === 'EXPLORATIONS_FONCTIONNELLES') {
                Log::info("  -> Classé comme EXPLORATIONS_FONCTIONNELLES");
                return 'EXPLORATIONS_FONCTIONNELLES';
            }
            
            // Par défaut, utiliser le service ID
            Log::info("  -> Type non reconnu, utilisant service ID {$serviceId}");
            return $serviceId;
        }

        // Fallback si pas de service
        Log::warning("  -> Pas de service trouvé, utilisant service ID {$serviceId}");
        return $serviceId;
    }

    /**
     * Décompose une hospitalisation en charges individuelles
     */
    private function decomposeHospitalisation($caisse, &$recapParService, $jour)
    {
        // Récupérer l'hospitalisation associée à cette caisse
        $hospitalisation = \App\Models\Hospitalisation::where('gestion_patient_id', $caisse->gestion_patient_id)
            ->whereDate('date_entree', $caisse->date_examen)
            ->first();

        if (!$hospitalisation) {
            // Si pas d'hospitalisation trouvée, traiter comme un examen normal
            $serviceKey = 'HOSPITALISATION';
            if (!isset($recapParService[$serviceKey])) {
                $recapParService[$serviceKey] = [];
            }
            if (!isset($recapParService[$serviceKey][$jour])) {
                $recapParService[$serviceKey][$jour] = [
                    'total' => 0,
                    'nombre' => 0
                ];
            }
            $recapParService[$serviceKey][$jour]['total'] += $caisse->total;
            $recapParService[$serviceKey][$jour]['nombre'] += 1;
            return;
        }

        // Vérifier si cette hospitalisation a déjà été traitée pour éviter les doublons
        static $hospitalisationsTraitees = [];
        $key = $hospitalisation->id . '_' . $jour;

        if (isset($hospitalisationsTraitees[$key])) {
            // Cette hospitalisation a déjà été traitée, ne pas la compter à nouveau
            return;
        }

        $hospitalisationsTraitees[$key] = true;

        // Récupérer les charges de l'hospitalisation
        $charges = \App\Models\HospitalisationCharge::where('hospitalisation_id', $hospitalisation->id)->get();

        Log::info("Décomposition hospitalisation ID {$hospitalisation->id} - {$charges->count()} charges trouvées");

        foreach ($charges as $charge) {
            $serviceKey = $this->classifyCharge($charge);

            Log::info("Charge: {$charge->description_snapshot} -> Service: {$serviceKey}");

            if (!isset($recapParService[$serviceKey])) {
                $recapParService[$serviceKey] = [];
            }
            if (!isset($recapParService[$serviceKey][$jour])) {
                $recapParService[$serviceKey][$jour] = [
                    'total' => 0,
                    'nombre' => 0
                ];
            }

            $recapParService[$serviceKey][$jour]['total'] += $charge->total_price;
            $recapParService[$serviceKey][$jour]['nombre'] += $charge->quantity;
        }
    }

    /**
     * Classifie une charge d'hospitalisation selon son type
     */
    private function classifyCharge($charge)
    {
        $description = strtolower($charge->description_snapshot);

        // PRIORITÉ 1: Essayer de récupérer le service via l'examen source
        // Cela garantit la classification correcte pour tous les examens
        if ($charge->type === 'examen' && $charge->source_id) {
            $examen = \App\Models\Examen::find($charge->source_id);
            if ($examen && $examen->service) {
                $serviceKey = $this->determineServiceKey($examen, $examen->service, $examen->service->id);
                return $serviceKey;
            }
        }

        // PRIORITÉ 2: Identifier les types spécifiques par nom
        
        // Identifier les chambres (ROOM_DAY)
        if (
            strpos($description, 'chambre') !== false ||
            strpos($description, 'room_day') !== false ||
            $charge->type === 'room_day'
        ) {
            return 'HOSPITALISATION';
        }

        // Identifier les médicaments
        if (
            $charge->type === 'pharmacy' ||
            strpos($description, 'flagyl') !== false ||
            strpos($description, 'novalgin') !== false ||
            strpos($description, 'ssi') !== false ||
            strpos($description, 'dislep') !== false ||
            strpos($description, 'perfalgan') !== false
        ) {
            return 'PHARMACIE';
        }

        // Examens de laboratoire courants (fallback si pas de source_id)
        if (
            strpos($description, 'nfs') !== false ||
            strpos($description, 'crp') !== false ||
            strpos($description, 'urée') !== false ||
            strpos($description, 'uree') !== false ||
            strpos($description, 'glycémie') !== false ||
            strpos($description, 'creatinine') !== false ||
            strpos($description, 'transaminases') !== false
        ) {
            $serviceLabo = \App\Models\Service::where('type_service', 'LABORATOIRE')->first();
            if ($serviceLabo) {
                return $serviceLabo->id;
            }
        }

        // Identifier les examens d'exploration fonctionnelle (fallback)
        if (
            strpos($description, 'ecg') !== false ||
            strpos($description, 'egg') !== false ||
            strpos($description, 'echo') !== false ||
            strpos($description, 'radiologie') !== false ||
            strpos($description, 'scanner') !== false
        ) {
            return 'EXPLORATIONS_FONCTIONNELLES';
        }

        // Identifier les consultations
        if (
            strpos($description, 'consultation') !== false ||
            strpos($description, 'cs') !== false
        ) {
            return 'CONSULTATIONS_EXTERNES';
        }

        // Par défaut, retourner le type de charge
        Log::warning("Charge non classifiée: {$charge->description_snapshot} (type: {$charge->type}, source_id: {$charge->source_id})");
        return strtoupper($charge->type);
    }
}
