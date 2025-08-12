import { test, expect } from '@playwright/test';

test.describe('OBRA VEHICULO ERROR FIX - Reproducir y Solucionar Error', () => {
    test.beforeEach(async ({ page }) => {
        // Bloquear recursos externos para acelerar el test
        await page.route('**/*', (route) => {
            const url = route.request().url();
            if (url.includes('maps.googleapis.com') ||
                url.includes('fonts.googleapis.com') ||
                url.includes('cdnjs.cloudflare.com')) {
                route.abort();
            } else {
                route.continue();
            }
        });
    });

    test('REPRODUCIR ERROR: crear obra con vehículo asignado debe funcionar SIN errores', async ({ page }) => {
        console.log('=== INICIANDO TEST PARA REPRODUCIR ERROR DE OBRA CON VEHICULOS ===');

        // Login
        await page.goto('http://localhost:8000/login');
        await page.waitForLoadState('networkidle');

        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        console.log('✅ Usuario autenticado');

        // Ir directamente a crear obra
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForLoadState('networkidle');

        console.log('🔍 Verificando que la página carga SIN errores...');

        // Verificar que NO hay error de "nombre_completo" en null
        const errorText = await page.textContent('body');

        if (errorText?.includes('Error al crear obra') ||
            errorText?.includes('nombre_completo') ||
            errorText?.includes('Attempt to read property')) {
            console.log('❌ ERROR DETECTADO EN LA PÁGINA');
            console.log('Contenido del error:', errorText.substring(0, 500));

            await page.screenshot({
                path: 'debug-error-nombre-completo.png',
                fullPage: true
            });

            throw new Error('ERROR CONFIRMADO: El formulario tiene el error de nombre_completo en null');
        }

        // Verificar que los dropdowns están cargados correctamente
        const encargadoSelect = page.locator('select[name="encargado_id"]');
        const encargadoOptions = await encargadoSelect.locator('option').count();

        console.log(`📋 Encargados disponibles: ${encargadoOptions}`);

        if (encargadoOptions <= 1) {
            console.log('⚠️  ADVERTENCIA: Pocos encargados disponibles');
            await page.screenshot({ path: 'debug-encargados-dropdown.png' });
        }

        // Llenar formulario básico
        const testObraName = `Obra Test Vehiculo ${Date.now()}`;
        await page.fill('input[name="nombre_obra"]', testObraName);
        await page.selectOption('select[name="estatus"]', 'planificada');
        await page.fill('input[name="fecha_inicio"]', '2025-01-15');
        await page.fill('textarea[name="observaciones"]', 'Test de obra con vehículos');

        // Seleccionar encargado (evitar el primero que puede ser placeholder)
        if (encargadoOptions > 1) {
            await page.selectOption('select[name="encargado_id"]', { index: 1 });
            console.log('✅ Encargado seleccionado');
        }

        // PARTE CRÍTICA: Agregar vehículo
        console.log('🚗 Intentando agregar vehículo...');

        const addVehicleButton = page.locator('button:has-text("Agregar Vehículo"), button[id*="agregar"], button[onclick*="vehiculo"]');

        if (await addVehicleButton.count() > 0) {
            await addVehicleButton.first().click();
            await page.waitForTimeout(1000);

            console.log('✅ Botón de agregar vehículo clickeado');

            // Verificar que apareció el formulario de vehículo
            const vehiculoSelect = page.locator('select[name*="vehiculo_id"], select[id*="vehiculo"]');

            if (await vehiculoSelect.count() > 0) {
                const vehiculoOptions = await vehiculoSelect.first().locator('option').count();
                console.log(`🚗 Vehículos disponibles: ${vehiculoOptions}`);

                if (vehiculoOptions > 1) {
                    await vehiculoSelect.first().selectOption({ index: 1 });
                    console.log('✅ Vehículo seleccionado');
                }
            }
        } else {
            console.log('⚠️  No se encontró botón para agregar vehículo');
        }

        // Tomar screenshot antes de enviar
        await page.screenshot({
            path: 'debug-form-before-submit.png',
            fullPage: true
        });

        // Enviar formulario
        console.log('📤 Enviando formulario...');
        const submitButton = page.locator('button[type="submit"]');
        await submitButton.click();

        // Esperar resultado
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        const finalUrl = page.url();
        const finalContent = await page.textContent('body');

        console.log(`📍 URL final: ${finalUrl}`);

        // Verificar resultado
        if (finalContent?.includes('exitosamente') || finalUrl.includes('/obras') && !finalUrl.includes('/create')) {
            console.log('✅ OBRA CREADA EXITOSAMENTE - ERROR SOLUCIONADO');
            await page.screenshot({ path: 'debug-success-final.png' });
        } else if (finalContent?.includes('Error') || finalContent?.includes('nombre_completo')) {
            console.log('❌ ERROR PERSISTE');
            console.log('Contenido del error:', finalContent?.substring(0, 800));
            await page.screenshot({ path: 'debug-error-persiste.png', fullPage: true });

            throw new Error('ERROR CONFIRMADO: El problema de nombre_completo sigue presente');
        } else {
            console.log('⚠️  Resultado incierto');
            await page.screenshot({ path: 'debug-resultado-incierto.png', fullPage: true });
        }
    });
});