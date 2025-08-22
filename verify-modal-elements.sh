#!/bin/bash

echo "🔧 Verificando elementos del modal de responsable en HTML..."

# Verificar que existe el botón en el header
echo "🔍 Buscando botón 'Asignar Responsable' en header..."
if grep -q "openCambiarEncargadoModal()" resources/views/obras/show.blade.php; then
    echo "✅ Función openCambiarEncargadoModal() encontrada"
else
    echo "❌ Función openCambiarEncargadoModal() NO encontrada"
fi

# Verificar texto del botón
if grep -q "Asignar Responsable" resources/views/obras/show.blade.php; then
    echo "✅ Texto 'Asignar Responsable' encontrado"
else
    echo "❌ Texto 'Asignar Responsable' NO encontrado"
fi

if grep -q "Cambiar Responsable" resources/views/obras/show.blade.php; then
    echo "✅ Texto 'Cambiar Responsable' encontrado"
else
    echo "❌ Texto 'Cambiar Responsable' NO encontrado"
fi

# Verificar modal HTML
echo "🔍 Buscando modal HTML..."
if grep -q "cambiar-encargado-modal" resources/views/obras/show.blade.php; then
    echo "✅ Modal HTML 'cambiar-encargado-modal' encontrado"
else
    echo "❌ Modal HTML 'cambiar-encargado-modal' NO encontrado"
fi

# Verificar JavaScript
echo "🔍 Buscando funciones JavaScript..."
if grep -q "openCambiarEncargadoModal" resources/views/obras/show.blade.php; then
    echo "✅ Función JavaScript openCambiarEncargadoModal encontrada"
else
    echo "❌ Función JavaScript openCambiarEncargadoModal NO encontrada"
fi

if grep -q "closeCambiarEncargadoModal" resources/views/obras/show.blade.php; then
    echo "✅ Función JavaScript closeCambiarEncargadoModal encontrada"
else
    echo "❌ Función JavaScript closeCambiarEncargadoModal NO encontrada"
fi

# Verificar ruta del formulario
echo "🔍 Buscando ruta del formulario..."
if grep -q "obras.cambiar-encargado" resources/views/obras/show.blade.php; then
    echo "✅ Ruta 'obras.cambiar-encargado' encontrada"
else
    echo "❌ Ruta 'obras.cambiar-encargado' NO encontrada"
fi

# Verificar que la ruta existe en web.php
echo "🔍 Verificando ruta en web.php..."
if grep -q "cambiar-encargado" routes/web.php; then
    echo "✅ Ruta 'cambiar-encargado' registrada en web.php"
else
    echo "❌ Ruta 'cambiar-encargado' NO registrada en web.php"
fi

echo ""
echo "📋 Resumen de elementos encontrados:"
echo "- Botones: $(grep -c "openCambiarEncargadoModal()" resources/views/obras/show.blade.php) instancias"
echo "- Modal HTML: $(grep -c "cambiar-encargado-modal" resources/views/obras/show.blade.php) instancias"
echo "- Funciones JS: $(grep -c "function.*EncargadoModal" resources/views/obras/show.blade.php) instancias"

echo ""
echo "🚀 Verificación completada. Si todos los elementos están presentes, el modal debería funcionar correctamente."
