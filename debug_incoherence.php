<?php

require_once 'vendor/autoload.php';

use App\Models\ModePaiement;
use App\Models\Caisse;
use App\Models\EtatCaisse;
use App\Models\Depense;
use App\Models\Credit;
use App\Models\Personnel;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ANALYSE DE L'INCOHÉRENCE DASHBOARD vs ÉTATS DE CAISSE ===\n\n";

// 🔍 ANALYSE DES CAISSES (SOURCE RÉELLE DE LIQUIDITÉ)
echo "🔍 ANALYSE DES CAISSES (SOURCE RÉELLE):\n";
echo "=======================================\n";

$caisses = Caisse::with('mode_paiements')->get();
$totalCaisses = 0;

foreach ($caisses as $caisse) {
    echo "\n📋 Caisse #{$caisse->id}:\n";
    echo "   - Patient: " . ($caisse->patient ? $caisse->patient->nom : 'N/A') . "\n";
    echo "   - Total facture: {$caisse->total} MRU\n";
    echo "   - Date: {$caisse->created_at->format('d/m/Y')}\n";

    $totalCaisse = 0;
    foreach ($caisse->mode_paiements as $paiement) {
        echo "   - {$paiement->type}: {$paiement->montant} MRU\n";
        $totalCaisse += $paiement->montant;
    }
    echo "   - Total caisse: {$totalCaisse} MRU\n";
    $totalCaisses += $totalCaisse;
}

echo "\n💰 TOTAL RÉEL DES CAISSES: {$totalCaisses} MRU\n";

// 🔍 ANALYSE DES ÉTATS DE CAISSE
echo "\n🔍 ANALYSE DES ÉTATS DE CAISSE:\n";
echo "================================\n";

$etatsCaisse = EtatCaisse::with('caisse')->get();
$totalEtats = 0;

foreach ($etatsCaisse as $etat) {
    echo "\n📊 État Caisse #{$etat->id}:\n";
    echo "   - Désignation: {$etat->designation}\n";
    echo "   - Recette: {$etat->recette} MRU\n";
    echo "   - Part médecin: {$etat->part_medecin} MRU\n";
    echo "   - Part clinique: {$etat->part_clinique} MRU\n";
    echo "   - Caisse liée: " . ($etat->caisse ? "#{$etat->caisse->id}" : "Aucune") . "\n";
    $totalEtats += $etat->recette;
}

echo "\n💰 TOTAL DES ÉTATS DE CAISSE: {$totalEtats} MRU\n";

// 🔍 ANALYSE DES CRÉDITS PERSONNEL
echo "\n🔍 ANALYSE DES CRÉDITS PERSONNEL:\n";
echo "==================================\n";

$creditsPersonnel = Credit::where('source_type', Personnel::class)->get();
$totalCreditsPersonnel = 0;

foreach ($creditsPersonnel as $credit) {
    $personnel = $credit->source;
    echo "\n💳 Crédit #{$credit->id}:\n";
    echo "   - Personnel: " . ($personnel ? $personnel->nom : 'N/A') . "\n";
    echo "   - Montant: {$credit->montant} MRU\n";
    echo "   - Mode paiement: {$credit->mode_paiement_id}\n";
    echo "   - Statut: {$credit->status}\n";
    $totalCreditsPersonnel += $credit->montant;
}

echo "\n💰 TOTAL CRÉDITS PERSONNEL: {$totalCreditsPersonnel} MRU\n";

// 🔍 ANALYSE DES DÉPENSES
echo "\n🔍 ANALYSE DES DÉPENSES:\n";
echo "========================\n";

$depenses = Depense::all();
$totalDepenses = 0;

foreach ($depenses as $depense) {
    echo "\n💸 Dépense #{$depense->id}:\n";
    echo "   - Nom: {$depense->nom}\n";
    echo "   - Montant: {$depense->montant} MRU\n";
    echo "   - Source: {$depense->source}\n";
    echo "   - Mode paiement: {$depense->mode_paiement_id}\n";
    $totalDepenses += $depense->montant;
}

echo "\n💰 TOTAL DÉPENSES: {$totalDepenses} MRU\n";

// 🔍 CALCUL DU DASHBOARD ACTUEL
echo "\n🔍 CALCUL DU DASHBOARD ACTUEL:\n";
echo "===============================\n";

$entrees = [
    'espèces' => 0,
    'bankily' => 0,
    'masrivi' => 0,
    'sedad' => 0,
];

// Entrées (ModePaiement)
$paiements = ModePaiement::all();
foreach ($paiements as $paiement) {
    if (in_array($paiement->type, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
        $entrees[$paiement->type] += $paiement->montant;
    }
}

echo "📊 ENTREES PAR MODE:\n";
foreach ($entrees as $mode => $montant) {
    echo "   {$mode}: {$montant} MRU\n";
}

$sorties = [
    'espèces' => 0,
    'bankily' => 0,
    'masrivi' => 0,
    'sedad' => 0,
];

// Sorties (Dépenses + Crédits)
foreach ($depenses as $depense) {
    if ($depense->mode_paiement_id && in_array($depense->mode_paiement_id, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
        $sorties[$depense->mode_paiement_id] += $depense->montant;
    }
}

foreach ($creditsPersonnel as $credit) {
    if ($credit->mode_paiement_id && in_array($credit->mode_paiement_id, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
        $sorties[$credit->mode_paiement_id] += $credit->montant;
    }
}

echo "\n📊 SORTIES PAR MODE:\n";
foreach ($sorties as $mode => $montant) {
    echo "   {$mode}: {$montant} MRU\n";
}

$totalDashboard = 0;
echo "\n💰 SOLDE PAR MODE:\n";
foreach (['espèces', 'bankily', 'masrivi', 'sedad'] as $mode) {
    $entree = $entrees[$mode] ?? 0;
    $sortie = $sorties[$mode] ?? 0;
    $solde = $entree - $sortie;
    $totalDashboard += $solde;
    echo "   {$mode}: {$entree} - {$sortie} = {$solde} MRU\n";
}

echo "\n🏦 TOTAL DASHBOARD: {$totalDashboard} MRU\n";

// 🔍 CONCLUSION
echo "\n🔍 CONCLUSION:\n";
echo "==============\n";
echo "📋 Total caisses (source réelle): {$totalCaisses} MRU\n";
echo "📊 Total états de caisse: {$totalEtats} MRU\n";
echo "🏦 Total dashboard: {$totalDashboard} MRU\n";
echo "💳 Total crédits personnel: {$totalCreditsPersonnel} MRU\n";
echo "💸 Total dépenses: {$totalDepenses} MRU\n";

if ($totalDashboard > $totalCaisses) {
    echo "\n⚠️  PROBLÈME DÉTECTÉ: Le dashboard affiche plus que les caisses réelles !\n";
    echo "   Différence: " . ($totalDashboard - $totalCaisses) . " MRU\n";
    echo "   Cela peut venir de:\n";
    echo "   - Crédits accordés sans fonds réels\n";
    echo "   - Dépenses créées sans source de liquidité\n";
    echo "   - Données de test non nettoyées\n";
}
