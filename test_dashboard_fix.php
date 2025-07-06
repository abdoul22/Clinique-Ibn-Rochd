<?php

require_once 'vendor/autoload.php';

use App\Models\ModePaiement;
use App\Models\Depense;
use App\Models\Caisse;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test du dashboard des modes de paiement ===\n\n";

try {
    // 0. Vérifier ou créer une caisse
    $caisse = Caisse::first();
    if (!$caisse) {
        $caisse = Caisse::create([
            'nom' => 'Caisse principale',
            'total' => 0
        ]);
        echo "Caisse principale créée avec l'ID : {$caisse->id}\n";
    } else {
        echo "Caisse existante trouvée avec l'ID : {$caisse->id}\n";
    }

    // Test 1: Récupération des types uniques
    echo "1. Test des types de modes de paiement uniques...\n";
    $typesModes = ModePaiement::distinct()->pluck('type')->toArray();
    echo "Types trouvés : " . implode(', ', $typesModes) . "\n";
    echo "Nombre de types uniques : " . count($typesModes) . "\n";

    // Test 2: Vérification des doublons
    echo "\n2. Vérification des doublons...\n";
    $allModes = ModePaiement::all();
    echo "Nombre total d'enregistrements : " . $allModes->count() . "\n";

    $typesCount = [];
    foreach ($allModes as $mode) {
        $typesCount[$mode->type] = ($typesCount[$mode->type] ?? 0) + 1;
    }

    foreach ($typesCount as $type => $count) {
        echo "  - $type : $count enregistrement(s)\n";
    }

    // Test 3: Vérifier les modes de paiement manquants
    echo "\n3. Vérification des modes de paiement manquants...\n";
    $modesAttendus = ['espèces', 'bankily', 'masrivi', 'sedad'];
    $modesManquants = array_diff($modesAttendus, $typesModes);

    if (!empty($modesManquants)) {
        echo "Modes manquants : " . implode(', ', $modesManquants) . "\n";
        echo "Ajout des modes manquants...\n";

        foreach ($modesManquants as $type) {
            // Vérifier s'il existe déjà un enregistrement
            $existant = ModePaiement::where('type', $type)->first();
            if (!$existant) {
                ModePaiement::create([
                    'type' => $type,
                    'montant' => 0,
                    'caisse_id' => $caisse->id
                ]);
                echo "  - Ajouté : $type\n";
            } else {
                echo "  - Déjà présent : $type\n";
            }
        }
    } else {
        echo "Tous les modes de paiement sont présents.\n";
    }

    // Test 4: Récupération mise à jour
    echo "\n4. Récupération mise à jour...\n";
    $typesModesUpdated = ModePaiement::distinct()->pluck('type')->toArray();
    echo "Types trouvés après mise à jour : " . implode(', ', $typesModesUpdated) . "\n";

    // Test 5: Calcul des données du dashboard
    echo "\n5. Calcul des données du dashboard...\n";
    $data = [];

    foreach ($typesModesUpdated as $type) {
        $montantTotal = ModePaiement::where('type', $type)->sum('montant');
        $sortie = Depense::where('mode_paiement_id', $type)->sum('montant');
        $solde = $montantTotal - $sortie;

        $data[] = [
            'mode' => ucfirst($type),
            'entree' => 0,
            'sortie' => $sortie,
            'solde' => $solde
        ];

        echo "  - $type : Total=$montantTotal, Sortie=$sortie, Solde=$solde\n";
    }

    echo "\n✅ Dashboard préparé avec " . count($data) . " modes de paiement\n";
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== Test terminé ===\n";
