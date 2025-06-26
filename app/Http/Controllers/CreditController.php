<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Personnel;
use App\Models\Assurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreditController extends Controller
{
    public function index()
    {
        $credits = Credit::with('source')->latest()->get();

        $creditsPersonnel = $credits->filter(fn($credit) => $credit->source_type === Personnel::class);
        $creditsAssurance = $credits->filter(fn($credit) => $credit->source_type === Assurance::class);

        return view('credits.index', compact('credits', 'creditsPersonnel', 'creditsAssurance'));
    }



    public function marquerComme($id, $statut)
    {
        $credit = Credit::findOrFail($id);

        if (!in_array($statut, ['payé', 'partiellement payé', 'non payé'])) {
            return back()->with('error', 'Statut invalide.');
        }

        $credit->status = $statut;
        $credit->save(); // Cela mettra aussi à jour "statut" via boot()

        if ($credit->source_type === 'App\\Models\\Personnel') {
            $credit->source->updateCredit();
        }

        if ($credit->source_type === 'App\\Models\\Assurance' && method_exists($credit->source, 'updateCredit')) {
            $credit->source->updateCredit();
        }

        return back()->with('success', "Crédit marqué comme $statut.");
    }


    public function payer($id)
    {
        $credit = Credit::findOrFail($id);
        return view('credits.payer', compact('credit'));
    }


    public function payerStore(Request $request, $id)
    {
        // On récupère manuellement le crédit avec findOrFail
        // $credit = Credit::findOrFail($id);

        $credit = Credit::findOrFail($id);
        // Validation du montant
        $maxAmount = $credit->montant - $credit->montant_paye;
        $request->validate([
            'montant' => "required|numeric|min:0.01|max:$maxAmount",
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

        // Déduction dans la source (personnel ou assurance)
        if ($credit->source_type === 'App\\Models\\Personnel') {
            $credit->source->decrement('credit', $montant);
        } elseif ($credit->source_type === 'App\\Models\\Assurance') {
            $credit->source->decrement('credit', $montant);
        }
        // Mise à jour du crédit de la source
        if ($credit->source_type === 'App\\Models\\Personnel') {
            $credit->source->updateCredit();
        }
        return redirect()->route('credits.index')->with('success', 'Paiement enregistré avec succès.');
    }

    public function create()
    {
        $personnels = Personnel::all();
        return view('credits.create', compact('personnels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'source_id' => 'required|exists:personnels,id',
            'montant' => 'required|numeric|min:1',
        ]);
        $personnel = Personnel::findOrFail($request->source_id);

        if ($request->montant > $personnel->salaire) {
            return back()->withErrors(['montant' => 'Le crédit dépasse le salaire du personnel.']);
        }
        $creditActuel = $personnel->credits()->sum('montant') - $personnel->credits()->sum('montant_paye');
        $soldeDispo = $personnel->salaire - $creditActuel;

        if ($request->montant > $soldeDispo) {
            return back()->withErrors([
                'montant' => "Ce crédit dépasse le solde disponible de {$soldeDispo} MRU sur le salaire de {$personnel->nom}."
            ]);
        }
        // $personnel->save();
        Log::info('Création crédit', [
            'montant' => $request->montant,
            'personnel' => $personnel->id,
        ]);

        Credit::create([
            'source_type' => \App\Models\Personnel::class,
            'source_id' => $request->source_id,
            'montant' => $request->montant,
            'status' => 'non payé',
            'statut' => 'non payé',
            'montant_paye' => 0,
        ]);
        $personnel->updateCredit();

        $personnel->updateCredit(); // met à jour le champ credit réel
        return redirect()->route('credits.index')->with('success', 'Crédit personnel ajouté avec succès.');
    }
}
