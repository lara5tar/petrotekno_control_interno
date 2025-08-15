#!/bin/bash

# Script para probar la funcionalidad de cambio de operador

echo "=== TESTING CAMBIO DE OPERADOR ==="
echo ""

# 1. Obtener token CSRF
echo "1. Obteniendo token CSRF..."
CSRF_TOKEN=$(curl -s -c cookies.txt http://127.0.0.1:8002/login | grep '_token' | sed -n 's/.*value="\([^"]*\)".*/\1/p')
echo "CSRF Token: $CSRF_TOKEN"
echo ""

# 2. Login
echo "2. Haciendo login..."
LOGIN_RESPONSE=$(curl -s -b cookies.txt -c cookies.txt -X POST \
  -d "email=admin@petrotekno.com" \
  -d "password=password" \
  -d "_token=$CSRF_TOKEN" \
  http://127.0.0.1:8002/login)

# Verificar si el login fue exitoso
if echo "$LOGIN_RESPONSE" | grep -q "dashboard\|home\|vehiculos"; then
    echo "✅ Login exitoso"
else
    echo "❌ Login falló"
    echo "Response: $LOGIN_RESPONSE"
    exit 1
fi
echo ""

# 3. Obtener lista de vehículos
echo "3. Obteniendo lista de vehículos..."
VEHICULOS_PAGE=$(curl -s -b cookies.txt http://127.0.0.1:8002/vehiculos)

# Extraer ID del primer vehículo
VEHICULO_ID=$(echo "$VEHICULOS_PAGE" | grep -o 'vehiculos/[0-9]*' | head -1 | sed 's/vehiculos\///')
echo "Primer vehículo ID: $VEHICULO_ID"

if [ -z "$VEHICULO_ID" ]; then
    echo "❌ No se encontraron vehículos"
    exit 1
fi
echo ""

# 4. Obtener detalles del vehículo
echo "4. Obteniendo detalles del vehículo..."
VEHICULO_DETAILS=$(curl -s -b cookies.txt http://127.0.0.1:8002/vehiculos/$VEHICULO_ID)

# Verificar si hay botón de cambiar operador
if echo "$VEHICULO_DETAILS" | grep -q "Cambiar Operador\|Asignar Operador"; then
    echo "✅ Botón de cambiar operador encontrado"
else
    echo "❌ No se encontró botón de cambiar operador"
fi
echo ""

# 5. Obtener lista de operadores
echo "5. Obteniendo lista de operadores disponibles..."
OPERADORES=$(curl -s -b cookies.txt http://127.0.0.1:8002/vehiculos/$VEHICULO_ID | grep -A 100 'id="personal_id"' | grep '<option value="[0-9]' | head -5)
echo "Operadores disponibles:"
echo "$OPERADORES"

# Extraer ID del primer operador
OPERADOR_ID=$(echo "$OPERADORES" | head -1 | grep -o 'value="[0-9]*"' | sed 's/value="\([0-9]*\)"/\1/')
echo "Primer operador ID: $OPERADOR_ID"
echo ""

# 6. Obtener nuevo token CSRF para el cambio
echo "6. Obteniendo nuevo token CSRF..."
NEW_CSRF_TOKEN=$(echo "$VEHICULO_DETAILS" | grep '_token' | sed -n 's/.*value="\([^"]*\)".*/\1/p')
echo "Nuevo CSRF Token: $NEW_CSRF_TOKEN"
echo ""

# 7. Probar cambio de operador
if [ ! -z "$OPERADOR_ID" ] && [ ! -z "$NEW_CSRF_TOKEN" ]; then
    echo "7. Probando cambio de operador..."
    
    CHANGE_RESPONSE=$(curl -s -b cookies.txt -X PATCH \
      -H "Content-Type: application/x-www-form-urlencoded" \
      -H "X-Requested-With: XMLHttpRequest" \
      -d "operador_id=$OPERADOR_ID" \
      -d "_token=$NEW_CSRF_TOKEN" \
      -d "_method=PATCH" \
      http://127.0.0.1:8002/vehiculos/$VEHICULO_ID/cambiar-operador)
    
    echo "Response del cambio de operador:"
    echo "$CHANGE_RESPONSE"
    echo ""
    
    # Verificar si la respuesta es exitosa
    if echo "$CHANGE_RESPONSE" | grep -q '"success":true'; then
        echo "✅ Cambio de operador exitoso"
    elif echo "$CHANGE_RESPONSE" | grep -q '"success":false'; then
        echo "❌ Error en cambio de operador:"
        echo "$CHANGE_RESPONSE" | grep -o '"error":"[^"]*"'
    else
        echo "⚠️ Respuesta inesperada:"
        echo "$CHANGE_RESPONSE"
    fi
else
    echo "❌ No se pudo probar cambio de operador - faltan datos"
fi

# Limpiar cookies
rm -f cookies.txt

echo ""
echo "=== FIN DEL TEST ==="
