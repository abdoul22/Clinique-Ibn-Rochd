<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Vérification de l'authentification ===\n\n";

// Vérifier s'il y a des utilisateurs dans la base
$users = User::all();
echo "Nombre d'utilisateurs dans la base: " . $users->count() . "\n\n";

if ($users->count() > 0) {
    echo "=== Utilisateurs disponibles ===\n";
    foreach ($users as $user) {
        echo "ID: {$user->id}\n";
        echo "Email: {$user->email}\n";
        echo "Nom: {$user->name}\n";
        echo "Rôle: {$user->role}\n";
        echo "Approuvé: " . ($user->is_approved ? 'OUI' : 'NON') . "\n";
        echo "---\n";
    }

    // Chercher un admin ou superadmin
    $admin = User::where('is_approved', true)->first();
    if ($admin) {
        $roleData = json_decode($admin->role, true);
        $roleName = $roleData['name'] ?? 'unknown';

        if (in_array($roleName, ['admin', 'superadmin'])) {
            echo "\n=== Compte admin disponible ===\n";
            echo "Email: {$admin->email}\n";
            echo "Rôle: {$roleName}\n";
            echo "Mot de passe: (utilisez le mot de passe que vous avez défini)\n";
        } else {
            echo "\n✗ Aucun compte admin approuvé trouvé (rôle: {$roleName})\n";
        }
    } else {
        echo "\n✗ Aucun compte approuvé trouvé\n";
    }
} else {
    echo "✗ Aucun utilisateur dans la base de données\n";
}
