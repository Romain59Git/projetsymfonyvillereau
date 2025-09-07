# Villereau Symfony

Application web Symfony pour le Club de Tennis de Table de Villereau.

## Configuration rapide

1. Cloner le repository
2. Copier `.env` vers `.env.local`
3. Configurer `DATABASE_URL=mysql://user:password@host:3306/database`
4. `docker-compose up -d`
5. `docker-compose exec app composer install`
6. `docker-compose exec app php bin/console doctrine:migrations:migrate`

## Technologies

- Symfony 7
- MySQL 8.0
- Docker
- Bootstrap 5
- Stimulus.js

## Structure

- `src/Controller/` - Contrôleurs Symfony
- `src/Entity/` - Entités Doctrine
- `templates/` - Templates Twig
- `assets/` - Assets frontend
- `migrations/` - Migrations de base de données

## Développement

```bash
# Démarrer l'environnement
docker-compose up -d

# Installer les dépendances
docker-compose exec app composer install

# Créer la base de données
docker-compose exec app php bin/console doctrine:database:create

# Exécuter les migrations
docker-compose exec app php bin/console doctrine:migrations:migrate

# Créer un admin
docker-compose exec app php bin/console app:create-admin
```

## Production

```bash
# Déploiement
docker-compose -f compose.prod.yaml up -d

# Optimisations
docker-compose exec app composer install --no-dev --optimize-autoloader
docker-compose exec app php bin/console cache:clear --env=prod
```
