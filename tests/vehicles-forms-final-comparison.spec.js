import { test, expect } from '@playwright/test';

test.describe('Vehicle Forms Comparison - Create vs Edit', () => {
    const baseURL = 'http://127.0.0.1:8001';

    // Helper para login
    async function login(page) {
        await page.goto(`${baseURL}/login`);
        await page.fill('[name="email"]', 'admin@petrotekno.com');
        await page.fill('[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle', { timeout: 5000 });
    }

    // Helper para capturar estructura del formulario
    async function captureFormStructure(page) {
        await page.waitForLoadState('networkidle');

        // Capturar título principal del header - simplificado y más robusto
        let title = 'No title found';
        try {
            title = await page.locator('h1').first().textContent({ timeout: 5000 }) || title;
            if (title.includes('Menú Principal')) {
                title = await page.locator('h1').nth(1).textContent({ timeout: 2000 }) || title;
            }
        } catch (e) {
            console.log('⚠️ No se pudo capturar el título:', e.message);
        }

        // Capturar secciones del formulario
        const sections = await page.locator('#vehiculoForm h3, #vehiculoForm h4').allTextContents();

        // Capturar campos del formulario principal únicamente
        const fields = await page.$$eval('#vehiculoForm input, #vehiculoForm select, #vehiculoForm textarea', elements =>
            elements.map(el => `${el.name || el.id}(${el.type || el.tagName.toLowerCase()})`).filter(f => f !== '()')
        );

        // Capturar botones
        const buttons = await page.locator('#vehiculoForm button[type="submit"], #vehiculoForm input[type="submit"]').allTextContents();

        // Capturar secciones de documentos
        const documentSections = await page.locator('#vehiculoForm label').allTextContents();

        // Capturar inputs de archivos
        const fileInputs = await page.$$eval('#vehiculoForm input[type="file"]', elements =>
            elements.map(el => el.name || el.id).filter(name => name)
        );

        // Estructura detallada de campos
        const formFields = await page.$$eval('#vehiculoForm input, #vehiculoForm select, #vehiculoForm textarea', elements =>
            elements.map(el => ({
                name: el.name || el.id,
                type: el.type || el.tagName.toLowerCase(),
                required: el.required,
                placeholder: el.placeholder
            })).filter(f => f.name)
        );

        return {
            title: title?.trim() || 'No title found',
            sections: sections.filter(s => s.trim()),
            formFields,
            buttons: buttons.filter(b => b.trim()),
            documentSections: documentSections.filter(s => s.trim()),
            fileInputs
        };
    } test('Compare CREATE vs EDIT form structures', async ({ page }) => {
        await login(page);

        // ========== ANALIZAR FORMULARIO DE CREACIÓN ==========
        console.log('\n🔍 ANALIZANDO FORMULARIO DE CREACIÓN...');
        await page.goto(`${baseURL}/vehiculos/create`);
        await page.waitForLoadState('networkidle', { timeout: 10000 });

        const createStructure = await captureFormStructure(page);

        console.log('📋 ESTRUCTURA CREATE:');
        console.log(`   Título: "${createStructure.title}"`);
        console.log(`   Secciones (${createStructure.sections.length}):`, createStructure.sections);
        console.log(`   Campos (${createStructure.formFields.length}):`, createStructure.formFields.map(f => `${f.name}(${f.type})`));
        console.log(`   Botones (${createStructure.buttons.length}):`, createStructure.buttons);
        console.log(`   Docs (${createStructure.documentSections.length}):`, createStructure.documentSections);
        console.log(`   Archivos (${createStructure.fileInputs.length}):`, createStructure.fileInputs);

        // ========== ANALIZAR FORMULARIO DE EDICIÓN ==========
        console.log('\n🔍 ANALIZANDO FORMULARIO DE EDICIÓN...');

        // Buscar un vehículo para editar
        await page.goto(`${baseURL}/vehiculos`);
        await page.waitForLoadState('networkidle', { timeout: 5000 });

        const editLink = page.locator('a[href*="/edit"]').first();
        const editLinkExists = await editLink.count() > 0;

        if (!editLinkExists) {
            console.log('⚠️ No se encontró vehículo para editar. Creando uno...');

            // Crear un vehículo de prueba rápido
            await page.goto(`${baseURL}/vehiculos/create`);
            await page.fill('[name="marca"]', 'Toyota');
            await page.fill('[name="modelo"]', 'Hilux');
            await page.fill('[name="anio"]', '2023');
            await page.fill('[name="n_serie"]', 'TEST123456789');
            await page.fill('[name="placas"]', 'TEST-123');
            await page.fill('[name="kilometraje_actual"]', '10000');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            // Volver a buscar link de editar
            await page.goto(`${baseURL}/vehiculos`);
            await page.waitForLoadState('networkidle');
        }

        // Ir a editar
        const editLinkFinal = page.locator('a[href*="/edit"]').first();
        await editLinkFinal.click();
        await page.waitForLoadState('networkidle', { timeout: 10000 });

        const editStructure = await captureFormStructure(page);

        console.log('\n📋 ESTRUCTURA EDIT:');
        console.log(`   Título: "${editStructure.title}"`);
        console.log(`   Secciones (${editStructure.sections.length}):`, editStructure.sections);
        console.log(`   Campos (${editStructure.formFields.length}):`, editStructure.formFields.map(f => `${f.name}(${f.type})`));
        console.log(`   Botones (${editStructure.buttons.length}):`, editStructure.buttons);
        console.log(`   Docs (${editStructure.documentSections.length}):`, editStructure.documentSections);
        console.log(`   Archivos (${editStructure.fileInputs.length}):`, editStructure.fileInputs);

        // ========== COMPARACIÓN DETALLADA ==========
        console.log('\n🔄 COMPARANDO ESTRUCTURAS...');

        // 1. Verificar títulos (deben ser diferentes pero apropiados)
        console.log(`📝 Título CREATE: "${createStructure.title}"`);
        console.log(`📝 Título EDIT: "${editStructure.title}"`);

        // Verificamos que contengan las palabras clave correctas
        expect(createStructure.title.toLowerCase()).toContain('agregar');
        expect(editStructure.title.toLowerCase()).toContain('editar');
        console.log('✅ Títulos correctos y diferentes');

        // 2. Verificar que tienen las mismas secciones
        expect(editStructure.sections.length).toBe(createStructure.sections.length);
        for (let i = 0; i < createStructure.sections.length; i++) {
            expect(editStructure.sections[i]).toBe(createStructure.sections[i]);
        }
        console.log('✅ Secciones idénticas');

        // 3. Verificar campos del formulario (EDIT puede tener _method adicional)
        const createVisibleFields = createStructure.formFields.filter(f => f.type !== 'hidden');
        const editVisibleFields = editStructure.formFields.filter(f => f.type !== 'hidden');

        // Los campos visibles deben ser idénticos
        expect(editVisibleFields.length).toBe(createVisibleFields.length);

        // EDIT debería tener un campo _method adicional (hidden)
        const editMethodField = editStructure.formFields.find(f => f.name === '_method');
        expect(editMethodField).toBeDefined();
        expect(editMethodField.type).toBe('hidden');

        console.log(`✅ CREATE tiene ${createVisibleFields.length} campos visibles`);
        console.log(`✅ EDIT tiene ${editVisibleFields.length} campos visibles + _method hidden`);

        const createFieldNames = createVisibleFields.map(f => f.name).sort();
        const editFieldNames = editVisibleFields.map(f => f.name).sort();

        for (let i = 0; i < createFieldNames.length; i++) {
            expect(editFieldNames[i]).toBe(createFieldNames[i]);
        }
        console.log('✅ Campos de formulario idénticos');

        // 4. Verificar tipos de campos
        for (const createField of createStructure.formFields) {
            const editField = editStructure.formFields.find(f => f.name === createField.name);
            expect(editField).toBeDefined();
            expect(editField.type).toBe(createField.type);
            expect(editField.required).toBe(createField.required);
        }
        console.log('✅ Tipos y propiedades de campos idénticos');

        // 5. Verificar secciones de documentos
        expect(editStructure.documentSections.length).toBe(createStructure.documentSections.length);
        for (let i = 0; i < createStructure.documentSections.length; i++) {
            expect(editStructure.documentSections[i]).toBe(createStructure.documentSections[i]);
        }
        console.log('✅ Secciones de documentos idénticas');

        // 6. Verificar inputs de archivo
        expect(editStructure.fileInputs.length).toBe(createStructure.fileInputs.length);
        const createFileInputs = createStructure.fileInputs.sort();
        const editFileInputs = editStructure.fileInputs.sort();
        for (let i = 0; i < createFileInputs.length; i++) {
            expect(editFileInputs[i]).toBe(createFileInputs[i]);
        }
        console.log('✅ Inputs de archivo idénticos');

        // 7. Verificar que los botones son apropiados
        expect(editStructure.buttons.length).toBeGreaterThanOrEqual(1); // Al menos Actualizar
        expect(createStructure.buttons.length).toBeGreaterThanOrEqual(1); // Al menos Guardar
        console.log(`✅ CREATE tiene ${createStructure.buttons.length} botón(es): ${createStructure.buttons.join(', ')}`);
        console.log(`✅ EDIT tiene ${editStructure.buttons.length} botón(es): ${editStructure.buttons.join(', ')}`);

        // ========== RESULTADO FINAL ==========
        console.log('\n🎉 RESULTADO DE LA COMPARACIÓN:');
        console.log('===============================');
        console.log('✅ Los formularios CREATE y EDIT tienen estructura IDÉNTICA');
        console.log('✅ Mismo número de secciones, campos y elementos');
        console.log('✅ Mismos tipos de datos y validaciones');
        console.log('✅ Mismos elementos de carga de archivos');
        console.log('✅ Títulos apropiados para cada contexto');
        console.log('\n🏆 EL DISEÑO ESTÁ CORRECTAMENTE UNIFICADO');

        // Screenshots finales para documentación
        await page.goto(`${baseURL}/vehiculos/create`);
        await page.waitForLoadState('networkidle');
        await page.screenshot({
            path: 'final-create-form.png',
            fullPage: true
        });

        await editLinkFinal.click();
        await page.waitForLoadState('networkidle');
        await page.screenshot({
            path: 'final-edit-form.png',
            fullPage: true
        });

        console.log('\n📸 Screenshots finales guardados:');
        console.log('   - final-create-form.png');
        console.log('   - final-edit-form.png');
    });

    test('Verify responsive design consistency', async ({ page }) => {
        await login(page);

        console.log('\n📱 VERIFICANDO DISEÑO RESPONSIVE...');

        // Tamaños de viewport a probar
        const viewports = [
            { name: 'Mobile', width: 375, height: 667 },
            { name: 'Tablet', width: 768, height: 1024 },
            { name: 'Desktop', width: 1200, height: 800 }
        ];

        for (const viewport of viewports) {
            console.log(`\n🖥️ Probando ${viewport.name} (${viewport.width}x${viewport.height})...`);

            await page.setViewportSize({ width: viewport.width, height: viewport.height });

            // Probar CREATE
            await page.goto(`${baseURL}/vehiculos/create`);
            await page.waitForLoadState('networkidle');

            const formVisible = await page.locator('#vehiculoForm').isVisible();
            const sectionsVisible = await page.locator('h3, h4').count();
            const fieldsVisible = await page.locator('input:visible, select:visible, textarea:visible').count();

            console.log(`   CREATE - Formulario visible: ${formVisible}, Secciones: ${sectionsVisible}, Campos: ${fieldsVisible}`);

            expect(formVisible).toBe(true);
            expect(sectionsVisible).toBeGreaterThan(3);
            expect(fieldsVisible).toBeGreaterThan(10);

            // Probar EDIT
            await page.goto(`${baseURL}/vehiculos`);
            await page.waitForLoadState('networkidle');

            const editLink = page.locator('a[href*="/edit"]').first();
            if (await editLink.count() > 0) {
                await editLink.click();
                await page.waitForLoadState('networkidle');

                const editFormVisible = await page.locator('#vehiculoForm').isVisible();
                const editSectionsVisible = await page.locator('#vehiculoForm h3, #vehiculoForm h4').count();
                const editFieldsVisible = await page.locator('#vehiculoForm input:visible, #vehiculoForm select:visible, #vehiculoForm textarea:visible').count();

                console.log(`   EDIT - Formulario visible: ${editFormVisible}, Secciones: ${editSectionsVisible}, Campos: ${editFieldsVisible}`);

                expect(editFormVisible).toBe(true);
                expect(editSectionsVisible).toBe(sectionsVisible); // Mismo número que create
                expect(editFieldsVisible).toBeGreaterThan(10);
            }
        }

        console.log('✅ Diseño responsive verificado en todos los tamaños');
    });
});
