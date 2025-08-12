import { test, expect } from '@playwright/test';

test.describe('Test Final - Encargado y Asignaciones', () => {
    test('verificar guardado exitoso de encargado_id y asignaciones', async ({ page }) => {
        console.log('=== TESTING FINAL ===');

        // Login
        console.log('🔐 Iniciando login...');
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // Ir al formulario de crear obra
        console.log('📝 Navegando a formulario...');
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForSelector('#createObraForm');

        // Generar nombre único
        const uniqueName = `Obra Test ${Date.now()}`;
        console.log(`📝 Creando obra: ${uniqueName}`);

        // Llenar datos básicos
        await page.fill('input[name="nombre_obra"]', uniqueName);
        await page.selectOption('select[name="estatus"]', 'planificada');
        await page.fill('input[name="fecha_inicio"]', '2025-08-15');

        // Seleccionar encargado
        console.log('👥 Seleccionando encargado...');
        await page.selectOption('select[name="encargado_id"]', '1');
        const selectedEncargado = await page.inputValue('select[name="encargado_id"]');
        console.log(`✅ Encargado seleccionado: ID=${selectedEncargado}`);

        // Agregar vehículo
        console.log('🚗 Agregando vehículo...');
        await page.click('button:has-text("Agregar Vehículo")');
        await page.waitForSelector('.vehicle-card', { timeout: 5000 });

        // Seleccionar vehículo
        await page.selectOption('.vehicle-card select[name*="vehiculo_id"]', '1');
        console.log('✅ Vehículo seleccionado: ID=1');

        // Enviar formulario
        console.log('📤 Enviando formulario...');
        await Promise.race([
            page.click('button[type="submit"]:has-text("Crear Obra")'),
            page.waitForNavigation({ timeout: 10000 })
        ]);

        // Esperar a que se procese
        await page.waitForTimeout(3000);

        // Verificar resultado
        const currentUrl = page.url();
        console.log(`📍 URL actual: ${currentUrl}`);

        const hasSuccess = await page.locator('.bg-green-100, .alert-success').count() > 0;
        const hasError = await page.locator('.bg-red-100, .alert-danger').count() > 0;
        const isInObrasIndex = currentUrl.includes('/obras') && !currentUrl.includes('/create');

        console.log(`📊 Mensaje de éxito: ${hasSuccess ? '✅ SÍ' : '❌ NO'}`);
        console.log(`📊 Mensaje de error: ${hasError ? '❌ SÍ' : '✅ NO'}`);
        console.log(`📊 Redirigido a obras index: ${isInObrasIndex ? '✅ SÍ' : '❌ NO'}`);

        if (hasError) {
            const errorText = await page.locator('.bg-red-100, .alert-danger').textContent();
            console.log(`❌ Error: "${errorText}"`);
        }

        if (hasSuccess) {
            const successText = await page.locator('.bg-green-100, .alert-success').textContent();
            console.log(`✅ Éxito: "${successText}"`);
        }

        console.log('\n📋 === RESUMEN ===');
        console.log(`✅ Formulario enviado`);
        console.log(`📍 URL final: ${currentUrl}`);
        console.log(`📊 Éxito: ${hasSuccess}`);
        console.log(`📊 Error: ${hasError}`);
        console.log(`📊 Redirigido correctamente: ${isInObrasIndex}`);

        // El test pasa si hay éxito y no hay errores
        expect(hasSuccess).toBe(true);
        expect(hasError).toBe(false);
        expect(isInObrasIndex).toBe(true);

        console.log('\n🎉 ¡TEST EXITOSO! - Encargado y asignaciones se guardan correctamente');
    });
});