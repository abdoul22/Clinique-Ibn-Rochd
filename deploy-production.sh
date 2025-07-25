#!/bin/bash

echo "🚀 Déploiement Production - Clinique Ibn Rochd"
echo "=============================================="

# 1. Installation des dépendances
echo "📦 Installation des dépendances..."
composer install --optimize-autoloader --no-dev
npm ci

# 2. Compilation des assets
echo "🎨 Compilation des assets..."
npm run build

# 3. Nettoyage des caches
echo "🧹 Nettoyage des caches..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# 4. Optimisation des caches
echo "⚡ Optimisation des caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Optimisation des permissions
echo "🔐 Configuration des permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# 6. Test que les assets existent
echo "🔍 Vérification des assets..."
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Manifest trouvé"
    cat public/build/manifest.json
else
    echo "❌ Manifest manquant"
fi

echo "✅ Déploiement terminé!"
echo "🌐 Vérifiez que APP_ENV=production dans votre .env"
echo "🌐 Vérifiez que APP_DEBUG=false dans votre .env"
