php artisan tinker

// ===================================================================
// Script Tinker pour supprimer les dossiers médicaux
// ATTENTION: Sauvegardez votre base de données avant d'exécuter !
// ===================================================================

// Afficher les statistiques avant suppression
echo "📊 Statistiques AVANT suppression:\n";
echo "Dossiers médicaux: " . \App\Models\DossierMedical::count() . "\n";
echo "Patients: " . \App\Models\GestionPatient::count() . "\n";
echo "Factures: " . \App\Models\Caisse::count() . "\n";
echo "Hospitalisations: " . \App\Models\Hospitalisation::count() . "\n";

// Vérifier s'il y a des relations critiques
$dossiersAvecRelations = \App\Models\DossierMedical::whereHas('caisses')
->orWhereHas('hospitalisations')
->orWhereHas('rendezVous')
->count();

echo "Dossiers avec relations: " . $dossiersAvecRelations . "\n\n";

// Option 1: Suppression SÉCURISÉE (dossiers sans relations)
$dossiersSansRelations = \App\Models\DossierMedical::whereDoesntHave('caisses')
->whereDoesntHave('hospitalisations')
->whereDoesntHave('rendezVous');

$countSansRelations = $dossiersSansRelations->count();
echo "🛡️ Suppression SÉCURISÉE de {$countSansRelations} dossiers sans relations...\n";

if ($countSansRelations > 0) {
$dossiersSansRelations->delete();
echo "✅ {$countSansRelations} dossiers supprimés en toute sécurité\n";
}

// Option 2: Suppression FORCÉE (tous les dossiers) - DÉCOMMENTEZ SI NÉCESSAIRE
/*
echo "⚠️ Suppression FORCÉE de TOUS les dossiers médicaux...\n";
$totalDossiers = \App\Models\DossierMedical::count();
\App\Models\DossierMedical::truncate();
echo "💀 {$totalDossiers} dossiers supprimés de force\n";
*/

// Afficher les statistiques après suppression
echo "\n📊 Statistiques APRÈS suppression:\n";
echo "Dossiers médicaux: " . \App\Models\DossierMedical::count() . "\n";
echo "Patients: " . \App\Models\GestionPatient::count() . " (préservés)\n";
echo "Factures: " . \App\Models\Caisse::count() . " (préservées)\n";
echo "Hospitalisations: " . \App\Models\Hospitalisation::count() . " (préservées)\n";

echo "\n✅ Nettoyage terminé !\n";

// Optionnel: Optimiser la base de données après suppression
// \DB::statement('OPTIMIZE TABLE dossier_medicals');

exit












