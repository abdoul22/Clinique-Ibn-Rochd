<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Depense::query();

        if ($request->has('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        if ($request->has('source') && in_array($request->source, ['manuelle', 'automatique'])) {
            $query->where('source', $request->source);
        }

        $depenses = $query->latest()->paginate(10);

        return view('depenses.index', compact('depenses'));
    }

    public function create()
    {
        $modes = ['espèces', 'bankily', 'masrivi', 'sedad'];
        return view('depenses.create', compact('modes')); 
    }


    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'montant' => 'required|string|max:255',
            'mode_paiement_id' => 'required|exists:mode_paiements,id',
        ]);

        if (str_contains(request('nom'), 'Part médecin')) {
            abort(403, 'Création manuelle des parts médecin interdite.');
        }

        Depense::create([
            'nom' => $request->nom,
            'montant' => $request->montant,
            'mode_paiement_id' => $request->mode_paiement_id,
            'source' => 'manuelle', // ou autre
        ]);
        return redirect()->route('depenses.index')->with('success', 'Dépense ajoutée avec succès.');
    }

    public function show($id)
    {
        $depense = Depense::findOrFail($id);
        return view('depenses.show', compact('depense'));
    }

    public function edit($id)
    {
        $depense = Depense::findOrFail($id);
        return view('depenses.edit', compact('depense'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'montant' => 'required|string|max:255',
        ]);

        $depense = Depense::findOrFail($id);
        $depense->update($request->all());
        return redirect()->route('depenses.index')->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $depense = Depense::findOrFail($id);
        $depense->delete();
        return redirect()->route('depenses.index')->with('success', 'Dépense supprimée.');
    }

    public function exportPdf()
    {
        $depenses = Depense::all();
        $pdf = Pdf::loadView('depenses.export_pdf', compact('depenses'));
        return $pdf->download('depenses.pdf');
    }

    public function print()
    {
        $depenses = Depense::all();
        return view('depenses.print', compact('depenses'));
    }
}
