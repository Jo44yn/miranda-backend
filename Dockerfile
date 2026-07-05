FROM php:8.2-apache

# Install system dependencies and PostgreSQL development libraries
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite for Laravel routing layout engine structures
RUN a2enmod rewrite

# Change Apache document root directory path to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Set active terminal layout tracking workspace directory
WORKDIR /var/www/html
COPY . .

# Install Composer securely inside the environment matrix
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Give server permissions to Laravel storage structures
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose web application port
EXPOSE 80

# Execute migration steps and start Apache server daemon engine layers
CMD php artisan migrate --force && apache2-foreground