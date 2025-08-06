FROM php:8.2-fpm

WORKDIR /var/www

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip libpq-dev git curl libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar proyecto Laravel
COPY . /var/www

# Instalar dependencias
RUN composer install --no-interaction --prefer-dist

# Dar permisos a Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache