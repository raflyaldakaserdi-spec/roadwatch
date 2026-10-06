FROM php:8.4-fpm-alpine

RUN apk add --no-cache nginx libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev zip unzip git postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql zip bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["sh", "-c", "php artisan config:clear && php artisan storage:link && nginx -g \"daemon off;\" & php-fpm"]

