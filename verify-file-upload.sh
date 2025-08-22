#!/bin/bash

echo "🔧 Verificando que el servidor esté funcionando..."

# Verificar conectividad básica
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 5 --max-time 10 http://127.0.0.1:8003/)

if [ "$RESPONSE" -ne 200 ]; then
    echo "❌ Error: Servidor no responde (código: $RESPONSE)"
    exit 1
fi

echo "✅ Servidor funcionando correctamente"

echo "📁 Verificando directorios de storage..."

# Verificar que los directorios de storage existen
if [ ! -d "storage/app/public/obras" ]; then
    echo "📂 Creando directorio storage/app/public/obras..."
    mkdir -p storage/app/public/obras/contratos
    mkdir -p storage/app/public/obras/fianzas 
    mkdir -p storage/app/public/obras/actas
    echo "✅ Directorios creados"
else
    echo "✅ Directorios de storage ya existen"
fi

echo "🔗 Verificando enlace simbólico de storage..."
if [ ! -L "public/storage" ]; then
    echo "📎 Creando enlace simbólico..."
    php artisan storage:link
else
    echo "✅ Enlace simbólico existe"
fi

echo "📊 Verificando permisos de storage..."
chmod -R 775 storage/app/public/
echo "✅ Permisos configurados"

echo "🎯 Verificación completada - El sistema está listo para subir archivos"
