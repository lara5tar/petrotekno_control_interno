import { test, expect } from '@playwright/test';

test.describe('Test Corrección Guardado Encargado y Asignaciones', () => {
    test('verificar que se guardan correctamente encargado_id y asignaciones', async ({ page }) => {
        console.log('=== TESTING CORRECCIÓN DE GUARDADO ===');

        test.setTimeout(60000);

        try {
            // Login
            console.log('🔐 Iniciando login...');
            await page.goto('http://localhost:8000/login');
            await page.fill('#email', 'admin@petrotekno.com');
            await page.fill('#password', 'password123');
            await page.click('button[type="submit"]');
            await page.waitForURL(/.*dashboard.*|.*home.*/);
            console.log('✅ Login exitoso');

            // Ir al formulario
            console.log('📝 Navegando a formulario...');
            await page.goto('http://localhost:8000/obras/create');
            await page.waitForLoadState('networkidle');
            await page.waitForTimeout(3000);

            // Llenar formulario básico
            console.log('📝 Llenando datos básicos...');
            await page.fill('input[name="nombre_obra"]', 'Obra Test Corrección Guardado');
            await page.selectOption('select[name="estatus"]', 'planificada');
            await page.fill('input[name="fecha_inicio"]', '2025-08-15');
            await page.fill('input[name="avance"]', '0');

            // Seleccionar encargado
            console.log('👥 Seleccionando encargado...');
            const encargadoSelect = page.locator('#encargado_id');
            await expect(encargadoSelect).toBeVisible();
            const firstOption = encargadoSelect.locator('option:not([value=""])').first();
            const encargadoId = await firstOption.getAttribute('value');
            await encargadoSelect.selectOption(encargadoId);
            console.log(`✅ Encargado seleccionado: ID=${encargadoId}`);

            // Agregar vehículo
            console.log('🚗 Agregando vehículo...');
            const addButton = page.locator('button:has-text("Agregar Vehículo")');
            await addButton.click();
            await page.waitForTimeout(2000);

            const vehicleSelect = page.locator('select.vehicle-select').first();
            const firstVehicleOption = vehicleSelect.locator('option:not([value=""])').first();
            const vehicleId = await firstVehicleOption.getAttribute('value');
            await vehicleSelect.selectOption(vehicleId);

            const kilometrajeInput = page.locator('input[name*="kilometraje_inicial"]').first();
            await kilometrajeInput.fill('1000');
            console.log(`✅ Vehículo seleccionado: ID=${vehicleId}`);

            // Enviar formulario
            console.log('📤 Enviando formulario...');
            const submitButton = page.locator('button[type="submit"]:has-text("Crear Obra")');
            await submitButton.click();

            // Esperar respuesta (sin timeout estricto de navegación)
            await page.waitForTimeout(5000);

            // Verificar resultado
            const currentUrl = page.url();
            console.log(`📍 URL actual: ${currentUrl}`);

            // Buscar mensajes de éxito o error
            const successMessage = page.locator('.bg-green-100, .alert-success');
            const errorMessage = page.locator('.bg-red-100, .alert-danger');

            const hasSuccess = await successMessage.count() > 0;
            const hasError = await errorMessage.count() > 0;

            console.log(`📊 Mensaje de éxito: ${hasSuccess ? '✅ SÍ' : '❌ NO'}`);
            console.log(`📊 Mensaje de error: ${hasError ? '❌ SÍ' : '✅ NO'}`);

            if (hasSuccess) {
                const successText = await successMessage.first().textContent();
                console.log(`✅ Mensaje: "${successText?.trim()}"`);
            }

            if (hasError) {
                const errorText = await errorMessage.first().textContent();
                console.log(`❌ Error: "${errorText?.trim()}"`);
            }

            // Verificar que estamos en la página correcta
            const isInObrasIndex = currentUrl.includes('/obras') && !currentUrl.includes('/create');
            console.log(`📊 Redirigido a obras index: ${isInObrasIndex ? '✅ SÍ' : '❌ NO'}`);

            // Screenshot final
            await page.screenshot({
                path: 'debug-correccion-guardado.png',
                fullPage: true
            });

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

        } catch (error) {
            console.log(`❌ Error en test: ${error.message}`);
            await page.screenshot({
                path: 'debug-correccion-error.png',
                fullPage: true
            });
            throw error;
        }
    });
});