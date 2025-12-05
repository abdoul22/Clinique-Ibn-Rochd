#!/bin/bash

# Script d'aide pour configurer la PWA pour une nouvelle clinique
# Usage: ./setup-pwa-clinic.sh

echo "=========================================="
echo "  Configuration PWA pour Nouvelle Clinique"
echo "=========================================="
echo ""

# Couleurs pour l'affichage
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel${NC}"
    exit 1
fi

echo -e "${YELLOW}Étape 1: Vérification des prérequis${NC}"
echo ""

# Vérifier si le logo existe
if [ -f "public/images/logo.png" ]; then
    echo -e "${GREEN}✓ Logo trouvé: public/images/logo.png${NC}"
else
    echo -e "${YELLOW}⚠ Logo non trouvé. Assurez-vous de placer le logo dans public/images/logo.png${NC}"
fi

# Vérifier les icônes PWA
if [ -f "public/pwa-192x192.png" ] && [ -f "public/pwa-512x512.png" ]; then
    echo -e "${GREEN}✓ Icônes PWA trouvées${NC}"
else
    echo -e "${YELLOW}⚠ Icônes PWA manquantes${NC}"
    echo "  Vous devez créer:"
    echo "    - public/pwa-192x192.png (192x192 pixels)"
    echo "    - public/pwa-512x512.png (512x512 pixels)"
    echo ""
    read -p "Voulez-vous essayer de générer les icônes automatiquement? (y/n) " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan pwa:generate-icons
    fi
fi

echo ""
echo -e "${YELLOW}Étape 2: Vérification de la configuration${NC}"
echo ""

# Vérifier si .env existe
if [ ! -f ".env" ]; then
    echo -e "${RED}❌ Fichier .env non trouvé${NC}"
    exit 1
fi

# Afficher la configuration actuelle
echo "Configuration actuelle de la clinique:"
echo "-----------------------------------"
grep "CLINIQUE_NAME=" .env 2>/dev/null || echo "CLINIQUE_NAME non défini"
grep "CLINIQUE_PRIMARY_COLOR=" .env 2>/dev/null || echo "CLINIQUE_PRIMARY_COLOR non défini"
grep "CLINIQUE_LOGO_PATH=" .env 2>/dev/null || echo "CLINIQUE_LOGO_PATH non défini"
echo ""

echo -e "${YELLOW}Étape 3: Vider le cache${NC}"
php artisan config:clear
php artisan cache:clear
echo -e "${GREEN}✓ Cache vidé${NC}"

echo ""
echo -e "${YELLOW}Étape 4: Construire les assets PWA${NC}"
read -p "Voulez-vous construire les assets maintenant? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    npm run build
    echo -e "${GREEN}✓ Assets construits${NC}"
else
    echo -e "${YELLOW}⚠ N'oubliez pas d'exécuter: npm run build${NC}"
fi

echo ""
echo -e "${YELLOW}Étape 5: Vérification${NC}"
echo ""

# Vérifier que sw.js existe
if [ -f "public/sw.js" ]; then
    echo -e "${GREEN}✓ Service Worker trouvé: public/sw.js${NC}"
else
    echo -e "${RED}❌ Service Worker manquant. Exécutez: npm run build${NC}"
fi

# Vérifier que le manifest est accessible (si serveur local)
echo ""
echo "Pour vérifier le manifest dynamique:"
echo "  Ouvrez: http://localhost/manifest.webmanifest"
echo ""
echo "Pour tester l'installation PWA:"
echo "  1. Ouvrez http://localhost dans Chrome"
echo "  2. Cherchez l'icône d'installation dans la barre d'adresse"
echo ""

echo -e "${GREEN}=========================================="
echo "  Configuration terminée!"
echo "==========================================${NC}"

