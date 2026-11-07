#!/usr/bin/env bash
set -e

echo "🚀 Starting Madinova Production Deployment..."

# 1. Activate maintenance mode with user-friendly retry headers
php artisan down --retry=60 || true

# 2. Install Composer dependencies for production
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 3. Compile frontend assets with Vite
if [ -f "package.json" ]; then
    npm ci --prefer-offline --no-audit
    npm run build
fi

# 4. Run database migrations
php artisan migrate --force

# 5. Create storage symlink
php artisan storage:link || true

# 6. Cache configuration, routes, and views for maximum performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Restart background queues
php artisan queue:restart || true

# 8. Bring application back online
php artisan up

echo "✅ Madinova deployed successfully!"
