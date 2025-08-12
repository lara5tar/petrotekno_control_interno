import { test, expect } from '@playwright/test';

test.describe('Diagnóstico Final del Modal', () => {
    test('test manual del flujo completo', async ({ page }) => {
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
        await page.waitForTimeout(3000);

        // Verificar que el formulario se carga
        await expect(page.locator('h2:has-text("Agregar Nueva Obra")')).toBeVisible();

        // Buscar el botón usando diferentes selectores
        const button1 = page.locator('button:has-text("Asignar Vehículo")').first();
        const button2 = page.locator('[x-data="obraFormController()"] button:has-text("Asignar Vehículo")').first();
        const button3 = page.locator('button').filter({ hasText: 'Asignar Vehículo' }).first();

        console.log('Botón método 1:', await button1.count());
        console.log('Botón método 2:', await button2.count());
        console.log('Botón método 3:', await button3.count());

        // Usar el botón que funcione
        const workingButton = await button1.count() > 0 ? button1 : button2;
        await expect(workingButton).toBeVisible();

        // Forzar el click con JavaScript
        await page.evaluate(() => {
            const buttons = document.querySelectorAll('button');
            for (const button of buttons) {
                if (button.textContent.includes('Asignar Vehículo')) {
                    button.click();
                    break;
                }
            }
        });

        await page.waitForTimeout(2000);

        // Verificar si el modal aparece usando diferentes selectores
        const modal1 = page.locator('[role="dialog"]');
        const modal2 = page.locator('div[x-show="showVehicleModal"]');
        const modal3 = page.locator('.fixed.inset-0.z-50');

        const modal1Count = await modal1.count();
        const modal2Count = await modal2.count();
        const modal3Count = await modal3.count();

        console.log('Modal role="dialog":', modal1Count, 'visible:', modal1Count > 0 ? await modal1.isVisible() : false);
        console.log('Modal x-show:', modal2Count, 'visible:', modal2Count > 0 ? await modal2.isVisible() : false);
        console.log('Modal class:', modal3Count, 'visible:', modal3Count > 0 ? await modal3.isVisible() : false);

        // Verificar el estado de Alpine.js
        const alpineState = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            if (element && element._x_dataStack && element._x_dataStack[0]) {
                return {
                    showVehicleModal: element._x_dataStack[0].showVehicleModal,
                    availableVehicles: element._x_dataStack[0].availableVehicles.length
                };
            }
            return null;
        });

        console.log('Estado Alpine después del click:', alpineState);

        // Verificar elementos específicos del modal
        const selectVehicle = page.locator('select[x-model="modalVehicle.vehiculo_id"]');
        const modalHeader = page.locator('h3:has-text("Asignar Vehículo a la Obra")');

        console.log('Select del modal:', await selectVehicle.count(), 'visible:', await selectVehicle.count() > 0 ? await selectVehicle.isVisible() : false);
        console.log('Header del modal:', await modalHeader.count(), 'visible:', await modalHeader.count() > 0 ? await modalHeader.isVisible() : false);

        // Tomar screenshot final
        await page.screenshot({ path: 'debug-modal-final-comprehensive.png', fullPage: true });

        console.log('\n=== LOGS DE LA CONSOLA ===');
        consoleLogs.forEach(log => console.log(log));
    });
});