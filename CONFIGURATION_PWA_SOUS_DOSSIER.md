# 🔧 Configuration PWA pour Installation dans un Sous-Dossier

## 📍 Votre Configuration

Votre application Laravel est installée dans un sous-dossier :
- **URL de base** : `https://ibnrochd.pro/ibnrochd/public/`
- **HTTPS** : ✅ Activé

## ⚠️ Problème Identifié

Quand Laravel est dans un sous-dossier (`/ibnrochd/public/`), le Service Worker et le manifest doivent être configurés avec le bon **chemin de base** (scope).

---

## ✅ Corrections Appliquées

### 1. ManifestController mis à jour
Le manifest génère maintenant automatiquement le bon `start_url` et `scope` en fonction du chemin de base détecté.

### 2. Vérifications à faire

#### A. Vérifier la configuration Vite

Si votre application est dans `/ibnrochd/public/`, vous devrez peut-être ajuster `vite.config.js` :

```javascript
VitePWA({
    registerType: 'autoUpdate',
    outDir: 'public',
    buildBase: '/ibnrochd/public/',  // ← Ajuster selon votre configuration
    scope: '/ibnrochd/public/',       // ← Ajuster selon votre configuration
    // ...
})
```

**⚠️ Important :** Ne modifiez cela QUE si les fichiers sont servis depuis `/ibnrochd/public/build/`. Sinon, laissez `/`.

#### B. Vérifier le fichier .env

Assurez-vous que `APP_URL` est correct :

```env
APP_URL=https://ibnrochd.pro/ibnrochd/public
```

Ou si vous utilisez une réécriture d'URL :

```env
APP_URL=https://ibnrochd.pro
```

---

## 🧪 Tests à Effectuer

### 1. Tester le manifest
Ouvrez dans votre navigateur :
```
https://ibnrochd.pro/ibnrochd/public/manifest.webmanifest
```

**Vérifiez que :**
- Le JSON est valide
- Les `src` des icônes sont des URLs absolues : `https://ibnrochd.pro/ibnrochd/public/pwa-192x192.png`
- Le `start_url` correspond au chemin de base : `/ibnrochd/public/` ou `/`
- Le `scope` correspond au chemin de base

### 2. Tester le Service Worker
Ouvrez dans votre navigateur :
```
https://ibnrochd.pro/ibnrochd/public/sw.js
```

**Doit retourner :** Du code JavaScript (pas une erreur 404)

### 3. Tester les icônes
```
https://ibnrochd.pro/ibnrochd/public/pwa-192x192.png
https://ibnrochd.pro/ibnrochd/public/pwa-512x512.png
```

**Doivent retourner :** Des images PNG

### 4. Vérifier dans Chrome DevTools

1. Ouvrez `https://ibnrochd.pro/ibnrochd/public/login`
2. F12 → Onglet **Application**
3. **Manifest** :
   - Vérifiez qu'il n'y a pas d'erreurs rouges
   - Vérifiez que les icônes sont chargées
4. **Service Workers** :
   - Statut doit être "actif et en cours d'exécution"
   - Source doit être `sw.js`
5. **Console** :
   - Pas d'erreurs en rouge

---

## 🔍 Diagnostic Spécifique

### Si le manifest retourne le mauvais `start_url`

Le `ManifestController` détecte automatiquement le chemin de base via `$request->getBasePath()`. 

Si cela ne fonctionne pas, vous pouvez forcer le chemin dans `.env` :

```env
# Optionnel : Forcer le chemin de base pour PWA
PWA_BASE_PATH=/ibnrochd/public
```

Puis modifiez `ManifestController.php` :

```php
$basePath = env('PWA_BASE_PATH', $request->getBasePath());
```

### Si le Service Worker ne s'enregistre pas

Le Service Worker doit être accessible depuis la même origine que votre application.

**Vérifiez :**
1. Que `sw.js` est accessible : `https://ibnrochd.pro/ibnrochd/public/sw.js`
2. Que le scope dans le manifest correspond au chemin de l'app
3. Qu'il n'y a pas d'erreurs CORS dans la console

---

## 📝 Checklist pour votre Configuration

- [ ] `APP_URL` correct dans `.env`
- [ ] Manifest accessible : `https://ibnrochd.pro/ibnrochd/public/manifest.webmanifest`
- [ ] Service Worker accessible : `https://ibnrochd.pro/ibnrochd/public/sw.js`
- [ ] Icônes accessibles : `https://ibnrochd.pro/ibnrochd/public/pwa-*.png`
- [ ] Manifest avec URLs absolues pour les icônes
- [ ] `start_url` et `scope` corrects dans le manifest
- [ ] Pas d'erreurs dans Chrome DevTools
- [ ] Service Worker actif dans DevTools

---

## 🚀 Actions Immédiates

1. **Déployez les corrections** :
```bash
git add .
git commit -m "Fix: Ajustement PWA pour sous-dossier /ibnrochd/public/"
git push
```

2. **Rebuild les assets** (sur le serveur) :
```bash
npm run build
```

3. **Videz le cache Laravel** :
```bash
php artisan config:clear
php artisan cache:clear
```

4. **Testez le manifest** :
Ouvrez : `https://ibnrochd.pro/ibnrochd/public/manifest.webmanifest`

5. **Vérifiez dans Chrome DevTools** :
- F12 → Application → Manifest
- Vérifiez les erreurs

---

## 🆘 Si ça ne fonctionne toujours pas

### Option 1 : Vérifier la réécriture d'URL

Si vous utilisez une réécriture d'URL pour masquer `/ibnrochd/public/`, vous devrez peut-être ajuster la configuration.

### Option 2 : Vérifier les permissions

```bash
# Sur le serveur
chmod 644 public/sw.js
chmod 644 public/pwa-*.png
chmod 644 public/manifest.webmanifest
```

### Option 3 : Vérifier les headers HTTP

Le manifest et le SW doivent avoir les bons Content-Type. Vérifiez dans `.htaccess` ou la config Nginx.

---

## 📞 Informations à Partager pour Diagnostic

Si le problème persiste, partagez :

1. **Résultat du manifest** :
```bash
curl https://ibnrochd.pro/ibnrochd/public/manifest.webmanifest
```

2. **Erreurs de la console Chrome** (F12 → Console)

3. **Statut du Service Worker** (F12 → Application → Service Workers)

4. **Votre configuration `.env`** (masquez les secrets) :
```env
APP_URL=...
```

---

## ✅ Résultat Attendu

Après ces corrections, vous devriez voir :
- ✅ L'icône d'installation dans Chrome (barre d'adresse)
- ✅ Le prompt "Ajouter à l'écran d'accueil" sur mobile
- ✅ L'app installée avec le nom "CENTRE IBN ROCHD"

