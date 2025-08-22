#!/bin/bash

echo "🔧 Test de reparación del botón Asignar Responsable..."

# 1. Verificar que el servidor esté corriendo
echo "📡 Verificando servidor..."
if curl -s http://127.0.0.1:8005 > /dev/null; then
    echo "✅ Servidor corriendo en puerto 8005"
else
    echo "❌ Servidor no responde en puerto 8005"
    exit 1
fi

# 2. Verificar elementos en el código
echo "🔍 Verificando elementos en código..."

# Botones
BOTONES=$(grep -c "openCambiarEncargadoModal" resources/views/obras/show.blade.php)
echo "  - Botones con openCambiarEncargadoModal: $BOTONES"

# Modal HTML
MODAL=$(grep -c "cambiar-encargado-modal" resources/views/obras/show.blade.php)
echo "  - Referencias al modal: $MODAL"

# Funciones JavaScript
JS_FUNCTIONS=$(grep -c "window\.openCambiarEncargadoModal" resources/views/obras/show.blade.php)
echo "  - Funciones JavaScript: $JS_FUNCTIONS"

# ID en botones
BOTONES_CON_ID=$(grep -c 'id="btn-.*responsable' resources/views/obras/show.blade.php)
echo "  - Botones con ID: $BOTONES_CON_ID"

# 3. Verificar que no hay errores de sintaxis
echo "🔧 Verificando sintaxis..."
if php -l resources/views/obras/show.blade.php > /dev/null 2>&1; then
    echo "✅ Sin errores de sintaxis PHP"
else
    echo "❌ Errores de sintaxis PHP detectados"
fi

# 4. Verificar ruta
echo "🛣️ Verificando ruta..."
if grep -q "obras.cambiar-encargado" routes/web.php; then
    echo "✅ Ruta registrada correctamente"
else
    echo "❌ Ruta no encontrada"
fi

echo ""
echo "📋 RESUMEN DE REPARACIONES APLICADAS:"
echo "  ✅ Funciones JavaScript movidas a window scope"
echo "  ✅ Agregados console.log para debug"
echo "  ✅ Agregados IDs a botones para mejor identificación"
echo "  ✅ Agregado botón de test temporal"
echo "  ✅ Botón de debug para verificar modal"
echo ""
echo "🚀 PRÓXIMOS PASOS:"
echo "  1. Abrir http://127.0.0.1:8005/obras/1 en el navegador"
echo "  2. Ir a pestaña 'Recursos'"
echo "  3. Buscar la sección 'Encargado de la Obra'"
echo "  4. Hacer click en cualquiera de los botones:"
echo "     - 'Asignar Responsable' (azul, en header)"
echo "     - 'Asignar Responsable' (rojo, en centro)"
echo "     - 'TEST: Abrir Modal' (verde, botón de test)"
echo "  5. Verificar que el modal se abre"
echo "  6. Si hay problemas, usar 'DEBUG: Verificar Modal'"
echo ""
echo "💡 TIP: Abrir DevTools (F12) para ver console.log"
