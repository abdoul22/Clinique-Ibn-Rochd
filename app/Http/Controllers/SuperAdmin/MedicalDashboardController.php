<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Medecin;
use App\Models\Consultation;
use App\Models\Ordonnance;
use App\Models\GestionPatient;
use App\Models\Caisse;
use App\Models\EtatCaisse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MedicalDashboardController extends Controller
{
    /**
     * Afficher le récapitulatif de tous les médecins avec statistiques
     */
    public function index(Request $request)
    {
        // Récupérer tous les médecins actifs avec leurs relations
        $query = Medecin::where('statut', 'actif');

        // Filtres
        if ($request->filled('specialite')) {
            $query->where('specialite', $request->specialite);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%");
            });
        }

        $medecins = $query->orderBy('nom')->get();

        // Calculer les statistiques pour chaque médecin
        $medecinStats = [];
        $totalConsultationsMois = 0;
        $totalOrdonnancesMois = 0;
        $totalRevenusMois = 0;

        foreach ($medecins as $medecin) {
            // Statistiques du mois en cours
            $consultationsMois = Caisse::where('medecin_id', $medecin->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();

            $ordonnancesMois = Ordonnance::where('medecin_id', $medecin->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();

            // Revenus du mois (part médecin via états de caisse)
            $revenusMois = EtatCaisse::where('medecin_id', $medecin->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('part_medecin');

            // Statistiques totales
            $consultationsTotal = Caisse::where('medecin_id', $medecin->id)->count();
            $ordonnancesTotal = Ordonnance::where('medecin_id', $medecin->id)->count();
            $patientsTotal = GestionPatient::whereHas('caisses', function($q) use ($medecin) {
                $q->where('medecin_id', $medecin->id);
            })->count();

            // Revenus total
            $revenusTotal = EtatCaisse::where('medecin_id', $medecin->id)
                ->sum('part_medecin');

            // Dernière activité
            $derniereActivite = Caisse::where('medecin_id', $medecin->id)
                ->latest('created_at')
                ->first();

            $medecinStats[] = [
                'medecin' => $medecin,
                'mois' => [
                    'consultations' => $consultationsMois,
                    'ordonnances' => $ordonnancesMois,
                    'revenus' => $revenusMois,
                ],
                'total' => [
                    'consultations' => $consultationsTotal,
                    'ordonnances' => $ordonnancesTotal,
                    'patients' => $patientsTotal,
                    'revenus' => $revenusTotal,
                ],
                'derniere_activite' => $derniereActivite ? $derniereActivite->created_at : null,
            ];

            $totalConsultationsMois += $consultationsMois;
            $totalOrdonnancesMois += $ordonnancesMois;
            $totalRevenusMois += $revenusMois;
        }

        // Trier par nombre de consultations du mois (desc)
        usort($medecinStats, function($a, $b) {
            return $b['mois']['consultations'] <=> $a['mois']['consultations'];
        });

        // Top 5 médecins pour les graphiques
        $top5Medecins = array_slice($medecinStats, 0, 5);

        // Données pour le graphique d'évolution mensuelle (6 derniers mois)
        $evolutionMensuelle = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mois = $date->format('M Y');
            
            $consultations = Caisse::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $ordonnances = Ordonnance::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $evolutionMensuelle[] = [
                'mois' => $mois,
                'consultations' => $consultations,
                'ordonnances' => $ordonnances,
            ];
        }

        // Liste des spécialités pour le filtre
        $specialites = Medecin::where('statut', 'actif')
            ->distinct()
            ->pluck('specialite')
            ->filter();

        return view('superadmin.medical.recap-medecins', compact(
            'medecinStats',
            'top5Medecins',
            'evolutionMensuelle',
            'specialites',
            'totalConsultationsMois',
            'totalOrdonnancesMois',
            'totalRevenusMois'
        ));
    }

    /**
     * Afficher les détails d'un médecin spécifique
     */
    public function show($id)
    {
        $medecin = Medecin::with('user')->findOrFail($id);
        
        // Statistiques du mois en cours
        $rapportsMois = Consultation::where('medecin_id', $medecin->id)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $ordonnancesMois = Ordonnance::where('medecin_id', $medecin->id)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->with('patient')
            ->get();

        $revenusMois = EtatCaisse::where('medecin_id', $medecin->id)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('part_medecin');

        // Statistiques totales
        $rapportsTotal = Consultation::where('medecin_id', $medecin->id)->count();
        $ordonnancesTotal = Ordonnance::where('medecin_id', $medecin->id)->count();
        $consultationsTotal = Caisse::where('medecin_id', $medecin->id)->count();
        $patientsTotal = GestionPatient::whereHas('caisses', function($q) use ($medecin) {
            $q->where('medecin_id', $medecin->id);
        })->count();

        $revenusTotal = EtatCaisse::where('medecin_id', $medecin->id)
            ->sum('part_medecin');

        // Dernières activités
        $dernieresConsultations = Consultation::where('medecin_id', $medecin->id)
            ->with('patient')
            ->latest()
            ->take(5)
            ->get();

        $dernieresOrdonnances = Ordonnance::where('medecin_id', $medecin->id)
            ->with('patient')
            ->latest()
            ->take(5)
            ->get();

        // Évolution des 6 derniers mois pour ce médecin
        $evolutionMensuelle = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mois = $date->format('M Y');
            
            $rapports = Consultation::where('medecin_id', $medecin->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $ordonnances = Ordonnance::where('medecin_id', $medecin->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $revenus = EtatCaisse::where('medecin_id', $medecin->id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('part_medecin');

            $evolutionMensuelle[] = [
                'mois' => $mois,
                'rapports' => $rapports,
                'ordonnances' => $ordonnances,
                'revenus' => $revenus,
            ];
        }

        return view('superadmin.medical.recap-medecins-show', compact(
            'medecin',
            'rapportsMois',
            'ordonnancesMois',
            'revenusMois',
            'rapportsTotal',
            'ordonnancesTotal',
            'consultationsTotal',
            'patientsTotal',
            'revenusTotal',
            'dernieresConsultations',
            'dernieresOrdonnances',
            'evolutionMensuelle'
        ));
    }

    /**
     * Afficher les consultations d'un médecin spécifique
     */
    public function consultationsByMedecin($medecinId, Request $request)
    {
        $medecin = Medecin::findOrFail($medecinId);
        
        $query = Consultation::where('medecin_id', $medecin->id)
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

        return view('superadmin.medical.consultations-by-medecin', compact('consultations', 'medecin'));
    }

    /**
     * Afficher les ordonnances d'un médecin spécifique
     */
    public function ordonnancesByMedecin($medecinId, Request $request)
    {
        $medecin = Medecin::findOrFail($medecinId);
        
        $query = Ordonnance::where('medecin_id', $medecin->id)
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

        return view('superadmin.medical.ordonnances-by-medecin', compact('ordonnances', 'medecin'));
    }

    /**
     * Afficher les patients d'un médecin spécifique
     */
    public function patientsByMedecin($medecinId, Request $request)
    {
        $medecin = Medecin::findOrFail($medecinId);
        
        $query = GestionPatient::whereHas('caisses', function($q) use ($medecin) {
            $q->where('medecin_id', $medecin->id);
        });

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(20);

        return view('superadmin.medical.patients-by-medecin', compact('patients', 'medecin'));
    }

    /**
     * Exporter le récapitulatif en PDF
     */
    public function exportPdf()
    {
        $medecins = Medecin::where('statut', 'actif')->orderBy('nom')->get();

        $medecinStats = [];
        foreach ($medecins as $medecin) {
            $consultationsMois = Caisse::where('medecin_id', $medecin->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();

            $consultationsTotal = Caisse::where('medecin_id', $medecin->id)->count();
            
            $revenusMois = EtatCaisse::where('medecin_id', $medecin->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('part_medecin');

            $medecinStats[] = [
                'medecin' => $medecin,
                'consultations_mois' => $consultationsMois,
                'consultations_total' => $consultationsTotal,
                'revenus_mois' => $revenusMois,
            ];
        }

        $pdf = Pdf::loadView('superadmin.medical.recap-medecins-pdf', compact('medecinStats'));
        return $pdf->download('recap-medecins-' . date('Y-m-d') . '.pdf');
    }
}

