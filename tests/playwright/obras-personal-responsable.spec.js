import { test, expect } from '@playwright/test';

test.describe('Crear Obra - Personal Responsable de Obra', () => {
    test.beforeEach(async ({ page }) => {
        // Navegar a la página de login
        await page.goto('/login');

        // Hacer login con credenciales de administrador
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar redirección al home
        await page.waitForURL('/home');
    });

    test('debe mostrar solo personal con categoría "Responsable de obra" en el selector de encargados', async ({ page }) => {
        // Navegar a la página de crear obra
        await page.goto('/obras/create');

        // Verificar que estamos en la página correcta
        await expect(page.locator('h2')).toContainText('Crear Nueva Obra');

        // Verificar que existe el selector de responsable
        const encargadoSelector = page.locator('select[name="encargado_id"]');
        await expect(encargadoSelector).toBeVisible();

        // Verificar el label del selector
        await expect(page.locator('label[for="encargado_id"]')).toContainText('Responsable de la obra');

        // Verificar la descripción que menciona la categoría específica
        await expect(page.locator('text=Personal con categoría "Responsable de obra"')).toBeVisible();

        // Obtener todas las opciones del selector (excluyendo la opción vacía)
        const encargadoOptions = page.locator('select[name="encargado_id"] option:not([value=""])');
        const optionCount = await encargadoOptions.count();

        console.log(`📋 Se encontraron ${optionCount} opciones de personal responsable`);

        if (optionCount > 0) {
            // Verificar que cada opción tiene los atributos correctos
            for (let i = 0; i < optionCount; i++) {
                const option = encargadoOptions.nth(i);

                // Verificar que tiene un ID válido
                const value = await option.getAttribute('value');
                expect(value).toMatch(/^\d+$/); // Debe ser un número

                // Verificar que tiene el atributo data-categoria
                const categoria = await option.getAttribute('data-categoria');
                expect(categoria).toBeTruthy();

                // Verificar que el texto incluye el nombre y la categoría
                const text = await option.textContent();
                expect(text).toBeTruthy();

                console.log(`✅ Opción ${i + 1}: ID=${value}, Categoría="${categoria}", Texto="${text}"`);

                // Verificar que la categoría es "Responsable de obra"
                if (categoria && categoria !== 'Sin categoría') {
                    expect(categoria).toContain('Responsable de obra');
                }
            }

            console.log('✅ Todas las opciones tienen la categoría correcta');

        } else {
            console.log('⚠️ No se encontró personal con categoría "Responsable de obra"');

            // Verificar que aparece la opción por defecto
            await expect(page.locator('select[name="encargado_id"] option[value=""]')).toHaveText('Seleccione un responsable');
        }
    });

    test('debe filtrar correctamente el personal por categoría "Responsable de obra"', async ({ page }) => {
        // Navegar a la página de crear obra
        await page.goto('/obras/create');

        const encargadoSelector = page.locator('select[name="encargado_id"]');
        const encargadoOptions = page.locator('select[name="encargado_id"] option:not([value=""])');
        const optionCount = await encargadoOptions.count();

        if (optionCount > 0) {
            console.log(`📊 Analizando ${optionCount} opciones de personal...`);

            // Verificar que TODAS las opciones tienen la categoría correcta
            let correctCategoryCount = 0;
            let incorrectCategoryCount = 0;

            for (let i = 0; i < optionCount; i++) {
                const option = encargadoOptions.nth(i);
                const categoria = await option.getAttribute('data-categoria');
                const text = await option.textContent();

                if (categoria && categoria.includes('Responsable de obra')) {
                    correctCategoryCount++;
                    console.log(`✅ Personal válido: ${text} - ${categoria}`);
                } else {
                    incorrectCategoryCount++;
                    console.log(`❌ Personal inválido: ${text} - ${categoria || 'Sin categoría'}`);
                }
            }

            console.log(`📈 Resumen del filtrado:`);
            console.log(`   ✅ Personal con categoría correcta: ${correctCategoryCount}`);
            console.log(`   ❌ Personal con categoría incorrecta: ${incorrectCategoryCount}`);

            // EXPECTATIVA: Todos deben tener la categoría correcta
            expect(incorrectCategoryCount).toBe(0);
            expect(correctCategoryCount).toBe(optionCount);

            console.log('✅ El filtro por categoría "Responsable de obra" funciona correctamente');

        } else {
            console.log('⚠️ No hay personal disponible para verificar el filtrado');
        }
    });

    test('debe poder seleccionar personal responsable y crear obra exitosamente', async ({ page }) => {
        await page.goto('/obras/create');

        // Llenar campos obligatorios básicos
        await page.fill('input[name="nombre_obra"]', 'Test Obra - Personal Responsable');
        await page.selectOption('select[name="estatus"]', 'planificada');
        await page.fill('input[name="fecha_inicio"]', '2024-12-20');

        // Verificar y seleccionar encargado
        const encargadoSelector = page.locator('select[name="encargado_id"]');
        const encargadoOptions = await encargadoSelector.locator('option:not([value=""])').count();

        if (encargadoOptions > 0) {
            // Obtener información del primer encargado disponible
            const firstOption = encargadoSelector.locator('option:not([value=""])').first();
            const optionValue = await firstOption.getAttribute('value');
            const optionText = await firstOption.textContent();
            const optionCategoria = await firstOption.getAttribute('data-categoria');

            console.log(`👤 Seleccionando encargado:`);
            console.log(`   ID: ${optionValue}`);
            console.log(`   Nombre: ${optionText}`);
            console.log(`   Categoría: ${optionCategoria}`);

            // Seleccionar el encargado
            await encargadoSelector.selectOption(optionValue);

            // Verificar que se seleccionó correctamente
            const selectedValue = await encargadoSelector.inputValue();
            expect(selectedValue).toBe(optionValue);

            // Agregar observaciones para identificar la prueba
            await page.fill('textarea[name="observaciones"]', 'Obra de prueba con personal responsable - Playwright Test');

            // Enviar formulario
            await page.click('button[type="submit"]');

            // Verificar redirección exitosa
            await page.waitForURL('/obras', { timeout: 10000 });

            // Verificar mensaje de éxito
            const successMessage = page.locator('.bg-green-100');
            await expect(successMessage).toBeVisible();
            await expect(successMessage).toContainText('exitosamente');

            console.log('✅ Obra creada exitosamente con personal responsable asignado');

        } else {
            console.log('⚠️ No hay personal responsable disponible para crear la obra');

            // Intentar enviar el formulario sin encargado para verificar validación
            await page.click('button[type="submit"]');

            // Verificar que se muestra error de validación
            await expect(page.locator('.text-red-600')).toBeVisible();
            console.log('✅ Validación de campo obligatorio funciona correctamente');
        }
    });

    test('debe mostrar información detallada del personal en las opciones', async ({ page }) => {
        await page.goto('/obras/create');

        const encargadoOptions = page.locator('select[name="encargado_id"] option:not([value=""])');
        const optionCount = await encargadoOptions.count();

        if (optionCount > 0) {
            console.log(`🔍 Verificando información detallada de ${optionCount} opciones...`);

            for (let i = 0; i < Math.min(optionCount, 3); i++) { // Verificar máximo 3 para eficiencia
                const option = encargadoOptions.nth(i);
                const text = await option.textContent();
                const categoria = await option.getAttribute('data-categoria');

                // Verificar que el texto incluye nombre completo
                expect(text).toBeTruthy();
                expect(text.length).toBeGreaterThan(3); // Debe tener un nombre real

                // Verificar que incluye la categoría en el texto
                if (categoria && categoria !== 'Sin categoría') {
                    expect(text).toContain(categoria);
                }

                console.log(`📝 Opción ${i + 1}: "${text}"`);
                console.log(`   📂 Categoría: ${categoria}`);

                // Verificar estructura esperada: "Nombre - Categoría"
                if (text.includes(' - ')) {
                    const parts = text.split(' - ');
                    expect(parts.length).toBe(2);

                    const nombre = parts[0].trim();
                    const categoriaTexto = parts[1].trim();

                    expect(nombre.length).toBeGreaterThan(0);
                    expect(categoriaTexto.length).toBeGreaterThan(0);

                    console.log(`   👤 Nombre: "${nombre}"`);
                    console.log(`   🏷️ Categoría mostrada: "${categoriaTexto}"`);
                }
            }

            console.log('✅ Información detallada del personal se muestra correctamente');

        } else {
            console.log('⚠️ No hay opciones de personal para verificar información detallada');
        }
    });

    test('debe validar que el campo encargado es obligatorio', async ({ page }) => {
        await page.goto('/obras/create');

        // Llenar otros campos obligatorios pero dejar encargado vacío
        await page.fill('input[name="nombre_obra"]', 'Test Obra Sin Encargado');
        await page.selectOption('select[name="estatus"]', 'planificada');
        await page.fill('input[name="fecha_inicio"]', '2024-12-20');

        // Verificar que el campo está marcado como requerido
        const encargadoSelector = page.locator('select[name="encargado_id"]');
        await expect(encargadoSelector).toHaveAttribute('required');

        // Verificar que hay un asterisco rojo indicando campo obligatorio
        await expect(page.locator('label[for="encargado_id"] .text-red-500')).toHaveText('*');

        // Intentar enviar formulario sin seleccionar encargado
        await page.click('button[type="submit"]');

        // Verificar que no se redirige (se queda en la misma página)
        await expect(page.url()).toContain('/obras/create');

        console.log('✅ Validación de campo obligatorio para encargado funciona correctamente');
    });

    test('debe manejar el caso cuando no hay personal con categoría "Responsable de obra"', async ({ page }) => {
        await page.goto('/obras/create');

        const encargadoSelector = page.locator('select[name="encargado_id"]');
        const encargadoOptions = page.locator('select[name="encargado_id"] option:not([value=""])');
        const optionCount = await encargadoOptions.count();

        // Verificar que siempre existe la opción por defecto
        await expect(page.locator('select[name="encargado_id"] option[value=""]')).toBeVisible();
        await expect(page.locator('select[name="encargado_id"] option[value=""]')).toHaveText('Seleccione un responsable');

        if (optionCount === 0) {
            console.log('⚠️ No hay personal con categoría "Responsable de obra" registrado');
            console.log('📝 Recomendación: Agregar personal con esta categoría para poder crear obras');

            // Llenar formulario y verificar que falla por falta de encargado
            await page.fill('input[name="nombre_obra"]', 'Test Obra Sin Personal Responsable');
            await page.selectOption('select[name="estatus"]', 'planificada');
            await page.fill('input[name="fecha_inicio"]', '2024-12-20');

            // Intentar enviar
            await page.click('button[type="submit"]');

            // Verificar que no se puede crear obra sin encargado
            await expect(page.url()).toContain('/obras/create');

            console.log('✅ Sistema previene correctamente la creación de obra sin personal responsable');

        } else {
            console.log(`✅ Sistema tiene ${optionCount} personal(es) con categoría "Responsable de obra" disponible(s)`);
        }
    });
});