<?php

require_once 'vendor/autoload.php';

use App\Models\Lit;
use Illuminate\Support\Facades\DB;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Diagnostic du lit 7 ===\n\n";

// 1. Vérifier le lit 7 directement
echo "1. Données du lit 7:\n";
$lit7 = Lit::find(7);
if ($lit7) {
    echo "- ID: {$lit7->id}\n";
    echo "- Numéro: {$lit7->numero}\n";
    echo "- Chambre ID: {$lit7->chambre_id}\n";
    echo "- Statut: {$lit7->statut}\n";
    echo "- Type: {$lit7->type}\n";
} else {
    echo "❌ Lit 7 non trouvé\n";
    exit;
}

echo "\n";

// 2. Vérifier si la chambre existe
echo "2. Vérification de la chambre:\n";
$chambre = DB::table('chambres')->where('id', $lit7->chambre_id)->first();
if ($chambre) {
    echo "- Chambre trouvée: ID {$chambre->id}, Nom: {$chambre->nom}\n";
} else {
    echo "❌ Chambre avec ID {$lit7->chambre_id} non trouvée\n";
}

echo "\n";

// 3. Vérifier la relation Eloquent
echo "3. Test de la relation Eloquent:\n";
try {
    $lit7->load('chambre');
    if ($lit7->chambre) {
        echo "- Relation chambre chargée: {$lit7->chambre->nom}\n";
    } else {
        echo "❌ Relation chambre est null\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur lors du chargement de la relation: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Vérifier tous les lits et leurs chambres
echo "4. Tous les lits et leurs chambres:\n";
$lits = Lit::with('chambre')->get();
foreach ($lits as $lit) {
    $chambreInfo = $lit->chambre ? $lit->chambre->nom : 'NULL';
    echo "- Lit ID: {$lit->id}, Numéro: {$lit->numero}, Chambre ID: {$lit->chambre_id}, Chambre: {$chambreInfo}\n";
}

echo "\n";

// 5. Vérifier les chambres existantes
echo "5. Chambres existantes:\n";
$chambres = DB::table('chambres')->get();
foreach ($chambres as $chambre) {
    echo "- ID: {$chambre->id}, Nom: {$chambre->nom}\n";
}

echo "\n=== Diagnostic terminé ===\n";
