<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Personnel;
use App\Models\Assurance;
use App\Models\PaymentMode;
use App\Models\ModePaiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status'); // 'payé', 'partiellement payé', 'non payé' ou null

        $creditsPersonnel = Credit::where('source_type', \App\Models\Personnel::class)
            ->with('source')
            ->latest()
            ->paginate(10, ['*'], 'personnels');

        $creditsAssurance = Credit::where('source_type', \App\Models\Assurance::class)
            ->with('source')
            ->latest()
            ->paginate(10, ['*'], 'assurances');

        return view('credits.index', compact('creditsPersonnel', 'creditsAssurance'));
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
        $modes = \App\Models\ModePaiement::getTypes();
        return view('credits.payer', compact('credit', 'modes'));
    }


    public function payerStore(Request $request, $id)
    {
        $credit = Credit::findOrFail($id);

        // Validation du montant
        $maxAmount = $credit->montant - $credit->montant_paye;
        if ($maxAmount <= 0) {
            return back()->with('error', 'Ce crédit est déjà entièrement remboursé.');
        }
        // Récupérer la caisse courante (à adapter selon ta logique)
        $caisse = \App\Models\Caisse::latest()->first(); // ou la caisse de l'utilisateur connecté, etc.

        $modesDisponibles = \App\Models\ModePaiement::getTypes();
        $modesString = implode(',', $modesDisponibles);

        $request->validate([
            'montant' => "required|numeric|min:0.01|max:$maxAmount",
            'mode_paiement_id' => "required|string|in:$modesString",
        ]);

        $montant = $request->montant;
        $credit->montant_paye += $montant;

        // Mise à jour du statut
        if ($credit->montant_paye >= $credit->montant) {
            $credit->status = 'payé';
        } else {
            $credit->status = 'partiellement payé';
        }
        $credit->mode_paiement_id = $request->mode_paiement_id;
        $credit->save();

        // AJOUT : Créer une nouvelle entrée ModePaiement (entrée réelle)
        \App\Models\ModePaiement::create([
            'type' => $request->mode_paiement_id,
            'montant' => $montant,
            'caisse_id' => $caisse ? $caisse->id : 1,
            // 'caisse_id' => null, // ou une référence si besoin
        ]);
        // FIN AJOUT

        // SUPPRIMER l'ancien increment (sinon double comptage)
        // $modePaiement = \App\Models\ModePaiement::where('type', $request->mode_paiement_id)->latest()->first();
        // if ($modePaiement) {
        //     $modePaiement->increment('montant', $montant);
        // }

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
        foreach ($personnels as $personnel) {
            $personnel->updateCredit();
        }
        $assurances = \App\Models\Assurance::all();
        foreach ($assurances as $assurance) {
            $assurance->updateCredit();
        }
        $modes = \App\Models\ModePaiement::getTypes();
        return view('credits.create', compact('personnels', 'assurances', 'modes'));
    }

    public function store(Request $request)
    {
        $modesDisponibles = \App\Models\ModePaiement::getTypes();
        $modesString = implode(',', $modesDisponibles);

        $request->validate([
            'source_type' => 'required|in:personnel,assurance',
            'source_id' => 'required',
            'montant' => 'required|numeric|min:1',
            'mode_paiement_id' => "required|string|in:$modesString",
        ]);

        // Trouver le ModePaiement correspondant au type choisi
        $modePaiement = \App\Models\ModePaiement::where('type', $request->mode_paiement_id)->latest()->first();
        if ($modePaiement && $modePaiement->montant < $request->montant) {
            return back()->withErrors([
                'mode_paiement_id' => "Fonds insuffisants dans le mode de paiement {$request->mode_paiement_id}. Solde disponible : {$modePaiement->montant} MRU"
            ]);
        }
        if ($modePaiement) {
            $modePaiement->decrement('montant', $request->montant);
        }

        if ($request->source_type === 'personnel') {
            $personnel = Personnel::findOrFail($request->source_id);

            // Utiliser la nouvelle méthode de validation
            if (!$personnel->peutPrendreCredit($request->montant)) {
                $montantMax = $personnel->montant_max_credit;
                return back()->withErrors([
                    'montant' => "Ce crédit dépasse le montant maximum autorisé. Montant maximum : {$montantMax} MRU (Salaire: {$personnel->salaire} MRU - Crédit actuel: {$personnel->credit} MRU)"
                ]);
            }

            $sourceType = \App\Models\Personnel::class;
            $sourceId = $personnel->id;
            $sourceNom = $personnel->nom;
        } else {
            $assurance = \App\Models\Assurance::findOrFail($request->source_id);
            $sourceType = \App\Models\Assurance::class;
            $sourceId = $assurance->id;
            $sourceNom = $assurance->nom;
        }

        Log::info('Création crédit', [
            'montant' => $request->montant,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'source_nom' => $sourceNom,
            'mode_paiement' => $request->mode_paiement_id,
        ]);

        Credit::create([
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'montant' => $request->montant,
            'status' => 'non payé',
            'statut' => 'non payé',
            'montant_paye' => 0,
            'mode_paiement_id' => $request->mode_paiement_id
        ]);

        // Mettre à jour le crédit de la source
        if ($sourceType === \App\Models\Personnel::class) {
            $personnel->updateCredit();
        } elseif ($sourceType === \App\Models\Assurance::class) {
            $assurance->updateCredit();
        }

        return redirect()->route('credits.index')->with('success', 'Crédit ajouté avec succès.');
    }
}
