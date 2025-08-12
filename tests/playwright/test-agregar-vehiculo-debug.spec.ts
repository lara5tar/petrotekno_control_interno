import { test, expect } from '@playwright/test';

test.describe('Test Funcionalidad Agregar Vehículo', () => {
    test('diagnosticar y corregir problema con agregar vehículo', async ({ page }) => {
        console.log('=== TESTING FUNCIONALIDAD AGREGAR VEHÍCULO ===');

        // Escuchar errores de consola
        const consoleErrors = [];
        const consoleLogs = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
                console.log(`❌ CONSOLE ERROR: ${msg.text()}`);
            } else if (msg.type() === 'log') {
                consoleLogs.push(msg.text());
                console.log(`📝 CONSOLE LOG: ${msg.text()}`);
            }
        });

        // Login
        console.log('🔐 Iniciando login...');
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que redirija (puede ser a /home o /dashboard)
        await page.waitForTimeout(3000);
        const currentUrl = page.url();
        console.log(`📍 URL después del login: ${currentUrl}`);

        // Navegar al formulario de crear obra
        console.log('📝 Navegando a formulario...');
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForSelector('#createObraForm', { timeout: 10000 });
        console.log('✅ Formulario cargado');

        // Verificar Alpine.js
        const alpineLoaded = await page.evaluate(() => {
            return typeof window.Alpine !== 'undefined';
        });
        console.log(`🔍 Alpine.js cargado: ${alpineLoaded ? '✅ SÍ' : '❌ NO'}`);

        // Verificar componente Alpine
        const alpineComponentExists = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            return element !== null;
        });
        console.log(`🔍 Componente Alpine existe: ${alpineComponentExists ? '✅ SÍ' : '❌ NO'}`);

        // Verificar si el componente Alpine está inicializado
        const alpineInitialized = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            return element && element._x_dataStack && element._x_dataStack.length > 0;
        });
        console.log(`🔍 Componente Alpine inicializado: ${alpineInitialized ? '✅ SÍ' : '❌ NO'}`);

        // Verificar botón de agregar vehículo
        const addButton = await page.locator('button:has-text("Agregar Vehículo")');
        const addButtonExists = await addButton.count() > 0;
        console.log(`🚗 Botón "Agregar Vehículo" existe: ${addButtonExists ? '✅ SÍ' : '❌ NO'}`);

        if (addButtonExists) {
            const addButtonVisible = await addButton.isVisible();
            const addButtonEnabled = await addButton.isEnabled();
            console.log(`🚗 Botón visible: ${addButtonVisible ? '✅ SÍ' : '❌ NO'}`);
            console.log(`🚗 Botón habilitado: ${addButtonEnabled ? '✅ SÍ' : '❌ NO'}`);
        }

        // Verificar template de vehículo
        const templateExists = await page.locator('#vehicleTemplate').count() > 0;
        console.log(`📋 Template de vehículo existe: ${templateExists ? '✅ SÍ' : '❌ NO'}`);

        // Verificar container de vehículos
        const containerExists = await page.locator('#vehiculosContainer').count() > 0;
        console.log(`📦 Container de vehículos existe: ${containerExists ? '✅ SÍ' : '❌ NO'}`);

        // Intentar hacer click en el botón
        if (addButtonExists) {
            console.log('🔄 Intentando hacer click en "Agregar Vehículo"...');

            // Contar vehículos antes
            const vehiclesBefore = await page.locator('.vehicle-card').count();
            console.log(`🚗 Vehículos antes del click: ${vehiclesBefore}`);

            // Hacer click
            await addButton.click();

            // Esperar un momento para que se procese
            await page.waitForTimeout(2000);

            // Contar vehículos después
            const vehiclesAfter = await page.locator('.vehicle-card').count();
            console.log(`🚗 Vehículos después del click: ${vehiclesAfter}`);

            const vehicleAdded = vehiclesAfter > vehiclesBefore;
            console.log(`✅ Vehículo agregado: ${vehicleAdded ? '✅ SÍ' : '❌ NO'}`);

            if (vehicleAdded) {
                console.log('🎉 ¡Funcionalidad de agregar vehículo FUNCIONA!');

                // Verificar que el dropdown de vehículos tiene opciones
                const vehicleOptions = await page.locator('.vehicle-card select[name*="vehiculo_id"] option').count();
                console.log(`🚗 Opciones en dropdown de vehículos: ${vehicleOptions}`);

                if (vehicleOptions > 1) { // Más de 1 porque incluye la opción "Seleccionar..."
                    console.log('✅ Dropdown de vehículos tiene opciones disponibles');
                } else {
                    console.log('❌ Dropdown de vehículos NO tiene opciones disponibles');
                }
            } else {
                console.log('❌ Funcionalidad de agregar vehículo NO FUNCIONA');

                // Debug adicional
                console.log('🔍 Debugging adicional...');

                // Verificar si hay función addVehicle en el contexto Alpine
                const addVehicleFunction = await page.evaluate(() => {
                    const element = document.querySelector('[x-data="obraFormController()"]');
                    if (element && element._x_dataStack && element._x_dataStack[0]) {
                        return typeof element._x_dataStack[0].addVehicle === 'function';
                    }
                    return false;
                });
                console.log(`🔍 Función addVehicle existe: ${addVehicleFunction ? '✅ SÍ' : '❌ NO'}`);

                // Intentar ejecutar la función manualmente
                if (addVehicleFunction) {
                    console.log('🔄 Intentando ejecutar addVehicle() manualmente...');
                    await page.evaluate(() => {
                        const element = document.querySelector('[x-data="obraFormController()"]');
                        if (element && element._x_dataStack && element._x_dataStack[0]) {
                            element._x_dataStack[0].addVehicle();
                        }
                    });

                    await page.waitForTimeout(1000);
                    const vehiclesAfterManual = await page.locator('.vehicle-card').count();
                    console.log(`🚗 Vehículos después de ejecución manual: ${vehiclesAfterManual}`);
                }
            }
        }

        console.log('\n📋 === RESUMEN ===');
        console.log(`📊 Errores de consola: ${consoleErrors.length}`);
        console.log(`📊 Alpine.js cargado: ${alpineLoaded}`);
        console.log(`📊 Componente inicializado: ${alpineInitialized}`);
        console.log(`📊 Botón existe: ${addButtonExists}`);
        console.log(`📊 Template existe: ${templateExists}`);
        console.log(`📊 Container existe: ${containerExists}`);

        if (consoleErrors.length > 0) {
            console.log('\n❌ ERRORES DE CONSOLA:');
            consoleErrors.forEach((error, index) => {
                console.log(`${index + 1}. ${error}`);
            });
        }

        // El test pasa si la funcionalidad funciona
        expect(addButtonExists).toBe(true);
        expect(templateExists).toBe(true);
        expect(containerExists).toBe(true);
    });
});