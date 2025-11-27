<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Ordonnance;
use App\Models\GestionPatient;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        if (!$medecin) {
            return redirect()->route('login')->with('error', 'Aucun profil médecin associé à votre compte.');
        }

        // Statistiques
        $stats = [
            'consultations_aujourdhui' => Consultation::parMedecin($medecin->id)
                ->whereDate('date_consultation', Carbon::today())
                ->count(),
            
            'consultations_mois' => Consultation::parMedecin($medecin->id)
                ->whereYear('date_consultation', Carbon::now()->year)
                ->whereMonth('date_consultation', Carbon::now()->month)
                ->count(),
            
            'ordonnances_mois' => Ordonnance::parMedecin($medecin->id)
                ->whereYear('date_ordonnance', Carbon::now()->year)
                ->whereMonth('date_ordonnance', Carbon::now()->month)
                ->count(),
            
            'patients_total' => Consultation::parMedecin($medecin->id)
                ->distinct('patient_id')
                ->count('patient_id'),
        ];

        // Dernières consultations
        $dernieresConsultations = Consultation::parMedecin($medecin->id)
            ->with(['patient', 'dossierMedical'])
            ->latest('date_consultation')
            ->limit(5)
            ->get();

        // Consultations à venir (si RDV existe)
        $consultationsAVenir = Consultation::parMedecin($medecin->id)
            ->where('statut', 'en_cours')
            ->whereDate('date_consultation', '>=', Carbon::today())
            ->with(['patient'])
            ->orderBy('date_consultation')
            ->limit(5)
            ->get();

        return view('medecin.dashboard', compact(
            'medecin',
            'stats',
            'dernieresConsultations',
            'consultationsAVenir'
        ));
    }

    /**
     * Afficher la liste des patients du médecin
     * (Seulement les patients qu'il a déjà consultés)
     */
    public function mesPatients()
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        if (!$medecin) {
            return redirect()->route('login')->with('error', 'Aucun profil médecin associé à votre compte.');
        }

        // Récupérer tous les patients uniques consultés par ce médecin
        $patients = GestionPatient::whereHas('consultations', function ($query) use ($medecin) {
            $query->where('medecin_id', $medecin->id);
        })
        ->withCount(['consultations' => function ($query) use ($medecin) {
            $query->where('medecin_id', $medecin->id);
        }])
        ->with(['consultations' => function ($query) use ($medecin) {
            $query->where('medecin_id', $medecin->id)
                ->latest('date_consultation')
                ->limit(1);
        }])
        ->orderBy('first_name')
        ->paginate(20);

        return view('medecin.patients.index', compact('medecin', 'patients'));
    }
}

