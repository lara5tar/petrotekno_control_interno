import { test, expect } from '@playwright/test';

test.describe('Diagnóstico de Errores en Edición de Obras', () => {
    test.beforeEach(async ({ page }) => {
        // Configurar interceptación de errores
        page.on('console', msg => {
            if (msg.type() === 'error') {
                console.log('❌ Error de consola:', msg.text());
            }
        });

        page.on('pageerror', error => {
            console.log('❌ Error de página:', error.message);
        });

        page.on('requestfailed', request => {
            console.log('❌ Request fallida:', request.url(), request.failure()?.errorText);
        });

        // Ir a la página de login
        await page.goto('/login');

        // Hacer login con credenciales válidas
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Esperar a que se complete el login
        await page.waitForURL('/home');
        await expect(page).toHaveURL('/home');
    });

    test('Diagnosticar errores en todas las obras existentes', async ({ page }) => {
        console.log('🔍 Iniciando diagnóstico de errores en edición de obras...');

        // Ir a la página de obras
        await page.goto('/obras');
        await page.waitForLoadState('networkidle');

        console.log('📋 Verificando página de índice de obras...');
        await expect(page).toHaveURL('/obras');

        // Buscar todas las obras en la tabla
        const obrasRows = await page.locator('table tbody tr').count();
        console.log(`📊 Encontradas ${obrasRows} obras en el sistema`);

        if (obrasRows === 0) {
            console.log('⚠️ No hay obras en el sistema para diagnosticar');
            return;
        }

        // Diagnosticar cada obra
        for (let i = 0; i < obrasRows; i++) {
            const row = page.locator('table tbody tr').nth(i);

            // Obtener información de la obra
            const obraName = await row.locator('td').first().textContent();
            console.log(`\n🏗️ Diagnosticando obra: ${obraName}`);

            // Buscar el botón de editar en esta fila
            const editButton = row.locator('a[href*="/edit"], button:has-text("Editar"), .btn:has-text("Editar")');

            if (await editButton.count() === 0) {
                console.log(`❌ No se encontró botón de editar para la obra: ${obraName}`);
                continue;
            }

            try {
                // Hacer clic en editar
                await editButton.click();
                await page.waitForLoadState('networkidle');

                const currentUrl = page.url();
                console.log(`🔗 URL de edición: ${currentUrl}`);

                // Verificar si llegamos a una página de edición válida
                if (currentUrl.includes('/edit')) {
                    console.log(`✅ Navegación exitosa a edición para: ${obraName}`);

                    // Verificar si hay errores en el formulario
                    const errorMessages = await page.locator('.alert-danger, .error, .invalid-feedback, .text-red-500').count();
                    if (errorMessages > 0) {
                        console.log(`⚠️ Se encontraron ${errorMessages} mensajes de error en el formulario`);

                        // Capturar los mensajes de error específicos
                        const errors = await page.locator('.alert-danger, .error, .invalid-feedback, .text-red-500').allTextContents();
                        errors.forEach((error, index) => {
                            console.log(`   Error ${index + 1}: ${error.trim()}`);
                        });
                    }

                    // Verificar campos del formulario
                    const nombreField = page.locator('input[name="nombre"], #nombre');
                    const descripcionField = page.locator('textarea[name="descripcion"], #descripcion');
                    const ubicacionField = page.locator('input[name="ubicacion"], #ubicacion');

                    console.log('🔍 Verificando campos del formulario:');

                    if (await nombreField.count() > 0) {
                        const nombreValue = await nombreField.inputValue();
                        console.log(`   - Nombre: "${nombreValue}"`);

                        // Verificar si el campo está deshabilitado o tiene errores
                        const isDisabled = await nombreField.isDisabled();
                        if (isDisabled) {
                            console.log('   ⚠️ Campo nombre está deshabilitado');
                        }
                    } else {
                        console.log('   ❌ Campo nombre no encontrado');
                    }

                    if (await descripcionField.count() > 0) {
                        const descripcionValue = await descripcionField.inputValue();
                        console.log(`   - Descripción: "${descripcionValue.substring(0, 50)}..."`);
                    } else {
                        console.log('   ❌ Campo descripción no encontrado');
                    }

                    if (await ubicacionField.count() > 0) {
                        const ubicacionValue = await ubicacionField.inputValue();
                        console.log(`   - Ubicación: "${ubicacionValue}"`);
                    } else {
                        console.log('   ❌ Campo ubicación no encontrado');
                    }

                    // Verificar botón de guardar
                    const saveButton = page.locator('button[type="submit"], input[type="submit"], .btn-primary:has-text("Guardar")');
                    if (await saveButton.count() > 0) {
                        const isDisabled = await saveButton.isDisabled();
                        console.log(`   - Botón guardar: ${isDisabled ? 'Deshabilitado' : 'Habilitado'}`);
                    } else {
                        console.log('   ❌ Botón de guardar no encontrado');
                    }

                    // Intentar una modificación pequeña para probar la funcionalidad
                    console.log('🧪 Probando modificación en el formulario...');

                    if (await descripcionField.count() > 0 && !(await descripcionField.isDisabled())) {
                        const originalValue = await descripcionField.inputValue();
                        await descripcionField.fill(originalValue + ' [TEST]');

                        // Intentar guardar
                        if (await saveButton.count() > 0 && !(await saveButton.isDisabled())) {
                            await saveButton.click();
                            await page.waitForLoadState('networkidle');

                            // Verificar si hubo errores después del envío
                            const postSubmitErrors = await page.locator('.alert-danger, .error, .invalid-feedback, .text-red-500').count();
                            if (postSubmitErrors > 0) {
                                console.log(`❌ Errores después del envío del formulario:`);
                                const postErrors = await page.locator('.alert-danger, .error, .invalid-feedback, .text-red-500').allTextContents();
                                postErrors.forEach((error, index) => {
                                    console.log(`   Error ${index + 1}: ${error.trim()}`);
                                });
                            } else {
                                console.log('✅ Formulario se envió sin errores visibles');
                            }
                        }
                    }

                } else {
                    console.log(`❌ No se pudo acceder a la página de edición. URL actual: ${currentUrl}`);

                    // Verificar si hay mensajes de error en la página actual
                    const pageErrors = await page.locator('.alert-danger, .error, .text-red-500').count();
                    if (pageErrors > 0) {
                        const errors = await page.locator('.alert-danger, .error, .text-red-500').allTextContents();
                        console.log('❌ Errores encontrados en la página:');
                        errors.forEach((error, index) => {
                            console.log(`   Error ${index + 1}: ${error.trim()}`);
                        });
                    }
                }

                // Volver a la página de obras para continuar con la siguiente
                await page.goto('/obras');
                await page.waitForLoadState('networkidle');

            } catch (error) {
                console.log(`❌ Error al diagnosticar obra ${obraName}: ${error.message}`);

                // Intentar volver a la página de obras
                await page.goto('/obras');
                await page.waitForLoadState('networkidle');
            }
        }

        console.log('\n🏁 Diagnóstico completado');
    });

    test('Verificar problemas específicos de autorización y validación', async ({ page }) => {
        console.log('🔒 Verificando problemas de autorización y validación...');

        // Ir directamente a una URL de edición específica
        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        const currentUrl = page.url();
        console.log(`🔗 Intentando acceder a: /obras/1/edit`);
        console.log(`🔗 URL actual: ${currentUrl}`);

        // Verificar si fue redirigido por problemas de autorización
        if (currentUrl.includes('/login')) {
            console.log('❌ Redirigido al login - problema de autenticación');
        } else if (currentUrl.includes('/home') || currentUrl.includes('/obras') && !currentUrl.includes('/edit')) {
            console.log('❌ Redirigido sin acceso a edición - posible problema de autorización');
        } else if (currentUrl.includes('/edit')) {
            console.log('✅ Acceso exitoso a página de edición');

            // Verificar estado del formulario
            const form = page.locator('form');
            if (await form.count() > 0) {
                console.log('✅ Formulario encontrado');

                // Verificar campos específicos
                const requiredFields = ['nombre', 'descripcion', 'ubicacion'];
                for (const fieldName of requiredFields) {
                    const field = page.locator(`input[name="${fieldName}"], textarea[name="${fieldName}"], select[name="${fieldName}"]`);
                    if (await field.count() > 0) {
                        const isDisabled = await field.isDisabled();
                        const value = await field.inputValue();
                        console.log(`   - Campo ${fieldName}: ${isDisabled ? 'Deshabilitado' : 'Habilitado'}, Valor: "${value}"`);
                    } else {
                        console.log(`   ❌ Campo ${fieldName} no encontrado`);
                    }
                }
            } else {
                console.log('❌ No se encontró formulario en la página');
            }
        } else {
            console.log(`❌ URL inesperada: ${currentUrl}`);
        }

        // Intentar con la segunda obra
        console.log('\n🔍 Probando con la segunda obra...');
        await page.goto('/obras/2/edit');
        await page.waitForLoadState('networkidle');

        const currentUrl2 = page.url();
        console.log(`🔗 URL actual para obra 2: ${currentUrl2}`);

        if (currentUrl2.includes('/edit')) {
            console.log('✅ Acceso exitoso a edición de obra 2');

            // Verificar errores específicos
            const errorAlerts = await page.locator('.alert-danger, .error, .text-red-500').count();
            if (errorAlerts > 0) {
                console.log('❌ Errores encontrados en obra 2:');
                const errors = await page.locator('.alert-danger, .error, .text-red-500').allTextContents();
                errors.forEach((error, index) => {
                    console.log(`   Error ${index + 1}: ${error.trim()}`);
                });
            }
        } else {
            console.log(`❌ No se pudo acceder a edición de obra 2. URL: ${currentUrl2}`);
        }
    });
});