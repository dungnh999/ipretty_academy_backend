# Sử dụng image PHP với FPM (FastCGI Process Manager)
FROM php:8.2-fpm

# Cài đặt các tiện ích hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Cài đặt Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Sao chép mã nguồn Laravel vào Docker image
COPY . /var/www

# Thiết lập thư mục làm việc
WORKDIR /var/www

RUN composer install --optimize-autoloader  --ignore-platform-req=ext-zip

# Cấp quyền cho thư mục lưu trữ và cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Sao chép file .env.example và thiết lập các biến môi trường
COPY .env.example .env

# Thiết lập quyền cho thư mục lưu trữ và cache
RUN php artisan key:generate

# Expose port 9000 và sử dụng PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
