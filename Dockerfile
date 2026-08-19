FROM composer:2 AS dependencies

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress


FROM php:8.2-fpm-alpine AS runtime

RUN apk add --no-cache oniguruma-dev \
    && docker-php-ext-install -j"$(nproc)" bcmath mbstring opcache \
    && apk del oniguruma-dev

WORKDIR /var/www/html

COPY --from=dependencies /app/vendor ./vendor
COPY docker/php/conf.d/app.ini /usr/local/etc/php/conf.d/app.ini
COPY --chown=www-data:www-data . ./
RUN mkdir -p runtime && chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
