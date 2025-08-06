#!/bin/bash

if [ ! -L "public/storage" ]; then
    rm -rf public/storage
    php artisan storage:link
fi

php artisan migrate --force

exec "$@"