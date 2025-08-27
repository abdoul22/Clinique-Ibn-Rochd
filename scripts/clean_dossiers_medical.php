<?php

/**
 * Script Tinker pour supprimer les dossiers médicaux en production
 *
 * Usage: php artisan tinker < scripts/clean_dossiers_medical.php
 *
 * ⚠️  ATTENTION: Ce script supprime DÉFINITIVEMENT les dossiers médicaux
 * Assurez-vous d'avoir une sauvegarde avant d'exécuter ce script !
 */

echo "🏥 SCRIPT DE NETTOYAGE DES DOSSIERS MÉDICAUX\n";
echo "==========================================\n\n";

// Vérification de l'environnement
$env = app()->environment();
echo "📍 Environnement détecté: {$env}\n";

if ($env === 'production') {
    echo "⚠️  VOUS ÊTES EN PRODUCTION!\n";
    echo "   Assurez-vous d'avoir une sauvegarde complète de la base de données.\n\n";
}

// Importation des modèles nécessaires
use App\Models\DossierMedical;
use App\Models\GestionPatient;
use App\Models\Caisse;
use App\Models\Hospitalisation;
use App\Models\RendezVous;

try {
    // 1. Statistiques avant suppression
    echo "📊 STATISTIQUES AVANT SUPPRESSION:\n";
    echo "   Dossiers médicaux: " . DossierMedical::count() . "\n";
    echo "   Patients: " . GestionPatient::count() . "\n";
    echo "   Factures/Caisses: " . Caisse::count() . "\n";
    echo "   Hospitalisations: " . Hospitalisation::count() . "\n";
    echo "   Rendez-vous: " . RendezVous::count() . "\n\n";

    // 2. Vérification des relations critiques
    echo "🔍 VÉRIFICATION DES RELATIONS:\n";

    $dossiersAvecCaisses = DossierMedical::whereHas('caisses')->count();
    $dossiersAvecHospitalisations = DossierMedical::whereHas('hospitalisations')->count();
    $dossiersAvecRdv = DossierMedical::whereHas('rendezVous')->count();

    echo "   Dossiers liés à des factures: {$dossiersAvecCaisses}\n";
    echo "   Dossiers liés à des hospitalisations: {$dossiersAvecHospitalisations}\n";
    echo "   Dossiers liés à des rendez-vous: {$dossiersAvecRdv}\n\n";

    if ($dossiersAvecCaisses > 0 || $dossiersAvecHospitalisations > 0 || $dossiersAvecRdv > 0) {
        echo "❌ ERREUR: Des dossiers médicaux sont liés à d'autres modules!\n";
        echo "   La suppression pourrait affecter:\n";
        if ($dossiersAvecCaisses > 0) echo "   - Les factures/caisses\n";
        if ($dossiersAvecHospitalisations > 0) echo "   - Les hospitalisations\n";
        if ($dossiersAvecRdv > 0) echo "   - Les rendez-vous\n";
        echo "\n   Voulez-vous continuer quand même ? (DANGEREUX)\n";
        echo "   Tapez 'OUI_JE_COMPRENDS_LES_RISQUES' pour continuer: ";

        // En mode script, on s'arrête ici pour la sécurité
        echo "\n❌ ARRÊT DU SCRIPT POUR SÉCURITÉ\n";
        echo "   Modifiez le script si vous voulez vraiment supprimer malgré les relations.\n";
        exit(1);
    }

    // 3. Suppression sécurisée (seulement si pas de relations critiques)
    echo "✅ AUCUNE RELATION CRITIQUE DÉTECTÉE\n";
    echo "🗑️  DÉBUT DE LA SUPPRESSION...\n\n";

    DB::transaction(function () {
        // Supprimer les dossiers médicaux sans relations
        $dossiersSupprimes = 0;

        DossierMedical::chunk(100, function ($dossiers) use (&$dossiersSupprimes) {
            foreach ($dossiers as $dossier) {
                // Double vérification avant suppression
                if (
                    !$dossier->caisses()->exists() &&
                    !$dossier->hospitalisations()->exists() &&
                    !$dossier->rendezVous()->exists()
                ) {

                    $dossier->delete();
                    $dossiersSupprimes++;

                    if ($dossiersSupprimes % 50 === 0) {
                        echo "   Supprimés: {$dossiersSupprimes} dossiers...\n";
                    }
                }
            }
        });

        echo "✅ Suppression terminée: {$dossiersSupprimes} dossiers supprimés\n\n";
    });

    // 4. Statistiques après suppression
    echo "📊 STATISTIQUES APRÈS SUPPRESSION:\n";
    echo "   Dossiers médicaux: " . DossierMedical::count() . "\n";
    echo "   Patients: " . GestionPatient::count() . " (inchangé)\n";
    echo "   Factures/Caisses: " . Caisse::count() . " (inchangé)\n";
    echo "   Hospitalisations: " . Hospitalisation::count() . " (inchangé)\n";
    echo "   Rendez-vous: " . RendezVous::count() . " (inchangé)\n\n";

    echo "🎉 NETTOYAGE TERMINÉ AVEC SUCCÈS!\n";
    echo "   Les autres modules n'ont pas été affectés.\n";
} catch (Exception $e) {
    echo "❌ ERREUR LORS DE L'EXÉCUTION:\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n🔄 La transaction a été annulée, aucune donnée n'a été supprimée.\n";
}

echo "\n📝 RECOMMANDATIONS POST-NETTOYAGE:\n";
echo "   1. Vérifiez que l'application fonctionne correctement\n";
echo "   2. Testez les modules patients, factures, hospitalisations\n";
echo "   3. Si des erreurs apparaissent, restaurez la sauvegarde\n";
echo "   4. Lancez une optimisation de la base de données si nécessaire\n";

echo "\n✨ Script terminé.\n";
