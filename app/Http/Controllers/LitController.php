<?php

namespace App\Http\Controllers;

use App\Models\Lit;
use App\Models\Chambre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LitController extends Controller
{
    public function index(Request $request)
    {
        $query = Lit::with(['chambre', 'hospitalisationActuelle.patient']);

        // Filtres
        if ($request->filled('chambre_id')) {
            $query->where('chambre_id', $request->chambre_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $lits = $query->orderBy('chambre_id')
            ->orderBy('numero')
            ->paginate(15);

        // Statistiques
        $stats = [
            'total' => Lit::count(),
            'libres' => Lit::where('statut', 'libre')->count(),
            'occupes' => Lit::where('statut', 'occupe')->count(),
            'maintenance' => Lit::where('statut', 'maintenance')->count(),
        ];

        $chambres = Chambre::orderBy('nom')->get();

        return view('lits.index', compact('lits', 'stats', 'chambres'));
    }

    public function create()
    {
        $chambres = Chambre::orderBy('nom')->get();
        return view('lits.create', compact('chambres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|integer|min:1',
            'chambre_id' => 'required|exists:chambres,id',
            'type' => 'required|in:standard,medicalise,reanimation',
            'statut' => 'required|in:libre,occupe,maintenance,reserve',
            'description' => 'nullable|string',
        ]);

        // Vérifier que le numéro de lit n'existe pas déjà dans cette chambre
        $existingLit = Lit::where('chambre_id', $request->chambre_id)
            ->where('numero', $request->numero)
            ->first();

        if ($existingLit) {
            return back()->withErrors(['numero' => 'Ce numéro de lit existe déjà dans cette chambre.'])->withInput();
        }

        Lit::create($request->all());

        return redirect()->route('lits.index')->with('success', 'Lit créé avec succès !');
    }

    public function show($id)
    {
        $lit = Lit::findOrFail($id);
        // Charger les relations nécessaires
        $lit->load(['chambre', 'hospitalisationActuelle.patient', 'hospitalisationActuelle.medecin']);
        return view('lits.show', compact('lit'));
    }

    public function edit($id)
    {
        $lit = Lit::findOrFail($id);
        $chambres = Chambre::orderBy('nom')->get();
        return view('lits.edit', compact('lit', 'chambres'));
    }

    public function update(Request $request, $id)
    {
        $lit = Lit::findOrFail($id);

        $request->validate([
            'numero' => 'required|integer|min:1',
            'chambre_id' => 'required|exists:chambres,id',
            'type' => 'required|in:standard,medicalise,reanimation',
            'statut' => 'required|in:libre,occupe,maintenance',
            'description' => 'nullable|string',
        ]);

        // Vérifier que le numéro de lit n'existe pas déjà dans cette chambre (sauf pour ce lit)
        $existingLit = Lit::where('chambre_id', $request->chambre_id)
            ->where('numero', $request->numero)
            ->where('id', '!=', $lit->id)
            ->first();

        if ($existingLit) {
            return back()->withErrors(['numero' => 'Ce numéro de lit existe déjà dans cette chambre.'])->withInput();
        }

        $lit->update($request->all());

        return redirect()->route('lits.show', $lit->id)->with('success', 'Lit mis à jour avec succès !');
    }

    public function destroy($id)
    {
        $lit = Lit::findOrFail($id);

        // Vérifier qu'aucune hospitalisation n'est en cours sur ce lit
        if ($lit->hospitalisationActuelle) {
            return back()->with('error', 'Impossible de supprimer un lit avec une hospitalisation en cours !');
        }

        $lit->delete();
        return redirect()->route('lits.index')->with('success', 'Lit supprimé avec succès !');
    }

    // API pour obtenir les lits disponibles
    public function getLitsDisponibles(Request $request)
    {
        $query = Lit::with('chambre')
            ->where('statut', 'libre');

        if ($request->filled('chambre_id')) {
            $query->where('chambre_id', $request->chambre_id);
        }

        $lits = $query->get()
            ->map(function ($lit) {
                return [
                    'id' => $lit->id,
                    'numero' => $lit->numero,
                    'nom_complet' => $lit->nom_complet,
                    'type' => $lit->type,
                    'chambre' => $lit->chambre ? $lit->chambre->nom : 'Chambre supprimée',
                ];
            });

        return response()->json($lits);
    }
}
