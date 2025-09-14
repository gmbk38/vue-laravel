FROM hh.example.ru:5000/php8-fpm:1.0

LABEL maintainer="Dmitrii Kirillov <dkir@unimon.ru>"

RUN apt-get update && apt-get install -y \
    php8.3-xml \
    php8.3-mysql \
    php8.3-pgsql \
    php8.3-zip \
    php8.3-pdo \
    php8.3-sqlite3 \
    php8.3-curl \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Установка Composer
COPY --from=composer:2.7.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/cgi-bin

# Установка Laravel и зависимостей
RUN composer create-project --prefer-dist laravel/laravel .
RUN composer require league/flysystem-aws-s3-v3
RUN php artisan install:api

# Копирование всех файлов проекта
COPY ./cgi-bin/ /var/www/cgi-bin/

# Права доступа
RUN chown -R www-data:www-data /var/www/cgi-bin/storage \
    && chmod -R 775 /var/www/cgi-bin/storage \
    && chmod -R 775 /var/www/cgi-bin/bootstrap/cache

# Конфиг PHP
ADD php.ini /usr/local/etc/php/conf.d/custom.ini

# Конфиг Nginx
ADD nginx.conf /etc/nginx/sites-available/default.conf
RUN ln -sf /etc/nginx/sites-available/default.conf /etc/nginx/sites-enabled/default

# Скрипт генерации ключа через хук
COPY 00php-key-gen.sh /docker-entrypoint.d/
