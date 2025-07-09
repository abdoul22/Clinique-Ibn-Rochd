<?php

require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Service;
use App\Models\Pharmacie;
use Illuminate\Support\Facades\DB;

echo "=== Test Complet des Services Pharmacie ===\n\n";

// 1. Vérifier tous les médicaments de la pharmacie
echo "1. Médicaments dans la pharmacie :\n";
$medicaments = Pharmacie::where('statut', 'actif')->get();

foreach ($medicaments as $medicament) {
    echo "   - ID: {$medicament->id} | {$medicament->nom_medicament} | Stock: {$medicament->stock} | Prix: {$medicament->prix_vente} MRU\n";
}

echo "   Total médicaments actifs : {$medicaments->count()}\n";

// 2. Vérifier tous les services existants
echo "\n2. Services existants :\n";
$services = Service::with('pharmacie')->get();

foreach ($services as $service) {
    echo "   - ID: {$service->id} | Nom: '{$service->nom}' | Type: {$service->type_service}\n";
    if ($service->pharmacie) {
        echo "     Médicament lié: {$service->pharmacie->nom_medicament}\n";
    }
}

echo "   Total services : {$services->count()}\n";

// 3. Vérifier les services de type médicament
echo "\n3. Services de type médicament :\n";
$servicesMedicaments = Service::where('type_service', 'medicament')->with('pharmacie')->get();

foreach ($servicesMedicaments as $service) {
    echo "   - ID: {$service->id} | Nom: '{$service->nom}'\n";
    if ($service->pharmacie) {
        echo "     Médicament: {$service->pharmacie->nom_medicament} | Stock: {$service->pharmacie->stock}\n";
    } else {
        echo "     Aucun médicament lié\n";
    }
}

echo "   Total services médicament : {$servicesMedicaments->count()}\n";

// 4. Identifier les médicaments sans service
echo "\n4. Médicaments sans service créé :\n";
$medicamentsSansService = Pharmacie::where('statut', 'actif')
    ->whereNotExists(function ($query) {
        $query->select(DB::raw(1))
            ->from('services')
            ->whereRaw('services.pharmacie_id = pharmacies.id')
            ->where('services.type_service', 'medicament');
    })
    ->get();

foreach ($medicamentsSansService as $medicament) {
    echo "   - {$medicament->nom_medicament} (ID: {$medicament->id})\n";
}

echo "   Médicaments sans service : {$medicamentsSansService->count()}\n";

// 5. Créer les services manquants
echo "\n5. Création des services manquants :\n";
$servicesCrees = 0;

foreach ($medicamentsSansService as $medicament) {
    $service = new Service();
    $service->nom = "Vente {$medicament->nom_medicament}";
    $service->type_service = 'medicament';
    $service->pharmacie_id = $medicament->id;
    $service->prix = $medicament->prix_vente;
    $service->quantite_defaut = $medicament->quantite;
    $service->description = "Service de vente pour {$medicament->nom_medicament}";
    $service->statut = 'actif';

    if ($service->save()) {
        echo "   ✓ Service créé pour {$medicament->nom_medicament}\n";
        $servicesCrees++;
    } else {
        echo "   ✗ Erreur lors de la création du service pour {$medicament->nom_medicament}\n";
    }
}

echo "   Services créés : {$servicesCrees}\n";

// 6. Vérification finale
echo "\n6. Vérification finale :\n";
$servicesFinaux = Service::where('type_service', 'medicament')->with('pharmacie')->get();
echo "   Total services médicament après création : {$servicesFinaux->count()}\n";

foreach ($servicesFinaux as $service) {
    echo "   - Service ID: {$service->id}\n";
    echo "     Nom affiché: Pharmacie\n";
    echo "     Observation affichée: " . ($service->pharmacie ? $service->pharmacie->nom_medicament : 'Aucun') . "\n";
}

echo "\n=== Test terminé ===\n";
echo "\nMaintenant sur http://localhost:8000/services, tu devrais voir tous les médicaments de la pharmacie !\n";
echo "\n";
