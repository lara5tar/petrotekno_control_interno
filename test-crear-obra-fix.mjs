import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();
    try {
        console.log('🔧 Probando creación de obra después del fix...');

        // Ir al login
        await page.goto('http://localhost:8003/login');
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar dashboard
        await page.waitForURL('**/dashboard');
        console.log('✅ Login exitoso');

        // Ir a obras
        await page.goto('http://localhost:8003/obras');
        await page.waitForTimeout(1000);

        // Hacer clic en crear obra
        await page.click('text=Agregar Obra');
        await page.waitForTimeout(1000);

        console.log('📝 Llenando formulario de obra...');

        // Llenar formulario básico
        await page.fill('input[name="nombre_obra"]', 'Test Obra Fix - ' + Date.now());
        await page.fill('input[name="ubicacion"]', 'Ubicación Test');
        await page.selectOption('select[name="estatus"]', 'en_progreso');
        await page.fill('input[name="avance"]', '25');

        // Fechas
        await page.fill('input[name="fecha_inicio"]', '2025-08-21');
        await page.fill('input[name="fecha_fin"]', '2025-08-31');

        // Observaciones (el campo que causaba el error)
        await page.fill('textarea[name="observaciones"]', 'Test de observaciones después del fix');

        // Responsable (primer option que no sea "Seleccione")
        const responsableOptions = await page.locator('select[name="encargado_id"] option').count();
        if (responsableOptions > 1) {
            await page.selectOption('select[name="encargado_id"]', { index: 1 });
        }

        console.log('📋 Formulario llenado, enviando...');

        // Enviar formulario
        await page.click('button[type="submit"]');

        // Esperar respuesta (éxito o error)
        await page.waitForTimeout(3000);

        // Verificar si hay error
        const errorElement = await page.locator('.text-red-600, .bg-red-100, .alert-danger').first();
        const hasError = await errorElement.isVisible().catch(() => false);

        if (hasError) {
            const errorText = await errorElement.textContent();
            console.log('❌ Error encontrado:', errorText);
        } else {
            console.log('✅ No se detectaron errores visibles');

            // Verificar si estamos en la página de listado (éxito)
            const currentUrl = page.url();
            if (currentUrl.includes('/obras') && !currentUrl.includes('/create')) {
                console.log('✅ ÉXITO: Obra creada correctamente - redirigido a lista');
            } else {
                console.log('⚠️  Aún en formulario - revisar si hay errores no visibles');
            }
        }

        // Screenshot para verificación
        await page.screenshot({ path: 'test-crear-obra-fix.png' });
        console.log('📸 Screenshot guardado como test-crear-obra-fix.png');

    } catch (error) {
        console.error('❌ Error en test:', error.message);
        await page.screenshot({ path: 'error-crear-obra-fix.png' });
    } finally {
        await browser.close();
    }
})();
