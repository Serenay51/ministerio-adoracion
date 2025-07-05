#!/bin/bash

echo "🔷 Iniciando deploy de Laravel en Render…"

cd /var/www/html

echo "🔷 Ejecutando migraciones y seeders…"
php artisan migrate --force --seed

echo "✅ Migraciones y seeders completados."

echo "🔷 Arrancando Apache en primer plano…"
exec apache2-foreground
