<?php

require_once 'vendor/autoload.php';

use App\Models\Lit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test d'accès aux lits ===\n\n";

// 1. Vérifier si les lits existent
echo "1. Vérification des lits dans la base de données:\n";
$lits = Lit::with('chambre')->get();
echo "Nombre total de lits: " . $lits->count() . "\n";

foreach ($lits as $lit) {
    echo "- Lit ID: {$lit->id}, Numéro: {$lit->numero}, Chambre: " . ($lit->chambre ? $lit->chambre->nom : 'N/A') . "\n";
}

echo "\n";

// 2. Vérifier les utilisateurs et leurs rôles
echo "2. Vérification des utilisateurs:\n";
$users = User::all();
foreach ($users as $user) {
    $roles = is_array($user->roles) ? $user->roles : json_decode($user->roles, true);
    echo "- User ID: {$user->id}, Email: {$user->email}, Rôles: " . json_encode($roles) . ", Approuvé: " . ($user->is_approved ? 'Oui' : 'Non') . "\n";
}

echo "\n";

// 3. Tester l'accès direct au modèle Lit
echo "3. Test d'accès direct au modèle Lit:\n";
try {
    $lit3 = Lit::find(3);
    if ($lit3) {
        echo "- Lit 3 trouvé: ID={$lit3->id}, Numéro={$lit3->numero}\n";
        $lit3->load('chambre');
        echo "- Chambre: " . ($lit3->chambre ? $lit3->chambre->nom : 'N/A') . "\n";
    } else {
        echo "- Lit 3 non trouvé\n";
    }

    $lit4 = Lit::find(4);
    if ($lit4) {
        echo "- Lit 4 trouvé: ID={$lit4->id}, Numéro={$lit4->numero}\n";
        $lit4->load('chambre');
        echo "- Chambre: " . ($lit4->chambre ? $lit4->chambre->nom : 'N/A') . "\n";
    } else {
        echo "- Lit 4 non trouvé\n";
    }
} catch (Exception $e) {
    echo "- Erreur lors de l'accès aux lits: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Vérifier les routes
echo "4. Vérification des routes:\n";
$routes = app('router')->getRoutes();
$litRoutes = [];
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'lits')) {
        $litRoutes[] = [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'middleware' => $route->middleware()
        ];
    }
}

echo "Routes lits trouvées:\n";
foreach ($litRoutes as $route) {
    echo "- URI: {$route['uri']}, Méthodes: " . implode(',', $route['methods']) . "\n";
    echo "  Middleware: " . implode(',', $route['middleware']) . "\n";
}

echo "\n=== Fin du test ===\n";
