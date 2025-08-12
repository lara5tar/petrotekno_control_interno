import { test, expect } from '@playwright/test';

test.describe('Diagnóstico Avanzado del Modal', () => {
    test('diagnóstico con logs de consola y evaluación directa', async ({ page }) => {
        // Capturar todos los logs de la consola
        const consoleLogs = [];
        page.on('console', msg => {
            consoleLogs.push(`${msg.type()}: ${msg.text()}`);
        });

        // Login
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Ir a crear obra
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForLoadState('domcontentloaded');
        await page.waitForTimeout(3000); // Dar más tiempo para Alpine.js

        // Verificar el estado inicial
        const initialState = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            return element && element._x_dataStack ? element._x_dataStack[0].showVehicleModal : null;
        });
        console.log('Estado inicial del modal:', initialState);

        // Buscar el botón de asignar vehículo
        const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
        await expect(assignButton).toBeVisible();

        // Simular el click del botón directamente con JavaScript
        await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            if (element && element._x_dataStack && element._x_dataStack[0]) {
                console.log('Llamando openVehicleModal() directamente...');
                element._x_dataStack[0].openVehicleModal();
            }
        });

        await page.waitForTimeout(1000);

        // Verificar el estado después de la llamada directa
        const stateAfterDirect = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            return element && element._x_dataStack ? element._x_dataStack[0].showVehicleModal : null;
        });
        console.log('Estado después de llamada directa:', stateAfterDirect);

        // Ahora probar con el click real del botón
        await assignButton.click();
        await page.waitForTimeout(1000);

        const stateAfterClick = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            return element && element._x_dataStack ? element._x_dataStack[0].showVehicleModal : null;
        });
        console.log('Estado después del click real:', stateAfterClick);

        // Verificar si el modal está visible en el DOM
        const modalInDOM = await page.locator('[role="dialog"]').count();
        const modalVisible = await page.locator('[role="dialog"]').isVisible();
        console.log('Modal en DOM:', modalInDOM);
        console.log('Modal visible:', modalVisible);

        // Imprimir todos los logs de la consola
        console.log('\n=== LOGS DE LA CONSOLA ===');
        consoleLogs.forEach(log => console.log(log));

        // Tomar screenshot final
        await page.screenshot({ path: 'debug-modal-final.png', fullPage: true });
    });
});