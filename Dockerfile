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

COPY --from=node:22-alpine /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-alpine /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

WORKDIR /app

COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci

COPY . .
RUN npm run build && rm -rf node_modules

RUN chown -R www-data:www-data storage bootstrap/cache public/build

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
