<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Medecin;
use App\Models\GestionPatient;
use App\Models\Motif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RendezVousController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RendezVous::with(['patient', 'medecin']);

        // Filtres
        if ($request->filled('medecin_id')) {
            $query->where('medecin_id', $request->medecin_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date_rdv', $request->date);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('patient_phone')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->patient_phone . '%');
            });
        }

        // Pour le calendrier
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereYear('date_rdv', $request->year)
                ->whereMonth('date_rdv', $request->month);
        }

        $rendezVous = $query->orderBy('date_rdv', 'desc')
            ->orderBy('heure_rdv', 'desc')
            ->paginate(15);

        $medecins = Medecin::where('statut', 'actif')
            ->orderByRaw("FIELD(fonction, 'Pr', 'Dr', 'Tss', 'SGF', 'IDE')")
            ->orderBy('nom')
            ->get();
        $patients = GestionPatient::all();

        // Données pour le calendrier
        $currentMonth = $request->filled('month') ? $request->month : now()->month;
        $currentYear = $request->filled('year') ? $request->year : now()->year;

        $calendarData = $this->generateCalendarData($currentMonth, $currentYear, $rendezVous);

        return view('rendezvous.index', compact('rendezVous', 'medecins', 'patients', 'calendarData', 'currentMonth', 'currentYear'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $medecins = Medecin::where('statut', 'actif')
            ->orderByRaw("FIELD(fonction, 'Pr', 'Dr', 'Tss', 'SGF', 'IDE')")
            ->orderBy('nom')
            ->get();
        $patients = GestionPatient::all();
        $motifs = Motif::actifs()->orderBy('nom')->get();

        // Récupérer le patient_id depuis la requête (si passé en paramètre)
        $patientId = $request->get('patient_id');

        // Calculer le numéro prévu pour chaque médecin (par jour, tous les numéros utilisés)
        // Utiliser la date de RDV si fournie, sinon la date actuelle
        $dateReference = $request->get('date_rdv') ? \Carbon\Carbon::parse($request->get('date_rdv'))->startOfDay() : now()->startOfDay();
        $numeros_par_medecin = [];
        foreach ($medecins as $medecin) {
            // Récupérer tous les numéros d'entrée utilisés pour ce médecin à cette date
            $numerosCaisses = \App\Models\Caisse::where('medecin_id', $medecin->id)
                ->whereDate('date_examen', $dateReference)
                ->pluck('numero_entre')
                ->toArray();

            $numerosRendezVous = RendezVous::where('medecin_id', $medecin->id)
                ->whereDate('date_rdv', $dateReference)
                ->where('statut', '=', 'confirme')
                ->pluck('numero_entree')
                ->toArray();

            // Fusionner et trier tous les numéros utilisés
            $numerosUtilises = array_merge($numerosCaisses, $numerosRendezVous);
            sort($numerosUtilises);

            // Trouver le prochain numéro disponible
            $prochainNumero = 1;
            foreach ($numerosUtilises as $numero) {
                if ($numero >= $prochainNumero) {
                    $prochainNumero = $numero + 1;
                }
            }

            $numeros_par_medecin[$medecin->id] = $prochainNumero;
        }

        return view('rendezvous.create', compact('medecins', 'patients', 'motifs', 'numeros_par_medecin', 'patientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'date_rdv' => 'required|date',
            'motif' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'date_rdv.date' => 'Le format de date n\'est pas valide.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dateRdv = $request->date_rdv;
        $motif = $request->motif ?: 'premier visite';

        // Le numéro d'entrée sera généré automatiquement par le modèle
        RendezVous::create([
            'patient_id' => $request->patient_id,
            'medecin_id' => $request->medecin_id,
            'date_rdv' => $dateRdv,
            'heure_rdv' => null, // Champ non utilisé
            'motif' => $motif,
            'statut' => 'confirme',
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Redirection en fonction du rôle de l'utilisateur
        $route = Auth::user()->role?->name === 'admin' ? 'admin.rendezvous.index' : 'rendezvous.index';
        return redirect()->route($route)
            ->with('success', 'Rendez-vous créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rendezVous = RendezVous::with(['patient', 'medecin'])->findOrFail($id);
        return view('rendezvous.show', compact('rendezVous'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rendezVous = RendezVous::findOrFail($id);
        $medecins = Medecin::where('statut', 'actif')
            ->orderByRaw("FIELD(fonction, 'Pr', 'Dr', 'Tss', 'SGF', 'IDE')")
            ->orderBy('nom')
            ->get();
        $patients = GestionPatient::all();
        $motifs = Motif::actifs()->orderBy('nom')->get();

        return view('rendezvous.edit', compact('rendezVous', 'medecins', 'patients', 'motifs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rendezVous = RendezVous::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'date_rdv' => 'required|date',
            'motif' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'statut' => 'required|in:confirme,annule',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $motif = $request->motif ?: 'premier visite';

        $rendezVous->update([
            'patient_id' => $request->patient_id,
            'medecin_id' => $request->medecin_id,
            'date_rdv' => $request->date_rdv,
            'motif' => $motif,
            'statut' => $request->statut,
            'notes' => $request->notes,
        ]);

        // Redirection en fonction du rôle de l'utilisateur
        $route = Auth::user()->role?->name === 'admin' ? 'admin.rendezvous.index' : 'rendezvous.index';
        return redirect()->route($route)
            ->with('success', 'Rendez-vous mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rendezVous = RendezVous::findOrFail($id);
        $rendezVous->delete();

        return redirect()->route('rendezvous.index')
            ->with('success', 'Rendez-vous supprimé avec succès.');
    }

    /**
     * Changer le statut d'un rendez-vous
     */
    public function changeStatus(Request $request, $id)
    {
        $rendezVous = RendezVous::findOrFail($id);

        $request->validate([
            'statut' => 'required|in:confirme,annule'
        ]);

        $rendezVous->update([
            'statut' => $request->statut,
            'annulator_id' => $request->statut === 'annule' ? Auth::id() : null
        ]);

        // Redirection en fonction du rôle de l'utilisateur
        $route = Auth::user()->role?->name === 'admin' ? 'admin.rendezvous.show' : 'rendezvous.show';
        return redirect()->route($route, $rendezVous->id)
            ->with('success', 'Statut du rendez-vous mis à jour.');
    }

    /**
     * Générer les données du calendrier
     */
    private function generateCalendarData($month, $year, $rendezVous)
    {
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();
        $startDate = $firstDay->copy()->startOfWeek(Carbon::MONDAY);
        $endDate = $lastDay->copy()->endOfWeek(Carbon::SUNDAY);

        $calendar = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dayRendezVous = $rendezVous->filter(function ($rdv) use ($currentDate) {
                return $rdv->date_rdv->format('Y-m-d') === $currentDate->format('Y-m-d');
            });

            $calendar[] = [
                'date' => $currentDate->copy(),
                'isCurrentMonth' => $currentDate->month === (int)$month,
                'isToday' => $currentDate->isToday(),
                'rendezVous' => $dayRendezVous,
                'count' => $dayRendezVous->count()
            ];

            $currentDate->addDay();
        }

        return $calendar;
    }

    /**
     * API pour récupérer les rendez-vous d'un jour spécifique
     */
    public function getRendezVousByDate(Request $request)
    {
        $date = $request->date;
        $medecinId = $request->medecin_id;

        $query = RendezVous::with(['patient', 'medecin'])
            ->whereDate('date_rdv', $date);

        if ($medecinId) {
            $query->where('medecin_id', $medecinId);
        }

        $rendezVous = $query->orderBy('heure_rdv')->get();

        return response()->json($rendezVous);
    }

    public function getNextNumeroEntreeApi(Request $request)
    {
        $medecinId = $request->get('medecin_id');
        $dateRdv = $request->get('date_rdv');

        if (!$medecinId) {
            return response()->json(['error' => 'Médecin requis'], 400);
        }

        // Utiliser la date de RDV si fournie, sinon la date actuelle
        $dateReference = $dateRdv ? \Carbon\Carbon::parse($dateRdv)->startOfDay() : now()->startOfDay();

        // Récupérer tous les numéros d'entrée utilisés pour ce médecin à cette date
        $numerosCaisses = \App\Models\Caisse::where('medecin_id', $medecinId)
            ->whereDate('date_examen', $dateReference)
            ->pluck('numero_entre')
            ->toArray();

        $numerosRendezVous = RendezVous::where('medecin_id', $medecinId)
            ->whereDate('date_rdv', $dateReference)
            ->where('statut', '=', 'confirme')
            ->pluck('numero_entree')
            ->toArray();

        // Fusionner et trier tous les numéros utilisés
        $numerosUtilises = array_merge($numerosCaisses, $numerosRendezVous);
        sort($numerosUtilises);

        // Trouver le prochain numéro disponible
        $numeroEntree = 1;
        foreach ($numerosUtilises as $numero) {
            if ($numero >= $numeroEntree) {
                $numeroEntree = $numero + 1;
            }
        }

        return response()->json(['numero' => $numeroEntree]);
    }
}
