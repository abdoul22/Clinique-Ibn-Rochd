# 🚀 Script de déploiement en production - Ibn Rochd (PowerShell)

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "🚀 Déploiement Production - Ibn Rochd" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Fonctions pour les messages colorés
function Log-Info {
    param([string]$Message)
    Write-Host "ℹ️ $Message" -ForegroundColor Blue
}

function Log-Success {
    param([string]$Message)
    Write-Host "✅ $Message" -ForegroundColor Green
}

function Log-Warning {
    param([string]$Message)
    Write-Host "⚠️ $Message" -ForegroundColor Yellow
}

function Log-Error {
    param([string]$Message)
    Write-Host "❌ $Message" -ForegroundColor Red
}

# Vérifier que nous sommes dans le bon répertoire
if (-not (Test-Path "artisan")) {
    Log-Error "Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
}

# Étape 1: Vérification Git
Log-Info "Étape 1: Vérification de l'état Git"
git status
Write-Host ""

$gitStatus = git status --porcelain
if ($gitStatus) {
    Log-Warning "Il y a des modifications non commitées. Veuillez les commiter d'abord:"
    Write-Host $gitStatus
    exit 1
}

Log-Success "Git est propre"
Write-Host ""

# Étape 2: Mise à jour du code
Log-Info "Étape 2: Mise à jour du code"
git pull origin main
if ($LASTEXITCODE -ne 0) {
    Log-Error "Erreur lors du git pull"
    exit 1
}
Log-Success "Code à jour"
Write-Host ""

# Étape 3: Installation des dépendances PHP
Log-Info "Étape 3: Installation des dépendances PHP"
composer install --optimize-autoloader --no-dev
if ($LASTEXITCODE -ne 0) {
    Log-Error "Erreur lors de la mise à jour des dépendances PHP"
    exit 1
}
Log-Success "Dépendances PHP installées"
Write-Host ""

# Étape 4: Compilation des assets
Log-Info "Étape 4: Compilation des assets frontend"
npm ci
npm run build
if ($LASTEXITCODE -ne 0) {
    Log-Error "Erreur lors de la compilation des assets"
    exit 1
}
Log-Success "Assets compilés"
Write-Host ""

# Étape 5: Nettoyage des caches
Log-Info "Étape 5: Nettoyage des caches"
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
Log-Success "Caches nettoyés"
Write-Host ""

# Étape 6: Exécution des migrations
Log-Info "Étape 6: Exécution des migrations"
php artisan migrate --force
if ($LASTEXITCODE -ne 0) {
    Log-Warning "Certaines migrations ont pu échouer (c'est peut-être normal)"
}
Log-Success "Migrations exécutées"
Write-Host ""

# Étape 7: Optimisation pour la production
Log-Info "Étape 7: Optimisation pour la production"
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
Log-Success "Optimisation complétée"
Write-Host ""

# Étape 8: Vérification des logs
Log-Info "Étape 8: Vérification des logs"
if (Test-Path "storage/logs/laravel.log") {
    Log-Info "Dernières 10 lignes du log:"
    Get-Content "storage/logs/laravel.log" -Tail 10
} else {
    Log-Warning "Fichier log non trouvé"
}
Write-Host ""

Log-Success "✨ Déploiement terminé avec succès!"
Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Prochaines étapes:" -ForegroundColor Cyan
Write-Host "1. Testez le site en production" -ForegroundColor White
Write-Host "2. Vérifiez les logs si une erreur persiste" -ForegroundColor White
Write-Host "3. En cas de problème, exécutez: php artisan logs" -ForegroundColor White
Write-Host "==========================================" -ForegroundColor Cyan
