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

        // Construire un résumé lisible des filtres de période (pour alléger la vue)
        $summary = '';
        if ($period === 'day' && $date) {
            $summary = \Carbon\Carbon::parse($date)->translatedFormat('d F Y');
        } elseif ($period === 'week' && $week) {
            $parts = explode('-W', $week);
            if (count($parts) === 2) {
                $start = \Carbon\Carbon::now()->setISODate((int) $parts[0], (int) $parts[1])->startOfWeek();
                $end = \Carbon\Carbon::now()->setISODate((int) $parts[0], (int) $parts[1])->endOfWeek();
                $summary = $start->translatedFormat('d F') . ' - ' . $end->translatedFormat('d F Y');
            }
        } elseif ($period === 'month' && $month) {
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                $summary = \Carbon\Carbon::create((int) $parts[0], (int) $parts[1])->translatedFormat('F Y');
            }
        } elseif ($period === 'year' && $year) {
            $summary = 'Année ' . $year;
        } elseif ($period === 'range' && $dateStart && $dateEnd) {
            $summary = \Carbon\Carbon::parse($dateStart)->translatedFormat('d F') . ' - ' . \Carbon\Carbon::parse($dateEnd)->translatedFormat('d F Y');
        }

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

        return view('credits.index', compact('creditsPersonnel', 'creditsAssurance', 'summary'));
    }

    public function show($id)
    {
        $credit = Credit::with('source')->findOrFail($id);

        // Calculer les informations de paiement
        $montantRestant = $credit->montant - $credit->montant_paye;
        $pourcentagePaye = $credit->montant > 0 ? ($credit->montant_paye / $credit->montant) * 100 : 0;

        // Récupérer l'historique des paiements si disponible
        $historiquePaiements = \App\Models\ModePaiement::where('source', 'credit_personnel')
            ->orWhere('source', 'credit_assurance')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('credits.show', compact('credit', 'montantRestant', 'pourcentagePaye', 'historiquePaiements'));
    }

    public function edit($id)
    {
        $credit = Credit::with('source')->findOrFail($id);
        $personnels = Personnel::all();
        $assurances = Assurance::all();
        $modes = \App\Models\ModePaiement::getTypes();

        return view('credits.edit', compact('credit', 'personnels', 'assurances', 'modes'));
    }

    // Ajout de la méthode manquante pour le paiement par déduction de salaire
    public function payerSalaire(Request $request, $id)
    {
        $credit = Credit::findOrFail($id);
        $personnel = $credit->source;

        $montantRestant = $credit->montant - $credit->montant_paye;

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
}
