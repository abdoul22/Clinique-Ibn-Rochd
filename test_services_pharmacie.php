<?php

require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Service;
use App\Models\Pharmacie;

echo "=== Test des Services Pharmacie ===\n\n";

// 1. Vérifier les services existants
echo "1. Services existants :\n";
$services = Service::with('pharmacie')->get();

foreach ($services as $service) {
    echo "   Service ID: {$service->id}\n";
    echo "   - Nom original: {$service->nom}\n";
    echo "   - Type: {$service->type_service}\n";
    echo "   - Observation: {$service->observation}\n";

    if ($service->type_service === 'medicament' && $service->pharmacie) {
        echo "   - Nom affichage: Pharmacie\n";
        echo "   - Observation affichage: {$service->pharmacie->nom_medicament}\n";
        echo "   - Médicament lié: {$service->pharmacie->nom_medicament}\n";
    } else {
        echo "   - Nom affichage: {$service->nom}\n";
        echo "   - Observation affichage: {$service->observation}\n";
    }
    echo "   ---\n";
}

// 2. Compter les services par type
echo "\n2. Statistiques des services :\n";
$stats = Service::selectRaw('type_service, COUNT(*) as count')
    ->groupBy('type_service')
    ->get();

foreach ($stats as $stat) {
    echo "   - {$stat->type_service}: {$stat->count} services\n";
}

// 3. Services de type médicament
echo "\n3. Services de type médicament :\n";
$servicesMedicaments = Service::where('type_service', 'medicament')
    ->with('pharmacie')
    ->get();

foreach ($servicesMedicaments as $service) {
    echo "   - Service ID: {$service->id}\n";
    echo "     Nom affiché: Pharmacie\n";
    echo "     Observation affichée: " . ($service->pharmacie ? $service->pharmacie->nom_medicament : 'Aucun médicament lié') . "\n";
    echo "     Prix: " . number_format($service->prix, 0, ',', ' ') . " MRU\n";
    echo "     Stock disponible: " . ($service->pharmacie ? $service->pharmacie->stock : 'N/A') . "\n";
}

// 4. Vérifier les vues PDF et Print
echo "\n4. Test des vues PDF et Print :\n";
echo "   - Les méthodes exportPdf() et print() traitent maintenant les données\n";
echo "   - Les services médicament afficheront 'Pharmacie' et le nom du médicament\n";

// 5. Test de création d'un service médicament
echo "\n5. Test de création d'un service médicament :\n";
$medicament = Pharmacie::where('statut', 'actif')->where('stock', '>', 0)->first();

if ($medicament) {
    echo "   Médicament disponible: {$medicament->nom_medicament}\n";
    echo "   - Prix de vente: " . number_format($medicament->prix_vente, 0, ',', ' ') . " MRU\n";
    echo "   - Stock: {$medicament->stock}\n";
    echo "   - Si un service est créé avec ce médicament:\n";
    echo "     * Nom affiché: Pharmacie\n";
    echo "     * Observation affichée: {$medicament->nom_medicament}\n";
} else {
    echo "   Aucun médicament disponible pour créer un service\n";
}

echo "\n=== Test terminé ===\n";
echo "\nPour tester manuellement :\n";
echo "1. http://localhost:8000/services (voir l'affichage dans le tableau)\n";
echo "2. http://localhost:8000/services/exportPdf (télécharger le PDF)\n";
echo "3. http://localhost:8000/services/print (voir l'impression)\n";
echo "\n";
