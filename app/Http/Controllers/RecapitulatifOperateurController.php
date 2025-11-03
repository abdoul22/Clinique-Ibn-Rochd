<?php

namespace App\Http\Controllers;

use App\Models\RecapitulatifOperateur;
use App\Models\Caisse;
use App\Models\Medecin;
use App\Models\Service;
use App\Models\Examen;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RecapitulatifOperateurController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer les données pour les filtres
        $medecins = Medecin::orderBy('nom')->get();
        $examens = Examen::orderBy('nom')->get();

        // Construire la requête de base avec filtres
        $query = Caisse::with(['medecin', 'examen']);

        // Appliquer les filtres de période
        $period = $request->get('period', 'day');

        if ($period === 'day' && $request->filled('date')) {
            $query->whereDate('date_examen', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
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

        // Filtrage par médecin
        if ($request->filled('medecin_id')) {
            $query->where('medecin_id', $request->medecin_id);
        }

        // Filtrage par examen
        if ($request->filled('examen_id')) {
            $query->where('examen_id', $request->examen_id);
        }

        // Récupérer les caisses filtrées
        $caisses = $query->get();

        // Récupérer tous les médecins et examens pour les relations
        $medecinsMap = Medecin::all()->keyBy('id');
        $examensMap = Examen::all()->keyBy('id');

        // Décomposer les examens multiples et grouper par médecin/examen
        $recapParOperateur = [];

        foreach ($caisses as $caisse) {
            $jour = $caisse->date_examen->format('Y-m-d');
            $medecinId = $caisse->medecin_id;

            if ($caisse->examens_data) {
                // Mode examens multiples
                $examensData = is_string($caisse->examens_data) ? json_decode($caisse->examens_data, true) : $caisse->examens_data;
                foreach ($examensData as $examenData) {
                    $examen = \App\Models\Examen::find($examenData['id']);
                    if ($examen) {
                        // Déterminer la clé basée sur le type d'examen
                        $service = \App\Models\Service::find($examen->idsvc);
                        $serviceKey = $this->determineServiceKey($examen, $service, $examen->idsvc);

                        if ($serviceKey === 'HOSPITALISATION') {
                            $key = $medecinId . '_HOSPITALISATION_' . $jour;
                        } else {
                            $key = $medecinId . '_' . $examen->id . '_' . $jour;
                        }

                        if (!isset($recapParOperateur[$key])) {
                            $examenId = ($serviceKey === 'HOSPITALISATION') ? 'HOSPITALISATION' : $examen->id;
                            $recapParOperateur[$key] = [
                                'medecin_id' => $medecinId,
                                'examen_id' => $examenId,
                                'jour' => $jour,
                                'nombre' => 0,
                                'recettes' => 0,
                                'tarif' => $examen->tarif,
                                'part_medecin' => 0,
                                'part_clinique' => 0,
                                'medecin' => $medecinsMap->get($medecinId),
                                'examen' => $examenId === 'HOSPITALISATION' ? (object)['nom' => 'Hospitalisation'] : $examensMap->get($examenId)
                            ];
                        }

                        $quantite = $examenData['quantite'];
                        $recapParOperateur[$key]['nombre'] += $quantite;
                        $recapParOperateur[$key]['recettes'] += $examen->tarif * $quantite;
                        $recapParOperateur[$key]['part_medecin'] += ($examen->part_medecin ?? 0) * $quantite;
                        $recapParOperateur[$key]['part_clinique'] += ($examen->part_cabinet ?? 0) * $quantite;
                    }
                }
            } else {
                // Mode examen unique
                $examen = $caisse->examen;
                if ($examen) {
                    // Vérifier si c'est une hospitalisation
                    if (strtolower($examen->nom) === 'hospitalisation') {
                        // Décomposer l'hospitalisation en charges individuelles
                        $this->decomposeHospitalisationOperateur($caisse, $recapParOperateur, $jour, $medecinId, $medecinsMap, $examensMap);
                    } else {
                        $key = $medecinId . '_' . $examen->id . '_' . $jour;

                        if (!isset($recapParOperateur[$key])) {
                            $recapParOperateur[$key] = [
                                'medecin_id' => $medecinId,
                                'examen_id' => $examen->id,
                                'jour' => $jour,
                                'nombre' => 0,
                                'recettes' => 0,
                                'tarif' => $examen->tarif,
                                'part_medecin' => 0,
                                'part_clinique' => 0,
                                'medecin' => $medecinsMap->get($medecinId),
                                'examen' => $examensMap->get($examen->id)
                            ];
                        }

                        $recapParOperateur[$key]['nombre'] += 1;
                        $recapParOperateur[$key]['recettes'] += $caisse->total;
                        $recapParOperateur[$key]['part_medecin'] += $examen->part_medecin ?? 0;
                        $recapParOperateur[$key]['part_clinique'] += $examen->part_cabinet ?? 0;
                    }
                }
            }
        }

        // Convertir en collection
        $recaps = collect($recapParOperateur)->map(function ($item) {
            return (object) $item;
        });

        // Trier par date décroissante (plus récent au plus ancien), puis par médecin, puis par examen
        $recapOperateurs = $recaps->sort(function ($a, $b) {
            // Comparer d'abord par date (ordre décroissant)
            $dateA = $a->jour ? strtotime($a->jour) : 0;
            $dateB = $b->jour ? strtotime($b->jour) : 0;
            
            if ($dateA !== $dateB) {
                return $dateB <=> $dateA; // Ordre décroissant (plus récent en premier)
            }
            
            // Si même date, trier par médecin_id
            $medecinA = $a->medecin_id ?? 0;
            $medecinB = $b->medecin_id ?? 0;
            
            if ($medecinA !== $medecinB) {
                return $medecinA <=> $medecinB;
            }
            
            // Si même médecin, trier par examen_id
            $examenA = is_numeric($a->examen_id) ? $a->examen_id : 0;
            $examenB = is_numeric($b->examen_id) ? $b->examen_id : 0;
            
            return $examenA <=> $examenB;
        })->values();

        // Pagination manuelle
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedRecaps = $recapOperateurs->slice($offset, $perPage);

        // Créer un objet de pagination personnalisé
        $recapOperateurs = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedRecaps,
            $recapOperateurs->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );

        // Calculer les totaux pour le résumé à partir des données filtrées
        $totalExamens = $recaps->sum('nombre');
        $totalRecettes = $recaps->sum('recettes');
        $totalPartMedecin = $recaps->sum('part_medecin');
        $totalPartClinique = $recaps->sum('part_clinique');

        $resume = [
            'total_examens' => $totalExamens,
            'total_recettes' => $totalRecettes,
            'total_part_medecin' => $totalPartMedecin,
            'total_part_clinique' => $totalPartClinique,
        ];

        return view('recapitulatif_operateurs.index', compact(
            'recapOperateurs',
            'medecins',
            'examens',
            'resume'
        ));
    }

    public function create()
    {
        $medecins = Medecin::all();
        $services = Service::all();
        return view('recapitulatif_operateurs.create', compact('medecins', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'service_id' => 'required|exists:services,id',
            'nombre' => 'required|integer',
            'tarif' => 'required|numeric',
            'recettes' => 'required|numeric',
            'part_medecin' => 'required|numeric',
            'part_clinique' => 'required|numeric',
            'date' => 'required|date',
        ]);

        RecapitulatifOperateur::create($request->all());

        return redirect()->route('recapitulatif-operateurs.index')->with('success', 'Récapitulatif ajouté.');
    }

    public function edit($id)
    {
        $recap = RecapitulatifOperateur::findOrFail($id);
        $medecins = Medecin::all();
        $services = Service::all();

        return view('recapitulatif_operateurs.edit', compact('recap', 'medecins', 'services'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'service_id' => 'required|exists:services,id',
            'nombre' => 'required|integer',
            'tarif' => 'required|numeric',
            'recettes' => 'required|numeric',
            'part_medecin' => 'required|numeric',
            'part_clinique' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $recap = RecapitulatifOperateur::findOrFail($id);
        $recap->update($request->all());

        return redirect()->route('recapitulatif-operateurs.index')->with('success', 'Récapitulatif mis à jour.');
    }

    public function show($id)
    {
        $recap = RecapitulatifOperateur::with(['medecin', 'service'])->findOrFail($id);
        return view('recapitulatif_operateurs.show', compact('recap'));
    }

    public function destroy($id)
    {
        $recap = RecapitulatifOperateur::findOrFail($id);
        $recap->delete();

        return redirect()->route('recapitulatif-operateurs.index')->with('success', 'Récapitulatif supprimé.');
    }

    public function exportPdf(Request $request)
    {
        // Construire la requête de base (même logique que index)
        $query = Caisse::with(['medecin', 'examen'])
            ->select([
                'caisses.medecin_id',
                'caisses.examen_id',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(caisses.total) as recettes'),
                DB::raw('DATE(CONVERT_TZ(caisses.date_examen, "+00:00", "+00:00")) as jour'),
                DB::raw('MAX(examens.tarif) as tarif'),
                DB::raw('SUM(examens.part_medecin) as part_medecin'),
                DB::raw('SUM(examens.part_cabinet) as part_clinique')
            ])
            ->join('examens', 'caisses.examen_id', '=', 'examens.id');

        // Filtrage par période
        $period = $request->get('period', 'day');

        if ($period === 'day' && $request->filled('date')) {
            $query->whereDate('caisses.date_examen', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $query->whereBetween('caisses.date_examen', [$start, $end]);
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->whereYear('caisses.date_examen', $parts[0])
                    ->whereMonth('caisses.date_examen', $parts[1]);
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $query->whereYear('caisses.date_examen', $request->year);
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('caisses.date_examen', [$request->date_start, $request->date_end]);
        }

        // Filtrage par médecin
        if ($request->filled('medecin_id')) {
            $query->where('caisses.medecin_id', $request->medecin_id);
        }

        // Filtrage par examen
        if ($request->filled('examen_id')) {
            $query->where('caisses.examen_id', $request->examen_id);
        }

        // Grouper par médecin, examen et jour
        $recaps = $query->groupBy('caisses.medecin_id', 'caisses.examen_id', DB::raw('DATE(CONVERT_TZ(caisses.date_examen, "+00:00", "+00:00"))'))
            ->orderBy('jour', 'desc')
            ->orderBy('caisses.medecin_id')
            ->orderBy('caisses.examen_id')
            ->get();

        $pdf = PDF::loadView('recapitulatif_operateurs.export_pdf', compact('recaps'));
        return $pdf->download('recapitulatif_operateurs.pdf');
    }

    public function print(Request $request)
    {
        // Récupérer les données pour les filtres
        $medecins = Medecin::orderBy('nom')->get();
        $examens = Examen::orderBy('nom')->get();

        // Construire la requête de base (même logique que index)
        $query = Caisse::with(['medecin', 'examen'])
            ->select([
                'caisses.medecin_id',
                'caisses.examen_id',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(caisses.total) as recettes'),
                DB::raw('DATE(CONVERT_TZ(caisses.date_examen, "+00:00", "+00:00")) as jour'),
                DB::raw('MAX(examens.tarif) as tarif'),
                DB::raw('SUM(examens.part_medecin) as part_medecin'),
                DB::raw('SUM(examens.part_cabinet) as part_clinique')
            ])
            ->join('examens', 'caisses.examen_id', '=', 'examens.id');

        // Filtrage par période
        $period = $request->get('period', 'day');

        if ($period === 'day' && $request->filled('date')) {
            $query->whereDate('caisses.date_examen', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $query->whereBetween('caisses.date_examen', [$start, $end]);
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->whereYear('caisses.date_examen', $parts[0])
                    ->whereMonth('caisses.date_examen', $parts[1]);
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $query->whereYear('caisses.date_examen', $request->year);
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('caisses.date_examen', [$request->date_start, $request->date_end]);
        }

        // Filtrage par médecin
        if ($request->filled('medecin_id')) {
            $query->where('caisses.medecin_id', $request->medecin_id);
        }

        // Filtrage par examen
        if ($request->filled('examen_id')) {
            $query->where('caisses.examen_id', $request->examen_id);
        }

        // Grouper par médecin, examen et jour (même logique que index)
        $recapOperateurs = $query->groupBy('caisses.medecin_id', 'caisses.examen_id', DB::raw('DATE(CONVERT_TZ(caisses.date_examen, "+00:00", "+00:00"))'))
            ->orderBy('jour', 'desc')
            ->orderBy('caisses.medecin_id')
            ->orderBy('caisses.examen_id')
            ->get(); // Pas de pagination pour l'impression

        // Calculer les totaux pour le résumé (même logique que index)
        $totauxQuery = Caisse::join('examens', 'caisses.examen_id', '=', 'examens.id');

        // Appliquer les mêmes filtres
        if ($period === 'day' && $request->filled('date')) {
            $totauxQuery->whereDate('caisses.date_examen', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $totauxQuery->whereBetween('caisses.date_examen', [$start, $end]);
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $totauxQuery->whereYear('caisses.date_examen', $parts[0])
                    ->whereMonth('caisses.date_examen', $parts[1]);
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $totauxQuery->whereYear('caisses.date_examen', $request->year);
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            $totauxQuery->whereBetween('caisses.date_examen', [$request->date_start, $request->date_end]);
        }

        if ($request->filled('medecin_id')) {
            $totauxQuery->where('caisses.medecin_id', $request->medecin_id);
        }

        if ($request->filled('examen_id')) {
            $totauxQuery->where('caisses.examen_id', $request->examen_id);
        }

        $totaux = $totauxQuery->select([
            DB::raw('COUNT(*) as total_examens'),
            DB::raw('SUM(caisses.total) as total_recettes'),
            DB::raw('SUM(examens.part_medecin) as total_part_medecin'),
            DB::raw('SUM(examens.part_cabinet) as total_part_clinique')
        ])->first();

        $resume = [
            'total_examens' => $totaux->total_examens ?? 0,
            'total_recettes' => $totaux->total_recettes ?? 0,
            'total_part_medecin' => $totaux->total_part_medecin ?? 0,
            'total_part_clinique' => $totaux->total_part_clinique ?? 0,
        ];

        // Générer le résumé de la période pour l'affichage
        $periodSummary = '';
        if ($period === 'day' && $request->filled('date')) {
            $periodSummary = 'Filtré sur le jour du ' . Carbon::parse($request->date)->translatedFormat('d F Y');
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $periodSummary = 'Filtré sur la semaine du ' . $start->translatedFormat('d F Y') . ' au ' . $end->translatedFormat('d F Y');
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $periodSummary = 'Filtré sur le mois de ' . Carbon::create($parts[0], $parts[1])->translatedFormat('F Y');
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $periodSummary = 'Filtré sur l\'année ' . $request->year;
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            $periodSummary = 'Filtré du ' . Carbon::parse($request->date_start)->translatedFormat('d F Y') . ' au ' . Carbon::parse($request->date_end)->translatedFormat('d F Y');
        }

        return view('recapitulatif_operateurs.print', compact(
            'recapOperateurs',
            'resume',
            'periodSummary'
        ));
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
     * Décompose une hospitalisation en charges individuelles pour les opérateurs
     */
    private function decomposeHospitalisationOperateur($caisse, &$recapParOperateur, $jour, $medecinId, $medecinsMap, $examensMap)
    {
        // Récupérer l'hospitalisation associée à cette caisse
        $hospitalisation = \App\Models\Hospitalisation::where('gestion_patient_id', $caisse->gestion_patient_id)
            ->whereDate('date_entree', $caisse->date_examen)
            ->first();

        if (!$hospitalisation) {
            // Si pas d'hospitalisation trouvée, traiter comme un examen normal
            $key = $medecinId . '_HOSPITALISATION_' . $jour;
            if (!isset($recapParOperateur[$key])) {
                $recapParOperateur[$key] = [
                    'medecin_id' => $medecinId,
                    'examen_id' => 'HOSPITALISATION',
                    'jour' => $jour,
                    'nombre' => 0,
                    'recettes' => 0,
                    'tarif' => 0,
                    'part_medecin' => 0,
                    'part_clinique' => 0,
                    'medecin' => $medecinsMap->get($medecinId),
                    'examen' => (object)['nom' => 'Hospitalisation']
                ];
            }
            $recapParOperateur[$key]['nombre'] += 1;
            $recapParOperateur[$key]['recettes'] += $caisse->total;
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

        Log::info("Décomposition hospitalisation opérateur ID {$hospitalisation->id} - {$charges->count()} charges trouvées");

        foreach ($charges as $charge) {
            $serviceKey = $this->classifyCharge($charge);
            $examenId = $serviceKey === 'HOSPITALISATION' ? 'HOSPITALISATION' : 'CHARGE_' . $charge->id;
            $key = $medecinId . '_' . $examenId . '_' . $jour;

            if (!isset($recapParOperateur[$key])) {
                $recapParOperateur[$key] = [
                    'medecin_id' => $medecinId,
                    'examen_id' => $examenId,
                    'jour' => $jour,
                    'nombre' => 0,
                    'recettes' => 0,
                    'tarif' => $charge->unit_price,
                    'part_medecin' => 0,
                    'part_clinique' => 0,
                    'medecin' => $medecinsMap->get($medecinId),
                    'examen' => $examenId === 'HOSPITALISATION' ?
                        (object)['nom' => 'Hospitalisation'] :
                        (object)['nom' => $charge->description_snapshot]
                ];
            }

            $recapParOperateur[$key]['nombre'] += $charge->quantity;
            $recapParOperateur[$key]['recettes'] += $charge->total_price;
            $recapParOperateur[$key]['part_medecin'] += $charge->part_medecin;
            $recapParOperateur[$key]['part_clinique'] += $charge->part_cabinet;
        }
    }

    /**
     * Classifie une charge d'hospitalisation selon son type
     */
    private function classifyCharge($charge)
    {
        $description = strtolower($charge->description_snapshot);

        // Identifier les médicaments
        if (
            $charge->type === 'pharmacy' ||
            strpos($description, 'flagyl') !== false ||
            strpos($description, 'novalgin') !== false ||
            strpos($description, 'ssi') !== false ||
            strpos($description, 'mg') !== false
        ) {
            return 'PHARMACIE';
        }

        // Identifier les examens d'exploration fonctionnelle
        if (
            strpos($description, 'ecg') !== false ||
            strpos($description, 'echo') !== false ||
            strpos($description, 'radiologie') !== false ||
            strpos($description, 'scanner') !== false
        ) {
            return 'EXPLORATIONS_FONCTIONNELLES';
        }

        // Identifier les consultations
        if (
            strpos($description, 'consultation') !== false ||
            strpos($description, 'cs') !== false ||
            strpos($description, 'dr.') !== false
        ) {
            return 'CONSULTATIONS_EXTERNES';
        }

        // Identifier les chambres (ROOM_DAY)
        if (
            strpos($description, 'chambre') !== false ||
            strpos($description, 'room_day') !== false ||
            strpos($description, 'lit') !== false
        ) {
            return 'HOSPITALISATION';
        }

        // Par défaut, retourner le type de charge
        return strtoupper($charge->type);
    }
}
