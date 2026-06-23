#!/bin/sh
set -e

if [ ! -f /app/storage/framework/views/.gitkeep ]; then
    mkdir -p /app/storage/framework/views
    mkdir -p /app/storage/framework/cache/data
    mkdir -p /app/storage/framework/sessions
    mkdir -p /app/storage/logs
    mkdir -p /app/storage/backup
fi

php artisan storage:link --no-interaction 2>/dev/null || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force --no-interaction

exec /usr/bin/supervisord -c /etc/supervisord.conf
