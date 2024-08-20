FROM php:8.1-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo_mysql zip mysqli

# Copy application code
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Set working directory
WORKDIR /var/www/html

# Create the MariaDB data directory and set permissions
RUN mkdir -p /var/lib/mysql && \
    chown -R 1001:1010 /var/lib/mysql

# Expose port 80
EXPOSE 80 

# Start Apache
CMD ["apache2-foreground"]
