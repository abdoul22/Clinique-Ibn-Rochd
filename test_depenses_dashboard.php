<?php

require_once 'vendor/autoload.php';

use App\Models\Depense;
use App\Models\ModePaiement;
use App\Models\Caisse;
use App\Models\EtatCaisse;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DES DÉPENSES DANS LE DASHBOARD ===\n\n";

// 🔼 ENTREES
echo "🔼 CALCUL DES ENTREES:\n";
echo "=====================\n";

$entrees = [
    'espèces' => 0,
    'bankily' => 0,
    'masrivi' => 0,
    'sedad' => 0,
];

// Recettes des caisses (ModePaiement)
$paiements = ModePaiement::all();
foreach ($paiements as $paiement) {
    if (in_array($paiement->type, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
        $entrees[$paiement->type] += $paiement->montant;
        echo "   + {$paiement->type}: {$paiement->montant} MRU (Caisse #{$paiement->caisse_id})\n";
    }
}

echo "\n📊 TOTAL DES ENTREES PAR MODE:\n";
foreach ($entrees as $mode => $montant) {
    echo "   {$mode}: {$montant} MRU\n";
}

// 🔽 SORTIES
echo "\n🔽 CALCUL DES SORTIES:\n";
echo "=====================\n";

$sorties = [
    'espèces' => 0,
    'bankily' => 0,
    'masrivi' => 0,
    'sedad' => 0,
];

// Dépenses par mode de paiement
echo "\n1. Dépenses par mode de paiement:\n";
$depenses = Depense::all();
foreach ($depenses as $depense) {
    if ($depense->mode_paiement_id && in_array($depense->mode_paiement_id, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
        $type = $depense->mode_paiement_id;
        $sorties[$type] += $depense->montant;
        $source = $depense->source === 'automatique' ? 'Part médecin' : 'Manuelle';
        echo "   - {$type}: {$depense->montant} MRU ({$depense->nom} - {$source})\n";
    } else {
        echo "   ⚠ {$depense->nom}: {$depense->montant} MRU (Mode non défini: {$depense->mode_paiement_id})\n";
    }
}

// Crédits accordés
echo "\n2. Crédits accordés:\n";
$credits = \App\Models\Credit::all();
foreach ($credits as $credit) {
    if ($credit->mode_paiement_id && in_array($credit->mode_paiement_id, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
        $type = $credit->mode_paiement_id;
        $sorties[$type] += $credit->montant;
        $sourceNom = $credit->source ? $credit->source->nom : 'N/A';
        echo "   - {$type}: {$credit->montant} MRU (Crédit {$sourceNom})\n";
    }
}

echo "\n📊 TOTAL DES SORTIES PAR MODE:\n";
foreach ($sorties as $mode => $montant) {
    echo "   {$mode}: {$montant} MRU\n";
}

// SOLDE FINAL
echo "\n💰 SOLDE FINAL PAR MODE:\n";
echo "========================\n";
$totalGlobal = 0;
foreach (['espèces', 'bankily', 'masrivi', 'sedad'] as $mode) {
    $entree = $entrees[$mode] ?? 0;
    $sortie = $sorties[$mode] ?? 0;
    $solde = $entree - $sortie;
    $totalGlobal += $solde;

    echo "   {$mode}: {$entree} - {$sortie} = {$solde} MRU\n";
}

echo "\n🏦 TRÉSORERIE TOTALE: {$totalGlobal} MRU\n";

// Détails des dépenses
echo "\n📋 DÉTAILS DES DÉPENSES:\n";
echo "======================\n";
echo "Total dépenses: " . Depense::count() . "\n";
echo "Dépenses manuelles: " . Depense::where('source', 'manuelle')->count() . "\n";
echo "Dépenses automatiques (parts médecin): " . Depense::where('source', 'automatique')->count() . "\n";

$depensesParSource = Depense::selectRaw('source, COUNT(*) as count, SUM(montant) as total')
    ->groupBy('source')
    ->get();

echo "\n📊 RÉPARTITION PAR SOURCE:\n";
foreach ($depensesParSource as $stat) {
    echo "   {$stat->source}: {$stat->count} dépenses, {$stat->total} MRU\n";
}

$depensesParMode = Depense::selectRaw('mode_paiement_id, COUNT(*) as count, SUM(montant) as total')
    ->whereNotNull('mode_paiement_id')
    ->groupBy('mode_paiement_id')
    ->get();

echo "\n📊 RÉPARTITION PAR MODE DE PAIEMENT:\n";
foreach ($depensesParMode as $stat) {
    echo "   {$stat->mode_paiement_id}: {$stat->count} dépenses, {$stat->total} MRU\n";
}
