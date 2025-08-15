#!/bin/bash

# Test para verificar la funcionalidad de asignación automática de obra en kilometrajes

BASE_URL="http://127.0.0.1:8000"
VEHICULO_ID=1

echo "🔍 Probando la funcionalidad de asignación automática de obra en kilometrajes..."

# Obtener CSRF token
echo "📝 Obteniendo token CSRF..."
CSRF_TOKEN=$(curl -s -c cookies.txt "${BASE_URL}/vehiculos/${VEHICULO_ID}" | grep -o 'name="_token" value="[^"]*"' | cut -d'"' -f4)

if [ -z "$CSRF_TOKEN" ]; then
    echo "❌ Error: No se pudo obtener el token CSRF"
    exit 1
fi

echo "✅ Token CSRF obtenido: ${CSRF_TOKEN:0:10}..."

# Hacer login 
echo "🔐 Iniciando sesión..."
curl -s -b cookies.txt -c cookies.txt \
    -d "email=admin@petrotekno.com" \
    -d "password=password" \
    -d "_token=$CSRF_TOKEN" \
    "${BASE_URL}/login" > /dev/null

# Obtener nuevo token después del login
CSRF_TOKEN=$(curl -s -b cookies.txt "${BASE_URL}/vehiculos/${VEHICULO_ID}" | grep -o 'name="_token" value="[^"]*"' | cut -d'"' -f4)

# Crear kilometraje de prueba
echo "📊 Creando kilometraje de prueba..."
RESPONSE=$(curl -s -b cookies.txt -c cookies.txt \
    -d "kilometraje=23500" \
    -d "fecha_captura=$(date +%Y-%m-%d)" \
    -d "observaciones=Prueba de asignación automática de obra" \
    -d "_token=$CSRF_TOKEN" \
    "${BASE_URL}/vehiculos/${VEHICULO_ID}/kilometrajes")

if echo "$RESPONSE" | grep -q "registrado exitosamente"; then
    echo "✅ Kilometraje creado exitosamente"
else
    echo "❌ Error al crear kilometraje"
    echo "Respuesta del servidor:"
    echo "$RESPONSE"
fi

# Verificar en base de datos
echo "🔍 Verificando en base de datos..."
php artisan tinker --execute="
\$ultimoKm = \App\Models\Kilometraje::with(['vehiculo', 'obra'])->orderBy('id', 'desc')->first();
if (\$ultimoKm) {
    echo 'Último kilometraje registrado:';
    echo '\n- ID: ' . \$ultimoKm->id;
    echo '\n- Vehículo: ' . \$ultimoKm->vehiculo->marca . ' ' . \$ultimoKm->vehiculo->modelo;
    echo '\n- Kilometraje: ' . \$ultimoKm->kilometraje . ' km';
    echo '\n- Obra asociada: ' . (\$ultimoKm->obra ? \$ultimoKm->obra->nombre_obra : 'Sin obra asignada');
    echo '\n- Observaciones: ' . \$ultimoKm->observaciones;
} else {
    echo 'No se encontraron kilometrajes';
}
"

# Limpiar archivos temporales
rm -f cookies.txt

echo "🎯 Prueba completada!"
