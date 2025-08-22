import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        console.log('🔧 Probando funcionalidad de asignar/cambiar responsable de obra...');

        // Navegar a la página de login
        await page.goto('http://127.0.0.1:8003/login');
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar dashboard
        await page.waitForURL('**/dashboard');
        console.log('✅ Login exitoso');

        // Ir a obras
        await page.goto('http://localhost:8003/obras');
        await page.waitForTimeout(2000);

        // Hacer clic en la primera obra para ver detalles
        const primeraObra = page.locator('tbody tr').first();
        await primeraObra.click();
        await page.waitForTimeout(2000);

        console.log('📋 Visualizando detalles de obra...');

        // Buscar el botón de asignar/cambiar responsable
        const botonResponsable = page.locator('button:has-text("Asignar Responsable"), button:has-text("Cambiar Responsable")').first();

        if (await botonResponsable.isVisible()) {
            const textoBoton = await botonResponsable.textContent();
            console.log(`🎯 Botón encontrado: "${textoBoton}"`);

            // Hacer clic en el botón
            await botonResponsable.click();
            await page.waitForTimeout(1000);

            // Verificar que se abre el modal
            const modal = page.locator('#cambiar-encargado-modal');
            const modalVisible = await modal.isVisible();

            if (modalVisible) {
                console.log('✅ Modal abierto correctamente');

                // Verificar elementos del modal
                const titulo = await page.locator('#modal-encargado-title').textContent();
                console.log(`📝 Título del modal: "${titulo}"`);

                // Verificar select de responsables
                const selectResponsables = page.locator('select[name="encargado_id"]');
                const optionsCount = await selectResponsables.locator('option').count();
                console.log(`📋 Opciones de responsables disponibles: ${optionsCount}`);

                if (optionsCount > 1) {
                    console.log('✅ Hay responsables disponibles para asignar');

                    // Seleccionar un responsable (segunda opción, la primera es "Seleccionar...")
                    await selectResponsables.selectOption({ index: 1 });

                    // Agregar observaciones
                    await page.fill('textarea[name="observaciones"]', 'Test de cambio de responsable desde modal');

                    console.log('📝 Formulario llenado, probando envío...');

                    // Hacer clic en el botón de enviar
                    await page.click('button[type="submit"]');
                    await page.waitForTimeout(3000);

                    // Verificar si hay mensaje de éxito
                    const mensajeExito = page.locator('.bg-green-100, .alert-success, .text-green-600');
                    const hayExito = await mensajeExito.isVisible().catch(() => false);

                    if (hayExito) {
                        const mensaje = await mensajeExito.textContent();
                        console.log('✅ ÉXITO: ', mensaje);
                    } else {
                        console.log('⚠️  No se detectó mensaje de éxito visible');
                    }
                } else {
                    console.log('⚠️  No hay responsables disponibles en el select');
                }

                // Cerrar modal si está abierto
                const cerrarBtn = page.locator('button:has-text("Cancelar")');
                if (await cerrarBtn.isVisible()) {
                    await cerrarBtn.click();
                    console.log('🔄 Modal cerrado');
                }

            } else {
                console.log('❌ Modal no se abrió correctamente');
            }
        } else {
            console.log('❌ No se encontró botón de asignar/cambiar responsable');
        }

        // Screenshot para verificación
        await page.screenshot({ path: 'test-responsable-obra.png' });
        console.log('📸 Screenshot guardado como test-responsable-obra.png');

    } catch (error) {
        console.error('❌ Error en test:', error.message);
        await page.screenshot({ path: 'error-responsable-obra.png' });
    } finally {
        await browser.close();
    }
})();
