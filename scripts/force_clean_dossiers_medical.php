<?php

/**
 * Script Tinker AVANCÉ pour supprimer les dossiers médicaux avec gestion des relations
 *
 * Usage: php artisan tinker < scripts/force_clean_dossiers_medical.php
 *
 * ⚠️  DANGER: Ce script peut supprimer des dossiers même s'ils ont des relations
 * UTILISEZ UNIQUEMENT SI VOUS SAVEZ CE QUE VOUS FAITES !
 */

echo "💀 SCRIPT DE NETTOYAGE FORCÉ DES DOSSIERS MÉDICAUX\n";
echo "================================================\n\n";

use App\Models\DossierMedical;
use App\Models\GestionPatient;
use App\Models\Caisse;
use App\Models\Hospitalisation;
use App\Models\RendezVous;

try {
    echo "📊 ANALYSE COMPLÈTE DES DONNÉES:\n";

    $totalDossiers = DossierMedical::count();
    $dossiersAvecCaisses = DossierMedical::whereHas('caisses')->get();
    $dossiersAvecHospitalisations = DossierMedical::whereHas('hospitalisations')->get();
    $dossiersAvecRdv = DossierMedical::whereHas('rendezVous')->get();

    echo "   Total dossiers médicaux: {$totalDossiers}\n";
    echo "   Dossiers avec factures: " . $dossiersAvecCaisses->count() . "\n";
    echo "   Dossiers avec hospitalisations: " . $dossiersAvecHospitalisations->count() . "\n";
    echo "   Dossiers avec rendez-vous: " . $dossiersAvecRdv->count() . "\n\n";

    if ($totalDossiers === 0) {
        echo "✅ Aucun dossier médical à supprimer.\n";
        exit(0);
    }

    // Option 1: Suppression simple (dossiers sans relations)
    echo "🔧 OPTIONS DE SUPPRESSION:\n\n";

    $dossiersSansRelations = DossierMedical::whereDoesntHave('caisses')
        ->whereDoesntHave('hospitalisations')
        ->whereDoesntHave('rendezVous')
        ->count();

    echo "1️⃣  SUPPRESSION SÛRE:\n";
    echo "   Supprimer {$dossiersSansRelations} dossiers sans relations\n";
    echo "   ✅ Sans risque pour les autres modules\n\n";

    if ($dossiersSansRelations > 0) {
        echo "🗑️  Suppression des dossiers sans relations...\n";

        DB::transaction(function () use (&$dossiersSansRelations) {
            $supprimés = 0;
            DossierMedical::whereDoesntHave('caisses')
                ->whereDoesntHave('hospitalisations')
                ->whereDoesntHave('rendezVous')
                ->chunk(100, function ($dossiers) use (&$supprimés) {
                    foreach ($dossiers as $dossier) {
                        $dossier->delete();
                        $supprimés++;

                        if ($supprimés % 25 === 0) {
                            echo "   Supprimés: {$supprimés} dossiers...\n";
                        }
                    }
                });

            echo "✅ {$supprimés} dossiers supprimés en toute sécurité\n\n";
        });
    }

    // Recalculer après suppression sûre
    $dossiersRestants = DossierMedical::count();

    if ($dossiersRestants > 0) {
        echo "⚠️  Il reste {$dossiersRestants} dossiers avec des relations\n\n";

        echo "2️⃣  SUPPRESSION FORCÉE (DANGEREUX):\n";
        echo "   ❌ Supprimer TOUS les dossiers restants\n";
        echo "   ⚠️  Cela peut casser les relations avec:\n";

        if ($dossiersAvecCaisses->count() > 0) {
            echo "       - Factures/Caisses (risque de données orphelines)\n";
        }
        if ($dossiersAvecHospitalisations->count() > 0) {
            echo "       - Hospitalisations (risque d'erreurs)\n";
        }
        if ($dossiersAvecRdv->count() > 0) {
            echo "       - Rendez-vous (risque de dysfonctionnements)\n";
        }

        // Pour la sécurité en production, on ne fait pas la suppression forcée automatiquement
        echo "\n❌ SUPPRESSION FORCÉE DÉSACTIVÉE POUR SÉCURITÉ\n";
        echo "   Si vous voulez vraiment supprimer ces dossiers:\n";
        echo "   1. Sauvegardez la base de données\n";
        echo "   2. Modifiez ce script pour activer la suppression forcée\n";
        echo "   3. Testez d'abord sur un environnement de test\n\n";

        // Code commenté pour la suppression forcée (décommentez si nécessaire)
        /*
        echo "🔥 SUPPRESSION FORCÉE ACTIVÉE...\n";

        DB::transaction(function () {
            $supprimés = 0;
            DossierMedical::chunk(100, function ($dossiers) use (&$supprimés) {
                foreach ($dossiers as $dossier) {
                    $dossier->delete();
                    $supprimés++;

                    if ($supprimés % 25 === 0) {
                        echo "   Supprimés: {$supprimés} dossiers...\n";
                    }
                }
            });

            echo "💀 {$supprimés} dossiers supprimés de force\n";
        });
        */
    }

    // Statistiques finales
    echo "📊 RÉSULTAT FINAL:\n";
    echo "   Dossiers médicaux restants: " . DossierMedical::count() . "\n";
    echo "   Patients: " . GestionPatient::count() . " (préservés)\n";
    echo "   Factures: " . Caisse::count() . " (préservées)\n";
    echo "   Hospitalisations: " . Hospitalisation::count() . " (préservées)\n";
    echo "   Rendez-vous: " . RendezVous::count() . " (préservés)\n\n";
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "   La transaction a été annulée.\n";
}

echo "✨ Script terminé.\n";


