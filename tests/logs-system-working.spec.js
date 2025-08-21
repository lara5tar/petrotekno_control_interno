import { test, expect } from '@playwright/test';

test('Logs system working test', async ({ page }) => {
    // Ir directamente a la página de logs
    await page.goto('http://127.0.0.1:8001/admin/logs');

    // Esperar a que la página cargue
    await page.waitForLoadState('networkidle');

    // Verificar que la página carga sin errores de 500
    const title = await page.locator('h1').textContent();
    expect(title).toContain('Logs del Sistema');

    // Verificar que hay contenido en la página
    const hasTable = await page.locator('table').isVisible();
    if (hasTable) {
        console.log('✅ Tabla de logs visible');
    }

    // Verificar filtros
    const userFilter = await page.locator('select[name="usuario_id"]').isVisible();
    expect(userFilter).toBe(true);

    const actionFilter = await page.locator('select[name="accion"]').isVisible();
    expect(actionFilter).toBe(true);

    // Verificar botón de volver
    const backButton = await page.locator('a:has-text("Volver a Configuración")').isVisible();
    expect(backButton).toBe(true);

    console.log('✅ Sistema de logs funcionando correctamente');
});
