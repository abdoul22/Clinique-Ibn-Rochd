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

        $query = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_entre', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('medecin', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%");
                    });
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

        $caisses = $query->orderBy('date_examen', 'desc')->paginate(10);
        return view('caisses.index', compact('caisses'));
    }

    public function create()
    {
        $patients = GestionPatient::all();
        $medecins = Medecin::all();
        $prescripteurs = Prescripteur::all();
        $services = Service::all();
        $exam_types = Examen::all();
        $assurances = \App\Models\Assurance::all();
        $todayCount = \App\Models\Caisse::whereDate('created_at', now())->count();
        $numero_prevu = $todayCount + 1;

        return view('caisses.create', compact(
            'numero_prevu',
            'patients',
            'medecins',
            'prescripteurs',
            'exam_types',
            'services',
            'assurances'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'prescripteur_id' => 'nullable|exists:prescripteurs,id',
            'examen_id' => 'required|exists:examens,id',
            'date_examen' => 'required|date',
            'total' => 'required|numeric',
            'type' => 'nullable|string|in:bankily,masrivi,especes',
            'assurance_id' => 'nullable|exists:assurances,id',
            'couverture' => 'nullable|numeric|min:0|max:100',
        ]);

        $dernierNumero = Caisse::max('numero_facture') ?? 0;
        $prochainNumero = $dernierNumero + 1;

        $data = $request->all();
        $data['nom_caissier'] = Auth::user()->name;
        $data['numero_facture'] = $prochainNumero;
        $data['couverture'] = $request->couverture ?? 0;
        $data['assurance_id'] = $request->assurance_id ?? null;

        $caisse = Caisse::create($data);

        $examen = Examen::findOrFail($request->examen_id);
        $part_cabinet = $examen->part_cabinet ?? 0;
        $part_medecin = $examen->part_medecin ?? ($caisse->total - $part_cabinet);
        // Couverture (par défaut 0)
        $couverture = $request->couverture ?? 0;
        $montantTotal = $caisse->total;
        $montantPatient = round($montantTotal * (100 - $couverture) / 100, 0); // Ce que paie le patient
        $montantAssurance = $montantTotal - $montantPatient;

        if ($montantPatient > 0 && !$caisse->paiements) {
            $caisse->paiements()->create([
                'type' => $request->type,
                'montant' => $montantPatient,
            ]);
        }

        if ($request->assurance_id && $montantAssurance > 0) {
            $assurance = \App\Models\Assurance::find($request->assurance_id);
            $assurance->increment('credit', $montantAssurance);
        }
        if ($montantPatient > 0) {
            // Vérifie s'il existe déjà un paiement pour cette caisse
            if (!$caisse->paiements()->exists()) {
                $caisse->paiements()->create([
                    'type' => $request->type,
                    'montant' => $montantPatient,
                ]);
            }
        }
        if ($caisse->assurance_id && $montantAssurance > 0) {
            $assurance = \App\Models\Assurance::find($request->assurance_id);
            $assurance->increment('credit', $montantAssurance);

            // Vérifie si un crédit existe déjà pour cette caisse précise (en liant via caisse_id si tu veux)
            \App\Models\Credit::create([
                'source_type'   => \App\Models\Assurance::class,
                'source_id'     => $caisse->assurance_id,
                'montant'       => $montantAssurance,
                'montant_paye'  => 0,
                'status'        => 'non payé',
                'statut'        => 'non payé',
                'caisse_id'     => $caisse->id,
            ]);
        }


        EtatCaisse::create([
            'designation' => 'Facture caisse n°' . $caisse->id,
            'recette' => $caisse->total,
            'part_medecin' => $part_medecin,
            'part_clinique' => $part_cabinet,
            'depense' => 0,
            'credit_personnel' => null,
            'personnel_id' => null,
            'assurance_id' => $caisse->assurance_id, // ✅ fonctionne enfin
            'caisse_id' => $caisse->id,
            'medecin_id' => $caisse->medecin_id,
        ]);

        // Gestion du mode de paiement avec couverture assurance
        $couverture = $request->input('couverture_assurance', 0); // en %
        $montantPatient = $caisse->total;
        if ($couverture > 0) {
            $montantPatient = $caisse->total * (1 - ($couverture / 100));
        }

        $role = Auth::user()->role->name;
        return redirect()->route($role . '.caisses.show', $caisse->id)
            ->with('success', 'Facture et état de caisse créés avec succès.');
    }

    public function show($id)
    {
        $caisse = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service'])->find($id);

        if (!$caisse) {
            abort(404, 'Caisse non trouvée.');
        }

        return view('caisses.show', compact('caisse'));
    }

    public function edit(Caisse $caisse, $id)
    {
        $caisse = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service'])->find($id);
        $patients = GestionPatient::all();
        $medecins = Medecin::all();
        $prescripteurs = Prescripteur::all();
        $exam_types = Examen::all();
        $services = Service::all();

        return view('caisses.edit', compact(
            'caisse',
            'patients',
            'medecins',
            'prescripteurs',
            'exam_types',
            'services'
        ));
    }

    public function update(Request $request, Caisse $caisse)
    {
        $request->validate([
            'numero_entre' => 'required|unique:caisses,numero_entre,' . $caisse->id,
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'prescripteur_id' => 'nullable|exists:prescripteurs,id',
            'examen_id' => 'required|exists:examens,id',
            'date_examen' => 'required|date',
        ]);

        $examenType = Examen::findOrFail($request->examen_id);
        $data = $request->except(['service_id']);
        $data['total'] = $examenType->tarif;

        $caisse->update($data);

        $role = Auth::user()->role->name;
        return redirect()->route('caisses.index')->with('success', 'Examen mis à jour avec succès.');
    }

    public function destroy(Caisse $caisse, $id)
    {
        $caisse = Caisse::find($id);
        if ($caisse->delete()) {
            $role = Auth::user()->role->name;

            if ($role === 'superadmin') {
                return redirect()->route('superadmin.caisses.index')->with('success', 'Facture supprimée !');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.caisses.index')->with('success', 'Facture supprimée !');
            }

            return redirect()->route('caisses.index')->with('success', 'Facture supprimée !');
        }

        return back()->with('error', 'Erreur lors de la suppression');
    }

    public function exportPdf(Caisse $caisse)
    {
        $caisses = Caisse::all();
        $pdf = PDF::loadView('caisses.export', compact('caisse'));
        return $pdf->download('examen-' . $caisse->numero_entre . '.pdf');
    }

    public function print()
    {
        $caisses = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service'])->get();
        return view('caisses.print', compact('caisses'));
    }
}
