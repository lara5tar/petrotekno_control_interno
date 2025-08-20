import { test, expect } from '@playwright/test';

test.describe('Campanita de Alertas en Navbar', () => {
    test.beforeEach(async ({ page }) => {
        // Ir a la página de login
        await page.goto('http://localhost:8002/login');

        // Hacer login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');

        // Esperar a que cargue el dashboard
        await page.waitForLoadState('networkidle');
    });

    test('La campanita debe estar visible en el navbar', async ({ page }) => {
        // Verificar que la campanita esté presente
        const campanitta = page.locator('a[title*="Centro de Alertas"]');
        await expect(campanitta).toBeVisible();

        // Verificar que tenga el ícono de campana
        const iconoCampana = campanitta.locator('svg');
        await expect(iconoCampana).toBeVisible();

        console.log('✅ Campanita visible en el navbar');
    });

    test('La campanita debe mostrar contador de alertas', async ({ page }) => {
        // Buscar el badge con el número de alertas
        const badge = page.locator('a[title*="Centro de Alertas"] span.absolute');

        // Verificar si hay alertas
        const badgeCount = await badge.count();
        if (badgeCount > 0) {
            await expect(badge).toBeVisible();

            // Verificar que tenga texto
            const badgeText = await badge.textContent();
            expect(badgeText).toBeTruthy();
            expect(badgeText.trim()).not.toBe('');

            console.log('✅ Badge de alertas visible con texto:', badgeText);
        } else {
            console.log('ℹ️ No hay alertas activas, badge no debería estar visible');
        }
    });

    test('Hacer clic en la campanita debe navegar a alertas unificadas', async ({ page }) => {
        // Hacer clic en la campanita
        const campanitta = page.locator('a[title*="Centro de Alertas"]');
        await campanitta.click();

        // Verificar que navegue a la página de alertas unificadas
        await page.waitForLoadState('networkidle');

        // Verificar la URL
        expect(page.url()).toContain('/alertas/unificada');

        // Verificar que se muestre el título de alertas
        const titulo = page.locator('h1, h2, h3').filter({ hasText: /alertas|centro/i });
        await expect(titulo.first()).toBeVisible();

        console.log('✅ Navegación a alertas unificadas funciona correctamente');
    });

    test('La campanita debe tener efectos hover', async ({ page }) => {
        const campanitta = page.locator('a[title*="Centro de Alertas"]');

        // Verificar estado inicial
        await expect(campanitta).toBeVisible();

        // Hacer hover
        await campanitta.hover();

        // Verificar que tenga la clase hover (esto puede variar según el CSS)
        const hasHoverClass = await campanitta.evaluate(el =>
            el.classList.contains('hover:bg-gray-700') ||
            el.classList.contains('group')
        );

        expect(hasHoverClass).toBeTruthy();

        console.log('✅ Efectos hover configurados correctamente');
    });

    test('Badge de alertas debe tener color correcto según urgencia', async ({ page }) => {
        const badge = page.locator('a[title*="Centro de Alertas"] span.absolute');

        const badgeCount = await badge.count();
        if (badgeCount > 0) {
            // Verificar que tenga colores apropiados
            const hasRedBg = await badge.evaluate(el =>
                el.classList.contains('bg-red-500')
            );
            const hasYellowBg = await badge.evaluate(el =>
                el.classList.contains('bg-yellow-500')
            );

            expect(hasRedBg || hasYellowBg).toBeTruthy();

            if (hasRedBg) {
                console.log('✅ Badge rojo - alertas urgentes detectadas');
            } else if (hasYellowBg) {
                console.log('✅ Badge amarillo - alertas normales detectadas');
            }
        }
    });

    test('Verificar tooltip de la campanita', async ({ page }) => {
        const campanitta = page.locator('a[title*="Centro de Alertas"]');

        // Verificar que tenga tooltip
        const title = await campanitta.getAttribute('title');
        expect(title).toContain('Centro de Alertas');
        expect(title).toContain('Ver todas las alertas del sistema');

        console.log('✅ Tooltip configurado correctamente:', title);
    });
});
