#!/bin/bash

# Test para verificar la funcionalidad de cambio de obra

BASE_URL="http://127.0.0.1:8000"
VEHICULO_ID=1
NUEVA_OBRA_ID=2
OPERADOR_ID=1

echo "🔍 Probando la funcionalidad de cambio de obra..."

# Verificar estado actual
echo "📊 Verificando estado actual del vehículo..."
php artisan tinker --execute="
\$vehiculo = \App\Models\Vehiculo::with(['obras', 'operador'])->find($VEHICULO_ID);
echo 'Vehículo: ' . \$vehiculo->marca . ' ' . \$vehiculo->modelo;
\$obraActual = \$vehiculo->obraActual()->first();
if (\$obraActual) {
    echo '\nObra actual: ' . \$obraActual->nombre_obra;
} else {
    echo '\nSin obra actual';
}
echo '\nOperador actual: ' . (\$vehiculo->operador ? \$vehiculo->operador->nombre_completo : 'Sin operador');
"

# Obtener CSRF token y hacer login
echo "🔐 Obteniendo sesión autenticada..."
CSRF_TOKEN=$(curl -s -c cookies.txt "${BASE_URL}/login" | grep -o 'name="_token" value="[^"]*"' | cut -d'"' -f4)

# Login
curl -s -b cookies.txt -c cookies.txt \
    -d "email=admin@petrotekno.com" \
    -d "password=password" \
    -d "_token=$CSRF_TOKEN" \
    "${BASE_URL}/login" > /dev/null

# Obtener nuevo token después del login
CSRF_TOKEN=$(curl -s -b cookies.txt "${BASE_URL}/vehiculos/${VEHICULO_ID}" | grep -o 'name="_token" value="[^"]*"' | cut -d'"' -f4)

echo "🔄 Realizando cambio de obra..."
RESPONSE=$(curl -s -b cookies.txt -c cookies.txt \
    -X POST \
    -d "obra_id=$NUEVA_OBRA_ID" \
    -d "operador_id=$OPERADOR_ID" \
    -d "kilometraje_inicial=24000" \
    -d "observaciones=Cambio de obra desde script de prueba" \
    -d "_token=$CSRF_TOKEN" \
    "${BASE_URL}/asignaciones-obra/vehiculos/${VEHICULO_ID}/cambiar-obra")

echo "📨 Respuesta del servidor:"
echo "$RESPONSE" | head -5

# Verificar resultado
echo "✅ Verificando resultado del cambio..."
php artisan tinker --execute="
\$vehiculo = \App\Models\Vehiculo::with(['obras', 'operador'])->find($VEHICULO_ID);
echo 'Estado después del cambio:';
echo '\nVehículo: ' . \$vehiculo->marca . ' ' . \$vehiculo->modelo;
\$obraActual = \$vehiculo->obraActual()->first();
if (\$obraActual) {
    echo '\nObra actual: ' . \$obraActual->nombre_obra;
    echo '\nUbicación: ' . \$obraActual->ubicacion;
} else {
    echo '\nSin obra actual';
}
echo '\nOperador actual: ' . (\$vehiculo->operador ? \$vehiculo->operador->nombre_completo : 'Sin operador');
"

# Limpiar
rm -f cookies.txt

echo "🎯 Prueba completada!"
