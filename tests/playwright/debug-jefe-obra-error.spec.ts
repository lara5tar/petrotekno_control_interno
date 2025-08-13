import { test, expect } from '@playwright/test';

test.describe('Debug Error Jefe de Obra en Personal', () => {

    test('Investigar error "Jefe de Obra" en crear personal', async ({ page }) => {
        console.log('🔍 INVESTIGANDO ERROR "JEFE DE OBRA"');
        console.log('================================');

        try {
            // Login
            await page.goto('/login');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            // Ir a crear personal
            await page.goto('/personal/create');
            await page.waitForLoadState('networkidle');

            console.log('📝 En formulario crear personal');
            await page.screenshot({ path: 'debug-jefe-obra-formulario-inicial.png' });

            // Llenar campos básicos
            await page.fill('input[name="nombre_completo"]', 'Test Jefe de Obra');

            // Buscar el dropdown de categorías
            const categoryDropdown = page.locator('select[name="categoria_personal_id"]');

            if (await categoryDropdown.isVisible()) {
                console.log('✅ Dropdown de categorías encontrado');

                // Obtener todas las opciones
                const options = await categoryDropdown.locator('option').allTextContents();
                console.log('📋 Opciones disponibles:', options);

                // Obtener valores de las opciones
                const optionValues = await categoryDropdown.locator('option').evaluateAll(options =>
                    options.map(option => ({ text: option.textContent?.trim(), value: option.value }))
                );
                console.log('📋 Opciones con valores:', optionValues);

                // Intentar seleccionar "Jefe de Obra"
                console.log('🎯 Intentando seleccionar "Jefe de Obra"...');

                // Probar diferentes formas de seleccionar
                try {
                    await categoryDropdown.selectOption({ label: 'Jefe de Obra' });
                    console.log('✅ Seleccionado por label');
                } catch (error) {
                    console.log('❌ Error seleccionando por label:', error.message);

                    // Intentar por índice
                    try {
                        const jefeObraIndex = options.findIndex(opt => opt.includes('Jefe de Obra'));
                        if (jefeObraIndex >= 0) {
                            await categoryDropdown.selectOption({ index: jefeObraIndex });
                            console.log(`✅ Seleccionado por índice: ${jefeObraIndex}`);
                        }
                    } catch (indexError) {
                        console.log('❌ Error seleccionando por índice:', indexError.message);
                    }
                }

                await page.screenshot({ path: 'debug-jefe-obra-seleccionado.png' });

                // Llenar otros campos requeridos
                await page.fill('input[name="curp_numero"]', 'JEOB801010HPLXXX01');
                await page.fill('input[name="rfc"]', 'JEOB801010ABC');
                await page.fill('input[name="nss"]', '12345678901');
                await page.fill('input[name="direccion"]', 'Dirección de prueba');
                await page.fill('input[name="ine"]', 'JEOB001');

                await page.screenshot({ path: 'debug-jefe-obra-formulario-completo.png' });

                // Intentar enviar el formulario
                console.log('📤 Enviando formulario...');
                const submitButton = page.locator('button[type="submit"]');
                await submitButton.click();

                // Esperar respuesta
                await page.waitForLoadState('networkidle', { timeout: 10000 });
                await page.screenshot({ path: 'debug-jefe-obra-resultado.png' });

                // Verificar si hay errores
                const errorMessages = await page.locator('.alert-danger, .text-danger, .invalid-feedback').allTextContents();
                if (errorMessages.length > 0) {
                    console.log('❌ Errores encontrados:');
                    errorMessages.forEach((error, index) => {
                        console.log(`   ${index + 1}: ${error}`);
                    });
                } else {
                    console.log('✅ No se encontraron errores visibles');
                }

                // Verificar URL actual
                console.log('📍 URL después del envío:', page.url());

            } else {
                console.log('❌ No se encontró el dropdown de categorías');
            }

        } catch (error) {
            console.log('❌ Error en la investigación:', error.message);
            await page.screenshot({ path: 'debug-jefe-obra-error-general.png' });
        }
    });

    test('Verificar datos de categorías en backend', async ({ page }) => {
        console.log('🗄️ VERIFICANDO DATOS DE CATEGORÍAS');
        console.log('=================================');

        console.log('🔍 Necesitamos verificar:');
        console.log('1. ¿Existe "Jefe de Obra" en categorias_personal?');
        console.log('2. ¿Cuál es su ID en la base de datos?');
        console.log('3. ¿El controlador está validando correctamente?');

        await page.screenshot({ path: 'debug-verificacion-categorias-backend.png' });
    });

    test('Test con diferentes categorías para comparar', async ({ page }) => {
        console.log('🔄 PROBANDO DIFERENTES CATEGORÍAS');
        console.log('================================');

        try {
            // Login
            await page.goto('/login');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            const categoriasParaProbar = ['Administrador', 'Operador', 'Supervisor'];

            for (const categoria of categoriasParaProbar) {
                console.log(`🧪 Probando categoría: ${categoria}`);

                await page.goto('/personal/create');
                await page.waitForLoadState('networkidle');

                // Llenar formulario básico
                await page.fill('input[name="nombre_completo"]', `Test ${categoria}`);

                // Seleccionar categoría
                const categoryDropdown = page.locator('select[name="categoria_personal_id"]');
                await categoryDropdown.selectOption({ label: categoria });

                // Llenar campos requeridos
                await page.fill('input[name="curp_numero"]', `TEST${Date.now()}`);
                await page.fill('input[name="rfc"]', `TST${Date.now()}`);
                await page.fill('input[name="nss"]', '12345678901');
                await page.fill('input[name="direccion"]', 'Dirección test');
                await page.fill('input[name="ine"]', `TST${Date.now()}`);

                // Enviar
                await page.click('button[type="submit"]');
                await page.waitForLoadState('networkidle');

                // Verificar resultado
                const hasError = await page.locator('.alert-danger').count() > 0;
                if (hasError) {
                    const errorText = await page.locator('.alert-danger').textContent();
                    console.log(`❌ ${categoria}: Error - ${errorText}`);
                } else {
                    console.log(`✅ ${categoria}: Funcionó correctamente`);
                }

                await page.screenshot({ path: `debug-categoria-${categoria.toLowerCase()}.png` });
            }

        } catch (error) {
            console.log('❌ Error probando categorías:', error.message);
        }
    });

});