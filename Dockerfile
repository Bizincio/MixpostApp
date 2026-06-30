FROM php:8.3-fpm

# Install system tools and PHP extensions Mixpost needs
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    ffmpeg \
    redis-server \
    autoconf \
    gcc \
    make \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer (the tool that downloads PHP's building blocks)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set our working folder inside the container
WORKDIR /var/www/html

# Copy all your project files into the container
COPY . .

# Download all the PHP packages Mixpost needs
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set correct file permissions
RUN chmod -R 755 public && \
    chmod -R 775 storage && \
    chmod -R 775 bootstrap/cache

# Copy in our custom startup script and nginx config
COPY docker/start.sh /start.sh
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
