#!/bin/sh

# Create storage link
php artisan storage:link

# Create a Filament user (update email/password as needed)
php artisan make:filament-user --email=admin@example.com --password=Adey@1997 --name=Admin

# Cache optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM in background
php-fpm -D

# Start nginx in foreground
nginx -g "daemon off;"