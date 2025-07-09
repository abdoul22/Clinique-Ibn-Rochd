<?php

require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Service;
use App\Models\Pharmacie;

echo "=== Test Final des Services Pharmacie ===\n\n";

// 1. Vérifier tous les services
echo "1. Tous les services :\n";
$services = Service::with('pharmacie')->get();

foreach ($services as $service) {
    echo "   - ID: {$service->id} | Nom: '{$service->nom}' | Type: {$service->type_service}\n";
    if ($service->pharmacie) {
        echo "     Médicament: {$service->pharmacie->nom_medicament} | Stock: {$service->pharmacie->stock}\n";
    }
}

echo "   Total services : {$services->count()}\n";

// 2. Services de type médicament
echo "\n2. Services de type médicament :\n";
$servicesMedicaments = Service::where('type_service', 'medicament')->with('pharmacie')->get();

foreach ($servicesMedicaments as $service) {
    echo "   - Service ID: {$service->id}\n";
    echo "     Nom affiché: Pharmacie\n";
    echo "     Observation affichée: " . ($service->pharmacie ? $service->pharmacie->nom_medicament : 'Aucun') . "\n";
    echo "     Prix: " . number_format($service->prix, 0, ',', ' ') . " MRU\n";
    echo "     Stock disponible: " . ($service->pharmacie ? $service->pharmacie->stock : 'N/A') . "\n";
}

echo "   Total services médicament : {$servicesMedicaments->count()}\n";

// 3. Vérifier que tous les médicaments ont un service
echo "\n3. Vérification de la correspondance médicaments/services :\n";
$medicaments = Pharmacie::where('statut', 'actif')->get();
$servicesMedicaments = Service::where('type_service', 'medicament')->with('pharmacie')->get();

echo "   Médicaments actifs : {$medicaments->count()}\n";
echo "   Services médicament : {$servicesMedicaments->count()}\n";

if ($medicaments->count() === $servicesMedicaments->count()) {
    echo "   ✓ Tous les médicaments ont un service correspondant !\n";
} else {
    echo "   ✗ Il manque des services pour certains médicaments\n";
}

// 4. Liste des médicaments disponibles
echo "\n4. Médicaments disponibles dans la pharmacie :\n";
foreach ($medicaments as $medicament) {
    $service = $servicesMedicaments->where('pharmacie_id', $medicament->id)->first();
    if ($service) {
        echo "   ✓ {$medicament->nom_medicament} - Stock: {$medicament->stock} - Prix: {$medicament->prix_vente} MRU\n";
    } else {
        echo "   ✗ {$medicament->nom_medicament} - PAS DE SERVICE\n";
    }
}

echo "\n=== Test terminé avec succès ===\n";
echo "\nMaintenant sur http://localhost:8000/services, tu devrais voir :\n";
echo "- Tous les médicaments de la pharmacie affichés\n";
echo "- Colonne 'Nom' : 'Pharmacie' pour tous les services médicament\n";
echo "- Colonne 'Observation' : Nom du médicament (ex: 'Paracétamol 500mg')\n";
echo "\n";
