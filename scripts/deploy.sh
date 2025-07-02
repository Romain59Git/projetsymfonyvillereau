#!/bin/bash

# ================================================================================
# SCRIPT DE DÉPLOIEMENT PRODUCTION - CDJ Villereau
# ================================================================================

set -e

echo "🚀 Démarrage du déploiement en production..."

# Vérifications préalables
if [ ! -f ".env.prod" ]; then
    echo "❌ Erreur: Le fichier .env.prod est requis pour le déploiement"
    exit 1
fi

# Backup de la base de données
echo "📦 Sauvegarde de la base de données..."
docker-compose -f compose.yaml -f compose.prod.yaml exec -T database pg_dump -U ${POSTGRES_USER} ${POSTGRES_DB} > "./backups/backup_$(date +%Y%m%d_%H%M%S).sql"

# Build et déploiement
echo "🔨 Build de l'image Docker..."
docker-compose -f compose.yaml -f compose.prod.yaml build --no-cache

echo "⚡ Démarrage des services..."
docker-compose -f compose.yaml -f compose.prod.yaml up -d

# Attendre que l'application soit prête
echo "⏳ Attente du démarrage de l'application..."
sleep 30

# Tests de santé
echo "🔍 Vérification de l'état des services..."
docker-compose -f compose.yaml -f compose.prod.yaml ps

# Migrations
echo "🗄️ Exécution des migrations..."
docker-compose -f compose.yaml -f compose.prod.yaml exec app php bin/console doctrine:migrations:migrate --no-interaction

# Cache warmup
echo "🔥 Préchargement du cache..."
docker-compose -f compose.yaml -f compose.prod.yaml exec app php bin/console cache:warmup --env=prod

# Test de connectivité
echo "🌐 Test de connectivité..."
if curl -f http://localhost/health > /dev/null 2>&1; then
    echo "✅ Déploiement réussi !"
else
    echo "❌ Erreur: L'application ne répond pas"
    exit 1
fi

echo "🎉 Déploiement terminé avec succès !" 