FROM php:8.4-apache

LABEL maintainer="Rene Irrgang <info@softdev-nord.de>"

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update --fix-missing \
    && apt-get upgrade -y \
    && apt-get --no-install-recommends install -y apt-utils \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get -y update \
    && apt-get -y --no-install-recommends install \
        apt-utils \
        bash-completion \
        build-essential \
        cron \
        curl \
        default-mysql-client \
        dialog \
        git \
        libcurl4 \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libsqlite3-0 \
        libsqlite3-dev \
        libxslt1-dev \
        libyaml-dev \
        libzip-dev \
        nano \
        nodejs \
        npm \
        openssl \
        wget \
        zip \
        zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ARG INSTALL_XDEBUG=false
RUN if [ "${INSTALL_XDEBUG}" = true ]; then \
        pecl install xdebug && docker-php-ext-enable xdebug; \
    fi

ARG INSTALL_REDIS=false
RUN if [ "${INSTALL_REDIS}" = true ]; then \
        pecl install redis && docker-php-ext-enable redis; \
    fi

ARG INSTALL_APCU=false
RUN if [ "${INSTALL_APCU}" = true ]; then \
        pecl install apcu && docker-php-ext-enable apcu; \
    fi

ARG INSTALL_OPCACHE=false
RUN if [ "${INSTALL_OPCACHE}" = true ]; then \
        docker-php-ext-install opcache; \
    fi

RUN PHP_EXTENSIONS="pdo pdo_mysql mysqli curl zip intl mbstring gettext exif xml gd" \
    && docker-php-ext-install ${PHP_EXTENSIONS} \
    && docker-php-ext-enable ${PHP_EXTENSIONS}

RUN pecl install pcov yaml \
    && docker-php-ext-enable pcov yaml

RUN a2enmod rewrite headers ssl

WORKDIR /var/www/html
ENV USER_NAME="sdn"
ENV WORKING_DIRECTORY="/var/www/html"

RUN useradd --uid 1000 --shell /bin/bash ${USER_NAME} \
    && mkdir --parents /home/${USER_NAME} \
    && chown --recursive ${USER_NAME} /home/${USER_NAME} \
    && chown --recursive ${USER_NAME}:${USER_NAME} ${WORKING_DIRECTORY}

RUN rm -rf /usr/src/*
