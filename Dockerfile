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
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# 4. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copy application code
COPY . .

# 6. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 7. Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Expose port
EXPOSE 8080

# 9. Run Laravel
CMD php artisan serve --host=0.0.0.0 --port=8080
