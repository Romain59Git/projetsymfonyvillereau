# 🏓 CDJ Villereau - Application Symfony

Site web officiel du Club de Tennis de Table de Villereau

## 📋 Checklist Pré-Déploiement

### 🔴 OBLIGATOIRE - Actions critiques

#### 1. Configuration des fichiers d'environnement

**Créer le fichier `.env` :**

```bash
# Copier depuis le template
cp .env.example .env

# Puis modifier ces valeurs obligatoirement :
APP_SECRET=générer_une_clé_secrète_de_64_caractères
DATABASE_URL=postgresql://user:password@host:5432/database
```

**Créer le fichier `.env.prod` :**

```bash
# Copier depuis le template
cp .env.prod.example .env.prod

# MODIFIER ABSOLUMENT ces valeurs :
APP_SECRET=clé_secrète_production_différente_dev
POSTGRES_PASSWORD=mot_de_passe_fort_unique
DOMAIN_NAME=votre-domaine-reel.com
SSL_EMAIL=votre-email@domaine.com
```

#### 2. Sécurité des mots de passe

**CHANGER IMMÉDIATEMENT :**

- Mot de passe PostgreSQL production
- Clés secrètes APP_SECRET (dev et prod)
- Comptes administrateur par défaut

#### 3. Certificats SSL

**Configurer Certbot :**

```bash
# Définir le domaine réel
DOMAIN_NAME=cdjvillereau.fr

# Définir l'email pour les notifications SSL
SSL_EMAIL=admin@cdjvillereau.fr
```

#### 4. Sauvegarde initiale

**Avant premier déploiement :**

```bash
# Créer le répertoire de sauvegarde
mkdir -p backups

# Tester le script de sauvegarde
./scripts/backup.sh
```

### 🟡 RECOMMANDÉ - Optimisations

#### 1. Monitoring et logs

```bash
# Activer le monitoring
docker-compose -f docker-compose.monitoring.yaml up -d

# Surveiller les logs
docker-compose logs -f app
```

#### 2. Tests avant déploiement

```bash
# Tests automatisés
./scripts/docker-test.sh

# Health check
curl http://localhost/health
```

#### 3. Performance

- Activer le cache Redis
- Configurer les logs en production
- Optimiser les images Docker

### 🟢 OPTIONNEL - Amélirations futures

#### 1. CI/CD

- Pipeline GitHub Actions
- Tests automatiques
- Déploiement automatique

#### 2. Monitoring avancé

- Métriques application
- Alertes automatiques
- Dashboard de supervision

## 🚀 Déploiement

### Déploiement de développement

```bash
# Build et démarrage
docker-compose up -d

# Vérification
curl http://localhost/health
```

### Déploiement de production

```bash
# Production avec SSL
docker-compose -f compose.prod.yaml up -d

# Vérification SSL
curl https://votre-domaine.com/health
```

## 🛠️ Commandes utiles

### Base de données

```bash
# Migrations
docker-compose exec app php bin/console doctrine:migrations:migrate

# Créer un admin
docker-compose exec app php bin/console app:create-admin admin@example.com password123

# Sauvegarde
./scripts/backup.sh

# Restauration
docker-compose exec postgres psql -U postgres -d cdjvillereau < backup_date.sql
```

### Maintenance

```bash
# Logs de l'application
docker-compose logs -f app

# Logs PostgreSQL
docker-compose logs -f postgres

# Redémarrage complet
docker-compose down && docker-compose up -d

# Nettoyage
docker system prune -a
```

### Tests et qualité

```bash
# Tests unitaires
docker-compose exec app php bin/phpunit

# Tests d'intégration
./scripts/docker-test.sh

# Code style
docker-compose exec app vendor/bin/php-cs-fixer fix
```

## 📁 Structure du projet

```text
├── src/                    # Code source Symfony
│   ├── Controller/         # Contrôleurs
│   ├── Entity/            # Entités Doctrine
│   ├── Form/              # Formulaires Symfony
│   └── Repository/        # Repositories Doctrine
├── templates/             # Templates Twig
├── config/                # Configuration Symfony
├── docker/                # Configuration Docker
├── scripts/               # Scripts de déploiement
└── tests/                 # Tests automatisés
```

## 🔒 Sécurité

### Authentification

- Système de connexion sécurisé
- Hashage des mots de passe avec bcrypt
- Protection CSRF sur tous les formulaires
- Gestion des rôles (USER, ADMIN)

### Protection des données

- Respect RGPD
- Chiffrement des données sensibles
- Logs sécurisés sans informations personnelles
- Sauvegarde chiffrée

### Infrastructure

- HTTPS obligatoire en production
- Headers de sécurité configurés
- Isolation des containers Docker
- Accès base de données restreint

## 📧 Support

En cas de problème :

1. Consulter les logs : `docker-compose logs`
2. Vérifier les health checks : `/health`
3. Restaurer une sauvegarde si nécessaire

---

**🎯 Statut du projet :** Prêt pour le déploiement après configuration des variables d'environnement
