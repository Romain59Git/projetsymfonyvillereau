# ================================================================================
# DOCKERFILE POUR APPLICATION SYMFONY EN PRODUCTION
# ================================================================================
# Image de base : PHP 8.4 FPM Alpine (légère et sécurisée)
FROM php:8.4-fpm-alpine

# Métadonnées
LABEL maintainer="Votre nom <votre.email@example.com>"
LABEL description="Application Symfony CDJ Villereau"

# Variables d'environnement
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_NO_INTERACTION=1

# Installation des dépendances système et extensions PHP nécessaires
RUN apk add --no-cache \
    # Outils système
    git \
    unzip \
    curl \
    # Bibliothèques pour extensions PHP
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    postgresql-dev \
    oniguruma-dev \
    # Nginx pour servir l'application
    nginx \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        zip \
        intl \
        gd \
        opcache \
        mbstring \
    && rm -rf /var/cache/apk/*

# Installation de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configuration PHP pour la production
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/upload.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/upload.ini \
    && echo "max_execution_time=300" > /usr/local/etc/php/conf.d/execution.ini

# Configuration OPcache pour la performance
RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=7963" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Création utilisateur non-root pour la sécurité
RUN addgroup -g 1000 -S symfony \
    && adduser -u 1000 -S symfony -G symfony

# Répertoire de travail
WORKDIR /var/www/symfony

# Copie des fichiers de configuration Composer (pour cache des dépendances)
COPY composer.json composer.lock ./

# Installation des dépendances PHP (sans les dépendances de dev)
RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && composer clear-cache

# Copie du code source
COPY . .

# Configuration des permissions
RUN chown -R symfony:symfony /var/www/symfony \
    && chmod -R 755 /var/www/symfony \
    && chmod -R 777 /var/www/symfony/var

# Compilation des assets pour la production
RUN php bin/console asset-map:compile --env=prod

# Configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Configuration Supervisor (pour gérer PHP-FPM + Nginx)
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# Nettoyage du cache et optimisations finales
RUN php bin/console cache:clear --env=prod \
    && php bin/console cache:warmup --env=prod \
    && composer dump-autoload --optimize --classmap-authoritative \
    && rm -rf /tmp/* /var/tmp/*

# Switch vers utilisateur non-root
USER symfony

# Exposition du port 80
EXPOSE 80

# Commande de démarrage
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/health || exit 1
