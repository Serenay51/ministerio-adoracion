# Imagen base con PHP 8.3 y Apache
FROM php:8.3-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev \
    && docker-php-ext-install pdo_mysql zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar el código de tu app
COPY . /var/www/html

# Cambiar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configurar Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN a2enmod rewrite

# Cambiar DocumentRoot para que apunte a /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Expone el puerto 80
EXPOSE 80

# Comandos de build: instalar dependencias y generar clave
RUN cd /var/www/html && cp .env.example .env \
    && composer install --no-dev --optimize-autoloader \
    && php artisan key:generate

# Start Apache
CMD ["apache2-foreground"]
