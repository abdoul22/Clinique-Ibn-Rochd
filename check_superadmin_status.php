<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vérification de l'utilisateur superadmin ===\n\n";

// Vérifier l'utilisateur superadmin
$superadmin = User::where('email', 'superadmin@clinique.fr')->first();

if ($superadmin) {
    echo "Utilisateur trouvé:\n";
    echo "- ID: {$superadmin->id}\n";
    echo "- Email: {$superadmin->email}\n";
    echo "- Role ID: {$superadmin->role_id}\n";
    echo "- Is Approved: " . ($superadmin->is_approved ? 'Oui' : 'Non') . "\n";

    // Vérifier le rôle
    $role = DB::table('roles')->where('id', $superadmin->role_id)->first();
    echo "- Rôle: " . ($role ? $role->name : 'N/A') . "\n";

    echo "\n";

    // Corriger si nécessaire
    if (!$superadmin->is_approved) {
        echo "❌ L'utilisateur n'est pas approuvé. Correction en cours...\n";

        $superadmin->is_approved = true;
        $superadmin->save();

        echo "✅ Utilisateur approuvé avec succès !\n";
    } else {
        echo "✅ L'utilisateur est déjà approuvé.\n";
    }

    // Vérifier aussi le rôle
    if ($superadmin->role_id != 1) {
        echo "❌ L'utilisateur n'a pas le bon rôle. Correction en cours...\n";

        $superadmin->role_id = 1; // superadmin
        $superadmin->save();

        echo "✅ Rôle corrigé avec succès !\n";
    } else {
        echo "✅ L'utilisateur a le bon rôle.\n";
    }

} else {
    echo "❌ Utilisateur superadmin@clinique.fr non trouvé.\n";
}

echo "\n";

// Afficher tous les utilisateurs pour comparaison
echo "Tous les utilisateurs:\n";
$users = User::all();
foreach ($users as $user) {
    $role = DB::table('roles')->where('id', $user->role_id)->first();
    echo "- ID: {$user->id}, Email: {$user->email}, Role: " . ($role ? $role->name : 'N/A') . ", Approved: " . ($user->is_approved ? 'Oui' : 'Non') . "\n";
}

echo "\n=== Vérification terminée ===\n";
echo "Vous pouvez maintenant vous connecter avec superadmin@clinique.fr\n";
