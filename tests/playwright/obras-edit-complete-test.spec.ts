import { test, expect, Page } from '@playwright/test';

test.describe('Obras Edit - Prueba Completa de Funcionalidad', () => {
    let page: Page;

    test.beforeEach(async ({ browser }) => {
        page = await browser.newPage();

        // Configurar el contexto para capturar errores
        page.on('console', msg => {
            if (msg.type() === 'error') {
                console.log(`❌ Error de consola: ${msg.text()}`);
            }
        });

        page.on('pageerror', error => {
            console.log(`❌ Error de página: ${error.message}`);
        });

        // Login como usuario con permisos de administrador
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Verificar que el login fue exitoso
        await page.waitForURL('**/dashboard');
        console.log('✅ Login exitoso');
    });

    test('debería cargar correctamente la página de edición de obra', async () => {
        console.log('🧪 Probando: Carga de página de edición');

        // Ir a la lista de obras
        await page.goto('/obras');
        await page.waitForLoadState('networkidle');

        // Buscar una obra existente para editar
        const editLinks = await page.locator('a[href*="/obras/"][href*="/edit"]').count();

        if (editLinks === 0) {
            console.log('⚠️ No hay obras para editar, creando una obra primero...');

            // Crear una obra para poder editarla
            await page.goto('/obras/create');
            await page.fill('input[name="nombre_obra"]', 'Obra Test para Edición');
            await page.selectOption('select[name="estatus"]', 'planificada');
            await page.fill('input[name="fecha_inicio"]', '2024-01-01');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            // Volver a la lista
            await page.goto('/obras');
            await page.waitForLoadState('networkidle');
        }

        // Hacer clic en el primer enlace de editar
        const firstEditLink = page.locator('a[href*="/obras/"][href*="/edit"]').first();
        await expect(firstEditLink).toBeVisible();
        await firstEditLink.click();

        // Verificar que la página de edición cargó correctamente
        await page.waitForLoadState('networkidle');

        // Verificar elementos clave de la página
        await expect(page.locator('h2:has-text("Editar Obra")')).toBeVisible();
        await expect(page.locator('input[name="nombre_obra"]')).toBeVisible();
        await expect(page.locator('select[name="estatus"]')).toBeVisible();
        await expect(page.locator('button[type="submit"]:has-text("Actualizar")')).toBeVisible();

        console.log('✅ Página de edición cargada correctamente');
    });

    test('debería validar campos requeridos en el formulario', async () => {
        console.log('🧪 Probando: Validación de campos requeridos');

        // Ir directamente a editar una obra (asumiendo que existe la obra con ID 1)
        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Limpiar el campo nombre_obra (requerido)
        await page.fill('input[name="nombre_obra"]', '');

        // Limpiar el campo estatus
        await page.selectOption('select[name="estatus"]', '');

        // Intentar enviar el formulario
        await page.click('button[type="submit"]');
        await page.waitForTimeout(1000);

        // Verificar que permanece en la página de edición (no redirige)
        expect(page.url()).toContain('/edit');

        // Verificar que hay mensajes de error o validación HTML5
        const nombreInput = page.locator('input[name="nombre_obra"]');
        const estatusSelect = page.locator('select[name="estatus"]');

        // Verificar validación HTML5
        const isNombreInvalid = await nombreInput.evaluate((el: HTMLInputElement) => !el.validity.valid);
        const isEstatusInvalid = await estatusSelect.evaluate((el: HTMLSelectElement) => !el.validity.valid);

        expect(isNombreInvalid || isEstatusInvalid).toBeTruthy();

        console.log('✅ Validación de campos requeridos funcionando');
    });

    test('debería actualizar obra exitosamente con datos válidos', async () => {
        console.log('🧪 Probando: Actualización exitosa de obra');

        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Obtener el nombre actual para verificar el cambio
        const nombreActual = await page.inputValue('input[name="nombre_obra"]');
        const timestamp = Date.now();
        const nuevoNombre = `${nombreActual} - Editado ${timestamp}`;

        // Actualizar campos del formulario
        await page.fill('input[name="nombre_obra"]', nuevoNombre);
        await page.selectOption('select[name="estatus"]', 'en_progreso');
        await page.fill('input[name="avance"]', '50');
        await page.fill('textarea[name="observaciones"]', 'Observaciones actualizadas desde prueba');

        // Verificar que la fecha de inicio esté llena (requerida)
        const fechaInicio = await page.inputValue('input[name="fecha_inicio"]');
        if (!fechaInicio) {
            await page.fill('input[name="fecha_inicio"]', '2024-01-01');
        }

        // Enviar formulario
        await page.click('button[type="submit"]');

        // Esperar la respuesta
        await page.waitForLoadState('networkidle');

        // Verificar redirección exitosa (debería ir a la lista de obras o mostrar mensaje de éxito)
        const currentUrl = page.url();
        const hasSuccessMessage = await page.locator('.alert-success, .bg-green-100').count() > 0;

        const isSuccess = currentUrl.includes('/obras') && !currentUrl.includes('/edit') || hasSuccessMessage;

        expect(isSuccess).toBeTruthy();

        console.log('✅ Obra actualizada exitosamente');
    });

    test('debería manejar la funcionalidad de la barra de progreso', async () => {
        console.log('🧪 Probando: Funcionalidad de barra de progreso');

        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Cambiar el valor de avance
        await page.fill('input[name="avance"]', '75');

        // Verificar que la barra de progreso se actualiza
        await page.waitForTimeout(500);

        const progressBar = page.locator('#progressBar');
        const progressText = page.locator('#progressText');

        await expect(progressText).toContainText('75% completado');

        // Verificar que el estatus "completada" actualiza automáticamente el avance a 100
        await page.selectOption('select[name="estatus"]', 'completada');
        await page.waitForTimeout(500);

        const avanceValue = await page.inputValue('input[name="avance"]');
        expect(avanceValue).toBe('100');

        await expect(progressText).toContainText('100% completado');

        console.log('✅ Barra de progreso funcionando correctamente');
    });

    test('debería validar fechas correctamente', async () => {
        console.log('🧪 Probando: Validación de fechas');

        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Establecer una fecha de inicio
        await page.fill('input[name="fecha_inicio"]', '2024-06-01');

        // Establecer una fecha de fin anterior a la de inicio (inválido)
        await page.fill('input[name="fecha_fin"]', '2024-05-01');

        // Intentar enviar el formulario
        await page.click('button[type="submit"]');
        await page.waitForTimeout(1000);

        // Debería permanecer en la página de edición o mostrar error
        expect(page.url()).toContain('/edit');

        // Corregir las fechas
        await page.fill('input[name="fecha_fin"]', '2024-07-01');

        console.log('✅ Validación de fechas funcionando');
    });

    test('debería manejar la subida de archivos correctamente', async () => {
        console.log('🧪 Probando: Funcionalidad de subida de archivos');

        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Crear un archivo temporal para la prueba
        const testFileContent = 'Contenido de prueba para archivo PDF';

        // Verificar que los elementos de subida de archivos están presentes
        await expect(page.locator('input[name="archivo_contrato"]')).toBeAttached();
        await expect(page.locator('input[name="archivo_fianza"]')).toBeAttached();
        await expect(page.locator('input[name="archivo_acta_entrega_recepcion"]')).toBeAttached();

        // Verificar que los labels están presentes y son clickeables
        await expect(page.locator('label[for="archivo_contrato"]')).toBeVisible();
        await expect(page.locator('label[for="archivo_fianza"]')).toBeVisible();
        await expect(page.locator('label[for="archivo_acta_entrega_recepcion"]')).toBeVisible();

        console.log('✅ Elementos de subida de archivos presentes');
    });

    test('debería mantener la funcionalidad de navegación', async () => {
        console.log('🧪 Probando: Funcionalidad de navegación');

        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Verificar botón "Volver al Listado"
        const volverButton = page.locator('a:has-text("Volver al Listado")');
        await expect(volverButton).toBeVisible();

        // Verificar que el href es correcto
        const href = await volverButton.getAttribute('href');
        expect(href).toContain('/obras');

        // Verificar botón "Ver Detalles"
        const verDetallesButton = page.locator('a:has-text("Ver Detalles")');
        await expect(verDetallesButton).toBeVisible();

        // Verificar botón "Cancelar"
        const cancelarButton = page.locator('a:has-text("Cancelar")');
        await expect(cancelarButton).toBeVisible();

        console.log('✅ Navegación funcionando correctamente');
    });

    test('debería manejar errores de servidor apropiadamente', async () => {
        console.log('🧪 Probando: Manejo de errores de servidor');

        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Interceptar el request de actualización para simular error 500
        await page.route('**/obras/*', route => {
            if (route.request().method() === 'PUT' || route.request().method() === 'PATCH') {
                route.fulfill({
                    status: 500,
                    contentType: 'text/html',
                    body: 'Internal Server Error'
                });
            } else {
                route.continue();
            }
        });

        // Llenar el formulario con datos válidos
        await page.fill('input[name="nombre_obra"]', 'Obra Test Error 500');
        await page.selectOption('select[name="estatus"]', 'en_progreso');

        // Intentar enviar el formulario
        await page.click('button[type="submit"]');

        // Esperar un momento para la respuesta
        await page.waitForTimeout(2000);

        // Debería manejar el error apropiadamente (permanecer en página o mostrar error)
        const hasErrorMessage = await page.locator('.alert-danger, .bg-red-100, .error').count() > 0;
        const staysOnPage = page.url().includes('/edit');

        expect(hasErrorMessage || staysOnPage).toBeTruthy();

        console.log('✅ Manejo de errores de servidor funcionando');
    });

    test('debería prevenir envío múltiple del formulario', async () => {
        console.log('🧪 Probando: Prevención de envío múltiple');

        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        let requestCount = 0;

        // Interceptar requests para contar envíos
        await page.route('**/obras/*', route => {
            if (route.request().method() === 'PUT' || route.request().method() === 'PATCH') {
                requestCount++;
                // Simular respuesta lenta
                setTimeout(() => {
                    route.fulfill({
                        status: 200,
                        contentType: 'text/html',
                        body: '<html><body>Success</body></html>'
                    });
                }, 1000);
            } else {
                route.continue();
            }
        });

        // Llenar datos válidos
        await page.fill('input[name="nombre_obra"]', 'Test Envío Multiple');

        const submitButton = page.locator('button[type="submit"]');

        // Hacer múltiples clicks rápidos
        await submitButton.click();
        await submitButton.click();
        await submitButton.click();

        // Esperar a que terminen las requests
        await page.waitForTimeout(2000);

        // Debería haber máximo una request
        expect(requestCount).toBeLessThanOrEqual(1);

        console.log(`✅ Prevención de envío múltiple funcionando (${requestCount} request(s))`);
    });

    test('debería mostrar información de auditoría correctamente', async () => {
        console.log('🧪 Probando: Información de auditoría');

        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Verificar que la sección de auditoría está presente
        await expect(page.locator('h3:has-text("Información de Auditoría")')).toBeVisible();

        // Verificar que muestra fecha de creación
        await expect(page.locator('label:has-text("Fecha de Creación")')).toBeVisible();

        // Verificar que muestra última actualización
        await expect(page.locator('label:has-text("Última Actualización")')).toBeVisible();

        console.log('✅ Información de auditoría visible');
    });

    test('debería manejar obras con asignaciones activas', async () => {
        console.log('🧪 Probando: Obras con asignaciones activas');

        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Verificar si hay alerta de asignaciones activas
        const alertaAsignaciones = page.locator('.bg-green-100:has-text("Obra con Asignaciones Activas")');

        if (await alertaAsignaciones.count() > 0) {
            await expect(alertaAsignaciones).toBeVisible();

            // Verificar botón para ver asignaciones
            const verAsignacionesButton = page.locator('a:has-text("Ver Asignaciones")');
            await expect(verAsignacionesButton).toBeVisible();

            console.log('✅ Alerta de asignaciones activas mostrada correctamente');
        } else {
            console.log('ℹ️ No hay asignaciones activas en esta obra');
        }
    });

    test.afterEach(async () => {
        console.log('🧹 Limpiando después de la prueba...');
        await page.close();
    });
});

