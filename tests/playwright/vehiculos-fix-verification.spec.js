import { test, expect } from '@playwright/test';

test.describe('Verificación de botón crear vehículos DESPUÉS de correcciones', () => {
    test('Admin debe poder ver el botón de crear vehículos', async ({ page }) => {
        // 1. Ir a la página de login
        await page.goto('http://localhost:8000/login');

        // 2. Hacer login como admin
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // 3. Esperar redirección
        await page.waitForURL('**/home');

        // 4. Navegar a vehículos
        await page.goto('http://localhost:8000/vehiculos');
        await page.waitForLoadState('networkidle');

        // 5. Verificar que aparezca AL MENOS UNO de los botones
        const botonesCrear = [
            'a[href*="vehiculos/create"]:has-text("Agregar Vehículo")',
            'a[href*="vehiculos/create"]:has-text("@can")',
            'a[href*="vehiculos/create"]:has-text("DEBUG")'
        ];

        let botonEncontrado = false;
        let tipoBoton = '';

        for (const selector of botonesCrear) {
            const boton = page.locator(selector);
            if (await boton.count() > 0) {
                botonEncontrado = true;
                tipoBoton = selector;
                break;
            }
        }

        console.log('🔍 RESULTADOS DESPUÉS DE CORRECCIONES:');
        console.log('Botón encontrado:', botonEncontrado);
        console.log('Tipo de botón:', tipoBoton);

        // 6. Información de debug adicional
        const todosBotones = await page.locator('a[href*="vehiculos/create"]').count();
        const autenticado = await page.evaluate(() => {
            return document.querySelector('body').getAttribute('data-user') || 'No detectado';
        });

        console.log('Total botones crear vehículos:', todosBotones);
        console.log('Usuario autenticado:', autenticado);

        // 7. Verificar contenido de la página
        const contenidoPagina = await page.content();
        const tieneHasPermission = contenidoPagina.includes('@hasPermission');
        const tieneCan = contenidoPagina.includes('@can');
        const tieneDebug = contenidoPagina.includes('DEBUG');

        console.log('Página contiene @hasPermission:', tieneHasPermission);
        console.log('Página contiene @can:', tieneCan);
        console.log('Página contiene DEBUG:', tieneDebug);

        // 8. Tomar screenshot para diagnóstico
        await page.screenshot({ path: 'debug-vehiculos-despues-correcciones.png', fullPage: true });

        // 9. Si encontramos botón, intentar hacer clic
        if (botonEncontrado) {
            console.log('✅ Intentando hacer clic en el botón...');
            await page.click(`a[href*="vehiculos/create"]`);
            await page.waitForLoadState('networkidle');

            const urlActual = page.url();
            console.log('URL después del clic:', urlActual);

            if (urlActual.includes('vehiculos/create')) {
                console.log('✅ ÉXITO: Se pudo acceder a crear vehículos');
            } else {
                console.log('❌ ERROR: No se pudo acceder a crear vehículos');
            }
        }

        // Assertion final
        expect(botonEncontrado).toBe(true);
    });
});