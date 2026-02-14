<?php

namespace App\Http\Controllers;

use App\Models\Prescripteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrescripteurController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescripteur::query();

        // Filtrage par nom
        if ($request->filled('nom')) {
            $query->where('nom', 'like', '%' . $request->nom . '%');
        }

        // Filtrage par spécialité
        if ($request->filled('specialite')) {
            $query->where('specialite', $request->specialite);
        }

        // Sous-requête pour les stats (Patients Apportés, Revenu Total, Total Examens)
        $statsSubquery = DB::table('caisses')
            ->select('prescripteur_id')
            ->selectRaw('COUNT(DISTINCT gestion_patient_id) as total_patients')
            ->selectRaw('COALESCE(SUM(total), 0) as total_revenu')
            ->selectRaw("COALESCE(SUM(
                CASE
                    WHEN examens_data IS NULL OR examens_data = '[]' OR examens_data = '' THEN 1
                    ELSE GREATEST(1, JSON_LENGTH(examens_data))
                END
            ), 0) as total_examens")
            ->whereNotNull('prescripteur_id')
            ->groupBy('prescripteur_id');

        // Tri : 1) Patients Apportés (desc), 2) Revenu Total (desc), 3) Total Examens (desc)
        $prescripteurs = $query
            ->select('prescripteurs.*')
            ->leftJoinSub($statsSubquery, 'stats', 'prescripteurs.id', '=', 'stats.prescripteur_id')
            ->orderByRaw('COALESCE(stats.total_patients, 0) DESC')
            ->orderByRaw('COALESCE(stats.total_revenu, 0) DESC')
            ->orderByRaw('COALESCE(stats.total_examens, 0) DESC')
            ->paginate(10);
        
        // Récupérer toutes les spécialités uniques existantes (non nulles et non vides)
        $specialites = Prescripteur::whereNotNull('specialite')
            ->where('specialite', '!=', '')
            ->distinct()
            ->pluck('specialite')
            ->sort()
            ->values();

        return view('prescripteurs.index', compact('prescripteurs', 'specialites'));
    }

    public function create()
    {
        return view('prescripteurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'specialite' => 'nullable|string|max:255',
        ]);

        $prescripteur = new Prescripteur();
        $prescripteur->nom = $request->nom;
        $prescripteur->specialite = $request->specialite;
        $prescripteur->save();

        // Redirection selon le rôle
        $userRole = Auth::user()->role?->name ?? 'guest';
        if ($userRole === 'medecin') {
            return redirect()->route('medecin.prescripteurs.index')->with('success', 'Prescripteur ajouté.');
        } elseif ($userRole === 'admin') {
            return redirect()->route('admin.prescripteurs.index')->with('success', 'Prescripteur ajouté.');
        }

        return redirect()->route('prescripteurs.index')->with('success', 'Prescripteur ajouté.');
    }

    public function edit($id)
    {
        $prescripteur = Prescripteur::findOrFail($id);
        $page = request('page', 1); // Récupérer le paramètre page
        return view('prescripteurs.edit', compact('prescripteur', 'page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'specialite' => 'nullable|string|max:255',
        ]);

        $prescripteur = Prescripteur::findOrFail($id);
        $prescripteur->update($request->all());

        // Conserver le paramètre de pagination
        $page = $request->input('return_page', 1);
        return redirect()->route('prescripteurs.index', ['page' => $page])->with('success', 'Prescripteur mis à jour.');
    }
    public function show($id, Request $request)
    {
        // Vérifier les permissions - seul le superadmin peut voir les détails
        $userRole = Auth::user()->role?->name ?? 'guest';
        if (!in_array($userRole, ['superadmin'])) {
            // Rediriger vers la bonne route selon le rôle
            if ($userRole === 'medecin') {
                return redirect()->route('medecin.prescripteurs.index')
                    ->with('error', 'Vous n\'avez pas l\'autorisation d\'accéder à cette page.');
            } elseif ($userRole === 'admin') {
                return redirect()->route('admin.prescripteurs.index')
                    ->with('error', 'Vous n\'avez pas l\'autorisation d\'accéder à cette page.');
            }
            return redirect()->route('prescripteurs.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation d\'accéder à cette page.');
        }
        
        $prescripteur = Prescripteur::findOrFail($id);
        
        // Récupérer toutes les caisses liées à ce prescripteur avec relations
        $query = \App\Models\Caisse::with(['patient', 'examen', 'medecin'])
            ->where('prescripteur_id', $id);
        
        // Filtrage par période
        $period = $request->get('period', 'all');
        
        if ($period === 'day' && $request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } elseif ($period === 'week' && $request->filled('week')) {
            $parts = explode('-W', $request->week);
            if (count($parts) === 2) {
                $start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
                $end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
                $query->whereBetween('created_at', [$start, $end]);
            }
        } elseif ($period === 'month' && $request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->whereYear('created_at', $parts[0])
                    ->whereMonth('created_at', $parts[1]);
            }
        } elseif ($period === 'year' && $request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        
        $caisses = $query->orderBy('created_at', 'desc')->get();
        
        // Calculs statistiques
        $totalPatients = $caisses->pluck('gestion_patient_id')->unique()->count();
        
        // Compter le nombre réel d'examens (en tenant compte de examens_data)
        $totalExamens = $caisses->sum(function($caisse) {
            if (!empty($caisse->examens_data)) {
                // examens_data peut être une string JSON ou déjà un array
                $examensArray = is_string($caisse->examens_data) 
                    ? json_decode($caisse->examens_data, true) 
                    : $caisse->examens_data;
                
                if (is_array($examensArray) && count($examensArray) > 0) {
                    return count($examensArray);
                }
            }
            return 1; // Si pas de examens_data, compter comme 1 examen
        });
        
        $totalRevenu = $caisses->sum('total');
        
        // Patients apportés (avec détails)
        $patients = $caisses->groupBy('gestion_patient_id')->map(function($group) {
            $patient = $group->first()->patient;
            
            // Compter le nombre réel d'examens pour ce patient
            $countExamens = $group->sum(function($caisse) {
                if (!empty($caisse->examens_data)) {
                    // examens_data peut être une string JSON ou déjà un array
                    $examensArray = is_string($caisse->examens_data) 
                        ? json_decode($caisse->examens_data, true) 
                        : $caisse->examens_data;
                    
                    if (is_array($examensArray) && count($examensArray) > 0) {
                        return count($examensArray);
                    }
                }
                return 1;
            });
            
            return [
                'patient' => $patient,
                'examens' => $group,
                'total' => $group->sum('total'),
                'count' => $countExamens
            ];
        })->sortByDesc('total');
        
        return view('prescripteurs.show', compact('prescripteur', 'caisses', 'totalPatients', 'totalExamens', 'totalRevenu', 'patients', 'period'));
    }

    public function print(Request $request)
    {
        $prescripteurs = Prescripteur::orderBy('nom')->get();
        return view('prescripteurs.print', compact('prescripteurs'));
    }

    public function exportPdf()
    {
        $prescripteurs = Prescripteur::orderBy('nom')->get();
        $pdf = \PDF::loadView('prescripteurs.export_pdf', compact('prescripteurs'));
        return $pdf->download('prescripteurs.pdf');
    }

    public function destroy($id)
    {
        $prescripteur = Prescripteur::findOrFail($id);
        $prescripteur->delete();

        return redirect()->route('prescripteurs.index')->with('success', 'Prescripteur supprimé.');
    }
}
