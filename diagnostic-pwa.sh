#!/bin/bash

# Script de diagnostic PWA pour production
# Usage: ./diagnostic-pwa.sh https://votre-domaine.com

if [ -z "$1" ]; then
    echo "Usage: $0 https://votre-domaine.com"
    exit 1
fi

DOMAIN="$1"
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
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$DOMAIN" --max-time 5)
if [[ "$DOMAIN" == https://* ]]; then
    echo -e "${GREEN}✓ URL HTTPS détectée${NC}"
else
    echo -e "${RED}❌ ERREUR: L'URL doit être en HTTPS pour les PWA${NC}"
    echo "   Les PWA nécessitent HTTPS en production (sauf localhost)"
fi
echo ""

# 2. Vérifier le manifest
echo -e "${YELLOW}2. Vérification Manifest...${NC}"
MANIFEST_URL="$DOMAIN/manifest.webmanifest"
MANIFEST_RESPONSE=$(curl -s -w "\n%{http_code}" "$MANIFEST_URL" --max-time 5)
HTTP_CODE=$(echo "$MANIFEST_RESPONSE" | tail -1)
MANIFEST_BODY=$(echo "$MANIFEST_RESPONSE" | sed '$d')

if [ "$HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✓ Manifest accessible (HTTP $HTTP_CODE)${NC}"
    
    # Vérifier que c'est du JSON valide
    if echo "$MANIFEST_BODY" | jq . > /dev/null 2>&1; then
        echo -e "${GREEN}✓ Manifest JSON valide${NC}"
        
        # Extraire les informations
        NAME=$(echo "$MANIFEST_BODY" | jq -r '.name // "N/A"')
        SHORT_NAME=$(echo "$MANIFEST_BODY" | jq -r '.short_name // "N/A"')
        echo "   Nom: $NAME"
        echo "   Nom court: $SHORT_NAME"
        
        # Vérifier les icônes
        ICONS=$(echo "$MANIFEST_BODY" | jq -r '.icons[].src' 2>/dev/null)
        if [ -n "$ICONS" ]; then
            echo -e "${GREEN}✓ Icônes définies dans le manifest${NC}"
            echo "$ICONS" | while read icon; do
                if [[ "$icon" == http* ]]; then
                    echo "   ✓ $icon (URL absolue)"
                else
                    echo -e "   ${YELLOW}⚠ $icon (URL relative - peut causer des problèmes)${NC}"
                fi
            done
        else
            echo -e "${RED}❌ Aucune icône définie dans le manifest${NC}"
        fi
    else
        echo -e "${RED}❌ Manifest JSON invalide${NC}"
    fi
else
    echo -e "${RED}❌ Manifest non accessible (HTTP $HTTP_CODE)${NC}"
    echo "   Vérifiez la route: php artisan route:list | grep manifest"
fi
echo ""

# 3. Vérifier le Service Worker
echo -e "${YELLOW}3. Vérification Service Worker...${NC}"
SW_URL="$DOMAIN/sw.js"
SW_HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$SW_URL" --max-time 5)

if [ "$SW_HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✓ Service Worker accessible (HTTP $SW_HTTP_CODE)${NC}"
    
    # Vérifier le Content-Type
    CONTENT_TYPE=$(curl -s -I "$SW_URL" | grep -i "content-type" | cut -d' ' -f2 | tr -d '\r')
    if [[ "$CONTENT_TYPE" == *"javascript"* ]] || [[ "$CONTENT_TYPE" == *"application/javascript"* ]]; then
        echo -e "${GREEN}✓ Content-Type correct: $CONTENT_TYPE${NC}"
    else
        echo -e "${YELLOW}⚠ Content-Type: $CONTENT_TYPE (devrait être application/javascript)${NC}"
    fi
else
    echo -e "${RED}❌ Service Worker non accessible (HTTP $SW_HTTP_CODE)${NC}"
    echo "   Exécutez: npm run build"
    echo "   Vérifiez que public/sw.js existe"
fi
echo ""

# 4. Vérifier les icônes
echo -e "${YELLOW}4. Vérification des Icônes PWA...${NC}"
ICON_192_URL="$DOMAIN/pwa-192x192.png"
ICON_512_URL="$DOMAIN/pwa-512x512.png"

check_icon() {
    local url=$1
    local name=$2
    local http_code=$(curl -s -o /dev/null -w "%{http_code}" "$url" --max-time 5)
    
    if [ "$http_code" = "200" ]; then
        local content_type=$(curl -s -I "$url" | grep -i "content-type" | cut -d' ' -f2 | tr -d '\r')
        if [[ "$content_type" == *"image"* ]]; then
            echo -e "${GREEN}✓ $name accessible (HTTP $http_code, $content_type)${NC}"
        else
            echo -e "${YELLOW}⚠ $name accessible mais Content-Type incorrect: $content_type${NC}"
        fi
    else
        echo -e "${RED}❌ $name non accessible (HTTP $http_code)${NC}"
    fi
}

check_icon "$ICON_192_URL" "pwa-192x192.png"
check_icon "$ICON_512_URL" "pwa-512x512.png"
echo ""

# 5. Résumé
echo -e "${YELLOW}=================================="
echo "Résumé du diagnostic:${NC}"
echo ""

if [[ "$DOMAIN" == https://* ]] && [ "$HTTP_CODE" = "200" ] && [ "$SW_HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✅ Les éléments de base sont en place${NC}"
    echo ""
    echo "Pour tester l'installation:"
    echo "1. Ouvrez $DOMAIN dans Chrome"
    echo "2. F12 → Onglet Application → Vérifiez Manifest et Service Workers"
    echo "3. Cherchez l'icône d'installation dans la barre d'adresse"
else
    echo -e "${RED}❌ Des problèmes ont été détectés${NC}"
    echo ""
    echo "Actions recommandées:"
    if [[ "$DOMAIN" != https://* ]]; then
        echo "- Configurez HTTPS (Let's Encrypt, Cloudflare, etc.)"
    fi
    if [ "$HTTP_CODE" != "200" ]; then
        echo "- Vérifiez que la route /manifest.webmanifest fonctionne"
        echo "- Exécutez: php artisan route:list | grep manifest"
    fi
    if [ "$SW_HTTP_CODE" != "200" ]; then
        echo "- Exécutez: npm run build"
        echo "- Vérifiez que public/sw.js existe"
    fi
fi

echo ""
echo "Pour plus de détails, consultez: DEPANNAGE_PWA_PRODUCTION.md"

