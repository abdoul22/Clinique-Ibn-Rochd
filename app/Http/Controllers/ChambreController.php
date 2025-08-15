<?php

namespace App\Http\Controllers;

use App\Models\Chambre;
use App\Models\Lit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChambreController extends Controller
{
    public function index(Request $request)
    {
        $query = Chambre::with(['lits' => function ($q) {
            $q->orderBy('numero');
        }]);

        // Filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('etage')) {
            $query->where('etage', $request->etage);
        }

        // Suppression du filtre batiment

        if ($request->filled('disponibilite')) {
            if ($request->disponibilite === 'libre') {
                $query->libre();
            } elseif ($request->disponibilite === 'occupee') {
                $query->whereDoesntHave('lits', function ($q) {
                    $q->where('statut', 'libre');
                });
            }
        }

        $chambres = $query->orderBy('batiment')
            ->orderBy('etage')
            ->orderBy('nom')
            ->paginate(15);

        // Statistiques
        $stats = [
            'total' => Chambre::count(),
            'actives' => Chambre::where('statut', 'active')->count(),
            'libres' => Chambre::libre()->count(),
            'occupees' => Chambre::count() - Chambre::libre()->count(),
        ];

        return view('chambres.index', compact('chambres', 'stats'));
    }

    public function create()
    {
        return view('chambres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            // Le champ batiment n'est plus supporté
            'type' => 'required|in:standard,simple,double,suite,VIP',
            'etage' => 'nullable|string|max:50',
            'capacite_lits' => 'required|integer|min:1|max:10',
            'tarif_journalier' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'equipements' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $data = $request->except('batiment');
            $chambre = Chambre::create($data);

            // Créer automatiquement les lits selon la capacité
            for ($i = 1; $i <= $request->capacite_lits; $i++) {
                Lit::create([
                    'numero' => $i,
                    'chambre_id' => $chambre->id,
                    'statut' => 'libre',
                    'type' => 'standard',
                ]);
            }
        });

        return redirect()->route('chambres.index')->with('success', 'Chambre créée avec succès !');
    }

    public function show($id)
    {
        try {
            $chambre = Chambre::findOrFail($id);

            // Charger les lits avec leurs hospitalisations actuelles
            $chambre->load(['lits' => function ($query) {
                $query->with(['hospitalisationActuelle' => function ($q) {
                    $q->with(['patient', 'medecin']);
                }]);
            }]);

            // Statistiques de la chambre
            $stats = [
                'total_lits' => $chambre->lits->count(),
                'lits_libres' => $chambre->lits_libres,
                'lits_occupes' => $chambre->lits_occupes,
                'taux_occupation' => $chambre->taux_occupation,
            ];

            return view('chambres.show', compact('chambre', 'stats'));
        } catch (\Exception $e) {
            Log::error('Erreur dans ChambreController@show: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du chargement de la chambre: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $chambre = Chambre::findOrFail($id);
        return view('chambres.edit', compact('chambre'));
    }

    public function update(Request $request, $id)
    {
        $chambre = Chambre::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:standard,simple,double,suite,VIP',
            'etage' => 'nullable|string|max:50',
            'capacite_lits' => 'required|integer|min:1|max:10',
            'tarif_journalier' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'equipements' => 'nullable|string',
            'statut' => 'required|in:active,inactive,maintenance',
        ]);

        $chambre->update($request->except('batiment'));

        return redirect()->route('chambres.show', $chambre->id)->with('success', 'Chambre mise à jour avec succès !');
    }

    public function destroy($id)
    {
        $chambre = Chambre::findOrFail($id);

        // Vérifier qu'aucun lit n'est occupé
        if ($chambre->lits_occupes > 0) {
            return back()->with('error', 'Impossible de supprimer une chambre avec des lits occupés !');
        }

        $chambre->delete();
        return redirect()->route('chambres.index')->with('success', 'Chambre supprimée avec succès !');
    }

    // API pour obtenir les chambres disponibles
    public function getChambresDisponibles(Request $request)
    {
        $chambres = Chambre::active()
            ->libre()
            ->with(['lits' => function ($q) {
                $q->where('statut', 'libre');
            }])
            ->get()
            ->map(function ($chambre) {
                return [
                    'id' => $chambre->id,
                    'nom' => $chambre->nom_complet,
                    'type' => $chambre->type,
                    'lits_disponibles' => $chambre->lits->count(),
                    'tarif' => $chambre->tarif_journalier,
                ];
            });

        return response()->json($chambres);
    }

    // API pour obtenir les lits disponibles d'une chambre
    public function getLitsDisponibles($id)
    {
        $chambre = Chambre::findOrFail($id);

        $lits = $chambre->lits()
            ->where('statut', 'libre')
            ->get()
            ->map(function ($lit) {
                return [
                    'id' => $lit->id,
                    'numero' => $lit->numero,
                    'nom_complet' => $lit->nom_complet,
                    'type' => $lit->type,
                ];
            });

        return response()->json($lits);
    }
}
