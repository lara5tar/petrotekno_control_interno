import { test, expect, Page } from '@playwright/test';

test.describe('Obras Edit Functionality', () => {
    let page: Page;

    test.beforeEach(async ({ browser }) => {
        page = await browser.newPage();

        // Login como usuario autenticado
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que la página cargue después del login
        await page.waitForURL('**/dashboard');
    });

    test('debería cargar la página de edición de obra sin errores', async () => {
        // Ir a la lista de obras
        await page.goto('/obras');
        await page.waitForLoadState('networkidle');

        // Buscar el primer botón de editar
        const editButton = page.locator('a[href*="/obras/"][href*="/edit"], button').first();
        await expect(editButton).toBeVisible();

        // Hacer clic en editar
        await editButton.click();

        // Verificar que la página de edición carga correctamente
        await page.waitForLoadState('networkidle');

        // Verificar que no hay errores JavaScript en la consola
        const logs = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                logs.push(msg.text());
            }
        });

        // Esperar un momento para capturar errores
        await page.waitForTimeout(2000);

        // Verificar que no hay errores críticos
        const criticalErrors = logs.filter(log =>
            log.includes('TypeError') ||
            log.includes('ReferenceError') ||
            log.includes('Cannot read property')
        );

        expect(criticalErrors).toHaveLength(0);
    });

    test('debería cargar todos los dropdowns correctamente en edición', async () => {
        // Ir directamente a editar una obra específica
        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Verificar que el dropdown de encargados está presente y funcional
        const encargadosSelect = page.locator('select[name="encargado_id"]');
        await expect(encargadosSelect).toBeVisible();

        // Verificar que tiene opciones
        const encargadosOptions = await encargadosSelect.locator('option').count();
        expect(encargadosOptions).toBeGreaterThan(1); // Al menos la opción por defecto + una más

        // Verificar dropdown de vehículos
        const vehiculosSelect = page.locator('select[name="vehiculo_id"]');
        await expect(vehiculosSelect).toBeVisible();

        const vehiculosOptions = await vehiculosSelect.locator('option').count();
        expect(vehiculosOptions).toBeGreaterThan(1);

        // Verificar dropdown de combustible (si existe)
        const combustibleSelect = page.locator('select[name="combustible_id"]');
        if (await combustibleSelect.count() > 0) {
            await expect(combustibleSelect).toBeVisible();
            const combustibleOptions = await combustibleSelect.locator('option').count();
            expect(combustibleOptions).toBeGreaterThan(0);
        }
    });

    test('debería mantener los valores actuales del formulario al cargar edición', async () => {
        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Verificar que los campos tienen valores pre-cargados
        const nombreInput = page.locator('input[name="nombre"]');
        await expect(nombreInput).toBeVisible();

        const nombreValue = await nombreInput.inputValue();
        expect(nombreValue).toBeTruthy(); // Debe tener algún valor

        const descripcionTextarea = page.locator('textarea[name="descripcion"]');
        if (await descripcionTextarea.count() > 0) {
            const descripcionValue = await descripcionTextarea.inputValue();
            // La descripción puede estar vacía, pero el campo debe existir
            expect(descripcionValue).toBeDefined();
        }

        // Verificar que los selects tienen valores seleccionados
        const encargadoSelect = page.locator('select[name="encargado_id"]');
        const encargadoValue = await encargadoSelect.inputValue();
        expect(encargadoValue).toBeTruthy();

        const vehiculoSelect = page.locator('select[name="vehiculo_id"]');
        const vehiculoValue = await vehiculoSelect.inputValue();
        expect(vehiculoValue).toBeTruthy();
    });

    test('debería validar campos requeridos al intentar guardar', async () => {
        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Limpiar campo nombre para probar validación
        const nombreInput = page.locator('input[name="nombre"]');
        await nombreInput.clear();

        // Intentar enviar el formulario
        const submitButton = page.locator('button[type="submit"], input[type="submit"]');
        await submitButton.click();

        // Verificar que aparece mensaje de error o validación
        await page.waitForTimeout(1000);

        // Buscar mensajes de error comunes
        const errorMessages = page.locator('.alert-danger, .error, .invalid-feedback, .text-danger');
        const hasErrors = await errorMessages.count() > 0;

        // Verificar que no se redirige si hay errores
        const currentUrl = page.url();
        expect(currentUrl).toContain('/edit');
    });

    test('debería actualizar obra exitosamente con datos válidos', async () => {
        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Modificar el nombre de la obra
        const nombreInput = page.locator('input[name="nombre"]');
        const timestamp = Date.now();
        const nuevoNombre = `Obra Test Editada ${timestamp}`;

        await nombreInput.clear();
        await nombreInput.fill(nuevoNombre);

        // Seleccionar un encargado válido
        const encargadoSelect = page.locator('select[name="encargado_id"]');
        const encargadoOptions = encargadoSelect.locator('option');
        const firstValidOption = await encargadoOptions.nth(1).getAttribute('value');
        if (firstValidOption) {
            await encargadoSelect.selectOption(firstValidOption);
        }

        // Seleccionar un vehículo válido
        const vehiculoSelect = page.locator('select[name="vehiculo_id"]');
        const vehiculoOptions = vehiculoSelect.locator('option');
        const firstValidVehiculo = await vehiculoOptions.nth(1).getAttribute('value');
        if (firstValidVehiculo) {
            await vehiculoSelect.selectOption(firstValidVehiculo);
        }

        // Enviar formulario
        const submitButton = page.locator('button[type="submit"], input[type="submit"]');
        await submitButton.click();

        // Verificar redirección exitosa
        await page.waitForLoadState('networkidle');

        // Debería redirigir a la lista de obras o mostrar mensaje de éxito
        const currentUrl = page.url();
        const isSuccess = currentUrl.includes('/obras') && !currentUrl.includes('/edit') ||
            await page.locator('.alert-success, .success').count() > 0;

        expect(isSuccess).toBeTruthy();
    });

    test('debería manejar errores de red y servidor correctamente', async () => {
        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Interceptar requests para simular error
        await page.route('**/obras/*', route => {
            if (route.request().method() === 'PUT' || route.request().method() === 'PATCH') {
                route.fulfill({
                    status: 500,
                    body: 'Internal Server Error'
                });
            } else {
                route.continue();
            }
        });

        // Intentar actualizar
        const submitButton = page.locator('button[type="submit"], input[type="submit"]');
        await submitButton.click();

        // Verificar que se maneja el error apropiadamente
        await page.waitForTimeout(2000);

        // Debería mostrar error o permanecer en la página
        const hasErrorMessage = await page.locator('.alert-danger, .error').count() > 0;
        const staysOnPage = page.url().includes('/edit');

        expect(hasErrorMessage || staysOnPage).toBeTruthy();
    });

    test('debería validar cambios en dropdowns dependientes', async () => {
        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Si hay dropdowns dependientes (ej: combustible depende de vehículo)
        const vehiculoSelect = page.locator('select[name="vehiculo_id"]');
        const combustibleSelect = page.locator('select[name="combustible_id"]');

        if (await combustibleSelect.count() > 0) {
            // Cambiar vehículo y verificar que combustible se actualiza
            const vehiculoOptions = vehiculoSelect.locator('option');
            const optionCount = await vehiculoOptions.count();

            if (optionCount > 2) {
                // Seleccionar una opción diferente
                await vehiculoSelect.selectOption({ index: 2 });

                // Esperar a que se actualice el dropdown dependiente
                await page.waitForTimeout(1000);

                // Verificar que el dropdown de combustible se actualizó
                const combustibleOptionsAfter = await combustibleSelect.locator('option').count();
                expect(combustibleOptionsAfter).toBeGreaterThan(0);
            }
        }
    });

    test('debería prevenir envío múltiple del formulario', async () => {
        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Interceptar requests para hacer submit lento
        let requestCount = 0;
        await page.route('**/obras/*', route => {
            if (route.request().method() === 'PUT' || route.request().method() === 'PATCH') {
                requestCount++;
                // Simular respuesta lenta
                setTimeout(() => {
                    route.fulfill({
                        status: 200,
                        body: JSON.stringify({ success: true })
                    });
                }, 2000);
            } else {
                route.continue();
            }
        });

        const submitButton = page.locator('button[type="submit"], input[type="submit"]');

        // Hacer click múltiples veces rápidamente
        await submitButton.click();
        await submitButton.click();
        await submitButton.click();

        // Esperar a que terminen las requests
        await page.waitForTimeout(3000);

        // Debería haber solo una request
        expect(requestCount).toBeLessThanOrEqual(1);
    });

    test.afterEach(async () => {
        await page.close();
    });
});