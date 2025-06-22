<?php

namespace App\Http\Controllers;

use App\Models\RecapitulatifOperateur;
use App\Models\Medecin;
use App\Models\Service;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RecapitulatifOperateurController extends Controller
{
    public function index()
    {
        $recapOperateurs = RecapitulatifOperateur::with(['medecin', 'service'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('recapitulatif_operateurs.index', compact('recapOperateurs'));
    }

    public function create()
    {
        $medecins = Medecin::all();
        $services = Service::all();
        return view('recapitulatif_operateurs.create', compact('medecins', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'service_id' => 'required|exists:services,id',
            'nombre' => 'required|integer',
            'tarif' => 'required|numeric',
            'recettes' => 'required|numeric',
            'part_medecin' => 'required|numeric',
            'part_clinique' => 'required|numeric',
            'date' => 'required|date',
        ]);

        RecapitulatifOperateur::create($request->all());

        return redirect()->route('recapitulatif-operateurs.index')->with('success', 'Récapitulatif ajouté.');
    }

    public function edit($id)
    {
        $recap = RecapitulatifOperateur::findOrFail($id);
        $medecins = Medecin::all();
        $services = Service::all();

        return view('recapitulatif_operateurs.edit', compact('recap', 'medecins', 'services'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'service_id' => 'required|exists:services,id',
            'nombre' => 'required|integer',
            'tarif' => 'required|numeric',
            'recettes' => 'required|numeric',
            'part_medecin' => 'required|numeric',
            'part_clinique' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $recap = RecapitulatifOperateur::findOrFail($id);
        $recap->update($request->all());

        return redirect()->route('recapitulatif-operateurs.index')->with('success', 'Récapitulatif mis à jour.');
    }

    public function show($id)
    {
        $recap = RecapitulatifOperateur::with(['medecin', 'service'])->findOrFail($id);
        return view('recapitulatif_operateurs.show', compact('recap'));
    }

    public function destroy($id)
    {
        $recap = RecapitulatifOperateur::findOrFail($id);
        $recap->delete();

        return redirect()->route('recapitulatif-operateurs.index')->with('success', 'Récapitulatif supprimé.');
    }

    public function exportPdf()
    {
        $recaps = RecapitulatifOperateur::with(['medecin', 'service'])->get();
        $pdf = PDF::loadView('recapitulatif_operateurs.export_pdf', compact('recaps'));
        return $pdf->download('recapitulatif_operateurs.pdf');
    }

    public function print()
    {
        $recaps = RecapitulatifOperateur::with(['medecin', 'service'])->get();
        return view('recapitulatif_operateurs.print', compact('recaps'));
    }
}
