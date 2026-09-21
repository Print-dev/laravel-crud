#!/bin/sh
set -e

echo "🚀 Iniciando Mini CRUD..."

# Verificar APP_KEY
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY no configurada, generando..."
    php artisan key:generate --force
fi

# Esperar a que MySQL esté listo
echo "⏳ Esperando a MySQL..."
sleep 10

# Ejecutar migraciones y seeders
echo "📦 Ejecutando migraciones..."
php artisan migrate --force --seed

# Crear enlace simbólico para storage
echo "🔗 Creando enlace de storage..."
php artisan storage:link

# Optimizar para producción
echo "⚡ Optimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Listo! Accede en http://localhost:8000"

# Ejecutar el comando principal
exec "$@"