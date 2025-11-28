# Use official PHP + Apache image
FROM php:8.2-apache

# Install system dependencies for MySQL & PHP extensions
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libzip-dev \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

# Copy project files to Apache root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose default Apache port
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
