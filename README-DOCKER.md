# 🐳 Guide Docker pour CDJ Villereau

## 📋 Aperçu des fichiers créés

```
├── Dockerfile                    # Image de production optimisée
├── Dockerfile.dev               # Image de développement avec Xdebug
├── .dockerignore                # Optimisation des builds
├── compose.yaml                 # Configuration Docker de base
├── compose.prod.yaml           # Surcharges pour la production
├── docker-compose.dev.yaml     # Alternative développement
├── docker/
│   ├── nginx/default.conf      # Configuration Nginx
│   └── supervisor/supervisord.conf # Gestionnaire de processus
└── .env.prod                   # Variables d'environnement production
```

## 🚀 Utilisation

### Développement

```bash
# Option 1: Utiliser les surcharges automatiques
docker-compose up -d

# Option 2: Utiliser la config développement explicite  
docker-compose -f compose.yaml -f docker-compose.dev.yaml up -d

# Accès:
# - Application: http://localhost:8080
# - Base de données: localhost:5432
# - Mails (MailPit): http://localhost:8025
# - Adminer (DB): http://localhost:8090
```

### Production

```bash
# Build et démarrage production
docker-compose -f compose.yaml -f compose.prod.yaml up -d --build

# Avec variables d'environnement
docker-compose -f compose.yaml -f compose.prod.yaml --env-file .env.prod up -d

# Accès:
# - Application: http://localhost (via Traefik)
# - HTTPS automatique avec Let's Encrypt
```

## 🛠️ Commandes utiles

```bash
# Voir les logs
docker-compose logs -f app

# Exécuter des commandes Symfony
docker-compose exec app php bin/console cache:clear

# Entrer dans le conteneur
docker-compose exec app sh

# Rebuild complet
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## 🔧 Configuration requise avant production

1. **Dans .env.prod :**
   - Modifier `DATABASE_URL`
   - Configurer `MAILER_DSN`
   - Adapter `TRUSTED_HOSTS`
   - Changer `POSTGRES_PASSWORD`

2. **Dans compose.prod.yaml :**
   - Remplacer `villereau.example.com` par votre domaine
   - Modifier l'email Let's Encrypt
   - Adapter les noms de conteneurs si besoin

## 📊 Fonctionnalités incluses

### Production
- ✅ Image Alpine (légère et sécurisée)
- ✅ PHP 8.4 avec extensions nécessaires
- ✅ Nginx + PHP-FPM supervisés
- ✅ OPcache activé pour les performances
- ✅ Utilisateur non-root pour la sécurité
- ✅ Reverse proxy Traefik avec HTTPS
- ✅ Healthchecks pour tous les services
- ✅ Volumes persistants (DB, uploads, certificats)
- ✅ Mise à jour automatique (Watchtower)

### Développement
- ✅ Hot reload du code
- ✅ Xdebug configuré
- ✅ MailPit pour tester les emails
- ✅ Adminer pour gérer la DB
- ✅ Ports exposés pour accès direct
- ✅ Dépendances de dev installées

## 🚨 Important

- Le fichier `.env.prod` est ignoré par Git (sécurité)
- Changez TOUS les mots de passe avant la production
- Testez en local avant de déployer
- Sauvegardez la base de données régulièrement
