# Use official PHP + Apache image
FROM php:8.2-apache

# Install system dependencies and PHP MySQL extensions
RUN apt-get update && apt-get install -y default-mysql-client libzip-dev unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

# Copy all project files to Apache web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose Apache port
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
