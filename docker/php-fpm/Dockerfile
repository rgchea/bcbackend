FROM php:7.1-fpm-alpine

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_mysql
ADD symfony.ini /usr/local/etc/php/conf.d/

RUN apk upgrade --update && apk add --no-cache \
  freetype-dev \
  libjpeg-turbo-dev \
  libjpeg \
  libltdl \
  libwebp-dev \
  libmcrypt-dev \
  libpng \
  libpng-dev \
  autoconf \
  g++ \
  make \
  tzdata \
  curl \
#&& docker-php-ext-configure mcrypt --with-mcrypt \
#&& docker-php-ext-install  mcrypt \
&& docker-php-ext-configure gd --with-jpeg-dir=/usr/include --with-png-dir=/usr/include --with-webp-dir=/usr/include --with-freetype-dir=/usr/include \
&& docker-php-ext-install gd \
&& docker-php-ext-configure opcache --enable-opcache \
&& docker-php-ext-install opcache

# redis, apcu
RUN docker-php-source extract \
    && apk add --no-cache --virtual .phpize-deps-configure $PHPIZE_DEPS \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && apk del .phpize-deps-configure \
    && docker-php-source delete

# Set timezone
RUN rm /etc/localtime
RUN cp /usr/share/zoneinfo/America/Guatemala /etc/localtime
RUN echo "America/Guatemala" >  /etc/timezone
RUN "date"

RUN apk del tzdata

WORKDIR /var/www/symfony
