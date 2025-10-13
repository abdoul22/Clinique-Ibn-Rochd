<?php

/**
 * Script rapide pour créer un utilisateur superadmin
 *
 * Utilisation:
 *   php create_superadmin_quick.php "Nom Complet" "email@example.com" "motdepasse"
 *
 * Exemple:
 *   php create_superadmin_quick.php "Dr Brahim" "drntaghry@yahoo.fr" "36305626"
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

// Vérifier les arguments
if ($argc < 4) {
    echo "╔════════════════════════════════════════════════════╗\n";
    echo "║   CRÉATION RAPIDE D'UN SUPERADMIN                  ║\n";
    echo "╚════════════════════════════════════════════════════╝\n\n";
    echo "Usage: php create_superadmin_quick.php \"Nom\" \"email\" \"motdepasse\"\n\n";
    echo "Exemple:\n";
    echo "  php create_superadmin_quick.php \"Dr Brahim\" \"drntaghry@yahoo.fr\" \"36305626\"\n\n";
    exit(1);
}

$name = $argv[1];
$email = $argv[2];
$password = $argv[3];

echo "=== CRÉATION D'UN SUPERADMIN ===\n\n";

// Validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "❌ ERREUR: Email invalide!\n";
    exit(1);
}

// Vérifier si l'utilisateur existe déjà
$existingUser = User::where('email', $email)->first();
if ($existingUser) {
    echo "❌ ERREUR: Un utilisateur avec cet email existe déjà!\n";
    echo "   Nom: {$existingUser->name}\n";
    echo "   Rôle: " . ($existingUser->role ? $existingUser->role->name : 'Aucun') . "\n";
    exit(1);
}

// Récupérer le rôle superadmin
$superadminRole = Role::where('name', 'superadmin')->first();

if (!$superadminRole) {
    echo "❌ ERREUR: Le rôle 'superadmin' n'existe pas!\n";
    exit(1);
}

try {
    // Créer l'utilisateur
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => \Hash::make($password),
        'role_id' => $superadminRole->id,
        'is_approved' => true,
    ]);

    echo "✅ Superadmin créé avec succès!\n\n";
    echo "Détails:\n";
    echo "  ID: {$user->id}\n";
    echo "  Nom: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Rôle: superadmin\n";
    echo "  Approuvé: Oui\n\n";

    echo "Connexion:\n";
    echo "  Email: {$user->email}\n";
    echo "  Mot de passe: {$password}\n";
} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    exit(1);
}
