<?php

namespace App\Http\Controllers;

use App\Models\Assurance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AssuranceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Assurance::query();

        if ($search) {
            $query->where('nom', 'like', "%{$search}%");
        }

        $assurances = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('assurances.index', compact('assurances'));
    }

    public function create()
    {
        return view('assurances.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:assurances,nom',
            'credit' => 'nullable|numeric|min:0'
        ]);

        // Vérifier si l'assurance existe déjà
        $existingAssurance = Assurance::where('nom', $request->nom)->first();

        if ($existingAssurance) {
            return back()->withErrors(['nom' => 'Une assurance avec ce nom existe déjà.'])->withInput();
        }

        Assurance::create($request->only('nom', 'credit'));

        return redirect()->route('assurances.index')->with('success', 'Assurance ajoutée avec succès.');
    }

    public function show($id)
    {
        $assurance = Assurance::findOrFail($id);
        return view('assurances.show', compact('assurance'));
    }

    public function edit($id)
    {
        $assurance = Assurance::findOrFail($id);
        return view('assurances.edit', compact('assurance'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $assurance = Assurance::findOrFail($id);
        $assurance->update($request->only('nom'));

        return redirect()->route('assurances.index')->with('success', 'Assurance mise à jour.');
    }

    public function destroy($id)
    {
        $assurance = Assurance::findOrFail($id);
        $assurance->delete();

        return redirect()->route('assurances.index')->with('success', 'Assurance supprimée.');
    }

    public function exportPdf()
    {
        $assurances = Assurance::all();
        $pdf = Pdf::loadView('assurances.export_pdf', compact('assurances'));
        return $pdf->download('assurances.pdf');
    }

    public function print()
    {
        $assurances = Assurance::all();
        return view('assurances.print', compact('assurances'));
    }
}
