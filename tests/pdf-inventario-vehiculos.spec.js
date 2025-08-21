import { test, expect } from '@playwright/test';

test.describe('Reportes PDF - Inventario de Vehículos', () => {

    test.beforeEach(async ({ page }) => {
        // Navegar a la página de login
        await page.goto('http://127.0.0.1:8000/login');

        // Hacer login como administrador
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que se complete el login
        await page.waitForURL('**/dashboard');
    });

    test('debe generar PDF desde la página principal de reportes', async ({ page }) => {
        // Navegar a la página de reportes
        await page.goto('http://127.0.0.1:8000/reportes');

        // Verificar que la página de reportes carga correctamente
        await expect(page.locator('h1')).toContainText('Reportes del Sistema');

        // Buscar la tarjeta de Inventario de Vehículos
        const inventarioCard = page.locator('.bg-white').filter({ hasText: 'Inventario de Vehículos' });
        await expect(inventarioCard).toBeVisible();

        // Verificar que tiene el botón de Descargar PDF
        const pdfButton = inventarioCard.locator('a').filter({ hasText: 'Descargar PDF' });
        await expect(pdfButton).toBeVisible();

        // Configurar el manejo de descarga
        const downloadPromise = page.waitForDownload();

        // Hacer clic en el botón de Descargar PDF
        await pdfButton.click();

        // Esperar la descarga
        const download = await downloadPromise;

        // Verificar que el archivo descargado es un PDF
        expect(download.suggestedFilename()).toMatch(/inventario_vehiculos_.*\.pdf/);

        // Verificar que el archivo no está vacío
        const downloadPath = await download.path();
        const { statSync } = await import('fs');
        const stats = statSync(downloadPath);
        expect(stats.size).toBeGreaterThan(1000); // El PDF debe tener al menos 1KB

        console.log(`PDF descargado correctamente: ${download.suggestedFilename()}`);
        console.log(`Tamaño del archivo: ${stats.size} bytes`);
    });

    test('debe generar PDF desde la vista HTML del reporte', async ({ page }) => {
        // Navegar directamente a la vista HTML del reporte
        await page.goto('http://127.0.0.1:8000/reportes/inventario-vehiculos');

        // Verificar que la página del reporte carga correctamente
        await expect(page.locator('h1')).toContainText('Inventario de Vehículos');

        // Buscar el botón de PDF en las opciones de exportación
        const pdfButton = page.locator('a').filter({ hasText: 'PDF' });
        await expect(pdfButton).toBeVisible();

        // Configurar el manejo de descarga
        const downloadPromise = page.waitForDownload();

        // Hacer clic en el botón de PDF
        await pdfButton.click();

        // Esperar la descarga
        const download = await downloadPromise;

        // Verificar que el archivo descargado es un PDF
        expect(download.suggestedFilename()).toMatch(/inventario_vehiculos_.*\.pdf/);

        console.log(`PDF descargado desde vista HTML: ${download.suggestedFilename()}`);
    });

    test('debe manejar PDF con filtros aplicados', async ({ page }) => {
        // Navegar a la vista del reporte con filtros
        await page.goto('http://127.0.0.1:8000/reportes/inventario-vehiculos');

        // Aplicar filtro por estatus si hay opciones disponibles
        const estatusSelect = page.locator('select[name="estatus"]');
        if (await estatusSelect.count() > 0) {
            await estatusSelect.selectOption({ index: 1 }); // Seleccionar el primer estado disponible

            // Aplicar filtros
            const filterButton = page.locator('button').filter({ hasText: 'Filtrar' });
            if (await filterButton.count() > 0) {
                await filterButton.click();
                await page.waitForLoadState('networkidle');
            }
        }

        // Generar PDF con filtros aplicados
        const downloadPromise = page.waitForDownload();
        const pdfButton = page.locator('a').filter({ hasText: 'PDF' });
        await pdfButton.click();

        const download = await downloadPromise;
        expect(download.suggestedFilename()).toMatch(/inventario_vehiculos_.*\.pdf/);

        console.log(`PDF con filtros descargado: ${download.suggestedFilename()}`);
    });

    test('debe verificar que no hay errores al generar PDF', async ({ page }) => {
        // Configurar el manejo de errores de consola
        const consoleErrors = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
            }
        });

        // Configurar el manejo de errores de respuesta
        const responseErrors = [];
        page.on('response', response => {
            if (response.status() >= 400) {
                responseErrors.push(`${response.status()} ${response.url()}`);
            }
        });

        // Navegar a reportes y generar PDF
        await page.goto('http://127.0.0.1:8000/reportes');

        const inventarioCard = page.locator('.bg-white').filter({ hasText: 'Inventario de Vehículos' });
        const pdfButton = inventarioCard.locator('a').filter({ hasText: 'Descargar PDF' });

        const downloadPromise = page.waitForDownload();
        await pdfButton.click();

        // Esperar la descarga con un timeout más largo
        const download = await downloadPromise;

        // Verificar que no hubo errores
        expect(consoleErrors).toHaveLength(0);
        expect(responseErrors).toHaveLength(0);
        expect(download.suggestedFilename()).toMatch(/inventario_vehiculos_.*\.pdf/);

        console.log('PDF generado sin errores');
    });

    test('debe verificar acceso con permisos correctos', async ({ page }) => {
        // Verificar que el usuario tiene acceso a reportes
        await page.goto('http://127.0.0.1:8000/reportes');

        // No debe haber redirección a página de error 403
        await expect(page).not.toHaveURL(/.*403.*/);
        await expect(page).not.toHaveURL(/.*login.*/);

        // Debe mostrar la página de reportes
        await expect(page.locator('h1')).toContainText('Reportes del Sistema');

        // El botón de PDF debe estar disponible
        const inventarioCard = page.locator('.bg-white').filter({ hasText: 'Inventario de Vehículos' });
        const pdfButton = inventarioCard.locator('a').filter({ hasText: 'Descargar PDF' });
        await expect(pdfButton).toBeVisible();

        console.log('Usuario tiene permisos correctos para generar PDFs');
    });
});
