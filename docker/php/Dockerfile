FROM php:7.1-fpm

RUN apt-get update

RUN pecl install xdebug-2.6.1 \
    && docker-php-ext-enable xdebug

WORKDIR /var/www
CMD php index.php
