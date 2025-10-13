<?php

/**
 * Script pour créer un utilisateur superadmin
 *
 * Utilisation:
 *   php create_superadmin.php
 *
 * Le script demandera interactivement:
 *   - Nom complet
 *   - Email
 *   - Mot de passe
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "╔════════════════════════════════════════════════════╗\n";
echo "║   CRÉATION D'UN UTILISATEUR SUPERADMIN             ║\n";
echo "╚════════════════════════════════════════════════════╝\n\n";

// Vérifier que le rôle superadmin existe
$superadminRole = Role::where('name', 'superadmin')->first();

if (!$superadminRole) {
    echo "❌ ERREUR: Le rôle 'superadmin' n'existe pas dans la base de données!\n";
    echo "Rôles disponibles:\n";
    Role::all()->each(function ($role) {
        echo "  - {$role->name} (ID: {$role->id})\n";
    });
    exit(1);
}

echo "✅ Rôle 'superadmin' trouvé (ID: {$superadminRole->id})\n\n";

// Fonction pour lire l'entrée utilisateur
function readline_prompt($prompt)
{
    echo $prompt;
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    return trim($line);
}

// Demander les informations
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Entrez les informations du nouvel utilisateur:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$name = readline_prompt("Nom complet: ");
$email = readline_prompt("Email: ");
$password = readline_prompt("Mot de passe: ");

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Validation
if (empty($name) || empty($email) || empty($password)) {
    echo "❌ ERREUR: Tous les champs sont obligatoires!\n";
    exit(1);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "❌ ERREUR: Email invalide!\n";
    exit(1);
}

// Vérifier si l'utilisateur existe déjà
$existingUser = User::where('email', $email)->first();
if ($existingUser) {
    echo "❌ ERREUR: Un utilisateur avec cet email existe déjà!\n";
    echo "   Nom: {$existingUser->name}\n";
    echo "   Email: {$existingUser->email}\n";
    echo "   Rôle: " . ($existingUser->role ? $existingUser->role->name : 'Aucun') . "\n";
    exit(1);
}

// Confirmation
echo "\nRécapitulatif:\n";
echo "  Nom: $name\n";
echo "  Email: $email\n";
echo "  Mot de passe: " . str_repeat('*', strlen($password)) . "\n";
echo "  Rôle: superadmin\n";
echo "  Approuvé: Oui\n\n";

$confirm = readline_prompt("Confirmer la création? (o/n): ");

if (strtolower($confirm) !== 'o' && strtolower($confirm) !== 'oui') {
    echo "\n❌ Création annulée.\n";
    exit(0);
}

// Créer l'utilisateur
try {
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => \Hash::make($password),
        'role_id' => $superadminRole->id,
        'is_approved' => true,
    ]);

    echo "\n╔════════════════════════════════════════════════════╗\n";
    echo "║           ✅ SUCCÈS!                               ║\n";
    echo "╚════════════════════════════════════════════════════╝\n\n";

    echo "Utilisateur créé avec succès!\n\n";
    echo "Détails:\n";
    echo "  ID: {$user->id}\n";
    echo "  Nom: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Rôle: " . ($user->role ? $user->role->name : 'Aucun') . "\n";
    echo "  Approuvé: " . ($user->is_approved ? 'Oui' : 'Non') . "\n";
    echo "  Date création: {$user->created_at}\n";

    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "L'utilisateur peut maintenant se connecter avec:\n";
    echo "  Email: {$user->email}\n";
    echo "  Mot de passe: (celui que vous avez entré)\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
} catch (\Exception $e) {
    echo "\n╔════════════════════════════════════════════════════╗\n";
    echo "║           ❌ ERREUR!                               ║\n";
    echo "╚════════════════════════════════════════════════════╝\n\n";

    echo "Erreur lors de la création de l'utilisateur:\n";
    echo $e->getMessage() . "\n\n";

    // Afficher plus de détails si disponibles
    if (method_exists($e, 'getTrace')) {
        echo "Stack trace:\n";
        echo $e->getTraceAsString() . "\n";
    }

    exit(1);
}

echo "\n=== LISTE DE TOUS LES UTILISATEURS ===\n";
$users = User::with('role')->get();
echo "Total: " . $users->count() . " utilisateur(s)\n\n";

foreach ($users as $u) {
    echo "- {$u->name} ({$u->email}) - Rôle: " . ($u->role ? $u->role->name : 'Aucun') . " - Approuvé: " . ($u->is_approved ? 'Oui' : 'Non') . "\n";
}
