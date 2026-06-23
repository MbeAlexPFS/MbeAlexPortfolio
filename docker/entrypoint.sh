#!/bin/sh
set -e

PORT="${PORT:-80}"

sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:8000>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf

php artisan storage:link --no-interaction 2>/dev/null || true

php artisan migrate --force --no-interaction 2>/dev/null || true

php-fpm -D

exec apache2ctl -D FOREGROUND
