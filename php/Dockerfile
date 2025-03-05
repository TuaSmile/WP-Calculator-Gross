FROM php:7.4-fpm-alpine3.13

RUN docker-php-ext-install mysqli

COPY phpconf.ini "$PHP_INI_DIR/conf.d"

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
