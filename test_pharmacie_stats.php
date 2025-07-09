<?php

require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pharmacie;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

echo "=== Test des Nouvelles Statistiques Pharmacie ===\n\n";

// 1. Vérifier les statistiques globales
echo "1. Statistiques globales de la pharmacie :\n";
$stats = Pharmacie::selectRaw('
    COUNT(*) as total_medicaments,
    SUM(stock) as total_stock,
    SUM(stock * prix_achat) as valeur_stock_achat,
    SUM(stock * prix_vente) as valeur_stock_vente,
    SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as medicaments_rupture,
    SUM(CASE WHEN stock <= 10 AND stock > 0 THEN 1 ELSE 0 END) as medicaments_faible_stock,
    AVG(prix_vente - prix_achat) as marge_moyenne
')->first();

echo "   - Total médicaments : " . ($stats->total_medicaments ?? 0) . "\n";
echo "   - Stock total : " . ($stats->total_stock ?? 0) . " unités\n";
echo "   - Valeur stock (achat) : " . number_format($stats->valeur_stock_achat ?? 0, 0, ',', ' ') . " MRU\n";
echo "   - Valeur stock (vente) : " . number_format($stats->valeur_stock_vente ?? 0, 0, ',', ' ') . " MRU\n";
echo "   - Médicaments en rupture : " . ($stats->medicaments_rupture ?? 0) . "\n";
echo "   - Médicaments stock faible : " . ($stats->medicaments_faible_stock ?? 0) . "\n";
echo "   - Marge moyenne : " . number_format($stats->marge_moyenne ?? 0, 0, ',', ' ') . " MRU\n";

// 2. Tester les filtres
echo "\n2. Test des filtres :\n";

// Filtre par statut actif
$actifs = Pharmacie::where('statut', 'actif')->count();
echo "   - Médicaments actifs : {$actifs}\n";

// Filtre par stock en rupture
$rupture = Pharmacie::where('stock', 0)->count();
echo "   - Médicaments en rupture : {$rupture}\n";

// Filtre par stock faible
$faible = Pharmacie::where('stock', '<=', 10)->where('stock', '>', 0)->count();
echo "   - Médicaments stock faible : {$faible}\n";

// 3. Statistiques détaillées d'un médicament
echo "\n3. Statistiques détaillées d'un médicament :\n";
$pharmacie = Pharmacie::first();
if ($pharmacie) {
    echo "   Médicament : {$pharmacie->nom_medicament}\n";
    echo "   - Stock : {$pharmacie->stock}\n";
    echo "   - Prix achat : " . number_format($pharmacie->prix_achat, 0, ',', ' ') . " MRU\n";
    echo "   - Prix vente : " . number_format($pharmacie->prix_vente, 0, ',', ' ') . " MRU\n";
    echo "   - Marge bénéficiaire : " . number_format($pharmacie->marge_beneficiaire, 0, ',', ' ') . " MRU\n";

    $valeurStockAchat = $pharmacie->stock * $pharmacie->prix_achat;
    $valeurStockVente = $pharmacie->stock * $pharmacie->prix_vente;
    $pourcentageMarge = $pharmacie->prix_achat > 0 ? (($pharmacie->prix_vente - $pharmacie->prix_achat) / $pharmacie->prix_achat) * 100 : 0;
    $ventesPotentielles = $pharmacie->stock * $pharmacie->prix_vente;
    $beneficePotentiel = $pharmacie->stock * ($pharmacie->prix_vente - $pharmacie->prix_achat);

    echo "   - Valeur stock (achat) : " . number_format($valeurStockAchat, 0, ',', ' ') . " MRU\n";
    echo "   - Valeur stock (vente) : " . number_format($valeurStockVente, 0, ',', ' ') . " MRU\n";
    echo "   - % Marge : " . number_format($pourcentageMarge, 1, ',', ' ') . "%\n";
    echo "   - Ventes potentielles : " . number_format($ventesPotentielles, 0, ',', ' ') . " MRU\n";
    echo "   - Bénéfice potentiel : " . number_format($beneficePotentiel, 0, ',', ' ') . " MRU\n";

    $statutStock = $pharmacie->stock == 0 ? 'Rupture' : ($pharmacie->stock <= 10 ? 'Faible' : 'OK');
    echo "   - Statut stock : {$statutStock}\n";

    // Services liés
    $servicesCount = $pharmacie->services->count();
    echo "   - Services liés : {$servicesCount}\n";
}

// 4. Test de recherche
echo "\n4. Test de recherche :\n";
$recherche = Pharmacie::where('nom_medicament', 'like', '%paracétamol%')->count();
echo "   - Médicaments contenant 'paracétamol' : {$recherche}\n";

$categorie = Pharmacie::where('categorie', 'like', '%antalgique%')->count();
echo "   - Médicaments catégorie 'antalgique' : {$categorie}\n";

// 5. Top 5 des médicaments par valeur de stock
echo "\n5. Top 5 des médicaments par valeur de stock :\n";
$topStock = Pharmacie::selectRaw('*, (stock * prix_vente) as valeur_stock')
    ->orderBy('valeur_stock', 'desc')
    ->limit(5)
    ->get();

foreach ($topStock as $index => $med) {
    echo "   " . ($index + 1) . ". {$med->nom_medicament} - " . number_format($med->valeur_stock, 0, ',', ' ') . " MRU\n";
}

echo "\n=== Test terminé ===\n";
echo "\nPour tester manuellement :\n";
echo "1. http://localhost:8000/pharmacie (avec les nouvelles statistiques)\n";
if ($pharmacie) {
    echo "2. http://localhost:8000/pharmacie/{$pharmacie->id} (statistiques détaillées)\n";
}
echo "\n";
