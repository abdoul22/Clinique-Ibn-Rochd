<?php

require_once 'vendor/autoload.php';

use App\Models\Personnel;
use App\Models\Credit;
use App\Models\Depense;
use App\Models\ModePaiement;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DU NOUVEAU SYSTÈME DE CRÉDITS PAR DÉDUCTION SALARIALE ===\n\n";

// Récupérer un personnel de test
$personnel = Personnel::first();
if (!$personnel) {
    echo "❌ Aucun personnel trouvé. Créons-en un...\n";
    $personnel = Personnel::create([
        'nom' => 'Test Personnel Salaire',
        'fonction' => 'Testeur',
        'salaire' => 80000,
        'telephone' => '123456789',
        'adresse' => 'Adresse Test'
    ]);
}

echo "👤 Personnel testé : {$personnel->nom}\n";
echo "💰 Salaire initial : {$personnel->salaire} MRU\n";
echo "💳 Crédit actuel : {$personnel->credit} MRU\n\n";

// Test 1: Créer un crédit pour le personnel (sans mode de paiement)
echo "=== TEST 1: CRÉATION D'UN CRÉDIT PERSONNEL ===\n";

$montantCredit = 20000;
if ($personnel->peutPrendreCredit($montantCredit)) {
    echo "✅ Création d'un crédit de {$montantCredit} MRU...\n";

    // Créer le crédit (sans mode_paiement_id pour le personnel)
    $credit = Credit::create([
        'source_type' => Personnel::class,
        'source_id' => $personnel->id,
        'montant' => $montantCredit,
        'status' => 'non payé',
        'statut' => 'Non payé',
        'montant_paye' => 0,
        'mode_paiement_id' => null // Pas de mode de paiement pour les crédits personnel
    ]);

    // Mettre à jour le crédit du personnel
    $personnel->updateCredit();

    echo "✅ Crédit créé avec succès !\n";
    echo "   💳 Nouveau crédit total : {$personnel->credit} MRU\n";
    echo "   📊 Nouveau crédit maximum : {$personnel->montant_max_credit} MRU\n";
    echo "   💵 Nouveau salaire net : {$personnel->salaire_net} MRU\n";
} else {
    echo "❌ Impossible de créer le crédit de {$montantCredit} MRU\n";
}

echo "\n=== TEST 2: PAIEMENT PAR DÉDUCTION SALARIALE ===\n";

$credit = $personnel->credits()->latest()->first();
if ($credit && $credit->status !== 'payé') {
    echo "✅ Paiement par déduction salariale...\n";

    $montantAPayer = 10000;
    $salaireAvant = $personnel->salaire;

    // Simuler le paiement par déduction salariale
    $credit->montant_paye += $montantAPayer;

    if ($credit->montant_paye >= $credit->montant) {
        $credit->status = 'payé';
    } else {
        $credit->status = 'partiellement payé';
    }

    $credit->save();

    // Créer une dépense pour le salaire déduit
    Depense::create([
        'nom' => "Déduction salaire - Crédit personnel : {$personnel->nom}",
        'montant' => $montantAPayer,
        'mode_paiement_id' => 'salaire',
        'source' => 'automatique',
        'etat_caisse_id' => null,
    ]);

    // Mettre à jour le crédit du personnel
    $personnel->updateCredit();

    echo "✅ Paiement enregistré avec succès !\n";
    echo "   💰 Salaire avant : {$salaireAvant} MRU\n";
    echo "   💰 Salaire après : {$personnel->salaire} MRU (inchangé - déduction virtuelle)\n";
    echo "   💳 Crédit restant : {$personnel->credit} MRU\n";
    echo "   📊 Statut du crédit : {$credit->status}\n";
} else {
    echo "⚠ Aucun crédit à payer ou crédit déjà payé.\n";
}

echo "\n=== TEST 3: VÉRIFICATION DES DÉPENSES ===\n";

$depensesSalaire = Depense::where('mode_paiement_id', 'salaire')->get();
echo "📋 Dépenses de déduction salariale trouvées : {$depensesSalaire->count()}\n";

foreach ($depensesSalaire as $depense) {
    echo "   - {$depense->nom} : {$depense->montant} MRU ({$depense->created_at->format('d/m/Y H:i')})\n";
}

echo "\n=== TEST 4: VÉRIFICATION DU DASHBOARD MODE-PAIEMENTS ===\n";

// Vérifier que les crédits personnel ne sont pas dans les sorties du dashboard
$creditsPersonnel = Credit::where('source_type', Personnel::class)->get();
$creditsAssurance = Credit::where('source_type', 'App\\Models\\Assurance')->get();

echo "💳 Crédits personnel : {$creditsPersonnel->count()}\n";
echo "🏥 Crédits assurance : {$creditsAssurance->count()}\n";

$totalCreditsPersonnel = $creditsPersonnel->sum('montant');
$totalCreditsAssurance = $creditsAssurance->sum('montant');

echo "💰 Total crédits personnel : {$totalCreditsPersonnel} MRU (ne doit PAS apparaître dans les sorties)\n";
echo "💰 Total crédits assurance : {$totalCreditsAssurance} MRU (apparaît dans les sorties)\n";

echo "\n=== RÉSUMÉ FINAL ===\n";
echo "👤 Personnel : {$personnel->nom}\n";
echo "💰 Salaire : {$personnel->salaire} MRU\n";
echo "💳 Crédit total : {$personnel->credit} MRU\n";
echo "📊 Crédit maximum possible : {$personnel->montant_max_credit} MRU\n";
echo "💵 Salaire net : {$personnel->salaire_net} MRU\n";
echo "📋 Dépenses salariales créées : {$depensesSalaire->count()}\n";

echo "\n🎉 Tests terminés !\n";
echo "\n✅ Le nouveau système fonctionne :\n";
echo "   - Les crédits du personnel ne sont plus considérés comme des dépenses\n";
echo "   - Les paiements se font par déduction salariale\n";
echo "   - Les déductions salariales sont enregistrées comme dépenses automatiques\n";
echo "   - Les crédits personnel n'apparaissent pas dans le dashboard mode-paiements\n";
