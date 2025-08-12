import { test, expect } from '@playwright/test';

test.describe('Análisis de Vista Show de Obras', () => {
    test('debería analizar la estructura y detectar duplicaciones', async ({ page }) => {
        // Navegar a la aplicación (asumiendo que hay una obra de prueba)
        await page.goto('http://localhost:8000/obras/1');

        // Esperar a que la página cargue completamente
        await page.waitForLoadState('networkidle');

        // Analizar la estructura general
        const mainContainer = page.locator('.p-6');
        await expect(mainContainer).toBeVisible();

        // Verificar el grid principal
        const gridContainer = page.locator('.grid.grid-cols-1.lg\\:grid-cols-2');
        await expect(gridContainer).toBeVisible();

        // Analizar panel izquierdo
        const leftPanel = gridContainer.locator('> div').first();
        console.log('=== ANÁLISIS PANEL IZQUIERDO ===');

        // Contar secciones en panel izquierdo
        const leftSections = leftPanel.locator('> div.bg-white.border');
        const leftSectionCount = await leftSections.count();
        console.log(`Secciones en panel izquierdo: ${leftSectionCount}`);

        for (let i = 0; i < leftSectionCount; i++) {
            const section = leftSections.nth(i);
            const title = await section.locator('.bg-gray-50 h3').textContent();
            console.log(`  - Sección ${i + 1}: ${title}`);
        }

        // Analizar panel derecho
        const rightPanel = gridContainer.locator('> div').last();
        console.log('\n=== ANÁLISIS PANEL DERECHO ===');

        // Contar secciones en panel derecho
        const rightSections = rightPanel.locator('> div.bg-white.border');
        const rightSectionCount = await rightSections.count();
        console.log(`Secciones en panel derecho: ${rightSectionCount}`);

        for (let i = 0; i < rightSectionCount; i++) {
            const section = rightSections.nth(i);
            const title = await section.locator('.bg-gray-50 h3').textContent();
            console.log(`  - Sección ${i + 1}: ${title}`);
        }

        // Detectar duplicaciones específicas
        console.log('\n=== DETECCIÓN DE DUPLICACIONES ===');

        // Buscar todas las secciones de "Observaciones"
        const observacionesSections = page.locator('h3:has-text("Observaciones")');
        const observacionesCount = await observacionesSections.count();
        console.log(`Secciones "Observaciones" encontradas: ${observacionesCount}`);

        // Buscar todas las secciones de "Documentos Principales"
        const documentosSections = page.locator('h3:has-text("Documentos Principales")');
        const documentosCount = await documentosSections.count();
        console.log(`Secciones "Documentos Principales" encontradas: ${documentosCount}`);

        // Buscar todas las secciones de "Estadísticas del Proyecto"
        const estadisticasSections = page.locator('h3:has-text("Estadísticas del Proyecto")');
        const estadisticasCount = await estadisticasSections.count();
        console.log(`Secciones "Estadísticas del Proyecto" encontradas: ${estadisticasCount}`);

        // Verificar si hay duplicaciones
        if (observacionesCount > 1) {
            console.log('⚠️  DUPLICACIÓN DETECTADA: Múltiples secciones de Observaciones');
        }

        if (documentosCount > 1) {
            console.log('⚠️  DUPLICACIÓN DETECTADA: Múltiples secciones de Documentos Principales');
        }

        if (estadisticasCount > 1) {
            console.log('⚠️  DUPLICACIÓN DETECTADA: Múltiples secciones de Estadísticas');
        }

        // Tomar screenshot para análisis visual
        await page.screenshot({
            path: 'debug-obras-show-duplications.png',
            fullPage: true
        });

        console.log('\n=== ANÁLISIS COMPLETADO ===');
        console.log('Screenshot guardado como: debug-obras-show-duplications.png');
    });
});