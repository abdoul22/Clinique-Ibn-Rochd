<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Caisse;
use App\Models\ModePaiement;
use App\Models\EtatCaisse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecapCaissierController extends Controller
{
    /**
     * Afficher le récapitulatif de tous les caissiers avec statistiques avancées
     */
    public function index(Request $request)
    {
        // Récupérer tous les caissiers (users avec fonction 'Caissier' et is_approved = true)
        $query = User::where('fonction', 'Caissier')
            ->where('is_approved', true)
            ->with('role');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre par période
        $periode = $request->input('periode', 'mois'); // jour, semaine, mois, annee, tout
        $dateDebut = $this->getDateDebut($periode);
        $dateFin = Carbon::now()->endOfDay();

        $caissiers = $query->orderBy('name')->get();

        // Calculer les statistiques pour chaque caissier
        $caissierStats = [];
        $totalFacturesPeriode = 0;
        $totalMontantPeriode = 0;
        $totalTransactionsPeriode = 0;

        foreach ($caissiers as $caissier) {
            // Factures de la période (basé sur nom_caissier)
            $facturesPeriode = Caisse::where('nom_caissier', $caissier->name)
                ->whereBetween('created_at', [$dateDebut, $dateFin])
                ->count();

            // Montant total encaissé pendant la période
            $montantPeriode = Caisse::where('nom_caissier', $caissier->name)
                ->whereBetween('created_at', [$dateDebut, $dateFin])
                ->sum('total');
            
            // Dépenses de la période
            $depensesPeriode = \App\Models\Depense::where('created_by', $caissier->id)
                ->whereBetween('created_at', [$dateDebut, $dateFin])
                ->sum('montant');
            
            // Montant net (recettes - dépenses)
            $montantNetPeriode = $montantPeriode - $depensesPeriode;

            // Transactions (modes de paiement) de la période
            $transactionsPeriode = ModePaiement::whereHas('caisse', function($q) use ($caissier, $dateDebut, $dateFin) {
                $q->where('nom_caissier', $caissier->name)
                  ->whereBetween('created_at', [$dateDebut, $dateFin]);
            })->count();

            // Statistiques totales (all-time)
            $facturesTotal = Caisse::where('nom_caissier', $caissier->name)->count();
            $montantTotal = Caisse::where('nom_caissier', $caissier->name)->sum('total');
            $depensesTotal = \App\Models\Depense::where('created_by', $caissier->id)->sum('montant');
            $montantNetTotal = $montantTotal - $depensesTotal;

            // Jours de travail actifs (jours où le caissier a créé au moins une facture)
            $joursActifs = Caisse::where('nom_caissier', $caissier->name)
                ->whereBetween('created_at', [$dateDebut, $dateFin])
                ->select(DB::raw('DATE(created_at) as date'))
                ->groupBy('date')
                ->get()
                ->count();

            // Moyenne par jour de travail
            $moyenneParJour = $joursActifs > 0 ? $facturesPeriode / $joursActifs : 0;

            // Dernière activité
            $derniereActivite = Caisse::where('nom_caissier', $caissier->name)
                ->latest('created_at')
                ->first();

            // Première activité (pour calculer l'ancienneté)
            $premiereActivite = Caisse::where('nom_caissier', $caissier->name)
                ->oldest('created_at')
                ->first();

            // Types de paiement acceptés
            $typesPaiement = ModePaiement::whereHas('caisse', function($q) use ($caissier, $dateDebut, $dateFin) {
                $q->where('nom_caissier', $caissier->name)
                  ->whereBetween('created_at', [$dateDebut, $dateFin]);
            })
            ->select('type', DB::raw('count(*) as count'), DB::raw('sum(montant) as total'))
            ->groupBy('type')
            ->get();

            // Vitesse de traitement (factures par heure pendant les jours actifs)
            $vitesseTraitement = $joursActifs > 0 ? round($facturesPeriode / ($joursActifs * 8), 2) : 0; // Supposant 8h de travail par jour

            // Performance par rapport à la moyenne
            $moyenneGlobale = $facturesPeriode > 0 ? $facturesPeriode / max(1, $joursActifs) : 0;

            // Taux d'activité (jours actifs / jours ouvrables de la période)
            $joursOuvrables = $this->getJoursOuvrables($dateDebut, $dateFin);
            $tauxActivite = $joursOuvrables > 0 ? round(($joursActifs / $joursOuvrables) * 100, 1) : 0;

            $caissierStats[] = [
                'caissier' => $caissier,
                'periode' => [
                    'factures' => $facturesPeriode,
                    'montant' => $montantPeriode,
                    'depenses' => $depensesPeriode,
                    'montant_net' => $montantNetPeriode,
                    'transactions' => $transactionsPeriode,
                    'jours_actifs' => $joursActifs,
                    'moyenne_par_jour' => round($moyenneParJour, 2),
                    'vitesse_traitement' => $vitesseTraitement,
                    'taux_activite' => $tauxActivite,
                ],
                'total' => [
                    'factures' => $facturesTotal,
                    'montant' => $montantTotal,
                    'depenses' => $depensesTotal,
                    'montant_net' => $montantNetTotal,
                ],
                'types_paiement' => $typesPaiement,
                'derniere_activite' => $derniereActivite,
                'premiere_activite' => $premiereActivite,
                'anciennete_jours' => $premiereActivite ? Carbon::parse($premiereActivite->created_at)->diffInDays(Carbon::now()) : 0,
            ];

            $totalFacturesPeriode += $facturesPeriode;
            $totalMontantPeriode += $montantPeriode;
            $totalTransactionsPeriode += $transactionsPeriode;
        }

        // Trier par performance (nombre de factures dans la période)
        usort($caissierStats, function($a, $b) {
            return $b['periode']['factures'] <=> $a['periode']['factures'];
        });

        // Top 5 caissiers
        $top5Caissiers = array_slice($caissierStats, 0, 5);

        // Évolution mensuelle (derniers 12 mois)
        $evolutionMensuelle = [];
        $caissierIds = $caissiers->pluck('id');
        for ($i = 11; $i >= 0; $i--) {
            $mois = Carbon::now()->subMonths($i);
            $factures = Caisse::whereIn('nom_caissier', $caissiers->pluck('name'))
                ->whereYear('created_at', $mois->year)
                ->whereMonth('created_at', $mois->month)
                ->count();
            
            $recettes = Caisse::whereIn('nom_caissier', $caissiers->pluck('name'))
                ->whereYear('created_at', $mois->year)
                ->whereMonth('created_at', $mois->month)
                ->sum('total');
            
            $depenses = \App\Models\Depense::whereIn('created_by', $caissierIds)
                ->whereYear('created_at', $mois->year)
                ->whereMonth('created_at', $mois->month)
                ->sum('montant');

            $evolutionMensuelle[] = [
                'mois' => $mois->format('M Y'),
                'factures' => $factures,
                'recettes' => $recettes,
                'depenses' => $depenses,
                'montant' => $recettes - $depenses, // Montant net
            ];
        }

        // Statistiques globales de comparaison
        $moyenneFactures = count($caissierStats) > 0 ? round($totalFacturesPeriode / count($caissierStats), 2) : 0;
        $moyenneMontant = count($caissierStats) > 0 ? round($totalMontantPeriode / count($caissierStats), 2) : 0;

        return view('superadmin.recap-caissiers.index', compact(
            'caissierStats',
            'top5Caissiers',
            'evolutionMensuelle',
            'totalFacturesPeriode',
            'totalMontantPeriode',
            'totalTransactionsPeriode',
            'moyenneFactures',
            'moyenneMontant',
            'periode'
        ));
    }

    /**
     * Afficher les détails d'un caissier spécifique
     */
    public function show($id, Request $request)
    {
        $caissier = User::where('fonction', 'Caissier')->findOrFail($id);
        
        // Filtre par période
        $periode = $request->input('periode', 'mois');
        $dateDebut = $this->getDateDebut($periode);
        $dateFin = Carbon::now()->endOfDay();

        // Statistiques de la période
        $facturesPeriode = Caisse::where('nom_caissier', $caissier->name)
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->count();

        $montantPeriode = Caisse::where('nom_caissier', $caissier->name)
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->sum('total');
        
        $depensesPeriode = \App\Models\Depense::where('created_by', $caissier->id)
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->sum('montant');
        
        $montantNetPeriode = $montantPeriode - $depensesPeriode;

        // Statistiques totales
        $facturesTotal = Caisse::where('nom_caissier', $caissier->name)->count();
        $montantTotal = Caisse::where('nom_caissier', $caissier->name)->sum('total');
        $depensesTotal = \App\Models\Depense::where('created_by', $caissier->id)->sum('montant');
        $montantNetTotal = $montantTotal - $depensesTotal;

        // Jours de travail
        $joursActifs = Caisse::where('nom_caissier', $caissier->name)
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->select(DB::raw('DATE(created_at) as date'))
            ->groupBy('date')
            ->get()
            ->count();

        // Activité par jour de la semaine
        $activiteParJour = Caisse::where('nom_caissier', $caissier->name)
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->select(DB::raw('DAYOFWEEK(created_at) as jour'), DB::raw('count(*) as count'))
            ->groupBy('jour')
            ->get()
            ->mapWithKeys(function($item) {
                $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                return [$jours[$item->jour - 1] => $item->count];
            });

        // Activité par heure de la journée
        $activiteParHeure = Caisse::where('nom_caissier', $caissier->name)
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->select(DB::raw('HOUR(created_at) as heure'), DB::raw('count(*) as count'))
            ->groupBy('heure')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->heure . 'h' => $item->count];
            });

        // Types de paiement
        $typesPaiement = ModePaiement::whereHas('caisse', function($q) use ($caissier, $dateDebut, $dateFin) {
            $q->where('nom_caissier', $caissier->name)
              ->whereBetween('created_at', [$dateDebut, $dateFin]);
        })
        ->select('type', DB::raw('count(*) as count'), DB::raw('sum(montant) as total'))
        ->groupBy('type')
        ->get();

        // Dernières factures
        $dernieresFactures = Caisse::where('nom_caissier', $caissier->name)
            ->with(['patient', 'medecin', 'examen'])
            ->latest('created_at')
            ->limit(20)
            ->get();
        
        // Dernières dépenses
        $dernieresDepenses = \App\Models\Depense::where('created_by', $caissier->id)
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->latest('created_at')
            ->limit(20)
            ->get();

        // Performance mensuelle (derniers 12 mois)
        $performanceMensuelle = [];
        for ($i = 11; $i >= 0; $i--) {
            $mois = Carbon::now()->subMonths($i);
            $factures = Caisse::where('nom_caissier', $caissier->name)
                ->whereYear('created_at', $mois->year)
                ->whereMonth('created_at', $mois->month)
                ->count();
            
            $recettes = Caisse::where('nom_caissier', $caissier->name)
                ->whereYear('created_at', $mois->year)
                ->whereMonth('created_at', $mois->month)
                ->sum('total');
            
            $depenses = \App\Models\Depense::where('created_by', $caissier->id)
                ->whereYear('created_at', $mois->year)
                ->whereMonth('created_at', $mois->month)
                ->sum('montant');

            $performanceMensuelle[] = [
                'mois' => $mois->format('M Y'),
                'factures' => $factures,
                'recettes' => $recettes,
                'depenses' => $depenses,
                'montant' => $recettes - $depenses, // Montant net
            ];
        }

        // Records personnels
        $meilleureJournee = Caisse::where('nom_caissier', $caissier->name)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'), DB::raw('sum(total) as montant'))
            ->groupBy('date')
            ->orderBy('count', 'desc')
            ->first();

        return view('superadmin.recap-caissiers.show', compact(
            'caissier',
            'facturesPeriode',
            'montantPeriode',
            'depensesPeriode',
            'montantNetPeriode',
            'facturesTotal',
            'montantTotal',
            'depensesTotal',
            'montantNetTotal',
            'joursActifs',
            'activiteParJour',
            'activiteParHeure',
            'typesPaiement',
            'dernieresFactures',
            'dernieresDepenses',
            'performanceMensuelle',
            'meilleureJournee',
            'periode'
        ));
    }

    /**
     * Exporter le récapitulatif en PDF
     */
    public function exportPdf(Request $request)
    {
        $periode = $request->input('periode', 'mois');
        $dateDebut = $this->getDateDebut($periode);
        $dateFin = Carbon::now()->endOfDay();

        $caissiers = User::where('fonction', 'Caissier')
            ->where('is_approved', true)
            ->orderBy('name')
            ->get();

        $caissierStats = [];
        foreach ($caissiers as $caissier) {
            $facturesPeriode = Caisse::where('nom_caissier', $caissier->name)
                ->whereBetween('created_at', [$dateDebut, $dateFin])
                ->count();

            $montantPeriode = Caisse::where('nom_caissier', $caissier->name)
                ->whereBetween('created_at', [$dateDebut, $dateFin])
                ->sum('total');

            $caissierStats[] = [
                'caissier' => $caissier,
                'factures' => $facturesPeriode,
                'montant' => $montantPeriode,
            ];
        }

        $pdf = Pdf::loadView('superadmin.recap-caissiers.pdf', compact(
            'caissierStats',
            'periode',
            'dateDebut',
            'dateFin'
        ));

        return $pdf->download('recap-caissiers-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Helper: Obtenir la date de début selon la période
     */
    private function getDateDebut($periode)
    {
        return match($periode) {
            'jour' => Carbon::now()->startOfDay(),
            'semaine' => Carbon::now()->startOfWeek(),
            'mois' => Carbon::now()->startOfMonth(),
            'annee' => Carbon::now()->startOfYear(),
            'tout' => Carbon::create(2000, 1, 1),
            default => Carbon::now()->startOfMonth(),
        };
    }

    /**
     * Helper: Calculer le nombre de jours ouvrables dans une période
     */
    private function getJoursOuvrables($dateDebut, $dateFin)
    {
        $count = 0;
        $current = $dateDebut->copy();

        while ($current->lte($dateFin)) {
            // Compter tous les jours sauf dimanche (0)
            if ($current->dayOfWeek !== 0) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }
}
