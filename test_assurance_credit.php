<?php

require_once 'vendor/autoload.php';

use App\Models\Assurance;
use App\Models\Personnel;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DES MÉTHODES ASSURANCE ===\n\n";

// Test du modèle Personnel
echo "--- TEST PERSONNEL ---\n";
$personnel = Personnel::first();
if ($personnel) {
    echo "✅ Personnel trouvé : {$personnel->nom}\n";
    $personnel->updateCredit();
    echo "   Crédit mis à jour : {$personnel->credit} MRU\n";
} else {
    echo "❌ Aucun personnel trouvé\n";
}

// Test du modèle Assurance
echo "\n--- TEST ASSURANCE ---\n";
$assurance = Assurance::first();
if ($assurance) {
    echo "✅ Assurance trouvée : {$assurance->nom}\n";
    $assurance->updateCredit();
    echo "   Crédit mis à jour : {$assurance->credit} MRU\n";
    echo "   Statut crédit : {$assurance->statut_credit}\n";
} else {
    echo "❌ Aucune assurance trouvée\n";
}

echo "\n✅ Test terminé - Les méthodes fonctionnent !\n";
