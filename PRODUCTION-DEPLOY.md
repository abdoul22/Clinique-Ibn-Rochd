# 🚀 Déploiement Production - Résolution des différences Local vs Production

## 🤔 **Pourquoi la page diffère entre Local et Production ?**

### **Causes principales :**

1. **🎨 Assets non compilés**
   - **Local** : Vite dev server (`npm run dev`)
   - **Production** : Assets compilés (`npm run build`)

2. **📦 Caches différents**
   - **Local** : Cache désactivé pour le développement
   - **Production** : Cache optimisé pour les performances

3. **🔧 Configuration d'environnement**
   - **Local** : `APP_ENV=local`, `APP_DEBUG=true`
   - **Production** : `APP_ENV=production`, `APP_DEBUG=false`

4. **📁 Fichiers manquants**
   - CSS Tailwind non généré
   - Manifest Vite manquant
   - Permissions incorrectes

---

## ✅ **Solutions étape par étape**

### **1. Compilation des assets**
```bash
# Compiler pour la production
npm run build

# Vérifier que les fichiers sont générés
ls -la public/build/
```

### **2. Nettoyage et optimisation des caches**
```bash
# Nettoyer tous les caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **3. Vérifier le fichier .env de production**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com
```

### **4. Permissions des fichiers**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/build
```

---

## 🛠️ **Scripts automatisés**

### **Pour Linux/Mac :**
```bash
./deploy-production.sh
```

### **Pour Windows :**
```powershell
.\deploy-production.ps1
```

---

## 🔍 **Diagnostic rapide**

### **Vérifier que les assets existent :**
```bash
# Le manifest doit exister
cat public/build/manifest.json

# Les fichiers CSS/JS doivent exister
ls -la public/build/assets/
```

### **Vérifier la configuration Laravel :**
```bash
# Tester la configuration
php artisan config:show

# Vérifier les routes
php artisan route:list
```

---

## 🚨 **Problèmes courants**

| Problème | Cause | Solution |
|----------|-------|----------|
| CSS non appliqué | Assets non compilés | `npm run build` |
| Page blanche | Erreur PHP cachée | Vérifier `storage/logs/laravel.log` |
| 404 sur assets | Mauvais chemin | Vérifier `APP_URL` dans `.env` |
| Styles cassés | Cache obsolète | `php artisan view:clear` |

---

## 📋 **Checklist de déploiement**

- [ ] ✅ `npm run build` exécuté
- [ ] ✅ `public/build/manifest.json` existe
- [ ] ✅ Fichiers CSS/JS dans `public/build/assets/`
- [ ] ✅ Caches optimisés
- [ ] ✅ `APP_ENV=production` dans `.env`
- [ ] ✅ `APP_DEBUG=false` dans `.env`
- [ ] ✅ Permissions correctes
- [ ] ✅ Tests de la page

---

## 🔄 **Commandes de débogage**

Si le problème persiste :

```bash
# 1. Vérifier les logs
tail -f storage/logs/laravel.log

# 2. Tester en mode debug temporairement
APP_DEBUG=true php artisan serve

# 3. Recréer complètement les assets
rm -rf public/build
npm run build

# 4. Vider TOUS les caches
php artisan optimize:clear
```

---

**💡 Astuce :** Gardez toujours une sauvegarde avant déploiement et testez sur un environnement de staging ! 
