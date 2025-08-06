FROM php:8.2-fpm

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /var/www

# Instalar extensiones de PHP y dependencias del sistema necesarias
# Incluye libpq-dev para PostgreSQL, git, curl, y las extensiones de PHP
# También añade nodejs y npm para el frontend
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

# Instala las extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar proyecto Laravel al contenedor
COPY . /var/www

# Instalar dependencias de Composer (PHP)
# Se usa --no-dev para no instalar dependencias de desarrollo en producción
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Instalar dependencias de Node.js y compilar el frontend (Vue)
# Esto ejecutará 'npm install' y luego 'npm run build' según tu package.json
RUN npm install && npm run build

# Optimizar la configuración y las rutas de Laravel para producción
RUN php artisan optimize

# Crear el enlace simbólico para el almacenamiento público
RUN php artisan storage:link

RUN php artisan migrate
# Dar permisos adecuados a los directorios de almacenamiento y caché
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exponer el puerto por defecto de PHP-FPM
# Railway redirigirá el tráfico web a este puerto
EXPOSE 9000

# Comando para iniciar PHP-FPM cuando el contenedor se ejecute
# Las migraciones se ejecutarán en el "Start Command" de Railway
CMD ["php-fpm"]