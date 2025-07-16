<?php

require_once 'vendor/autoload.php';

use App\Models\Lit;
use App\Models\Chambre;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test final du module lits avec findOrFail ===\n\n";

// 1. Tester findOrFail pour différents lits
echo "1. Test de findOrFail:\n";

$testIds = [1, 2, 3, 7, 9];
foreach ($testIds as $id) {
    try {
        $lit = Lit::findOrFail($id);
        echo "- Lit ID {$id}: Trouvé - Numéro {$lit->numero}, Chambre ID {$lit->chambre_id}\n";

        // Tester l'attribut nom_complet
        echo "  Nom complet: {$lit->nom_complet}\n";

    } catch (Exception $e) {
        echo "- Lit ID {$id}: ❌ Erreur - " . $e->getMessage() . "\n";
    }
}

echo "\n";

// 2. Tester avec un ID inexistant
echo "2. Test avec ID inexistant:\n";
try {
    $lit = Lit::findOrFail(999);
    echo "- Lit ID 999: Trouvé (impossible)\n";
} catch (Exception $e) {
    echo "- Lit ID 999: ❌ Erreur attendue - " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Tester les relations
echo "3. Test des relations:\n";
$lit = Lit::findOrFail(1);
$lit->load(['chambre', 'hospitalisationActuelle.patient']);

echo "- Lit ID 1:\n";
echo "  Numéro: {$lit->numero}\n";
echo "  Chambre: " . ($lit->chambre ? $lit->chambre->nom : 'NULL') . "\n";
echo "  Hospitalisation actuelle: " . ($lit->hospitalisationActuelle ? 'Oui' : 'Non') . "\n";

echo "\n";

// 4. Tester la génération de routes
echo "4. Test de génération de routes:\n";
try {
    $lit = Lit::findOrFail(2);
    $showUrl = route('lits.show', $lit->id);
    $editUrl = route('lits.edit', $lit->id);

    echo "- Lit ID 2:\n";
    echo "  Route show: {$showUrl}\n";
    echo "  Route edit: {$editUrl}\n";
    echo "  ✅ Routes générées avec succès\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de la génération des routes: " . $e->getMessage() . "\n";
}

echo "\n=== Test terminé ===\n";
echo "✅ Le module lits devrait maintenant fonctionner correctement avec findOrFail.\n";
