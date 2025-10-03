# Guna PHP 8.2 official image (FPM untuk Laravel lebih sesuai)
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Set working directory Laravel
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy semua fail Laravel ke dalam container
COPY . /var/www/html

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Cache config & route
RUN php artisan config:cache && php artisan route:cache

# Expose port (Railway akan inject $PORT)
EXPOSE 8000

# Jalankan Laravel
CMD php artisan serve --host=0.0.0.0 --port=$PORT
