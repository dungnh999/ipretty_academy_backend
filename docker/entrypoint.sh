#!/bin/bash

if [ ! -f "vendor/autoload.php" ]; then
    composer install --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-gd --ignore-platform-req=ext-exif --ignore-platform-req=ext-exif --ignore-platform-req=ext-gd --ignore-platform-req=ext-zip --ignore-platform-req=ext-zip
fi

if [ ! -f ".env" ]; then
    echo "Creating env file for env $APP_ENV"
    cp .env.example .env
else
    echo "env file exists."
fi

yarn run dev

php artisan migrate
php artisan optimize clear
php artisan view:clear
php artisan route:clear

php-fpm -D
nginx -g "daemon off;"
