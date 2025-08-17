import { test, expect } from '@playwright/test';

// Helper function for authentication
async function login(page) {
    await page.goto('http://127.0.0.1:8001/login');
    await page.waitForLoadState('networkidle');

    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');

    await page.waitForLoadState('networkidle');
}

test.describe('Vehicle Edit Form Status Field Check', () => {
    test('Check if estatus_id field appears as required in edit form', async ({ page }) => {
        await login(page);

        console.log('\n🔍 VERIFICANDO CAMPO ESTATUS_ID EN FORMULARIO DE EDICIÓN...\n');

        // Ir a la lista de vehículos
        await page.goto('http://127.0.0.1:8001/vehiculos');
        await page.waitForLoadState('networkidle');

        // Buscar un vehículo para editar
        const editLink = page.locator('a[href*="/edit"]').first();
        const editLinkExists = await editLink.count() > 0;

        if (!editLinkExists) {
            console.log('⚠️ No se encontró vehículo para editar');
            return;
        }

        await editLink.click();
        await page.waitForLoadState('networkidle');

        console.log('📋 ANALIZANDO FORMULARIO DE EDICIÓN...');

        // Verificar si existe campo estatus_id
        const estatusIdField = page.locator('input[name="estatus_id"], select[name="estatus_id"]');
        const estatusIdExists = await estatusIdField.count() > 0;

        console.log(`🔍 Campo estatus_id presente: ${estatusIdExists ? 'SÍ' : 'NO'}`);

        if (estatusIdExists) {
            const isRequired = await estatusIdField.getAttribute('required');
            console.log(`📝 Es requerido: ${isRequired !== null ? 'SÍ' : 'NO'}`);

            const fieldType = await estatusIdField.getAttribute('type') || await estatusIdField.evaluate(el => el.tagName.toLowerCase());
            console.log(`🏷️ Tipo de campo: ${fieldType}`);
        }

        // Verificar si existe campo estatus (sin _id)
        const estatusField = page.locator('input[name="estatus"], select[name="estatus"]');
        const estatusExists = await estatusField.count() > 0;

        console.log(`🔍 Campo estatus presente: ${estatusExists ? 'SÍ' : 'NO'}`);

        if (estatusExists) {
            const isRequired = await estatusField.getAttribute('required');
            console.log(`📝 Es requerido: ${isRequired !== null ? 'SÍ' : 'NO'}`);

            const fieldType = await estatusField.getAttribute('type') || await estatusField.evaluate(el => el.tagName.toLowerCase());
            console.log(`🏷️ Tipo de campo: ${fieldType}`);
        }

        // Buscar todos los campos relacionados con estatus
        const allStatusFields = await page.$$eval('input, select', elements =>
            elements.filter(el =>
                (el.name && el.name.toLowerCase().includes('estatus')) ||
                (el.id && el.id.toLowerCase().includes('estatus'))
            ).map(el => ({
                name: el.name,
                id: el.id,
                type: el.type || el.tagName.toLowerCase(),
                required: el.required,
                value: el.value
            }))
        );

        console.log('\n📝 TODOS LOS CAMPOS RELACIONADOS CON ESTATUS:');
        if (allStatusFields.length === 0) {
            console.log('   ✅ No se encontraron campos de estatus');
        } else {
            allStatusFields.forEach((field, index) => {
                console.log(`   ${index + 1}. ${field.name || field.id} (${field.type}) - Requerido: ${field.required ? 'SÍ' : 'NO'}`);
            });
        }

        // Buscar campos con asterisco (indicador de requerido)
        const requiredLabels = await page.$$eval('label', labels =>
            labels.filter(label => label.textContent.includes('*') && label.textContent.toLowerCase().includes('estatus'))
                .map(label => ({
                    text: label.textContent.trim(),
                    for: label.getAttribute('for')
                }))
        );

        console.log('\n🏷️ ETIQUETAS CON ASTERISCO RELACIONADAS CON ESTATUS:');
        if (requiredLabels.length === 0) {
            console.log('   ✅ No se encontraron etiquetas de estatus con asterisco');
        } else {
            requiredLabels.forEach((label, index) => {
                console.log(`   ${index + 1}. "${label.text}" (for: ${label.for})`);
            });
        }

        // Verificar errores de validación relacionados con estatus_id
        const validationErrors = await page.$$eval('.error, .invalid-feedback, .text-red-500, .text-red-600, .text-red-700', errors =>
            errors.filter(error => error.textContent.toLowerCase().includes('estatus'))
                .map(error => error.textContent.trim())
        );

        console.log('\n❌ ERRORES DE VALIDACIÓN RELACIONADOS CON ESTATUS:');
        if (validationErrors.length === 0) {
            console.log('   ✅ No se encontraron errores de validación de estatus');
        } else {
            validationErrors.forEach((error, index) => {
                console.log(`   ${index + 1}. "${error}"`);
            });
        }

        // Conclusión
        console.log('\n🏆 RESULTADO:');
        if (estatusIdExists) {
            console.log('   ❌ PROBLEMA ENCONTRADO: Campo estatus_id presente en el formulario');
            console.log('   🔧 ACCIÓN REQUERIDA: Eliminar campo estatus_id del formulario');
        } else {
            console.log('   ✅ CORRECTO: No se encontró campo estatus_id problemático');
        }

        // Test assertions
        expect(estatusIdExists).toBe(false); // No debería existir estatus_id
    });

    test('Check form validation rules for status fields', async ({ page }) => {
        await login(page);

        console.log('\n🔍 VERIFICANDO REGLAS DE VALIDACIÓN...\n');

        // Ir al formulario de edición
        await page.goto('http://127.0.0.1:8001/vehiculos');
        await page.waitForLoadState('networkidle');

        const editLink = page.locator('a[href*="/edit"]').first();
        if (await editLink.count() > 0) {
            await editLink.click();
            await page.waitForLoadState('networkidle');

            // Intentar enviar el formulario sin llenar nada para ver errores de validación
            const submitBtn = page.locator('button[type="submit"]');
            await submitBtn.click();
            await page.waitForLoadState('networkidle');

            // Buscar mensajes de error específicos de estatus_id
            const errorMessages = await page.$$eval('*', elements =>
                Array.from(elements)
                    .map(el => el.textContent)
                    .filter(text => text && text.toLowerCase().includes('estatus') && text.toLowerCase().includes('requerido'))
            );

            console.log('📝 MENSAJES DE ERROR RELACIONADOS CON ESTATUS:');
            if (errorMessages.length === 0) {
                console.log('   ✅ No se encontraron errores de validación de estatus');
            } else {
                errorMessages.forEach((msg, index) => {
                    console.log(`   ${index + 1}. "${msg}"`);
                });
            }

            // Test assertion
            const hasEstatusIdError = errorMessages.some(msg => msg.toLowerCase().includes('estatus_id'));
            expect(hasEstatusIdError).toBe(false);
        }
    });
});
