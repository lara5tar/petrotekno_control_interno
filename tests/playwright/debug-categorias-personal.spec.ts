import { test, expect } from '@playwright/test';

test.describe('Diagnóstico Categorías Personal en Create', () => {

    test('Investigar formulario crear personal - categorías faltantes', async ({ page }) => {
        console.log('🔍 INVESTIGANDO CATEGORÍAS EN CREAR PERSONAL');
        console.log('============================================');

        try {
            // Login como admin
            await page.goto('/login');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            console.log('✅ Login exitoso');

            // Ir al módulo de personal
            await page.goto('/personal');
            await page.waitForLoadState('networkidle');
            await page.screenshot({ path: 'debug-personal-index.png' });

            console.log('📍 En módulo personal');

            // Buscar botón crear/agregar personal
            const createButtons = [
                'button:has-text("Crear")',
                'button:has-text("Agregar")',
                'button:has-text("Nuevo")',
                'a:has-text("Crear")',
                'a:has-text("Agregar")',
                'a:has-text("Nuevo")',
                '.btn-primary',
                '[href*="create"]',
                '[href*="personal/create"]'
            ];

            let buttonFound = false;
            for (const selector of createButtons) {
                const button = page.locator(selector);
                if (await button.count() > 0 && await button.first().isVisible()) {
                    console.log(`✅ Encontrado botón crear: ${selector}`);
                    await button.first().click();
                    buttonFound = true;
                    break;
                }
            }

            if (!buttonFound) {
                console.log('❌ No se encontró botón para crear personal');
                // Intentar ir directamente a la URL
                await page.goto('/personal/create');
            }

            await page.waitForLoadState('networkidle');
            await page.screenshot({ path: 'debug-crear-personal-formulario.png' });

            console.log('📝 En formulario crear personal');
            console.log('🔍 Buscando dropdown de categorías...');

            // Buscar el dropdown de categorías
            const categorySelectors = [
                'select[name="categoria_id"]',
                'select[name="categoria"]',
                'select[id*="categoria"]',
                'select[class*="categoria"]',
                'select:has(option:text("Administrador"))',
                'select:has(option:text("Operador"))',
                'select:has(option:text("Supervisor"))'
            ];

            let categoryDropdownFound = false;
            for (const selector of categorySelectors) {
                const dropdown = page.locator(selector);
                if (await dropdown.count() > 0) {
                    console.log(`✅ Encontrado dropdown categorías: ${selector}`);

                    // Obtener opciones del dropdown
                    const options = await dropdown.locator('option').allTextContents();
                    console.log('📋 Opciones disponibles:', options);

                    categoryDropdownFound = true;
                    break;
                }
            }

            if (!categoryDropdownFound) {
                console.log('❌ NO se encontró dropdown de categorías');
                console.log('🔍 Buscando otros elementos de categoría...');

                // Buscar cualquier elemento que mencione categoría
                const categoryElements = await page.locator('*:has-text("categoría"), *:has-text("Categoría"), *:has-text("categoria"), *:has-text("Categoria")').count();
                console.log(`📊 Elementos que mencionan "categoría": ${categoryElements}`);

                if (categoryElements > 0) {
                    const categoryTexts = await page.locator('*:has-text("categoría"), *:has-text("Categoría")').allTextContents();
                    console.log('📋 Textos encontrados:', categoryTexts);
                }
            }

            // Verificar si hay errores en la consola
            page.on('console', msg => {
                if (msg.type() === 'error') {
                    console.log('❌ Error JS:', msg.text());
                }
            });

            // Obtener todos los selects del formulario
            const allSelects = await page.locator('select').count();
            console.log(`📊 Total de selects en formulario: ${allSelects}`);

            if (allSelects > 0) {
                for (let i = 0; i < allSelects; i++) {
                    const select = page.locator('select').nth(i);
                    const name = await select.getAttribute('name') || 'sin-nombre';
                    const options = await select.locator('option').allTextContents();
                    console.log(`📋 Select ${i + 1} (name: ${name}):`, options);
                }
            }

            // Capturar contenido completo del formulario
            const formContent = await page.content();
            console.log('📄 Verificando contenido del formulario...');

            if (formContent.includes('categoria') || formContent.includes('Categoria')) {
                console.log('✅ El formulario contiene referencias a categoría');
            } else {
                console.log('❌ El formulario NO contiene referencias a categoría');
            }

        } catch (error) {
            console.log('❌ Error en investigación:', error.message);
            await page.screenshot({ path: 'debug-error-categorias.png' });
        }
    });

    test('Verificar datos en base de datos', async ({ page }) => {
        console.log('🗄️ VERIFICANDO DATOS EN BASE DE DATOS');
        console.log('====================================');

        console.log('🔍 Información que debemos verificar en backend:');
        console.log('1. ¿Existen categorías en la tabla categorias_personal?');
        console.log('2. ¿El controlador está pasando las categorías a la vista?');
        console.log('3. ¿La vista está renderizando el dropdown correctamente?');

        await page.screenshot({ path: 'debug-verificacion-bd.png' });
    });

    test('Inspeccionar respuesta del servidor', async ({ page }) => {
        console.log('🌐 INSPECCIONANDO RESPUESTA DEL SERVIDOR');
        console.log('=======================================');

        // Interceptar la request al formulario create
        page.on('response', response => {
            if (response.url().includes('/personal/create')) {
                console.log(`📡 Response status: ${response.status()}`);
                console.log(`📡 Response URL: ${response.url()}`);
            }
        });

        try {
            await page.goto('/login');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            console.log('🔍 Navegando a /personal/create...');
            await page.goto('/personal/create');
            await page.waitForLoadState('networkidle');

            // Verificar si hay errores en la página
            const hasError = await page.locator('text=Error, text=Exception, .alert-danger').count() > 0;
            if (hasError) {
                console.log('❌ Se detectaron errores en la página');
                const errorText = await page.locator('text=Error, text=Exception, .alert-danger').first().textContent();
                console.log('❌ Error detectado:', errorText);
            } else {
                console.log('✅ No se detectaron errores evidentes');
            }

            await page.screenshot({ path: 'debug-respuesta-servidor.png' });

        } catch (error) {
            console.log('❌ Error inspeccionando servidor:', error.message);
        }
    });

});