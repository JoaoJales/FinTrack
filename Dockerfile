FROM php:8.2-fpm-alpine

# Dependências do sistema
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm

# Extensões PHP para Laravel + PostgreSQL
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    zip \
    bcmath \
    gd \
    opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia dependências primeiro (melhor cache de layers)
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

COPY package.json package-lock.json ./
RUN npm ci

# Copia o projeto
COPY . .

# Finaliza autoloader e compila assets
RUN composer dump-autoload --optimize
RUN npm run build

# Permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
