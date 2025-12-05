# PWA Dynamique - Guide d'utilisation

## Vue d'ensemble

L'application est maintenant une **Progressive Web App (PWA)** entièrement dynamique qui s'adapte automatiquement à chaque clinique cliente en utilisant les informations de configuration dans `config/clinique.php`.

## Fonctionnalités

- ✅ **Manifest dynamique** : Le nom, la description, les couleurs et les icônes sont générés dynamiquement selon la configuration de chaque clinique
- ✅ **Installation** : L'application peut être installée sur mobile et desktop
- ✅ **Mode hors-ligne** : Service Worker pour le cache des ressources
- ✅ **Icônes personnalisées** : Utilise automatiquement le logo de la clinique

## Configuration

### Variables d'environnement (.env)

```env
# Nom de la clinique (utilisé pour le nom de l'app PWA)
CLINIQUE_NAME="Nom de la Clinique"

# Nom court (max 12 caractères recommandé, sinon généré automatiquement)
CLINIQUE_SHORT_NAME="Clinique"

# Couleur principale (utilisée pour theme-color)
CLINIQUE_PRIMARY_COLOR="#1e40af"

# Couleur de fond pour le splash screen
CLINIQUE_PWA_BACKGROUND_COLOR="#ffffff"

# Logo de la clinique (chemin relatif depuis public/)
CLINIQUE_LOGO_PATH="images/logo.png"

# Icônes PWA personnalisées (optionnel, utilise le logo par défaut)
CLINIQUE_PWA_ICON_192="images/pwa-icon-192.png"
CLINIQUE_PWA_ICON_512="images/pwa-icon-512.png"
```

### Fichier de configuration (config/clinique.php)

Toutes les valeurs peuvent être modifiées directement dans `config/clinique.php` ou via les variables d'environnement.

## Génération des icônes PWA

### Méthode automatique (recommandée)

Utilisez la commande Artisan pour générer automatiquement les icônes PWA à partir du logo de la clinique :

```bash
php artisan pwa:generate-icons
```

Cette commande :
- Lit le logo configuré dans `config/clinique.php`
- Génère `public/pwa-192x192.png` (192x192 pixels)
- Génère `public/pwa-512x512.png` (512x512 pixels)
- Préserve la transparence si le logo est en PNG
- Centre et redimensionne le logo en conservant les proportions

**Options :**
- `--force` : Force la régénération même si les icônes existent déjà

### Méthode manuelle

Si vous préférez créer les icônes manuellement :

1. Créez deux versions de votre logo :
   - `public/pwa-192x192.png` (192x192 pixels)
   - `public/pwa-512x512.png` (512x512 pixels)

2. Ou configurez des chemins personnalisés dans `.env` :
   ```env
   CLINIQUE_PWA_ICON_192="images/mon-icon-192.png"
   CLINIQUE_PWA_ICON_512="images/mon-icon-512.png"
   ```

## Vérification

### 1. Vérifier le manifest

Accédez à : `http://votre-domaine.com/manifest.webmanifest`

Vous devriez voir un JSON avec les informations de votre clinique :
```json
{
  "name": "Nom de votre clinique",
  "short_name": "Clinique",
  "description": "Description des services",
  "theme_color": "#1e40af",
  "icons": [...]
}
```

### 2. Vérifier dans Chrome DevTools

1. Ouvrez Chrome DevTools (F12)
2. Allez dans l'onglet **Application**
3. Vérifiez :
   - **Manifest** : Doit afficher les informations de votre clinique
   - **Service Workers** : Doit être actif
   - **Storage** : Doit montrer les fichiers mis en cache

### 3. Tester l'installation

- **Sur Desktop (Chrome/Edge)** : Une icône d'installation apparaîtra dans la barre d'adresse
- **Sur Mobile (Android)** : Un prompt "Ajouter à l'écran d'accueil" apparaîtra
- **Sur iOS (Safari)** : Utilisez le bouton "Partager" > "Sur l'écran d'accueil"

## Déploiement

1. **Générer les icônes** pour chaque nouvelle clinique :
   ```bash
   php artisan pwa:generate-icons
   ```

2. **Construire les assets** :
   ```bash
   npm run build
   ```

3. **Vérifier** que les fichiers suivants existent :
   - `public/sw.js` (Service Worker)
   - `public/workbox-*.js` (Workbox)
   - `public/pwa-192x192.png`
   - `public/pwa-512x512.png`

## Personnalisation avancée

### Changer les couleurs du thème

Modifiez dans `.env` ou `config/clinique.php` :
```php
'primary_color' => '#votre-couleur-hex',
'pwa_background_color' => '#votre-couleur-fond',
```

### Personnaliser le nom court

Si le nom généré automatiquement ne convient pas :
```env
CLINIQUE_SHORT_NAME="MonApp"
```

## Dépannage

### Les icônes ne s'affichent pas

1. Vérifiez que les fichiers existent dans `public/`
2. Vérifiez les permissions des fichiers
3. Régénérez avec `php artisan pwa:generate-icons --force`

### Le manifest ne se charge pas

1. Vérifiez la route : `php artisan route:list | grep manifest`
2. Vérifiez que le contrôleur `ManifestController` existe
3. Vérifiez les logs Laravel

### Le Service Worker ne fonctionne pas

1. Vérifiez que `npm run build` a été exécuté
2. Vérifiez que `public/sw.js` existe
3. Vérifiez la console du navigateur pour les erreurs

## Notes importantes

- ⚠️ **HTTPS requis** : Les PWA nécessitent HTTPS en production (sauf localhost)
- ⚠️ **Cache** : Après modification du manifest, videz le cache du navigateur
- ⚠️ **Service Worker** : Les modifications du SW nécessitent un rebuild (`npm run build`)

