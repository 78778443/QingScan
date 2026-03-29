FROM php:7.4-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY vuln.php /var/www/html/vuln.php

WORKDIR /var/www/html
