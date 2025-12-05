#!/bin/bash

# Script de diagnostic PWA pour ibnrochd.pro
# Usage: ./test-pwa-ibnrochd.sh

DOMAIN="https://ibnrochd.pro/ibnrochd/public"

echo "🔍 Diagnostic PWA pour $DOMAIN"
echo "=================================="
echo ""

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 1. Vérifier HTTPS
echo -e "${YELLOW}1. Vérification HTTPS...${NC}"
if curl -s -o /dev/null -w "%{http_code}" "$DOMAIN" --max-time 5 | grep -q "200"; then
    echo -e "${GREEN}✓ Site accessible en HTTPS${NC}"
else
    echo -e "${RED}❌ Site non accessible${NC}"
    exit 1
fi
echo ""

# 2. Vérifier le manifest
echo -e "${YELLOW}2. Vérification Manifest...${NC}"
MANIFEST_URL="$DOMAIN/manifest.webmanifest"
MANIFEST_RESPONSE=$(curl -s -w "\n%{http_code}" "$MANIFEST_URL" --max-time 5 2>/dev/null)
HTTP_CODE=$(echo "$MANIFEST_RESPONSE" | tail -1)
MANIFEST_BODY=$(echo "$MANIFEST_RESPONSE" | sed '$d')

if [ "$HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✓ Manifest accessible (HTTP $HTTP_CODE)${NC}"
    
    if command -v jq &> /dev/null; then
        NAME=$(echo "$MANIFEST_BODY" | jq -r '.name // "N/A"' 2>/dev/null)
        START_URL=$(echo "$MANIFEST_BODY" | jq -r '.start_url // "N/A"' 2>/dev/null)
        SCOPE=$(echo "$MANIFEST_BODY" | jq -r '.scope // "N/A"' 2>/dev/null)
        
        echo "   Nom: $NAME"
        echo "   start_url: $START_URL"
        echo "   scope: $SCOPE"
        
        # Vérifier les icônes
        echo ""
        echo "   Icônes:"
        echo "$MANIFEST_BODY" | jq -r '.icons[].src' 2>/dev/null | while read icon; do
            if [[ "$icon" == http* ]]; then
                echo -e "   ${GREEN}✓ $icon (URL absolue)${NC}"
            else
                echo -e "   ${YELLOW}⚠ $icon (URL relative)${NC}"
            fi
        done
    else
        echo "   (Installez 'jq' pour un diagnostic détaillé)"
        echo "$MANIFEST_BODY" | head -20
    fi
else
    echo -e "${RED}❌ Manifest non accessible (HTTP $HTTP_CODE)${NC}"
    echo "   URL testée: $MANIFEST_URL"
fi
echo ""

# 3. Vérifier le Service Worker
echo -e "${YELLOW}3. Vérification Service Worker...${NC}"
SW_URL="$DOMAIN/sw.js"
SW_HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$SW_URL" --max-time 5 2>/dev/null)

if [ "$SW_HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✓ Service Worker accessible (HTTP $SW_HTTP_CODE)${NC}"
else
    echo -e "${RED}❌ Service Worker non accessible (HTTP $SW_HTTP_CODE)${NC}"
    echo "   URL testée: $SW_URL"
    echo "   Solution: Exécutez 'npm run build' sur le serveur"
fi
echo ""

# 4. Vérifier les icônes
echo -e "${YELLOW}4. Vérification des Icônes PWA...${NC}"
check_icon() {
    local url=$1
    local name=$2
    local http_code=$(curl -s -o /dev/null -w "%{http_code}" "$url" --max-time 5 2>/dev/null)
    
    if [ "$http_code" = "200" ]; then
        echo -e "${GREEN}✓ $name accessible (HTTP $http_code)${NC}"
    else
        echo -e "${RED}❌ $name non accessible (HTTP $http_code)${NC}"
    fi
}

check_icon "$DOMAIN/pwa-192x192.png" "pwa-192x192.png"
check_icon "$DOMAIN/pwa-512x512.png" "pwa-512x512.png"
echo ""

# 5. Résumé
echo -e "${YELLOW}=================================="
echo "Résumé:${NC}"
echo ""

if [ "$HTTP_CODE" = "200" ] && [ "$SW_HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✅ Les éléments de base sont accessibles${NC}"
    echo ""
    echo "Prochaines étapes:"
    echo "1. Ouvrez $DOMAIN dans Chrome"
    echo "2. F12 → Onglet Application → Vérifiez Manifest et Service Workers"
    echo "3. Cherchez l'icône d'installation dans la barre d'adresse"
    echo ""
    echo "Si l'icône n'apparaît pas:"
    echo "- Vérifiez la console pour les erreurs"
    echo "- Vérifiez que le scope dans le manifest correspond au chemin de l'app"
    echo "- Videz le cache du navigateur (Ctrl+Shift+Delete)"
else
    echo -e "${RED}❌ Des problèmes ont été détectés${NC}"
    echo ""
    echo "Actions recommandées:"
    if [ "$HTTP_CODE" != "200" ]; then
        echo "- Vérifiez la route: php artisan route:list | grep manifest"
        echo "- Vérifiez que le fichier .htaccess permet les fichiers .webmanifest"
    fi
    if [ "$SW_HTTP_CODE" != "200" ]; then
        echo "- Exécutez: npm run build sur le serveur"
        echo "- Vérifiez que public/sw.js existe"
    fi
fi

echo ""
echo "Pour plus de détails: CONFIGURATION_PWA_SOUS_DOSSIER.md"

