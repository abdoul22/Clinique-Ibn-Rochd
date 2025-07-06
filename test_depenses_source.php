<?php

require_once 'vendor/autoload.php';

use App\Models\Depense;
use App\Models\Personnel;
use App\Models\Credit;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DE L'AFFICHAGE DES SOURCES DANS LES DÉPENSES ===\n\n";

// Récupérer toutes les dépenses
$depenses = Depense::all();

echo "📋 Total des dépenses : {$depenses->count()}\n\n";

echo "=== ANALYSE DES DÉPENSES PAR TYPE ===\n";

$stats = [
    'manuelle' => 0,
    'part_medecin' => 0,
    'deduction_salaire' => 0,
    'autre_automatique' => 0
];

foreach ($depenses as $depense) {
    echo "💸 Dépense #{$depense->id} : {$depense->nom}\n";
    echo "   - Montant : {$depense->montant} MRU\n";
    echo "   - Mode paiement : {$depense->mode_paiement_id}\n";
    echo "   - Source : {$depense->source}\n";

    // Déterminer le type réel
    if ($depense->mode_paiement_id === 'salaire') {
        echo "   - Type détecté : Déduction salariale\n";
        $stats['deduction_salaire']++;
    } elseif ($depense->source === 'automatique' && str_contains($depense->nom, 'Part médecin')) {
        echo "   - Type détecté : Part médecin\n";
        $stats['part_medecin']++;
    } elseif ($depense->source === 'automatique') {
        echo "   - Type détecté : Autre automatique\n";
        $stats['autre_automatique']++;
    } else {
        echo "   - Type détecté : Manuelle\n";
        $stats['manuelle']++;
    }
    echo "\n";
}

echo "=== STATISTIQUES ===\n";
echo "📊 Dépenses manuelles : {$stats['manuelle']}\n";
echo "📊 Parts médecin : {$stats['part_medecin']}\n";
echo "📊 Déductions salariales : {$stats['deduction_salaire']}\n";
echo "📊 Autres automatiques : {$stats['autre_automatique']}\n";

echo "\n=== VÉRIFICATION DES FILTRES ===\n";

// Test du filtre part_medecin
$partMedecin = Depense::where('source', 'automatique')->where('nom', 'like', '%Part médecin%')->get();
echo "🔍 Filtre 'part_medecin' : {$partMedecin->count()} résultats\n";

// Test du filtre deduction_salaire
$deductionSalaire = Depense::where('mode_paiement_id', 'salaire')->get();
echo "🔍 Filtre 'deduction_salaire' : {$deductionSalaire->count()} résultats\n";

// Test du filtre automatique (excluant part_medecin et salaire)
$automatique = Depense::where('source', 'automatique')
    ->where('nom', 'not like', '%Part médecin%')
    ->where('mode_paiement_id', '!=', 'salaire')
    ->get();
echo "🔍 Filtre 'automatique' (autres) : {$automatique->count()} résultats\n";

echo "\n✅ Test terminé !\n";
echo "\nLes modifications apportées :\n";
echo "1. Les déductions salariales affichent 'Déduction salariale' en violet\n";
echo "2. Les parts médecin affichent 'Part médecin' en vert\n";
echo "3. Les autres dépenses automatiques affichent 'Généré automatiquement' en bleu\n";
echo "4. Les dépenses manuelles affichent 'Manuelle' en gris\n";
echo "5. Nouveaux filtres disponibles : 'part_medecin' et 'deduction_salaire'\n";
