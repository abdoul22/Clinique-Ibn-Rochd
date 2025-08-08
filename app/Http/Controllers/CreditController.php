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
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $type = $request->input('type'); // 'personnel', 'assurance' ou null

        // Query de base pour les crédits personnel
        $queryPersonnel = Credit::where('source_type', \App\Models\Personnel::class)
            ->with('source');

        // Query de base pour les crédits assurance
        $queryAssurance = Credit::where('source_type', \App\Models\Assurance::class)
            ->with('source');

        // Appliquer le filtre de statut
        if ($status) {
            $queryPersonnel->where('status', $status);
            $queryAssurance->where('status', $status);
        }

        // Appliquer les filtres de date
        if ($period === 'day' && $date) {
            $queryPersonnel->whereDate('created_at', $date);
            $queryAssurance->whereDate('created_at', $date);
        } elseif ($period === 'week' && $week) {
            $parts = explode('-W', $week);
            if (count($parts) === 2) {
                $yearW = (int)$parts[0];
                $weekW = (int)$parts[1];
                $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                $queryPersonnel->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                $queryAssurance->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            }
        } elseif ($period === 'month' && $month) {
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                $yearM = (int)$parts[0];
                $monthM = (int)$parts[1];
                $queryPersonnel->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
                $queryAssurance->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
            }
        } elseif ($period === 'year' && $year) {
            $queryPersonnel->whereYear('created_at', $year);
            $queryAssurance->whereYear('created_at', $year);
        } elseif ($period === 'range' && $dateStart && $dateEnd) {
            $queryPersonnel->whereBetween('created_at', [$dateStart, $dateEnd]);
            $queryAssurance->whereBetween('created_at', [$dateStart, $dateEnd]);
        }

        // Appliquer le filtre par type - si on veut voir seulement un type
        if ($type === 'personnel') {
            $creditsPersonnel = $queryPersonnel->latest()->paginate(10, ['*'], 'personnels');
            $creditsAssurance = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, [
                'path' => request()->url(),
                'pageName' => 'assurances',
            ]);
        } elseif ($type === 'assurance') {
            $creditsPersonnel = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, [
                'path' => request()->url(),
                'pageName' => 'personnels',
            ]);
            $creditsAssurance = $queryAssurance->latest()->paginate(10, ['*'], 'assurances');
        } else {
            // Afficher les deux types
            $creditsPersonnel = $queryPersonnel->latest()->paginate(10, ['*'], 'personnels');
            $creditsAssurance = $queryAssurance->latest()->paginate(10, ['*'], 'assurances');
        }

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

        // Mettre à jour dynamiquement le montant de la dépense liée
        $depense = $credit->depense;
        if ($depense) {
            $nouveauMontant = max($credit->montant - $credit->montant_paye, 0);
            $depense->montant = $nouveauMontant;
            $depense->save();
        }

        if ($credit->montant_paye >= $credit->montant) {
            $credit->status = 'payé';
            // Marquer la dépense liée comme remboursée
            if ($depense) {
                $depense->rembourse = true;
                $depense->save();
            }
        } else {
            $credit->status = 'partiellement payé';
        }

        $credit->save();

        // Créer une recette (entrée) pour le remboursement du crédit
        \App\Models\ModePaiement::create([
            'type' => 'espèces',
            'montant' => $montant,
            'caisse_id' => null,
            'source' => 'credit_personnel',
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
            'source' => 'credit_assurance', // Ajout explicite
        ]);

        // Mettre à jour l'EtatCaisse existant au lieu de créer une nouvelle entrée
        $etatCaisseExistant = null;

        // Essayer plusieurs méthodes de recherche pour trouver l'entrée existante
        if ($credit->caisse_id) {
            // Méthode 1 : Recherche par caisse_id et assurance_id
            $etatCaisseExistant = \App\Models\EtatCaisse::where('assurance_id', $credit->source_id)
                ->where('caisse_id', $credit->caisse_id)
                ->first();
        }

        if (!$etatCaisseExistant) {
            // Méthode 2 : Recherche par assurance_id seulement (plus récente)
            $etatCaisseExistant = \App\Models\EtatCaisse::where('assurance_id', $credit->source_id)
                ->whereNotNull('caisse_id')
                ->latest()
                ->first();
        }

        if ($etatCaisseExistant) {
            // Mettre à jour l'entrée existante
            $etatCaisseExistant->increment('recette', $montant);
            $etatCaisseExistant->increment('part_clinique', $montant);

            // Log pour débugger (optionnel)
            Log::info("EtatCaisse mis à jour", [
                'etat_caisse_id' => $etatCaisseExistant->id,
                'credit_id' => $credit->id,
                'montant' => $montant
            ]);
        } else {
            // Ne pas créer d' EtatCaisse sans caisse pour un remboursement d'assurance.
            // On s'appuie uniquement sur l'entrée ModePaiement (source=credit_assurance) et l'historique l'affiche désormais.
            Log::info("Aucune entrée EtatCaisse trouvée à mettre à jour pour le crédit #{$credit->id}; enregistrement du paiement uniquement dans ModePaiement.");
        }

        // Mise à jour du crédit de la source
        if ($credit->source_type === 'App\\Models\\Assurance') {
            $credit->source->updateCredit();
        }

        return redirect()->route('credits.index')->with('success', 'Paiement enregistré avec succès.');
    }

    public function create()
    {
        // Ne proposer la création de crédits que pour le personnel.
        $personnels = Personnel::all();
        foreach ($personnels as $personnel) {
            $personnel->updateCredit();
        }
        $modes = \App\Models\ModePaiement::getTypes();
        // On envoie une liste d'assurances vide pour masquer la section dans la vue
        $assurances = collect();
        return view('credits.create', compact('personnels', 'assurances', 'modes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'source_type' => 'required|in:personnel',
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
            // Bloqué : la création de crédits d'assurance se fait automatiquement à la création de facture
            return back()->with('error', "La création de crédits d'assurance se fait automatiquement lors de la facturation d'un patient assuré.");
        }

        Log::info('Création crédit', [
            'montant' => $request->montant,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'source_nom' => $sourceNom,
            'mode_paiement' => $modePaiementId,
        ]);

        $credit = Credit::create([
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'montant' => $request->montant,
            'status' => 'non payé',
            'statut' => 'non payé',
            'montant_paye' => 0,
            'mode_paiement_id' => $modePaiementId
        ]);

        // Création automatique d'une dépense pour les crédits du personnel
        if ($sourceType === \App\Models\Personnel::class) {
            \App\Models\Depense::create([
                'nom' => "Crédit accordé à $sourceNom",
                'montant' => $request->montant,
                'etat_caisse_id' => null,
                'mode_paiement_id' => 'espèces', // Correction : mode de paiement par défaut
                'source' => 'crédit personnel',
                'credit_id' => $credit->id,
            ]);
        }

        // Mettre à jour le crédit de la source
        if ($sourceType === \App\Models\Personnel::class) {
            $personnel->updateCredit();
        }

        return redirect()->route('credits.index')->with('success', 'Crédit ajouté avec succès.');
    }
}
