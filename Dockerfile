FROM php:8.4-fpm

# Install system dependencies FIRST
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libpq-dev \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# IMPORTANT: Install GD FIRST before other extensions
# This is a known issue in PHP 8.4 where GD must be installed first
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

# Then install other extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    opcache \
    sodium \
    zip \
    mbstring

# Install Redis extension from PECL
RUN pecl install redis-5.3.7 && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-scripts --no-interaction

# Create necessary directories
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views

# Set proper permissions
RUN chown -R www-data:www-data /app

# Expose port
EXPOSE 8000

# Start application
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

