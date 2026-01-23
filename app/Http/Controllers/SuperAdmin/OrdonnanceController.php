<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Ordonnance;
use App\Models\OrdonnanceMedicament;
use App\Models\Medicament;
use App\Models\GestionPatient;
use App\Models\Medecin;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class OrdonnanceController extends Controller
{
    /**
     * Afficher la liste de TOUTES les ordonnances (superadmin)
     */
    public function index(Request $request)
    {
        $query = Ordonnance::with(['patient', 'medecin', 'medicaments', 'consultation']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date_ordonnance', $request->date);
        }

        if ($request->filled('medecin_id')) {
            $query->where('medecin_id', $request->medecin_id);
        }

        $ordonnances = $query->latest('date_ordonnance')->paginate(20);
        
        // Liste des médecins pour le filtre
        $medecins = Medecin::where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        return view('superadmin.medical.ordonnances.index', compact('ordonnances', 'medecins'));
    }

    /**
     * Formulaire de création d'une ordonnance
     */
    public function create(Request $request)
    {
        $patient = null;
        $consultation = null;

        if ($request->filled('consultation_id')) {
            $consultation = Consultation::with('patient')->findOrFail($request->consultation_id);
            $patient = $consultation->patient;
        } elseif ($request->filled('patient_id')) {
            $patient = GestionPatient::findOrFail($request->patient_id);
        }

        // SUPERADMIN : Accès à TOUS les patients
        $patients = GestionPatient::orderBy('first_name')->get();
        
        // Liste de tous les médecins actifs
        $medecins = Medecin::where('statut', 'actif')
            ->orderBy('nom')
            ->get();
            
        $medicaments = Medicament::actifs()->orderBy('nom')->get();

        return view('superadmin.medical.ordonnances.create', compact('patient', 'consultation', 'patients', 'medecins', 'medicaments'));
    }

    /**
     * Enregistrer une nouvelle ordonnance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'date_ordonnance' => 'required|date',
            'date_expiration' => 'nullable|date|after:date_ordonnance',
            'notes' => 'nullable|string',
            'medicaments' => 'required|array|min:1',
            'medicaments.*.medicament_id' => 'nullable|exists:medicaments,id',
            'medicaments.*.medicament_nom' => 'required|string',
            'medicaments.*.dosage' => 'nullable|string',
            'medicaments.*.duree' => 'nullable|string',
            'medicaments.*.note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Créer l'ordonnance
            $ordonnance = Ordonnance::create([
                'patient_id' => $validated['patient_id'],
                'medecin_id' => $validated['medecin_id'],
                'consultation_id' => $validated['consultation_id'] ?? null,
                'date_ordonnance' => $validated['date_ordonnance'],
                'date_expiration' => $validated['date_expiration'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'statut' => 'active',
            ]);

            // Ajouter les médicaments
            foreach ($validated['medicaments'] as $index => $med) {
                OrdonnanceMedicament::create([
                    'ordonnance_id' => $ordonnance->id,
                    'medicament_id' => $med['medicament_id'] ?? null,
                    'medicament_nom' => $med['medicament_nom'],
                    'dosage' => $med['dosage'] ?? null,
                    'duree' => $med['duree'] ?? null,
                    'note' => $med['note'] ?? null,
                    'ordre' => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('superadmin.medical.ordonnances.show', $ordonnance->id)
                ->with('success', 'Ordonnance créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'ordonnance : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'une ordonnance
     */
    public function show($id)
    {
        $ordonnance = Ordonnance::with(['patient', 'medecin', 'medicaments.medicament', 'consultation'])
            ->findOrFail($id);

        return view('superadmin.medical.ordonnances.show', compact('ordonnance'));
    }

    /**
     * Formulaire d'édition d'une ordonnance
     */
    public function edit($id)
    {
        $ordonnance = Ordonnance::with(['patient', 'medecin', 'medicaments'])->findOrFail($id);

        // SUPERADMIN : Accès à TOUS les patients
        $patients = GestionPatient::orderBy('first_name')->get();
        
        // Liste de tous les médecins actifs
        $medecins = Medecin::where('statut', 'actif')
            ->orderBy('nom')
            ->get();
            
        $medicaments = Medicament::actifs()->orderBy('nom')->get();

        return view('superadmin.medical.ordonnances.edit', compact('ordonnance', 'patients', 'medecins', 'medicaments'));
    }

    /**
     * Mettre à jour une ordonnance
     */
    public function update(Request $request, $id)
    {
        $ordonnance = Ordonnance::findOrFail($id);

        $validated = $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'date_ordonnance' => 'required|date',
            'date_expiration' => 'nullable|date|after:date_ordonnance',
            'notes' => 'nullable|string',
            'medicaments' => 'required|array|min:1',
            'medicaments.*.medicament_id' => 'nullable|exists:medicaments,id',
            'medicaments.*.medicament_nom' => 'required|string',
            'medicaments.*.dosage' => 'nullable|string',
            'medicaments.*.duree' => 'nullable|string',
            'medicaments.*.note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour l'ordonnance
            $ordonnance->update([
                'medecin_id' => $validated['medecin_id'],
                'date_ordonnance' => $validated['date_ordonnance'],
                'date_expiration' => $validated['date_expiration'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Supprimer les anciens médicaments et ajouter les nouveaux
            $ordonnance->medicaments()->delete();
            
            foreach ($validated['medicaments'] as $index => $med) {
                OrdonnanceMedicament::create([
                    'ordonnance_id' => $ordonnance->id,
                    'medicament_id' => $med['medicament_id'] ?? null,
                    'medicament_nom' => $med['medicament_nom'],
                    'dosage' => $med['dosage'] ?? null,
                    'duree' => $med['duree'] ?? null,
                    'note' => $med['note'] ?? null,
                    'ordre' => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('superadmin.medical.ordonnances.show', $ordonnance->id)
                ->with('success', 'Ordonnance mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'ordonnance : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une ordonnance
     * SUPERADMIN : Peut supprimer N'IMPORTE QUELLE ordonnance
     */
    public function destroy($id)
    {
        $ordonnance = Ordonnance::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Supprimer les médicaments associés
            $ordonnance->medicaments()->delete();
            
            // Supprimer l'ordonnance
            $ordonnance->delete();
            
            DB::commit();

            return redirect()
                ->route('superadmin.medical.ordonnances.index')
                ->with('success', 'Ordonnance supprimée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Imprimer une ordonnance
     */
    public function print($id)
    {
        $ordonnance = Ordonnance::with(['patient', 'medecin', 'medicaments.medicament'])
            ->findOrFail($id);

        return view('superadmin.medical.ordonnances.print', compact('ordonnance'));
    }
}


