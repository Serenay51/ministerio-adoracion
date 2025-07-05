FROM php:8.3-apache

WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev curl gnupg nodejs npm \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos composer para cache
COPY composer.json composer.lock ./

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Copiar archivos package.json para cache npm
COPY package*.json ./

# Instalar dependencias npm
RUN npm ci

# Copiar todo el código fuente
COPY . .

# Asegurarse de que exista .env
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Generar key de Laravel
RUN php artisan key:generate

# Compilar assets con Vite
RUN npm run build

# Permisos para storage y cache
RUN chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache public

# Configurar Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Migraciones y seed
RUN php artisan migrate --force \
    && php artisan db:seed --force

EXPOSE 80

CMD ["apache2-foreground"]
