import { test, expect } from '@playwright/test';

test.describe('Debug Cambiar Obra', () => {
    test('Verificar que la página del vehículo carga sin errores', async ({ page }) => {
        // Configurar para capturar errores de consola
        const errors = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                errors.push(msg.text());
            }
        });

        // Ir al login
        await page.goto('http://127.0.0.1:8000/login');

        // Hacer login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar redirección
        await page.waitForURL(/.*home.*|.*dashboard.*/);

        // Ir a la página del vehículo
        console.log('Navegando a página del vehículo...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1');

        // Esperar un poco para que la página cargue
        await page.waitForTimeout(3000);

        // Verificar si hay errores en la página
        const title = await page.title();
        console.log('Título de la página:', title);

        // Verificar si hay errores de SQL en la página
        const bodyText = await page.textContent('body');
        if (bodyText.includes('SQLSTATE') || bodyText.includes('QueryException')) {
            console.log('❌ Error SQL detectado en la página');
            console.log('Texto del error:', bodyText.substring(0, 500));

            // Tomar captura de pantalla
            await page.screenshot({ path: 'debug-sql-error.png', fullPage: true });
        } else {
            console.log('✅ Página cargó sin errores SQL');

            // Verificar elementos básicos
            const vehiculoTitle = await page.locator('h1, h2, .text-2xl').first().textContent();
            console.log('Título del vehículo:', vehiculoTitle);

            // Tomar captura de pantalla del estado actual
            await page.screenshot({ path: 'debug-pagina-vehiculo.png', fullPage: true });
        }

        // Mostrar errores de consola si los hay
        if (errors.length > 0) {
            console.log('Errores de consola:', errors);
        }
    });
});
