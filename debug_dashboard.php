<?php

require_once 'vendor/autoload.php';

use App\Models\ModePaiement;
use App\Models\Caisse;
use App\Models\EtatCaisse;
use App\Models\Depense;
use App\Models\Credit;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG DASHBOARD - CALCUL CORRIGÉ (SANS DOUBLE COMPTABILISATION) ===\n\n";

// 🔼 ENTREES : Seulement les recettes des caisses (ModePaiement)
$entrees = [
    'espèces' => 0,
    'bankily' => 0,
    'masrivi' => 0,
    'sedad' => 0,
];

echo "🔼 CALCUL DES ENTREES (SEULEMENT MODEPAIEMENT):\n";
echo "===============================================\n";

// ➤ Recettes des caisses (ModePaiement) - SEULE SOURCE D'ENTRÉES
echo "\n1. Recettes des caisses (ModePaiement):\n";
$paiements = ModePaiement::all();
foreach ($paiements as $paiement) {
    if (in_array($paiement->type, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
        $entrees[$paiement->type] += $paiement->montant;
        echo "   - {$paiement->type}: +{$paiement->montant} (Caisse #{$paiement->caisse_id})\n";
    }
}

echo "\n📊 TOTAL DES ENTREES PAR MODE:\n";
foreach ($entrees as $mode => $montant) {
    echo "   {$mode}: {$montant}\n";
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

// ➤ Dépenses
echo "\n1. Dépenses:\n";
foreach (Depense::all() as $depense) {
    if ($depense->mode_paiement_id && in_array($depense->mode_paiement_id, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
        $type = $depense->mode_paiement_id;
        $sorties[$type] += $depense->montant;
        echo "   - {$type}: -{$depense->montant} ({$depense->nom})\n";
    }
}

// ➤ Crédits accordés
echo "\n2. Crédits accordés:\n";
foreach (Credit::all() as $credit) {
    if ($credit->mode_paiement_id && in_array($credit->mode_paiement_id, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
        $type = $credit->mode_paiement_id;
        $sorties[$type] += $credit->montant;
        $sourceNom = $credit->source ? $credit->source->nom : 'N/A';
        echo "   - {$type}: -{$credit->montant} (Crédit {$sourceNom})\n";
    }
}

echo "\n📊 TOTAL DES SORTIES PAR MODE:\n";
foreach ($sorties as $mode => $montant) {
    echo "   {$mode}: {$montant}\n";
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

    echo "   {$mode}: {$entree} - {$sortie} = {$solde}\n";
}

echo "\n🏦 TRÉSORERIE TOTALE: {$totalGlobal}\n";

// Détails des données
echo "\n📋 DÉTAILS DES DONNÉES:\n";
echo "======================\n";
echo "Total ModePaiement: " . ModePaiement::count() . "\n";
echo "Total Caisse: " . Caisse::count() . "\n";
echo "Total EtatCaisse: " . EtatCaisse::count() . "\n";
echo "Total Depense: " . Depense::count() . "\n";
echo "Total Credit: " . Credit::count() . "\n";

// Vérification des états de caisse (pour information)
echo "\n📋 ÉTATS DE CAISSE (INFORMATIF - NON COMPTÉS COMME ENTREES):\n";
echo "============================================================\n";
$etatsCaisse = EtatCaisse::with('caisse')->get();
foreach ($etatsCaisse as $etat) {
    echo "   - État #{$etat->id}: {$etat->recette} MRU (Caisse #{$etat->caisse_id})\n";
}
