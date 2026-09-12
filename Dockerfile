# Stage 1: Build Node.js assets
FROM node:22-alpine AS node-builder

WORKDIR /app

# Copy package management files
COPY package.json package-lock.json* pnpm-lock.yaml* yarn.lock* ./

# Install dependencies based on the lock file present
RUN if [ -f pnpm-lock.yaml ]; then \
        corepack enable pnpm && pnpm install --frozen-lockfile; \
    elif [ -f yarn.lock ]; then \
        yarn install --frozen-lockfile; \
    else \
        npm ci; \
    fi

# Copy the rest of the application
COPY . .

# Build Vite assets
RUN npm run build


# Stage 2: Build PHP Application
FROM php:8.3-fpm-alpine AS app

# Install system dependencies and PHP extensions
# Add postgresql-dev for pgsql extensions and linux-headers/oniguruma for others
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    linux-headers \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring zip pcntl bcmath opcache

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files (excluding those in .dockerignore)
COPY . .

# Copy built frontend assets from node-builder
COPY --from=node-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Use entrypoint to run migrations and cache
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Default command for the container
CMD ["php-fpm"]


# Stage 3: Build Nginx web server
FROM nginx:alpine AS web

# Copy Nginx config
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy public directory with built assets
COPY --from=app /var/www/html/public /var/www/html/public
