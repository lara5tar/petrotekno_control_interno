import { test, expect } from '@playwright/test';

test('Logs system improved design test', async ({ page }) => {
    // Ir a la página de logs
    await page.goto('http://127.0.0.1:8001/admin/logs');

    // Esperar a que la página cargue
    await page.waitForLoadState('networkidle');

    // Verificar elementos del header mejorado
    await expect(page.locator('h1:has-text("📊 Logs del Sistema")')).toBeVisible();
    await expect(page.locator('text=Monitoreo y registro de actividad en tiempo real')).toBeVisible();

    // Verificar filtros mejorados
    await expect(page.locator('text=Filtros de Búsqueda')).toBeVisible();
    await expect(page.locator('select[name="usuario_id"]')).toBeVisible();
    await expect(page.locator('select[name="accion"]')).toBeVisible();
    await expect(page.locator('select[name="tabla_afectada"]')).toBeVisible();

    // Verificar estadísticas mejoradas
    await expect(page.locator('text=Total de Registros')).toBeVisible();
    await expect(page.locator('text=Página Actual')).toBeVisible();
    await expect(page.locator('text=Total de Páginas')).toBeVisible();
    await expect(page.locator('text=En Esta Página')).toBeVisible();

    // Verificar tabla con nuevo diseño
    await expect(page.locator('text=Registro de Actividad')).toBeVisible();
    await expect(page.locator('table')).toBeVisible();

    // Verificar que hay datos en la tabla
    const rows = page.locator('tbody tr');
    const rowCount = await rows.count();
    expect(rowCount).toBeGreaterThan(0);

    // Verificar badges de colores para diferentes acciones
    const badges = page.locator('tbody tr .inline-flex.items-center.px-3.py-1.rounded-full');
    await expect(badges.first()).toBeVisible();

    // Verificar botón de volver
    const backButton = page.locator('a:has-text("Volver a Configuración")');
    await expect(backButton).toBeVisible();

    // Verificar botones de filtro
    const filterButton = page.locator('button:has-text("Filtrar Resultados")');
    await expect(filterButton).toBeVisible();

    const clearButton = page.locator('a:has-text("Limpiar Filtros")');
    await expect(clearButton).toBeVisible();

    console.log('✅ Diseño mejorado de logs funcionando correctamente');
    console.log(`📊 Se encontraron ${rowCount} registros en la tabla`);
});
