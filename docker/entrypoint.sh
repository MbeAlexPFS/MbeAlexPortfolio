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
touch database/database.sqlite 2>/dev/null || true

# Démarre un MySQL embarqué quand l'app vise 127.0.0.1 (Render / standalone).
if [ "${DB_HOST:-127.0.0.1}" = "127.0.0.1" ]; then
    DB_DATABASE="${DB_DATABASE:-laravel}"
    DB_USERNAME="${DB_USERNAME:-app_user}"
    DB_PASSWORD="${DB_PASSWORD:-secret}"

    mkdir -p /run/mysqld
    chown -R mysql:mysql /var/lib/mysql /run/mysqld 2>/dev/null || true

    mysqld_safe --user=mysql --bind-address=127.0.0.1 >/dev/null 2>&1 &

    i=0
    while [ "$i" -lt 60 ]; do
        if mysqladmin ping --silent >/dev/null 2>&1; then
            break
        fi
        i=$((i + 1))
        sleep 1
    done

    mysql -uroot <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USERNAME'@'127.0.0.1' IDENTIFIED BY '$DB_PASSWORD';
CREATE USER IF NOT EXISTS '$DB_USERNAME'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'127.0.0.1';
GRANT ALL PRIVILEGES ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'localhost';
FLUSH PRIVILEGES;
SQL
fi

php artisan storage:link --no-interaction 2>/dev/null || true

COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction --no-progress || true

php artisan migrate:fresh --force --no-interaction || true

php artisan db:seed --force --no-interaction || true

php-fpm -D

exec apache2ctl -D FOREGROUND
