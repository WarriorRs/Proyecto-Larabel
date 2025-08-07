# Usa una imagen base de PHP con FPM
FROM php:8.2-fpm

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /var/www/backend

# Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar el proyecto completo al contenedor
COPY . /var/www/backend

# Copia el script de entrada y dale permisos de ejecución
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Instalar dependencias de Composer (PHP) y de Node.js
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm install

# Compilar el frontend (Vue) y optimizar Laravel
RUN npm run build
RUN php artisan optimize

# Crear el enlace simbólico para el almacenamiento público
RUN php artisan storage:link

# Dar permisos adecuados
RUN chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache

# Exponer el puerto por defecto de PHP-FPM
EXPOSE 9000

# El comando de entrada que ejecuta tu script
CMD ["entrypoint.sh"]