FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev curl gnupg nodejs npm \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos composer para cache
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader

# Copiar package.json y package-lock.json para cache npm
COPY package*.json ./

RUN npm install

# Copiar el resto del código fuente (incluye .env.example)
COPY . .

# Copiar .env si no existe
RUN if [ ! -f .env ]; then cp .env.example .env; fi

RUN php artisan key:generate

# Compilar assets
RUN npm run build

RUN chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache public

# Configurar Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

RUN php artisan migrate --force \
    && php artisan db:seed --force

EXPOSE 80

CMD ["apache2-foreground"]
