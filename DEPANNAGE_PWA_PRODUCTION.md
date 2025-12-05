# 🔧 Guide de Dépannage PWA en Production

## ❌ Problème : L'option d'installation n'apparaît pas en production

### ✅ Checklist de vérification

#### 1. **HTTPS est OBLIGATOIRE** ⚠️ CRITIQUE

Les PWA **nécessitent HTTPS** en production (sauf localhost).

**Vérification :**
```bash
# Votre site doit être accessible via HTTPS
https://votre-domaine.com
```

**Solutions :**
- Utilisez Let's Encrypt (gratuit) : `certbot`
- Utilisez Cloudflare (gratuit) avec SSL automatique
- Configurez SSL sur votre serveur (Apache/Nginx)

**⚠️ Important :** Même avec un certificat auto-signé, Chrome peut refuser l'installation.

---

#### 2. **Vérifier que le manifest est accessible**

**Test :**
```bash
curl https://votre-domaine.com/manifest.webmanifest
```

**Doit retourner :**
```json
{
  "name": "Nom de la Clinique",
  "short_name": "Clinique",
  "icons": [...]
}
```

**Si erreur 404 :**
- Vérifiez la route : `php artisan route:list | grep manifest`
- Vérifiez que le fichier `.htaccess` (Apache) ou la config Nginx permet les fichiers `.webmanifest`

**Solution Apache (.htaccess) :**
```apache
# Ajouter dans public/.htaccess
<Files "manifest.webmanifest">
    Header set Content-Type "application/manifest+json"
</Files>
```

**Solution Nginx :**
```nginx
location ~ \.webmanifest$ {
    add_header Content-Type application/manifest+json;
}
```

---

#### 3. **Vérifier que le Service Worker est accessible**

**Test :**
```bash
curl https://votre-domaine.com/sw.js
```

**Doit retourner :** Le code JavaScript du Service Worker

**Si erreur 404 :**
- Vérifiez que `npm run build` a été exécuté en production
- Vérifiez que `public/sw.js` existe sur le serveur
- Vérifiez les permissions : `chmod 644 public/sw.js`

**Solution :**
```bash
# Sur le serveur de production
cd /chemin/vers/votre/projet
npm run build
```

---

#### 4. **Vérifier que les icônes sont accessibles**

**Test :**
```bash
curl -I https://votre-domaine.com/pwa-192x192.png
curl -I https://votre-domaine.com/pwa-512x512.png
```

**Doit retourner :** `HTTP/1.1 200 OK` avec `Content-Type: image/png`

**Si erreur 404 :**
- Vérifiez que les fichiers existent dans `public/`
- Vérifiez les permissions : `chmod 644 public/pwa-*.png`
- Vérifiez que les chemins dans le manifest sont corrects (doivent être des URLs absolues)

---

#### 5. **Vérifier la console du navigateur**

**Chrome DevTools (F12) :**
1. Onglet **Console** : Cherchez les erreurs en rouge
2. Onglet **Application** :
   - **Manifest** : Vérifiez les erreurs (icônes manquantes, etc.)
   - **Service Workers** : Vérifiez le statut (doit être "actif")
   - **Storage** : Vérifiez les erreurs de cache

**Erreurs courantes :**
- `Failed to register a ServiceWorker` → Vérifiez HTTPS et que `sw.js` est accessible
- `Manifest: property 'icons' ignored` → Les icônes ne sont pas accessibles
- `Site cannot be installed: no matching service worker detected` → Le SW n'est pas enregistré

---

#### 6. **Vérifier les headers HTTP**

Le manifest et le Service Worker doivent avoir les bons headers.

**Test :**
```bash
curl -I https://votre-domaine.com/manifest.webmanifest
curl -I https://votre-domaine.com/sw.js
```

**Headers requis :**
```
Content-Type: application/manifest+json  (pour manifest)
Content-Type: application/javascript      (pour sw.js)
```

**Si mauvais Content-Type :**
- Vérifiez la configuration du serveur web (Apache/Nginx)
- Ajoutez les règles dans `.htaccess` ou la config Nginx

---

#### 7. **Vérifier que le manifest est valide**

**Test en ligne :**
https://manifest-validator.appspot.com/

Collez l'URL de votre manifest : `https://votre-domaine.com/manifest.webmanifest`

**Erreurs courantes :**
- Icônes manquantes ou non accessibles
- Chemins relatifs au lieu d'URLs absolues
- Taille d'icône incorrecte

---

#### 8. **Vérifier les critères d'installabilité Chrome**

Chrome nécessite :
1. ✅ HTTPS (ou localhost)
2. ✅ Manifest valide et accessible
3. ✅ Service Worker enregistré et actif
4. ✅ Icône 192x192 accessible
5. ✅ Icône 512x512 accessible
6. ✅ `start_url` dans le scope du Service Worker

**Test dans Chrome DevTools :**
1. F12 → Onglet **Application**
2. Section **Manifest**
3. Vérifiez les erreurs affichées

---

## 🔍 Diagnostic étape par étape

### Étape 1 : Vérifier HTTPS
```bash
# Votre site doit être en HTTPS
curl -I https://votre-domaine.com
# Doit retourner : HTTP/2 200 (pas d'erreur SSL)
```

### Étape 2 : Vérifier le manifest
```bash
curl https://votre-domaine.com/manifest.webmanifest | jq
# Vérifiez que les "src" des icônes sont des URLs absolues
# Exemple : "https://votre-domaine.com/pwa-192x192.png"
```

### Étape 3 : Vérifier le Service Worker
```bash
curl https://votre-domaine.com/sw.js | head -20
# Doit retourner du code JavaScript
```

