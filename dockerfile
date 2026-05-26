# Construction et personnalisation de l'image PHP
FROM php:8.3-fpm-alpine

# Librairies d'éxtension pour PHP
RUN apk add --no-cache icu-dev libzip-dev $PHPIZE_DEPS

# Éxtension PHP
RUN docker-php-ext-install \
    pdo_mysql \
    intl \
    zip

# Éxtension MongoDB
RUN apk add --no-cache openssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Taille pour les fichiers upload
RUN echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/uploads.ini

# Installation de Composer [source, image Composer] [destination, image PHP]
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail de l'application
WORKDIR /var/www/html