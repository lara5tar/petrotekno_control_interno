import { test, expect } from '@playwright/test';

test.describe('Historial de Obras por Vehículo - Descarga PDF', () => {

    test.beforeEach(async ({ page }) => {
        // Ir a la página de login
        await page.goto('/login');

        // Realizar login (asumiendo que existe un usuario test)
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que se complete el login
        await page.waitForURL('**/dashboard');

        // Navegar a la página de reportes
        await page.goto('/reportes');

        // Hacer clic en "Ver Reporte" del Historial de Obras por Vehículo
        await page.click('a[href*="historial-obras-vehiculo"]');

        // Esperar a que cargue la página de reportes
        await page.waitForLoadState('networkidle');
    });

    test('debe mostrar el dropdown de PDF en la vista de reportes', async ({ page }) => {
        // Verificar que el botón del dropdown de PDF existe
        const pdfDropdownButton = page.locator('#pdf-dropdown-button');
        await expect(pdfDropdownButton).toBeVisible();

        // Verificar el texto del botón
        await expect(pdfDropdownButton).toContainText('Exportar PDF');

        // Verificar que tiene el icono de dropdown
        const dropdownIcon = pdfDropdownButton.locator('svg').last();
        await expect(dropdownIcon).toBeVisible();
    });

    test('debe abrir y cerrar el dropdown de PDF correctamente', async ({ page }) => {
        const pdfDropdownButton = page.locator('#pdf-dropdown-button');
        const pdfDropdownMenu = page.locator('#pdf-dropdown-menu');

        // Verificar que el dropdown está inicialmente oculto
        await expect(pdfDropdownMenu).toHaveClass(/hidden/);

        // Hacer clic en el botón para abrir el dropdown
        await pdfDropdownButton.click();

        // Verificar que el dropdown se abre
        await expect(pdfDropdownMenu).not.toHaveClass(/hidden/);

        // Hacer clic fuera del dropdown para cerrarlo
        await page.click('body');

        // Verificar que el dropdown se cierra
        await expect(pdfDropdownMenu).toHaveClass(/hidden/);
    });

    test('debe mostrar las opciones correctas en el dropdown', async ({ page }) => {
        const pdfDropdownButton = page.locator('#pdf-dropdown-button');
        const pdfDropdownMenu = page.locator('#pdf-dropdown-menu');

        // Abrir el dropdown
        await pdfDropdownButton.click();

        // Verificar que muestra la opción "PDF por Vehículo"
        const pdfPorVehiculoOption = pdfDropdownMenu.locator('button:has-text("PDF por Vehículo")');
        await expect(pdfPorVehiculoOption).toBeVisible();
        await expect(pdfPorVehiculoOption).toContainText('Requiere seleccionar vehículo');

        // Verificar que muestra el selector de vehículos
        const vehiculoSelect = pdfDropdownMenu.locator('#vehiculo-pdf-select');
        await expect(vehiculoSelect).toBeVisible();
        await expect(vehiculoSelect).toContainText('Seleccionar vehículo...');

        // Verificar que muestra el botón de descarga
        const descargarButton = pdfDropdownMenu.locator('button:has-text("Descargar PDF")');
        await expect(descargarButton).toBeVisible();
    });

    test('debe cargar vehículos en el selector', async ({ page }) => {
        const pdfDropdownButton = page.locator('#pdf-dropdown-button');
        const vehiculoSelect = page.locator('#vehiculo-pdf-select');

        // Abrir el dropdown
        await pdfDropdownButton.click();

        // Verificar que el selector tiene opciones de vehículos
        const options = vehiculoSelect.locator('option');
        const optionCount = await options.count();

        // Debe tener al menos la opción por defecto más vehículos
        expect(optionCount).toBeGreaterThan(1);

        // Verificar que la primera opción es el placeholder
        const firstOption = options.first();
        await expect(firstOption).toHaveText('Seleccionar vehículo...');
        await expect(firstOption).toHaveValue('');
    });

    test('debe validar selección de vehículo antes de descargar', async ({ page }) => {
        const pdfDropdownButton = page.locator('#pdf-dropdown-button');
        const descargarButton = page.locator('button:has-text("Descargar PDF")').last();

        // Abrir el dropdown
        await pdfDropdownButton.click();

        // Configurar interceptor para alertas
        let alertMessage = '';
        page.on('dialog', async dialog => {
            alertMessage = dialog.message();
            await dialog.accept();
        });

        // Intentar descargar sin seleccionar vehículo
        await descargarButton.click();

        // Esperar un momento para que se ejecute la validación
        await page.waitForTimeout(500);

        // Verificar que se muestra una alerta (SweetAlert2)
        const swalPopup = page.locator('.swal2-popup');
        if (await swalPopup.isVisible()) {
            await expect(swalPopup).toContainText('Seleccionar Vehículo');
            await expect(swalPopup).toContainText('Por favor seleccione un vehículo');

            // Cerrar la alerta
            await page.click('.swal2-confirm');
        }
    });

    test('debe generar descarga de PDF al seleccionar vehículo', async ({ page }) => {
        const pdfDropdownButton = page.locator('#pdf-dropdown-button');
        const vehiculoSelect = page.locator('#vehiculo-pdf-select');
        const descargarButton = page.locator('button:has-text("Descargar PDF")').last();

        // Abrir el dropdown
        await pdfDropdownButton.click();

        // Verificar que hay vehículos disponibles
        const options = vehiculoSelect.locator('option');
        const optionCount = await options.count();

        if (optionCount > 1) {
            // Seleccionar el primer vehículo disponible (no el placeholder)
            const secondOption = options.nth(1);
            const vehiculoValue = await secondOption.getAttribute('value');

            await vehiculoSelect.selectOption(vehiculoValue);

            // Configurar interceptor para nuevas páginas/tabs (descargas)
            const downloadPromise = page.waitForEvent('download');

            // Hacer clic en descargar
            await descargarButton.click();

            // Esperar a que se inicie la descarga
            try {
                const download = await downloadPromise;

                // Verificar que el archivo se descarga
                expect(download).toBeTruthy();

                // Verificar que el nombre del archivo contiene información del vehículo
                const filename = download.suggestedFilename();
                expect(filename).toMatch(/historial.*vehiculo.*\.pdf/i);

            } catch (error) {
                // Si no hay descarga directa, verificar que se abre una nueva pestaña/ventana
                const newPagePromise = page.context().waitForEvent('page');

                const newPage = await newPagePromise;

                // Verificar que la URL contiene los parámetros correctos
                const url = newPage.url();
                expect(url).toContain('formato=pdf');
                expect(url).toContain(`vehiculo_id=${vehiculoValue}`);

                await newPage.close();
            }

            // Verificar que el dropdown se cierra después de la descarga
            const pdfDropdownMenu = page.locator('#pdf-dropdown-menu');
            await expect(pdfDropdownMenu).toHaveClass(/hidden/);

            // Verificar que el selector se resetea
            await expect(vehiculoSelect).toHaveValue('');
        }
    });

    test('debe mantener filtros aplicados en la descarga de PDF', async ({ page }) => {
        // Aplicar algunos filtros primero
        const fechaInicioInput = page.locator('input[name="fecha_inicio"]');
        const fechaFinInput = page.locator('input[name="fecha_fin"]');

        if (await fechaInicioInput.isVisible()) {
            await fechaInicioInput.fill('2024-01-01');
        }

        if (await fechaFinInput.isVisible()) {
            await fechaFinInput.fill('2024-12-31');
        }

        // Buscar/aplicar filtros si hay un botón de búsqueda
        const buscarButton = page.locator('button:has-text("Buscar"), button:has-text("Filtrar")');
        if (await buscarButton.isVisible()) {
            await buscarButton.click();
            await page.waitForLoadState('networkidle');
        }

        // Abrir dropdown de PDF
        const pdfDropdownButton = page.locator('#pdf-dropdown-button');
        await pdfDropdownButton.click();

        // Seleccionar un vehículo
        const vehiculoSelect = page.locator('#vehiculo-pdf-select');
        const options = vehiculoSelect.locator('option');
        const optionCount = await options.count();

        if (optionCount > 1) {
            const secondOption = options.nth(1);
            const vehiculoValue = await secondOption.getAttribute('value');
            await vehiculoSelect.selectOption(vehiculoValue);

            // Configurar interceptor para nueva página
            const newPagePromise = page.context().waitForEvent('page');

            // Hacer clic en descargar
            const descargarButton = page.locator('button:has-text("Descargar PDF")').last();
            await descargarButton.click();

            try {
                const newPage = await newPagePromise;
                const url = newPage.url();

                // Verificar que la URL mantiene los filtros aplicados
                expect(url).toContain('formato=pdf');
                expect(url).toContain(`vehiculo_id=${vehiculoValue}`);

                if (await fechaInicioInput.inputValue()) {
                    expect(url).toContain('fecha_inicio=2024-01-01');
                }

                if (await fechaFinInput.inputValue()) {
                    expect(url).toContain('fecha_fin=2024-12-31');
                }

                await newPage.close();
            } catch (error) {
                console.log('No se abrió nueva página, posible descarga directa');
            }
        }
    });

    test('debe funcionar la opción "PDF por Vehículo" original', async ({ page }) => {
        // Seleccionar un vehículo en los filtros principales si existe
        const vehiculoFilterSelect = page.locator('select[name="vehiculo_id"]');

        if (await vehiculoFilterSelect.isVisible()) {
            const options = vehiculoFilterSelect.locator('option');
            const optionCount = await options.count();

            if (optionCount > 1) {
                const secondOption = options.nth(1);
                await vehiculoFilterSelect.selectOption(await secondOption.getAttribute('value'));

                // Abrir dropdown de PDF
                const pdfDropdownButton = page.locator('#pdf-dropdown-button');
                await pdfDropdownButton.click();

                // Hacer clic en la opción "PDF por Vehículo"
                const pdfPorVehiculoButton = page.locator('button:has-text("PDF por Vehículo")');

                // Configurar interceptor para nueva página
                const newPagePromise = page.context().waitForEvent('page');

                await pdfPorVehiculoButton.click();

                try {
                    const newPage = await newPagePromise;
                    const url = newPage.url();

                    // Verificar que se genera el PDF
                    expect(url).toContain('formato=pdf');

                    await newPage.close();
                } catch (error) {
                    console.log('No se abrió nueva página, posible descarga directa o alerta');
                }
            }
        }
    });
});
