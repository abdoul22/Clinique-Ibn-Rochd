<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Service;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamenController extends Controller
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

        $query = Examen::with(['service.pharmacie']);

        if ($search) {
            $query->where('nom', 'like', "%{$search}%")
                ->orWhereHas('service', function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%");
                });
        }

        // Filtrage par période sur created_at
        if ($period === 'day' && $date) {
            $query->whereDate('created_at', $date);
        } elseif ($period === 'week' && $week) {
            $parts = explode('-W', $week);
            if (count($parts) === 2) {
                $yearW = (int)$parts[0];
                $weekW = (int)$parts[1];
                $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            }
        } elseif ($period === 'month' && $month) {
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                $yearM = (int)$parts[0];
                $monthM = (int)$parts[1];
                $query->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
            }
        } elseif ($period === 'year' && $year) {
            $query->whereYear('created_at', $year);
        } elseif ($period === 'range' && $dateStart && $dateEnd) {
            $query->whereBetween('created_at', [$dateStart, $dateEnd]);
        }

        $examens = $query->orderBy('created_at', 'desc')->paginate(10);

        // Traiter les données pour l'affichage
        $examens->getCollection()->transform(function ($examen) {
            // Si l'examen est lié à un service de type médicament (pharmacie)
            if ($examen->service && $examen->service->type_service === 'medicament' && $examen->service->pharmacie) {
                $examen->nom_affichage = $examen->service->pharmacie->nom_medicament;
                $examen->service_affichage = 'Pharmacie';
            } else {
                $examen->nom_affichage = $examen->nom;
                $examen->service_affichage = $examen->service->nom ?? '-';
            }
            return $examen;
        });

        return view('examens.index', compact('examens'));
    }

    public function create()
    {
        $services = Service::all();
        $totaux = \App\Models\Examen::getTotaux();
        return view('examens.create', compact('services', 'totaux'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'idsvc' => 'required|exists:services,id',
            'tarif' => 'required|numeric|min:0',
            'part_cabinet' => 'nullable|numeric|min:0',
            'part_medecin' => 'nullable|numeric|min:0',
        ]);

        $tarif = $request->tarif;
        $part_cabinet = $request->part_cabinet;
        $part_medecin = $request->part_medecin;

        // Calcul automatique
        if (is_null($part_medecin) && !is_null($part_cabinet)) {
            $part_medecin = $tarif - $part_cabinet;
        } elseif (is_null($part_cabinet) && !is_null($part_medecin)) {
            $part_cabinet = $tarif - $part_medecin;
        } elseif (is_null($part_cabinet) && is_null($part_medecin)) {
            return back()->withErrors(['part_medecin' => 'Remplir au moins une part (cabinet ou médecin).']);
        }

        Examen::create([
            'nom' => $request->nom,
            'idsvc' => $request->idsvc,
            'tarif' => $tarif,
            'part_cabinet' => $part_cabinet,
            'part_medecin' => $part_medecin,
        ]);

        return redirect()->route('examens.index')->with('success', 'Examen ajouté avec succès.');
    }

    public function show($id)
    {
        $examen = Examen::with('service')->findOrFail($id);
        return view('examens.show', compact('examen'));
    }

    public function edit($id)
    {
        $examen = Examen::findOrFail($id);
        $services = Service::all();
        return view('examens.edit', compact('examen', 'services'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'idsvc' => 'required|exists:services,id',
            'tarif' => 'required|numeric|min:0',
            'part_cabinet' => 'required|numeric',
            'part_medecin' => 'required|numeric',
        ]);

        $tarif = $request->tarif;
        $part_cabinet = $request->part_cabinet;
        $part_medecin = $request->part_medecin;

        // Calcul automatique
        if (is_null($part_medecin) && !is_null($part_cabinet)) {
            $part_medecin = $tarif - $part_cabinet;
        } elseif (is_null($part_cabinet) && !is_null($part_medecin)) {
            $part_cabinet = $tarif - $part_medecin;
        } elseif (is_null($part_cabinet) && is_null($part_medecin)) {
            return back()->withErrors(['part_medecin' => 'Remplir au moins une part (cabinet ou médecin).']);
        }

        $examen = Examen::findOrFail($id);
        $examen->update($request->all());
        return redirect()->route('examens.index')->with('success', 'Examen mis à jour.');
    }

    public function destroy($id)
    {
        $examen = Examen::findOrFail($id);
        $examen->delete();
        return redirect()->route('examens.index')->with('success', 'Examen supprimé.');
    }

    public function exportPdf()
    {
        $examens = Examen::with(['service.pharmacie'])->get();

        // Traiter les données pour l'affichage
        $examens->transform(function ($examen) {
            // Si l'examen est lié à un service de type médicament (pharmacie)
            if ($examen->service && $examen->service->type_service === 'medicament' && $examen->service->pharmacie) {
                $examen->nom_affichage = $examen->service->pharmacie->nom_medicament;
                $examen->service_affichage = 'Pharmacie';
            } else {
                $examen->nom_affichage = $examen->nom;
                $examen->service_affichage = $examen->service->nom ?? '-';
            }
            return $examen;
        });

        $pdf = Pdf::loadView('examens.export_pdf', compact('examens'));
        return $pdf->download('examens.pdf');
    }

    public function print()
    {
        $examens = Examen::with(['service.pharmacie'])->get();

        // Traiter les données pour l'affichage
        $examens->transform(function ($examen) {
            // Si l'examen est lié à un service de type médicament (pharmacie)
            if ($examen->service && $examen->service->type_service === 'medicament' && $examen->service->pharmacie) {
                $examen->nom_affichage = $examen->service->pharmacie->nom_medicament;
                $examen->service_affichage = 'Pharmacie';
            } else {
                $examen->nom_affichage = $examen->nom;
                $examen->service_affichage = $examen->service->nom ?? '-';
            }
            return $examen;
        });

        return view('examens.print', compact('examens'));
    }
}
