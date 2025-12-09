FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN set -xe && \
    # 💡 1. CORREÇÃO CRÍTICA DO REPOSITÓRIO (Resolve o erro da v3.23)
    # Força a atualização dos repositórios para os da versão 3.19 (base do PHP 8.3 Alpine)
    echo "@community https://dl-cdn.alpinelinux.org/alpine/v3.19/community" >> /etc/apk/repositories && \
    echo "@main https://dl-cdn.alpinelinux.org/alpine/v3.19/main" >> /etc/apk/repositories && \
    # 2. Atualiza e instala as dependências de COMPILAÇÃO (Xdebug)
    apk update && \
    apk add --no-cache --virtual .build-deps \
        autoconf \
        build-base \
        php83-dev \
        linux-headers \
    && \
    # 3. INSTALAÇÃO E ATIVAÇÃO DO XDEBUG
    pecl install xdebug && docker-php-ext-enable xdebug \
    && \
    # 4. Instalação das DEPENDÊNCIAS DO SISTEMA DA APLICAÇÃO
    apk add --no-cache \
        git \
        netcat-openbsd \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        freetype-dev \
        mariadb-connector-c-dev \
    && \
    # 5. Compila e instala as Extensões PHP
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        zip \
        opcache \
        gd \
    && \
    # 6. Remove as dependências de build temporárias (LIMPEZA)
    apk del .build-deps \
    && \
    # 7. Instala o Composer e dependências
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --no-autoloader --no-scripts --prefer-dist; \
    composer dump-autoload --optimize; \
    rm -rf /root/.composer/cache; \
    rm -rf /var/cache/apk/*

COPY . .