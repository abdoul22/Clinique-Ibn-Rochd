<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\GestionPatient;
use App\Models\DossierMedical;
use App\Models\Medecin;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    /**
     * Afficher la liste de TOUTES les consultations (superadmin)
     */
    public function index(Request $request)
    {
        $query = Consultation::with(['patient', 'medecin', 'dossierMedical']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($pq) use ($search) {
                    $pq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhere('motif', 'like', "%{$search}%")
                ->orWhere('resume', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date_consultation', $request->date);
        }

        if ($request->filled('medecin_id')) {
            $query->where('medecin_id', $request->medecin_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $consultations = $query->latest('date_consultation')->paginate(20);
        
        // Liste des médecins pour le filtre
        $medecins = Medecin::where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        return view('superadmin.medical.consultations.index', compact('consultations', 'medecins'));
    }

    /**
     * Formulaire de création d'une consultation
     */
    public function create(Request $request)
    {
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = GestionPatient::find($request->patient_id);
        }

        // SUPERADMIN : Accès à TOUS les patients
        $patients = GestionPatient::orderBy('first_name')->get();
        
        // Liste de tous les médecins actifs
        $medecins = Medecin::where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        return view('superadmin.medical.consultations.create', compact('patient', 'patients', 'medecins'));
    }

    /**
     * Enregistrer une nouvelle consultation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
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

        $validated['dossier_medical_id'] = $dossier->id;
        $validated['statut'] = 'terminee';

        $consultation = Consultation::create($validated);

        // Mettre à jour le dossier médical
        $dossier->derniere_visite = Carbon::now();
        $dossier->save();

        return redirect()
            ->route('superadmin.medical.consultations.show', $consultation->id)
            ->with('success', 'Rapport médical créé avec succès.');
    }

    /**
     * Afficher les détails d'une consultation
     */
    public function show($id)
    {
        $consultation = Consultation::with(['patient', 'medecin', 'dossierMedical', 'ordonnances'])
            ->findOrFail($id);

        return view('superadmin.medical.consultations.show', compact('consultation'));
    }

    /**
     * Formulaire d'édition d'une consultation
     */
    public function edit($id)
    {
        $consultation = Consultation::with(['patient', 'medecin'])->findOrFail($id);

        // SUPERADMIN : Accès à TOUS les patients
        $patients = GestionPatient::orderBy('first_name')->get();
        
        // Liste de tous les médecins actifs
        $medecins = Medecin::where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        return view('superadmin.medical.consultations.edit', compact('consultation', 'patients', 'medecins'));
    }

    /**
     * Mettre à jour une consultation
     */
    public function update(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        $validated = $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
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
            ->route('superadmin.medical.consultations.show', $consultation->id)
            ->with('success', 'Rapport médical mis à jour avec succès.');
    }

    /**
     * Supprimer une consultation
     * SUPERADMIN : Peut supprimer N'IMPORTE QUELLE consultation
     */
    public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();

        return redirect()
            ->route('superadmin.medical.consultations.index')
            ->with('success', 'Rapport médical supprimé avec succès.');
    }

    /**
     * Imprimer une consultation
     */
    public function print($id)
    {
        $consultation = Consultation::with(['patient', 'medecin', 'dossierMedical'])
            ->findOrFail($id);

        return view('superadmin.medical.consultations.print', compact('consultation'));
    }

    /**
     * Exporter une consultation en PDF
     */
    public function exportPdf($id)
    {
        $consultation = Consultation::with(['patient', 'medecin', 'dossierMedical'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('superadmin.medical.consultations.pdf', compact('consultation'));
        return $pdf->download('consultation-' . $consultation->id . '.pdf');
    }
}

