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

        // Pour le calendrier
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereYear('date_rdv', $request->year)
                ->whereMonth('date_rdv', $request->month);
        }

        $rendezVous = $query->orderBy('date_rdv', 'asc')
            ->orderBy('heure_rdv', 'asc')
            ->get();

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

        return view('rendezvous.create', compact('medecins', 'patients', 'motifs'));
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
            'heure_rdv' => 'required|date_format:H:i',
            'motif' => 'required|string|max:255',
            'duree_consultation' => 'nullable|integer|min:15|max:180',
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

        // Vérifier si le créneau est disponible
        $dateRdv = $request->date_rdv;
        $heureRdv = $request->heure_rdv;
        $duree = (int)($request->duree_consultation ?? 30);
        $medecinId = $request->medecin_id;

        $heureFin = Carbon::parse($heureRdv)->addMinutes($duree)->format('H:i:s');

        $conflict = RendezVous::where('medecin_id', $medecinId)
            ->where('date_rdv', $dateRdv)
            ->where('statut', '!=', 'annule')
            ->where(function ($query) use ($heureRdv, $heureFin) {
                // Vérifier si le début du nouveau RDV chevauche un RDV existant
                $query->whereBetween('heure_rdv', [$heureRdv, $heureFin])
                    // Ou si la fin d'un RDV existant chevauche le nouveau RDV
                    ->orWhere(function ($subQuery) use ($heureRdv, $heureFin) {
                        $subQuery->whereRaw('TIME_TO_SEC(heure_rdv) + (duree_consultation * 60) > TIME_TO_SEC(?)', [$heureRdv])
                            ->where('heure_rdv', '<', $heureFin);
                    });
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withErrors(['heure_rdv' => 'Ce créneau n\'est pas disponible pour ce médecin.'])
                ->withInput();
        }

        RendezVous::create($request->all());

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
            'heure_rdv' => 'required|date_format:H:i',
            'motif' => 'required|string|max:255',
            'duree_consultation' => 'nullable|integer|min:15|max:180',
            'notes' => 'nullable|string',
            'statut' => 'required|in:en_attente,confirme,annule,termine',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier les conflits (exclure le rendez-vous actuel)
        $dateRdv = $request->date_rdv;
        $heureRdv = $request->heure_rdv;
        $duree = (int)($request->duree_consultation ?? 30);
        $medecinId = $request->medecin_id;

        $heureFin = Carbon::parse($heureRdv)->addMinutes($duree)->format('H:i:s');

        $conflict = RendezVous::where('medecin_id', $medecinId)
            ->where('date_rdv', $dateRdv)
            ->where('id', '!=', $rendezVous->id)
            ->where('statut', '!=', 'annule')
            ->where(function ($query) use ($heureRdv, $heureFin) {
                // Vérifier si le début du nouveau RDV chevauche un RDV existant
                $query->whereBetween('heure_rdv', [$heureRdv, $heureFin])
                    // Ou si la fin d'un RDV existant chevauche le nouveau RDV
                    ->orWhere(function ($subQuery) use ($heureRdv, $heureFin) {
                        $subQuery->whereRaw('TIME_TO_SEC(heure_rdv) + (duree_consultation * 60) > TIME_TO_SEC(?)', [$heureRdv])
                            ->where('heure_rdv', '<', $heureFin);
                    });
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withErrors(['heure_rdv' => 'Ce créneau n\'est pas disponible pour ce médecin.'])
                ->withInput();
        }

        $rendezVous->update($request->all());

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
            'statut' => 'required|in:en_attente,confirme,annule,termine'
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
