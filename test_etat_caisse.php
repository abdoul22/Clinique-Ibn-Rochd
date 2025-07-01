<?php

require_once 'vendor/autoload.php';

use App\Models\Caisse;
use App\Models\EtatCaisse;
use App\Models\GestionPatient;
use App\Models\Medecin;
use App\Models\Examen;
use App\Models\Service;
use App\Models\Assurance;
use App\Models\ModePaiement;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AJOUT DE DONNÉES DE TEST POUR ÉTATS DE CAISSE ===\n\n";

// Vérifier si on a déjà des données
if (EtatCaisse::count() > 0) {
    echo "Des états de caisse existent déjà. Suppression...\n";
    EtatCaisse::truncate();
}

// Créer un patient de test
$patient = GestionPatient::first();
if (!$patient) {
    echo "Création d'un patient de test...\n";
    $patient = GestionPatient::create([
        'nom' => 'Patient Test',
        'prenom' => 'État Caisse',
        'date_naissance' => '1990-01-01',
        'telephone' => '123456789',
        'adresse' => 'Adresse Test'
    ]);
}

// Créer un médecin de test
$medecin = Medecin::first();
if (!$medecin) {
    echo "Création d'un médecin de test...\n";
    $medecin = Medecin::create([
        'nom' => 'Dr. Test',
        'prenom' => 'Médecin',
        'specialite' => 'Généraliste',
        'telephone' => '987654321'
    ]);
}

// Créer un service de test
$service = Service::first();
if (!$service) {
    echo "Création d'un service de test...\n";
    $service = Service::create([
        'nom' => 'Service Test',
        'prix' => 3000
    ]);
}

// Créer un examen de test
$examen = Examen::first();
if (!$examen) {
    echo "Création d'un examen de test...\n";
    $examen = Examen::create([
        'nom' => 'Examen Test',
        'idsvc' => $service->id,
        'tarif' => 5000,
        'part_cabinet' => 3000,
        'part_medecin' => 2000
    ]);
}

// Créer une assurance de test
$assurance = Assurance::first();
if (!$assurance) {
    echo "Création d'une assurance de test...\n";
    $assurance = Assurance::create([
        'nom' => 'Assurance Test',
        'pourcentage' => 80
    ]);
}

// Créer une caisse de test avec plusieurs modes de paiement
echo "Création d'une caisse de test avec états de caisse...\n";

$caisse = Caisse::create([
    'gestion_patient_id' => $patient->id,
    'medecin_id' => $medecin->id,
    'examen_id' => $examen->id,
    'service_id' => $service->id,
    'assurance_id' => $assurance->id,
    'date_examen' => now(),
    'total' => 15000, // Total de la facture
    'nom_caissier' => 'Caissier Test',
    'couverture' => 12000, // Couverture assurance
    'numero_facture' => 1001
]);

// Ajouter plusieurs modes de paiement à cette caisse
ModePaiement::create([
    'caisse_id' => $caisse->id,
    'type' => 'espèces',
    'montant' => 5000
]);

ModePaiement::create([
    'caisse_id' => $caisse->id,
    'type' => 'bankily',
    'montant' => 7000
]);

ModePaiement::create([
    'caisse_id' => $caisse->id,
    'type' => 'masrivi',
    'montant' => 3000
]);

// Créer un état de caisse pour cette caisse
$etatCaisse = EtatCaisse::create([
    'designation' => 'Consultation avec examen',
    'recette' => 15000, // Recette totale
    'part_medecin' => 5000, // Part du médecin
    'part_clinique' => 10000, // Part de la clinique
    'depense' => 0,
    'caisse_id' => $caisse->id,
    'medecin_id' => $medecin->id
]);

echo "✅ État de caisse créé avec succès!\n";
echo "   - Caisse #{$caisse->id}\n";
echo "   - Recette: {$etatCaisse->recette} MRU\n";
echo "   - Part médecin: {$etatCaisse->part_medecin} MRU\n";
echo "   - Part clinique: {$etatCaisse->part_clinique} MRU\n";
echo "   - Modes de paiement: espèces (5000), bankily (7000), masrivi (3000)\n\n";

// Créer un deuxième état de caisse pour un personnel
$personnel = \App\Models\Personnel::first();
if ($personnel) {
    $caisse2 = Caisse::create([
        'gestion_patient_id' => $patient->id,
        'medecin_id' => $medecin->id,
        'examen_id' => $examen->id,
        'service_id' => $service->id,
        'date_examen' => now(),
        'total' => 8000,
        'nom_caissier' => 'Caissier Test',
        'couverture' => 0,
        'numero_facture' => 1002
    ]);

    ModePaiement::create([
        'caisse_id' => $caisse2->id,
        'type' => 'sedad',
        'montant' => 8000
    ]);

    $etatCaisse2 = EtatCaisse::create([
        'designation' => 'Consultation personnel',
        'recette' => 8000,
        'part_medecin' => 3000,
        'part_clinique' => 5000,
        'depense' => 0,
        'personnel_id' => $personnel->id,
        'caisse_id' => $caisse2->id,
        'medecin_id' => $medecin->id
    ]);

    echo "✅ État de caisse personnel créé!\n";
    echo "   - Personnel: {$personnel->nom}\n";
    echo "   - Recette: {$etatCaisse2->recette} MRU\n";
    echo "   - Mode de paiement: sedad (8000)\n\n";
}

echo "🎉 Données de test créées avec succès!\n";
echo "Vous pouvez maintenant tester le dashboard avec les nouvelles entrées.\n";
