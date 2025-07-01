<?php

require_once 'vendor/autoload.php';

use App\Models\Personnel;
use App\Models\Credit;
use App\Models\PaymentMode;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DE LA LOGIQUE DE CRÉDIT PERSONNEL ===\n\n";

// Récupérer un personnel de test
$personnel = Personnel::first();
if (!$personnel) {
    echo "❌ Aucun personnel trouvé. Créons-en un...\n";
    $personnel = Personnel::create([
        'nom' => 'Test Personnel',
        'fonction' => 'Testeur',
        'salaire' => 50000,
        'telephone' => '123456789',
        'adresse' => 'Adresse Test'
    ]);
}

echo "👤 Personnel testé : {$personnel->nom}\n";
echo "💰 Salaire : {$personnel->salaire} MRU\n";
echo "💳 Crédit actuel : {$personnel->credit} MRU\n";
echo "📊 Crédit maximum possible : {$personnel->montant_max_credit} MRU\n";
echo "💵 Salaire net : {$personnel->salaire_net} MRU\n\n";

// Test 1: Vérifier si le personnel peut prendre un crédit
echo "=== TEST 1: VALIDATION DES CRÉDITS ===\n";

$montantsTest = [10000, 20000, 30000, 60000];

foreach ($montantsTest as $montant) {
    $peutPrendre = $personnel->peutPrendreCredit($montant);
    $status = $peutPrendre ? "✅ AUTORISÉ" : "❌ REFUSÉ";
    echo "   Crédit de {$montant} MRU : {$status}\n";
}

echo "\n=== TEST 2: CRÉATION D'UN CRÉDIT VALIDE ===\n";

// Créer un mode de paiement de test
$modePaiement = \App\Models\ModePaiement::where('type', 'espèces')->first();
if (!$modePaiement) {
    echo "❌ Mode de paiement 'espèces' non trouvé\n";
    exit;
}

// Tenter de créer un crédit valide
$montantCredit = 15000;
if ($personnel->peutPrendreCredit($montantCredit)) {
    echo "✅ Création d'un crédit de {$montantCredit} MRU...\n";

    // Déduire du mode de paiement
    $modePaiement->decrement('montant', $montantCredit);

    // Créer le crédit
    $credit = Credit::create([
        'source_type' => Personnel::class,
        'source_id' => $personnel->id,
        'montant' => $montantCredit,
        'status' => 'non payé',
        'statut' => 'Non payé',
        'montant_paye' => 0,
        'mode_paiement_id' => 'espèces'
    ]);

    // Mettre à jour le crédit du personnel
    $personnel->updateCredit();

    echo "✅ Crédit créé avec succès !\n";
    echo "   💳 Nouveau crédit total : {$personnel->credit} MRU\n";
    echo "   📊 Nouveau crédit maximum : {$personnel->montant_max_credit} MRU\n";
    echo "   💵 Nouveau salaire net : {$personnel->salaire_net} MRU\n";
} else {
    echo "❌ Impossible de créer le crédit de {$montantCredit} MRU\n";
}

echo "\n=== TEST 3: DÉDUCTION AUTOMATIQUE ===\n";

if ($personnel->aDesCreditsEnCours()) {
    echo "✅ Le personnel a des crédits en cours. Test de déduction...\n";

    $creditAvant = $personnel->credit;
    $salaireAvant = $personnel->salaire;

    $montantDeduit = $personnel->deduireCreditDuSalaire();

    echo "   💳 Crédit avant déduction : {$creditAvant} MRU\n";
    echo "   💰 Salaire avant déduction : {$salaireAvant} MRU\n";
    echo "   📉 Montant déduit : {$montantDeduit} MRU\n";
    echo "   💳 Crédit après déduction : {$personnel->credit} MRU\n";
    echo "   💰 Salaire après déduction : {$personnel->salaire} MRU\n";

    if ($montantDeduit > 0) {
        echo "✅ Déduction réussie !\n";
    } else {
        echo "⚠ Aucune déduction effectuée.\n";
    }
} else {
    echo "⚠ Le personnel n'a pas de crédits en cours.\n";
}

echo "\n=== RÉSUMÉ FINAL ===\n";
echo "👤 Personnel : {$personnel->nom}\n";
echo "💰 Salaire final : {$personnel->salaire} MRU\n";
echo "💳 Crédit final : {$personnel->credit} MRU\n";
echo "📊 Crédit maximum possible : {$personnel->montant_max_credit} MRU\n";
echo "💵 Salaire net final : {$personnel->salaire_net} MRU\n";

echo "\n🎉 Tests terminés !\n";
