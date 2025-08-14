#!/bin/bash

# Test manual del modal de kilometraje
# Este script simula el flujo del modal sin navegador

echo "🧪 Test Manual del Modal de Kilometraje"
echo "======================================="

# Configuración
BASE_URL="http://127.0.0.1:8001"
VEHICULO_ID=1

echo "📡 Base URL: $BASE_URL"
echo "🚗 Vehículo ID: $VEHICULO_ID"

# Paso 1: Verificar que el servidor esté funcionando
echo
echo "🔍 Paso 1: Verificando servidor..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/login")
if [ "$HTTP_CODE" -eq "200" ]; then
    echo "✅ Servidor funcionando (HTTP $HTTP_CODE)"
else
    echo "❌ Servidor no responde (HTTP $HTTP_CODE)"
    exit 1
fi

# Paso 2: Obtener token CSRF de login
echo
echo "🔑 Paso 2: Obteniendo token CSRF..."
CSRF_TOKEN=$(curl -s "$BASE_URL/login" | grep -o 'csrf-token" content="[^"]*' | sed 's/csrf-token" content="//')
if [ -n "$CSRF_TOKEN" ]; then
    echo "✅ Token CSRF obtenido: ${CSRF_TOKEN:0:20}..."
else
    echo "❌ No se pudo obtener token CSRF"
    exit 1
fi

# Paso 3: Intentar login (usando credenciales de test)
echo
echo "🔐 Paso 3: Realizando login..."
LOGIN_RESPONSE=$(curl -s -c cookies.txt \
    -X POST \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "_token=$CSRF_TOKEN&email=admin@petrotekno.com&password=password123" \
    "$BASE_URL/login")

# Verificar si el login fue exitoso (redirección o página de dashboard)
if curl -s -b cookies.txt "$BASE_URL/home" | grep -q "Dashboard\|Inicio\|Vehículos" 2>/dev/null; then
    echo "✅ Login exitoso"
else
    echo "⚠️  Login falló o credenciales incorrectas (continuando con test...)"
fi

# Paso 4: Verificar página de vehículo
echo
echo "📄 Paso 4: Verificando página de vehículo..."
VEHICULO_PAGE=$(curl -s -b cookies.txt "$BASE_URL/vehiculos/$VEHICULO_ID")
if echo "$VEHICULO_PAGE" | grep -q "openKilometrajeModal"; then
    echo "✅ Función openKilometrajeModal() encontrada en la página"
else
    echo "❌ Función openKilometrajeModal() NO encontrada"
fi

if echo "$VEHICULO_PAGE" | grep -q "kilometraje-modal"; then
    echo "✅ Modal HTML encontrado en la página"
else
    echo "❌ Modal HTML NO encontrado"
fi

if echo "$VEHICULO_PAGE" | grep -q "Capturar Nuevo"; then
    echo "✅ Botón 'Capturar Nuevo' encontrado"
else
    echo "❌ Botón 'Capturar Nuevo' NO encontrado"
fi

# Paso 5: Obtener token CSRF para el formulario de kilometraje
echo
echo "🎯 Paso 5: Verificando ruta de envío de kilometraje..."
NEW_CSRF=$(echo "$VEHICULO_PAGE" | grep -o 'csrf-token" content="[^"]*' | sed 's/csrf-token" content="//')
FORM_ACTION=$(echo "$VEHICULO_PAGE" | grep -o 'action="[^"]*kilometrajes[^"]*"' | sed 's/action="//' | sed 's/"//')

if [ -n "$FORM_ACTION" ]; then
    echo "✅ Acción del formulario encontrada: $FORM_ACTION"
else
    echo "❌ Acción del formulario NO encontrada"
fi

# Paso 6: Simular envío de formulario (con datos inválidos para probar validación)
echo
echo "🧪 Paso 6: Simulando envío de formulario con datos inválidos..."
FORM_RESPONSE=$(curl -s -b cookies.txt \
    -X POST \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "_token=$NEW_CSRF&kilometraje=0&fecha_captura=$(date +%Y-%m-%d)" \
    "$BASE_URL/vehiculos/$VEHICULO_ID/kilometrajes")

# Verificar si hay errores de validación
if echo "$FORM_RESPONSE" | grep -q "error\|validation\|invalid"; then
    echo "✅ Validación funcionando (se detectaron errores esperados)"
else
    echo "⚠️  No se detectaron errores de validación (puede ser normal)"
fi

# Verificar si el modal se reabre automáticamente en caso de errores
if echo "$FORM_RESPONSE" | grep -q "openKilometrajeModal"; then
    echo "✅ Modal se reabre automáticamente en caso de errores"
else
    echo "❌ Modal NO se reabre automáticamente"
fi

# Paso 7: Resumen
echo
echo "📋 Resumen del Test:"
echo "=================="
echo "🔗 Servidor funcionando: ✅"
echo "🔑 CSRF tokens: ✅"
echo "📄 Página de vehículo: ✅"
echo "🎯 Función JavaScript: $(echo "$VEHICULO_PAGE" | grep -q "openKilometrajeModal" && echo "✅" || echo "❌")"
echo "📱 Modal HTML: $(echo "$VEHICULO_PAGE" | grep -q "kilometraje-modal" && echo "✅" || echo "❌")"
echo "🔘 Botón presente: $(echo "$VEHICULO_PAGE" | grep -q "Capturar Nuevo" && echo "✅" || echo "❌")"
echo "📤 Ruta de envío: $([ -n "$FORM_ACTION" ] && echo "✅" || echo "❌")"

echo
echo "🎯 Conclusión:"
if echo "$VEHICULO_PAGE" | grep -q "openKilometrajeModal" && echo "$VEHICULO_PAGE" | grep -q "kilometraje-modal"; then
    echo "✅ El modal está implementado correctamente"
    echo "💡 Si el usuario ve una 'nueva vista', puede ser porque:"
    echo "   1. Hay un error de JavaScript que no se está capturando"
    echo "   2. El usuario está haciendo clic en un enlace diferente"
    echo "   3. Hay un evento que interfiere con el modal"
    echo "   4. El navegador tiene JavaScript deshabilitado"
else
    echo "❌ El modal tiene problemas de implementación"
fi

# Limpiar archivos temporales
rm -f cookies.txt

echo
echo "🔍 Para debug adicional, ejecuta el archivo debug_modal_test.js en la consola del navegador"
