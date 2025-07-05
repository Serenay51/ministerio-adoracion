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

# Copiar package.json y package-lock.json primero para aprovechar cache y solo reinstalar si cambian
COPY package*.json ./

# Instalar Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Copiar el resto del código fuente
COPY . .

# Preparar el entorno Laravel
RUN cp .env.example .env \
    && composer install --no-dev --optimize-autoloader \
    && php artisan key:generate

# Instalar Breeze si no está instalado
RUN if [ ! -d "resources/views/auth" ]; then \
        composer require laravel/breeze --dev && \
        php artisan breeze:install blade && \
        npm install && npm run build ; \
    else \
        npm install && npm run build ; \
    fi

# Cambiar permisos a www-data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configurar Apache para usar el directorio public/
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Exponer el puerto 80
EXPOSE 80

# Iniciar Apache en primer plano
CMD ["apache2-foreground"]
