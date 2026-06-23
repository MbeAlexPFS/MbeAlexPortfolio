#!/bin/sh
set -e

php artisan storage:link --no-interaction 2>/dev/null || true
php artisan migrate --force --no-interaction

php-fpm -D

exec apache2ctl -D FOREGROUND
