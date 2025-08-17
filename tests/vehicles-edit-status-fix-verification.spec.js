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

test.describe('Vehicle Edit Form Status Fix Verification', () => {
    test('Verify edit form works without estatus_id field', async ({ page }) => {
        await login(page);

        console.log('\n🔧 VERIFICANDO SOLUCIÓN DEL PROBLEMA ESTATUS_ID...\n');

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

        console.log('📝 Navegando al formulario de edición...');
        await editLink.click();
        await page.waitForLoadState('networkidle');

        // Verificar que el formulario carga sin errores de estatus_id
        const currentUrl = page.url();
        console.log(`📍 URL actual: ${currentUrl}`);

        // Buscar errores relacionados con estatus_id en la página
        const statusIdErrors = await page.$$eval('.bg-red-100, .border-red-400, .text-red-700, .alert-danger, .error, .invalid-feedback', elements =>
            elements.map(el => el.textContent?.trim() || '').filter(text =>
                text && text.toLowerCase().includes('estatus') &&
                (text.toLowerCase().includes('requerido') ||
                    text.toLowerCase().includes('required') ||
                    text.toLowerCase().includes('obligatorio'))
            )
        );

        console.log('🔍 ERRORES RELACIONADOS CON ESTATUS EN LA PÁGINA:');
        if (statusIdErrors.length === 0) {
            console.log('   ✅ No se encontraron errores de estatus en la carga inicial');
        } else {
            statusIdErrors.forEach((error, index) => {
                console.log(`   ❌ ${index + 1}. "${error}"`);
            });
        }

        // Intentar hacer una edición simple
        console.log('\n🔄 PROBANDO EDICIÓN SIMPLE...');

        // Hacer un cambio menor en observaciones
        const observacionesField = page.locator('textarea[name="observaciones"]');
        await observacionesField.clear();
        await observacionesField.fill('Test de edición sin estatus_id - ' + new Date().toISOString());

        console.log('📝 Campo de observaciones actualizado');

        // Enviar el formulario
        const submitBtn = page.locator('button[type="submit"]');
        await submitBtn.click();

        console.log('🚀 Formulario enviado');

        // Esperar respuesta
        await page.waitForLoadState('networkidle', { timeout: 10000 });

        const finalUrl = page.url();
        console.log(`📍 URL final: ${finalUrl}`);

        // Verificar si hay errores de validación después del envío
        const validationErrors = await page.$$eval('.bg-red-100, .border-red-400, .text-red-700, .alert-danger, .error, .invalid-feedback', elements =>
            elements.map(el => el.textContent?.trim() || '').filter(text =>
                text && text.toLowerCase().includes('estatus') &&
                (text.toLowerCase().includes('requerido') ||
                    text.toLowerCase().includes('required') ||
                    text.toLowerCase().includes('obligatorio'))
            )
        );

        console.log('\n🎯 ERRORES DE VALIDACIÓN DESPUÉS DEL ENVÍO:');
        if (validationErrors.length === 0) {
            console.log('   ✅ No se encontraron errores de validación de estatus');
        } else {
            validationErrors.forEach((error, index) => {
                console.log(`   ❌ ${index + 1}. "${error}"`);
            });
        }

        // Verificar si hay mensaje de éxito
        const successMessages = await page.$$eval('.bg-green-100, .alert-success, .success', elements =>
            elements.map(el => el.textContent?.trim() || '').filter(text => text)
        );

        console.log('\n🎉 MENSAJES DE ÉXITO:');
        if (successMessages.length > 0) {
            successMessages.forEach((msg, index) => {
                console.log(`   ✅ ${index + 1}. "${msg}"`);
            });
        } else {
            console.log('   ⚠️ No se encontraron mensajes de éxito');
        }

        // Verificar que no redirigió a edit por error
        const isStillInEdit = finalUrl.includes('/edit');
        console.log(`🔄 ¿Sigue en página de edición?: ${isStillInEdit ? 'SÍ (puede indicar error)' : 'NO (correcto)'}`);

        // Conclusion
        console.log('\n🏆 RESULTADO DE LA VERIFICACIÓN:');
        if (validationErrors.length === 0 && !isStillInEdit) {
            console.log('   ✅ PROBLEMA SOLUCIONADO: El formulario funciona sin estatus_id');
            console.log('   ✅ No hay errores de validación de estatus');
            console.log('   ✅ La edición se completó exitosamente');
        } else {
            console.log('   ❌ PROBLEMA PERSISTE: Aún hay errores relacionados con estatus');
        }

        // Test assertions
        expect(validationErrors.length).toBe(0);
        expect(isStillInEdit).toBe(false);
    });

    test('Verify controller validation rules are correct', async ({ page }) => {
        console.log('\n🔧 VERIFICANDO REGLAS DE VALIDACIÓN DEL CONTROLADOR...\n');

        // Esta prueba simula enviar datos incompletos para verificar que 
        // estatus_id ya no sea requerido

        await login(page);

        await page.goto('http://127.0.0.1:8001/vehiculos');
        await page.waitForLoadState('networkidle');

        const editLink = page.locator('a[href*="/edit"]').first();
        if (await editLink.count() > 0) {
            await editLink.click();
            await page.waitForLoadState('networkidle');

            // Vaciar todos los campos requeridos excepto estatus para probar validación
            await page.fill('input[name="marca"]', '');

            const submitBtn = page.locator('button[type="submit"]');
            await submitBtn.click();
            await page.waitForLoadState('networkidle');

            // Buscar errores de validación
            const allErrors = await page.$$eval('*', elements =>
                Array.from(elements)
                    .map(el => el.textContent || '')
                    .filter(text => text.toLowerCase().includes('obligatori') ||
                        text.toLowerCase().includes('requerido') ||
                        text.toLowerCase().includes('required'))
            );

            console.log('📝 ERRORES DE VALIDACIÓN ENCONTRADOS:');
            allErrors.forEach((error, index) => {
                console.log(`   ${index + 1}. "${error}"`);
            });

            // Verificar que NO hay error de estatus_id
            const hasEstatusError = allErrors.some(error =>
                error.toLowerCase().includes('estatus') &&
                (error.toLowerCase().includes('requerido') ||
                    error.toLowerCase().includes('obligatorio') ||
                    error.toLowerCase().includes('required'))
            );

            console.log(`\n🎯 ¿Error de estatus encontrado?: ${hasEstatusError ? 'SÍ (MAL)' : 'NO (BIEN)'}`);

            expect(hasEstatusError).toBe(false);
        }
    });
});
