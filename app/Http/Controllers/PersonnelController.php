<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index()
    {
        $personnels = Personnel::latest()->paginate(10);
        return view('personnels.index', compact('personnels'));
    }

    public function create()
    {
        return view('personnels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'fonction' => 'required',
            'salaire' => 'required|numeric',
            'telephone' => 'nullable',
            'adresse' => 'nullable',
        ]);


        Personnel::create($request->all());
        return redirect()->route('personnels.index')->with('success', 'Personnel ajouté.');
    }

    public function show(Personnel $personnel, $id)
    {
        $personnel = Personnel::findorfail($id);

        return view('personnels.show', compact('personnel'));
    }

    public function edit(Personnel $personnel, $id)
    {
        $personnel = Personnel::findorfail($id);
        return view('personnels.edit', compact('personnel'));
    }

    public function update(Request $request, $id)
    {
        $personnel = Personnel::findOrFail($id);
        $request->validate([
            'nom' => 'required',
            'fonction' => 'required',
            'salaire' => 'required|numeric',
            'telephone' => 'nullable',
            'adresse' => 'nullable',
        ]);


        $personnel->update($request->all());
        return redirect()->route('personnels.index')->with('success', 'Personnel mis à jour.');
    }

    public function destroy(Personnel $personnel, $id)
    {
        $personnel = Personnel::findorfail($id);
        $personnel->delete();
        return redirect()->route('personnels.index')->with('success', 'Personnel supprimé.');
    }
}
