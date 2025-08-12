import { test, expect } from '@playwright/test';

test.describe('Verificación de Corrección de Duplicaciones', () => {
    test('debería confirmar que las duplicaciones han sido eliminadas', async ({ page }) => {
        // Navegar a la aplicación
        await page.goto('http://localhost:8000/obras/1');

        // Esperar a que la página cargue
        await page.waitForLoadState('networkidle');

        // Verificar que solo hay UNA sección de Observaciones
        const observacionesSections = page.locator('h3:has-text("Observaciones")');
        const observacionesCount = await observacionesSections.count();
        expect(observacionesCount).toBe(1);

        // Verificar que solo hay UNA sección de Documentos Principales
        const documentosSections = page.locator('h3:has-text("Documentos Principales")');
        const documentosCount = await documentosSections.count();
        expect(documentosCount).toBe(1);

        // Verificar que solo hay UNA sección de Estadísticas del Proyecto
        const estadisticasSections = page.locator('h3:has-text("Estadísticas del Proyecto")');
        const estadisticasCount = await estadisticasSections.count();
        expect(estadisticasCount).toBe(1);

        // Verificar que existe la nueva sección de Información Adicional
        const infoAdicionalSection = page.locator('h3:has-text("Información Adicional")');
        await expect(infoAdicionalSection).toBeVisible();

        // Verificar que el layout de 2 columnas funciona correctamente
        const gridContainer = page.locator('.grid.grid-cols-1.lg\\:grid-cols-2');
        await expect(gridContainer).toBeVisible();

        // Tomar screenshot final para verificación visual
        await page.screenshot({
            path: 'debug-obras-show-fixed.png',
            fullPage: true
        });

        console.log('✅ Verificación completada: No hay duplicaciones');
        console.log('✅ Screenshot de verificación guardado como: debug-obras-show-fixed.png');
    });
});