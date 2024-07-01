FROM php:8.2-apache

## Install dependencies
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_mysql intl zip && \
    docker-php-ext-enable pdo_mysql intl zip

RUN a2enmod rewrite

## Install Node.js 20.X
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - &&\
    apt-get update && apt-get install -y -f \
    nodejs git unzip

## Install Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

## Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

## Copy files to container
COPY . /var/www/

## Set up Apache configuration file
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf

## Set up environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1

## Install dependencies and build assets
RUN cd /var/www && \
    composer install && \
    npm install && \
    npm run build

WORKDIR /var/www/

EXPOSE 80