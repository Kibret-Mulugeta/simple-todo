# Use official PHP + Apache image
FROM php:8.2-apache

# Install mysqli and pdo_mysql extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all project files to Apache root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Enable Apache rewrite (optional if you use clean URLs)
RUN a2enmod rewrite

# Expose port 1000
EXPOSE 1000

# Start Apache in the foreground
CMD ["apache2-foreground"]
