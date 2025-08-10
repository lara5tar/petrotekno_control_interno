import { test, expect } from '@playwright/test';

test.describe('Obras - Crear Nueva Obra', () => {
  test.beforeEach(async ({ page }) => {
    // Interceptar y bloquear requests a URLs externas para evitar timeouts
    await page.route('**/*', (route) => {
      const url = route.request().url();
      if (url.includes('maps.googleapis.com') || 
          url.includes('fonts.googleapis.com') ||
          url.includes('cdnjs.cloudflare.com') ||
          url.includes('ajax.googleapis.com')) {
        route.abort();
      } else {
        route.continue();
      }
    });
  });

  test('debería crear una nueva obra exitosamente', async ({ page }) => {
    console.log('=== INICIANDO TEST DE CREACIÓN DE OBRA ===');
    
    // Navegar al login
    await page.goto('http://localhost:8000/login');
    await page.waitForLoadState('networkidle');
    
    // Login como admin
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
    
    console.log('Usuario autenticado exitosamente');
    
    // Navegar a obras
    await page.goto('http://localhost:8000/obras');
    await page.waitForLoadState('networkidle');
    
    console.log('Navegando a página de obras');
    
    // Buscar el botón de crear obra (puede tener diferentes textos)
    const createButton = page.locator('a[href*="/obras/create"], button:has-text("Crear"), a:has-text("Nueva"), a:has-text("Agregar")').first();
    
    if (await createButton.count() === 0) {
      console.log('No se encontró botón de crear obra, verificando permisos...');
      
      // Verificar si hay mensaje de permisos
      const noPermissionText = await page.textContent('body');
      if (noPermissionText?.includes('permisos')) {
        console.log('ERROR: Usuario no tiene permisos para crear obras');
      }
      
      // Tomar screenshot para debug
      await page.screenshot({ path: 'debug-no-create-button.png', fullPage: true });
      throw new Error('No se encontró botón para crear obra');
    }
    
    // Hacer clic en crear obra
    await createButton.click();
    await page.waitForLoadState('networkidle');
    
    console.log('Navegando a formulario de creación');
    
    // Verificar que estamos en la página de creación
    await expect(page).toHaveURL(/.*obras\/create.*/);
    
    // Llenar el formulario
    const testObraName = `Obra Test ${Date.now()}`;
    
    await page.fill('input[name="nombre_obra"]', testObraName);
    await page.selectOption('select[name="estatus"]', 'planificada');
    await page.fill('input[name="fecha_inicio"]', '2024-01-15');
    await page.fill('input[name="fecha_fin"]', '2024-12-31');
    await page.fill('input[name="avance"]', '0');
    await page.fill('textarea[name="observaciones"]', 'Obra de prueba creada por test automatizado');
    
    console.log(`Formulario llenado con obra: ${testObraName}`);
    
    // Tomar screenshot antes de enviar
    await page.screenshot({ path: 'debug-form-filled.png', fullPage: true });
    
    // Enviar formulario
    const submitButton = page.locator('button[type="submit"], input[type="submit"]');
    await submitButton.click();
    
    console.log('Formulario enviado');
    
    // Esperar respuesta y verificar redirección
    await page.waitForLoadState('networkidle');
    
    // Verificar que fue exitoso (debe redirigir a index o mostrar mensaje de éxito)
    const currentUrl = page.url();
    console.log(`URL después de envío: ${currentUrl}`);
    
    // Verificar mensaje de éxito o redirección a lista
    const hasSuccessMessage = await page.locator('text=/exitosamente|éxito|creada/i').count() > 0;
    const isOnIndexPage = currentUrl.includes('/obras') && !currentUrl.includes('/create');
    
    if (hasSuccessMessage || isOnIndexPage) {
      console.log('✅ OBRA CREADA EXITOSAMENTE');
      
      // Tomar screenshot del resultado
      await page.screenshot({ path: 'debug-obra-created-success.png', fullPage: true });
      
      // Si estamos en la lista, buscar la obra creada
      if (isOnIndexPage) {
        const obraExists = await page.locator(`text=${testObraName}`).count() > 0;
        if (obraExists) {
          console.log('✅ Obra encontrada en la lista');
        }
      }
    } else {
      console.log('❌ ERROR: No se detectó éxito en la creación');
      
      // Verificar si hay errores en la página
      const pageText = await page.textContent('body');
      console.log('Contenido de la página:', pageText?.substring(0, 500));
      
      // Tomar screenshot del error
      await page.screenshot({ path: 'debug-obra-creation-error.png', fullPage: true });
      
      throw new Error('La creación de obra no fue exitosa');
    }
  });
});
