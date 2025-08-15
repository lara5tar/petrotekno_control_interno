import { test, expect } from '@playwright/test';

test.describe('Test Rápido - Cambiar Obra', () => {
    async function login(page) {
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home', { timeout: 10000 });
    }

    test('Test básico - Modal y envío', async ({ page }) => {
        console.log('🚀 Test rápido de cambiar obra...');

        // Login
        await login(page);
        console.log('✅ Login exitoso');

        // Ir a página del vehículo
        await page.goto('http://localhost:8000/vehiculos/1');
        await expect(page).toHaveTitle(/Detalles del Vehículo/);
        console.log('✅ Página cargada');

        // Abrir modal
        await page.locator('button:has-text("Cambiar Obra")').first().click();

        // Verificar modal
        const modal = page.locator('#cambiar-obra-modal');
        await expect(modal).toBeVisible();
        console.log('✅ Modal abierto');

        // Verificar elementos del formulario
        await expect(page.locator('#obra_id')).toBeVisible();
        await expect(page.locator('#operador_id')).toBeVisible();
        await expect(page.locator('#kilometraje_inicial')).toBeVisible();
        console.log('✅ Elementos del formulario visibles');

        // Llenar formulario básico
        await page.locator('#obra_id').selectOption({ index: 1 });
        await page.locator('#operador_id').selectOption({ index: 1 });
        await page.locator('#kilometraje_inicial').fill('60000');
        console.log('✅ Formulario llenado');

        // Capturar errores
        const errors = [];
        page.on('pageerror', error => {
            errors.push(error.message);
            console.log('❌ Error de página:', error.message);
        });

        // Monitorear respuesta
        let responseStatus = null;
        page.on('response', response => {
            if (response.url().includes('/asignaciones-obra/cambiar-obra')) {
                responseStatus = response.status();
                console.log(`📡 Respuesta: ${response.status()}`);
            }
        });

        // Enviar formulario
        console.log('💾 Enviando formulario...');
        await page.locator('#cambiar-obra-form button[type="submit"]').click();

        // Esperar un poco para la respuesta
        await page.waitForTimeout(3000);

        // Verificar resultados
        console.log('📊 Resultados:');
        console.log(`   Errores de página: ${errors.length}`);
        console.log(`   Estado de respuesta: ${responseStatus || 'No recibida'}`);

        if (errors.length > 0) {
            console.log('❌ Errores encontrados:');
            errors.forEach(error => console.log(`   - ${error}`));
        }

        if (responseStatus) {
            if (responseStatus >= 200 && responseStatus < 300) {
                console.log('✅ Respuesta exitosa');
            } else if (responseStatus >= 400) {
                console.log('❌ Error en el servidor');
            }
        }

        // Tomar screenshot
        await page.screenshot({ path: 'test-cambiar-obra-resultado.png' });
        console.log('📸 Screenshot guardado');

        console.log('🎉 Test completado');
    });
});
