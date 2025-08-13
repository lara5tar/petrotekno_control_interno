import { test, expect } from '@playwright/test';

test.describe('Debug: Acceso a Crear Vehículos - UI', () => {
    test('Verificar si el botón de crear vehículos es visible para admin', async ({ page }) => {
        console.log('🔍 Iniciando verificación de UI para crear vehículos...');

        // Ir a la página de login
        await page.goto('http://localhost:8000/login');
        console.log('📍 Navegado a página de login');

        // Login como admin
        await page.fill('[name="email"]', 'admin@petrotekno.com');
        await page.fill('[name="password"]', 'password');
        await page.click('button[type="submit"]');
        console.log('🔐 Login realizado');

        // Esperar a que se complete el login
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso, redirigido a home');

        // Navegar directamente a la página de vehículos
        await page.goto('http://localhost:8000/vehiculos');
        console.log('🚗 Navegado a página de vehículos');

        // Esperar a que la página cargue completamente
        await page.waitForLoadState('networkidle');

        // Tomar screenshot del estado actual
        await page.screenshot({ path: 'debug-vehiculos-page.png', fullPage: true });
        console.log('📸 Screenshot guardado como debug-vehiculos-page.png');

        // Verificar si existe el botón de "Agregar Vehículo"
        const addButton = page.locator('a[href*="vehiculos/create"], button:has-text("Agregar Vehículo"), a:has-text("Agregar Vehículo")');
        const addButtonExists = await addButton.count();
        console.log(`🔍 Botones "Agregar Vehículo" encontrados: ${addButtonExists}`);

        if (addButtonExists > 0) {
            console.log('✅ Botón de agregar vehículo ENCONTRADO');

            // Verificar si es visible
            const isVisible = await addButton.first().isVisible();
            console.log(`👁️ ¿Es visible? ${isVisible}`);

            // Verificar si es clickeable
            const isEnabled = await addButton.first().isEnabled();
            console.log(`🖱️ ¿Es clickeable? ${isEnabled}`);

            // Obtener el texto y atributos del botón
            const buttonText = await addButton.first().textContent();
            const buttonHref = await addButton.first().getAttribute('href');
            console.log(`📝 Texto del botón: "${buttonText}"`);
            console.log(`🔗 Href del botón: "${buttonHref}"`);

            // Intentar hacer click en el botón
            try {
                await addButton.first().click();
                await page.waitForLoadState('networkidle');

                const currentUrl = page.url();
                console.log(`🌐 URL después del click: ${currentUrl}`);

                if (currentUrl.includes('/vehiculos/create')) {
                    console.log('✅ ÉXITO: Navegó correctamente a crear vehículo');
                } else {
                    console.log('❌ ERROR: No navegó a la página de crear vehículo');
                }

                // Tomar screenshot de la página de crear
                await page.screenshot({ path: 'debug-vehiculos-create-page.png', fullPage: true });
                console.log('📸 Screenshot de create guardado');

            } catch (error) {
                console.log(`❌ ERROR al hacer click: ${error.message}`);
            }
        } else {
            console.log('❌ PROBLEMA: Botón de agregar vehículo NO ENCONTRADO');

            // Buscar elementos relacionados con permisos
            const permissionElements = await page.locator('[data-permission*="crear"], [class*="permission"], [class*="hasPermission"]').count();
            console.log(`🔒 Elementos relacionados con permisos: ${permissionElements}`);

            // Verificar el HTML del encabezado donde debería estar el botón
            const headerContent = await page.locator('.flex.justify-between.items-center, .header, h2').first().innerHTML();
            console.log('📋 Contenido del encabezado:');
            console.log(headerContent);

            // Buscar cualquier elemento que mencione "crear" o "agregar"
            const createElements = await page.locator('*:has-text("crear"), *:has-text("Crear"), *:has-text("agregar"), *:has-text("Agregar")').count();
            console.log(`➕ Elementos con texto "crear/agregar": ${createElements}`);
        }

        // Verificar información del usuario logueado
        const userInfo = await page.locator('[data-user], .user-info, .user-name').textContent().catch(() => 'No encontrado');
        console.log(`👤 Info usuario: ${userInfo}`);

        // Verificar si hay mensajes de error en la página
        const errorMessages = await page.locator('.alert-danger, .error, .text-red-500, .text-red-600').count();
        console.log(`⚠️ Mensajes de error en página: ${errorMessages}`);

        if (errorMessages > 0) {
            const errorText = await page.locator('.alert-danger, .error, .text-red-500, .text-red-600').first().textContent();
            console.log(`📋 Contenido del error: ${errorText}`);
        }

        console.log('🏁 Verificación completada');
    });
});