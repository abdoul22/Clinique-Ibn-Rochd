<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Correction de l'assignation de rôle pour l'utilisateur superadmin ===\n\n";

// Vérifier l'état actuel
echo "État actuel des rôles:\n";
$roles = DB::table('roles')->get();
foreach ($roles as $role) {
    echo "- ID: {$role->id}, Nom: {$role->name}\n";
}

echo "\n";

// Vérifier l'utilisateur superadmin actuel
echo "Utilisateur superadmin actuel:\n";
$superadminUser = DB::table('users')
    ->where('email', 'superadmin@clinique.fr')
    ->first();

if ($superadminUser) {
    echo "- Email: {$superadminUser->email}, Role ID actuel: {$superadminUser->role_id}\n";

    // Trouver le rôle superadmin correct (ID 1)
    $superadminRole = DB::table('roles')->where('name', 'superadmin')->first();

    if ($superadminRole) {
        echo "- Rôle superadmin trouvé: ID {$superadminRole->id}, Nom: {$superadminRole->name}\n";

        // Mettre à jour l'utilisateur pour utiliser le bon rôle
        $updated = DB::table('users')
            ->where('email', 'superadmin@clinique.fr')
            ->update(['role_id' => $superadminRole->id]);

        if ($updated) {
            echo "✅ Utilisateur superadmin mis à jour avec succès !\n";
        } else {
            echo "❌ Erreur lors de la mise à jour de l'utilisateur.\n";
        }
    } else {
        echo "❌ Rôle superadmin non trouvé.\n";
    }
} else {
    echo "❌ Utilisateur superadmin non trouvé.\n";
}

echo "\n";

// Vérifier l'état après correction
echo "État après correction:\n";
$usersWithRoles = DB::table('users')
    ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
    ->select('users.id', 'users.email', 'users.role_id', 'roles.name as role_name')
    ->get();

foreach ($usersWithRoles as $user) {
    echo "- User ID: {$user->id}, Email: {$user->email}, Role: " . ($user->role_name ?? 'Aucun') . "\n";
}

echo "\n";

// Supprimer le rôle super_admin en double s'il n'est plus utilisé
$unusedRole = DB::table('users')
    ->where('role_id', 3)
    ->count();

if ($unusedRole == 0) {
    $deleted = DB::table('roles')->where('id', 3)->delete();
    if ($deleted) {
        echo "🗑️  Rôle 'super_admin' (ID 3) supprimé car non utilisé.\n";
    }
}

echo "\n=== Correction terminée ===\n";
echo "Vous pouvez maintenant vous reconnecter avec superadmin@clinique.fr et accéder à /lits/3 ou /lits/4\n";
