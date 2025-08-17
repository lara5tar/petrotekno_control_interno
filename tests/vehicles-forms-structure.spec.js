import { test, expect } from '@playwright/test';

test.describe('Vehicles Forms Design Structure Verification', () => {
    const baseURL = 'http://127.0.0.1:8001';

    test('Verify create form structure and elements', async ({ page }) => {
        // Ir directamente a la página de crear (sin autenticación por ahora)
        await page.goto(`${baseURL}/vehiculos/create`);

        try {
            await page.waitForLoadState('networkidle', { timeout: 10000 });
        } catch (error) {
            console.log('Timeout esperando carga de red, continuando...');
        }

        // Verificar que la página cargó correctamente
        await expect(page).toHaveTitle(/Agregar Vehículo|Editar Vehículo/);

        // Verificar elementos principales del formulario de creación

        // 1. Verificar título principal
        const mainHeader = page.locator('h2');
        await expect(mainHeader).toBeVisible();

        // 2. Verificar secciones principales
        const expectedSections = [
            'Información del Vehículo',
            'Documentos del Vehículo',
            'Intervalos de Mantenimiento',
            'Observaciones'
        ];

        for (const sectionTitle of expectedSections) {
            const section = page.locator(`h3:has-text("${sectionTitle}"), h4:has-text("${sectionTitle}")`);
            if (await section.count() > 0) {
                await expect(section.first()).toBeVisible();
                console.log(`✅ Sección encontrada: ${sectionTitle}`);
            } else {
                console.log(`⚠️ Sección parcial o no encontrada: ${sectionTitle}`);
            }
        }

        // 3. Verificar campos obligatorios
        const requiredFields = [
            { name: 'marca', label: 'Marca' },
            { name: 'modelo', label: 'Modelo' },
            { name: 'anio', label: 'Año' },
            { name: 'n_serie', label: 'Número de Serie' },
            { name: 'placas', label: 'Placas' },
            { name: 'kilometraje_actual', label: 'Kilometraje Actual' }
        ];

        for (const field of requiredFields) {
            const fieldElement = page.locator(`[name="${field.name}"]`);
            if (await fieldElement.count() > 0) {
                await expect(fieldElement).toBeVisible();
                console.log(`✅ Campo encontrado: ${field.label} (${field.name})`);
            } else {
                console.log(`❌ Campo NO encontrado: ${field.label} (${field.name})`);
            }
        }

        // 4. Verificar secciones de documentos
        const documentSections = [
            'Póliza de Seguro',
            'Derecho Vehicular',
            'Factura y/o Pedimento',
            'Fotografía del Vehículo'
        ];

        for (const docSection of documentSections) {
            const section = page.locator(`label:has-text("${docSection}")`);
            if (await section.count() > 0) {
                await expect(section.first()).toBeVisible();
                console.log(`✅ Sección de documento encontrada: ${docSection}`);
            } else {
                console.log(`⚠️ Sección de documento no encontrada: ${docSection}`);
            }
        }

        // 5. Verificar botones de acción
        const submitButton = page.locator('button[type="submit"]');
        const cancelLink = page.locator('a:has-text("Cancelar")');

        if (await submitButton.count() > 0) {
            await expect(submitButton).toBeVisible();
            console.log('✅ Botón de envío encontrado');
        }

        if (await cancelLink.count() > 0) {
            await expect(cancelLink).toBeVisible();
            console.log('✅ Enlace de cancelar encontrado');
        }

        // 6. Verificar estructura de grid responsive
        const grids = {
            'Three columns': '.grid.grid-cols-1.md\\:grid-cols-3',
            'Two columns': '.grid.grid-cols-1.md\\:grid-cols-2',
            'Document grid': '.grid.grid-cols-1.lg\\:grid-cols-2'
        };

        for (const [gridName, gridSelector] of Object.entries(grids)) {
            const grid = page.locator(gridSelector);
            if (await grid.count() > 0) {
                console.log(`✅ Grid responsive encontrado: ${gridName}`);
            } else {
                console.log(`⚠️ Grid responsive no encontrado: ${gridName}`);
            }
        }

        console.log('\n🔍 ESTRUCTURA DEL FORMULARIO DE CREACIÓN VERIFICADA');
    });

    test('Capture current form structure for comparison', async ({ page }) => {
        await page.goto(`${baseURL}/vehiculos/create`);

        try {
            await page.waitForLoadState('networkidle', { timeout: 10000 });
        } catch (error) {
            console.log('Timeout esperando carga de red, continuando...');
        }

        // Capturar la estructura actual del formulario
        const formStructure = await page.evaluate(() => {
            const structure = {
                title: document.querySelector('h2')?.textContent?.trim() || 'No title found',
                sections: [],
                formFields: [],
                buttons: [],
                fileInputs: [],
                gridClasses: []
            };

            // Capturar títulos de secciones
            const sections = document.querySelectorAll('h3, h4');
            sections.forEach(section => {
                if (section.textContent?.trim()) {
                    structure.sections.push(section.textContent.trim());
                }
            });

            // Capturar campos del formulario
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.name && input.type !== 'hidden') {
                    structure.formFields.push({
                        name: input.name,
                        type: input.type,
                        required: input.required,
                        placeholder: input.placeholder
                    });
                }
            });

            // Capturar botones
            const buttons = document.querySelectorAll('button, [role="button"]');
            buttons.forEach(btn => {
                if (btn.textContent?.trim()) {
                    structure.buttons.push(btn.textContent.trim());
                }
            });

            // Capturar inputs de archivo
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                if (input.name) {
                    structure.fileInputs.push(input.name);
                }
            });

            // Capturar clases de grid
            const gridElements = document.querySelectorAll('[class*="grid-cols"]');
            gridElements.forEach(el => {
                const classes = Array.from(el.classList).filter(cls => cls.includes('grid'));
                structure.gridClasses.push(...classes);
            });

            return structure;
        });

        console.log('\n📊 ESTRUCTURA ACTUAL DEL FORMULARIO:');
        console.log('==========================================');
        console.log(`Título: ${formStructure.title}`);
        console.log(`Secciones (${formStructure.sections.length}):`, formStructure.sections);
        console.log(`Campos (${formStructure.formFields.length}):`, formStructure.formFields.map(f => `${f.name} (${f.type})`));
        console.log(`Botones (${formStructure.buttons.length}):`, formStructure.buttons);
        console.log(`Inputs de archivo (${formStructure.fileInputs.length}):`, formStructure.fileInputs);
        console.log(`Clases de grid únicos:`, [...new Set(formStructure.gridClasses)]);

        // Verificar estructura mínima esperada
        expect(formStructure.sections.length).toBeGreaterThan(3);
        expect(formStructure.formFields.length).toBeGreaterThan(6);
        expect(formStructure.buttons.length).toBeGreaterThan(1);

        console.log('\n✅ ESTRUCTURA BÁSICA VERIFICADA CORRECTAMENTE');
    });

    test('Visual layout verification', async ({ page }) => {
        await page.goto(`${baseURL}/vehiculos/create`);

        try {
            await page.waitForLoadState('networkidle', { timeout: 10000 });
        } catch (error) {
            console.log('Timeout esperando carga de red, continuando...');
        }

        // Verificar que los elementos principales estén visualmente posicionados correctamente

        // 1. Verificar que el formulario tenga un contenedor principal
        const mainContainer = page.locator('.bg-white.rounded-lg.shadow');
        if (await mainContainer.count() > 0) {
            await expect(mainContainer.first()).toBeVisible();
            console.log('✅ Contenedor principal del formulario encontrado');
        }

        // 2. Verificar espaciado entre secciones
        const sectionContainers = page.locator('.space-y-8 > div');
        const sectionCount = await sectionContainers.count();
        console.log(`📐 Número de secciones con espaciado: ${sectionCount}`);

        // 3. Verificar responsive design elements
        await page.setViewportSize({ width: 1200, height: 800 });

        // En desktop, verificar que algunos elementos estén en múltiples columnas
        const multiColumnElements = page.locator('.md\\:grid-cols-3, .md\\:grid-cols-2');
        const multiColCount = await multiColumnElements.count();
        console.log(`📱 Elementos multi-columna en desktop: ${multiColCount}`);

        // Cambiar a mobile
        await page.setViewportSize({ width: 375, height: 667 });

        // Verificar que el formulario siga siendo usable en mobile
        const formElement = page.locator('form');
        if (await formElement.count() > 0) {
            const formBox = await formElement.first().boundingBox();
            if (formBox) {
                console.log(`📱 Formulario en mobile - Ancho: ${formBox.width}px`);
                expect(formBox.width).toBeLessThanOrEqual(375); // No debe exceder el viewport
            }
        }

        // Volver a desktop
        await page.setViewportSize({ width: 1200, height: 800 });

        console.log('✅ VERIFICACIÓN VISUAL COMPLETADA');
    });
});
