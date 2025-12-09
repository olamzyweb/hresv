FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql
RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . /var/www/html

# Simple dev settings
RUN echo "upload_max_filesize=10M\npost_max_size=12M" > /usr/local/etc/php/conf.d/uploads.ini

CMD ["apache2-foreground"]

