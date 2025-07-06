<?php

namespace App\Http\Controllers;

use App\Models\Motif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MotifController extends Controller
{
    /**
     * Afficher la liste des motifs
     */
    public function index()
    {
        $motifs = Motif::orderBy('nom')->get();
        return view('motifs.index', compact('motifs'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('motifs.create');
    }

    /**
     * Enregistrer un nouveau motif
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:motifs,nom',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Motif::create($request->all());

        return redirect()->route('motifs.index')
            ->with('success', 'Motif ajouté avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        $motif = Motif::findOrFail($id);
        return view('motifs.edit', compact('motif'));
    }

    /**
     * Mettre à jour un motif
     */
    public function update(Request $request, $id)
    {
        $motif = Motif::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:motifs,nom,' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $motif->update($request->all());

        return redirect()->route('motifs.index')
            ->with('success', 'Motif mis à jour avec succès.');
    }

    /**
     * Supprimer un motif
     */
    public function destroy($id)
    {
        $motif = Motif::findOrFail($id);
        $motif->delete();

        return redirect()->route('motifs.index')
            ->with('success', 'Motif supprimé avec succès.');
    }

    /**
     * Activer/Désactiver un motif
     */
    public function toggleStatus($id)
    {
        $motif = Motif::findOrFail($id);
        $motif->update(['actif' => !$motif->actif]);

        $status = $motif->actif ? 'activé' : 'désactivé';
        return redirect()->route('motifs.index')
            ->with('success', "Motif {$status} avec succès.");
    }

    /**
     * API pour récupérer les motifs actifs (pour les selects)
     */
    public function getMotifsActifs()
    {
        $motifs = Motif::actifs()->orderBy('nom')->get(['id', 'nom']);
        return response()->json($motifs);
    }
}
