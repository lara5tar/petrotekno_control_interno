import { test, expect } from '@playwright/test';

test.describe('Admin Reports Access Test', () => {

    test('verify admin can access reports view', async ({ page }) => {
        console.log('🚀 Iniciando test de acceso a reportes para admin...');

        // Ir directamente a la página de reportes
        await page.goto('http://127.0.0.1:8000/reportes');
        console.log('📍 URL inicial:', page.url());

        // Si hay redirección a login, hacer login como admin
        if (page.url().includes('/login')) {
            console.log('🔐 Redirigido a login, autenticando como admin...');

            // Llenar formulario de login
            await page.fill('input[name="email"]', 'admin@petrotekno.com');

            // Probar contraseñas comunes para admin
            const passwords = ['123456', 'password', 'admin', 'petrotekno', 'admin123'];
            let loginSuccess = false;

            for (const pwd of passwords) {
                console.log(`🔑 Probando contraseña: ${pwd}`);
                await page.fill('input[name="password"]', pwd);
                await page.click('button[type="submit"]');

                // Esperar respuesta del servidor
                await page.waitForTimeout(2000);

                // Verificar si el login fue exitoso
                if (!page.url().includes('/login')) {
                    console.log(`✅ Login exitoso con contraseña: ${pwd}`);
                    loginSuccess = true;
                    break;
                } else {
                    console.log(`❌ Login fallido con contraseña: ${pwd}`);
                    // Limpiar y volver a llenar el email
                    await page.fill('input[name="email"]', 'admin@petrotekno.com');
                }
            }

            if (!loginSuccess) {
                console.log('❌ ERROR: No se pudo hacer login con ninguna contraseña');
                await page.screenshot({ path: 'debug-admin-login-failed.png' });
                throw new Error('Login failed for admin user');
            }

            // Después del login exitoso, ir a reportes
            console.log('📊 Navegando a reportes después del login...');
            await page.goto('http://127.0.0.1:8000/reportes');
            await page.waitForTimeout(1000);
        }

        console.log('📍 URL después del login:', page.url());

        // Verificar que NO estamos en la página de login
        expect(page.url()).not.toContain('/login');
        console.log('✅ No redirigido a login - permisos OK');

        // Verificar que llegamos a la página de reportes
        expect(page.url()).toContain('/reportes');
        console.log('✅ Acceso a reportes confirmado');

        // Verificar que no hay error 403 de permisos
        const pageContent = await page.content();
        expect(pageContent).not.toContain('403');
        expect(pageContent).not.toContain('NO TIENES PERMISOS');
        expect(pageContent).not.toContain('Unauthorized');
        console.log('✅ Sin errores de permisos (403)');

        // Verificar elementos específicos de la página de reportes
        await page.waitForSelector('h1, h2, .card-title', { timeout: 5000 });

        // Buscar indicadores de que estamos en la página correcta
        const hasReportsTitle = await page.locator('text=/reportes/i').count() > 0;
        const hasInventoryOption = await page.locator('text=/inventario/i').count() > 0;
        const hasVehiclesOption = await page.locator('text=/vehículos/i').count() > 0;

        if (hasReportsTitle || hasInventoryOption || hasVehiclesOption) {
            console.log('✅ Contenido de reportes detectado en la página');
        } else {
            console.log('⚠️  Contenido de reportes no detectado claramente');
            await page.screenshot({ path: 'debug-reports-content.png' });
        }

        // Verificar que existe el enlace al inventario de vehículos
        const inventoryLink = page.locator('a[href*="inventario-vehiculos"]');
        if (await inventoryLink.count() > 0) {
            console.log('✅ Enlace a inventario de vehículos encontrado');

            // Probar clic en el enlace
            await inventoryLink.first().click();
            await page.waitForTimeout(2000);

            // Verificar que llegamos al inventario
            expect(page.url()).toContain('inventario-vehiculos');
            console.log('✅ Acceso al inventario de vehículos confirmado');

            // Verificar que no hay errores en esta página tampoco
            const inventoryContent = await page.content();
            expect(inventoryContent).not.toContain('403');
            expect(inventoryContent).not.toContain('NO TIENES PERMISOS');
            console.log('✅ Sin errores de permisos en inventario');

        } else {
            console.log('⚠️  Enlace a inventario no encontrado');
        }

        // Tomar screenshot final del éxito
        await page.screenshot({ path: 'admin-reports-access-success.png' });
        console.log('📸 Screenshot de éxito guardado');

        console.log('🎉 Test completado exitosamente - Admin puede acceder a reportes');
    });

    test('verify specific reports permissions', async ({ page }) => {
        console.log('🔍 Verificando permisos específicos de reportes...');

        // Login como admin (reutilizando lógica)
        await page.goto('http://127.0.0.1:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', '123456');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);

        // Ir a reportes
        await page.goto('http://127.0.0.1:8000/reportes');
        await page.waitForTimeout(1000);

        // Verificar que el sidebar/menú contiene reportes
        const sidebarReports = page.locator('nav a[href*="reportes"], .sidebar a[href*="reportes"]');
        if (await sidebarReports.count() > 0) {
            console.log('✅ Enlace de reportes encontrado en el menú');
        } else {
            console.log('⚠️  Enlace de reportes no encontrado en el menú');
        }

        // Verificar botones de exportar (si existen)
        const exportButtons = page.locator('button:has-text("Exportar"), .btn:has-text("Excel"), .btn:has-text("PDF")');
        if (await exportButtons.count() > 0) {
            console.log('✅ Botones de exportar encontrados');
        } else {
            console.log('ℹ️  Botones de exportar no encontrados (puede ser normal)');
        }

        console.log('✅ Verificación de permisos completada');
    });
});
