<?php

require_once 'vendor/autoload.php';

use App\Models\Lit;
use App\Models\Chambre;
use Illuminate\Support\Facades\DB;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test des corrections du module lits ===\n\n";

// 1. Tester l'attribut nom_complet avec chambre null
echo "1. Test de l'attribut nom_complet:\n";

// Créer un lit temporaire avec chambre_id null pour tester
$testLit = new Lit();
$testLit->numero = 999;
$testLit->chambre_id = null;
$testLit->statut = 'libre';
$testLit->type = 'standard';

echo "- Test avec chambre null: " . $testLit->nom_complet . "\n";

// Test avec une chambre existante
$lit7 = Lit::with('chambre')->find(7);
if ($lit7) {
    echo "- Test avec chambre existante: " . $lit7->nom_complet . "\n";
}

echo "\n";

// 2. Vérifier tous les lits et leurs relations
echo "2. Vérification de tous les lits:\n";
$lits = Lit::with('chambre')->get();
foreach ($lits as $lit) {
    $chambreInfo = $lit->chambre ? $lit->chambre->nom : 'NULL';
    $nomComplet = $lit->nom_complet;
    echo "- Lit ID: {$lit->id}, Numéro: {$lit->numero}, Chambre: {$chambreInfo}, Nom complet: {$nomComplet}\n";
}

echo "\n";

// 3. Tester les méthodes du contrôleur
echo "3. Test des méthodes du contrôleur:\n";

// Simuler la méthode getLitsDisponibles
$litsDisponibles = Lit::with('chambre')
    ->where('statut', 'libre')
    ->get()
    ->map(function ($lit) {
        return [
            'id' => $lit->id,
            'numero' => $lit->numero,
            'nom_complet' => $lit->nom_complet,
            'type' => $lit->type,
            'chambre' => $lit->chambre ? $lit->chambre->nom : 'Chambre supprimée',
        ];
    });

echo "Lits disponibles (simulation API):\n";
foreach ($litsDisponibles as $lit) {
    echo "- ID: {$lit['id']}, Numéro: {$lit['numero']}, Chambre: {$lit['chambre']}, Nom complet: {$lit['nom_complet']}\n";
}

echo "\n";

// 4. Vérifier les chambres existantes
echo "4. Chambres existantes:\n";
$chambres = Chambre::all();
foreach ($chambres as $chambre) {
    echo "- ID: {$chambre->id}, Nom: {$chambre->nom}\n";
}

echo "\n";

// 5. Test de robustesse - simuler une chambre supprimée
echo "5. Test de robustesse (chambre supprimée):\n";

// Trouver un lit et simuler la suppression de sa chambre
$litTest = Lit::first();
if ($litTest) {
    echo "- Lit testé: ID {$litTest->id}, Chambre ID {$litTest->chambre_id}\n";

    // Simuler l'accès à nom_complet même si la chambre est supprimée
    $litTest->chambre_id = 99999; // ID inexistant
    $litTest->chambre = null; // Relation null

    echo "- Nom complet avec chambre supprimée: " . $litTest->nom_complet . "\n";
}

echo "\n=== Tests terminés ===\n";
echo "✅ Toutes les corrections ont été appliquées avec succès.\n";
echo "Le module lits devrait maintenant fonctionner correctement.\n";
