#!/bin/sh
set -e

# We only want to run migrations and cache setup if this is the main app (PHP-FPM) container.
# If the container is started with a different command (e.g. for queue or reverb),
# we skip this part.
if [ "$1" = "php-fpm" ]; then
    echo "Running migrations..."
    php artisan migrate --force

    echo "Running seeders..."
    php artisan db:seed --force

    echo "Caching configuration..."
    php artisan config:cache
    php artisan event:cache
    php artisan route:cache
    php artisan view:cache

    # Ensure any cache/log files created by root are owned by www-data
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
fi

# Execute the container's main process
exec "$@"
