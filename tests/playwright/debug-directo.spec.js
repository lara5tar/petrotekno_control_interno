import { test, expect } from '@playwright/test';

test('Test directo cambio operador vehículo 2', async ({ page }) => {
    // Capturar todo tipo de errores
    const errorLogs = [];

    page.on('response', async response => {
        if (response.url().includes('cambiar-operador')) {
            const responseText = await response.text().catch(() => '');
            console.log(`🎯 RESPUESTA CAMBIAR OPERADOR:`);
            console.log(`Status: ${response.status()}`);
            console.log(`Body: ${responseText}`);

            errorLogs.push({
                status: response.status(),
                body: responseText,
                headers: await response.allHeaders()
            });
        }
    });

    page.on('console', msg => {
        if (msg.type() === 'error') {
            console.log(`🚨 JS ERROR: ${msg.text()}`);
            errorLogs.push({ type: 'js_error', message: msg.text() });
        }
    });

    try {
        // 1. Login directo
        console.log('1. Haciendo login...');
        await page.goto('http://127.0.0.1:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // 2. Ir directamente al vehículo 2 (que ya probaste)
        console.log('2. Navegando al vehículo 2...');
        await page.goto('http://127.0.0.1:8000/vehiculos/2');
        await page.waitForTimeout(2000);

        // 3. Tomar screenshot inicial
        await page.screenshot({ path: 'vehiculo-2-inicial.png', fullPage: true });

        // 4. Buscar botón cambiar operador
        console.log('3. Buscando botón cambiar operador...');
        const btnCambiar = page.locator('button:has-text("Cambiar Operador"), button:has-text("Asignar Operador")');

        const btnExists = await btnCambiar.count() > 0;
        console.log(`Botón existe: ${btnExists}`);

        if (btnExists) {
            // 5. Hacer clic en cambiar operador
            console.log('4. Haciendo clic en cambiar operador...');
            await btnCambiar.first().click();
            await page.waitForTimeout(1000);

            // 6. Screenshot del modal
            await page.screenshot({ path: 'modal-operador.png', fullPage: true });

            // 7. Verificar operadores disponibles
            console.log('5. Verificando operadores...');
            const selectOperador = page.locator('#personal_id');
            const opciones = await selectOperador.locator('option').count();
            console.log(`Operadores disponibles: ${opciones - 1}`); // -1 por "Seleccione"

            if (opciones > 1) {
                // 8. Seleccionar primer operador
                console.log('6. Seleccionando operador...');
                await selectOperador.selectOption({ index: 1 });
                await page.waitForTimeout(500);

                // 9. GUARDAR - aquí es donde ocurre el error
                console.log('7. 🎯 HACIENDO CLIC EN GUARDAR...');

                // Preparar interceptor para capturar la respuesta
                const responsePromise = page.waitForResponse(
                    response => response.url().includes('cambiar-operador'),
                    { timeout: 5000 }
                ).catch(err => {
                    console.log('⚠️ No se capturó respuesta del servidor');
                    return null;
                });

                // Hacer clic en guardar
                await page.click('#btnGuardarOperador');

                // Esperar respuesta
                const response = await responsePromise;

                if (response) {
                    const responseText = await response.text();
                    console.log(`📊 RESPUESTA COMPLETA:`);
                    console.log(`Status: ${response.status()}`);
                    console.log(`Body: ${responseText}`);
                }

                // 10. Esperar un momento para que aparezcan mensajes
                console.log('8. Esperando mensajes...');
                await page.waitForTimeout(3000);

                // 11. Buscar TODOS los elementos que podrían mostrar errores
                console.log('9. 🔍 BUSCANDO ELEMENTOS DE ERROR...');

                // Buscar elementos con clases comunes de error
                const errorSelectors = [
                    '.alert',
                    '.error',
                    '.message',
                    '.notification',
                    '.toast',
                    '[role="alert"]',
                    '.text-red',
                    '.bg-red',
                    '.border-red',
                    '[style*="color: red"]',
                    '[style*="background: red"]',
                    '[style*="background-color: red"]',
                    '.alert-danger',
                    '.alert-error'
                ];

                for (const selector of errorSelectors) {
                    const elements = await page.locator(selector).all();
                    if (elements.length > 0) {
                        console.log(`\n🔴 Encontrados ${elements.length} elementos con selector: ${selector}`);
                        for (let i = 0; i < elements.length; i++) {
                            const element = elements[i];
                            const text = await element.textContent().catch(() => '');
                            const isVisible = await element.isVisible().catch(() => false);
                            const classes = await element.getAttribute('class').catch(() => '');
                            const style = await element.getAttribute('style').catch(() => '');

                            console.log(`  Elemento ${i + 1}:`);
                            console.log(`    Texto: "${text}"`);
                            console.log(`    Visible: ${isVisible}`);
                            console.log(`    Clases: ${classes}`);
                            console.log(`    Estilo: ${style}`);
                        }
                    }
                }

                // 12. Screenshot final
                await page.screenshot({ path: 'despues-guardar.png', fullPage: true });

                // 13. Inspeccionar el DOM completo buscando elementos vacíos pero rojos
                console.log('\n🔍 BUSCANDO ELEMENTOS ROJOS VACÍOS...');
                const redEmptyElements = await page.evaluate(() => {
                    const allElements = document.querySelectorAll('*');
                    const redElements = [];

                    for (let element of allElements) {
                        const computedStyle = window.getComputedStyle(element);
                        const hasRedColor = computedStyle.color.includes('rgb(255, 0, 0)') ||
                            computedStyle.color.includes('red') ||
                            computedStyle.backgroundColor.includes('rgb(255, 0, 0)') ||
                            computedStyle.backgroundColor.includes('red') ||
                            computedStyle.borderColor.includes('rgb(255, 0, 0)') ||
                            computedStyle.borderColor.includes('red');

                        if (hasRedColor) {
                            redElements.push({
                                tagName: element.tagName,
                                className: element.className,
                                id: element.id,
                                textContent: element.textContent.trim(),
                                innerHTML: element.innerHTML,
                                style: element.getAttribute('style') || '',
                                computedColor: computedStyle.color,
                                computedBgColor: computedStyle.backgroundColor,
                                computedBorderColor: computedStyle.borderColor,
                                isVisible: element.offsetParent !== null
                            });
                        }
                    }
                    return redElements;
                });

                console.log(`Elementos rojos encontrados: ${redEmptyElements.length}`);
                redEmptyElements.forEach((element, index) => {
                    console.log(`\n🔴 Elemento rojo ${index + 1}:`);
                    console.log(`  Tag: ${element.tagName}`);
                    console.log(`  ID: ${element.id}`);
                    console.log(`  Clases: ${element.className}`);
                    console.log(`  Texto: "${element.textContent}"`);
                    console.log(`  HTML: ${element.innerHTML.substring(0, 200)}...`);
                    console.log(`  Estilo: ${element.style}`);
                    console.log(`  Color computado: ${element.computedColor}`);
                    console.log(`  Fondo computado: ${element.computedBgColor}`);
                    console.log(`  Visible: ${element.isVisible}`);
                });

            } else {
                console.log('❌ No hay operadores disponibles');
            }
        } else {
            console.log('❌ No se encontró botón de cambiar operador');
        }

        // Mostrar resumen de logs de error
        console.log('\n📋 RESUMEN DE ERRORES CAPTURADOS:');
        errorLogs.forEach((log, index) => {
            console.log(`Error ${index + 1}:`, log);
        });

    } catch (error) {
        console.error('💥 Error durante el test:', error);
        await page.screenshot({ path: 'error-final.png', fullPage: true });
    }
});
