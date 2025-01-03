# Used for prod build.
FROM php:8.2-fpm as php

# Set environment variables
ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_ENABLE_CLI=1
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=1
ENV PHP_OPCACHE_REVALIDATE_FREQ=1

# Install dependencies.
RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    libcurl4-gnutls-dev \
    nginx \
    libonig-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev

# Install PHP extensions.
RUN docker-php-ext-install mysqli pdo pdo_mysql bcmath curl opcache mbstring

# Configure and install GD extension.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Copy composer executable.
COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer

# Copy configuration files.
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf


RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs
RUN apt-get install -y supervisor
RUN npm install --global yarn

# Set working directory to /var/www.
WORKDIR /var/www/backend

# Copy files from current folder to container current folder (set in workdir).
COPY --chown=www-data:www-data . .
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chown -R www-data:www-data /var/www/backend

# Create laravel caching folders.
RUN mkdir -p /var/www/backend/storage/framework
RUN mkdir -p /var/www/backend/storage/framework/cache
RUN mkdir -p /var/www/backend/storage/framework/testing
RUN mkdir -p /var/www/backend/storage/framework/sessions
RUN mkdir -p /var/www/backend/storage/framework/views

# Fix files ownership.
RUN chown -R www-data /var/www/backend/storage
RUN chown -R www-data /var/www/backend/storage/framework
RUN chown -R www-data /var/www/backend/storage/framework/sessions

# Set correct permission.
RUN chmod -R 755 /var/www/backend/storage
RUN chmod -R 755 /var/www/backend/storage/logs
RUN chmod -R 755 /var/www/backend/storage/framework
RUN chmod -R 755 /var/www/backend/storage/framework/sessions
RUN chmod -R 755 /var/www/backend/bootstrap
RUN chmod -R 755 /var/www/backend/public
RUN chmod -R 755 /var/www/backend/public/public
RUN chown -R www-data:www-data /var/www/backend/public /var/www/backend/public \
    && chmod -R 775 /var/www/backend/public /var/www/backend/public

# Adjust user permission & group
RUN usermod --uid 1000 www-data
RUN groupmod --gid 1001 www-data

RUN chmod +x docker/entrypoint.sh

# Run the entrypoint file.
#ENTRYPOINT ["docker/entrypoint.sh" ]
RUN  ./docker/entrypoint.sh