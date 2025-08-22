#!/bin/bash

echo "🔧 Verificando sintaxis del archivo create.blade.php..."
php -l resources/views/obras/create.blade.php

echo ""
echo "🧹 Limpiando cachés..."
php artisan view:clear
php artisan config:clear

echo ""
echo "📡 Probando conexión básica al servidor..."
sleep 2
curl -s -I http://127.0.0.1:8002 | head -1

echo ""
echo "📋 Probando página de crear obra (solo cabeceras)..."
curl -s -I http://127.0.0.1:8002/obras/create | head -1

echo ""
echo "✅ Verificación completada"
