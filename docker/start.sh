#!/bin/bash

# Start Redis
redis-server --daemonize yes

# Wait a moment for everything to be ready
sleep 2

# Run database migrations (creates Mixpost's tables)
php artisan migrate --force

# Cache config for speed
php artisan optimize

# Start PHP-FPM in the background
php-fpm -D

# Start Horizon (the queue worker) in the background
php artisan horizon &

# Start Nginx in the foreground (keeps the container alive)
nginx -g "daemon off;"
