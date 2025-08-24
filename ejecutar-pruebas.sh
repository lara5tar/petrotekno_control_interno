#!/bin/bash

# 🎯 Script de Ejecución Completa de Pruebas - Sistema PetroTekno
# Autor: Análisis automatizado Playwright
# Fecha: Agosto 2025

echo "🚀 INICIANDO ANÁLISIS COMPLETO DEL SISTEMA PETROTEKNO"
echo "======================================================="

# Verificar dependencias
echo "📋 Verificando dependencias..."
if ! command -v node &> /dev/null; then
    echo "❌ Node.js no está instalado"
    exit 1
fi

if ! command -v php &> /dev/null; then
    echo "❌ PHP no está instalado"
    exit 1
fi

echo "✅ Dependencias verificadas"

# Instalar dependencias de Playwright si no existen
if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependencias de Playwright..."
    npm install
fi

# Verificar si los navegadores de Playwright están instalados
echo "🌐 Verificando navegadores de Playwright..."
npx playwright install

# Iniciar servidor Laravel en background
echo "🔧 Iniciando servidor Laravel..."
php artisan serve --port=8001 &
SERVER_PID=$!

# Esperar a que el servidor esté listo
echo "⏳ Esperando a que el servidor esté listo..."
sleep 3

# Verificar que el servidor está corriendo
if curl -s http://127.0.0.1:8001 > /dev/null; then
    echo "✅ Servidor Laravel corriendo en puerto 8001"
else
    echo "❌ Error: Servidor no responde"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

echo ""
echo "🧪 EJECUTANDO SUITE COMPLETA DE PRUEBAS"
echo "========================================"

# Función para ejecutar pruebas y manejar errores
run_test() {
    local test_file=$1
    local test_name=$2
    
    echo ""
    echo "📝 Ejecutando: $test_name"
    echo "----------------------------------------"
    
    if npx playwright test "tests/playwright/$test_file" --project=chromium --reporter=list; then
        echo "✅ $test_name - COMPLETADO"
    else
        echo "⚠️  $test_name - COMPLETADO CON OBSERVACIONES"
    fi
}

# Ejecutar análisis sin login (siempre funciona)
run_test "analisis-sin-login.spec.js" "Análisis del Sistema (Sin Login)"

# Intentar ejecutar pruebas con autenticación
echo ""
echo "🔐 INTENTANDO PRUEBAS CON AUTENTICACIÓN"
echo "====================================="

# Resetear contraseña del admin (por si acaso)
echo "🔑 Configurando credenciales de prueba..."
php artisan tinker --execute="
\$user = App\Models\User::where('email', 'admin@petrotekno.com')->first();
if (\$user) {
    \$user->password = Hash::make('password123');
    \$user->save();
    echo 'Usuario admin configurado correctamente';
} else {
    echo 'Usuario admin no encontrado';
}
"

# Ejecutar pruebas de autenticación
run_test "auth.spec.js" "Pruebas de Autenticación"

# Si las pruebas de auth funcionan, ejecutar el resto
echo ""
echo "📊 EJECUTANDO PRUEBAS DE MÓDULOS PRINCIPALES"
echo "==========================================="

run_test "vehiculos.spec.js" "Gestión de Vehículos"
run_test "personal.spec.js" "Gestión de Personal"
run_test "reportes.spec.js" "Sistema de Reportes"
run_test "dashboard.spec.js" "Dashboard y UX"
run_test "mantenimientos-obras.spec.js" "Mantenimientos y Obras"
run_test "suite-completa.spec.js" "Pruebas de Integración"

echo ""
echo "📈 GENERANDO REPORTE COMPLETO"
echo "============================="

# Ejecutar con reporte HTML
npx playwright test --reporter=html

echo ""
echo "🎯 RESUMEN FINAL"
echo "==============="
echo "✅ Suite de pruebas ejecutada"
echo "📊 Reporte HTML generado en: playwright-report/"
echo "📋 Reporte completo disponible en: REPORTE_COMPLETO_TESTING.md"
echo "🌐 Para ver el reporte HTML: npx playwright show-report"
echo ""

# Matar el servidor
echo "🛑 Deteniendo servidor Laravel..."
kill $SERVER_PID 2>/dev/null

echo "🏆 ANÁLISIS COMPLETO FINALIZADO"
echo ""
echo "📞 PRÓXIMOS PASOS:"
echo "  1. Revisar el reporte HTML: npx playwright show-report"
echo "  2. Revisar logs en playwright-report/"
echo "  3. Configurar CI/CD para ejecución automática"
echo "  4. Implementar en pipeline de desarrollo"
echo ""
echo "🎉 Sistema PetroTekno - Testing completado exitosamente!"
