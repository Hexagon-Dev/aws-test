    FROM composer:latest AS composer
    FROM php:7.4-fpm-alpine

    RUN apk add --no-cache bash

    # PHP extensions installer
    COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

    # Composer
    COPY --from=composer /usr/bin/composer /usr/bin/composer
    RUN mkdir /.composer && chown -R 1000:1000 /.composer

    RUN install-php-extensions pdo_mysql gmp gd zip exif pcntl

    RUN mkdir -p /usr/src/php/ext/redis; \
        curl -fsSL --ipv4 https://github.com/phpredis/phpredis/archive/5.3.2.tar.gz | tar xvz -C "/usr/src/php/ext/redis" --strip 1; \
        docker-php-ext-install redis

    # Setup Working Dir
    WORKDIR /app

    # setup
    COPY . /app
    RUN [ ! -e ".env" ] && cp .env.example .env || echo 0
    RUN composer install --no-dev --no-interaction --ansi

    USER 1000:1000
