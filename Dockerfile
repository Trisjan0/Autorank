# Stage 1: Build
FROM php:8.2-fpm AS builder

# System dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip git unzip curl nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Install Node deps & build assets
RUN npm install && npm run build

# Cache configs
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Stage 2: Runtime
FROM php:8.2-fpm

WORKDIR /var/www/html

# Copy from builder stage
COPY --from=builder /var/www/html /var/www/html

# Expose port
EXPOSE 8000

# Run migrations + seed before starting
CMD php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan serve --host=0.0.0.0 --port=8000
