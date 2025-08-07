#!/bin/sh
echo "Esperando que la base de datos esté disponible..."
until pg_isready -h db -U postgres; do
  sleep 1
done
echo "Base de datos lista. Ejecutando migraciones..."
php artisan migrate --force
echo "Migraciones completadas."
exec php-fpm