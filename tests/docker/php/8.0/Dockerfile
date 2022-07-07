FROM --platform=linux/amd64 php:8.0.20-fpm-alpine

RUN apk add git unzip zlib-dev libzip-dev

RUN docker-php-ext-install bcmath zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN curl --remote-name --time-cond ./php/cacert.pem https://curl.se/ca/cacert.pem

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /var/www
