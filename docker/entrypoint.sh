#!/usr/bin/env bash
set -e

cd /var/www/html

# Garante storage/framework
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Storage symlink (para uploads servidos via /storage)
php artisan storage:link --force || true

# Espera o banco ficar disponível (até 30s)
if [ -n "$DB_HOST" ]; then
    for i in $(seq 1 30); do
        if php -r "try{new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');exit(0);}catch(Exception \$e){exit(1);}" 2>/dev/null; then
            break
        fi
        echo "Waiting for database... ($i/30)"
        sleep 1
    done
fi

# Migrations (--force porque está em produção)
php artisan migrate --force --no-interaction || true

# Seeders só na primeira subida (controlado por env)
if [ "${DB_SEED_ON_BOOT:-false}" = "true" ]; then
    php artisan db:seed --force --no-interaction || true
fi

# Cache otimizado
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
