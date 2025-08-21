import { test, expect } from '@playwright/test';

test('Logs functionality test', async ({ page }) => {
    // Navegar a la página de configuración
    await page.goto('http://127.0.0.1:8001/configuracion');

    // Esperar a que la página cargue completamente
    await page.waitForLoadState('networkidle');

    // Hacer login si es necesario (asumiendo que hay un formulario de login)
    try {
        await page.waitForSelector('input[name="email"]', { timeout: 3000 });
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');
    } catch (e) {
        console.log('No login form found, proceeding...');
    }

    // Buscar el botón "Ver Logs del Sistema"
    const logsButton = page.locator('a:has-text("Ver Logs del Sistema")');

    // Verificar que el botón existe
    await expect(logsButton).toBeVisible();

    // Hacer clic en el botón
    await logsButton.click();

    // Esperar a que la página de logs cargue
    await page.waitForLoadState('networkidle');

    // Verificar que estamos en la página de logs
    await expect(page).toHaveURL(/.*\/admin\/logs/);

    // Verificar que los elementos principales de la página de logs están presentes
    await expect(page.locator('h1:has-text("Logs del Sistema")')).toBeVisible();
    await expect(page.locator('text=Filtros de Búsqueda')).toBeVisible();
    await expect(page.locator('text=Registro de Actividad')).toBeVisible();

    // Verificar que el botón de volver funciona
    const backButton = page.locator('a:has-text("Volver a Configuración")');
    await expect(backButton).toBeVisible();

    console.log('✅ Test de logs del sistema completado exitosamente');
});
