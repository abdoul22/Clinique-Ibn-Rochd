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
    public function mesPatients(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        if (!$medecin) {
            return redirect()->route('login')->with('error', 'Aucun profil médecin associé à votre compte.');
        }

        // Construire la requête de base
        $query = GestionPatient::whereHas('consultations', function ($q) use ($medecin) {
            $q->where('medecin_id', $medecin->id);
        });

        // Recherche par nom ou téléphone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtre par période de dernière consultation
        if ($request->filled('periode')) {
            $periode = $request->periode;
            $query->whereHas('consultations', function ($q) use ($medecin, $periode) {
                $q->where('medecin_id', $medecin->id);
                
                switch ($periode) {
                    case 'aujourdhui':
                        $q->whereDate('date_consultation', Carbon::today());
                        break;
                    case 'semaine':
                        $q->whereBetween('date_consultation', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'mois':
                        $q->whereMonth('date_consultation', Carbon::now()->month)
                          ->whereYear('date_consultation', Carbon::now()->year);
                        break;
                    case '3mois':
                        $q->where('date_consultation', '>=', Carbon::now()->subMonths(3));
                        break;
                    case '6mois':
                        $q->where('date_consultation', '>=', Carbon::now()->subMonths(6));
                        break;
                    case 'annee':
                        $q->whereYear('date_consultation', Carbon::now()->year);
                        break;
                }
            });
        }

        // Récupérer les patients avec leurs statistiques
        $patients = $query
            ->withCount(['consultations' => function ($q) use ($medecin) {
                $q->where('medecin_id', $medecin->id);
            }])
            ->with(['consultations' => function ($q) use ($medecin) {
                $q->where('medecin_id', $medecin->id)
                    ->latest('date_consultation')
                    ->limit(1);
            }])
            ->orderBy('first_name')
            ->paginate(20)
            ->appends($request->query());

        // Statistiques globales
        $stats = [
            'total_patients' => GestionPatient::whereHas('consultations', function ($q) use ($medecin) {
                $q->where('medecin_id', $medecin->id);
            })->count(),
            
            'total_consultations' => Consultation::where('medecin_id', $medecin->id)->count(),
            
            'patients_actifs' => GestionPatient::whereHas('consultations', function ($q) use ($medecin) {
                $q->where('medecin_id', $medecin->id)
                  ->where('date_consultation', '>=', Carbon::now()->subMonths(6));
            })->count(),
        ];

        return view('medecin.patients.index', compact('medecin', 'patients', 'stats'));
    }

    /**
     * Exporter la liste des patients en PDF
     */
    public function exportPatientsPdf(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $medecin = $user->medecin;

        if (!$medecin) {
            return redirect()->route('login')->with('error', 'Aucun profil médecin associé à votre compte.');
        }

        // Récupérer tous les patients (sans pagination pour l'export)
        $query = GestionPatient::whereHas('consultations', function ($q) use ($medecin) {
            $q->where('medecin_id', $medecin->id);
        });

        // Appliquer les mêmes filtres que la liste
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('periode')) {
            $periode = $request->periode;
            $query->whereHas('consultations', function ($q) use ($medecin, $periode) {
                $q->where('medecin_id', $medecin->id);
                
                switch ($periode) {
                    case 'aujourdhui':
                        $q->whereDate('date_consultation', Carbon::today());
                        break;
                    case 'semaine':
                        $q->whereBetween('date_consultation', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'mois':
                        $q->whereMonth('date_consultation', Carbon::now()->month)
                          ->whereYear('date_consultation', Carbon::now()->year);
                        break;
                    case '3mois':
                        $q->where('date_consultation', '>=', Carbon::now()->subMonths(3));
                        break;
                    case '6mois':
                        $q->where('date_consultation', '>=', Carbon::now()->subMonths(6));
                        break;
                    case 'annee':
                        $q->whereYear('date_consultation', Carbon::now()->year);
                        break;
                }
            });
        }

        $patients = $query
            ->withCount(['consultations' => function ($q) use ($medecin) {
                $q->where('medecin_id', $medecin->id);
            }])
            ->with(['consultations' => function ($q) use ($medecin) {
                $q->where('medecin_id', $medecin->id)
                    ->latest('date_consultation')
                    ->limit(1);
            }])
            ->orderBy('first_name')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('medecin.patients.pdf', compact('medecin', 'patients'));
        
        return $pdf->download('mes-patients-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
}

