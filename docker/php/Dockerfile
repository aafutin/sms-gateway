FROM php:8.4-fpm

RUN apt-get update && apt-get install -y libpq-dev libicu-dev libzip-dev librabbitmq-dev unzip git \
    && docker-php-ext-install pdo_pgsql intl zip opcache \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && rm -rf /var/lib/apt/lists/* /tmp/pear

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# www-data с вашим UID, чтобы файлы из контейнера были вашими
ARG UID=1000
RUN usermod -u ${UID} www-data

WORKDIR /var/www/app
USER www-data
