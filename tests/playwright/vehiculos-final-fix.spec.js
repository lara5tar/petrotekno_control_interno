import { test, expect } from '@playwright/test';

test.describe('Verificación FINAL - Botón crear vehículos con PHP puro', () => {
    test('Admin debe poder ver y usar el botón de crear vehículos', async ({ page }) => {
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

        // 5. Buscar cualquiera de los botones de crear vehículos
        const selectoresBotones = [
            'a[href*="vehiculos/create"]:has-text("Agregar Vehículo")',
            'a[href*="vehiculos/create"]:has-text("ADMIN: Agregar Vehículo")'
        ];

        let botonEncontrado = null;
        let tipoBoton = '';

        for (const selector of selectoresBotones) {
            const boton = page.locator(selector);
            if (await boton.count() > 0) {
                botonEncontrado = boton.first();
                tipoBoton = selector;
                break;
            }
        }

        console.log('🔍 RESULTADOS FINALES:');
        console.log('Botón encontrado:', botonEncontrado !== null);
        console.log('Tipo de botón:', tipoBoton);

        // 6. Información adicional de debug
        const todosBotones = await page.locator('a[href*="vehiculos/create"]').count();
        const contenidoPagina = await page.content();

        console.log('Total botones crear vehículos:', todosBotones);
        console.log('Página contiene PHP @php:', contenidoPagina.includes('@php'));
        console.log('Página contiene hasPermission:', contenidoPagina.includes('hasPermission'));
        console.log('Página contiene ADMIN:', contenidoPagina.includes('ADMIN'));

        // 7. Tomar screenshot
        await page.screenshot({ path: 'debug-vehiculos-final-fix.png', fullPage: true });

        // 8. Si encontramos botón, hacer clic
        if (botonEncontrado) {
            console.log('✅ Haciendo clic en el botón...');

            // Hacer clic y esperar navegación
            await Promise.all([
                page.waitForNavigation(),
                botonEncontrado.click()
            ]);

            const urlFinal = page.url();
            console.log('URL final:', urlFinal);

            if (urlFinal.includes('vehiculos/create')) {
                console.log('🎉 ¡ÉXITO TOTAL! Se puede acceder a crear vehículos');

                // Verificar que cargó la página de crear
                const titulo = await page.textContent('h1, h2, .title');
                console.log('Título de la página:', titulo);

                // Tomar screenshot de la página de crear
                await page.screenshot({ path: 'debug-vehiculos-create-page.png', fullPage: true });
            } else {
                console.log('❌ ERROR: Redirigió a una página incorrecta');
            }
        } else {
            console.log('❌ ERROR: No se encontró el botón de crear vehículos');

            // Debug adicional
            const todosEnlaces = await page.locator('a').count();
            console.log('Total enlaces en la página:', todosEnlaces);

            // Buscar enlaces que contengan 'create'
            const enlacesCreate = await page.locator('a[href*="create"]').count();
            console.log('Enlaces que contienen "create":', enlacesCreate);
        }

        // Assertion final
        expect(botonEncontrado).not.toBeNull();
    });
});