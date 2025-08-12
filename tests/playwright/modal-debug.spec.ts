import { test, expect } from '@playwright/test';

test.describe('Diagnóstico del Modal de Vehículos', () => {
    test('diagnóstico básico del modal', async ({ page }) => {
        // Login
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Ir a crear obra
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForLoadState('domcontentloaded');

        // Esperar a que Alpine.js se inicialice
        await page.waitForTimeout(2000);

        // Verificar que Alpine.js está cargado
        const alpineCheck = await page.evaluate(() => {
            return typeof window.Alpine !== 'undefined';
        });
        console.log('Alpine.js está disponible:', alpineCheck);

        // Verificar que el controlador está inicializado
        const controllerCheck = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            return element && element._x_dataStack;
        });
        console.log('Controlador Alpine inicializado:', controllerCheck);

        // Buscar el botón de asignar vehículo
        const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
        console.log('Botón encontrado:', await assignButton.count());

        // Verificar si el botón está visible y habilitado
        if (await assignButton.count() > 0) {
            const isVisible = await assignButton.isVisible();
            const isEnabled = await assignButton.isEnabled();
            console.log('Botón visible:', isVisible);
            console.log('Botón habilitado:', isEnabled);

            // Verificar el estado inicial del modal
            const modal = page.locator('[role="dialog"]');
            const modalVisible = await modal.isVisible();
            console.log('Modal visible antes del click:', modalVisible);

            // Hacer click en el botón
            await assignButton.click();

            // Esperar un poco para que Alpine.js reaccione
            await page.waitForTimeout(1000);

            // Verificar el estado después del click
            const modalVisibleAfter = await modal.isVisible();
            console.log('Modal visible después del click:', modalVisibleAfter);

            // Verificar si hay errores en la consola
            const logs = [];
            page.on('console', msg => logs.push(msg.text()));

            // Tomar screenshot para debugging
            await page.screenshot({ path: 'debug-modal-state.png', fullPage: true });
        }

        // Verificar si hay vehículos disponibles
        const vehicleInfo = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            if (element && element._x_dataStack && element._x_dataStack[0]) {
                return {
                    allVehicles: element._x_dataStack[0].allVehicles,
                    availableVehicles: element._x_dataStack[0].availableVehicles
                };
            }
            return null;
        });
        console.log('Información de vehículos:', vehicleInfo);
    });
});