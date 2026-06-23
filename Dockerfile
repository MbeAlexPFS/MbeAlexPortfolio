FROM php:8.5-fpm-alpine AS base

RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    unzip \
    git \
    oniguruma-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    xml \
    gd \
    zip \
    opcache \
    bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --from=oven/bun:latest /usr/local/bin/bun /usr/local/bin/bun

WORKDIR /app

COPY composer.json composer.lock package.json bun.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN bun install --frozen-lockfile

COPY . .
RUN bun run build && rm -rf node_modules

RUN chown -R www-data:www-data storage bootstrap/cache public/build

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
