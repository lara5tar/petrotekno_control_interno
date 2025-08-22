#!/bin/bash

# Script para probar la reparación del botón responsable obra
echo "🔧 Iniciando prueba de reparación..."

# Verificar que el servidor esté activo
echo "📡 Verificando servidor..."
curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8002 > /dev/null
SERVER_STATUS=$?

if [ $SERVER_STATUS -ne 0 ]; then
  echo "❌ El servidor no está activo. Iniciando servidor..."
  php artisan serve --host=0.0.0.0 --port=8002 &
  sleep 3
fi

# Crear un script simple para probar la funcionalidad
echo "📝 Creando script de prueba..."

cat > test-modal-tmp.js << EOF
const { chromium } = require('playwright');

(async () => {
  console.log('🔧 Iniciando prueba del modal responsable obra...');
  
  const browser = await chromium.launch({ 
    headless: false,
    slowMo: 100 
  });
  
  const page = await browser.newPage();
  
  try {
    // Acceder a la página
    await page.goto('http://127.0.0.1:8002/login');
    console.log('✅ Página cargada');
    
    // Login
    await page.fill('#email', 'admin@petrotekno.com');
    await page.fill('#password', 'password');
    await page.click('button[type="submit"]');
    
    // Ir a vehículos
    await page.waitForTimeout(2000);
    await page.goto('http://127.0.0.1:8002/vehiculos');
    await page.waitForTimeout(2000);
    
    // Hacer clic en el primer vehículo
    const vehiculoLinks = await page.$$('a[href*="/vehiculos/"]');
    
    if (vehiculoLinks.length === 0) {
      console.log('❌ No se encontraron vehículos');
      await browser.close();
      return;
    }
    
    await vehiculoLinks[0].click();
    await page.waitForTimeout(2000);
    
    // Capturar antes de hacer clic
    await page.screenshot({ path: 'antes-btn-responsable.png' });
    
    // Encontrar el botón por su ID
    const boton = await page.$('#btn-responsable-obra');
    
    if (!boton) {
      console.log('❌ No se encontró el botón #btn-responsable-obra');
      // Verificar HTML
      const html = await page.content();
      console.log('HTML de la página:', html.slice(0, 500) + '...');
      await browser.close();
      return;
    }
    
    // Hacer clic en el botón
    console.log('🖱️ Haciendo clic en el botón...');
    await boton.click();
    
    // Esperar a que aparezca el modal
    await page.waitForTimeout(1000);
    await page.screenshot({ path: 'despues-btn-responsable.png' });
    
    // Verificar si el modal está visible
    const modal = await page.$('#responsable-obra-modal');
    const isVisible = await modal.isVisible();
    
    console.log(`Modal visible: ${isVisible ? '✅ SÍ' : '❌ NO'}`);
    
    // Tomar captura final
    await page.screenshot({ path: 'resultado-modal-responsable.png' });
    
    // Cerrar navegador
    await page.waitForTimeout(3000);
    await browser.close();
    
  } catch (error) {
    console.error('❌ Error:', error);
    await browser.close();
  }
})();
EOF

# Ejecutar el script
echo "🚀 Ejecutando prueba..."
node test-modal-tmp.js

# Limpiar
echo "🧹 Limpiando archivos temporales..."
rm test-modal-tmp.js

echo "✅ Prueba completada."
