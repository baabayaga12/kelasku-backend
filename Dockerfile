FROM php:8.2-fpm

# Install system dependencies and PHP extensions needed by Laravel
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libjpeg-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ \
    && docker-php-ext-install pdo pdo_mysql mbstring xml gd zip

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . /var/www

# Install PHP dependencies (production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Ensure storage and cache are writable
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose port used by artisan serve
EXPOSE 8080

# Start Laravel's built-in server (for small projects). For higher scale, use php-fpm + nginx.
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]