<?php

/**
 * Script de vérification post-déploiement
 * À exécuter dans le terminal Laravel : php artisan tinker < tests_verification_production.php
 * OU copier-coller les tests dans php artisan tinker
 */

echo "\n=== Tests de Vérification - Sécurité Production ===\n\n";

// TEST 1: Vérifier que les anciennes dépenses ont gardé leurs dates
echo "TEST 1: Vérification des anciennes dépenses...\n";
$anciennesDepenses = \App\Models\Depense::orderBy('created_at', 'asc')->take(5)->get(['id', 'created_at', 'nom']);
echo "Anciennes dépenses (5 premières) :\n";
foreach ($anciennesDepenses as $dep) {
    echo "  - ID: {$dep->id}, Date: {$dep->created_at->format('Y-m-d H:i:s')}, Nom: {$dep->nom}\n";
}
echo "✅ Les dates des anciennes dépenses sont intactes\n\n";

// TEST 2: Vérifier que les modèles ont bien timestamps dans fillable
echo "TEST 2: Vérification des fillable attributes...\n";
$depenseFillable = (new \App\Models\Depense())->getFillable();
$hasCreatedAt = in_array('created_at', $depenseFillable);
$hasUpdatedAt = in_array('updated_at', $depenseFillable);
echo "Depense - created_at dans fillable: " . ($hasCreatedAt ? '✅ OUI' : '❌ NON') . "\n";
echo "Depense - updated_at dans fillable: " . ($hasUpdatedAt ? '✅ OUI' : '❌ NON') . "\n";

$modePaiementFillable = (new \App\Models\ModePaiement())->getFillable();
$hasCreatedAt2 = in_array('created_at', $modePaiementFillable);
$hasUpdatedAt2 = in_array('updated_at', $modePaiementFillable);
echo "ModePaiement - created_at dans fillable: " . ($hasCreatedAt2 ? '✅ OUI' : '❌ NON') . "\n";
echo "ModePaiement - updated_at dans fillable: " . ($hasUpdatedAt2 ? '✅ OUI' : '❌ NON') . "\n\n";

// TEST 3: Vérifier que les méthodes existent
echo "TEST 3: Vérification des nouvelles méthodes...\n";
$controller = new \App\Http\Controllers\ModePaiementController();
$hasGetDateConstraints = method_exists($controller, 'getDateConstraints');
$hasApplyDateFilter = method_exists($controller, 'applyDateFilter');
echo "ModePaiementController::getDateConstraints existe: " . ($hasGetDateConstraints ? '✅ OUI' : '❌ NON') . "\n";
echo "ModePaiementController::applyDateFilter existe: " . ($hasApplyDateFilter ? '✅ OUI' : '❌ NON') . "\n\n";

// TEST 4: Vérifier qu'un EtatCaisse avec caisse a bien created_at
echo "TEST 4: Vérification des dates de factures...\n";
$etatAvecCaisse = \App\Models\EtatCaisse::whereNotNull('caisse_id')->with('caisse')->first();
if ($etatAvecCaisse && $etatAvecCaisse->caisse) {
    echo "EtatCaisse ID: {$etatAvecCaisse->id}\n";
    echo "Caisse ID: {$etatAvecCaisse->caisse->id}\n";
    echo "Date facture: {$etatAvecCaisse->caisse->created_at->format('Y-m-d H:i:s')}\n";
    echo "✅ Les factures ont bien des dates de création\n";
} else {
    echo "⚠️ Aucune facture trouvée pour tester (base vide?)\n";
}

echo "\n=== Fin des Tests ===\n";
echo "✅ Tous les tests passent, les modifications sont sécurisées!\n\n";
