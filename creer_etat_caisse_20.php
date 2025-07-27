<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Caisse;
use App\Models\EtatCaisse;

echo "=== CRÉATION ÉTAT DE CAISSE POUR CAISSE #20 ===\n\n";

$caisse = Caisse::find(20);

if (!$caisse) {
    echo "❌ Caisse #20 introuvable\n";
    exit;
}

// Vérifier si l'état de caisse existe déjà
$etatExistant = EtatCaisse::where('caisse_id', 20)->first();

if ($etatExistant) {
    echo "⚠️  État de caisse existe déjà (ID: {$etatExistant->id})\n";
    echo "- Recette actuelle: {$etatExistant->recette} MRU\n";
    echo "- Assurance ID: " . ($etatExistant->assurance_id ?? 'NULL') . "\n\n";
} else {
    echo "✅ Création d'un nouvel état de caisse...\n";

    // Calculer la part patient en fonction de l'assurance
    $montantTotal = $caisse->total;
    $couverture = $caisse->couverture ?? 0;
    $montantAssurance = $montantTotal * ($couverture / 100);
    $montantPatient = $montantTotal - $montantAssurance;

    $etat = EtatCaisse::create([
        'designation' => 'Facture caisse n°' . $caisse->id,
        'recette' => $montantPatient,
        'part_medecin' => 0,
        'part_clinique' => 0,
        'depense' => 0,
        'assurance_id' => $caisse->assurance_id && $caisse->couverture > 0 ? $caisse->assurance_id : null,
        'caisse_id' => $caisse->id,
        'medecin_id' => $caisse->medecin_id,
    ]);

    echo "✅ État de caisse créé avec succès (ID: {$etat->id})\n";
    echo "- Désignation: {$etat->designation}\n";
    echo "- Recette: {$etat->recette} MRU\n";
    echo "- Assurance ID: " . ($etat->assurance_id ?? 'NULL') . "\n\n";
}

echo "📊 DONNÉES CAISSE #20 :\n";
echo "- Total: {$caisse->total} MRU\n";
echo "- Couverture: {$caisse->couverture}%\n";
echo "- Assurance ID: " . ($caisse->assurance_id ?? 'NULL') . "\n";
echo "- Médecin ID: {$caisse->medecin_id}\n\n";

echo "🎯 MAINTENANT :\n";
echo "1. Allez sur http://localhost:8000/caisses/20/edit\n";
echo "2. Modifiez quelque chose (ex: total)\n";
echo "3. Sauvegardez\n";
echo "4. Vérifiez http://localhost:8000/caisses et http://localhost:8000/etatcaisse\n";
