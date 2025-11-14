#!/bin/sh

# Clear any old caches first (important for Filament)
php artisan optimize:clear

# Publish Filament assets (to fix missing layout)
php artisan filament:assets

# Create storage symlink
php artisan storage:link

# Cache optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM in background
php-fpm -D

# Start nginx in foreground
nginx -g "daemon off;"