// Test adicional para verificar la integridad de la actualización
test.describe('Obras Edit - Verificación de Integridad de Datos', () => {
    test('debería preservar los datos originales cuando la actualización falla', async ({ page }) => {
        console.log('🧪 Probando: Preservación de datos en fallo');

        // Login
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard');

        // Ir a editar obra
        await page.goto('/obras/1/edit');
        await page.waitForLoadState('networkidle');

        // Guardar valores originales
        const nombreOriginal = await page.inputValue('input[name="nombre_obra"]');
        const estatusOriginal = await page.inputValue('select[name="estatus"]');

        // Interceptar para simular fallo de red
        await page.route('**/obras/*', route => {
            if (route.request().method() === 'PUT' || route.request().method() === 'PATCH') {
                route.abort();
            } else {
                route.continue();
            }
        });

        // Modificar datos
        await page.fill('input[name="nombre_obra"]', 'Nombre Modificado');
        await page.selectOption('select[name="estatus"]', 'completada');

        // Intentar enviar
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);

        // Verificar que los datos modificados se mantienen en el formulario
        const nombreActual = await page.inputValue('input[name="nombre_obra"]');
        expect(nombreActual).toBe('Nombre Modificado');

        console.log('✅ Datos preservados correctamente en caso de fallo');
    });
});