import { test, expect } from '@playwright/test';

test.describe('Bug del Modal Auto-Abriendo', () => {
    test('detectar si el modal se abre automáticamente al cargar la página', async ({ page }) => {
        // Capturar logs
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

        // Esperar un momento para que se cargue Alpine.js
        await page.waitForTimeout(2000);

        // Verificar el estado inicial del modal INMEDIATAMENTE después de cargar
        const initialModalState = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            return element && element._x_dataStack ? element._x_dataStack[0].showVehicleModal : null;
        });

        console.log('Estado inicial del modal (al cargar):', initialModalState);

        // Verificar si el modal está visible en el DOM inmediatamente
        const modalVisible = await page.locator('[role="dialog"]').isVisible();
        const modalExists = await page.locator('[role="dialog"]').count();

        console.log('Modal visible al cargar:', modalVisible);
        console.log('Modal existe al cargar:', modalExists);

        // Verificar si el botón "Asignar Vehículo" está visible
        const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
        const buttonVisible = await assignButton.isVisible();
        console.log('Botón asignar vehículo visible:', buttonVisible);

        // Tomar screenshot inicial
        await page.screenshot({ path: 'debug-modal-auto-open-inicial.png', fullPage: true });

        // Esperar más tiempo para ver si algo cambia
        await page.waitForTimeout(3000);

        // Verificar nuevamente después de unos segundos
        const laterModalState = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            return element && element._x_dataStack ? element._x_dataStack[0].showVehicleModal : null;
        });

        const laterModalVisible = await page.locator('[role="dialog"]').isVisible();

        console.log('Estado del modal después de 3 segundos:', laterModalState);
        console.log('Modal visible después de 3 segundos:', laterModalVisible);

        // Tomar screenshot después de esperar
        await page.screenshot({ path: 'debug-modal-auto-open-despues.png', fullPage: true });

        // Verificar si hay algún elemento que esté ejecutando openVehicleModal() automáticamente
        const autoTriggers = await page.evaluate(() => {
            // Buscar elementos con @click="openVehicleModal()" 
            const clickElements = document.querySelectorAll('[\\@click*="openVehicleModal"]');
            const results = [];

            clickElements.forEach((el, index) => {
                results.push({
                    index,
                    tagName: el.tagName,
                    textContent: el.textContent?.trim(),
                    onclick: el.getAttribute('@click'),
                    visible: el.offsetParent !== null
                });
            });

            return results;
        });

        console.log('Elementos que pueden activar el modal:', autoTriggers);

        // Imprimir logs de consola
        console.log('\n=== LOGS DE LA CONSOLA ===');
        consoleLogs.forEach(log => console.log(log));

        // El test debe fallar si el modal se abre automáticamente
        expect(initialModalState, 'El modal NO debería abrirse automáticamente al cargar la página').toBe(false);
        expect(modalVisible, 'El modal NO debería ser visible al cargar la página').toBe(false);
    });
});