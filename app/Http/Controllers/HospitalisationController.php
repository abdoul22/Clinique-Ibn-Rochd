<?php

namespace App\Http\Controllers;

use App\Models\Hospitalisation;
use App\Models\GestionPatient;
use App\Models\Medecin;
use App\Models\Service;
use App\Models\Chambre;
use App\Models\Lit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HospitalisationController extends Controller
{
    public function index()
    {
        $hospitalisations = Hospitalisation::with(['patient', 'medecin', 'service', 'lit.chambre'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('hospitalisations.index', compact('hospitalisations'));
    }

    public function create()
    {
        $patients = GestionPatient::all();
        $medecins = Medecin::all();
        $services = Service::all();
        $chambres = Chambre::active()->with(['lits' => function ($q) {
            $q->where('statut', 'libre')->orderBy('numero');
        }])->get();

        // Préparer les données des lits pour le JavaScript
        $litsParChambre = [];
        foreach ($chambres as $chambre) {
            $litsParChambre[$chambre->id] = $chambre->lits->map(function ($lit) use ($chambre) {
                return [
                    'id' => $lit->id,
                    'numero' => $lit->numero,
                    'nom_complet' => $chambre->nom . ' - Lit ' . $lit->numero
                ];
            })->toArray();
        }

        return view('hospitalisations.create', compact('patients', 'medecins', 'services', 'chambres', 'litsParChambre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'service_id' => 'required|exists:services,id',
            'lit_id' => 'required|exists:lits,id',
            'date_entree' => 'required|date',
            'date_sortie' => 'nullable|date|after_or_equal:date_entree',
            'motif' => 'nullable|string',
            'statut' => 'required|in:en cours,terminé,annulé',
            'montant_total' => 'nullable|numeric',
            'observation' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            // Vérifier que le lit est disponible
            $lit = Lit::findOrFail($request->lit_id);
            if (!$lit->est_libre) {
                throw new \Exception('Ce lit n\'est pas disponible.');
            }

            // Créer l'hospitalisation
            $hospitalisation = Hospitalisation::create($request->all());

            // Marquer le lit comme occupé
            $lit->occuper();
        });

        return redirect()->route('hospitalisations.index')->with('success', 'Hospitalisation ajoutée !');
    }

    public function show($id)
    {
        $hospitalisation = Hospitalisation::with(['patient', 'medecin', 'service', 'lit.chambre'])
            ->findOrFail($id);
        return view('hospitalisations.show', compact('hospitalisation'));
    }

    public function edit($id)
    {
        $hospitalisation = Hospitalisation::with(['lit.chambre'])->findOrFail($id);
        $patients = GestionPatient::all();
        $medecins = Medecin::all();
        $services = Service::all();
        $chambres = Chambre::active()->with(['lits'])->get();

        // Préparer les données des lits pour le JavaScript
        $litsParChambre = [];
        foreach ($chambres as $chambre) {
            $litsParChambre[$chambre->id] = $chambre->lits->map(function ($lit) use ($chambre) {
                return [
                    'id' => $lit->id,
                    'numero' => $lit->numero,
                    'nom_complet' => $chambre->nom . ' - Lit ' . $lit->numero
                ];
            })->toArray();
        }

        return view('hospitalisations.edit', compact('hospitalisation', 'patients', 'medecins', 'services', 'chambres', 'litsParChambre'));
    }

    public function update(Request $request, $id)
    {
        $hospitalisation = Hospitalisation::findOrFail($id);

        $request->validate([
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'service_id' => 'required|exists:services,id',
            'lit_id' => 'required|exists:lits,id',
            'date_entree' => 'required|date',
            'date_sortie' => 'nullable|date|after_or_equal:date_entree',
            'motif' => 'nullable|string',
            'statut' => 'required|in:en cours,terminé,annulé',
            'montant_total' => 'nullable|numeric',
            'observation' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $hospitalisation) {
            $ancienLitId = $hospitalisation->lit_id;
            $nouveauLitId = $request->lit_id;

            // Si changement de lit
            if ($ancienLitId != $nouveauLitId) {
                // Libérer l'ancien lit
                if ($ancienLitId) {
                    $ancienLit = Lit::find($ancienLitId);
                    $ancienLit->liberer();
                }

                // Vérifier que le nouveau lit est disponible
                $nouveauLit = Lit::findOrFail($nouveauLitId);
                if (!$nouveauLit->est_libre) {
                    throw new \Exception('Ce lit n\'est pas disponible.');
                }

                // Occuper le nouveau lit
                $nouveauLit->occuper();
            }

            // Mettre à jour l'hospitalisation
            $hospitalisation->update($request->all());

            // Si l'hospitalisation est terminée ou annulée, libérer le lit
            if (in_array($request->statut, ['terminé', 'annulé']) && $nouveauLitId) {
                $lit = Lit::find($nouveauLitId);
                $lit->liberer();
            }
        });

        return redirect()->route('hospitalisations.show', $hospitalisation->id)->with('success', 'Hospitalisation modifiée !');
    }

    public function destroy($id)
    {
        $hospitalisation = Hospitalisation::findOrFail($id);

        DB::transaction(function () use ($hospitalisation) {
            // Libérer le lit si il y en a un
            if ($hospitalisation->lit_id) {
                $lit = Lit::find($hospitalisation->lit_id);
                $lit->liberer();
            }

            $hospitalisation->delete();
        });

        return redirect()->route('hospitalisations.index')->with('success', 'Hospitalisation supprimée !');
    }

    // API pour obtenir les lits disponibles d'une chambre
    public function getLitsDisponibles(Request $request)
    {
        $chambreId = $request->chambre_id;
        $lits = Lit::where('chambre_id', $chambreId)
            ->where('statut', 'libre')
            ->get()
            ->map(function ($lit) {
                return [
                    'id' => $lit->id,
                    'numero' => $lit->numero,
                    'nom_complet' => $lit->nom_complet,
                ];
            });

        return response()->json($lits);
    }
}
