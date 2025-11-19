# Multi-stage Dockerfile for Laravel optimized for Render

# Stage 1: Build frontend assets
FROM node:20-bullseye AS node-build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --silent
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# Stage 2: Application
FROM php:8.2-fpm-bullseye AS base

# system deps
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libonig-dev libxml2-dev libssl-dev \
    nginx supervisor ca-certificates curl procps libmariadb-dev-compat libmariadb-dev \
  && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Create app dir
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copy built frontend assets from node-build stage
COPY --from=node-build /app/public/build ./public/build

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy nginx and supervisor configs
COPY deploy/nginx.conf /etc/nginx/sites-available/default
COPY deploy/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n"]
