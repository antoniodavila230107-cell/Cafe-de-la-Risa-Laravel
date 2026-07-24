FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html
COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

ENV PORT=8000
EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan cafe:import-products && php artisan cafe:import-recipes && php artisan cafe:import-sales && php artisan serve --host=0.0.0.0 --port=$PORT"]
