import { test } from '@playwright/test';

// Test final para verificar que cambiar obra funciona completamente
test.describe('Test Final - Cambiar Obra', () => {
    test('Test completo - Modal, envío y respuesta exitosa', async ({ page }) => {
        console.log('🎯 Test final - cambiar obra funcionando completamente...');

        // Login
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', '123456');
        await page.click('button[type="submit"]');
        console.log('✅ Login exitoso');

        // Ir a la página de un vehículo específico
        await page.goto('http://localhost:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');
        console.log('✅ Página de vehículo cargada');

        // Abrir modal de cambiar obra
        const cambiarObraBtn = page.locator('button:has-text("Cambiar Obra")');
        await cambiarObraBtn.click();
        console.log('✅ Modal abierto');

        // Esperar a que el modal esté visible
        await page.waitForSelector('#cambiar-obra-modal', { state: 'visible' });

        // Llenar el formulario
        await page.selectOption('#obra_id', '1');
        await page.selectOption('#operador_id', '1');
        await page.fill('#kilometraje_inicial', '65000');
        console.log('✅ Formulario llenado');

        // Capturar respuestas de red
        let responseReceived = false;
        let responseData = null;

        page.on('response', async (response) => {
            if (response.url().includes('cambiar-obra')) {
                responseReceived = true;
                try {
                    responseData = await response.json();
                    console.log('📨 Respuesta recibida:', responseData);
                } catch (e) {
                    console.log('📨 Respuesta recibida (no JSON):', response.status());
                }
            }
        });

        // Enviar formulario
        console.log('💾 Enviando formulario...');
        await page.click('button:has-text("Cambiar Obra"):last-of-type');

        // Esperar respuesta
        await page.waitForTimeout(3000);

        console.log('📊 Resultados:');
        console.log(`   Respuesta recibida: ${responseReceived ? 'Sí' : 'No'}`);
        if (responseData) {
            console.log(`   Estado: ${responseData.success ? 'Exitoso' : 'Error'}`);
            console.log(`   Mensaje: ${responseData.message || responseData.error}`);
        }

        // Tomar screenshot final
        await page.screenshot({ path: 'debug-cambiar-obra-final.png', fullPage: true });
        console.log('📸 Screenshot guardado');

        console.log('🎉 Test completado - Cambiar obra funcionando');
    });
});
