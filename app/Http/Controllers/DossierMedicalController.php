<?php

namespace App\Http\Controllers;

use App\Models\DossierMedical;
use App\Models\GestionPatient;
use App\Models\Caisse;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DossierMedicalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statut = $request->input('statut');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');

        $query = DossierMedical::with(['patient']);

        // Filtre par recherche
        if ($search) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%");
            })
                ->orWhere('numero_dossier', 'like', "%{$search}%");
        }

        // Filtre par statut
        if ($statut) {
            $query->where('statut', $statut);
        }

        // Filtre par date de dernière visite
        if ($dateDebut) {
            $query->where('derniere_visite', '>=', $dateDebut);
        }
        if ($dateFin) {
            $query->where('derniere_visite', '<=', $dateFin);
        }

        $dossiers = $query->orderBy('derniere_visite', 'desc')
            ->orderBy('nombre_visites', 'desc')
            ->paginate(10);

        return view('dossiermedical.index', compact('dossiers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Les dossiers sont créés automatiquement, pas besoin de formulaire de création
        return redirect()->route('dossiers.index')->with('info', 'Les dossiers sont créés automatiquement lors de la première visite du patient.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Les dossiers sont créés automatiquement
        return redirect()->route('dossiers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dossier = DossierMedical::with([
            'patient',
            'examens.medecin',
            'examens.examen',
            'examens.service',
            'examens.prescripteur',
            'rendezVous.medecin'
        ])->findOrFail($id);

        // Calculer les statistiques
        $statistiques = $dossier->calculerStatistiques();

        // Récupérer l'historique des examens
        $examens = $dossier->examens()
            ->with(['medecin', 'examen', 'service', 'prescripteur'])
            ->orderBy('date_examen', 'desc')
            ->get();

        // Récupérer l'historique des rendez-vous
        $rendezVous = $dossier->rendezVous()
            ->with(['medecin'])
            ->orderBy('date_rdv', 'desc')
            ->orderBy('heure_rdv', 'desc')
            ->get();

        // Grouper les examens par année/mois
        $examensParPeriode = $examens->groupBy(function ($examen) {
            return $examen->date_examen->format('Y-m');
        });

        return view('dossiermedical.show', compact('dossier', 'statistiques', 'examens', 'rendezVous', 'examensParPeriode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $dossier = DossierMedical::with('patient')->findOrFail($id);
        return view('dossiermedical.edit', compact('dossier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $dossier = DossierMedical::findOrFail($id);

        $request->validate([
            'statut' => 'required|in:actif,inactif,archive',
            'notes_generales' => 'nullable|string|max:1000',
        ]);

        $dossier->update($request->only(['statut', 'notes_generales']));

        return redirect()->route('dossiers.show', $dossier->id)
            ->with('success', 'Dossier médical mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $dossier = DossierMedical::findOrFail($id);
        $dossier->delete();

        return redirect()->route('dossiers.index')
            ->with('success', 'Dossier médical supprimé avec succès.');
    }

    /**
     * Méthode pour créer ou mettre à jour automatiquement un dossier
     */
    public static function creerOuMettreAJour($patientId)
    {
        $patient = GestionPatient::find($patientId);

        if (!$patient) {
            return null;
        }

        // Vérifier si le patient a au moins un examen
        $aDesExamens = Caisse::where('gestion_patient_id', $patientId)->exists();

        if (!$aDesExamens) {
            return null; // Pas de dossier si pas d'examens
        }

        // Chercher le dossier existant ou en créer un nouveau
        $dossier = DossierMedical::firstOrNew(['patient_id' => $patientId]);

        if (!$dossier->exists) {
            // Créer un nouveau dossier
            $dossier->numero_dossier = 'DOS-' . str_pad($patientId, 6, '0', STR_PAD_LEFT);
            $dossier->date_creation = Carbon::now();
            $dossier->statut = 'actif';
        }

        // Mettre à jour les statistiques
        $examens = Caisse::where('gestion_patient_id', $patientId);

        $dossier->nombre_visites = $examens->count();
        $dossier->total_depense = $examens->sum('total');
        $dossier->derniere_visite = $examens->max('date_examen');

        $dossier->save();

        return $dossier;
    }

    /**
     * Méthode pour synchroniser tous les dossiers
     */
    public function synchroniser()
    {
        // Récupérer tous les patients qui ont des examens
        $patientsAvecExamens = GestionPatient::whereHas('caisses')->get();

        foreach ($patientsAvecExamens as $patient) {
            self::creerOuMettreAJour($patient->id);
        }

        return redirect()->route('dossiers.index')
            ->with('success', 'Synchronisation des dossiers terminée.');
    }
}
