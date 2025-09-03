<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\ModePaiement;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $now = now();
        $year = (int) $request->get('year', $now->year);
        $month = (int) $request->get('month', $now->month);

        $debut = now()->setDate($year, $month, 1)->startOfDay();
        $fin = (clone $debut)->endOfMonth()->endOfDay();

        $personnels = Personnel::orderBy('nom')->get()->map(function (Personnel $p) use ($debut, $fin, $year, $month) {
            $creditTotal = Credit::where('source_type', Personnel::class)
                ->where('source_id', $p->id)
                ->sum('montant');
            $creditPaye = Credit::where('source_type', Personnel::class)
                ->where('source_id', $p->id)
                ->sum('montant_paye');
            $creditRestant = max($creditTotal - $creditPaye, 0);

            $creditMois = Credit::where('source_type', Personnel::class)
                ->where('source_id', $p->id)
                ->whereBetween('created_at', [$debut, $fin])
                ->sum('montant');

            $isPaid = \App\Models\Payroll::where('personnel_id', $p->id)
                ->where('year', $year)
                ->where('month', $month)
                ->exists();

            return [
                'id' => $p->id,
                'nom' => $p->nom,
                'fonction' => $p->fonction,
                'salaire' => (float) $p->salaire,
                'credit_restant' => (float) $creditRestant,
                'net_a_payer' => max(((float) $p->salaire) - $creditRestant, 0),
                'credit_ce_mois' => (float) $creditMois,
                'is_paid' => $isPaid,
            ];
        });

        // Tri: 1) crédit restant > 0, 2) net à payer > 0, 3) non payé d'abord, 4) nom
        $personnels = $personnels->sortBy(function ($x) {
            return [
                $x['credit_restant'] > 0 ? 0 : 1,
                $x['net_a_payer'] > 0 ? 0 : 1,
                $x['is_paid'] ? 1 : 0,
                strtolower($x['nom'])
            ];
        })->values();

        $modes = ModePaiement::getTypes();

        return view('salaires.index', [
            'personnels' => $personnels,
            'year' => $year,
            'month' => $month,
            'modes' => $modes,
            // URL pour payer un par un -> /credits avec filtres prédéfinis
            'url_paiement_un_par_un' => route('credits.index', ['type' => 'personnel', 'status' => 'non payé']),
        ]);
    }

    public function pdf(Request $request)
    {
        // Préparer les mêmes données que index
        $now = now();
        $year = (int) $request->get('year', $now->year);
        $month = (int) $request->get('month', $now->month);

        $debut = now()->setDate($year, $month, 1)->startOfDay();
        $fin = (clone $debut)->endOfMonth()->endOfDay();

        $personnels = Personnel::orderBy('nom')->get()->map(function (Personnel $p) use ($debut, $fin, $year, $month) {
            $creditTotal = Credit::where('source_type', Personnel::class)
                ->where('source_id', $p->id)
                ->sum('montant');
            $creditPaye = Credit::where('source_type', Personnel::class)
                ->where('source_id', $p->id)
                ->sum('montant_paye');
            $creditRestant = max($creditTotal - $creditPaye, 0);

            $creditMois = Credit::where('source_type', Personnel::class)
                ->where('source_id', $p->id)
                ->whereBetween('created_at', [$debut, $fin])
                ->sum('montant');

            return [
                'id' => $p->id,
                'nom' => $p->nom,
                'fonction' => $p->fonction,
                'salaire' => (float) $p->salaire,
                'credit_restant' => (float) $creditRestant,
                'net_a_payer' => max(((float) $p->salaire) - $creditRestant, 0),
                'credit_ce_mois' => (float) $creditMois,
                'is_paid' => \App\Models\Payroll::where('personnel_id', $p->id)->where('year', $year)->where('month', $month)->exists(),
            ];
        });
        $personnels = $personnels->sortByDesc(function ($x) {
            return $x['credit_ce_mois'] > 0 ? 1 : 0;
        })->values();

        $data = ['personnels' => $personnels, 'year' => $year, 'month' => $month];
        $pdf = Pdf::loadView('salaires.pdf', $data);
        $filename = 'salaires_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.pdf';
        return $pdf->download($filename);
    }

    public function payAll(Request $request)
    {
        $request->validate([
            'mode' => 'required|string|in:' . implode(',', ModePaiement::getTypes()),
        ]);
        $mode = $request->mode;
        $now = now();
        $year = (int) $request->get('year', $now->year);
        $month = (int) $request->get('month', $now->month);

        $personnels = Personnel::all();
        foreach ($personnels as $personnel) {
            // Si déjà payé ce mois-ci, sauter
            if (\App\Models\Payroll::where('personnel_id', $personnel->id)->where('year', $year)->where('month', $month)->exists()) {
                continue;
            }
            // 1) Payer tous les crédits restants du personnel via déduction salariale
            $credits = Credit::where('source_type', Personnel::class)
                ->where('source_id', $personnel->id)
                ->whereColumn('montant_paye', '<', 'montant')
                ->get();

            foreach ($credits as $credit) {
                $reste = $credit->montant - $credit->montant_paye;
                if ($reste <= 0) {
                    continue;
                }

                // Marquer comme payé totalement
                $credit->montant_paye = $credit->montant;
                $credit->status = 'payé';
                $credit->save();

                // Journaliser l'opération comme "entrée" de remboursement (déduction salariale)
                $datePaiement = now()->setDate($year, $month, 1)->endOfMonth();
                ModePaiement::create([
                    'type' => $mode,
                    'montant' => $reste,
                    'caisse_id' => null,
                    'source' => 'credit_personnel',
                    'created_at' => $datePaiement,
                    'updated_at' => $datePaiement,
                ]);
            }

            // 2) Créer la dépense de salaire net (salaire - totalité crédits restants avant paiement)
            $creditTotal = Credit::where('source_type', Personnel::class)
                ->where('source_id', $personnel->id)
                ->sum('montant');
            $creditPaye = Credit::where('source_type', Personnel::class)
                ->where('source_id', $personnel->id)
                ->sum('montant_paye');
            $creditRestantApres = max($creditTotal - $creditPaye, 0);
            $net = max($personnel->salaire - $creditRestantApres, 0);

            if ($net > 0) {
                // Créer la date du mois payé (dernier jour du mois)
                $datePaiement = now()->setDate($year, $month, 1)->endOfMonth();

                \App\Models\Depense::create([
                    'nom' => 'Salaire ' . $datePaiement->translatedFormat('F Y') . ' - ' . $personnel->nom,
                    'montant' => $net,
                    'mode_paiement_id' => $mode,
                    'source' => 'salaire',
                    'created_at' => $datePaiement,
                    'updated_at' => $datePaiement,
                ]);
                \App\Models\Payroll::create([
                    'personnel_id' => $personnel->id,
                    'year' => $year,
                    'month' => $month,
                    'montant_net' => $net,
                    'mode' => $mode,
                    'paid_at' => $datePaiement,
                ]);
            }
        }

        return redirect()->route('salaires.index', ['year' => $year, 'month' => $month])->with('success', 'Tous les salaires ont été traités.');
    }

    public function payOne(Request $request, $personnelId)
    {
        $request->validate([
            'mode' => 'required|string|in:' . implode(',', ModePaiement::getTypes()),
        ]);

        $mode = $request->mode;
        $personnel = Personnel::findOrFail($personnelId);
        $now = now();
        $year = (int) $request->get('year', $now->year);
        $month = (int) $request->get('month', $now->month);

        // Bloquer si déjà payé
        if (\App\Models\Payroll::where('personnel_id', $personnel->id)->where('year', $year)->where('month', $month)->exists()) {
            return redirect()->route('salaires.index', ['year' => $year, 'month' => $month])->with('info', 'Salaire déjà payé pour ' . $personnel->nom . ' ce mois.');
        }

        // Payer crédits restants
        $credits = Credit::where('source_type', Personnel::class)
            ->where('source_id', $personnel->id)
            ->whereColumn('montant_paye', '<', 'montant')
            ->get();
        foreach ($credits as $credit) {
            $reste = $credit->montant - $credit->montant_paye;
            if ($reste <= 0) {
                continue;
            }
            $credit->montant_paye = $credit->montant;
            $credit->status = 'payé';
            $credit->save();

            $datePaiement = now()->setDate($year, $month, 1)->endOfMonth();
            ModePaiement::create([
                'type' => $mode,
                'montant' => $reste,
                'caisse_id' => null,
                'source' => 'credit_personnel',
                'created_at' => $datePaiement,
                'updated_at' => $datePaiement,
            ]);
        }

        // Créer la dépense de salaire net
        $creditTotal = Credit::where('source_type', Personnel::class)
            ->where('source_id', $personnel->id)
            ->sum('montant');
        $creditPaye = Credit::where('source_type', Personnel::class)
            ->where('source_id', $personnel->id)
            ->sum('montant_paye');
        $creditRestantApres = max($creditTotal - $creditPaye, 0);
        $net = max($personnel->salaire - $creditRestantApres, 0);

        if ($net > 0) {
            // Créer la date du mois payé (dernier jour du mois)
            $datePaiement = now()->setDate($year, $month, 1)->endOfMonth();

            \App\Models\Depense::create([
                'nom' => 'Salaire ' . $datePaiement->translatedFormat('F Y') . ' - ' . $personnel->nom,
                'montant' => $net,
                'mode_paiement_id' => $mode,
                'source' => 'salaire',
                'created_at' => $datePaiement,
                'updated_at' => $datePaiement,
            ]);
            \App\Models\Payroll::create([
                'personnel_id' => $personnel->id,
                'year' => $year,
                'month' => $month,
                'montant_net' => $net,
                'mode' => $mode,
                'paid_at' => $datePaiement,
            ]);
            return redirect()->route('salaires.index', ['year' => $year, 'month' => $month])->with('success', 'Salaire payé pour ' . $personnel->nom . '.');
        }

        // Cas net=0: prévenir clairement
        return redirect()->route('salaires.index', ['year' => $year, 'month' => $month])->with('info', "Paiement ignoré pour {$personnel->nom} : aucun salaire net à verser (salaire ou net à payer égal à 0).");
    }
}
