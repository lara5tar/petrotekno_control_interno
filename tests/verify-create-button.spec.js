import { test, expect } from '@playwright/test';

test.describe('Admin Roles - Create Button Verification', () => {

    test('should show create role button in admin/roles page', async ({ page }) => {
        console.log('🔍 Iniciando test para verificar botón crear rol...');

        // Ir a la página de login
        await page.goto('http://127.0.0.1:8000/login');
        console.log('📍 Navegando a login...');

        // Hacer login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        console.log('🔐 Haciendo login...');

        // Esperar a que el login complete
        await page.waitForTimeout(2000);

        // Navegar específicamente a admin/roles
        await page.goto('http://127.0.0.1:8000/admin/roles');
        console.log('📍 Navegando a /admin/roles...');

        // Esperar a que la página cargue completamente
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        // Tomar screenshot para debug
        await page.screenshot({ path: 'debug-admin-roles-full.png', fullPage: true });
        console.log('📸 Screenshot tomado: debug-admin-roles-full.png');

        // Verificar URL actual
        const currentUrl = page.url();
        console.log('🌐 URL actual:', currentUrl);
        expect(currentUrl).toContain('/admin/roles');

        // Verificar título de la página
        const pageTitle = await page.locator('h1').first().textContent();
        console.log('📝 Título de la página:', pageTitle);
        expect(pageTitle).toContain('Gestión de Roles');

        // Verificar el debug message sobre permisos
        const debugPermissions = page.locator('text=Debug: Puede crear roles:');
        if (await debugPermissions.isVisible()) {
            const debugText = await debugPermissions.textContent();
            console.log('🔧 Debug permisos:', debugText);
        }

        // Buscar todos los botones de crear rol
        const buttonSelectors = [
            'a:has-text("Crear Nuevo Rol (Siempre Visible)")',
            'a:has-text("Crear Nuevo Rol (@can)")',
            'a:has-text("Crear Nuevo Rol (hasPermission)")',
            'a[href*="roles/create"]'
        ];

        let buttonsFound = 0;
        for (const selector of buttonSelectors) {
            const button = page.locator(selector);
            const isVisible = await button.isVisible();
            console.log(`🔍 Botón "${selector}": ${isVisible ? '✅ VISIBLE' : '❌ NO VISIBLE'}`);

            if (isVisible) {
                buttonsFound++;

                // Verificar que el botón es clickeable
                await expect(button).toBeEnabled();
                console.log(`✅ Botón clickeable confirmado`);

                // Verificar href
                const href = await button.getAttribute('href');
                console.log(`🔗 Href del botón: ${href}`);
                expect(href).toContain('roles/create');
            }
        }

        console.log(`📊 Total de botones encontrados: ${buttonsFound}`);

        // Al menos uno de los botones debe ser visible
        expect(buttonsFound).toBeGreaterThan(0);

        // Test de navegación: hacer click en el primer botón visible
        const anyCreateButton = page.locator('a[href*="roles/create"]').first();
        if (await anyCreateButton.isVisible()) {
            console.log('🖱️ Haciendo click en el botón crear rol...');
            await anyCreateButton.click();

            // Verificar navegación
            await page.waitForTimeout(1000);
            const newUrl = page.url();
            console.log('🌐 Nueva URL después del click:', newUrl);
            expect(newUrl).toContain('/admin/roles/create');

            // Verificar que la página de crear rol cargó
            const createTitle = await page.locator('h1').first().textContent();
            console.log('📝 Título de página crear:', createTitle);
            expect(createTitle).toContain('Crear');

            console.log('✅ Navegación a crear rol exitosa!');
        }

        console.log('🎉 Test completado exitosamente!');
    });
});
