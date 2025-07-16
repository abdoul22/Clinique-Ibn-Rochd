<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vérification de la structure des rôles ===\n\n";

// 1. Vérifier la structure de la table users
echo "1. Structure de la table users:\n";
$columns = DB::select("DESCRIBE users");
foreach ($columns as $column) {
    echo "- {$column->Field}: {$column->Type} " . ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}

echo "\n";

// 2. Vérifier les données de role_id
echo "2. Données de role_id:\n";
$users = DB::table('users')->select('id', 'email', 'role_id')->get();
foreach ($users as $user) {
    echo "- User ID: {$user->id}, Email: {$user->email}, Role ID: " . ($user->role_id ?? 'NULL') . "\n";
}

echo "\n";

// 3. Vérifier s'il y a des rôles dans la table roles
echo "3. Contenu de la table roles:\n";
try {
    $roles = DB::table('roles')->get();
    echo "Nombre de rôles: " . $roles->count() . "\n";
    foreach ($roles as $role) {
        echo "- ID: {$role->id}, Nom: {$role->name}\n";
    }
} catch (Exception $e) {
    echo "Erreur lors de l'accès à la table roles: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Vérifier les utilisateurs avec leurs rôles
echo "4. Utilisateurs avec leurs rôles:\n";
try {
    $usersWithRoles = DB::table('users')
        ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
        ->select('users.id', 'users.email', 'users.role_id', 'roles.name as role_name')
        ->get();

    foreach ($usersWithRoles as $user) {
        echo "- User ID: {$user->id}, Email: {$user->email}, Role ID: " . ($user->role_id ?? 'NULL') . ", Role: " . ($user->role_name ?? 'Aucun') . "\n";
    }
} catch (Exception $e) {
    echo "Erreur lors de la jointure: " . $e->getMessage() . "\n";
}

echo "\n=== Fin de la vérification ===\n";
