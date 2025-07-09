<?php

require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Examen;
use App\Models\Service;
use App\Models\Pharmacie;

echo "=== Test des Examens Pharmacie ===\n\n";

// 1. Vérifier les examens existants
echo "1. Examens existants avec traitement d'affichage :\n";
$examens = Examen::with(['service.pharmacie'])->get();

foreach ($examens as $examen) {
    echo "   Examen ID: {$examen->id}\n";
    echo "   - Nom original: '{$examen->nom}'\n";
    echo "   - Service original: '" . ($examen->service ? $examen->service->nom : 'Aucun') . "'\n";
    echo "   - Type de service: " . ($examen->service ? $examen->service->type_service : 'Aucun') . "\n";

    // Appliquer le même traitement que dans le contrôleur
    if ($examen->service && $examen->service->type_service === 'medicament' && $examen->service->pharmacie) {
        $nom_affichage = $examen->service->pharmacie->nom_medicament;
        $service_affichage = 'Pharmacie';
        echo "   - Nom affiché: '{$nom_affichage}' (en bleu)\n";
        echo "   - Service affiché: '{$service_affichage}' (en vert)\n";
        echo "   - Médicament lié: {$examen->service->pharmacie->nom_medicament}\n";
    } else {
        $nom_affichage = $examen->nom;
        $service_affichage = $examen->service->nom ?? '-';
        echo "   - Nom affiché: '{$nom_affichage}'\n";
        echo "   - Service affiché: '{$service_affichage}'\n";
    }
    echo "   - Tarif: " . number_format($examen->tarif, 0, ',', ' ') . " MRU\n";
    echo "   ---\n";
}

// 2. Vérifier les examens liés aux services de type médicament
echo "\n2. Examens liés aux services de type médicament :\n";
$examensMedicaments = Examen::whereHas('service', function($query) {
    $query->where('type_service', 'medicament');
})->with(['service.pharmacie'])->get();

if ($examensMedicaments->count() > 0) {
    foreach ($examensMedicaments as $examen) {
        echo "   ✓ Examen ID: {$examen->id}\n";
        echo "     Dans la table examens :\n";
        echo "     - Colonne 'nom' affiche: " . ($examen->service->pharmacie ? $examen->service->pharmacie->nom_medicament : 'Aucun médicament') . "\n";
        echo "     - Colonne 'service' affiche: Pharmacie\n";
        echo "     - Tarif: " . number_format($examen->tarif, 0, ',', ' ') . " MRU\n";
    }
} else {
    echo "   Aucun examen lié à un service de type médicament trouvé\n";
}

// 3. Vérifier les autres examens
echo "\n3. Autres examens :\n";
$autresExamens = Examen::whereHas('service', function($query) {
    $query->where('type_service', '!=', 'medicament');
})->with('service')->get();

foreach ($autresExamens as $examen) {
    echo "   - Examen ID: {$examen->id}\n";
    echo "     Nom affiché: '{$examen->nom}'\n";
    echo "     Service affiché: '" . ($examen->service ? $examen->service->nom : 'Aucun') . "'\n";
}

// 4. Test de création d'un examen lié à un service médicament
echo "\n4. Test de création d'un examen lié à un service médicament :\n";
$serviceMedicament = Service::where('type_service', 'medicament')->with('pharmacie')->first();

if ($serviceMedicament && $serviceMedicament->pharmacie) {
    echo "   Service médicament disponible: {$serviceMedicament->nom}\n";
    echo "   - Type: {$serviceMedicament->type_service}\n";
    echo "   - Médicament lié: {$serviceMedicament->pharmacie->nom_medicament}\n";
    echo "   - Si un examen est créé avec ce service:\n";
    echo "     * Nom affiché: {$serviceMedicament->pharmacie->nom_medicament}\n";
    echo "     * Service affiché: Pharmacie\n";
} else {
    echo "   Aucun service de type médicament disponible\n";
}

// 5. Résumé
echo "\n5. Résumé :\n";
$totalExamens = Examen::count();
$examensMedicament = Examen::whereHas('service', function($query) {
    $query->where('type_service', 'medicament');
})->count();
$examensAutres = $totalExamens - $examensMedicament;

echo "   - Total examens : {$totalExamens}\n";
echo "   - Examens liés à des services médicament : {$examensMedicament}\n";
echo "   - Autres examens : {$examensAutres}\n";

echo "\n=== Test terminé avec succès ===\n";
echo "\nRésultat attendu sur http://localhost:8000/examens :\n";
echo "- Les examens liés aux services de type 'medicament' affichent le nom du médicament dans la colonne Nom\n";
echo "- Les examens liés aux services de type 'medicament' affichent 'Pharmacie' dans la colonne Service\n";
echo "- Les autres examens affichent leurs noms et services originaux\n";
echo "\n";
