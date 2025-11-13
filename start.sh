#!/usr/bin/env bash

# Run Laravel migrations and cache (optional on first deploy)
php artisan migrate --force
php artisan config:cache
php artisan route:cache

#!/bin/sh

# Start PHP-FPM in background
php-fpm -D

# Substitute Render PORT into nginx config
envsubst '$PORT' < /etc/nginx/sites-available/default > /etc/nginx/sites-available/render.conf
mv /etc/nginx/sites-available/render.conf /etc/nginx/sites-available/default

# Start nginx in foreground
nginx -g "daemon off;"