<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Personnel;
use App\Models\Assurance;
use Illuminate\Http\Request;

class CreditController extends Controller
{
public function index()
{
$credits = Credit::with('source')->latest()->get();
return view('credits.index', compact('credits'));
}

public function marquerComme($id, $statut)
{
$credit = Credit::findOrFail($id);

if (!in_array($statut, ['payé', 'partiellement payé','non payé'])) {
return back()->with('error', 'Statut invalide.');
}

$credit->update(['statut' => $statut]);

// Déduction du crédit dans la source
if ($credit->type === 'personnel') {
$personnel = \App\Models\Personnel::find($credit->source_id);
if ($personnel && $statut === 'payé') {
$personnel->decrement('credit', $credit->montant);
}
}

if ($credit->type === 'assurance') {
$assurance = \App\Models\Assurance::find($credit->source_id);
if ($assurance && $statut === 'payé') {
$assurance->decrement('credit', $credit->montant);
}
}

return back()->with('success', "Crédit marqué comme $statut.");
}

    public function payer(Credit $credit)
    {
        return view('credits.payer', compact('credit'));
    }

    public function payerStore(Request $request, Credit $credit)
    {
        $request->validate([
            'montant' => 'required|numeric|min:1|max:' . ($credit->montant - $credit->montant_paye),
        ]);

        $montant = $request->montant;
        $credit->montant_paye += $montant;

        // Mise à jour du statut
        if ($credit->montant_paye >= $credit->montant) {
            $credit->status = 'payé';
        } else {
            $credit->status = 'partiellement payé';
        }

        $credit->save();

        // Déduction du crédit source
        if ($credit->source_type === 'App\\Models\\Personnel') {
            $credit->source->decrement('credit', $montant);
        } elseif ($credit->source_type === 'App\\Models\\Assurance') {
            $credit->source->decrement('credit', $montant);
        }

        return redirect()->route('credits.index')->with('success', 'Paiement enregistré avec succès.');
    }
    
}
