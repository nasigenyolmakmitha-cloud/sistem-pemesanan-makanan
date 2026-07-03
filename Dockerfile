FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    pkg-config \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions that are available in docker-php-ext-install
RUN docker-php-ext-configure gd --with-freetype=/usr/include/freetype2 --with-jpeg=/usr/include
RUN docker-php-ext-install -j$(nproc) \
    gd \
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

