# Use official PHP + Apache image
FROM php:8.2-apache

# Enable mysqli extension for MySQL
RUN docker-php-ext-install mysqli

# Copy all project files to Apache root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 1000
EXPOSE 1000

# Start Apache in the foreground
CMD ["apache2-foreground"]
