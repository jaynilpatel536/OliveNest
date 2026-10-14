FROM php:8.2-apache

ENV PORT=10000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install zip
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Enable Apache modules
RUN a2enmod rewrite alias

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Apache config
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Copy project files
COPY . /var/www/html/

WORKDIR /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create required directories
RUN mkdir -p uploads/homes uploads/rooms \
    && chown -R www-data:www-data /var/www/html

# Render injects PORT env var — make Apache listen on it
RUN sed -i 's/Listen 80/Listen \${PORT}/' /etc/apache2/ports.conf

EXPOSE 10000

CMD ["apache2-foreground"]
