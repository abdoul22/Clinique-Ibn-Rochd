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
use Carbon\Carbon;

class RecapitulatifOperateurController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer les données pour les filtres
        $medecins = Medecin::orderBy('nom')->get();
        $examens = Examen::orderBy('nom')->get();

        // Construire la requête de base
        $query = Caisse::with(['medecin', 'examen'])
            ->select([
                'medecin_id',
                'examen_id',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(total) as recettes'),
                DB::raw('DATE(CONVERT_TZ(date_examen, "+00:00", "+00:00")) as jour'),
                DB::raw('MAX(examens.tarif) as tarif'),
                DB::raw('SUM(examens.part_medecin) as part_medecin'),
                DB::raw('SUM(examens.part_cabinet) as part_clinique')
            ])
            ->join('examens', 'caisses.examen_id', '=', 'examens.id');

        // Filtrage par période
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

        // Grouper par médecin, examen et jour (une ligne par médecin par examen par jour)
        $recapOperateurs = $query->groupBy('medecin_id', 'examen_id', DB::raw('DATE(CONVERT_TZ(date_examen, "+00:00", "+00:00"))'))
            ->orderBy('jour', 'desc')
            ->orderBy('medecin_id')
            ->orderBy('examen_id')
            ->paginate(15);

        // Calculer les totaux pour le résumé
        $totauxQuery = Caisse::join('examens', 'caisses.examen_id', '=', 'examens.id');

        // Appliquer les mêmes filtres
        if ($period === 'day' && $request->filled('date')) {
            $totauxQuery->whereDate('date_examen', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
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

        if ($request->filled('medecin_id')) {
            $totauxQuery->where('medecin_id', $request->medecin_id);
        }

        if ($request->filled('examen_id')) {
            $totauxQuery->where('examen_id', $request->examen_id);
        }

        $totaux = $totauxQuery->select([
            DB::raw('COUNT(*) as total_examens'),
            DB::raw('SUM(total) as total_recettes'),
            DB::raw('SUM(examens.part_medecin) as total_part_medecin'),
            DB::raw('SUM(examens.part_cabinet) as total_part_clinique')
        ])->first();

        $resume = [
            'total_examens' => $totaux->total_examens ?? 0,
            'total_recettes' => $totaux->total_recettes ?? 0,
            'total_part_medecin' => $totaux->total_part_medecin ?? 0,
            'total_part_clinique' => $totaux->total_part_clinique ?? 0,
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

    public function exportPdf()
    {
        $recaps = Caisse::with(['medecin', 'examen'])
            ->select([
                'medecin_id',
                'examen_id',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(total) as recettes'),
                DB::raw('MAX(date_examen) as date'),
                DB::raw('MAX(examens.tarif) as tarif'),
                DB::raw('SUM(examens.part_medecin) as part_medecin'),
                DB::raw('SUM(examens.part_cabinet) as part_clinique')
            ])
            ->join('examens', 'caisses.examen_id', '=', 'examens.id')
            ->groupBy('medecin_id', 'examen_id')
            ->orderBy('date', 'desc')
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
                'medecin_id',
                'examen_id',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(total) as recettes'),
                DB::raw('DATE(CONVERT_TZ(date_examen, "+00:00", "+00:00")) as jour'),
                DB::raw('MAX(examens.tarif) as tarif'),
                DB::raw('SUM(examens.part_medecin) as part_medecin'),
                DB::raw('SUM(examens.part_cabinet) as part_clinique')
            ])
            ->join('examens', 'caisses.examen_id', '=', 'examens.id');

        // Filtrage par période
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

        // Grouper par médecin, examen et jour (même logique que index)
        $recapOperateurs = $query->groupBy('medecin_id', 'examen_id', DB::raw('DATE(CONVERT_TZ(date_examen, "+00:00", "+00:00"))'))
            ->orderBy('jour', 'desc')
            ->orderBy('medecin_id')
            ->orderBy('examen_id')
            ->get(); // Pas de pagination pour l'impression

        // Calculer les totaux pour le résumé (même logique que index)
        $totauxQuery = Caisse::join('examens', 'caisses.examen_id', '=', 'examens.id');

        // Appliquer les mêmes filtres
        if ($period === 'day' && $request->filled('date')) {
            $totauxQuery->whereDate('date_examen', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
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

        if ($request->filled('medecin_id')) {
            $totauxQuery->where('medecin_id', $request->medecin_id);
        }

        if ($request->filled('examen_id')) {
            $totauxQuery->where('examen_id', $request->examen_id);
        }

        $totaux = $totauxQuery->select([
            DB::raw('COUNT(*) as total_examens'),
            DB::raw('SUM(total) as total_recettes'),
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
}
