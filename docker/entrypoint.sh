#!/bin/bash
echo "ENTRYPOINT script is running"

if [ ! -f "vendor/autoload.php" ]; then
    composer install --ignore-platform-req=ext-zip --ignore-platform-req=ext-zip --no-progress --no-interaction --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-gd --ignore-platform-req=ext-exif --ignore-platform-req=ext-exif --ignore-platform-req=ext-gd
fi

if [ ! -f ".env" ]; then
    echo "Creating env file for env $APP_ENV"
    cp .env.example .env
else
    echo "env file exists."
fi

#php artisan migrate
#php artisan optimize clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan queue:work

php-fpm -D
nginx -g "daemon off;"
