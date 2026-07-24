FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libsqlite3-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring gd

WORKDIR /var/www/html
COPY . .

RUN cp .env.example .env
RUN echo "APP_NAME=\"Café de la Risa\"" >> .env
RUN echo "APP_ENV=production" >> .env
RUN echo "APP_KEY=base64:YjEtH8yycb+mPjcZR3aEjvPbwbQco1ZnWnkLA0F/l9I=" >> .env
RUN echo "APP_DEBUG=false" >> .env
RUN echo "DB_CONNECTION=sqlite" >> .env
RUN echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> .env

RUN touch database/database.sqlite
RUN chmod -R 777 storage database

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

ENV PORT=8000
EXPOSE 8000

CMD ["sh", "-c", "touch database/database.sqlite && chmod -R 777 storage database && php artisan migrate --force && php artisan db:seed --force && php artisan cafe:import-products && php artisan cafe:import-recipes && php artisan cafe:import-sales && php artisan serve --host=0.0.0.0 --port=$PORT"]
