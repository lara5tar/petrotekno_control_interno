import { test, expect } from '@playwright/test';

test('Debug cambiar operador issue', async ({ page }) => {
    // Configurar interceptación de requests
    const requests = [];
    const responses = [];

    page.on('request', request => {
        requests.push({
            url: request.url(),
            method: request.method(),
            headers: request.headers(),
            postData: request.postData()
        });
        console.log(`➤ REQUEST: ${request.method()} ${request.url()}`);
    });

    page.on('response', response => {
        responses.push({
            url: response.url(),
            status: response.status(),
            statusText: response.statusText()
        });
        console.log(`← RESPONSE: ${response.status()} ${response.url()}`);
    });

    // Interceptar errores de consola
    page.on('console', msg => {
        console.log(`CONSOLE ${msg.type()}: ${msg.text()}`);
    });

    page.on('pageerror', error => {
        console.log(`PAGE ERROR: ${error.message}`);
    });

    try {
        // Ir a la página de login
        await page.goto('http://127.0.0.1:8000/login');

        // Login como admin
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar redirección
        await page.waitForURL('**/dashboard');

        // Ir a vehículos
        await page.goto('http://127.0.0.1:8000/vehiculos');

        // Hacer clic en el primer vehículo para ver detalles
        await page.click('tbody tr:first-child a');

        // Esperar a que cargue la página de detalles
        await page.waitForSelector('.card-header');

        // Buscar y hacer clic en el botón "Cambiar Operador"
        const cambiarOperadorBtn = page.locator('button:has-text("Cambiar Operador"), button:has-text("Asignar Operador")');

        if (await cambiarOperadorBtn.count() > 0) {
            console.log('Botón encontrado, haciendo clic...');
            await cambiarOperadorBtn.click();

            // Esperar a que aparezca el modal
            await page.waitForSelector('#cambiarOperadorModal');

            // Seleccionar un operador si hay opciones disponibles
            const operadorSelect = page.locator('#personal_id');
            const options = await operadorSelect.locator('option').count();

            console.log(`Operadores disponibles: ${options - 1}`); // -1 por la opción "Seleccione"

            if (options > 1) {
                // Seleccionar el primer operador disponible
                await operadorSelect.selectOption({ index: 1 });

                // Hacer clic en guardar y capturar la respuesta
                console.log('Enviando formulario...');

                const [response] = await Promise.all([
                    page.waitForResponse(response =>
                        response.url().includes('cambiar-operador') &&
                        response.request().method() === 'PATCH'
                    ),
                    page.click('#btnGuardarOperador')
                ]);

                console.log(`Response status: ${response.status()}`);
                console.log(`Response headers:`, await response.allHeaders());

                const responseText = await response.text();
                console.log(`Response body:`, responseText);

                // Esperar un momento para ver si aparecen notificaciones
                await page.waitForTimeout(2000);

                // Capturar cualquier notificación que aparezca
                const alerts = await page.locator('.alert').count();
                console.log(`Alertas encontradas: ${alerts}`);

                for (let i = 0; i < alerts; i++) {
                    const alert = page.locator('.alert').nth(i);
                    const text = await alert.textContent();
                    const classes = await alert.getAttribute('class');
                    console.log(`Alerta ${i + 1}: "${text}" (classes: ${classes})`);
                }

                // Tomar screenshot
                await page.screenshot({ path: 'debug-operador-result.png', fullPage: true });

            } else {
                console.log('No hay operadores disponibles en el dropdown');
            }
        } else {
            console.log('No se encontró el botón de cambiar operador');
        }

    } catch (error) {
        console.error('Error durante el test:', error);
        await page.screenshot({ path: 'debug-operador-error.png', fullPage: true });
        throw error;
    }
});
