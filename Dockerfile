FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN apk update && \
    apk add --no-cache \
    # Ferramentas e dependências do sistema
    git \
    netcat-openbsd \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    # Extensões do PHP
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    zip \
    opcache \
    gd && \
    # Instala o Composer (Gerenciador de Dependências PHP) globalmente
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    # Limpeza final (Clean Image)
    rm -rf /var/cache/apk/*

RUN set -ex; \
    composer install --no-dev --no-autoloader --no-scripts --prefer-dist; \
    composer dump-autoload --optimize; \
    rm -rf /root/.composer/cache

    COPY . .