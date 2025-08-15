import { test, expect } from '@playwright/test';

test.describe('Debug Modal - Verificar Estructura', () => {
  async function login(page) {
    console.log('🔐 Iniciando proceso de login...');
    await page.goto('http://localhost:8000/login');
    
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/home', { timeout: 10000 });
    console.log('✅ Login exitoso');
  }

  test('Inspeccionar estructura del modal y botones', async ({ page }) => {
    console.log('🔍 Iniciando inspección de modal...');
    
    await login(page);
    
    console.log('📍 Navegando a página del vehículo...');
    await page.goto('http://localhost:8000/vehiculos/1');
    
    await expect(page).toHaveTitle(/Detalles del Vehículo/);
    console.log('✅ Página cargada');
    
    // Buscar todos los botones que contengan "Cambiar" o "Obra"
    console.log('🔍 Buscando botones relacionados con "Cambiar Obra"...');
    
    const botonesCambiar = await page.locator('button:has-text("Cambiar")').all();
    console.log(`📊 Encontrados ${botonesCambiar.length} botones con "Cambiar"`);
    
    for (let i = 0; i < botonesCambiar.length; i++) {
      const texto = await botonesCambiar[i].textContent();
      console.log(`   Botón ${i + 1}: "${texto}"`);
    }
    
    const botonesObra = await page.locator('button:has-text("Obra")').all();
    console.log(`📊 Encontrados ${botonesObra.length} botones con "Obra"`);
    
    for (let i = 0; i < botonesObra.length; i++) {
      const texto = await botonesObra[i].textContent();
      console.log(`   Botón ${i + 1}: "${texto}"`);
    }
    
    // Buscar cualquier botón que contenga ambas palabras
    const botonCambiarObra = page.locator('button:has-text("Cambiar"), button:has-text("obra")').first();
    
    if (await botonCambiarObra.isVisible()) {
      console.log('✅ Botón encontrado, haciendo clic...');
      await botonCambiarObra.click();
      
      // Esperar un poco para que aparezca el modal
      await page.waitForTimeout(1000);
      
      // Buscar cualquier modal visible
      console.log('🔍 Buscando modales visibles...');
      
      const modalesConId = await page.locator('[id*="modal"], [id*="Modal"]').all();
      console.log(`📊 Encontrados ${modalesConId.length} elementos con ID que contiene "modal"`);
      
      for (let i = 0; i < modalesConId.length; i++) {
        const id = await modalesConId[i].getAttribute('id');
        const visible = await modalesConId[i].isVisible();
        console.log(`   Modal ${i + 1}: ID="${id}", Visible=${visible}`);
      }
      
      // Buscar por clases de modal
      const modalesConClass = await page.locator('[class*="modal"]').all();
      console.log(`📊 Encontrados ${modalesConClass.length} elementos con clase que contiene "modal"`);
      
      // Verificar divs con backdrop
      const backdrops = await page.locator('.modal-backdrop, [class*="backdrop"]').all();
      console.log(`📊 Encontrados ${backdrops.length} elementos backdrop`);
      
      // Tomar screenshot para inspección visual
      await page.screenshot({ path: 'debug-modal-state.png', fullPage: true });
      console.log('📸 Screenshot guardado como debug-modal-state.png');
      
    } else {
      console.log('❌ No se encontró botón de cambiar obra');
      
      // Buscar contenido general de la página
      const contenidoPagina = await page.textContent('body');
      if (contenidoPagina.includes('Cambiar')) {
        console.log('✅ La palabra "Cambiar" existe en la página');
      } else {
        console.log('❌ La palabra "Cambiar" NO existe en la página');
      }
      
      if (contenidoPagina.includes('Obra')) {
        console.log('✅ La palabra "Obra" existe en la página');
      } else {
        console.log('❌ La palabra "Obra" NO existe en la página');
      }
    }
    
    console.log('🔍 Inspección completada');
  });
});
