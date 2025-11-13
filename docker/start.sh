#!/bin/sh

# Fix permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Laravel optimizations
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Filament specific
php artisan storage:link
php artisan filament:install --panels --force

# Start services
php-fpm -D
nginx -g "daemon off;"