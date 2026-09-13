#!/bin/sh
set -e

PORT="${PORT:-80}"

sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:8000>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache/data storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R a+w storage bootstrap/cache 2>/dev/null || true
touch storage/logs/laravel.log 2>/dev/null || true
chmod a+rw storage/logs/laravel.log 2>/dev/null || true

php artisan storage:link --no-interaction 2>/dev/null || true

COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction --no-progress || true

# DB externe : la base vide est initialisée une seule fois, ensuite on migre seulement.
if php artisan migrate:status --no-interaction >/dev/null 2>&1; then
    php artisan migrate --force --no-interaction || true
else
    php artisan migrate:fresh --force --no-interaction || true
    php artisan db:seed --force --no-interaction || true
fi

php-fpm -D

exec apache2ctl -D FOREGROUND
