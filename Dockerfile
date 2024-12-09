# Gebruik een officiële PHP-image met alle extensies voor Laravel
FROM php:8.2-fpm

# Installeer benodigde extensies en afhankelijkheden
RUN apt-get update && apt-get install -y \
    curl zip unzip git libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Pas permissies aan
RUN mkdir -p /var/www/storage/framework/{cache,sessions,testing,views} \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Installeer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the Nginx snippets directory to the container
COPY ./laravel_app/docker/nginx/snippets /etc/nginx/snippets

# Copy the Nginx configuration file to the container
COPY ./laravel_app/docker/nginx/nginx.conf /etc/nginx/nginx.conf


# Stel de werkdirectory in voor Laravel
WORKDIR /var/www/laravel_app

# Kopieer projectbestanden naar de container
COPY ./laravel_app /var/www/laravel_app

# Installeer Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Stel de containerpoort in
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
