FROM node:24-bookworm-slim AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY vite.config.js ./
COPY postcss.config.js tailwind.config.js ./
RUN npm run build

FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --no-scripts

COPY . ./
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative
RUN php artisan package:discover --ansi

FROM php:8.3-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libonig-dev libpq-dev libsqlite3-dev libzip-dev \
    && docker-php-ext-install bcmath intl mbstring pdo_mysql pdo_pgsql pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=vendor /app ./
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 8080

CMD ["sh", "-c", "php artisan storage:link --force --quiet && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
