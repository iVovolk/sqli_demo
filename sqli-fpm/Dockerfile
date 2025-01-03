FROM php:8.4.2-fpm

ENV DEBIAN_FRONTEND=noninteractive

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions mbstring opcache mysqli

ENV PHP_ENABLE_XDEBUG=0 \
    TERM=linux

COPY image-files/ /

RUN mkdir /var/www/sqli && chown -R www-data:www-data /var/www/sqli

WORKDIR /var/www/sqli
