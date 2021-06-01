FROM php:8-apache

# system dependecies
RUN apt-get update \
    && apt-get install -y \
    sqlite3 \
    git \
    libssl-dev \
    default-mysql-client \
    libmcrypt-dev \
    libicu-dev \
    libpq-dev \
    libjpeg62-turbo-dev \
    libjpeg-dev  \
    libpng-dev \
    zlib1g-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    nano

# PHP dependencies
RUN docker-php-ext-install \
    gd \
    intl \
    mbstring \
    pdo \
    zip

# Apache
RUN a2enmod rewrite && echo "ServerName docker" >> /etc/apache2/apache2.conf

# Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /var/www
