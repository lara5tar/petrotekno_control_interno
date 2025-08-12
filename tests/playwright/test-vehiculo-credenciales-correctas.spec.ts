import { test, expect } from '@playwright/test';

test.describe('Test Agregar Vehículo - Credenciales Correctas', () => {
    test('diagnosticar funcionalidad agregar vehículo con login correcto', async ({ page }) => {
        console.log('=== DIAGNÓSTICO CON CREDENCIALES CORRECTAS ===');

        // Escuchar errores de consola
        const consoleErrors = [];
        const consoleLogs = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
                console.log(`❌ CONSOLE ERROR: ${msg.text()}`);
            } else if (msg.type() === 'log') {
                consoleLogs.push(msg.text());
            }
        });

        // Login con credenciales correctas
        console.log('🔐 Iniciando login con admin@petrotekno.com...');
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar redirección
        await page.waitForTimeout(3000);
        const currentUrl = page.url();
        console.log(`📍 URL después del login: ${currentUrl}`);

        // Ir al formulario de crear obra
        console.log('📝 Navegando a formulario de obras...');
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForTimeout(3000);

        // Verificar si llegamos al formulario
        const formExists = await page.locator('#createObraForm').count() > 0;
        console.log(`📝 Formulario existe: ${formExists ? '✅ SÍ' : '❌ NO'}`);

        if (!formExists) {
            const pageTitle = await page.title();
            const hasError = await page.locator('.bg-red-100, .alert-danger').count() > 0;
            console.log(`📄 Título de página: ${pageTitle}`);
            console.log(`❌ Hay errores en página: ${hasError ? '✅ SÍ' : '❌ NO'}`);

            if (hasError) {
                const errorText = await page.locator('.bg-red-100, .alert-danger').first().textContent();
                console.log(`❌ Texto del error: ${errorText}`);
            }
            return;
        }

        console.log('✅ ¡Acceso al formulario exitoso!');

        // Examinar el código JavaScript presente
        console.log('🔍 Examinando JavaScript en la página...');

        const jsAnalysis = await page.evaluate(() => {
            // Buscar scripts inline
            const inlineScripts = Array.from(document.querySelectorAll('script:not([src])')).map(script => script.innerHTML);

            // Buscar función obraFormController
            const hasObraFormController = inlineScripts.some(script => script.includes('obraFormController'));

            // Buscar función addVehicle
            const hasAddVehicle = inlineScripts.some(script => script.includes('addVehicle'));

            // Buscar elementos relacionados
            const elements = {
                xDataElement: document.querySelector('[x-data="obraFormController()"]') !== null,
                addButton: document.querySelector('button[\\@click="addVehicle()"]') !== null,
                vehicleTemplate: document.querySelector('#vehicleTemplate') !== null,
                vehiculosContainer: document.querySelector('#vehiculosContainer') !== null,
                alpineLoaded: typeof window.Alpine !== 'undefined'
            };

            return {
                hasObraFormController,
                hasAddVehicle,
                elements,
                scriptCount: inlineScripts.length,
                firstScriptPreview: inlineScripts[0] ? inlineScripts[0].substring(0, 200) + '...' : 'No scripts'
            };
        });

        console.log(`🔍 Función obraFormController encontrada: ${jsAnalysis.hasObraFormController ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🔍 Función addVehicle encontrada: ${jsAnalysis.hasAddVehicle ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🔍 Alpine.js cargado: ${jsAnalysis.elements.alpineLoaded ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🔍 Elemento x-data: ${jsAnalysis.elements.xDataElement ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🔍 Botón agregar: ${jsAnalysis.elements.addButton ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🔍 Template vehículo: ${jsAnalysis.elements.vehicleTemplate ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🔍 Container vehículos: ${jsAnalysis.elements.vehiculosContainer ? '✅ SÍ' : '❌ NO'}`);
        console.log(`📊 Scripts en página: ${jsAnalysis.scriptCount}`);

        // Si hay problemas con los elementos, buscarlos alternativamente
        if (!jsAnalysis.elements.addButton || !jsAnalysis.elements.vehicleTemplate) {
            console.log('🔍 Buscando elementos alternativos...');

            const alternativeElements = await page.evaluate(() => {
                return {
                    anyAddButton: document.querySelectorAll('button').length,
                    buttonsWithText: Array.from(document.querySelectorAll('button')).map(btn => btn.textContent?.trim()).filter(text => text?.includes('Agregar') || text?.includes('Vehículo')),
                    templatesFound: document.querySelectorAll('template, [id*="template"], [id*="Template"]').length,
                    containersFound: document.querySelectorAll('[id*="container"], [id*="Container"]').length
                };
            });

            console.log(`🔍 Botones totales: ${alternativeElements.anyAddButton}`);
            console.log(`🔍 Botones con texto relacionado: ${JSON.stringify(alternativeElements.buttonsWithText)}`);
            console.log(`🔍 Templates encontrados: ${alternativeElements.templatesFound}`);
            console.log(`🔍 Containers encontrados: ${alternativeElements.containersFound}`);
        }

        // Intentar la funcionalidad si los elementos básicos existen
        if (jsAnalysis.elements.xDataElement) {
            console.log('🔄 Intentando funcionalidad de agregar vehículo...');

            // Contar vehículos antes
            const vehiclesBefore = await page.locator('.vehicle-card').count();
            console.log(`🚗 Vehículos antes: ${vehiclesBefore}`);

            // Buscar cualquier botón que pueda agregar vehículos
            const addButtons = await page.locator('button').all();
            let buttonClicked = false;

            for (let button of addButtons) {
                const buttonText = await button.textContent();
                if (buttonText && (buttonText.includes('Agregar') && buttonText.includes('Vehículo'))) {
                    console.log(`🔄 Haciendo click en: "${buttonText.trim()}"`);
                    await button.click();
                    buttonClicked = true;
                    break;
                }
            }

            if (!buttonClicked) {
                console.log('❌ No se encontró botón para hacer click');

                // Intentar ejecutar función directamente
                console.log('🔄 Intentando ejecutar addVehicle() directamente...');
                await page.evaluate(() => {
                    try {
                        const element = document.querySelector('[x-data="obraFormController()"]');
                        if (element && element._x_dataStack && element._x_dataStack[0]) {
                            if (typeof element._x_dataStack[0].addVehicle === 'function') {
                                element._x_dataStack[0].addVehicle();
                                console.log('✅ Función addVehicle ejecutada');
                            } else {
                                console.log('❌ Función addVehicle no encontrada en el stack');
                            }
                        } else {
                            console.log('❌ Elemento Alpine no inicializado');
                        }
                    } catch (error) {
                        console.log('❌ Error ejecutando addVehicle:', error.message);
                    }
                });
            }

            await page.waitForTimeout(2000);

            const vehiclesAfter = await page.locator('.vehicle-card').count();
            console.log(`🚗 Vehículos después: ${vehiclesAfter}`);

            if (vehiclesAfter > vehiclesBefore) {
                console.log('🎉 ¡ÉXITO! Se agregó un vehículo');

                // Verificar dropdown de vehículos
                const vehicleOptions = await page.locator('.vehicle-card select option').count();
                console.log(`🚗 Opciones en dropdown: ${vehicleOptions}`);

            } else {
                console.log('❌ No se agregó ningún vehículo');
            }
        }

        console.log('\n📋 === RESUMEN FINAL ===');
        console.log(`📊 Acceso al formulario: ${formExists ? '✅ EXITOSO' : '❌ FALLIDO'}`);
        console.log(`📊 JavaScript cargado: ${jsAnalysis.hasObraFormController && jsAnalysis.hasAddVehicle ? '✅ COMPLETO' : '❌ INCOMPLETO'}`);
        console.log(`📊 Alpine.js funcional: ${jsAnalysis.elements.alpineLoaded && jsAnalysis.elements.xDataElement ? '✅ SÍ' : '❌ NO'}`);
        console.log(`📊 Elementos necesarios: ${jsAnalysis.elements.addButton && jsAnalysis.elements.vehicleTemplate ? '✅ PRESENTES' : '❌ FALTANTES'}`);
        console.log(`📊 Errores de consola: ${consoleErrors.length}`);

        if (consoleErrors.length > 0) {
            console.log('\n❌ ERRORES DETECTADOS:');
            consoleErrors.forEach((error, index) => {
                console.log(`${index + 1}. ${error}`);
            });
        }
    });
});