FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    curl zip unzip git libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN mkdir -p /var/www/storage/framework/{cache,sessions,testing,views} \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./laravel_app/docker/nginx/snippets /etc/nginx/snippets

COPY ./laravel_app/docker/nginx/nginx.conf /etc/nginx/nginx.conf


WORKDIR /var/www/laravel_app

COPY ./laravel_app /var/www/laravel_app

RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000

CMD ["php-fpm"]
