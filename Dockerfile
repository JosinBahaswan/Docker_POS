FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql zip gd \
    && a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html