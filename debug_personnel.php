<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Personnel;

echo "=== Debug du module personnel ===\n\n";

echo "1. Tous les utilisateurs (détail complet) :\n";
$users = User::all();
foreach ($users as $user) {
    $status = $user->is_approved ? '✅ Approuvé' : '⏳ En attente';
    $fonction = $user->fonction ?: 'Non définie';
    echo "- ID: {$user->id} | {$user->name} ({$user->role->name}) : {$fonction} - {$status}\n";
}

echo "\n2. Utilisateurs récents (créés aujourd'hui) :\n";
$recentUsers = User::whereDate('created_at', today())->get();
foreach ($recentUsers as $user) {
    $status = $user->is_approved ? '✅ Approuvé' : '⏳ En attente';
    $fonction = $user->fonction ?: 'Non définie';
    echo "- {$user->name} ({$user->role->name}) : {$fonction} - {$status} - Créé: {$user->created_at}\n";
}

echo "\n3. Utilisateurs en attente d'approbation :\n";
$pendingUsers = User::where('is_approved', false)->get();
foreach ($pendingUsers as $user) {
    $fonction = $user->fonction ?: 'Non définie';
    echo "- {$user->name} ({$user->role->name}) : {$fonction} - Créé: {$user->created_at}\n";
}

echo "\n4. Utilisateurs approuvés sans fonction :\n";
$approvedWithoutFunction = User::where('is_approved', true)
    ->where(function ($query) {
        $query->whereNull('fonction')->orWhere('fonction', '');
    })
    ->get();

foreach ($approvedWithoutFunction as $user) {
    echo "- {$user->name} ({$user->role->name}) - Créé: {$user->created_at}\n";
}

echo "\n5. Personnel existant :\n";
$personnel = Personnel::all();
foreach ($personnel as $p) {
    echo "- {$p->nom} : {$p->fonction}\n";
}

echo "\n6. Recommandations :\n";
if ($pendingUsers->count() > 0) {
    echo "⚠️ Il y a {$pendingUsers->count()} utilisateur(s) en attente d'approbation\n";
    echo "   → Allez sur /superadmin/admins pour les approuver\n";
}

if ($approvedWithoutFunction->count() > 0) {
    echo "⚠️ Il y a {$approvedWithoutFunction->count()} utilisateur(s) approuvé(s) sans fonction\n";
    echo "   → Attribuez-leur une fonction dans /superadmin/admins\n";
}

echo "\n✅ Debug terminé !\n";
