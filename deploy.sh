#!/bin/bash
# deploy.sh - Script de déploiement pour ibnrochd

# Variables de configuration
SERVER="82.25.113.122"
PORT="65002"
USER="u790866683"
REMOTE_PATH="/home/u790866683/domains/ibnrochd.pro/public_html/ibnrochd"

echo "🚀 Début du déploiement..."

# 1. Mettre à jour le code local
echo "📥 Récupération des dernières modifications..."
git pull origin main

# 2. Synchronisation des fichiers
echo "📤 Envoi des fichiers vers le serveur..."

# Créer un archive temporaire en excluant les dossiers
# Pour Windows Git Bash - créer dans le dossier temp
mkdir -p /tmp
tar --exclude='.git' --exclude='node_modules' --exclude='.env' --exclude='storage/logs/*' --exclude='storage/framework/cache/*' -czf /tmp/temp_deploy.tar.gz .

# Envoyer l'archive
scp -P $PORT /tmp/temp_deploy.tar.gz $USER@$SERVER:$REMOTE_PATH/

# Extraire sur le serveur et nettoyer
ssh -p $PORT $USER@$SERVER "cd $REMOTE_PATH && tar -xzf temp_deploy.tar.gz && rm temp_deploy.tar.gz"

# Nettoyer localement
rm /tmp/temp_deploy.tar.gz

echo "✅ Déploiement terminé !"
