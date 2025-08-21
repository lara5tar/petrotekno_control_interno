import { test, expect } from '@playwright/test';

test.describe('Verificación PDF Inventario de Vehículos', () => {

    test('debe poder acceder a la URL del PDF directamente', async ({ page }) => {
        // Configurar el manejo de descarga
        const downloadPromise = page.waitForDownload();

        // Navegar directamente a la URL del PDF (asumiendo que el usuario ya está logueado)
        await page.goto('http://127.0.0.1:8000/reportes/inventario-vehiculos?formato=pdf');

        // Esperar la descarga
        const download = await downloadPromise;

        // Verificar que el archivo descargado es un PDF
        expect(download.suggestedFilename()).toMatch(/inventario_vehiculos_.*\.pdf/);

        // Verificar que el archivo no está vacío
        const downloadPath = await download.path();
        const { statSync } = await import('fs');
        const stats = statSync(downloadPath);
        expect(stats.size).toBeGreaterThan(1000); // El PDF debe tener al menos 1KB

        console.log(`✅ PDF generado correctamente: ${download.suggestedFilename()}`);
        console.log(`📄 Tamaño del archivo: ${(stats.size / 1024).toFixed(2)} KB`);
    });

    test('debe verificar que la respuesta no sea un error 500', async ({ page }) => {
        // Configurar el manejo de respuestas
        let responseStatus = 0;
        page.on('response', response => {
            if (response.url().includes('inventario-vehiculos') && response.url().includes('formato=pdf')) {
                responseStatus = response.status();
            }
        });

        // Intentar acceder al PDF
        try {
            const downloadPromise = page.waitForDownload({ timeout: 10000 });
            await page.goto('http://127.0.0.1:8000/reportes/inventario-vehiculos?formato=pdf');
            await downloadPromise;

            console.log(`✅ PDF generado sin errores de servidor`);
        } catch (error) {
            // Si hay error, verificar que no sea un 500
            expect(responseStatus).not.toBe(500);
            console.log(`⚠️ Error controlado: ${error.message}, status: ${responseStatus}`);
        }
    });

    test('debe poder generar PDF con filtros aplicados', async ({ page }) => {
        // Configurar el manejo de descarga
        const downloadPromise = page.waitForDownload();

        // Probar con filtro por estatus
        await page.goto('http://127.0.0.1:8000/reportes/inventario-vehiculos?formato=pdf&estatus=disponible');

        // Esperar la descarga
        const download = await downloadPromise;

        // Verificar que el archivo se generó
        expect(download.suggestedFilename()).toMatch(/inventario_vehiculos_.*\.pdf/);

        console.log(`✅ PDF con filtros generado: ${download.suggestedFilename()}`);
    });
});

test.describe('Verificación de Interfaz sin Descarga', () => {

    test('debe mostrar la página de reportes correctamente', async ({ page }) => {
        // Navegar a reportes sin descargar PDF
        await page.goto('http://127.0.0.1:8000/reportes');

        // Verificar que la página carga
        await expect(page.locator('body')).toBeVisible();

        // Buscar elementos relacionados con reportes de vehículos
        const content = await page.content();
        expect(content).toContain('Inventario');

        console.log('✅ Página de reportes carga correctamente');
    });

    test('debe mostrar la vista HTML del inventario', async ({ page }) => {
        // Navegar a la vista HTML del reporte
        await page.goto('http://127.0.0.1:8000/reportes/inventario-vehiculos');

        // Verificar que la página carga
        await expect(page.locator('body')).toBeVisible();

        // Verificar que hay contenido relacionado con vehículos
        const content = await page.content();
        expect(content).toContain('Inventario') || expect(content).toContain('Vehículos');

        console.log('✅ Vista HTML del inventario carga correctamente');
    });
});
