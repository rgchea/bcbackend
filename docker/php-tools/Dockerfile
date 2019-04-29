FROM php:7.2-fpm

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    git \
    unzip \
    tzdata \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
	&& docker-php-ext-install -j$(nproc) iconv \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

RUN curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony && \
	chmod a+x /usr/local/bin/symfony
RUN symfony --version

# Use the default production configuration
#RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
#RUN sed -i -e 's/memory_limit = 128M/memory_limit = 4096M/g' $PHP_INI_DIR/php.ini

# Set timezone
RUN rm /etc/localtime
RUN cp /usr/share/zoneinfo/America/Guatemala /etc/localtime
RUN echo "America/Guatemala" >  /etc/timezone
RUN "date"

WORKDIR /var/www/symfony
