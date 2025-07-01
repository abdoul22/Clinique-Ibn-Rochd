<?php

require_once 'vendor/autoload.php';

use App\Models\Personnel;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MARQUAGE DU PERSONNEL EXISTANT ===\n\n";

$personnels = Personnel::all();

foreach ($personnels as $personnel) {
    echo "👤 Personnel: {$personnel->nom}\n";

    // Marquer comme non approuvé et créé par 'user' (pas superadmin)
    $personnel->update([
        'is_approved' => false,
        'created_by' => 'user'
    ]);

    echo "   ✅ Marqué comme non approuvé (created_by: user)\n";
}

echo "\n✅ Tous les personnels existants sont maintenant non approuvés.\n";
echo "   Seul le superadmin pourra les approuver pour les crédits.\n";
