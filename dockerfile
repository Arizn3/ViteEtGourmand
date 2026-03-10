# Construction et personnalisation de l'image PHP
FROM php:8.3-fpm-alpine

# Librairies d'éxtension pour PHP
RUN apk add --no-cache icu-dev libzip-dev $PHPIZE_DEPS

# Éxtension pour PHP
RUN docker-php-ext-install \
    pdo_mysql \
    intl \
    zip

# Éxtension MongoDB
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Installation de Composer [source, image Composer] [destination, image PHP]
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail de l'application
WORKDIR /var/www/html