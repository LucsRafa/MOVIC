FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends     git     unzip     libzip-dev     libpng-dev     libjpeg62-turbo-dev     libfreetype6-dev     libonig-dev     libicu-dev     libxml2-dev     && docker-php-ext-configure gd --with-freetype --with-jpeg     && docker-php-ext-install -j16 pdo_mysql mbstring intl gd zip bcmath exif     && rm -rf /var/lib/apt/lists/*

COPY php.ini /usr/local/etc/php/conf.d/movic.ini

WORKDIR /var/www/backend
