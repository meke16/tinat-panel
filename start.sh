#!/usr/bin/env bash

# Run Laravel migrations and cache (optional on first deploy)
php artisan migrate --force
php artisan config:cache
php artisan route:cache

# Start services
service nginx start
php-fpm
