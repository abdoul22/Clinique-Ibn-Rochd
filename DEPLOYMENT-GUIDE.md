# 📋 Guide de Déploiement Production - Ibn Rochd

## 🎯 Objectif

Ce guide vous aide à déployer le code en production en toute sécurité et à déboguer les problèmes courants.

---

## 📌 Prérequis

-   ✅ Accès SSH au serveur de production
-   ✅ Git configuré sur le serveur
-   ✅ PHP 8.1+ installé
-   ✅ Composer installé
-   ✅ Node.js et npm installés
-   ✅ Base de données MySQL/MariaDB

---

## 🚀 Déploiement Rapide

### **Option 1: Avec le script automatisé (Recommandé)**

#### Sur Linux/Mac:

```bash
chmod +x deploy-production.sh
./deploy-production.sh
```

#### Sur Windows (PowerShell):

```powershell
Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope Process
.\deploy-production.ps1
```

### **Option 2: Pas à pas manuel**

```bash
# 1. Passer en SSH sur le serveur
ssh user@ibnrochd.pro

# 2. Aller dans le répertoire du projet
cd /chemin/vers/clinique-ibn-rochd

# 3. Mettre à jour le code
git pull origin main

# 4. Installer les dépendances
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# 5. Nettoyer les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 6. Exécuter les migrations
php artisan migrate --force

# 7. Optimiser pour production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔍 Vérification Post-Déploiement

### 1. **Vérifier que le site fonctionne**

```bash
# Testez l'accueil
curl https://ibnrochd.pro/ibnrochd/public/

# Testez une page d'hospitalisation (remplacez 34 par un ID réel)
curl https://ibnrochd.pro/ibnrochd/public/index.php/hospitalisations/34
```

### 2. **Vérifier les logs**

```bash
# Afficher les derniers logs
tail -n 50 storage/logs/laravel.log

# Afficher les logs en temps réel
tail -f storage/logs/laravel.log
```

### 3. **Vérifier les permissions**

```bash
# Les répertoires doivent être accessibles par le serveur web
ls -la storage/
ls -la bootstrap/cache/
ls -la public/

# Si les permissions sont incorrectes:
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 4. **Vérifier la configuration**

```bash
# Afficher la configuration active
php artisan config:show

# Vérifier que APP_ENV et APP_DEBUG sont corrects
grep APP_ENV .env
grep APP_DEBUG .env
```

---

## 🐛 Déboguer l'Erreur 500

### **Étape 1: Vérifier les logs Laravel**

```bash
tail -n 100 storage/logs/laravel.log | grep -i error
```

### **Étape 2: Activer le mode DEBUG temporairement**

```bash
# ⚠️ ATTENTION: Ne laissez pas DEBUG=true en production!

# Temporaire (quelques minutes):
APP_DEBUG=true php -S 0.0.0.0:8000

# Ou modifiez .env:
APP_DEBUG=true
```

Puis testez: `https://ibnrochd.pro/ibnrochd/public/index.php/hospitalisations/34`

Cela affichera l'erreur réelle au lieu du message générique 500.

### **Étape 3: Vérifier que les migrations sont à jour**

```bash
# Afficher le statut des migrations
php artisan migrate:status

# Vérifier quelle migration a échoué
php artisan migrate:status | grep PENDING
```

### **Étape 4: Vérifier les fichiers assets**

```bash
# Vérifier que les assets compilés existent
ls -la public/build/

# Vérifier que le manifest existe
cat public/build/manifest.json

# Recompiler si nécessaire
npm run build
```

---

## 📋 Checklist Avant Déploiement

-   [ ] Tous les changements sont commités et pushés
-   [ ] Les tests passent en local (`npm run dev`, accédez au site)
-   [ ] Le fichier `.env` de production est correct
-   [ ] `APP_ENV=production` et `APP_DEBUG=false`
-   [ ] Sauvegardes de la base de données faites
-   [ ] Sauvegardes du code en production faites

---

## 📋 Checklist Après Déploiement

-   [ ] ✅ Le site s'affiche sans erreur CSS/JS
-   [ ] ✅ Les pages hospitalisations se chargent
-   [ ] ✅ Les CRUD (Create, Read, Update, Delete) fonctionnent
-   [ ] ✅ Les exports PDF génèrent correctement
-   [ ] ✅ Les formulaires valident et soumettent
-   [ ] ✅ Aucune erreur dans la console JavaScript
-   [ ] ✅ Les logs Laravel ne montrent pas d'erreurs

---

## 🆘 Problèmes Courants

### **Erreur: "The GET method is not supported for route"**

**Cause:** Routes dupliquées ou middleware incorrect  
**Solution:**

```bash
php artisan route:list | grep hospitalisation
php artisan route:clear
php artisan route:cache
```

### **Erreur: "Class not found"**

**Cause:** Autoloader pas mis à jour  
**Solution:**

```bash
composer dump-autoload --optimize
```

### **Erreur: "Migrations pending"**

**Cause:** Migrations non exécutées  
**Solution:**

```bash
php artisan migrate --force
php artisan migrate:status
```

### **Erreur: "500 Internal Server Error"**

**Solution étape par étape:**

1. Vérifier les logs: `tail -f storage/logs/laravel.log`
2. Vérifier les permissions: `chmod -R 755 storage`
3. Nettoyer les caches: `php artisan optimize:clear`
4. Exécuter les migrations: `php artisan migrate --force`
5. Recompiler les assets: `npm run build`

### **CSS/JS ne se chargent pas**

**Cause:** Assets non compilés ou mauvaise URL  
**Solution:**

```bash
# Recompiler les assets
npm run build

# Vérifier que APP_URL est correct dans .env
grep APP_URL .env

# Vérifier que les fichiers existent
ls -la public/build/assets/
```

---

## 🚨 Rollback (Revenir à la version précédente)

Si quelque chose s'est mal passé:

```bash
# Voir l'historique Git
git log --oneline -n 10

# Revenir à la version précédente
git revert HEAD
git push origin main

# Puis déployer à nouveau
./deploy-production.sh
```

---

## 📞 Support et Logs

### **Fichiers de logs importants**

```
/chemin/vers/clinique-ibn-rochd/storage/logs/laravel.log  (logs Laravel)
/var/log/apache2/error.log  (logs Apache - si Apache)
/var/log/nginx/error.log    (logs Nginx - si Nginx)
```

### **Commandes utiles**

```bash
# Voir les derniers logs en temps réel
tail -f storage/logs/laravel.log

# Nettoyer TOUS les caches
php artisan optimize:clear

# Redémarrer les services (peut être nécessaire)
sudo systemctl restart php-fpm
sudo systemctl restart nginx  # ou apache2
```

---

## ✅ Résumé du Processus Complet

```bash
# 1. SSH sur le serveur
ssh user@ibnrochd.pro

# 2. Aller dans le projet
cd /chemin/vers/clinique-ibn-rochd

# 3. Exécuter le script de déploiement
./deploy-production.sh

# 4. Vérifier les logs
tail -n 50 storage/logs/laravel.log

# 5. Tester le site
# Accédez à https://ibnrochd.pro/ibnrochd/public/ dans votre navigateur

# 6. Si erreur 500:
php artisan logs
# Cherchez l'erreur et corrigez-la
```

---

**💡 Conseil:** Gardez toujours ce document à portée de main et n'hésitez pas à l'consulter avant chaque déploiement!
