# Stage 1: Build dependencies and install PHP extensions
FROM php:8.2-fpm as builder

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \ 
    zip \
    git \
    unzip \
    curl \
    nodejs \
    npm \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel optimizations
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Stage 2: Runtime container
FROM php:8.2-fpm

# Install runtime dependencies (fewer than builder)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    curl \
 && docker-php-ext-install pdo pdo_mysql pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy files from builder
COPY --from=builder /var/www/html /var/www/html
COPY --from=builder /usr/bin/composer /usr/bin/composer

# Expose port 8000
EXPOSE 8000

# Run migrations + seeds every deploy, then start Laravel
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8000
