FROM php:7.4-fpm-alpine

ARG WWWUSER
ARG WWWGROUP

ENV WWWUSER=${WWWUSER}
ENV WWWGROUP=${WWWGROUP}

WORKDIR /var/www/html

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions bcmath \
    gd \
    igbinary \
    imap \
    intl \
    ldap \
    memcached \
    msgpack \
    mysqli \
    pcov \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    redis \
    soap \
    xdebug \
    zip \
    @composer

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN addgroup -g ${WWWGROUP} --system sentrycx
RUN adduser -G sentrycx --system -D -s /bin/sh -u ${WWWUSER} sentrycx
RUN sed -i "s/user = www-data/user = sentrycx/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = sentrycx/g" /usr/local/etc/php-fpm.d/www.conf
