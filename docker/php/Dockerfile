FROM php:8.2-fpm

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install curl and unzip (needed for composer)
RUN apt-get update && apt-get install -y curl unzip git \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY ./backend /var/www/html

# Create uploads directory and set proper permissions
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/uploads

# Install AWS SDK for PHP
RUN composer require aws/aws-sdk-php

# Expose php-fpm port
EXPOSE 9000