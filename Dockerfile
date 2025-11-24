FROM php:8.4.14-fpm

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    libzip-dev

WORKDIR /var/www/html

RUN docker-php-ext-install pdo_mysql exif pcntl bcmath sockets zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
 
COPY / /var/www/html/

RUN mkdir -p tmp

RUN composer install
RUN composer dump-autoload

EXPOSE 8080

COPY .docker/docker-entrypoint.sh /root/
RUN ["chmod", "+x", "/root/docker-entrypoint.sh"]
ENTRYPOINT /root/docker-entrypoint.sh
