<?php

namespace App\Http\Controllers;

use App\Models\RecapitulatifOperateur;
use App\Models\Caisse;
use App\Models\Medecin;
use App\Models\Service;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class RecapitulatifOperateurController extends Controller
{
    public function index()
    {
        $recapOperateurs = \App\Models\Caisse::with(['medecin', 'service', 'examen'])
            ->select([
                'medecin_id',
                'service_id',
                'examen_id',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(total) as recettes'),
                DB::raw('DATE(CONVERT_TZ(date_examen, "+00:00", "+00:00")) as jour'),
                DB::raw('MAX(examens.tarif) as tarif'),
                DB::raw('SUM(examens.part_medecin) as part_medecin'),
                DB::raw('SUM(examens.part_cabinet) as part_clinique')
            ])
            ->join('examens', 'caisses.examen_id', '=', 'examens.id')
            ->groupBy('medecin_id', 'service_id', 'examen_id', 'jour')
            ->orderBy('jour', 'desc')
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
        $recaps = Caisse::with(['medecin', 'service', 'examen'])
            ->select([
                'medecin_id',
                'service_id',
                'examen_id',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(total) as recettes'),
                DB::raw('MAX(date_examen) as date'),
                DB::raw('MAX(examens.tarif) as tarif'),
                DB::raw('SUM(examens.part_medecin) as part_medecin'),
                DB::raw('SUM(examens.part_cabinet) as part_clinique')
            ])
            ->join('examens', 'caisses.examen_id', '=', 'examens.id')
            ->groupBy('medecin_id', 'service_id', 'examen_id')
            ->orderBy('date', 'desc')
            ->get();

        $pdf = PDF::loadView('recapitulatif_operateurs.export_pdf', compact('recaps'));
        return $pdf->download('recapitulatif_operateurs.pdf');
    }

    public function print()
    {
        $recaps = Caisse::with(['medecin', 'service', 'examen'])
            ->select([
                'medecin_id',
                'service_id',
                'examen_id',
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(total) as recettes'),
                DB::raw('MAX(date_examen) as date'),
                DB::raw('MAX(examens.tarif) as tarif'),
                DB::raw('SUM(examens.part_medecin) as part_medecin'),
                DB::raw('SUM(examens.part_cabinet) as part_clinique')
            ])
            ->join('examens', 'caisses.examen_id', '=', 'examens.id')
            ->groupBy('medecin_id', 'service_id', 'examen_id')
            ->orderBy('date', 'desc')
            ->get();

        return view('recapitulatif_operateurs.print', compact('recaps'));
    }
}
