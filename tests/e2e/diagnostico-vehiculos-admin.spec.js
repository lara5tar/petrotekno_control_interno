const { test, expect } = require('@playwright/test');

test.describe('Diagnóstico: Admin - Acceso a Crear Vehículos', () => {
    test('Debug completo del flujo de acceso a crear vehículos', async ({ page }) => {
        console.log('🔍 INICIANDO DIAGNÓSTICO COMPLETO');

        // 1. Ir a la página de login
        await page.goto('http://localhost:8000/login');
        console.log('✅ Navegó a la página de login');

        // 2. Hacer login con credenciales de admin
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // 3. Esperar redirección al dashboard
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso, redirigido a /home');

        // 4. Verificar que estamos autenticados
        const isAuthenticated = await page.locator('body').textContent();
        console.log('🔍 Usuario logueado detectado:', isAuthenticated.includes('Cerrar Sesión') ? 'SÍ' : 'NO');

        // 5. Buscar el menú/enlace de vehículos
        const vehiculosLink = page.locator('a[href*="vehiculos"]').first();
        const vehiculosExists = await vehiculosLink.count() > 0;
        console.log('🔍 Enlace de vehículos encontrado:', vehiculosExists ? 'SÍ' : 'NO');

        if (vehiculosExists) {
            const vehiculosText = await vehiculosLink.textContent();
            console.log('📝 Texto del enlace:', vehiculosText);
            const vehiculosHref = await vehiculosLink.getAttribute('href');
            console.log('📝 URL del enlace:', vehiculosHref);

            // 6. Hacer clic en vehículos
            await vehiculosLink.click();
            console.log('✅ Click en enlace de vehículos realizado');

            // 7. Esperar que cargue la página de vehículos
            await page.waitForLoadState('networkidle');
            const currentUrl = page.url();
            console.log('📍 URL actual después del click:', currentUrl);

            // 8. Verificar si llegamos a la página de vehículos
            if (currentUrl.includes('vehiculos')) {
                console.log('✅ Acceso exitoso a la página de vehículos');

                // 9. Buscar el botón/enlace de "Crear" o "Nuevo"
                const crearButtons = [
                    page.locator('a[href*="/vehiculos/create"]'),
                    page.locator('button:has-text("Crear")'),
                    page.locator('button:has-text("Nuevo")'),
                    page.locator('a:has-text("Crear")'),
                    page.locator('a:has-text("Nuevo")'),
                    page.locator('[data-bs-target*="crear"]'),
                    page.locator('[onclick*="crear"]')
                ];

                let crearButtonFound = false;
                for (let i = 0; i < crearButtons.length; i++) {
                    const buttonCount = await crearButtons[i].count();
                    if (buttonCount > 0) {
                        const buttonText = await crearButtons[i].first().textContent();
                        const buttonHref = await crearButtons[i].first().getAttribute('href');
                        console.log(`✅ Botón crear encontrado (${i + 1}): "${buttonText}" -> ${buttonHref}`);
                        crearButtonFound = true;

                        // 10. Intentar hacer clic en el botón de crear
                        try {
                            await crearButtons[i].first().click();
                            console.log('✅ Click en botón crear realizado');

                            await page.waitForLoadState('networkidle');
                            const finalUrl = page.url();
                            console.log('📍 URL final:', finalUrl);

                            if (finalUrl.includes('/create')) {
                                console.log('🎉 ¡ÉXITO! Acceso a crear vehículos funcionando correctamente');
                            } else {
                                console.log('❌ ERROR: No se llegó a la página de crear');

                                // Verificar si hay mensajes de error
                                const errorMessages = await page.locator('.alert-danger, .error, .text-danger').allTextContents();
                                if (errorMessages.length > 0) {
                                    console.log('🚨 Mensajes de error encontrados:', errorMessages);
                                }
                            }
                        } catch (error) {
                            console.log('❌ ERROR al hacer click en crear:', error.message);
                        }
                        break;
                    }
                }

                if (!crearButtonFound) {
                    console.log('❌ NO se encontró ningún botón de crear vehículos');

                    // Debug: Mostrar todos los enlaces y botones disponibles
                    const allLinks = await page.locator('a').allTextContents();
                    const allButtons = await page.locator('button').allTextContents();

                    console.log('🔍 Enlaces disponibles:', allLinks.slice(0, 10));
                    console.log('🔍 Botones disponibles:', allButtons.slice(0, 10));
                }
            } else {
                console.log('❌ ERROR: No se llegó a la página de vehículos');
                console.log('📍 URL actual:', currentUrl);

                // Verificar si hay mensajes de error o redirecciones
                const pageContent = await page.content();
                if (pageContent.includes('sin permisos') || pageContent.includes('no autorizado')) {
                    console.log('🚨 PROBLEMA: Mensaje de permisos encontrado en la página');
                }
            }
        } else {
            console.log('❌ PROBLEMA CRÍTICO: No se encontró enlace de vehículos en el menú');

            // Debug: Mostrar estructura del menú
            const menuItems = await page.locator('nav a, .sidebar a, .menu a').allTextContents();
            console.log('🔍 Items del menú encontrados:', menuItems);
        }

        // 11. Verificación final de permisos via JavaScript
        const hasPermission = await page.evaluate(() => {
            // Intentar hacer una petición AJAX para verificar permisos
            return fetch('/vehiculos/create', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => ({
                    status: response.status,
                    ok: response.ok,
                    statusText: response.statusText
                }))
                .catch(error => ({
                    error: error.message
                }));
        });

        console.log('🔍 Verificación directa de /vehiculos/create:', hasPermission);

        console.log('🏁 DIAGNÓSTICO COMPLETADO');
    });
});