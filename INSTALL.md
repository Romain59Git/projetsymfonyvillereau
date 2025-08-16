# 🚀 Guide d'Installation - CDJ Villereau

## 📋 Prérequis

- Docker & Docker Compose
- Git
- Domaine configuré (pour la production)

## 🔧 Installation

### 1. Configuration des variables d'environnement

Créez le fichier `.env` pour le développement :

```bash
# Environment
APP_ENV=dev
APP_DEBUG=1
APP_SECRET=your_secret_key_here

# Database
DATABASE_URL="postgresql://villereau_user:password@127.0.0.1:5432/villereau_dev?serverVersion=16&charset=utf8"

# Mailer
MAILER_DSN=smtp://localhost:1025

# Security
TRUSTED_HOSTS=localhost,127.0.0.1,::1

# Upload
UPLOAD_PATH=/public/uploads
```

Créez le fichier `.env.prod` pour la production :

```bash
# Environment
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=VOTRE_CLE_SECRETE_64_CARACTERES

# Database
DATABASE_URL="postgresql://villereau_prod:MOT_DE_PASSE_FORT@database:5432/villereau_prod?serverVersion=16&charset=utf8"
POSTGRES_DB=villereau_prod
POSTGRES_USER=villereau_prod
POSTGRES_PASSWORD=MOT_DE_PASSE_FORT

# Mailer
MAILER_DSN=smtp://votre-serveur-smtp:587?username=username&password=password

# Security
TRUSTED_HOSTS=^votre-domaine\.com$

# SSL & Domain
DOMAIN_NAME=votre-domaine.com
SSL_EMAIL=admin@votre-domaine.com
```

### 2. Développement

```bash
# Démarrage
docker-compose up -d

# Installation des dépendances
docker-compose exec app composer install

# Migrations
docker-compose exec app php bin/console doctrine:migrations:migrate

# Accès : http://localhost:8080
```

### 3. Production

```bash
# Déploiement automatique
./scripts/deploy.sh

# Ou manuel
docker-compose -f compose.yaml -f compose.prod.yaml up -d --build
```

## 🔐 Sécurité

### Variables à changer OBLIGATOIREMENT :

- `APP_SECRET` : Générer avec `php bin/console secrets:generate-keys`
- `POSTGRES_PASSWORD` : Mot de passe fort unique
- `DOMAIN_NAME` : Votre domaine réel
- `SSL_EMAIL` : Votre email pour Let's Encrypt
- `MAILER_DSN` : Configuration SMTP réelle

### Permissions

```bash
chmod +x scripts/*.sh
chmod 755 public/uploads
chmod 777 var/
```

## 📊 Monitoring

- Health Check : `/health`
- Logs : `docker-compose logs -f app`
- Métriques : Intégration Prometheus disponible

## 🔄 Sauvegarde

```bash
# Sauvegarde automatique
./scripts/backup.sh

# Restauration
docker-compose exec database psql -U username -d database < backup.sql
```

## ⚡ Performance

- OPcache activé en production
- Assets compilés
- Cache Symfony optimisé
- Base de données PostgreSQL indexée

## 🆘 Dépannage

### Logs

```bash
docker-compose logs app
docker-compose logs database
```

### Reset complet

```bash
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

### Tests

```bash
docker-compose exec app php bin/phpunit
``` 