import { test, expect } from '@playwright/test';

test('Debug login and check operador functionality', async ({ page }) => {
    // Configurar interceptación
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

    try {
        // Ir a la página de login
        await page.goto('http://127.0.0.1:8001/login');

        // Login como admin
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');

        console.log('Enviando formulario de login...');
        await page.click('button[type="submit"]');

        // Esperar un momento y tomar screenshot del resultado
        await page.waitForTimeout(3000);
        await page.screenshot({ path: 'debug-login-result.png', fullPage: true });

        // Verificar a dónde nos redirigió
        const currentUrl = page.url();
        console.log(`URL actual después del login: ${currentUrl}`);

        // Si no estamos en dashboard, intentar navegar directamente a vehículos
        if (!currentUrl.includes('dashboard') && !currentUrl.includes('home')) {
            console.log('No estamos en dashboard/home, navegando directamente a vehículos...');
            await page.goto('http://127.0.0.1:8001/vehiculos');
        } else {
            console.log('Login exitoso, navegando a vehículos...');
            await page.goto('http://127.0.0.1:8001/vehiculos');
        }

        // Esperar a que cargue la página de vehículos
        await page.waitForTimeout(2000);
        await page.screenshot({ path: 'debug-vehiculos-page.png', fullPage: true });

        // Buscar si hay vehículos en la tabla
        const vehiculos = await page.locator('tbody tr').count();
        console.log(`Vehículos encontrados: ${vehiculos}`);

        if (vehiculos > 0) {
            // Hacer clic en el primer vehículo
            console.log('Haciendo clic en el primer vehículo...');
            await page.click('tbody tr:first-child a');

            // Esperar a que cargue la página de detalles
            await page.waitForTimeout(2000);
            await page.screenshot({ path: 'debug-vehiculo-details.png', fullPage: true });

            // Buscar el botón de cambiar operador
            const cambiarOperadorBtn = page.locator('button:has-text("Cambiar Operador"), button:has-text("Asignar Operador")');
            const btnCount = await cambiarOperadorBtn.count();
            console.log(`Botones de operador encontrados: ${btnCount}`);

            if (btnCount > 0) {
                console.log('Haciendo clic en el botón de cambiar operador...');
                await cambiarOperadorBtn.first().click();

                // Esperar a que aparezca el modal
                await page.waitForTimeout(1000);
                await page.screenshot({ path: 'debug-modal-operador.png', fullPage: true });

                // Verificar si hay operadores disponibles
                const operadorSelect = page.locator('#personal_id');
                const options = await operadorSelect.locator('option').count();
                console.log(`Opciones de operadores: ${options}`);

                if (options > 1) {
                    // Seleccionar el primer operador disponible
                    await operadorSelect.selectOption({ index: 1 });

                    console.log('Enviando formulario de cambio de operador...');

                    // Hacer clic en guardar y capturar la respuesta
                    const [response] = await Promise.all([
                        page.waitForResponse(response =>
                            response.url().includes('cambiar-operador') &&
                            response.request().method() === 'PATCH'
                        ),
                        page.click('#btnGuardarOperador')
                    ]);

                    console.log(`Response status: ${response.status()}`);
                    const responseText = await response.text();
                    console.log(`Response body:`, responseText);

                    // Esperar y capturar cualquier notificación
                    await page.waitForTimeout(3000);
                    await page.screenshot({ path: 'debug-operador-final.png', fullPage: true });

                    // Buscar alertas
                    const alerts = await page.locator('.alert').count();
                    console.log(`Alertas encontradas: ${alerts}`);

                    for (let i = 0; i < alerts; i++) {
                        const alert = page.locator('.alert').nth(i);
                        const text = await alert.textContent();
                        const classes = await alert.getAttribute('class');
                        console.log(`Alerta ${i + 1}: "${text}" (classes: ${classes})`);
                    }

                } else {
                    console.log('No hay operadores disponibles');
                }
            } else {
                console.log('No se encontró el botón de cambiar operador');
            }
        } else {
            console.log('No hay vehículos en la tabla');
        }

    } catch (error) {
        console.error('Error durante el test:', error);
        await page.screenshot({ path: 'debug-error.png', fullPage: true });
        throw error;
    }
});
