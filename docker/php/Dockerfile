FROM php:8.1-fpm-alpine
WORKDIR /app
COPY --from=composer:2  /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

#RUN apk --update --no-cache
RUN install-php-extensions intl opcache zip pgsql pdo_pgsql soap sockets memcached xdebug

COPY ./ /app
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/xdebug.prod.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./docker/php/opcache.prod.ini /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
COPY ./docker/php/logging.conf /usr/local/etc/php-fpm.d/logging.conf


RUN APP_ENV=prod composer i --no-dev --optimize-autoloader --ignore-platform-reqs