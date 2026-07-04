FROM node:20-alpine AS frontend-build

WORKDIR /app

# Copy hanya file yang dibutuhkan untuk install dependency dulu (cache-friendly)
COPY package*.json ./
RUN npm install

# Copy sisa source yang dibutuhkan Vite untuk build (resources, config, dsb)
COPY . .
RUN npm run build


FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Install php-extension-installer (menangani otomatis semua dependency
# system & pkg-config yang dibutuhkan tiap ekstensi PHP, termasuk fix
# untuk masalah pdo_pgsql di PHP 8.4)
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install semua ekstensi PHP yang dibutuhkan
RUN install-php-extensions \
    gd \
    pdo \
    pdo_pgsql \
    opcache \
    sodium \
    zip \
    mbstring \
    redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-scripts --no-interaction

# Copy hasil build asset Vite (JS/CSS terkompilasi) dari stage frontend-build
COPY --from=frontend-build /app/public/build /app/public/build

# Create necessary directories
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views

# Set proper permissions
RUN chown -R www-data:www-data /app

# Expose port
EXPOSE 8000

# Start application
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

RUN php artisan storage:link