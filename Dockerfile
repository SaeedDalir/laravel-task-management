FROM php:8.3-fpm-alpine

RUN apk add --no-cache git curl zip unzip libzip-dev libpq-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

RUN adduser -D -u 1000 saeed

WORKDIR /var/www/html

USER saeed

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]


