FROM php:7.4-apache

LABEL maintainer="Rene Irrgang <info@softdev-nord.de>"

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="10000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="192" \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE="10"

###########################################################################
# Install useful tools and install important libaries
###########################################################################
RUN apt-get update \
	&& DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
	software-properties-common \
	&& apt-get update \
	&& DEBIAN_FRONTEND=noninteractive apt-get install -y \
	nodejs \
	zlib1g-dev libpng-dev libjpeg-dev \
	libfreetype6-dev \
	libicu-dev \
    libssl-dev \
	libjpeg62-turbo-dev \
	libmcrypt-dev \
	libedit-dev \
	libedit2 \
	libxslt1-dev \
	libzip-dev \
	apt-utils \
	gnupg \
	redis-tools \
	mariadb-client \
	zlib1g-dev \
	libpng-dev \
	libjpeg-dev \
	libwebp-dev \
	libxpm-dev \
	git \
	vim \
    nano \
	wget \
	curl \
	lynx \
	psmisc \
	unzip \
	tar \
	cron \
	bash-completion \
	&& apt-get clean

RUN docker-php-ext-configure gd \
    --with-webp \
    --with-jpeg \
    --with-xpm \
    --with-freetype \
&& docker-php-ext-install gd

###########################################################################
# Copy composer
###########################################################################
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

###########################################################################
# xDebug
###########################################################################
ARG INSTALL_XDEBUG=false

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug-2.9.2 && \
    docker-php-ext-enable xdebug \
;fi

###########################################################################
# PHP Redis
###########################################################################
ARG INSTALL_REDIS=false

RUN if [ ${INSTALL_REDIS} = true ]; then \
    pecl install redis-5.2.2 && \
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
RUN PHP_EXTENSIONS="pdo_mysql mysqli zip intl" \
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
ENV USER_NAME="webWMS"
ENV WORKING_DIRECTORY="/var/www/html"

###########################################################################
# Setup non-root user
###########################################################################
RUN useradd --uid 1000 --shell /bin/bash ${USER_NAME} \
    && mkdir --parents /home/${USER_NAME} \
    && chown --recursive ${USER_NAME} /home/${USER_NAME} \
    && chown --recursive ${USER_NAME}:${USER_NAME} ${WORKING_DIRECTORY}

COPY ./cron.d/backup-db /etc/cron.d/backup-db
RUN chmod 0644 /etc/cron.d/backup-db && \
    chown root:root /etc/cron.d/backup-db && \
    crontab /etc/cron.d/backup-db

###########################################################################
# Cleanup
###########################################################################
RUN rm -rf /usr/src/*
