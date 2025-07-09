<?php

namespace App\Http\Controllers;

use App\Models\RecapitulatifServiceJournalier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Caisse;

class RecapitulatifServiceJournalierController extends Controller
{
    public function index(Request $request)
    {
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
            ->paginate(15);

        // Charger les services séparément pour éviter les problèmes de relations
        $serviceIds = $recaps->pluck('service_id')->unique()->filter();
        $services = [];
        if ($serviceIds->count() > 0) {
            $services = \App\Models\Service::whereIn('id', $serviceIds)->pluck('nom', 'id')->toArray();
        }

        // Calculer les totaux pour le résumé
        $totauxQuery = Caisse::join('services', 'caisses.service_id', '=', 'services.id');

        // Appliquer les mêmes filtres pour les totaux
        if ($period === 'day' && $request->filled('date')) {
            $totauxQuery->whereDate('date_examen', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $totauxQuery->whereBetween('date_examen', [$start, $end]);
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $totauxQuery->whereYear('date_examen', $parts[0])
                    ->whereMonth('date_examen', $parts[1]);
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $totauxQuery->whereYear('date_examen', $request->year);
        } elseif ($period === 'range' && $request->filled('date_start') && $request->filled('date_end')) {
            $totauxQuery->whereBetween('date_examen', [$request->date_start, $request->date_end]);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $totauxQuery->where('services.nom', 'like', "%{$searchTerm}%");
        }

        if ($request->filled('service_id')) {
            $totauxQuery->where('service_id', $request->service_id);
        }

        $totaux = $totauxQuery->select([
            DB::raw('COUNT(*) as total_actes'),
            DB::raw('SUM(total) as total_recettes'),
        ])->first();

        $resume = [
            'total_actes' => $totaux->total_actes ?? 0,
            'total_recettes' => $totaux->total_recettes ?? 0,
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
            $services = \App\Models\Service::whereIn('id', $serviceIds)->pluck('nom', 'id')->toArray();
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
}
