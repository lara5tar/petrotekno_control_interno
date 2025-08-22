import { chromium } from 'playwright';

async function testAsignacionCompleta() {
    console.log('🚀 Iniciando test de asignación completa de responsable...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 1000
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Login
        console.log('📋 1. Haciendo login...');
        await page.goto('http://127.0.0.1:8002/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');

        // 2. Ir a obra sin responsable (crear una nueva o usar una existente)
        console.log('📋 2. Navegando a obra...');
        await page.goto('http://127.0.0.1:8002/obras/1');
        await page.waitForLoadState('networkidle');

        // 3. Verificar estado inicial
        console.log('🔍 3. Verificando estado inicial...');
        const recuadroRojo = page.locator('.bg-red-50.border-red-200');
        const tieneRecuadroRojo = await recuadroRojo.isVisible();

        if (tieneRecuadroRojo) {
            console.log('✅ Obra sin responsable - Procediendo con asignación');

            // 4. Abrir modal
            console.log('🖱️ 4. Abriendo modal...');
            await page.click('#btn-cambiar-responsable');
            await page.waitForSelector('#cambiar-responsable-modal:not(.hidden)', { timeout: 3000 });

            // 5. Seleccionar responsable
            console.log('👤 5. Seleccionando responsable...');
            await page.selectOption('#responsable_id', { index: 1 });

            const responsableSeleccionado = await page.inputValue('#responsable_id');
            console.log(`✅ Responsable seleccionado ID: ${responsableSeleccionado}`);

            // 6. Agregar observaciones
            await page.fill('#observaciones_responsable', 'Asignación vía test automatizado');

            // 7. Enviar formulario
            console.log('💾 7. Enviando formulario...');
            await page.click('#submit-responsable-btn');
            await page.waitForURL('**/obras/*');

            // 8. Verificar éxito
            console.log('🔍 8. Verificando asignación...');
            await page.waitForTimeout(2000);

            const recuadroRojoFinal = await page.locator('.bg-red-50.border-red-200').isVisible();
            if (!recuadroRojoFinal) {
                console.log('✅ ¡ÉXITO! Responsable asignado correctamente');

                // Verificar que aparece la información del responsable
                const infoResponsable = page.locator('.space-y-4').first();
                if (await infoResponsable.isVisible()) {
                    console.log('✅ Información del responsable visible');
                }

            } else {
                console.log('❌ El recuadro rojo sigue visible');
            }

        } else {
            console.log('ℹ️ Esta obra ya tiene responsable asignado');

            // Probar cambio de responsable
            console.log('🔄 Probando cambio de responsable...');
            await page.click('#btn-cambiar-responsable');
            await page.waitForSelector('#cambiar-responsable-modal:not(.hidden)', { timeout: 3000 });

            const titulo = await page.locator('#modal-responsable-title').textContent();
            console.log(`✅ Modal de cambio abierto: ${titulo.trim()}`);

            // Cerrar modal
            await page.keyboard.press('Escape');
            console.log('✅ Modal cerrado');
        }

        console.log('🎉 Test de asignación completado exitosamente');

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-asignacion-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

testAsignacionCompleta().catch(console.error);
