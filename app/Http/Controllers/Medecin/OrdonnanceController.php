<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Ordonnance;
use App\Models\OrdonnanceMedicament;
use App\Models\Medicament;
use App\Models\GestionPatient;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class OrdonnanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        if (!$medecin) {
            return redirect()->route('login')->with('error', 'Aucun profil médecin associé à votre compte.');
        }

        $query = Ordonnance::parMedecin($medecin->id)
            ->with(['patient', 'medicaments', 'consultation']);

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

        $ordonnances = $query->latest('date_ordonnance')->paginate(20);

        return view('medecin.ordonnances.index', compact('ordonnances', 'medecin'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        if (!$medecin) {
            return redirect()->route('login')->with('error', 'Aucun profil médecin associé à votre compte.');
        }

        $patient = null;
        $consultation = null;

        if ($request->filled('consultation_id')) {
            $consultation = Consultation::with('patient')->findOrFail($request->consultation_id);
            $patient = $consultation->patient;
        } elseif ($request->filled('patient_id')) {
            $patient = GestionPatient::findOrFail($request->patient_id);
        }

        $patients = GestionPatient::orderBy('first_name')->limit(100)->get();
        $medicaments = Medicament::actifs()->orderBy('nom')->get();

        return view('medecin.ordonnances.create', compact('medecin', 'patient', 'consultation', 'patients', 'medicaments'));
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
                'medecin_id' => $medecin->id,
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
                ->route('medecin.ordonnances.show', $ordonnance->id)
                ->with('success', 'Ordonnance créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'ordonnance : ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $ordonnance = Ordonnance::with(['patient', 'medecin', 'medicaments.medicament', 'consultation'])
            ->findOrFail($id);

        // Vérifier que l'ordonnance appartient bien au médecin connecté
        if ($ordonnance->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        return view('medecin.ordonnances.show', compact('ordonnance', 'medecin'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $ordonnance = Ordonnance::with(['patient', 'medicaments'])->findOrFail($id);

        // Vérifier que l'ordonnance appartient bien au médecin connecté
        if ($ordonnance->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        $patients = GestionPatient::orderBy('first_name')->limit(100)->get();
        $medicaments = Medicament::actifs()->orderBy('nom')->get();

        return view('medecin.ordonnances.edit', compact('ordonnance', 'medecin', 'patients', 'medicaments'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $ordonnance = Ordonnance::findOrFail($id);

        // Vérifier que l'ordonnance appartient bien au médecin connecté
        if ($ordonnance->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
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
                'date_ordonnance' => $validated['date_ordonnance'],
                'date_expiration' => $validated['date_expiration'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Supprimer les anciens médicaments
            $ordonnance->medicaments()->delete();

            // Ajouter les nouveaux médicaments
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
                ->route('medecin.ordonnances.show', $ordonnance->id)
                ->with('success', 'Ordonnance mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'ordonnance : ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $ordonnance = Ordonnance::findOrFail($id);

        // Vérifier que l'ordonnance appartient bien au médecin connecté
        if ($ordonnance->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        $ordonnance->delete();

        return redirect()
            ->route('medecin.ordonnances.index')
            ->with('success', 'Ordonnance supprimée avec succès.');
    }

    public function printPdf($id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $ordonnance = Ordonnance::with(['patient', 'medecin', 'medicaments'])->findOrFail($id);

        // Vérifier que l'ordonnance appartient bien au médecin connecté
        if ($ordonnance->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        $pdf = Pdf::loadView('medecin.ordonnances.pdf', compact('ordonnance'));
        
        return $pdf->stream('ordonnance-' . $ordonnance->reference . '.pdf');
    }

    public function print($id)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        $ordonnance = Ordonnance::with(['patient', 'medecin', 'medicaments'])->findOrFail($id);

        // Vérifier que l'ordonnance appartient bien au médecin connecté
        if ($ordonnance->medecin_id !== $medecin->id) {
            abort(403, 'Accès non autorisé');
        }

        $formatClass = request('format', 'a5') === 'a4' ? 'format-a4' : 'format-a5';

        return view('medecin.ordonnances.print', compact('ordonnance', 'formatClass'));
    }

    public function searchMedicaments(Request $request)
    {
        $search = $request->get('q', '');
        
        $medicaments = Medicament::actifs()
            ->rechercheParNom($search)
            ->limit(20)
            ->get()
            ->map(function($medicament) {
                return [
                    'id' => $medicament->id,
                    'text' => $medicament->nom_complet,
                    'nom' => $medicament->nom,
                    'forme' => $medicament->forme,
                    'dosage' => $medicament->dosage,
                ];
            });

        return response()->json($medicaments);
    }

    public function storeMedicament(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'forme' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:255',
        ]);

        $medicament = Medicament::create([
            'nom' => $validated['nom'],
            'forme' => $validated['forme'] ?? null,
            'dosage' => $validated['dosage'] ?? null,
            'actif' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Médicament créé avec succès',
            'medicament' => [
                'id' => $medicament->id,
                'nom' => $medicament->nom,
                'forme' => $medicament->forme,
                'dosage' => $medicament->dosage,
                'nom_complet' => $medicament->nom_complet,
            ]
        ]);
    }
}

