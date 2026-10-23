FROM php:8.1-fpm-alpine as app

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# RUN docker-php-ext-install pdo pdo_mysql
RUN set -eux; \
    install-php-extensions pdo pdo_mysql;

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:2.4 /usr/bin/composer /usr/bin/composer

COPY ./app/composer.* ./

RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction

COPY ./app .

RUN composer dump-autoload --optimize

FROM app AS app_dev

ENV XDEBUG_MODE=debug

RUN set -eux; \
    install-php-extensions xdebug

COPY ./php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
