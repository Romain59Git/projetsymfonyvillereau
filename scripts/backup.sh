#!/bin/bash

# ================================================================================
# SCRIPT DE SAUVEGARDE - CDJ Villereau
# ================================================================================

set -e

# Configuration
BACKUP_DIR="./backups"
RETENTION_DAYS=30
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Création du répertoire de sauvegarde
mkdir -p "$BACKUP_DIR"

echo "📦 Démarrage de la sauvegarde..."

# Sauvegarde de la base de données
if docker-compose -f compose.yaml -f compose.prod.yaml ps database | grep -q "Up"; then
    echo "🗄️ Sauvegarde de la base de données PostgreSQL..."
    docker-compose -f compose.yaml -f compose.prod.yaml exec -T database pg_dump -U ${POSTGRES_USER:-villereau_prod} ${POSTGRES_DB:-villereau_prod} > "$BACKUP_DIR/db_backup_$TIMESTAMP.sql"
    
    # Compression
    gzip "$BACKUP_DIR/db_backup_$TIMESTAMP.sql"
    echo "✅ Base de données sauvegardée: db_backup_$TIMESTAMP.sql.gz"
else
    echo "❌ La base de données n'est pas accessible"
    exit 1
fi

# Sauvegarde des uploads
if [ -d "./public/uploads" ]; then
    echo "📁 Sauvegarde des fichiers uploadés..."
    tar -czf "$BACKUP_DIR/uploads_backup_$TIMESTAMP.tar.gz" -C "./public" uploads/
    echo "✅ Uploads sauvegardés: uploads_backup_$TIMESTAMP.tar.gz"
fi

# Nettoyage des anciennes sauvegardes
echo "🧹 Nettoyage des sauvegardes (>$RETENTION_DAYS jours)..."
find "$BACKUP_DIR" -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +$RETENTION_DAYS -delete

echo "✅ Sauvegarde terminée avec succès !"
echo "📊 Sauvegardes disponibles:"
ls -la "$BACKUP_DIR" 