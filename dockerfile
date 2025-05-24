# Dockerfile
# Utilisation de Debian Bullseye (avec clés déjà présentes) pour éviter les erreurs GPG
FROM php:8.3-apache-bullseye

# 1) Installer les dépendances PHP et extensions
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libonig-dev libzip-dev zip unzip \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# 2) Installer le driver MongoDB
RUN pecl install mongodb \
 && docker-php-ext-enable mongodb

# 3) Activer mod_rewrite
RUN a2enmod rewrite

# 4) Copier l’application
WORKDIR /var/www/html
COPY . /var/www/html

# 5) Installer Composer et dépendances
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader

# 6) Ajuster les permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
