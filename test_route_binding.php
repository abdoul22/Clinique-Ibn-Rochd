<?php

require_once 'vendor/autoload.php';

use App\Models\Lit;
use Illuminate\Support\Facades\Route;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test du route model binding pour les lits ===\n\n";

// 1. Vérifier que les lits existent
echo "1. Vérification des lits:\n";
$lits = Lit::all();
foreach ($lits as $lit) {
    echo "- Lit ID: {$lit->id}, Numéro: {$lit->numero}, Chambre ID: {$lit->chambre_id}\n";
}

echo "\n";

// 2. Tester la génération des routes
echo "2. Test de génération des routes:\n";

$lit1 = Lit::find(1);
if ($lit1) {
    echo "- Lit 1 trouvé: ID {$lit1->id}\n";

    // Simuler la génération de routes
    try {
        $showUrl = route('lits.show', $lit1);
        echo "- Route show: {$showUrl}\n";

        $editUrl = route('lits.edit', $lit1);
        echo "- Route edit: {$editUrl}\n";

        echo "✅ Routes générées avec succès\n";
    } catch (Exception $e) {
        echo "❌ Erreur lors de la génération des routes: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Lit 1 non trouvé\n";
}

echo "\n";

// 3. Vérifier les routes disponibles
echo "3. Routes disponibles pour les lits:\n";
$routes = app('router')->getRoutes();
$litRoutes = [];
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'lits') && !str_contains($route->uri(), 'api')) {
        $litRoutes[] = [
            'name' => $route->getName(),
            'uri' => $route->uri(),
            'methods' => $route->methods()
        ];
    }
}

foreach ($litRoutes as $route) {
    echo "- {$route['name']}: {$route['uri']} [" . implode(',', $route['methods']) . "]\n";
}

echo "\n=== Test terminé ===\n";
