FROM php:8.1-fpm

RUN apt-get update

RUN apt-get install -y \
        libzip-dev \
        unzip \
        && docker-php-ext-install zip

# Install GMP
RUN apt-get install -y \
        libgmp-dev \
    && docker-php-ext-install gmp

# Install xdebug
RUN pecl install xdebug-3.1.4 \
   && docker-php-ext-enable xdebug

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --snapshot
RUN composer --version

RUN cp /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime
RUN echo "America/Sao_Paulo" >  /etc/timezone

COPY php.ini /usr/local/etc/php/conf.d/

WORKDIR /app
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["bash", "/usr/local/bin/entrypoint.sh"]