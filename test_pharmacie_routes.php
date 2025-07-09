<?php

require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pharmacie;
use App\Models\Service;
use App\Models\Examen;
use App\Models\Caisse;

echo "=== Test des Routes Pharmacie avec \$id ===\n\n";

// 1. Vérifier qu'il y a des médicaments dans la base
$pharmacies = Pharmacie::all();
echo "1. Nombre de médicaments dans la base : " . $pharmacies->count() . "\n";

if ($pharmacies->count() > 0) {
    $pharmacie = $pharmacies->first();
    echo "   Premier médicament : {$pharmacie->nom_medicament} (ID: {$pharmacie->id})\n";

    // 2. Tester les routes avec $id
    echo "\n2. Test des routes avec \$id :\n";
    echo "   - Route show : pharmacie/{$pharmacie->id}\n";
    echo "   - Route edit : pharmacie/{$pharmacie->id}/edit\n";
    echo "   - Route update : pharmacie/{$pharmacie->id} (PUT)\n";
    echo "   - Route destroy : pharmacie/{$pharmacie->id} (DELETE)\n";

    // 3. Vérifier les relations
    echo "\n3. Relations du médicament :\n";
    echo "   - Services liés : " . $pharmacie->services->count() . "\n";

    if ($pharmacie->services->count() > 0) {
        echo "   - Services :\n";
        foreach ($pharmacie->services as $service) {
            echo "     * {$service->nom_service} (Prix: {$service->prix} MRU)\n";
        }
    }

    // 4. Tester la création d'un service lié
    echo "\n4. Test de création d'un service lié :\n";
    $service = new Service();
    $service->nom_service = "Test Service pour " . $pharmacie->nom_medicament;
    $service->prix = $pharmacie->prix_vente;
    $service->type = 'medicament';
    $service->pharmacie_id = $pharmacie->id;
    $service->description = "Service de test pour le médicament";
    $service->statut = 'actif';

    if ($service->save()) {
        echo "   ✓ Service créé avec succès (ID: {$service->id})\n";

        // 5. Vérifier que la relation fonctionne
        $pharmacie->refresh();
        echo "   - Nombre de services après création : " . $pharmacie->services->count() . "\n";

        // 6. Tester la suppression du service de test
        $service->delete();
        echo "   ✓ Service de test supprimé\n";
    } else {
        echo "   ✗ Erreur lors de la création du service\n";
    }
} else {
    echo "   Aucun médicament trouvé. Création d'un médicament de test...\n";

    $pharmacie = new Pharmacie();
    $pharmacie->nom_medicament = "Paracétamol Test";
    $pharmacie->prix_achat = 50;
    $pharmacie->prix_vente = 100;
    $pharmacie->prix_unitaire = 100;
    $pharmacie->quantite = 1;
    $pharmacie->stock = 50;
    $pharmacie->description = "Médicament de test";
    $pharmacie->categorie = "Antalgiques";
    $pharmacie->fournisseur = "Test Pharma";
    $pharmacie->statut = 'actif';

    if ($pharmacie->save()) {
        echo "   ✓ Médicament de test créé (ID: {$pharmacie->id})\n";
        echo "   - Route show : pharmacie/{$pharmacie->id}\n";
        echo "   - Route edit : pharmacie/{$pharmacie->id}/edit\n";
    } else {
        echo "   ✗ Erreur lors de la création du médicament de test\n";
    }
}

// 7. Vérifier les routes dans le fichier web.php
echo "\n5. Vérification des routes dans web.php :\n";
$routesContent = file_get_contents('routes/web.php');
if (strpos($routesContent, 'Route::resource(\'pharmacie\'') !== false) {
    echo "   ✓ Routes pharmacie trouvées dans web.php\n";
} else {
    echo "   ✗ Routes pharmacie manquantes dans web.php\n";
}

echo "\n=== Test terminé ===\n";
echo "\nPour tester manuellement :\n";
echo "1. http://localhost:8000/pharmacie\n";
if ($pharmacies->count() > 0) {
    $first = $pharmacies->first();
    echo "2. http://localhost:8000/pharmacie/{$first->id}\n";
    echo "3. http://localhost:8000/pharmacie/{$first->id}/edit\n";
}
echo "\n";
