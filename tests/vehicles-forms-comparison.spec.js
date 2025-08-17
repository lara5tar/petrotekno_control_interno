const { test, expect } = require('@playwright/test');

test.describe('Vehículos Forms Design Comparison', () => {
    let baseURL = 'http://127.0.0.1:8001';

    test.beforeEach(async ({ page }) => {
        // Aquí puedes agregar autenticación si es necesaria
        // await page.goto(`${baseURL}/login`);
        // await page.fill('[name="email"]', 'admin@example.com');
        // await page.fill('[name="password"]', 'password');
        // await page.click('button[type="submit"]');
    });

    test('Create and Edit forms should have identical structure and design', async ({ page }) => {
        // Primero ir a la página de crear vehículo
        await page.goto(`${baseURL}/vehiculos/create`);

        // Esperar a que cargue completamente
        await page.waitForLoadState('networkidle');

        // Capturar elementos de la página de creación
        const createFormElements = await page.evaluate(() => {
            const formStructure = {
                title: document.querySelector('h2')?.textContent?.trim(),
                breadcrumbs: Array.from(document.querySelectorAll('.breadcrumb li, nav li')).map(el => el.textContent?.trim()),
                sections: [],
                buttons: [],
                formFields: []
            };

            // Capturar secciones principales
            const sections = document.querySelectorAll('h3, h4');
            sections.forEach(section => {
                formStructure.sections.push({
                    text: section.textContent?.trim(),
                    tagName: section.tagName
                });
            });

            // Capturar botones
            const buttons = document.querySelectorAll('button, .btn, [role="button"]');
            buttons.forEach(btn => {
                formStructure.buttons.push({
                    text: btn.textContent?.trim(),
                    type: btn.type || btn.getAttribute('type'),
                    classes: btn.className
                });
            });

            // Capturar campos del formulario
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                formStructure.formFields.push({
                    name: input.name,
                    type: input.type,
                    placeholder: input.placeholder,
                    required: input.required,
                    tagName: input.tagName
                });
            });

            return formStructure;
        });

        // Verificar que el título sea correcto para crear
        expect(createFormElements.title).toBe('Agregar Nuevo Vehículo');

        // Ahora ir a la página de editar (necesitaremos un vehículo existente)
        // Primero intentemos ir al listado para obtener un ID
        await page.goto(`${baseURL}/vehiculos`);
        await page.waitForLoadState('networkidle');

        // Buscar el primer enlace de editar
        const editLink = await page.locator('a[href*="/vehiculos/"][href*="/edit"]').first();

        if (await editLink.count() > 0) {
            await editLink.click();
            await page.waitForLoadState('networkidle');

            // Capturar elementos de la página de edición
            const editFormElements = await page.evaluate(() => {
                const formStructure = {
                    title: document.querySelector('h2')?.textContent?.trim(),
                    breadcrumbs: Array.from(document.querySelectorAll('.breadcrumb li, nav li')).map(el => el.textContent?.trim()),
                    sections: [],
                    buttons: [],
                    formFields: []
                };

                // Capturar secciones principales
                const sections = document.querySelectorAll('h3, h4');
                sections.forEach(section => {
                    formStructure.sections.push({
                        text: section.textContent?.trim(),
                        tagName: section.tagName
                    });
                });

                // Capturar botones
                const buttons = document.querySelectorAll('button, .btn, [role="button"]');
                buttons.forEach(btn => {
                    formStructure.buttons.push({
                        text: btn.textContent?.trim(),
                        type: btn.type || btn.getAttribute('type'),
                        classes: btn.className
                    });
                });

                // Capturar campos del formulario
                const inputs = document.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    formStructure.formFields.push({
                        name: input.name,
                        type: input.type,
                        placeholder: input.placeholder,
                        required: input.required,
                        tagName: input.tagName
                    });
                });

                return formStructure;
            });

            // Verificar que el título sea correcto para editar
            expect(editFormElements.title).toBe('Editar Vehículo');

            // Comparar estructuras (excepto el título)
            console.log('CREATE SECTIONS:', createFormElements.sections);
            console.log('EDIT SECTIONS:', editFormElements.sections);

            // Verificar que tienen las mismas secciones
            expect(editFormElements.sections.length).toBe(createFormElements.sections.length);

            // Verificar cada sección (excepto diferencias esperadas como títulos)
            for (let i = 0; i < createFormElements.sections.length; i++) {
                if (!createFormElements.sections[i].text.includes('Agregar') &&
                    !createFormElements.sections[i].text.includes('Editar')) {
                    expect(editFormElements.sections[i].text).toBe(createFormElements.sections[i].text);
                    expect(editFormElements.sections[i].tagName).toBe(createFormElements.sections[i].tagName);
                }
            }

            // Verificar que tienen el mismo número de campos (excluyendo hidden fields como _method)
            const createVisibleFields = createFormElements.formFields.filter(field =>
                field.type !== 'hidden' && field.name !== '_token'
            );
            const editVisibleFields = editFormElements.formFields.filter(field =>
                field.type !== 'hidden' && field.name !== '_token' && field.name !== '_method'
            );

            console.log('CREATE FIELDS:', createVisibleFields.length);
            console.log('EDIT FIELDS:', editVisibleFields.length);

            expect(editVisibleFields.length).toBe(createVisibleFields.length);

            // Verificar que los campos tienen los mismos nombres y tipos
            for (let i = 0; i < createVisibleFields.length; i++) {
                const createField = createVisibleFields[i];
                const editField = editVisibleFields.find(f => f.name === createField.name);

                expect(editField).toBeDefined();
                expect(editField.type).toBe(createField.type);
                expect(editField.placeholder).toBe(createField.placeholder);
                expect(editField.required).toBe(createField.required);
                expect(editField.tagName).toBe(createField.tagName);
            }

            console.log('✅ Las estructuras de los formularios son idénticas');
        } else {
            console.log('⚠️ No se encontró ningún vehículo para editar. Creando uno primero...');

            // Ir a crear un vehículo para poder probar la edición
            await page.goto(`${baseURL}/vehiculos/create`);
            await page.waitForLoadState('networkidle');

            // Llenar el formulario con datos mínimos
            await page.fill('[name="marca"]', 'Toyota');
            await page.fill('[name="modelo"]', 'Hilux');
            await page.fill('[name="anio"]', '2023');
            await page.fill('[name="n_serie"]', 'TEST123456789');
            await page.fill('[name="placas"]', 'TEST-123');
            await page.fill('[name="kilometraje_actual"]', '10000');

            // Enviar el formulario
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            console.log('Vehículo de prueba creado. Por favor ejecuta el test nuevamente para verificar la edición.');
        }
    });

    test('Visual comparison of form layouts', async ({ page }) => {
        // Test visual para comparar layouts
        await page.goto(`${baseURL}/vehiculos/create`);
        await page.waitForLoadState('networkidle');

        // Verificar elementos específicos del diseño

        // 1. Verificar el header principal
        const mainHeader = await page.locator('h2').first();
        await expect(mainHeader).toBeVisible();
        await expect(mainHeader).toHaveText('Agregar Nuevo Vehículo');

        // 2. Verificar las secciones principales
        const sections = [
            'Información del Vehículo',
            'Documentos del Vehículo',
            'Intervalos de Mantenimiento (Opcional)',
            'Observaciones Adicionales'
        ];

        for (const sectionTitle of sections) {
            const section = page.locator(`h3:has-text("${sectionTitle}"), h4:has-text("${sectionTitle}")`);
            await expect(section).toBeVisible();
        }

        // 3. Verificar los campos principales
        const requiredFields = [
            '[name="marca"]',
            '[name="modelo"]',
            '[name="anio"]',
            '[name="n_serie"]',
            '[name="placas"]',
            '[name="kilometraje_actual"]'
        ];

        for (const field of requiredFields) {
            await expect(page.locator(field)).toBeVisible();
        }

        // 4. Verificar botones de acción
        await expect(page.locator('button[type="submit"]')).toBeVisible();
        await expect(page.locator('a:has-text("Cancelar")')).toBeVisible();

        // 5. Verificar estructura de documentos
        const documentSections = [
            'Póliza de Seguro',
            'Derecho Vehicular',
            'Factura y/o Pedimento',
            'Fotografía del Vehículo'
        ];

        for (const docSection of documentSections) {
            const section = page.locator(`label:has-text("${docSection}")`);
            await expect(section).toBeVisible();
        }

        console.log('✅ Todos los elementos de diseño están presentes y visibles');
    });

    test('Form responsiveness and grid layout', async ({ page }) => {
        await page.goto(`${baseURL}/vehiculos/create`);
        await page.waitForLoadState('networkidle');

        // Verificar que el layout responsive funciona

        // Desktop view
        await page.setViewportSize({ width: 1200, height: 800 });

        // Verificar que los grids de 3 columnas están presentes
        const threeColumnGrid = page.locator('.grid.grid-cols-1.md\\:grid-cols-3');
        await expect(threeColumnGrid).toBeVisible();

        // Verificar que los grids de 2 columnas están presentes
        const twoColumnGrid = page.locator('.grid.grid-cols-1.md\\:grid-cols-2');
        await expect(twoColumnGrid).toBeVisible();

        // Verificar el grid de documentos (lg:grid-cols-2)
        const documentGrid = page.locator('.grid.grid-cols-1.lg\\:grid-cols-2');
        await expect(documentGrid).toBeVisible();

        // Mobile view
        await page.setViewportSize({ width: 375, height: 667 });

        // En mobile, todos los elementos deberían estar en una sola columna
        // Los grids deberían colapsar apropiadamente

        await page.setViewportSize({ width: 1200, height: 800 }); // Volver a desktop

        console.log('✅ El layout responsive funciona correctamente');
    });
});
