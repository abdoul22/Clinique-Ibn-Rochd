<?php

require_once 'vendor/autoload.php';

use App\Models\Caisse;
use App\Models\Depense;
use App\Models\ModePaiement;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test des recettes et dépenses ===\n\n";

try {
    // Test 1: Vérifier les recettes (caisse)
    echo "1. Vérification des recettes (caisse)...\n";
    $recettes = Caisse::with(['mode_paiements', 'patient'])->get();
    echo "Nombre total de recettes : " . $recettes->count() . "\n";

    $totalRecettes = 0;
    foreach ($recettes as $recette) {
        $modePaiement = $recette->mode_paiements->first();
        $modeType = $modePaiement ? $modePaiement->type : 'N/A';
        $patientNom = $recette->patient ? $recette->patient->nom : 'Patient inconnu';

        echo "  - Facture #{$recette->numero_facture} : {$recette->total} MRU ({$modeType}) - {$patientNom}\n";
        $totalRecettes += $recette->total;
    }
    echo "Total recettes : {$totalRecettes} MRU\n";

    // Test 2: Vérifier les dépenses
    echo "\n2. Vérification des dépenses...\n";
    $depenses = Depense::all();
    echo "Nombre total de dépenses : " . $depenses->count() . "\n";

    $totalDepenses = 0;
    foreach ($depenses as $depense) {
        echo "  - {$depense->nom} : {$depense->montant} MRU ({$depense->mode_paiement_id})\n";
        $totalDepenses += $depense->montant;
    }
    echo "Total dépenses : {$totalDepenses} MRU\n";

    // Test 3: Calculer le solde net
    echo "\n3. Calcul du solde net...\n";
    $soldeNet = $totalRecettes - $totalDepenses;
    echo "Solde net : {$soldeNet} MRU\n";

    // Test 4: Vérifier les modes de paiement
    echo "\n4. Vérification des modes de paiement...\n";
    $modes = ModePaiement::distinct()->pluck('type')->toArray();
    echo "Modes de paiement disponibles : " . implode(', ', $modes) . "\n";

    foreach ($modes as $type) {
        $recettesMode = Caisse::whereHas('mode_paiements', function($query) use ($type) {
            $query->where('type', $type);
        })->sum('total');

        $depensesMode = Depense::where('mode_paiement_id', $type)->sum('montant');
        $soldeMode = $recettesMode - $depensesMode;

        echo "  - {$type} : Recettes={$recettesMode}, Dépenses={$depensesMode}, Solde={$soldeMode}\n";
    }

    echo "\n✅ Test terminé avec succès\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== Test terminé ===\n";
