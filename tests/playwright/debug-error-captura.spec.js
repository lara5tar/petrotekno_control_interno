import { test, expect } from '@playwright/test';

test('Capturar error específico al cambiar operador', async ({ page }) => {
    // Interceptar todas las requests y responses
    const networkLogs = [];

    page.on('request', request => {
        networkLogs.push({
            type: 'REQUEST',
            method: request.method(),
            url: request.url(),
            headers: request.headers(),
            postData: request.postData()
        });
        console.log(`🔵 REQ: ${request.method()} ${request.url()}`);
    });

    page.on('response', async response => {
        const responseText = await response.text().catch(() => '');
        networkLogs.push({
            type: 'RESPONSE',
            status: response.status(),
            url: response.url(),
            headers: await response.allHeaders(),
            body: responseText
        });
        console.log(`🟢 RES: ${response.status()} ${response.url()}`);

        // Log respuestas con errores
        if (response.status() >= 400) {
            console.log(`❌ ERROR RESPONSE: ${response.status()} ${response.url()}`);
            console.log(`Error body: ${responseText}`);
        }
    });

    // Interceptar errores de consola
    page.on('console', msg => {
        console.log(`📝 CONSOLE ${msg.type()}: ${msg.text()}`);
    });

    // Interceptar errores de página
    page.on('pageerror', error => {
        console.log(`🚨 PAGE ERROR: ${error.message}`);
    });

    try {
        console.log('🚀 Iniciando test de cambio de operador...');

        // 1. Ir al login
        await page.goto('http://127.0.0.1:8000/login');
        await page.screenshot({ path: 'step1-login-page.png', fullPage: true });

        // 2. Login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // 3. Esperar y verificar login
        await page.waitForTimeout(2000);
        await page.screenshot({ path: 'step2-after-login.png', fullPage: true });

        // 4. Ir a vehículos
        await page.goto('http://127.0.0.1:8000/vehiculos');
        await page.waitForTimeout(1000);
        await page.screenshot({ path: 'step3-vehiculos-list.png', fullPage: true });

        // 5. Hacer clic en un vehículo específico (el que probaste: vehículo 2)
        await page.goto('http://127.0.0.1:8000/vehiculos/2');
        await page.waitForTimeout(2000);
        await page.screenshot({ path: 'step4-vehiculo-details.png', fullPage: true });

        // 6. Buscar y hacer clic en el botón de cambiar operador
        console.log('🔍 Buscando botón de cambiar operador...');

        const cambiarBtn = page.locator('button:has-text("Cambiar Operador"), button:has-text("Asignar Operador")');
        const btnCount = await cambiarBtn.count();
        console.log(`Botones encontrados: ${btnCount}`);

        if (btnCount > 0) {
            await cambiarBtn.first().click();
            console.log('✅ Botón de cambiar operador clickeado');
            await page.waitForTimeout(1000);
            await page.screenshot({ path: 'step5-modal-opened.png', fullPage: true });

            // 7. Seleccionar un operador
            const operadorSelect = page.locator('#personal_id');
            const options = await operadorSelect.locator('option').count();
            console.log(`Opciones de operadores disponibles: ${options}`);

            if (options > 1) {
                await operadorSelect.selectOption({ index: 1 });
                console.log('✅ Operador seleccionado');
                await page.waitForTimeout(500);
                await page.screenshot({ path: 'step6-operador-selected.png', fullPage: true });

                // 8. Hacer clic en guardar Y capturar todo lo que pase
                console.log('🎯 Haciendo clic en guardar...');

                // Interceptar específicamente la respuesta del cambio de operador
                const responsePromise = page.waitForResponse(
                    response => response.url().includes('cambiar-operador'),
                    { timeout: 10000 }
                );

                await page.click('#btnGuardarOperador');

                try {
                    const response = await responsePromise;
                    const responseText = await response.text();

                    console.log('📊 RESPUESTA DEL SERVIDOR:');
                    console.log(`Status: ${response.status()}`);
                    console.log(`Headers:`, await response.allHeaders());
                    console.log(`Body: ${responseText}`);

                    // 9. Esperar un momento y capturar el estado final
                    await page.waitForTimeout(3000);
                    await page.screenshot({ path: 'step7-after-save.png', fullPage: true });

                    // 10. Buscar específicamente mensajes de error
                    console.log('🔍 Buscando mensajes de error...');

                    // Buscar alertas/mensajes
                    const alerts = await page.locator('.alert, .error, .message, .notification, [role="alert"]').all();
                    console.log(`Elementos de alerta encontrados: ${alerts.length}`);

                    for (let i = 0; i < alerts.length; i++) {
                        const alert = alerts[i];
                        const text = await alert.textContent();
                        const classes = await alert.getAttribute('class');
                        const style = await alert.getAttribute('style');
                        const isVisible = await alert.isVisible();

                        console.log(`🚨 Alerta ${i + 1}:`);
                        console.log(`  Texto: "${text}"`);
                        console.log(`  Clases: ${classes}`);
                        console.log(`  Estilo: ${style}`);
                        console.log(`  Visible: ${isVisible}`);
                    }

                    // Buscar elementos rojos específicamente
                    const redElements = await page.locator('[style*="color: red"], [style*="background: red"], [style*="background-color: red"], .text-red, .bg-red').all();
                    console.log(`Elementos rojos encontrados: ${redElements.length}`);

                    for (let i = 0; i < redElements.length; i++) {
                        const element = redElements[i];
                        const text = await element.textContent();
                        const classes = await element.getAttribute('class');
                        const style = await element.getAttribute('style');

                        console.log(`🔴 Elemento rojo ${i + 1}:`);
                        console.log(`  Texto: "${text}"`);
                        console.log(`  Clases: ${classes}`);
                        console.log(`  Estilo: ${style}`);
                    }

                } catch (timeoutError) {
                    console.log('⏰ Timeout esperando respuesta del servidor');
                    console.log('Error:', timeoutError.message);
                }

            } else {
                console.log('❌ No hay operadores disponibles');
            }

        } else {
            console.log('❌ No se encontró botón de cambiar operador');
        }

        // 11. Dump de todos los logs de red
        console.log('📋 RESUMEN DE REQUESTS/RESPONSES:');
        networkLogs.forEach((log, index) => {
            if (log.type === 'RESPONSE' && log.url.includes('cambiar-operador')) {
                console.log(`\n🎯 LOG ${index + 1} - CAMBIAR OPERADOR RESPONSE:`);
                console.log(`Status: ${log.status}`);
                console.log(`URL: ${log.url}`);
                console.log(`Body: ${log.body}`);
            }
        });

    } catch (error) {
        console.error('💥 Error durante el test:', error);
        await page.screenshot({ path: 'error-final.png', fullPage: true });
        throw error;
    }
});
