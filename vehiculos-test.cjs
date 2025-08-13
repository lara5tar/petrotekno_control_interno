const { chromium } = require('@playwright/test');

(async () => {
  const browser = await chromium.launch();
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navegando a la página de vehículos...');
    await page.goto('http://localhost:8000/vehiculos', { timeout: 30000 });
    console.log('Verificando que la página cargó correctamente...');
    
    // Verificar si hay algún mensaje de error en la página
    const errorText = await page.textContent('body');
    if (errorText.includes('Internal Server Error') || errorText.includes('TypeError')) {
      console.error('❌ Error encontrado en la página:', errorText.substring(0, 200) + '...');
    } else {
      console.log('✅ La página cargó correctamente sin errores.');
    }
    
    // Tomar una captura de pantalla
    await page.screenshot({ path: 'vehiculos-page-test.png' });
    console.log('Captura de pantalla guardada como vehiculos-page-test.png');
  } catch (error) {
    console.error('❌ Error durante la prueba:', error);
  } finally {
    await browser.close();
  }
})();
