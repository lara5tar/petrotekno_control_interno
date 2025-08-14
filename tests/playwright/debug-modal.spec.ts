import { test, expect } from '@playwright/test';

test.describe('Debug Modal de Kilometraje', () => {
    test.beforeEach(async ({ page }) => {
        // Navegar a la página de login
        await page.goto('http://127.0.0.1:8001/login');

        // Realizar login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar redirección al home
        await page.waitForURL('**/home');

        // Navegar a la lista de vehículos
        await page.goto('http://127.0.0.1:8001/vehiculos');
        await page.waitForLoadState('networkidle');

        // Hacer clic en el primer vehículo para ver detalles
        await page.click('table tbody tr:first-child a[title="Ver detalles"]');
        await page.waitForLoadState('networkidle');
    });

    test('debug paso a paso del modal', async ({ page }) => {
        // Escuchar errores de JavaScript
        const jsErrors: string[] = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                jsErrors.push(msg.text());
            }
        });

        console.log('=== PASO 1: Verificar página cargada ===');
        const title = await page.title();
        console.log('Título de la página:', title);

        console.log('=== PASO 2: Hacer clic en pestaña Kilometraje ===');
        await page.click('button:has-text("Kilometraje")');
        await page.waitForTimeout(1000);

        console.log('=== PASO 3: Verificar si existe el modal en el DOM ===');
        const modal = page.locator('#kilometraje-modal');
        const modalExists = await modal.count();
        console.log('Modal existe en DOM:', modalExists > 0);

        if (modalExists > 0) {
            const modalClasses = await modal.getAttribute('class');
            console.log('Clases del modal:', modalClasses);
        }

        console.log('=== PASO 4: Verificar si existe el botón ===');
        const boton = page.locator('button:has-text("Capturar Nuevo")');
        const botonCount = await boton.count();
        console.log('Cantidad de botones "Capturar Nuevo":', botonCount);

        if (botonCount > 0) {
            const botonVisible = await boton.first().isVisible();
            console.log('Primer botón es visible:', botonVisible);

            const onclick = await boton.first().getAttribute('onclick');
            console.log('Onclick del botón:', onclick);
        }

        console.log('=== PASO 5: Verificar si la función JavaScript existe ===');
        const funcionExiste = await page.evaluate(() => {
            return typeof (window as any).openKilometrajeModal === 'function';
        });
        console.log('Función openKilometrajeModal existe:', funcionExiste);

        console.log('=== PASO 6: Ejecutar función directamente ===');
        if (funcionExiste) {
            try {
                await page.evaluate(() => {
                    (window as any).openKilometrajeModal();
                });
                console.log('Función ejecutada sin errores');

                // Verificar si el modal se abrió
                await page.waitForTimeout(500);
                const modalClasesDespues = await modal.getAttribute('class');
                console.log('Clases del modal después de ejecutar función:', modalClasesDespues);

                const modalVisible = await modal.isVisible();
                console.log('Modal visible después de función:', modalVisible);

            } catch (error) {
                console.log('Error al ejecutar función:', error);
            }
        }

        console.log('=== PASO 7: Hacer clic en el botón ===');
        if (botonCount > 0) {
            try {
                await boton.first().click();
                console.log('Click en botón realizado');

                await page.waitForTimeout(500);
                const modalClasesFinal = await modal.getAttribute('class');
                console.log('Clases del modal después del click:', modalClasesFinal);

                const modalVisibleFinal = await modal.isVisible();
                console.log('Modal visible después del click:', modalVisibleFinal);

            } catch (error) {
                console.log('Error al hacer click en botón:', error);
            }
        }

        console.log('=== ERRORES DE JAVASCRIPT ===');
        if (jsErrors.length > 0) {
            jsErrors.forEach((error, index) => {
                console.log(`Error ${index + 1}:`, error);
            });
        } else {
            console.log('No se detectaron errores de JavaScript');
        }

        // Capturar screenshot para debug
        await page.screenshot({ path: 'debug-modal.png', fullPage: true });
        console.log('Screenshot guardado como debug-modal.png');
    });
});
