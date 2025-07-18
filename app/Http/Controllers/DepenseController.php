<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\PaymentMode;
use App\Models\ModePaiement;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        $query = Depense::with(['modePaiement', 'credit']);

        // Exclure les crédits personnel des dépenses (ils sont payés par déduction salaire)
        $query->where(function ($q) {
            $q->whereNull('credit_id')
                ->orWhereHas('credit', function ($creditQuery) {
                    $creditQuery->where('source_type', '!=', \App\Models\Personnel::class);
                });
        });

        // Filtrage par période
        if ($period === 'day' && $date) {
            $query->whereDate('created_at', $date);
        } elseif ($period === 'week' && $week) {
            $parts = explode('-W', $week);
            if (count($parts) === 2) {
                $yearW = (int)$parts[0];
                $weekW = (int)$parts[1];
                $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            }
        } elseif ($period === 'month' && $month) {
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                $yearM = (int)$parts[0];
                $monthM = (int)$parts[1];
                $query->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
            }
        } elseif ($period === 'year' && $year) {
            $query->whereYear('created_at', $year);
        } elseif ($period === 'range' && $dateStart && $dateEnd) {
            $query->whereBetween('created_at', [$dateStart, $dateEnd]);
        }

        if ($request->has('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        if ($request->has('source') && in_array($request->source, ['manuelle', 'automatique', 'part_medecin'])) {
            if ($request->source === 'part_medecin') {
                $query->where('nom', 'like', '%Part médecin%');
            } else {
                $query->where('source', $request->source);
            }
        }

        if ($request->has('mode_paiement') && in_array($request->mode_paiement, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
            $query->where('mode_paiement_id', $request->mode_paiement);
        }

        $depenses = $query->latest()->paginate(10);

        // Calculer le total des dépenses (excluant les crédits personnel et assurance)
        $totalDepenses = $query->sum('montant');

        return view('depenses.index', ['depenses' => $depenses]);
    }

    public function create()
    {
        $modes = \App\Models\ModePaiement::getTypes();
        return view('depenses.create', compact('modes'));
    }


    public function store(Request $request)
    {
        $modesDisponibles = \App\Models\ModePaiement::getTypes();
        $modesString = implode(',', $modesDisponibles);

        $request->validate([
            'nom' => 'required|string|max:255',
            'montant' => 'required|string|max:255',
            'mode_paiement_id' => "required|string|in:$modesString",
        ]);

        if (str_contains(request('nom'), 'Part médecin')) {
            abort(403, 'Création manuelle des parts médecin interdite.');
        }

        // Vérification du solde du mode de paiement
        $modePaiement = \App\Models\ModePaiement::where('type', $request->mode_paiement_id)->latest()->first();
        if ($modePaiement && $modePaiement->montant < $request->montant) {
            return back()->withErrors([
                'mode_paiement_id' => "Fonds insuffisants dans le mode de paiement {$request->mode_paiement_id}. Solde disponible : {$modePaiement->montant} MRU"
            ]);
        }

        // Vérification du solde global de la caisse
        $soldeCaisse = \App\Models\Caisse::sum('total');
        $totalDepenses = \App\Models\Depense::sum('montant');
        $soldeDisponible = $soldeCaisse - $totalDepenses;
        if ($request->montant > $soldeDisponible) {
            return back()->withErrors([
                'montant' => "Le montant de la dépense ({$request->montant} MRU) dépasse le solde disponible de la caisse ({$soldeDisponible} MRU). Dépense refusée."
            ]);
        }

        if ($modePaiement) {
            $modePaiement->decrement('montant', $request->montant);
        }

        Depense::create([
            'nom' => $request->nom,
            'montant' => $request->montant,
            'mode_paiement_id' => $request->mode_paiement_id,
            'source' => 'manuelle',
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
