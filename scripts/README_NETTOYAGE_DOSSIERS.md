# 🏥 Scripts de Nettoyage des Dossiers Médicaux

## ⚠️ IMPORTANT - LISEZ AVANT UTILISATION

Ces scripts permettent de supprimer les dossiers médicaux en production tout en préservant les autres modules (patients, factures, hospitalisations, rendez-vous).

## 📋 Prérequis

1. **Sauvegarde obligatoire** : Faites une sauvegarde complète de votre base de données
2. **Accès SSH** : Connectez-vous à votre serveur de production
3. **Droits admin** : Assurez-vous d'avoir les droits d'administration

## 🚀 Utilisation

### Script 1: Nettoyage Sécurisé (Recommandé)

```bash
# Se placer dans le répertoire de l'application
cd /path/to/your/laravel/app

# Exécuter le script sécurisé
php artisan tinker < scripts/clean_dossiers_medical.php
```

**Ce script :**

-   ✅ Vérifie les relations avant suppression
-   ✅ Supprime uniquement les dossiers sans relations
-   ✅ Préserve tous les autres modules
-   ✅ S'arrête si des relations critiques sont détectées

### Script 2: Nettoyage Avancé (Expert seulement)

```bash
# Pour les utilisateurs avancés seulement
php artisan tinker < scripts/force_clean_dossiers_medical.php
```

**Ce script :**

-   🔍 Analyse complète des relations
-   🛡️ Suppression sûre en priorité
-   ⚠️ Option de suppression forcée (désactivée par défaut)
-   📊 Statistiques détaillées

## 🔧 Options Avancées

### Suppression Forcée (DANGEREUX)

Si vous devez vraiment supprimer des dossiers avec relations :

1. **Modifiez le script** `force_clean_dossiers_medical.php`
2. **Décommentez** la section "SUPPRESSION FORCÉE"
3. **Testez d'abord** sur un environnement de développement

```php
// Décommentez cette section dans le script :
/*
echo "🔥 SUPPRESSION FORCÉE ACTIVÉE...\n";
DB::transaction(function () {
    // ... code de suppression forcée
});
*/
```

## 📊 Vérifications Post-Nettoyage

Après exécution, vérifiez que votre application fonctionne :

```bash
# Vérifier l'état de l'application
php artisan route:list | grep dossiers
php artisan migrate:status

# Tester les modules principaux
curl http://localhost:8000/patients
curl http://localhost:8000/caisses
curl http://localhost:8000/hospitalisations
```

## 🆘 En Cas de Problème

### Si l'application ne fonctionne plus :

1. **Restaurez la sauvegarde** immédiatement
2. **Vérifiez les logs** : `tail -f storage/logs/laravel.log`
3. **Contactez l'équipe technique**

### Commandes de diagnostic :

```bash
# Vérifier l'état de la base de données
php artisan tinker -e "echo 'Patients: ' . App\Models\GestionPatient::count(); echo '\nCaisses: ' . App\Models\Caisse::count();"

# Vérifier les relations orphelines
php artisan tinker -e "
use App\Models\Caisse;
use App\Models\DossierMedical;
echo 'Caisses avec dossier inexistant: ' . Caisse::whereHas('dossierMedical', function(\$q) { \$q->whereNull('id'); })->count();
"
```

## 🎯 Résultats Attendus

Après un nettoyage réussi :

-   ✅ Dossiers médicaux supprimés
-   ✅ Patients préservés
-   ✅ Factures/Caisses préservées
-   ✅ Hospitalisations préservées
-   ✅ Rendez-vous préservés
-   ✅ Application fonctionnelle

## 📞 Support

En cas de doute ou de problème :

1. **Ne procédez pas** à la suppression
2. **Contactez l'équipe technique**
3. **Demandez assistance** pour l'exécution

---

**⚠️ Rappel : Ces scripts modifient la base de données de production. Soyez prudent !**













