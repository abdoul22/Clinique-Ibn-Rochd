<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $query = Examen::with(['service.pharmacie'])
            ->where('nom', 'NOT LIKE', '%Hospitalisation%');

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
            // Détection compatible: ancien 'medicament' ou nouveau 'PHARMACIE'
            $isMedicament = $examen->service && (
                $examen->service->type_service === 'medicament' ||
                $examen->service->type_service === 'PHARMACIE'
            ) && $examen->service->pharmacie;

            if ($isMedicament) {
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
        $medicaments = \App\Models\Pharmacie::where('statut', 'actif')->where('stock', '>', 0)->get();
        $totaux = \App\Models\Examen::getTotaux();
        $assurances = \App\Models\Assurance::orderBy('nom')->get();
        return view('examens.create', compact('services', 'medicaments', 'totaux', 'assurances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'idsvc' => 'nullable|exists:services,id',
            'medicament_id' => 'nullable|exists:pharmacies,id',
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
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Remplir au moins une part (cabinet ou médecin).'], 422);
            }
            return back()->withErrors(['part_medecin' => 'Remplir au moins une part (cabinet ou médecin).']);
        }

        // Si aucun service n'est fourni, utiliser un service par défaut ou le premier service disponible
        $serviceId = $request->idsvc;
        if (!$serviceId) {
            $defaultService = Service::first();
            if (!$defaultService) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Aucun service disponible. Veuillez créer un service d\'abord.'], 422);
                }
                return back()->withErrors(['idsvc' => 'Aucun service disponible. Veuillez créer un service d\'abord.']);
            }
            $serviceId = $defaultService->id;
        }

        // Si un médicament est sélectionné: ne pas créer de service automatiquement.
        if ($request->medicament_id) {
            $medicament = \App\Models\Pharmacie::find($request->medicament_id);
            if ($medicament) {
                // Reprendre le service sélectionné dans le select (idsvc) qui est de type PHARMACIE
                // et lier simplement l'examen à ce service générique.
                $serviceId = $request->idsvc ?? ($serviceId ?? null);
                $nomExamen = $medicament->nom_medicament;
                // Mettre le tarif par défaut sur le prix du médicament
                $tarif = $medicament->prix_vente;
                $part_cabinet = $tarif; // PHARMACIE: part cabinet = tarif
                $part_medecin = 0;
            }
        }

        $examen = Examen::create([
            'nom' => $nomExamen ?? $request->nom,
            'idsvc' => $serviceId,
            'tarif' => $tarif,
            'part_cabinet' => $part_cabinet,
            'part_medecin' => $part_medecin,
        ]);

        // Sauvegarder les tarifs assurances si fournis
        if ($request->has('assurance_tarifs')) {
            foreach ($request->assurance_tarifs as $assuranceId => $tarifAssurance) {
                if (!empty($tarifAssurance) && is_numeric($tarifAssurance) && $tarifAssurance > 0) {
                    \App\Models\ExamenAssuranceTarif::create([
                        'examen_id' => $examen->id,
                        'assurance_id' => $assuranceId,
                        'tarif_assurance' => $tarifAssurance,
                    ]);
                }
            }
        }

        // Si c'est une requête AJAX, retourner du JSON
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $examen->id,
                'nom' => $examen->nom,
                'tarif' => $examen->tarif,
                'part_cabinet' => $examen->part_cabinet,
                'part_medecin' => $examen->part_medecin,
                'message' => 'Examen ajouté avec succès.'
            ]);
        }

        return redirect()->route('examens.index')->with('success', 'Examen ajouté avec succès.');
    }

    public function show($id)
    {
        $examen = Examen::with('service')->findOrFail($id);
        return view('examens.show', compact('examen'));
    }

    public function edit($id)
    {
        $examen = Examen::with('assuranceTarifs')->findOrFail($id);
        $services = Service::all();
        $assurances = \App\Models\Assurance::orderBy('nom')->get();
        $tarifsExistants = $examen->assuranceTarifs->pluck('tarif_assurance', 'assurance_id')->toArray();
        $page = request('page', 1); // Récupérer le paramètre page
        return view('examens.edit', compact('examen', 'services', 'assurances', 'tarifsExistants', 'page'));
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
        
        // Mettre à jour les tarifs assurances
        if ($request->has('assurance_tarifs')) {
            // Supprimer les anciens tarifs
            \App\Models\ExamenAssuranceTarif::where('examen_id', $examen->id)->delete();
            
            // Créer les nouveaux tarifs
            foreach ($request->assurance_tarifs as $assuranceId => $tarifAssurance) {
                if (!empty($tarifAssurance) && is_numeric($tarifAssurance) && $tarifAssurance > 0) {
                    \App\Models\ExamenAssuranceTarif::create([
                        'examen_id' => $examen->id,
                        'assurance_id' => $assuranceId,
                        'tarif_assurance' => $tarifAssurance,
                    ]);
                }
            }
        }
        
        // Conserver le paramètre de pagination et détecter le rôle
        $page = $request->input('return_page', 1);
        $role = Auth::user()->role->name;
        $routeName = ($role === 'superadmin' || $role === 'admin') ? $role . '.examens.index' : 'examens.index';
        
        return redirect()->route($routeName, ['page' => $page])->with('success', 'Examen mis à jour.');
    }

    public function destroy($id)
    {
        $examen = Examen::findOrFail($id);
        $examen->delete();
        return redirect()->route('examens.index')->with('success', 'Examen supprimé.');
    }

    public function exportPdf()
    {
        $examens = Examen::with(['service.pharmacie'])
            ->where('nom', 'NOT LIKE', '%Hospitalisation%')
            ->get();

        // Traiter les données pour l'affichage
        $examens->transform(function ($examen) {
            // Si l'examen est lié à un service de type médicament (ancien ou nouveau)
            if ($examen->service && ($examen->service->type_service === 'medicament' || $examen->service->type_service === 'PHARMACIE') && $examen->service->pharmacie) {
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
        $examens = Examen::with(['service.pharmacie'])
            ->where('nom', 'NOT LIKE', '%Hospitalisation%')
            ->get();

        // Traiter les données pour l'affichage
        $examens->transform(function ($examen) {
            // Si l'examen est lié à un service de type médicament (ancien ou nouveau)
            if ($examen->service && ($examen->service->type_service === 'medicament' || $examen->service->type_service === 'PHARMACIE') && $examen->service->pharmacie) {
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
