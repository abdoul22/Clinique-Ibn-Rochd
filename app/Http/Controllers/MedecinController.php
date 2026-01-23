<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\EtatCaisse;
use Illuminate\Http\Request;
use App\Models\Medecin;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;


class MedecinController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $nameFilter = $request->input('name_filter');
        $specialiteFilter = $request->input('specialite_filter');
        $phoneFilter = $request->input('phone_filter');
        $statusFilter = $request->input('status_filter');
        $emailFilter = $request->input('email_filter');

        $query = Medecin::query();

        // Filtre de recherche générale
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('specialite', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        // Filtre par nom/prénom
        if ($nameFilter) {
            $query->where(function ($q) use ($nameFilter) {
                $q->where('nom', 'like', "%{$nameFilter}%")
                    ->orWhere('prenom', 'like', "%{$nameFilter}%");
            });
        }

        // Filtre par spécialité
        if ($specialiteFilter) {
            $query->where('specialite', 'like', "%{$specialiteFilter}%");
        }

        // Filtre par téléphone
        if ($phoneFilter) {
            $query->where('telephone', 'like', "%{$phoneFilter}%");
        }

        // Filtre par statut
        if ($statusFilter) {
            $query->where('statut', $statusFilter);
        }

        // Filtre par email
        if ($emailFilter) {
            $query->where('email', 'like', "%{$emailFilter}%");
        }

        $medecins = $query->orderBy('created_at', 'desc')->paginate(10);

        $viewPath = $this->resolveViewPath('index');
        return view($viewPath, compact('medecins'));
    }

    public function create()
    {
        $viewPath = $this->resolveViewPath('create');
        return view($viewPath);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'fonction' => 'required|in:Pr,Dr,Tss,SGF,IDE,Phr',
            'prenom' => 'required',
            'specialite' => 'required',
            'email' => 'nullable|email|unique:medecins,email',
        ]);

        $medecin = Medecin::create($request->only([
            'nom',
            'fonction',
            'prenom',
            'specialite',
            'telephone',
            'email',
            'adresse',
            'statut',
        ]));

        $role = Auth::user()->role->name;

        if ($medecin) {
            return redirect()->route("{$role}.medecins.index")->with('success', 'Médecin ajouté avec succès.');
        }

        return back()->with('error', 'Erreur lors de la sauvegarde.');
    }
    public function stats($id, Request $request)
    {
        $medecin = Medecin::with('caisses.examen')->findOrFail($id);

        $startDate = $request->input('start_date') ?? now()->subMonth()->toDateString();
        $endDate = $request->input('end_date') ?? now()->toDateString();

        // Convertir en objets Carbon pour une manipulation cohérente
        $startDateCarbon = Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = Carbon::parse($endDate)->endOfDay();

        $examensHier = Caisse::where('medecin_id', $id)
            ->whereDate('date_examen', now()->subDay())
            ->count();

        $examensParJour = Caisse::where('medecin_id', $id)
            ->whereBetween('date_examen', [$startDateCarbon, $endDateCarbon])
            ->selectRaw('DATE(date_examen) as jour, COUNT(*) as total')
            ->groupBy('jour')
            ->orderBy('jour')
            ->pluck('total', 'jour');

        $examensAujourdhui = Caisse::where('medecin_id', $id)
            ->whereDate('date_examen', now()->toDateString())
            ->count();

        // Étendre les dates manquantes dans la période
        $period = CarbonPeriod::create($startDateCarbon, $endDateCarbon);
        $examensParJourComplet = collect();
        foreach ($period as $date) {
            $jour = $date->toDateString();
            $examensParJourComplet[$jour] = $examensParJour[$jour] ?? 0;
        }
        $examensParJour = $examensParJourComplet;

        $partsParJour = EtatCaisse::where('medecin_id', $id)
            ->where('validated', true)
            ->whereBetween('created_at', [$startDateCarbon, $endDateCarbon])
            ->selectRaw('DATE(created_at) as jour, SUM(part_medecin) as total')
            ->groupBy('jour')
            ->orderBy('jour')
            ->pluck('total', 'jour');

        $examensHebdo = Caisse::where('medecin_id', $id)
            ->whereBetween('date_examen', [$startDateCarbon, $endDateCarbon])
            ->selectRaw('YEARWEEK(date_examen, 1) as semaine, COUNT(*) as total')
            ->groupBy('semaine')
            ->orderBy('semaine')
            ->pluck('total', 'semaine');

        $partsParJourComplet = collect();
        foreach ($period as $date) {
            $jour = $date->toDateString();
            $partsParJourComplet[$jour] = $partsParJour[$jour] ?? 0;
        }
        $totalExamens = $examensParJour->sum();

        $partsParJour = $partsParJourComplet;

        $examensMensuels = Caisse::where('medecin_id', $id)
            ->whereBetween('date_examen', [$startDateCarbon, $endDateCarbon])
            ->selectRaw('DATE_FORMAT(date_examen, "%Y-%m") as mois, COUNT(*) as total')
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois');

        $examensAnnuels = Caisse::where('medecin_id', $id)
            ->whereBetween('date_examen', [$startDateCarbon, $endDateCarbon])
            ->selectRaw('YEAR(date_examen) as annee, COUNT(*) as total')
            ->groupBy('annee')
            ->orderBy('annee')
            ->pluck('total', 'annee');

        // Calculs STATIQUES pour les compteurs (indépendants du filtre date)
        $now = Carbon::now();

        $totalJour = Caisse::where('medecin_id', $id)
            ->whereDate('date_examen', $now->toDateString())
            ->count();

        $totalSemaine = Caisse::where('medecin_id', $id)
            ->whereBetween('date_examen', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
            ->count();

        $totalMois = Caisse::where('medecin_id', $id)
            ->whereMonth('date_examen', $now->month)
            ->whereYear('date_examen', $now->year)
            ->count();

        $totalAnnee = Caisse::where('medecin_id', $id)
            ->whereYear('date_examen', $now->year)
            ->count();

        return view('medecins.stats', compact(
            'medecin',
            'examensParJour',
            'partsParJour',
            'examensHebdo',
            'examensMensuels',
            'examensAnnuels',
            'startDate',
            'endDate',
            'totalJour',
            'totalSemaine',
            'totalMois',
            'totalAnnee',
            'examensHier',
            'examensAujourdhui',
            'examensHier',
            'totalExamens'
        ));
    }


    public function statistiques($id)
    {
        $medecin = Medecin::with('caisses', 'etatsCaisse')->findOrFail($id);

        $caisses = $medecin->caisses;

        // Filtrage par période
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        $stats = [
            'jour' => $caisses->where('created_at', '>=', $today),
            'semaine' => $caisses->where('created_at', '>=', $weekStart),
            'mois' => $caisses->where('created_at', '>=', $monthStart),
            'annee' => $caisses->where('created_at', '>=', $yearStart),
        ];

        $examens = [];

        foreach ($stats as $periode => $liste) {
            $examens[$periode] = [
                'nombre_examens' => $liste->count(),
                'total_recette' => $liste->sum('total'),
                'part_medecin_validee' => $medecin->etatsCaisse
                    ->where('validated', true)
                    ->where('created_at', '>=', ${$periode . 'Start'})
                    ->sum('part_medecin'),
            ];
        }

        return view('medecins.stats', compact('medecin', 'examens'));
    }

    public function edit($id)
    {
        $medecin = Medecin::findOrFail($id);
        $page = request('page', 1); // Récupérer le paramètre page
        $viewPath = $this->resolveViewPath('edit');
        return view($viewPath, compact('medecin', 'page'));
    }

    public function update(Request $request, $id)
    {
        $medecin = Medecin::findOrFail($id);

        $request->validate([
            'nom' => 'required',
            'fonction' => 'required|in:Pr,Dr,Tss,SGF,IDE,Phr',
            'prenom' => 'required',
            'specialite' => 'required',
            'email' => 'nullable|email|unique:medecins,email,' . $medecin->id,
        ]);

        $medecin->update($request->only([
            'nom',
            'fonction',
            'prenom',
            'specialite',
            'telephone',
            'email',
            'adresse',
            'statut',
        ]));

        // Conserver le paramètre de pagination
        $page = $request->input('return_page', 1);
        $role = Auth::user()->role->name;
        $routeName = $role . '.medecins.index';
        
        return redirect()->route($routeName, ['page' => $page])->with('success', 'Médecin mis à jour avec succès.');
    }
    public function show($id)
    {
        $medecin = Medecin::with(['caisses', 'caisses.examen', 'user'])->findOrFail($id);
        return view('medecins.show', compact('medecin'));
    }

    public function destroy($id)
    {
        $medecin = Medecin::findOrFail($id);

        if ($medecin->delete()) {
            return redirect()->route(Auth::user()->role->name . '.medecins.index')->with('success', 'Médecin supprimé avec succès.');
        }

        return back()->with('error', 'Erreur lors de la suppression.');
    }

    private function resolveViewPath($view)
    {
        $role = Auth::user()->role->name;

        return match ($role) {
            'superadmin' => "medecins.$view",
            'admin' => "medecins.$view",
            default => "medecins.$view",
        };
    }

    private function resolveRoute($routeName)
    {
        $role = Auth::user()->role->name;

        return match ($role) {
            'superadmin' => "medecins.$routeName",
            'admin' => "medecins.$routeName",
            default => "medecins.$routeName",
        };
    }
}
