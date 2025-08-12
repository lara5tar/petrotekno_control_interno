import { test, expect } from '@playwright/test';

test.describe('Diagnóstico del Modal - Asignar Vehículos', () => {
    test('diagnosticar por qué el modal no se abre al hacer clic', async ({ page }) => {
        // Capturar todos los logs de la consola
        const consoleLogs = [];
        page.on('console', msg => {
            consoleLogs.push(`${msg.type()}: ${msg.text()}`);
        });

        // Capturar errores de JavaScript
        const jsErrors = [];
        page.on('pageerror', error => {
            jsErrors.push(error.message);
        });

        // Login
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Ir a crear obra
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForLoadState('domcontentloaded');

        // Esperar a que Alpine.js se cargue
        await page.waitForTimeout(3000);

        console.log('=== ESTADO INICIAL ===');

        // Verificar que Alpine.js está cargado
        const alpineLoaded = await page.evaluate(() => {
            return typeof Alpine !== 'undefined' && Alpine.version;
        });
        console.log('Alpine.js cargado:', alpineLoaded);

        // Verificar el estado inicial del controlador
        const initialState = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            if (!element || !element._x_dataStack) return { error: 'Controlador no encontrado' };
            return {
                showVehicleModal: element._x_dataStack[0].showVehicleModal,
                availableVehicles: element._x_dataStack[0].availableVehicles?.length,
                assignedVehicles: element._x_dataStack[0].assignedVehicles?.length
            };
        });
        console.log('Estado inicial del controlador:', initialState);

        // Encontrar el botón "Asignar Vehículo"
        const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
        const buttonExists = await assignButton.count();
        const buttonVisible = await assignButton.isVisible();
        const buttonEnabled = await assignButton.isEnabled();

        console.log('Botón "Asignar Vehículo":', {
            exists: buttonExists,
            visible: buttonVisible,
            enabled: buttonEnabled
        });

        // Verificar los atributos del botón
        const buttonAttributes = await assignButton.evaluate(el => {
            return {
                onclick: el.getAttribute('@click'),
                disabled: el.disabled,
                classes: el.className,
                innerHTML: el.innerHTML
            };
        });
        console.log('Atributos del botón:', buttonAttributes);

        // Tomar screenshot antes del click
        await page.screenshot({ path: 'debug-antes-del-click.png', fullPage: true });

        console.log('=== SIMULANDO CLICK ===');

        // Hacer click en el botón
        await assignButton.click();

        // Esperar un momento después del click
        await page.waitForTimeout(2000);

        // Verificar el estado después del click
        const stateAfterClick = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            if (!element || !element._x_dataStack) return { error: 'Controlador no encontrado' };
            return {
                showVehicleModal: element._x_dataStack[0].showVehicleModal,
                availableVehicles: element._x_dataStack[0].availableVehicles?.length
            };
        });
        console.log('Estado después del click:', stateAfterClick);

        // Verificar si el modal existe en el DOM
        const modalElements = await page.evaluate(() => {
            const modals = document.querySelectorAll('[role="dialog"]');
            const results = [];
            modals.forEach((modal, index) => {
                results.push({
                    index,
                    visible: modal.offsetParent !== null,
                    display: window.getComputedStyle(modal).display,
                    xShow: modal.getAttribute('x-show'),
                    xCloak: modal.hasAttribute('x-cloak'),
                    innerHTML: modal.innerHTML.substring(0, 100) + '...'
                });
            });
            return results;
        });
        console.log('Elementos modal en DOM:', modalElements);

        // Verificar si hay elementos con x-show="showVehicleModal"
        const xShowElements = await page.evaluate(() => {
            const elements = document.querySelectorAll('[x-show*="showVehicleModal"]');
            const results = [];
            elements.forEach((el, index) => {
                results.push({
                    index,
                    tagName: el.tagName,
                    xShow: el.getAttribute('x-show'),
                    visible: el.offsetParent !== null,
                    display: window.getComputedStyle(el).display,
                    opacity: window.getComputedStyle(el).opacity
                });
            });
            return results;
        });
        console.log('Elementos con x-show="showVehicleModal":', xShowElements);

        // Intentar ejecutar openVehicleModal() directamente
        console.log('=== PRUEBA DIRECTA ===');
        const directResult = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            if (element && element._x_dataStack && element._x_dataStack[0].openVehicleModal) {
                try {
                    element._x_dataStack[0].openVehicleModal();
                    return {
                        success: true,
                        newState: element._x_dataStack[0].showVehicleModal
                    };
                } catch (error) {
                    return {
                        success: false,
                        error: error.message
                    };
                }
            }
            return { success: false, error: 'Función no encontrada' };
        });
        console.log('Resultado de openVehicleModal() directo:', directResult);

        // Tomar screenshot después del click
        await page.screenshot({ path: 'debug-despues-del-click.png', fullPage: true });

        // Verificar nuevamente el estado del modal
        await page.waitForTimeout(1000);
        const finalModalState = await page.evaluate(() => {
            const modal = document.querySelector('[role="dialog"]');
            if (!modal) return { error: 'Modal no encontrado' };

            return {
                exists: true,
                visible: modal.offsetParent !== null,
                display: window.getComputedStyle(modal).display,
                opacity: window.getComputedStyle(modal).opacity,
                zIndex: window.getComputedStyle(modal).zIndex,
                position: window.getComputedStyle(modal).position
            };
        });
        console.log('Estado final del modal:', finalModalState);

        // Imprimir todos los logs y errores
        console.log('\n=== LOGS DE CONSOLA ===');
        consoleLogs.forEach(log => console.log(log));

        console.log('\n=== ERRORES DE JAVASCRIPT ===');
        jsErrors.forEach(error => console.log('ERROR:', error));

        // Test assertion - el modal debería estar visible después del click
        const modalVisible = await page.locator('[role="dialog"]').isVisible();
        console.log('\n=== RESULTADO FINAL ===');
        console.log('Modal visible después de todas las pruebas:', modalVisible);
    });
});