import { test, expect } from '@playwright/test';

test.describe('Vehicle Creation with Operator Assignment - Comprehensive Test', () => {
    let consoleErrors: string[] = [];
    let networkErrors: string[] = [];

    test.beforeEach(async ({ page }) => {
        // Reset error arrays
        consoleErrors = [];
        networkErrors = [];

        // Listen for console errors
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
                console.log(`❌ CONSOLE ERROR: ${msg.text()}`);
            }
        });

        // Listen for network failures
        page.on('requestfailed', request => {
            networkErrors.push(`${request.method()} ${request.url()} - ${request.failure()?.errorText}`);
            console.log(`🌐 NETWORK ERROR: ${request.method()} ${request.url()} - ${request.failure()?.errorText}`);
        });

        // Block external resources to speed up tests
        await page.route('**/*', (route) => {
            const url = route.request().url();
            if (url.includes('fonts.googleapis.com') ||
                url.includes('maps.googleapis.com') ||
                url.includes('cdnjs.cloudflare.com')) {
                route.abort();
            } else {
                route.continue();
            }
        });
    });

    test('should create vehicle with operator successfully and verify database storage', async ({ page }) => {
        console.log('🚀 INICIANDO TEST COMPLETO: Crear vehículo con operador');

        // Step 1: Login as admin
        console.log('🔐 Paso 1: Autenticación como administrador');
        await page.goto('/login');
        await page.waitForLoadState('networkidle');

        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Wait for successful login
        await page.waitForURL('**/home', { timeout: 10000 });
        console.log('✅ Login exitoso');

        // Step 2: Navigate to vehicle creation page
        console.log('🚗 Paso 2: Navegando a crear vehículo');
        await page.goto('/vehiculos/create');
        await page.waitForLoadState('networkidle');

        // Verify page loaded correctly
        await expect(page.locator('h2:has-text("Agregar Nuevo Vehículo")')).toBeVisible();
        console.log('✅ Página de creación cargada correctamente');

        // Step 3: Check operator dropdown availability
        console.log('👥 Paso 3: Verificando disponibilidad de operadores');
        const operadorDropdown = page.locator('select[name="operador_id"]');
        await expect(operadorDropdown).toBeVisible();

        const operatorOptions = await operadorDropdown.locator('option:not([value=""])').count();
        console.log(`📋 Operadores disponibles: ${operatorOptions}`);

        if (operatorOptions === 0) {
            throw new Error('❌ No hay operadores disponibles en el sistema');
        }

        // Step 4: Fill basic vehicle information
        console.log('📝 Paso 4: Llenando información básica del vehículo');
        const timestamp = Date.now();
        const testData = {
            marca: 'Toyota',
            modelo: 'Hilux',
            anio: '2024',
            n_serie: `TEST_VIN_${timestamp}`,
            placas: `TST-${timestamp.toString().slice(-3)}`,
            kilometraje_actual: '15000'
        };

        await page.fill('input[name="marca"]', testData.marca);
        await page.fill('input[name="modelo"]', testData.modelo);
        await page.fill('input[name="anio"]', testData.anio);
        await page.fill('input[name="n_serie"]', testData.n_serie);
        await page.fill('input[name="placas"]', testData.placas);
        await page.fill('input[name="kilometraje_actual"]', testData.kilometraje_actual);

        console.log('✅ Información básica completada');

        // Step 5: Select an operator
        console.log('👤 Paso 5: Seleccionando operador');

        // Get the first available operator
        const firstOperatorOption = operadorDropdown.locator('option:not([value=""])').first();
        const operatorId = await firstOperatorOption.getAttribute('value');
        const operatorName = await firstOperatorOption.textContent();

        await operadorDropdown.selectOption(operatorId!);

        // Verify operator was selected
        const selectedOperatorId = await operadorDropdown.inputValue();
        expect(selectedOperatorId).toBe(operatorId);

        console.log(`✅ Operador seleccionado: ${operatorName?.trim()} (ID: ${operatorId})`);

        // Step 6: Add optional fields
        console.log('🔧 Paso 6: Agregando campos opcionales');
        await page.fill('input[name="intervalo_km_motor"]', '5000');
        await page.fill('input[name="intervalo_km_transmision"]', '40000');
        await page.fill('input[name="intervalo_km_hidraulico"]', '10000');
        await page.fill('textarea[name="observaciones"]', `Vehículo de prueba creado por Playwright - ${new Date().toISOString()}`);

        // Step 7: Take screenshot before submission
        await page.screenshot({
            path: 'debug-vehiculo-antes-envio.png',
            fullPage: true
        });

        // Step 8: Submit the form
        console.log('📤 Paso 7: Enviando formulario');

        // Wait for form submission and potential redirect
        const submitPromise = page.waitForResponse(response =>
            response.url().includes('/vehiculos') &&
            response.request().method() === 'POST'
        );

        await page.click('button[type="submit"]:has-text("Guardar Vehículo")');

        // Wait for the response
        try {
            const response = await submitPromise;
            console.log(`📨 Respuesta del servidor: ${response.status()}`);

            if (!response.ok()) {
                const responseText = await response.text();
                console.log(`❌ Error en respuesta: ${responseText}`);
            }
        } catch (error) {
            console.log(`⚠️ No se capturó la respuesta HTTP: ${error}`);
        }

        // Wait for navigation or stay on the same page if there are errors
        await page.waitForLoadState('networkidle');

        // Step 9: Verify the result
        console.log('🔍 Paso 8: Verificando resultado');

        const currentUrl = page.url();
        console.log(`📍 URL actual: ${currentUrl}`);

        // Check for validation errors
        const validationErrors = await page.locator('.bg-red-100, .text-red-600, .border-red-500').count();

        if (validationErrors > 0) {
            console.log('❌ Errores de validación encontrados');

            // Capture and log all error messages
            const errorMessages = await page.locator('.bg-red-100, .text-red-600').allTextContents();
            errorMessages.forEach((error, index) => {
                console.log(`  Error ${index + 1}: ${error.trim()}`);
            });

            // Take screenshot of errors
            await page.screenshot({
                path: 'debug-vehiculo-errores-validacion.png',
                fullPage: true
            });

            // Check if operator-related error
            const operatorError = errorMessages.some(error =>
                error.toLowerCase().includes('operador') ||
                error.toLowerCase().includes('operator') ||
                error.toLowerCase().includes('personal')
            );

            if (operatorError) {
                console.log('🎯 ERROR RELACIONADO CON OPERADOR DETECTADO');

                // Try to identify the specific issue
                const operatorFieldError = await page.locator('select[name="operador_id"] + p.text-red-600').textContent();
                if (operatorFieldError) {
                    console.log(`🔍 Error específico del operador: ${operatorFieldError}`);
                }
            }

            throw new Error(`Validation errors detected: ${errorMessages.join(', ')}`);
        }

        // Check for success message or redirect to vehicle details
        const successMessage = await page.locator('.bg-green-100').count();
        const isOnVehicleDetailsPage = currentUrl.includes('/vehiculos/') &&
            !currentUrl.includes('/create') &&
            !currentUrl.includes('/edit');

        if (successMessage > 0 || isOnVehicleDetailsPage) {
            console.log('✅ Vehículo creado exitosamente');

            if (isOnVehicleDetailsPage) {
                // Extract vehicle ID from URL
                const vehicleId = currentUrl.split('/').pop();
                console.log(`🆔 ID del vehículo creado: ${vehicleId}`);

                // Step 10: Verify operator assignment on details page
                console.log('👤 Paso 9: Verificando asignación de operador en página de detalle');

                // Wait for page to load completely
                await page.waitForTimeout(2000);

                // Check if operator information is displayed
                const operatorSection = page.locator('text=Operador Actual, text=Operador Asignado');
                const hasOperatorSection = await operatorSection.count() > 0;

                if (hasOperatorSection) {
                    const operatorDisplayedName = await page.locator('text=Operador Actual, text=Operador Asignado').locator('..').locator('.bg-gray-600').textContent();
                    console.log(`✅ Operador mostrado en detalles: ${operatorDisplayedName?.trim()}`);

                    // Verify it matches what we selected
                    if (operatorDisplayedName?.trim() === operatorName?.trim()) {
                        console.log('🎉 ¡PERFECTO! El operador se guardó y muestra correctamente');
                    } else {
                        console.log('⚠️ ADVERTENCIA: El operador mostrado no coincide con el seleccionado');
                    }
                } else {
                    // Check for "no operator assigned" message
                    const noOperatorMessage = await page.locator('text=No hay operador asignado, text=Sin operador').count();
                    if (noOperatorMessage > 0) {
                        console.log('❌ PROBLEMA: El vehículo se creó pero SIN operador asignado');

                        // This indicates the issue we need to fix
                        throw new Error('Vehicle created but operator was not saved properly');
                    } else {
                        console.log('⚠️ No se pudo determinar el estado del operador en la página');
                    }
                }

                // Take screenshot of final result
                await page.screenshot({
                    path: 'debug-vehiculo-resultado-final.png',
                    fullPage: true
                });

                // Step 11: Database verification (if possible through UI)
                console.log('💾 Paso 10: Verificación adicional');

                // Try to edit the vehicle to see if operator is actually saved
                const editButton = page.locator('a:has-text("Editar Vehículo")');
                if (await editButton.count() > 0) {
                    await editButton.click();
                    await page.waitForLoadState('networkidle');

                    const operatorInEditForm = await page.locator('select[name="operador_id"]').inputValue();
                    console.log(`🔍 Operador en formulario de edición: ${operatorInEditForm}`);

                    if (operatorInEditForm === operatorId) {
                        console.log('✅ CONFIRMADO: El operador se guardó correctamente en la base de datos');
                    } else {
                        console.log('❌ PROBLEMA CONFIRMADO: El operador NO se guardó en la base de datos');
                        throw new Error('Operator was not saved to database');
                    }
                }

            } else if (successMessage > 0) {
                // Success message shown, probably redirected to index
                const successText = await page.locator('.bg-green-100').textContent();
                console.log(`✅ Mensaje de éxito: ${successText?.trim()}`);
            }

        } else {
            console.log('❓ Estado indeterminado - no hay errores evidentes pero tampoco confirmación clara de éxito');

            // Take screenshot for analysis
            await page.screenshot({
                path: 'debug-vehiculo-estado-indeterminado.png',
                fullPage: true
            });
        }

        // Step 12: Report any JavaScript or network errors
        console.log('📊 Paso 11: Reporte de errores técnicos');

        if (consoleErrors.length > 0) {
            console.log('❌ Errores de JavaScript detectados:');
            consoleErrors.forEach((error, index) => {
                console.log(`  JS Error ${index + 1}: ${error}`);
            });
        }

        if (networkErrors.length > 0) {
            console.log('🌐 Errores de red detectados:');
            networkErrors.forEach((error, index) => {
                console.log(`  Network Error ${index + 1}: ${error}`);
            });
        }

        if (consoleErrors.length === 0 && networkErrors.length === 0) {
            console.log('✅ No se detectaron errores técnicos');
        }

        // Final assertion - test should pass if we get here without throwing
        expect(validationErrors).toBe(0);
    });

    test('should identify specific operator field validation issues', async ({ page }) => {
        console.log('🔬 TEST ESPECÍFICO: Identificar problemas de validación del operador');

        // Login
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');

        // Go to create vehicle
        await page.goto('/vehiculos/create');
        await page.waitForLoadState('networkidle');

        // Fill minimal required fields (without operator)
        const timestamp = Date.now();
        await page.fill('input[name="marca"]', 'Test');
        await page.fill('input[name="modelo"]', 'Test');
        await page.fill('input[name="anio"]', '2024');
        await page.fill('input[name="n_serie"]', `TEST_NO_OP_${timestamp}`);
        await page.fill('input[name="placas"]', `NO-${timestamp.toString().slice(-3)}`);
        await page.fill('input[name="kilometraje_actual"]', '1000');

        // Verify operator field is optional (should not be required)
        const operatorField = page.locator('select[name="operador_id"]');
        const isRequired = await operatorField.getAttribute('required');

        console.log(`📋 ¿Campo operador es requerido? ${isRequired ? 'SÍ' : 'NO'}`);

        // Test 1: Submit without operator (should work)
        console.log('🧪 Prueba 1: Envío sin operador (debería funcionar)');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        const errorsWithoutOperator = await page.locator('.bg-red-100, .text-red-600').count();
        console.log(`❓ Errores sin operador: ${errorsWithoutOperator}`);

        // Test 2: Try with invalid operator ID
        if (errorsWithoutOperator === 0) {
            console.log('✅ Vehículo sin operador creado correctamente');

            // Go back to create another one with operator
            await page.goto('/vehiculos/create');
            await page.waitForLoadState('networkidle');

            // Fill basic fields again
            const timestamp2 = Date.now();
            await page.fill('input[name="marca"]', 'Test2');
            await page.fill('input[name="modelo"]', 'Test2');
            await page.fill('input[name="anio"]', '2024');
            await page.fill('input[name="n_serie"]', `TEST_WITH_OP_${timestamp2}`);
            await page.fill('input[name="placas"]', `WO-${timestamp2.toString().slice(-3)}`);
            await page.fill('input[name="kilometraje_actual"]', '2000');

            console.log('🧪 Prueba 2: Envío con operador válido');

            // Select valid operator
            const operatorOptions = await operatorField.locator('option:not([value=""])').count();
            if (operatorOptions > 0) {
                await operatorField.selectOption({ index: 1 });
                const selectedOperator = await operatorField.inputValue();
                console.log(`👤 Operador seleccionado: ${selectedOperator}`);

                await page.click('button[type="submit"]');
                await page.waitForLoadState('networkidle');

                const errorsWithOperator = await page.locator('.bg-red-100, .text-red-600').count();
                console.log(`❓ Errores con operador: ${errorsWithOperator}`);

                if (errorsWithOperator > 0) {
                    const errorMessages = await page.locator('.bg-red-100, .text-red-600').allTextContents();
                    console.log('❌ ERRORES CON OPERADOR DETECTADOS:');
                    errorMessages.forEach(error => console.log(`  - ${error.trim()}`));

                    // Take screenshot for debugging
                    await page.screenshot({
                        path: 'debug-vehiculo-error-con-operador.png',
                        fullPage: true
                    });
                } else {
                    console.log('✅ Vehículo con operador creado correctamente');
                }
            }
        } else {
            console.log('❌ Error inesperado: Falló crear vehículo sin operador');
        }
    });
});