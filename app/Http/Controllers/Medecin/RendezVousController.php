<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RendezVousController extends Controller
{
    /**
     * Liste des rendez-vous du médecin connecté
     */
    public function index(Request $request)
    {
        $medecin = Auth::user()->medecin;

        if (!$medecin) {
            return redirect()->route('login')->with('error', 'Aucun profil médecin associé à votre compte.');
        }

        $query = RendezVous::with(['patient'])
            ->where('medecin_id', $medecin->id);

        // Filtres
        if ($request->filled('date')) {
            $query->whereDate('date_rdv', $request->date);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('patient_search')) {
            $search = $request->patient_search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $rendezVous = $query->orderBy('date_rdv', 'desc')
            ->orderBy('heure_rdv', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('medecin.rendezvous.index', compact('rendezVous', 'medecin'));
    }

    /**
     * Détails d'un rendez-vous
     */
    public function show($id)
    {
        $medecin = Auth::user()->medecin;
        
        $rendezVous = RendezVous::with(['patient.dossierMedical', 'medecin'])
            ->where('medecin_id', $medecin->id)
            ->findOrFail($id);

        $dossierId = $rendezVous->patient->dossierMedical?->id;

        return view('medecin.rendezvous.show', compact('rendezVous', 'medecin', 'dossierId'));
    }
}

