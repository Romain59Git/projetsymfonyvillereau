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
MAILER_DSN=smtp://serveur-smtp-reel
```

#### 2. Génération des secrets

```bash
# Générer APP_SECRET
php bin/console secrets:generate-keys

# Ou manuellement :
openssl rand -hex 32
```

#### 3. Configuration du domaine

**Dans `.env.prod` :**
- Remplacer `villereau.example.com` par votre domaine réel
- Configurer les DNS pour pointer vers votre serveur

**Dans `compose.prod.yaml` :**
- Modifier la ligne `traefik.http.routers.app.rule=Host('votre-domaine.com')`
- Changer `admin@example.com` par votre email réel

#### 4. Configuration SMTP

**Configurer un serveur email réel dans `.env.prod` :**
```bash
# Exemples de configuration :
MAILER_DSN=smtp://smtp.gmail.com:587?username=email@gmail.com&password=app_password
MAILER_DSN=smtp://smtp.mailgun.org:587?username=postmaster@domain&password=key
MAILER_DSN=smtp://smtp.sendgrid.net:587?username=apikey&password=your_key
```

### 🟡 RECOMMANDÉ - Sécurité et monitoring

#### 5. Configuration GitHub (si CI/CD activé)

**Ajouter ces secrets dans GitHub :**
- `HOST` : IP ou domaine de votre serveur
- `USERNAME` : Utilisateur SSH du serveur
- `SSH_KEY` : Clé privée SSH pour l'accès

#### 6. Sauvegarde

```bash
# Créer le dossier des sauvegardes
mkdir -p backups

# Tester le script de sauvegarde
./scripts/backup.sh
```

#### 7. Tests de sécurité

```bash
# Vérifier les dépendances
composer audit

# Tests unitaires
php bin/phpunit
```

### 🟢 OPTIONNEL - Optimisations

#### 8. Monitoring (optionnel)

```bash
# Pour activer le monitoring complet
docker-compose -f compose.yaml -f compose.prod.yaml -f docker-compose.monitoring.yaml up -d
```

#### 9. Performance

**Optimiser la base de données :**
- Créer les index appropriés
- Configurer PostgreSQL selon votre charge

**CDN et cache :**
- Configurer un CDN pour les assets statiques
- Optimiser le cache Redis si ajouté

## 🚀 Déploiement

### Test local
```bash
# Tester la configuration production localement
docker-compose -f compose.yaml -f compose.prod.yaml up -d --build
```

### Déploiement automatique
```bash
# Lancer le script de déploiement
./scripts/deploy.sh
```

### Vérification post-déploiement
```bash
# Vérifier l'état des services
docker-compose -f compose.yaml -f compose.prod.yaml ps

# Tester les endpoints
curl -f https://votre-domaine.com/health
curl -f https://votre-domaine.com/

# Vérifier les logs
docker-compose -f compose.yaml -f compose.prod.yaml logs -f app
```

## ⚠️ Points de vigilance

- **Jamais de commit des fichiers `.env*`** → Ajoutés au `.gitignore`
- **Changer TOUS les mots de passe par défaut**
- **Tester en local avant production**
- **Sauvegarder avant chaque déploiement**
- **Vérifier les certificats SSL après déploiement**

## 📚 Documentation complète

- [`INSTALL.md`](INSTALL.md) : Guide d'installation détaillé
- [`README-DOCKER.md`](README-DOCKER.md) : Documentation Docker
- [`.github/workflows/`](.github/workflows/) : Configuration CI/CD

## 🆘 Support

En cas de problème :
1. Consulter les logs : `docker-compose logs`
2. Vérifier les health checks : `/health`
3. Restaurer une sauvegarde si nécessaire

---

**🎯 Statut du projet :** Prêt pour le déploiement après configuration des variables d'environnement 