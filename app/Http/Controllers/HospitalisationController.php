<?php

namespace App\Http\Controllers;

use App\Models\Hospitalisation;
use App\Models\GestionPatient;
use App\Models\Medecin;
use App\Models\Service;
use Illuminate\Http\Request;

class HospitalisationController extends Controller
{
    public function index()
    {
        $hospitalisations = Hospitalisation::with(['patient', 'medecin', 'service'])->orderByDesc('created_at')->paginate(15);
        return view('hospitalisations.index', compact('hospitalisations'));
    }

    public function create()
    {
        $patients = GestionPatient::all();
        $medecins = Medecin::all();
        $services = Service::all();
        return view('hospitalisations.create', compact('patients', 'medecins', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'service_id' => 'required|exists:services,id',
            'date_entree' => 'required|date',
            'date_sortie' => 'nullable|date|after_or_equal:date_entree',
            'motif' => 'nullable|string',
            'statut' => 'required|in:en cours,terminé,annulé',
            'chambre' => 'nullable|string',
            'lit' => 'nullable|string',
            'montant_total' => 'nullable|numeric',
            'observation' => 'nullable|string',
        ]);
        $hospitalisation = Hospitalisation::create($request->all());
        return redirect()->route('hospitalisations.show', $hospitalisation->id)->with('success', 'Hospitalisation ajoutée !');
    }

    public function show($id)
    {
        $hospitalisation = Hospitalisation::with(['patient', 'medecin', 'service'])->findOrFail($id);
        return view('hospitalisations.show', compact('hospitalisation'));
    }

    public function edit($id)
    {
        $hospitalisation = Hospitalisation::findOrFail($id);
        $patients = GestionPatient::all();
        $medecins = Medecin::all();
        $services = Service::all();
        return view('hospitalisations.edit', compact('hospitalisation', 'patients', 'medecins', 'services'));
    }

    public function update(Request $request, $id)
    {
        $hospitalisation = Hospitalisation::findOrFail($id);
        $request->validate([
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'service_id' => 'required|exists:services,id',
            'date_entree' => 'required|date',
            'date_sortie' => 'nullable|date|after_or_equal:date_entree',
            'motif' => 'nullable|string',
            'statut' => 'required|in:en cours,terminé,annulé',
            'chambre' => 'nullable|string',
            'lit' => 'nullable|string',
            'montant_total' => 'nullable|numeric',
            'observation' => 'nullable|string',
        ]);
        $hospitalisation->update($request->all());
        return redirect()->route('hospitalisations.show', $hospitalisation->id)->with('success', 'Hospitalisation modifiée !');
    }

    public function destroy($id)
    {
        $hospitalisation = Hospitalisation::findOrFail($id);
        $hospitalisation->delete();
        return redirect()->route('hospitalisations.index')->with('success', 'Hospitalisation supprimée !');
    }
}
