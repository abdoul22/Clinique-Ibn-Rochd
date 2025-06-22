<?php

namespace App\Http\Controllers;

use App\Models\Prescripteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescripteurController extends Controller
{
    public function index()
    {
        $prescripteurs = Prescripteur::orderBy('created_at', 'desc')->paginate(10);
        return view('prescripteurs.index', compact('prescripteurs'));
    }

    public function create()
    {
        return view('prescripteurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'specialite' => 'nullable|string|max:255',
        ]);

        $prescripteur = new Prescripteur();
        $prescripteur->nom = $request->nom;
        $prescripteur->specialite = $request->specialite;
        $prescripteur->save();

        return redirect()->route('prescripteurs.index')->with('success', 'Prescripteur ajouté.');
    }

    public function edit($id)
    {
        $prescripteur = Prescripteur::findOrFail($id);
        return view('prescripteurs.edit', compact('prescripteur'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'specialite' => 'nullable|string|max:255',
        ]);

        $prescripteur = Prescripteur::findOrFail($id);
        $prescripteur->update($request->all());

        return redirect()->route('prescripteurs.index')->with('success', 'Prescripteur mis à jour.');
    }
    public function show($id)
    {
        $prescripteur = Prescripteur::findOrFail($id);
        return view('prescripteurs.show', compact('prescripteur'));
    }

    public function destroy($id)
    {
        $prescripteur = Prescripteur::findOrFail($id);
        $prescripteur->delete();

        return redirect()->route('prescripteurs.index')->with('success', 'Prescripteur supprimé.');
    }
}
