FROM php:7.3-apache-stretch

ARG DEBIAN_FRONTEND=noninteractive

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="10000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="192" \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE="10"

###########################################################################
# Update
###########################################################################
RUN apt-get -y update --fix-missing && \
    apt-get upgrade -y && \
    apt-get --no-install-recommends install -y apt-utils && \
    rm -rf /var/lib/apt/lists/*

###########################################################################
# Install useful tools and install important libaries
###########################################################################
RUN apt-get -y update && \
    apt-get -y --no-install-recommends install nano wget dialog libsqlite3-dev libsqlite3-0 && \
    apt-get -y --no-install-recommends install mysql-client zlib1g-dev libzip-dev libicu-dev && \
    apt-get -y --no-install-recommends install --fix-missing apt-utils build-essential git curl && \
    apt-get -y --no-install-recommends install --fix-missing libcurl3 libcurl3-dev zip openssl && \
    rm -rf /var/lib/apt/lists/*

###########################################################################
# Copy composer
###########################################################################
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

###########################################################################
# Imagick
###########################################################################
ARG INSTALL_IMAGEMAGICK=false

RUN if [ ${INSTALL_IMAGEMAGICK} = true ]; then \
    apt-get -y update && \
    apt-get -y --no-install-recommends install libmagickwand-dev imagemagick && \
    rm -rf /var/lib/apt/lists/* && \
    pecl install imagick && \
    docker-php-ext-enable imagick \
;fi

###########################################################################
# xDebug
###########################################################################
ARG INSTALL_XDEBUG=false

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug-2.7.2 && \
    docker-php-ext-enable xdebug \
;fi

###########################################################################
# PHP Redis
###########################################################################
ARG INSTALL_REDIS=false

RUN if [ ${INSTALL_REDIS} = true ]; then \
    pecl install redis-5.0.2 && \
    docker-php-ext-enable redis \
;fi

###########################################################################
# APCU:
###########################################################################
ARG INSTALL_APCU=false

RUN if [ ${INSTALL_APCU} = true ]; then \
    pecl install apcu; \
    docker-php-ext-enable apcu \
;fi

###########################################################################
# Opcache:
###########################################################################
ARG INSTALL_OPCACHE=false

RUN if [ ${INSTALL_OPCACHE} = true ]; then \
    docker-php-ext-install opcache \
;fi

###########################################################################
# IonCube Loader:
###########################################################################

ARG INSTALL_IONCUBE=false

RUN if [ ${INSTALL_IONCUBE} = true ]; then \
    # Install the php ioncube loader \
    curl -sSL https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz -o ioncube.tar.gz && \
    tar -xzf ioncube.tar.gz && \
    export PHP_EXT_DIR=$(php-config --extension-dir) && \
    mv ./ioncube/ioncube_loader_lin_7.3.so  $PHP_EXT_DIR/ && \
    echo "zend_extension = $PHP_EXT_DIR/ioncube_loader_lin_7.3.so" >> /usr/local/etc/php/php.ini && \
    cd .. && rm -rf setup \


    #curl -L -o /tmp/ioncube_loaders_lin_x86-64.tar.gz https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz \
    #&& tar zxpf /tmp/ioncube_loaders_lin_x86-64.tar.gz -C /tmp \
    #.&& mv /tmp/ioncube/ioncube_loader_lin_7.3.so $(php -r "echo ini_get('extension_dir');")/ioncube_loader.so \
    #&& printf "zend_extension=ioncube_loader.so\n" > /usr/local/etc/php/conf.d/docker-php-ext-ioncube.ini \
    #&& rm -rf /tmp/ioncube*
;fi

###########################################################################
# Other PHP Extensions
###########################################################################
RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-install pdo_sqlite && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install curl && \
    docker-php-ext-install tokenizer && \
    docker-php-ext-install json && \
    docker-php-ext-install zip && \
    docker-php-ext-install -j$(nproc) intl && \
    docker-php-ext-install mbstring && \
    docker-php-ext-install gettext && \
    docker-php-ext-install exif

###########################################################################
# Freetype
###########################################################################
RUN apt-get -y update && \
    apt-get --no-install-recommends install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd

###########################################################################
# Enable apache modules
###########################################################################
RUN a2enmod rewrite headers

###########################################################################
# Cleanup
###########################################################################
RUN rm -rf /usr/src/*