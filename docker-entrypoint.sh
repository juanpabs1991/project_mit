#!/bin/sh
set -e

# Wait for MySQL to be ready before running migrations.
# The DB host comes from docker-compose (service name "db").
echo "Waiting for MySQL at ${DB_HOST:-db}:${DB_PORT:-3306}..."
until php -r "new PDO('mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306};', '${DB_USERNAME:-attendly}', '${DB_PASSWORD:-secret}');" 2>/dev/null; do
    echo "  MySQL not ready yet, retrying in 2s..."
    sleep 2
done
echo "MySQL is ready."

# Generate APP_KEY if one is not already set in .env
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations and seeders
echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --force || true

# Execute the CMD passed to the container (php artisan serve)
echo "Starting application..."
exec "$@"
