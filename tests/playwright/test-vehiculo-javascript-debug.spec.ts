import { test, expect } from '@playwright/test';

test.describe('Test Agregar Vehículo - Diagnóstico JavaScript', () => {
    test('diagnosticar problema JavaScript sin login', async ({ page }) => {
        console.log('=== DIAGNÓSTICO JAVASCRIPT AGREGAR VEHÍCULO ===');

        // Escuchar errores de consola
        const consoleErrors = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
                console.log(`❌ CONSOLE ERROR: ${msg.text()}`);
            }
        });

        // Intentar acceder directo al formulario
        console.log('📝 Accediendo directo al formulario...');
        await page.goto('http://localhost:8000/obras/create');

        // Esperar un poco para ver qué pasa
        await page.waitForTimeout(3000);

        // Verificar si estamos en login o en el formulario
        const isLoginPage = await page.locator('input[name="email"]').count() > 0;
        const isFormPage = await page.locator('#createObraForm').count() > 0;

        console.log(`🔍 ¿Está en página de login?: ${isLoginPage ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🔍 ¿Está en formulario?: ${isFormPage ? '✅ SÍ' : '❌ NO'}`);

        if (isLoginPage) {
            console.log('🔐 Necesita login, intentando...');
            await page.fill('input[name="email"]', 'admin@admin.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(3000);

            // Intentar ir al formulario nuevamente
            await page.goto('http://localhost:8000/obras/create');
            await page.waitForTimeout(3000);
        }

        // Verificar si llegamos al formulario
        const formExists = await page.locator('#createObraForm').count() > 0;
        console.log(`📝 Formulario existe: ${formExists ? '✅ SÍ' : '❌ NO'}`);

        if (!formExists) {
            console.log('❌ No se puede acceder al formulario');
            const currentUrl = page.url();
            const pageContent = await page.content();
            console.log(`📍 URL actual: ${currentUrl}`);
            console.log(`📄 Contenido incluye "obras": ${pageContent.includes('obras') ? '✅ SÍ' : '❌ NO'}`);
            console.log(`📄 Contenido incluye "error": ${pageContent.includes('error') ? '✅ SÍ' : '❌ NO'}`);
            return;
        }

        // Diagnosticar Alpine.js
        console.log('🔍 Diagnosticando Alpine.js...');

        const alpineStatus = await page.evaluate(() => {
            return {
                alpineExists: typeof window.Alpine !== 'undefined',
                scriptTags: document.querySelectorAll('script[src*="alpine"]').length,
                xDataElements: document.querySelectorAll('[x-data]').length,
                obraFormController: document.querySelector('[x-data="obraFormController()"]') !== null
            };
        });

        console.log(`🔍 Alpine existe: ${alpineStatus.alpineExists ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🔍 Scripts Alpine: ${alpineStatus.scriptTags}`);
        console.log(`🔍 Elementos x-data: ${alpineStatus.xDataElements}`);
        console.log(`🔍 obraFormController: ${alpineStatus.obraFormController ? '✅ SÍ' : '❌ NO'}`);

        // Diagnosticar botón agregar vehículo
        console.log('🔍 Diagnosticando botón...');

        const buttonStatus = await page.evaluate(() => {
            const button = document.querySelector('button[\\@click="addVehicle()"]') ||
                document.querySelector('button:has-text("Agregar Vehículo")');
            return {
                exists: button !== null,
                hasClickHandler: button ? button.getAttribute('@click') || button.getAttribute('onclick') : null,
                isVisible: button ? !button.hidden && button.offsetParent !== null : false,
                isEnabled: button ? !button.disabled : false
            };
        });

        console.log(`🚗 Botón existe: ${buttonStatus.exists ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🚗 Click handler: ${buttonStatus.hasClickHandler || 'NINGUNO'}`);
        console.log(`🚗 Visible: ${buttonStatus.isVisible ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🚗 Habilitado: ${buttonStatus.isEnabled ? '✅ SÍ' : '❌ NO'}`);

        // Diagnosticar template y container
        console.log('🔍 Diagnosticando template y container...');

        const templateStatus = await page.evaluate(() => {
            return {
                templateExists: document.querySelector('#vehicleTemplate') !== null,
                containerExists: document.querySelector('#vehiculosContainer') !== null,
                templateContent: document.querySelector('#vehicleTemplate') ?
                    document.querySelector('#vehicleTemplate').innerHTML.length > 0 : false
            };
        });

        console.log(`📋 Template existe: ${templateStatus.templateExists ? '✅ SÍ' : '❌ NO'}`);
        console.log(`📦 Container existe: ${templateStatus.containerExists ? '✅ SÍ' : '❌ NO'}`);
        console.log(`📋 Template tiene contenido: ${templateStatus.templateContent ? '✅ SÍ' : '❌ NO'}`);

        // Si todo existe, intentar simular click
        if (buttonStatus.exists && templateStatus.templateExists && templateStatus.containerExists) {
            console.log('🔄 Intentando simular click...');

            const vehiclesBefore = await page.locator('.vehicle-card').count();
            console.log(`🚗 Vehículos antes: ${vehiclesBefore}`);

            // Intentar click directo
            await page.click('button:has-text("Agregar Vehículo")');
            await page.waitForTimeout(1000);

            const vehiclesAfter = await page.locator('.vehicle-card').count();
            console.log(`🚗 Vehículos después: ${vehiclesAfter}`);

            if (vehiclesAfter > vehiclesBefore) {
                console.log('🎉 ¡FUNCIONA! El botón agregó un vehículo');
            } else {
                console.log('❌ El click no agregó vehículos');

                // Intentar ejecutar función directamente
                console.log('🔄 Intentando ejecutar función directamente...');
                await page.evaluate(() => {
                    // Buscar la función en el contexto global
                    if (typeof addVehicle === 'function') {
                        addVehicle();
                    } else if (window.Alpine) {
                        // Buscar en el componente Alpine
                        const element = document.querySelector('[x-data="obraFormController()"]');
                        if (element && element._x_dataStack && element._x_dataStack[0]) {
                            if (typeof element._x_dataStack[0].addVehicle === 'function') {
                                element._x_dataStack[0].addVehicle();
                            }
                        }
                    }
                });

                await page.waitForTimeout(1000);
                const vehiclesAfterDirect = await page.locator('.vehicle-card').count();
                console.log(`🚗 Vehículos después de ejecución directa: ${vehiclesAfterDirect}`);
            }
        }

        console.log('\n📋 === RESUMEN DIAGNÓSTICO ===');
        console.log(`📊 Errores de consola: ${consoleErrors.length}`);
        console.log(`📊 Alpine cargado: ${alpineStatus.alpineExists}`);
        console.log(`📊 Componente existe: ${alpineStatus.obraFormController}`);
        console.log(`📊 Botón existe: ${buttonStatus.exists}`);
        console.log(`📊 Template existe: ${templateStatus.templateExists}`);
        console.log(`📊 Container existe: ${templateStatus.containerExists}`);

        if (consoleErrors.length > 0) {
            console.log('\n❌ ERRORES ENCONTRADOS:');
            consoleErrors.forEach((error, index) => {
                console.log(`${index + 1}. ${error}`);
            });
        }
    });
});