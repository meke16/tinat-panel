#!/bin/sh

# Generate Laravel optimized files
php artisan config:cache
php artisan route:cache

# Start PHP-FPM in background
php-fpm -D

# Start nginx in foreground
nginx -g "daemon off;"