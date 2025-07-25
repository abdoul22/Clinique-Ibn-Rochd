Write-Host "🚀 Déploiement Production - Clinique Ibn Rochd" -ForegroundColor Green
Write-Host "=============================================="

# 1. Installation des dépendances
Write-Host "📦 Installation des dépendances..." -ForegroundColor Yellow
composer install --optimize-autoloader --no-dev
npm ci

# 2. Compilation des assets
Write-Host "🎨 Compilation des assets..." -ForegroundColor Yellow
npm run build

# 3. Nettoyage des caches
Write-Host "🧹 Nettoyage des caches..." -ForegroundColor Yellow
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# 4. Optimisation des caches
Write-Host "⚡ Optimisation des caches..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Test que les assets existent
Write-Host "🔍 Vérification des assets..." -ForegroundColor Yellow
if (Test-Path "public/build/manifest.json") {
    Write-Host "✅ Manifest trouvé" -ForegroundColor Green
    Get-Content "public/build/manifest.json"
} else {
    Write-Host "❌ Manifest manquant" -ForegroundColor Red
}

Write-Host "✅ Déploiement terminé!" -ForegroundColor Green
Write-Host "🌐 Vérifiez que APP_ENV=production dans votre .env" -ForegroundColor Cyan
Write-Host "🌐 Vérifiez que APP_DEBUG=false dans votre .env" -ForegroundColor Cyan
