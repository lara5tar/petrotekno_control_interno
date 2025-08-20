import { test, expect } from '@playwright/test';

test.describe('Vista Unificada - Pruebas Exitosas', () => {

    test.beforeEach(async ({ page }) => {
        page.setDefaultTimeout(30000);

        // Login
        await page.goto('http://localhost:8001/login', {
            waitUntil: 'load',
            timeout: 30000
        });

        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        await page.waitForURL('**/home', { timeout: 30000 });
    });

    test('debe cargar la vista unificada sin errores', async ({ page }) => {
        // Navegar a la vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });

        // Verificar que no estemos en una página de error
        await expect(page).not.toHaveURL(/login/);
        await expect(page.locator('body')).not.toContainText('Internal Server Error');

        // Verificar título principal
        await expect(page.locator('h1').filter({ hasText: 'Centro de Alertas Unificado' })).toBeVisible();

        // Verificar que hay contenido estadístico
        await expect(page.locator('body')).toContainText('Total Alertas');

        console.log('✅ Vista unificada carga correctamente');
    });

    test('debe mostrar alertas de mantenimiento y documentos', async ({ page }) => {
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });

        // Verificar que hay alertas de ambos tipos
        const hasMantenimiento = await page.locator('body').textContent();
        const hasDocumentos = await page.locator('body').textContent();

        expect(hasMantenimiento).toContain('Mantenimiento');
        expect(hasDocumentos).toContain('Documentos');

        console.log('✅ Muestra alertas de ambos tipos');
    });

    test('debe tener funcionalidad de filtros', async ({ page }) => {
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });

        // Verificar que existen los controles de filtro
        const filterSelects = await page.locator('select').count();
        expect(filterSelects).toBeGreaterThan(0);

        console.log('✅ Tiene controles de filtro');
    });

    test('debe tener enlaces de navegación funcionando', async ({ page }) => {
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });

        // Verificar enlace a vista tradicional
        const enlaceTradicional = page.locator('a').filter({ hasText: 'Ver vista de tabla tradicional' });
        await expect(enlaceTradicional).toBeVisible();

        console.log('✅ Enlaces de navegación presentes');
    });

    test('debe mostrar estadísticas de alertas', async ({ page }) => {
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });

        // Verificar estadísticas básicas
        await expect(page.locator('body')).toContainText('Total Alertas');
        await expect(page.locator('body')).toContainText('Vencidas');
        await expect(page.locator('body')).toContainText('Próximas');

        console.log('✅ Estadísticas de alertas mostradas');
    });

    test('debe mostrar vehículos específicos de prueba', async ({ page }) => {
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });

        // Verificar vehículos de prueba específicos
        const bodyText = await page.locator('body').textContent();

        // Al menos uno de estos vehículos debe estar presente
        const hasTestVehicles = bodyText?.includes('TEST-DOC') ||
            bodyText?.includes('Ford F-150') ||
            bodyText?.includes('Test Final X');

        expect(hasTestVehicles).toBeTruthy();

        console.log('✅ Vehículos de prueba presentes');
    });
});
