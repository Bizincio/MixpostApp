#!/bin/bash

# Start Redis first
redis-server --daemonize yes

# Wait for Redis to be ready
sleep 2

# Clear any corrupted cached config from previous runs
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run database migrations and seed default admin user
php artisan migrate --force
php artisan db:seed --force

# Re-cache everything fresh
php artisan optimize

# Fix permissions after cache regeneration
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Start PHP-FPM in background
php-fpm -D

# Start Horizon queue worker in background
php artisan horizon &

# Start Nginx in foreground (keeps container alive)
nginx -g "daemon off;"