### Étape 4 : Vérifier les icônes
```bash
curl -I https://votre-domaine.com/pwa-192x192.png
curl -I https://votre-domaine.com/pwa-512x512.png
# Doit retourner : HTTP/2 200
```

### Étape 5 : Vérifier dans Chrome DevTools
1. Ouvrez `https://votre-domaine.com`
2. F12 → Onglet **Application**
3. Vérifiez :
   - **Manifest** : Pas d'erreurs rouges
   - **Service Workers** : Statut "actif"
   - **Console** : Pas d'erreurs

---

## 🛠️ Solutions courantes

### Solution 1 : Ajouter les headers dans .htaccess (Apache)

Ajoutez dans `public/.htaccess` :

```apache
# Headers pour PWA
<Files "manifest.webmanifest">
    Header set Content-Type "application/manifest+json"
    Header set Cache-Control "public, max-age=3600"
</Files>

<Files "sw.js">
    Header set Content-Type "application/javascript"
    Header set Cache-Control "public, max-age=0"
    Header set Service-Worker-Allowed "/"
</Files>
```

### Solution 2 : Configurer Nginx

Ajoutez dans votre config Nginx :

```nginx
# Manifest PWA
location = /manifest.webmanifest {
    add_header Content-Type application/manifest+json;
    add_header Cache-Control "public, max-age=3600";
}

# Service Worker
location = /sw.js {
    add_header Content-Type application/javascript;
    add_header Cache-Control "public, max-age=0";
    add_header Service-Worker-Allowed "/";
}
```

### Solution 3 : Vérifier que les assets sont construits

```bash
# Sur le serveur de production
cd /chemin/vers/projet
npm install
npm run build

# Vérifier que les fichiers existent
ls -la public/sw.js
ls -la public/workbox-*.js
```

### Solution 4 : Vider le cache du navigateur

Parfois le navigateur cache une ancienne version :

1. Chrome : Ctrl+Shift+Delete → Vider le cache
2. Ou : F12 → Onglet **Application** → **Clear storage** → **Clear site data**

### Solution 5 : Vérifier les permissions des fichiers

```bash
# Sur le serveur
chmod 644 public/sw.js
chmod 644 public/pwa-*.png
chmod 644 public/manifest.webmanifest
```

---

## 🧪 Test rapide

Exécutez ce script pour diagnostiquer rapidement :

```bash
#!/bin/bash
DOMAIN="https://votre-domaine.com"

echo "🔍 Diagnostic PWA pour $DOMAIN"
echo ""

echo "1. Vérification HTTPS..."
curl -I $DOMAIN 2>&1 | grep -i "http" | head -1

echo ""
echo "2. Vérification Manifest..."
curl -s $DOMAIN/manifest.webmanifest | jq -r '.name, .short_name' 2>/dev/null || echo "❌ Manifest non accessible"

echo ""
echo "3. Vérification Service Worker..."
curl -I $DOMAIN/sw.js 2>&1 | grep -i "200\|404" | head -1

echo ""
echo "4. Vérification Icônes..."
curl -I $DOMAIN/pwa-192x192.png 2>&1 | grep -i "200\|404" | head -1
curl -I $DOMAIN/pwa-512x512.png 2>&1 | grep -i "200\|404" | head -1

echo ""
echo "✅ Diagnostic terminé"
```

---

## 📱 Test sur mobile

### Android (Chrome)
1. Ouvrez `https://votre-domaine.com`
2. Menu (⋮) → **Installer l'application**
3. Si l'option n'apparaît pas → Vérifiez la console (chrome://inspect)

### iOS (Safari)
1. Ouvrez `https://votre-domaine.com`
2. Bouton **Partager** (□↑)
3. **Sur l'écran d'accueil**
4. Si l'option n'apparaît pas → Vérifiez que le manifest est valide

---

## ⚠️ Erreurs fréquentes et solutions

| Erreur | Cause | Solution |
|--------|-------|----------|
| `Failed to register ServiceWorker` | HTTPS manquant ou SW non accessible | Activer HTTPS, vérifier `sw.js` |
| `Manifest: property 'icons' ignored` | Icônes non accessibles | Vérifier les URLs absolues dans le manifest |
| `Site cannot be installed` | Critères non remplis | Vérifier HTTPS, manifest, SW, icônes |
| `404 sur manifest.webmanifest` | Route non configurée | Vérifier `routes/web.php` |
| `404 sur sw.js` | Build non exécuté | Exécuter `npm run build` |

---

## ✅ Checklist finale

Avant de déployer en production :

- [ ] Site accessible en **HTTPS**
- [ ] `npm run build` exécuté
- [ ] `public/sw.js` existe et est accessible
- [ ] `public/pwa-192x192.png` existe et est accessible
- [ ] `public/pwa-512x512.png` existe et est accessible
- [ ] `/manifest.webmanifest` retourne un JSON valide
- [ ] Les icônes dans le manifest sont des **URLs absolues**
- [ ] Headers HTTP corrects (Content-Type)
- [ ] Pas d'erreurs dans la console Chrome DevTools
- [ ] Manifest validé sur https://manifest-validator.appspot.com/

---

## 🆘 Si rien ne fonctionne

1. **Vérifiez les logs Laravel** : `storage/logs/laravel.log`
2. **Vérifiez les logs du serveur web** (Apache/Nginx)
3. **Testez avec un manifest statique** pour isoler le problème
4. **Vérifiez que le domaine n'est pas sur une liste noire** (rare mais possible)

---

## 📞 Support

Si le problème persiste après avoir suivi ce guide :
1. Partagez l'URL de votre site
2. Partagez le résultat de `curl https://votre-domaine.com/manifest.webmanifest`
3. Partagez les erreurs de la console Chrome DevTools

