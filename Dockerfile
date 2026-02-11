# 1. Base image
FROM php:8.2-fpm

# 2. Set working directory
WORKDIR /var/www/html

# 3. Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# 4. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copy composer files first (better caching)
COPY composer.json composer.lock ./

# 6. Allow composer as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# 7. Install PHP dependencies (disable scripts during build)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts

# 8. Copy rest of application
COPY . .

# 9. Create required Laravel folders
RUN mkdir -p storage bootstrap/cache

# 10. Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# 11. Expose port
EXPOSE 8080

# 12. Run Laravel (JSON format recommended)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
