FROM php:8.3-apache

LABEL maintainer="Rene Irrgang <info@softdev-nord.de>"

# Surpresses debconf complaints of trying to install apt packages interactively
# https://github.com/moby/moby/issues/4032#issuecomment-192327844
 
ARG DEBIAN_FRONTEND=noninteractive

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
    apt-get -y --no-install-recommends install nano wget nodejs npm \
    dialog \
    cron \
    bash-completion \
    libsqlite3-dev \
    libsqlite3-0 && \
    apt-get -y --no-install-recommends install default-mysql-client \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    zlib1g-dev \
    libzip-dev \
    libxslt-dev \
    libicu-dev && \
    apt-get -y --no-install-recommends install --fix-missing apt-utils \
    build-essential \
    git \
    curl \
    libonig-dev && \
    apt-get -y --no-install-recommends install --fix-missing libcurl4 \
    libcurl4-openssl-dev \
    zip \
    openssl && \
    rm -rf /var/lib/apt/lists/*

###########################################################################
# Copy composer
###########################################################################
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

###########################################################################
# xDebug
###########################################################################
ARG INSTALL_XDEBUG=false

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug-3.3.1 && \
    docker-php-ext-enable xdebug \
;fi

###########################################################################
# PHP Redis
###########################################################################
ARG INSTALL_REDIS=false

RUN if [ ${INSTALL_REDIS} = true ]; then \
    pecl install redis-5.3.3 && \
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
# Other PHP Extensions
###########################################################################
RUN PHP_EXTENSIONS="pdo pdo_mysql mysqli curl zip intl mbstring gettext exif xml gd" \
    && docker-php-ext-install ${PHP_EXTENSIONS} \
    && docker-php-ext-enable ${PHP_EXTENSIONS}
RUN pecl install pcov \
    && docker-php-ext-enable pcov

###########################################################################
# Enable apache modules
###########################################################################
RUN a2enmod rewrite headers ssl

###########################################################################
# Workdir and user
###########################################################################
WORKDIR /var/www/html
ENV USER_NAME="sdn"
ENV WORKING_DIRECTORY="/var/www/html"

###########################################################################
# Setup non-root user
###########################################################################
RUN useradd --uid 1000 --shell /bin/bash ${USER_NAME} \
    && mkdir --parents /home/${USER_NAME} \
    && chown --recursive ${USER_NAME} /home/${USER_NAME} \
    && chown --recursive ${USER_NAME}:${USER_NAME} ${WORKING_DIRECTORY}

###########################################################################
# Cleanup
###########################################################################
RUN rm -rf /usr/src/*
