# syntax=docker/dockerfile:1

# -----------------------------------------------------------------------------
# Stage 1: Build frontend assets with Node
# -----------------------------------------------------------------------------
FROM node:22-bookworm-slim AS frontend

WORKDIR /var/www/html

# Copy dependency manifests first for better layer caching
COPY package.json package-lock.json vite.config.js ./

# Install npm dependencies
RUN npm ci

# Copy the rest of the source needed by Vite to build
COPY resources/ ./resources/
COPY public/ ./public/

# Build production assets into public/build
RUN npm run build

# -----------------------------------------------------------------------------
# Stage 2: Final PHP runtime image
# -----------------------------------------------------------------------------
FROM php:8.2-fpm-bookworm

WORKDIR /var/www/html

# System dependencies required by Laravel and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        zip \
        unzip \
        default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer (official installer, pinned to a known good copy)
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy dependency manifests first for better layer caching
COPY composer.json composer.lock ./

# Install PHP dependencies (no dev packages, optimized autoloader)
# Use --no-scripts because artisan doesn't exist yet; scripts run after source is copied.
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts

# Copy the application source code
COPY . .

# Copy the built frontend assets from the frontend stage
COPY --from=frontend /var/www/html/public/build ./public/build

# Now run the composer post-install scripts (package:discover, etc.)
RUN composer dump-autoload --optimize --no-interaction \
    && php artisan package:discover --ansi

# Ensure storage and bootstrap/cache are writable by the web user
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the port the app listens on inside the container
EXPOSE 8000

# Entrypoint: run migrations + seed, then start the dev server.
# (For a more production-like setup you would use PHP-FPM + Nginx instead.)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
