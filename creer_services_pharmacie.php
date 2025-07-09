<?php

require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Service;
use App\Models\Pharmacie;

echo "=== Création des Services pour tous les Médicaments ===\n\n";

// 1. Récupérer tous les médicaments actifs
$medicaments = Pharmacie::where('statut', 'actif')->get();
echo "Médicaments trouvés : {$medicaments->count()}\n";

// 2. Pour chaque médicament, créer un service s'il n'existe pas
$servicesCrees = 0;

foreach ($medicaments as $medicament) {
    // Vérifier si un service existe déjà pour ce médicament
    $serviceExistant = Service::where('pharmacie_id', $medicament->id)
        ->where('type_service', 'medicament')
        ->first();

    if (!$serviceExistant) {
        // Créer un nouveau service
        $service = new Service();
        $service->nom = "Vente {$medicament->nom_medicament}";
        $service->type_service = 'medicament';
        $service->pharmacie_id = $medicament->id;
        $service->prix = $medicament->prix_vente;
        $service->quantite_defaut = $medicament->quantite;
        $service->description = "Service de vente pour {$medicament->nom_medicament}";
        $service->statut = 'actif';

        if ($service->save()) {
            echo "✓ Service créé pour : {$medicament->nom_medicament}\n";
            $servicesCrees++;
        } else {
            echo "✗ Erreur pour : {$medicament->nom_medicament}\n";
        }
    } else {
        echo "- Service existe déjà pour : {$medicament->nom_medicament}\n";
    }
}

echo "\nServices créés : {$servicesCrees}\n";

// 3. Vérification finale
$totalServices = Service::where('type_service', 'medicament')->count();
echo "Total services médicament : {$totalServices}\n";

echo "\n=== Terminé ===\n";
echo "Maintenant va sur http://localhost:8000/services pour voir tous les médicaments !\n";
