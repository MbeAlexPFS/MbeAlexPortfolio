FROM php:8.5-fpm

RUN apt-get update && apt-get install -y \
    apache2 \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    gd \
    zip \
    bcmath

RUN docker-php-ext-install opcache

RUN a2enmod proxy proxy_fcgi rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --from=oven/bun:latest /usr/local/bin/bun /usr/local/bin/bun

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /app

COPY composer.json composer.lock package.json bun.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN bun install --frozen-lockfile

COPY . .
RUN bun run build && rm -rf node_modules

RUN chown -R www-data:www-data storage bootstrap/cache public/build

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
