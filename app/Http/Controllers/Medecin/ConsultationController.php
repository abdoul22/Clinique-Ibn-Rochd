<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\GestionPatient;
use App\Models\DossierMedical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        if (!$medecin) {
            return redirect()->route('login')->with('error', 'Aucun profil médecin associé à votre compte.');
        }

        $query = Consultation::parMedecin($medecin->id)
            ->with(['patient', 'dossierMedical']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date_consultation', $request->date);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $consultations = $query->latest('date_consultation')->paginate(20);

        return view('medecin.consultations.index', compact('consultations', 'medecin'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        if (!$medecin) {
            return redirect()->route('login')->with('error', 'Aucun profil médecin associé à votre compte.');
        }

        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = GestionPatient::find($request->patient_id);
        }

        // Recherche de patients - UNIQUEMENT ceux examinés par ce médecin (via caisses)
        $patients = GestionPatient::query()
            ->whereHas('caisses', function($q) use ($medecin) {
                $q->where('medecin_id', $medecin->id);
            })
            ->orderBy('first_name')
            ->get();

        return view('medecin.consultations.create', compact('medecin', 'patient', 'patients'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        if (!$medecin) {
            return redirect()->route('login')->with('error', 'Aucun profil médecin associé à votre compte.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:gestion_patients,id',
            'date_consultation' => 'required|date',
            'heure_consultation' => 'nullable',
            'motif' => 'nullable|string',
            'antecedents' => 'nullable|string',
            'histoire_maladie' => 'nullable|string',
            'examen_clinique' => 'nullable|string',
            'conduite_tenir' => 'nullable|string',
            'resume' => 'nullable|string',
        ]);

        // Créer ou récupérer le dossier médical
        $dossier = DossierMedical::firstOrCreate(
            ['patient_id' => $validated['patient_id']],
            [
                'numero_dossier' => 'DOS-' . str_pad($validated['patient_id'], 6, '0', STR_PAD_LEFT),
                'date_creation' => Carbon::now(),
                'statut' => 'actif',
            ]
        );

        $validated['medecin_id'] = $medecin->id;
        $validated['dossier_medical_id'] = $dossier->id;
        $validated['statut'] = 'terminee';

        $consultation = Consultation::create($validated);

        // Mettre à jour le dossier médical
        $dossier->derniere_visite = Carbon::now();
        $dossier->save();

        return redirect()
            ->route('medecin.consultations.show', $consultation->id)
            ->with('success', 'Rapport médical créé avec succès.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $consultation = Consultation::with(['patient', 'medecin', 'dossierMedical', 'ordonnances'])
            ->findOrFail($id);

        // Vérifier que le rapport médical appartient bien au médecin connecté
        if ($consultation->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        return view('medecin.consultations.show', compact('consultation', 'medecin'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $consultation = Consultation::with(['patient'])->findOrFail($id);

        // Vérifier que le rapport médical appartient bien au médecin connecté
        if ($consultation->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        // Recherche de patients - UNIQUEMENT ceux examinés par ce médecin (via caisses)
        $patients = GestionPatient::query()
            ->whereHas('caisses', function($q) use ($medecin) {
                $q->where('medecin_id', $medecin->id);
            })
            ->orderBy('first_name')
            ->get();

        return view('medecin.consultations.edit', compact('consultation', 'medecin', 'patients'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $consultation = Consultation::findOrFail($id);

        // Vérifier que le rapport médical appartient bien au médecin connecté
        if ($consultation->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'date_consultation' => 'required|date',
            'heure_consultation' => 'nullable',
            'motif' => 'nullable|string',
            'antecedents' => 'nullable|string',
            'histoire_maladie' => 'nullable|string',
            'examen_clinique' => 'nullable|string',
            'conduite_tenir' => 'nullable|string',
            'resume' => 'nullable|string',
        ]);

        $consultation->update($validated);

        return redirect()
            ->route('medecin.consultations.show', $consultation->id)
            ->with('success', 'Rapport médical mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $consultation = Consultation::findOrFail($id);

        // Vérifier que le rapport médical appartient bien au médecin connecté
        if ($consultation->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        $consultation->delete();

        return redirect()
            ->route('medecin.consultations.index')
            ->with('success', 'Rapport médical supprimé avec succès.');
    }

    public function printPdf($id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $consultation = Consultation::with(['patient', 'medecin'])->findOrFail($id);

        // Vérifier que le rapport médical appartient bien au médecin connecté
        if ($consultation->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        return view('medecin.consultations.print', compact('consultation'));
    }

    public function searchPatients(Request $request)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        if (!$medecin) {
            return response()->json([]);
        }

        $search = $request->get('q', '');
        
        // Recherche UNIQUEMENT parmi les patients examinés par ce médecin (via caisses)
        $patients = GestionPatient::query()
            ->whereHas('caisses', function($q) use ($medecin) {
                $q->where('medecin_id', $medecin->id);
            })
            ->where(function($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get()
            ->map(function($patient) {
                return [
                    'id' => $patient->id,
                    'text' => $patient->first_name . ' ' . $patient->last_name . ' - ' . ($patient->phone ?? 'N/A'),
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'phone' => $patient->phone,
                    'age' => $patient->age,
                ];
            });

        return response()->json($patients);
    }
}

