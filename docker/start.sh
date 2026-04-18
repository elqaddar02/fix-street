#!/bin/sh

# Cache Laravel config for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations automatically on deploy
php artisan migrate --force

# Start Nginx in the background, then PHP-FPM in the foreground
nginx
php-fpm
