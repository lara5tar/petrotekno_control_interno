const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false, slowMo: 500 });
  const context = await browser.newContext();
  const page = await context.newPage();

  try {
    console.log('🚀 Iniciando pruebas de cuenta bancaria...');

    // 1. Ir a la página de login
    console.log('📝 Navegando a la página de login...');
    await page.goto('http://localhost:8003/login');
    await page.waitForLoadState('networkidle');

    // 2. Login (ajusta las credenciales según tu sistema)
    console.log('🔐 Iniciando sesión...');
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');

    // 3. Navegar a crear personal
    console.log('➕ Navegando a crear personal...');
    await page.goto('http://localhost:8003/personal/create');
    await page.waitForLoadState('networkidle');

    // 4. Verificar que el campo cuenta_bancaria existe
    console.log('🔍 Verificando que el campo cuenta_bancaria existe...');
    const cuentaBancariaInput = await page.locator('input[name="cuenta_bancaria"]');
    const isVisible = await cuentaBancariaInput.isVisible();
    
    if (!isVisible) {
      throw new Error('❌ El campo cuenta_bancaria NO está visible en el formulario');
    }
    console.log('✅ Campo cuenta_bancaria encontrado y visible');

    // 5. Llenar el formulario mínimo
    console.log('📋 Llenando formulario de prueba...');
    await page.fill('input[name="nombre_completo"]', 'TEST CUENTA BANCARIA PLAYWRIGHT');
    await page.selectOption('select[name="categoria_personal_id"]', '1');
    await page.fill('input[name="cuenta_bancaria"]', '123456789012345678');

    // 6. Enviar el formulario
    console.log('💾 Guardando personal...');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
    
    // Esperar un poco para que se procese
    await page.waitForTimeout(2000);

    // 7. Verificar que se guardó correctamente
    console.log('🔎 Verificando que se guardó correctamente...');
    const currentUrl = page.url();
    console.log('URL actual:', currentUrl);

    // Buscar mensaje de éxito o verificar que no hay errores
    const errorMessage = await page.locator('.text-red-500, .alert-danger').count();
    const successMessage = await page.locator('.text-green-500, .alert-success, .bg-green-50').count();

    if (errorMessage > 0) {
      console.log('⚠️  Se detectaron mensajes de error');
      const errorText = await page.locator('.text-red-500, .alert-danger').first().textContent();
      console.log('Error:', errorText);
    }

    if (successMessage > 0) {
      console.log('✅ Se detectó mensaje de éxito');
    }

    // 8. Verificar en base de datos usando la API de Laravel
    console.log('🗄️  Verificando en base de datos...');
    const response = await page.goto('http://localhost:8003/api/personal');
    const personalList = await response.json();
    
    const testPersonal = personalList.data?.find(p => p.nombre_completo === 'TEST CUENTA BANCARIA PLAYWRIGHT') 
                      || personalList.find(p => p.nombre_completo === 'TEST CUENTA BANCARIA PLAYWRIGHT');
    
    if (testPersonal) {
      console.log('✅ Personal encontrado en la base de datos');
      console.log('ID:', testPersonal.id);
      console.log('Nombre:', testPersonal.nombre_completo);
      console.log('Cuenta bancaria:', testPersonal.cuenta_bancaria);
      
      if (testPersonal.cuenta_bancaria === '123456789012345678') {
        console.log('✅✅✅ PRUEBA EXITOSA: El campo cuenta_bancaria se guardó correctamente');
      } else {
        console.log('❌ El campo cuenta_bancaria NO tiene el valor esperado');
        console.log('Esperado: 123456789012345678');
        console.log('Recibido:', testPersonal.cuenta_bancaria);
      }

      // 9. Limpiar: Eliminar el registro de prueba
      console.log('🧹 Limpiando registro de prueba...');
      await page.goto(`http://localhost:8003/personal/${testPersonal.id}`);
      await page.waitForLoadState('networkidle');
      
      // Buscar y hacer clic en el botón de eliminar
      const deleteButton = await page.locator('button[type="submit"]:has-text("Eliminar")');
      if (await deleteButton.count() > 0) {
        page.on('dialog', dialog => dialog.accept()); // Aceptar el confirm
        await deleteButton.click();
        await page.waitForTimeout(1000);
        console.log('✅ Registro de prueba eliminado');
      }
    } else {
      console.log('⚠️  No se encontró el personal de prueba en la base de datos');
      console.log('Esto podría indicar un problema en el guardado');
    }

    console.log('\n🎉 Pruebas completadas');

  } catch (error) {
    console.error('❌ Error durante las pruebas:', error.message);
    console.error(error.stack);
  } finally {
    await browser.close();
  }
})();
