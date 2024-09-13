# Sử dụng PHP-FPM image với phiên bản PHP cần thiết
FROM php:8.2-fpm

# Cài đặt các extension PHP cần thiết
RUN docker-php-ext-install pdo pdo_mysql

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

# Sao chép toàn bộ mã nguồn Laravel vào container
COPY ./ /var/www/backend

# Đặt thư mục làm việc
WORKDIR /var/www/backend

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cài đặt các gói PHP thông qua Composer
RUN composer install --optimize-autoloader --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-gd --ignore-platform-req=ext-exif --ignore-platform-req=ext-zip

# Phân quyền cho thư mục storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache
RUN chmod -R 775 /var/www/backend/storage /var/www/backend/bootstrap/cache

# Mở cổng cho PHP-FPM
EXPOSE 9000

# Chạy PHP-FPM
CMD ["php-fpm"]
