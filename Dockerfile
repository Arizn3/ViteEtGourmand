# Construction et personnalisation de l'image PHP
FROM php:8.3-fpm-alpine

# Dépendances système
RUN apk add --no-cache icu-dev libzip-dev $PHPIZE_DEPS

# Éxtensions PHP
RUN docker-php-ext-install \
    pdo_mysql \
    intl \
    zip

# Éxtension MongoDB
RUN apk add --no-cache openssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Config PHP (upload)
RUN echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/uploads.ini

# Installation de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail de l'application
WORKDIR /var/www/html/app

# Optimisation Docker cache
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

# Copie du projet
COPY . .

# (Optionnel Symfony prod)
RUN php bin/console cache:clear || true