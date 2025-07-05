# Imagen base con PHP 8.3 y Apache
FROM php:8.3-apache

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev curl gnupg \
    && docker-php-ext-install pdo_mysql zip

# Copiar composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos para cachear dependencias Node y PHP
COPY composer.json composer.lock package.json package-lock.json ./

# Instalar Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar dependencias JS
RUN npm install

# Copiar todo el código fuente
COPY . .

# Fijar permisos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Compilar los assets con Vite
RUN npm run build

# Configurar Apache para usar /public como DocumentRoot
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Preparar Laravel
RUN composer install --no-dev --optimize-autoloader \
    && cp .env.example .env \
    && php artisan key:generate

# Exponer puerto 80
EXPOSE 80

# Iniciar Apache en primer plano
CMD ["apache2-foreground"]
