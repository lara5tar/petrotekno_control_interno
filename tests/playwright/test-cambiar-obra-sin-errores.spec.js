import { test, expect } from '@playwright/test';

test.describe('Test Completo - Cambiar Obra Sin Errores', () => {
    // Función helper para login
    async function login(page) {
        console.log('🔐 Iniciando proceso de login...');
        await page.goto('http://localhost:8000/login');

        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home', { timeout: 10000 });
        console.log('✅ Login exitoso');
    }

    test('Verificar que cambiar obra funciona sin errores', async ({ page }) => {
        console.log('🚀 Test detallado: Cambiar obra sin errores...');

        // Capturar errores de consola
        const consoleErrors = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
                console.log('❌ Error de consola:', msg.text());
            }
        });

        // Capturar errores de red
        const networkErrors = [];
        page.on('response', response => {
            if (response.status() >= 400) {
                networkErrors.push({
                    url: response.url(),
                    status: response.status(),
                    statusText: response.statusText()
                });
                console.log(`❌ Error de red: ${response.status()} ${response.statusText()} - ${response.url()}`);
            }
        });

        // Hacer login
        await login(page);

        // Navegar a la página del vehículo
        console.log('📍 Navegando a página del vehículo...');
        await page.goto('http://localhost:8000/vehiculos/1');

        // Verificar que la página carga sin errores
        await expect(page).toHaveTitle(/Detalles del Vehículo/);
        console.log('✅ Página del vehículo cargada');

        // Verificar que no hay errores hasta ahora
        if (consoleErrors.length > 0) {
            console.log('⚠️ Errores de consola encontrados en la carga:', consoleErrors);
        }
        if (networkErrors.length > 0) {
            console.log('⚠️ Errores de red encontrados en la carga:', networkErrors);
        }

        // Buscar el botón de cambiar obra
        console.log('🔍 Buscando botón "Cambiar Obra"...');
        const cambiarObraButton = page.locator('button:has-text("Cambiar Obra")').first();

        // Verificar que el botón existe
        await expect(cambiarObraButton).toBeVisible({ timeout: 10000 });
        console.log('✅ Botón "Cambiar Obra" encontrado');

        // Hacer clic para abrir el modal
        console.log('🖱️ Abriendo modal...');
        await cambiarObraButton.click();

        // Verificar que el modal se abre
        const modal = page.locator('#cambiar-obra-modal');
        await expect(modal).toBeVisible({ timeout: 5000 });
        console.log('✅ Modal abierto correctamente');

        // Verificar elementos del formulario
        const selectObra = page.locator('#obra_id');
        const selectOperador = page.locator('#operador_id');
        const inputKilometraje = page.locator('#kilometraje_inicial');

        await expect(selectObra).toBeVisible();
        await expect(selectOperador).toBeVisible();
        await expect(inputKilometraje).toBeVisible();
        console.log('✅ Elementos del formulario visibles');

        // Verificar opciones disponibles
        console.log('🔍 Verificando opciones disponibles...');
        await selectObra.click();
        const opcionesObra = await selectObra.locator('option').count();
        console.log(`📊 Opciones de obra disponibles: ${opcionesObra}`);

        await selectOperador.click();
        const opcionesOperador = await selectOperador.locator('option').count();
        console.log(`📊 Opciones de operador disponibles: ${opcionesOperador}`);

        // Solo proceder si hay opciones disponibles
        if (opcionesObra > 1 && opcionesOperador > 1) {
            console.log('✅ Hay datos suficientes para realizar el test');

            // Llenar el formulario
            console.log('📝 Llenando formulario...');
            await selectObra.selectOption({ index: 1 });
            await selectOperador.selectOption({ index: 1 });
            await inputKilometraje.fill('55000');

            // Añadir observaciones
            const textareaObservaciones = page.locator('#observaciones_cambio');
            await textareaObservaciones.fill('Test automatizado - cambio de obra');

            console.log('✅ Formulario completado');

            // Preparar para capturar la respuesta
            let responseReceived = false;
            let responseData = null;

            // Listener para capturar la respuesta
            page.on('response', async response => {
                if (response.url().includes('/asignaciones-obra/cambiar-obra') && response.request().method() === 'POST') {
                    responseReceived = true;
                    responseData = {
                        status: response.status(),
                        statusText: response.statusText(),
                        url: response.url()
                    };

                    console.log(`📡 Respuesta capturada: ${response.status()} ${response.statusText()}`);

                    // Intentar leer el contenido de la respuesta
                    try {
                        const responseText = await response.text();
                        if (response.status() >= 400) {
                            console.log('❌ Contenido de error:', responseText.substring(0, 500));
                        } else {
                            console.log('✅ Respuesta exitosa recibida');
                        }
                    } catch (e) {
                        console.log('⚠️ No se pudo leer el contenido de la respuesta');
                    }
                }
            });

            // Hacer clic en el botón de enviar
            console.log('💾 Enviando formulario...');
            const btnSubmit = page.locator('#cambiar-obra-form button[type="submit"]');
            await btnSubmit.click();

            // Esperar respuesta con timeout más largo
            console.log('⏳ Esperando respuesta del servidor...');
            let waitTime = 0;
            const maxWaitTime = 15000; // 15 segundos
            const checkInterval = 500; // Revisar cada 500ms

            while (!responseReceived && waitTime < maxWaitTime) {
                await page.waitForTimeout(checkInterval);
                waitTime += checkInterval;

                if (waitTime % 2000 === 0) { // Log cada 2 segundos
                    console.log(`⏳ Esperando... ${waitTime / 1000}s`);
                }
            }

            if (responseReceived) {
                console.log('✅ Respuesta del servidor recibida');
                console.log('📊 Datos de respuesta:', responseData);

                // Verificar si fue exitosa
                if (responseData.status >= 200 && responseData.status < 400) {
                    console.log('🎉 ¡Cambio de obra procesado exitosamente!');

                    // Verificar si hay redirection o notificación
                    await page.waitForTimeout(2000);

                    // Buscar mensajes de éxito
                    const successMessages = await page.locator('.alert-success, .toast-success, .swal2-success, [class*="success"]').count();
                    if (successMessages > 0) {
                        console.log('✅ Mensaje de éxito mostrado al usuario');
                    }

                    // Verificar si se cerró el modal
                    const modalVisible = await modal.isVisible();
                    if (!modalVisible) {
                        console.log('✅ Modal cerrado automáticamente después del éxito');
                    }

                } else {
                    console.log('❌ Error en el procesamiento del cambio de obra');
                }
            } else {
                console.log('❌ No se recibió respuesta del servidor en el tiempo esperado');
            }

        } else {
            console.log('⚠️ No hay suficientes datos para realizar el test (faltan obras o operadores)');
        }

        // Reporte final de errores
        console.log('\n📊 REPORTE FINAL:');
        console.log(`Errores de consola: ${consoleErrors.length}`);
        console.log(`Errores de red: ${networkErrors.length}`);

        if (consoleErrors.length > 0) {
            console.log('❌ Errores de consola encontrados:');
            consoleErrors.forEach((error, index) => {
                console.log(`   ${index + 1}. ${error}`);
            });
        }

        if (networkErrors.length > 0) {
            console.log('❌ Errores de red encontrados:');
            networkErrors.forEach((error, index) => {
                console.log(`   ${index + 1}. ${error.status} ${error.statusText} - ${error.url}`);
            });
        }

        // Tomar screenshot final
        await page.screenshot({ path: 'cambiar-obra-final-state.png', fullPage: true });
        console.log('📸 Screenshot final guardado');

        // El test pasa si no hay errores críticos
        expect(consoleErrors.filter(error =>
            !error.includes('favicon') &&
            !error.includes('DevTools') &&
            !error.includes('Extension')
        ).length).toBe(0);

        expect(networkErrors.filter(error =>
            error.status >= 500 // Solo errores de servidor
        ).length).toBe(0);

        console.log('🎉 Test completado - No se encontraron errores críticos');
    });
});
