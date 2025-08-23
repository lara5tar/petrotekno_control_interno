import { test, expect } from '@playwright/test';

test.describe('Verificación Directa - Elementos PDF por Vehículo', () => {

    test('verificar elementos HTML en página real', async ({ page }) => {
        // Ir directamente a la página
        await page.goto('/reportes/historial-obras-vehiculo');

        // Esperar a que se cargue
        await page.waitForTimeout(2000);

        console.log('URL actual:', page.url());
        console.log('Título de página:', await page.title());

        // Tomar screenshot para debug
        await page.screenshot({ path: 'debug-page-elements.png', fullPage: true });

        // Buscar el elemento del dropdown por diferentes métodos
        const dropdownButtonByText = page.locator('text=Exportar PDF');
        const dropdownButtonById = page.locator('#pdf-dropdown-button');
        const dropdownButtonByClass = page.locator('button:has-text("Exportar PDF")');

        console.log('Dropdown por texto existe:', await dropdownButtonByText.count());
        console.log('Dropdown por ID existe:', await dropdownButtonById.count());
        console.log('Dropdown por clase existe:', await dropdownButtonByClass.count());

        // Verificar si existe algún botón de PDF
        const anyPdfButton = page.locator('button, a').filter({ hasText: /PDF|pdf/ });
        console.log('Botones con "PDF" encontrados:', await anyPdfButton.count());

        // Listar todos los botones en la página
        const allButtons = page.locator('button, a');
        const buttonCount = await allButtons.count();
        console.log(`Total de botones/enlaces encontrados: ${buttonCount}`);

        for (let i = 0; i < Math.min(buttonCount, 10); i++) {
            const buttonText = await allButtons.nth(i).textContent();
            console.log(`Botón ${i}: "${buttonText}"`);
        }

        // Verificar elementos específicos del dropdown
        const dropdownMenu = page.locator('#pdf-dropdown-menu');
        const vehiculoSelect = page.locator('#vehiculo-pdf-select');

        console.log('Dropdown menu existe:', await dropdownMenu.count());
        console.log('Selector de vehículo existe:', await vehiculoSelect.count());

        // Verificar contenido de la página
        const pageContent = await page.content();
        console.log('Página contiene "pdf-dropdown-button":', pageContent.includes('pdf-dropdown-button'));
        console.log('Página contiene "Exportar PDF":', pageContent.includes('Exportar PDF'));
        console.log('Página contiene "descargarPDFVehiculo":', pageContent.includes('descargarPDFVehiculo'));
    });

    test('verificar JavaScript está cargado', async ({ page }) => {
        await page.goto('/reportes/historial-obras-vehiculo');
        await page.waitForTimeout(2000);

        // Verificar que las funciones JavaScript existen
        const functions = await page.evaluate(() => {
            return {
                validarDescargaPDF: typeof validarDescargaPDF !== 'undefined',
                descargarPDFVehiculo: typeof descargarPDFVehiculo !== 'undefined',
                Swal: typeof Swal !== 'undefined'
            };
        });

        console.log('Funciones JavaScript disponibles:', functions);

        // Verificar que los event listeners están agregados
        const hasEventListeners = await page.evaluate(() => {
            const button = document.getElementById('pdf-dropdown-button');
            return button !== null;
        });

        console.log('Botón dropdown encontrado en DOM:', hasEventListeners);
    });

    test('simular clic completo en elementos reales', async ({ page }) => {
        await page.goto('/reportes/historial-obras-vehiculo');
        await page.waitForTimeout(3000);

        try {
            // Intentar encontrar y hacer clic en el botón dropdown
            const pdfButton = page.locator('#pdf-dropdown-button').first();

            if (await pdfButton.isVisible()) {
                console.log('✅ Botón PDF dropdown encontrado y visible');

                await pdfButton.click();
                console.log('✅ Clic en botón dropdown realizado');

                // Verificar que el dropdown se abre
                const dropdownMenu = page.locator('#pdf-dropdown-menu');
                await page.waitForTimeout(500);

                if (await dropdownMenu.isVisible()) {
                    console.log('✅ Dropdown menu se abrió correctamente');

                    // Verificar elementos dentro del dropdown
                    const vehiculoSelect = page.locator('#vehiculo-pdf-select');
                    const descargarButton = page.locator('button:has-text("Descargar PDF")').last();

                    console.log('Selector de vehículo visible:', await vehiculoSelect.isVisible());
                    console.log('Botón descargar visible:', await descargarButton.isVisible());

                    // Verificar opciones del selector
                    const options = vehiculoSelect.locator('option');
                    const optionCount = await options.count();
                    console.log(`Opciones de vehículo encontradas: ${optionCount}`);

                    if (optionCount > 1) {
                        console.log('✅ Hay vehículos disponibles para seleccionar');
                    }

                } else {
                    console.log('❌ Dropdown menu no se abrió');
                }

            } else {
                console.log('❌ Botón PDF dropdown no encontrado o no visible');
            }

        } catch (error) {
            console.log('Error durante la simulación:', error.message);
        }

        // Tomar screenshot final
        await page.screenshot({ path: 'debug-final-state.png', fullPage: true });
    });
});
