<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Personnel;
use App\Models\Assurance;
use App\Models\PaymentMode;
use App\Models\ModePaiement;
use App\Models\Depense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

        // Si c'est un crédit personnel, rediriger vers la page de paiement par salaire
        if ($credit->source_type === 'App\\Models\\Personnel') {
            return view('credits.payer-salaire', compact('credit'));
        }

        // Pour les assurances, utiliser l'ancien système
        $modes = \App\Models\ModePaiement::getTypes();
        return view('credits.payer', compact('credit', 'modes'));
    }

    public function payerSalaire(Request $request, $id)
    {
        $credit = Credit::findOrFail($id);

        if ($credit->source_type !== 'App\\Models\\Personnel') {
            return back()->with('error', 'Cette méthode est réservée aux crédits du personnel.');
        }

        $personnel = $credit->source;
        $montantRestant = $credit->montant - $credit->montant_paye;

        if ($montantRestant <= 0) {
            return back()->with('error', 'Ce crédit est déjà entièrement remboursé.');
        }

        $request->validate([
            'montant' => "required|numeric|min:0.01|max:$montantRestant",
        ]);

        $montant = $request->montant;

        // Mettre à jour le crédit
        $credit->montant_paye += $montant;

        if ($credit->montant_paye >= $credit->montant) {
            $credit->status = 'payé';
        } else {
            $credit->status = 'partiellement payé';
        }

        $credit->save();

        // Créer une dépense pour le salaire déduit
        Depense::create([
            'nom' => "Déduction salaire - Crédit personnel : {$personnel->nom}",
            'montant' => $montant,
            'mode_paiement_id' => 'salaire',
            'source' => 'automatique',
            'etat_caisse_id' => null,
        ]);

        // Mettre à jour le crédit du personnel
        $personnel->updateCredit();

        return redirect()->route('credits.index')->with('success', 'Paiement par déduction salaire enregistré avec succès.');
    }

    public function payerStore(Request $request, $id)
    {
        $credit = Credit::findOrFail($id);

        // Si c'est un crédit personnel, utiliser la nouvelle méthode
        if ($credit->source_type === 'App\\Models\\Personnel') {
            return $this->payerSalaire($request, $id);
        }

        // Pour les assurances, utiliser l'ancien système
        $maxAmount = $credit->montant - $credit->montant_paye;
        if ($maxAmount <= 0) {
            return back()->with('error', 'Ce crédit est déjà entièrement remboursé.');
        }

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

        // Créer une nouvelle entrée ModePaiement (entrée réelle)
        \App\Models\ModePaiement::create([
            'type' => $request->mode_paiement_id,
            'montant' => $montant,
            'caisse_id' => null, // Pas de caisse pour les remboursements
        ]);

        // Mise à jour du crédit de la source
        if ($credit->source_type === 'App\\Models\\Assurance') {
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
        $request->validate([
            'source_type' => 'required|in:personnel,assurance',
            'source_id' => 'required',
            'montant' => 'required|numeric|min:1',
        ]);

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

            // Pour les crédits personnel, pas de mode_paiement_id (payé par salaire)
            $modePaiementId = null;
        } else {
            $assurance = \App\Models\Assurance::findOrFail($request->source_id);
            $sourceType = \App\Models\Assurance::class;
            $sourceId = $assurance->id;
            $sourceNom = $assurance->nom;

            // Pour les assurances, pas de mode_paiement_id (payé quand l'assurance rembourse)
            $modePaiementId = null;
        }

        Log::info('Création crédit', [
            'montant' => $request->montant,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'source_nom' => $sourceNom,
            'mode_paiement' => $modePaiementId,
        ]);

        Credit::create([
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'montant' => $request->montant,
            'status' => 'non payé',
            'statut' => 'non payé',
            'montant_paye' => 0,
            'mode_paiement_id' => $modePaiementId
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
