# Imagen base con PHP 8.3 y Apache
FROM php:8.3-apache

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev curl gnupg \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar composer.json y composer.lock primero para aprovechar cache
COPY composer.json composer.lock ./

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Copiar package.json y package-lock.json primero para aprovechar cache
COPY package*.json ./

# Instalar Node.js 20.x
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Copiar el resto del código fuente
COPY . .

# Instalar Breeze (si no está instalado)
RUN if [ ! -d "resources/views/auth" ]; then \
        composer require laravel/breeze --dev && \
        php artisan breeze:install blade && \
        npm install && npm run build ; \
    fi

# Compilar assets siempre para asegurarse
RUN npm install && npm run build

# Cambiar permisos para Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configurar Apache para servir desde public/
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Generar archivo .env si no existe y clave app
RUN if [ ! -f .env ]; then cp .env.example .env; fi \
    && php artisan key:generate

# Migrar la base de datos (opcional)
# RUN php artisan migrate --force

EXPOSE 80

CMD ["apache2-foreground"]
