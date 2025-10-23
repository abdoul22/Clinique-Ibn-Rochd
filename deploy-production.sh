#!/bin/bash

# 🚀 Script de déploiement en production - Ibn Rochd

echo "=========================================="
echo "🚀 Déploiement Production - Ibn Rochd"
echo "=========================================="
echo ""

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
log_info() {
    echo -e "${BLUE}ℹ️ $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    log_error "Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

log_info "Étape 1: Vérification de l'état Git"
git status
echo ""

# Vérifier s'il y a des modifications non commitées
if ! git diff-index --quiet HEAD --; then
    log_warning "Il y a des modifications non commitées. Veuillez les commiter d'abord:"
    git status --short
    exit 1
fi

log_success "Git est propre"
echo ""

log_info "Étape 2: Mise à jour du code"
git pull origin main
if [ $? -ne 0 ]; then
    log_error "Erreur lors du git pull"
    exit 1
fi
log_success "Code à jour"
echo ""

log_info "Étape 3: Installation des dépendances PHP"
composer install --optimize-autoloader --no-dev
if [ $? -ne 0 ]; then
    log_error "Erreur lors de la mise à jour des dépendances PHP"
    exit 1
fi
log_success "Dépendances PHP installées"
echo ""

log_info "Étape 4: Compilation des assets frontend"
npm ci
npm run build
if [ $? -ne 0 ]; then
    log_error "Erreur lors de la compilation des assets"
    exit 1
fi
log_success "Assets compilés"
echo ""

log_info "Étape 5: Nettoyage des caches"
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
log_success "Caches nettoyés"
echo ""

log_info "Étape 6: Exécution des migrations"
php artisan migrate --force
if [ $? -ne 0 ]; then
    log_warning "Certaines migrations ont pu échouer (c'est peut-être normal)"
fi
log_success "Migrations exécutées"
echo ""

log_info "Étape 7: Optimisation pour la production"
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
log_success "Optimisation complétée"
echo ""

log_info "Étape 8: Vérification des logs"
if [ -f "storage/logs/laravel.log" ]; then
    log_info "Dernières 10 lignes du log:"
    tail -n 10 storage/logs/laravel.log
else
    log_warning "Fichier log non trouvé"
fi
echo ""

log_success "✨ Déploiement terminé avec succès!"
echo ""
echo "=========================================="
echo "Prochaines étapes:"
echo "1. Testez le site en production"
echo "2. Vérifiez les logs si une erreur persiste"
echo "3. En cas de problème, exécutez: php artisan logs"
echo "=========================================="
