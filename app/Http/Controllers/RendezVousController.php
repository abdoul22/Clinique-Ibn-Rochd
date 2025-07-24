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

        $medecins = Medecin::where('statut', 'actif')->get();
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
    public function create()
    {
        $medecins = Medecin::where('statut', 'actif')->get();
        $patients = GestionPatient::all();
        $motifs = Motif::actifs()->orderBy('nom')->get();

        // Calculer le numéro prévu pour chaque médecin (par jour, partagé entre caisses et rendez-vous)
        $today = now()->startOfDay();
        $numeros_par_medecin = [];
        foreach ($medecins as $medecin) {
            // Compter les caisses de ce médecin aujourd'hui
            $countCaisses = \App\Models\Caisse::where('medecin_id', $medecin->id)
                ->whereDate('created_at', $today)
                ->count();

            // Compter les rendez-vous de ce médecin aujourd'hui
            $countRendezVous = RendezVous::where('medecin_id', $medecin->id)
                ->whereDate('created_at', $today)
                ->count();

            // Total des entrées pour ce médecin aujourd'hui
            $totalEntrees = $countCaisses + $countRendezVous;
            $numeros_par_medecin[$medecin->id] = $totalEntrees + 1;
        }

        return view('rendezvous.create', compact('medecins', 'patients', 'motifs', 'numeros_par_medecin'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'date_rdv' => 'required|date|after_or_equal:today',
            'motif' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'date_rdv.after_or_equal' => 'La date du rendez-vous doit être aujourd\'hui ou une date future.',
            'date_rdv.date' => 'Le format de date n\'est pas valide.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dateRdv = $request->date_rdv;

        // Génération du numéro d'entrée spécifique au médecin ET au jour
        // (partagé entre caisses ET rendez-vous)
        $today = now()->startOfDay(); // 00h GMT du jour actuel

        // Compter les caisses de ce médecin aujourd'hui
        $countCaisses = \App\Models\Caisse::where('medecin_id', $request->medecin_id)
            ->whereDate('created_at', $today)
            ->count();

        // Compter les rendez-vous de ce médecin aujourd'hui
        $countRendezVous = RendezVous::where('medecin_id', $request->medecin_id)
            ->whereDate('created_at', $today)
            ->count();

        // Total des entrées pour ce médecin aujourd'hui
        $totalEntrees = $countCaisses + $countRendezVous;
        $numeroEntree = $totalEntrees + 1;

        $motif = $request->motif ?: 'premier visite';

        RendezVous::create([
            'patient_id' => $request->patient_id,
            'medecin_id' => $request->medecin_id,
            'date_rdv' => $dateRdv,
            'heure_rdv' => null, // Champ non utilisé
            'motif' => $motif,
            'statut' => 'confirme',
            'notes' => $request->notes,
            'numero_entree' => $numeroEntree,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('rendezvous.index')
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
        $medecins = Medecin::where('statut', 'actif')->get();
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

        return redirect()->route('rendezvous.index')
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

        $rendezVous->update(['statut' => $request->statut]);

        return redirect()->back()
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
}
