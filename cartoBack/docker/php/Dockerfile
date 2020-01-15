FROM php:7.2-fpm

RUN apt-get update

RUN apt-get install -y libpq-dev
RUN docker-php-ext-install pdo_pgsql

#RUN pecl install apcu
#RUN docker-php-ext-enable apcu

RUN apt-get install -y zlib1g-dev unzip
RUN docker-php-ext-install zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

RUN composer -V

WORKDIR /usr/src/app

RUN PATH=$PATH:/usr/src/apps/vendor/bin:bin
