import { test, expect } from '@playwright/test';

test.describe('Editar Obra - Prueba Funcional', () => {
    test.beforeEach(async ({ page }) => {
        // Configurar base URL
        await page.goto('http://localhost:8000');

        // Login como administrador
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a ser redirigido después del login
        await page.waitForTimeout(2000);
    });

    test('debería cargar la página de edición de obra correctamente', async ({ page }) => {
        console.log('🧪 Probando: Carga de página de edición de obra');

        // Ir a la lista de obras
        await page.goto('http://localhost:8000/obras');
        await page.waitForLoadState('networkidle');

        // Verificar que hay un enlace de editar
        const editLinks = page.locator('a[href*="/obras/"][href*="/edit"]');
        const editLinksCount = await editLinks.count();

        if (editLinksCount === 0) {
            // Si no hay obras, crear una primero
            console.log('⚠️ No hay obras existentes, creando una obra de prueba...');
            await page.goto('http://localhost:8000/obras/create');
            await page.fill('input[name="nombre_obra"]', 'Obra Test para Edición Playwright');
            await page.selectOption('select[name="estatus"]', 'planificada');
            await page.fill('input[name="fecha_inicio"]', '2024-01-01');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(3000);

            // Volver a la lista de obras
            await page.goto('http://localhost:8000/obras');
            await page.waitForLoadState('networkidle');
        }

        // Hacer clic en el primer enlace de editar
        await editLinks.first().click();
        await page.waitForLoadState('networkidle');

        // Verificar que la página de edición cargó correctamente
        await expect(page.locator('h2')).toContainText('Editar Obra');
        await expect(page.locator('input[name="nombre_obra"]')).toBeVisible();
        await expect(page.locator('select[name="estatus"]')).toBeVisible();
        await expect(page.locator('button[type="submit"]')).toBeVisible();

        console.log('✅ Página de edición cargada correctamente');
    });

    test('debería actualizar una obra exitosamente', async ({ page }) => {
        console.log('🧪 Probando: Actualización exitosa de obra');

        // Ir directamente a editar la primera obra
        await page.goto('http://localhost:8000/obras');
        await page.waitForLoadState('networkidle');

        const editLink = page.locator('a[href*="/obras/"][href*="/edit"]').first();
        await editLink.click();
        await page.waitForLoadState('networkidle');

        // Verificar que estamos en la página de edición
        await expect(page.locator('h2')).toContainText('Editar Obra');

        // Obtener el nombre actual y modificarlo
        const nombreInput = page.locator('input[name="nombre_obra"]');
        const nombreActual = await nombreInput.inputValue();
        const timestamp = Date.now();
        const nuevoNombre = `${nombreActual} - Editado ${timestamp}`;

        // Actualizar los campos
        await nombreInput.clear();
        await nombreInput.fill(nuevoNombre);

        // Cambiar el estatus
        await page.selectOption('select[name="estatus"]', 'en_progreso');

        // Agregar observaciones
        const observacionesTextarea = page.locator('textarea[name="observaciones"]');
        if (await observacionesTextarea.count() > 0) {
            await observacionesTextarea.fill('Obra actualizada mediante prueba de Playwright');
        }

        // Asegurar que hay una fecha de inicio válida
        const fechaInput = page.locator('input[name="fecha_inicio"]');
        const fechaActual = await fechaInput.inputValue();
        if (!fechaActual) {
            await fechaInput.fill('2024-01-01');
        }

        // Enviar el formulario
        await page.click('button[type="submit"]');

        // Esperar la respuesta y verificar el resultado
        await page.waitForTimeout(3000);

        // Verificar que se redirigió correctamente (puede ser a lista de obras o mostrar mensaje de éxito)
        const currentUrl = page.url();
        const hasSuccessMessage = await page.locator('.alert-success, .bg-green-100').count() > 0;
        const isInObrasList = currentUrl.includes('/obras') && !currentUrl.includes('/edit');

        expect(isInObrasList || hasSuccessMessage).toBeTruthy();

        console.log('✅ Obra actualizada exitosamente');
    });

    test('debería validar campos requeridos', async ({ page }) => {
        console.log('🧪 Probando: Validación de campos requeridos');

        await page.goto('http://localhost:8000/obras');
        await page.waitForLoadState('networkidle');

        const editLink = page.locator('a[href*="/obras/"][href*="/edit"]').first();
        await editLink.click();
        await page.waitForLoadState('networkidle');

        // Limpiar el campo nombre (requerido)
        await page.fill('input[name="nombre_obra"]', '');

        // Intentar enviar el formulario
        await page.click('button[type="submit"]');
        await page.waitForTimeout(1000);

        // Verificar que permanece en la página de edición
        expect(page.url()).toContain('/edit');

        // Verificar validación HTML5 o mensaje de error
        const nombreInput = page.locator('input[name="nombre_obra"]');
        const isInvalid = await nombreInput.evaluate((el: HTMLInputElement) => !el.validity.valid);
        const hasErrorMessage = await page.locator('.alert-danger, .text-red-600, .error').count() > 0;

        expect(isInvalid || hasErrorMessage).toBeTruthy();

        console.log('✅ Validación de campos requeridos funcionando');
    });

    test('debería manejar la funcionalidad de navegación', async ({ page }) => {
        console.log('🧪 Probando: Funcionalidad de navegación');

        await page.goto('http://localhost:8000/obras');
        await page.waitForLoadState('networkidle');

        const editLink = page.locator('a[href*="/obras/"][href*="/edit"]').first();
        await editLink.click();
        await page.waitForLoadState('networkidle');

        // Verificar que los botones de navegación están presentes
        const volverButton = page.locator('a:has-text("Volver"), a:has-text("Cancelar")');
        await expect(volverButton.first()).toBeVisible();

        const verDetallesButton = page.locator('a:has-text("Ver Detalles")');
        if (await verDetallesButton.count() > 0) {
            await expect(verDetallesButton).toBeVisible();
        }

        console.log('✅ Botones de navegación presentes');
    });

    test('debería mostrar los campos principales del formulario', async ({ page }) => {
        console.log('🧪 Probando: Campos principales del formulario');

        await page.goto('http://localhost:8000/obras');
        await page.waitForLoadState('networkidle');

        const editLink = page.locator('a[href*="/obras/"][href*="/edit"]').first();
        await editLink.click();
        await page.waitForLoadState('networkidle');

        // Verificar campos principales
        await expect(page.locator('input[name="nombre_obra"]')).toBeVisible();
        await expect(page.locator('select[name="estatus"]')).toBeVisible();
        await expect(page.locator('input[name="fecha_inicio"]')).toBeVisible();

        // Verificar que tienen valores pre-llenados
        const nombreValue = await page.locator('input[name="nombre_obra"]').inputValue();
        const estatusValue = await page.locator('select[name="estatus"]').inputValue();

        expect(nombreValue.length).toBeGreaterThan(0);
        expect(estatusValue.length).toBeGreaterThan(0);

        console.log('✅ Campos principales del formulario correctos');
    });
});

// Prueba específica para verificar el backend
test.describe('Verificación del Backend - Editar Obra', () => {
    test('debería responder correctamente a las rutas de obra', async ({ page }) => {
        console.log('🧪 Probando: Respuesta del backend');

        // Verificar que el login funciona
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);

        // Verificar que la lista de obras responde
        const response = await page.goto('http://localhost:8000/obras');
        expect(response?.status()).toBe(200);

        // Verificar que no hay errores 500 en la página
        await page.waitForLoadState('networkidle');
        const pageContent = await page.content();
        expect(pageContent).not.toContain('500');
        expect(pageContent).not.toContain('Internal Server Error');

        console.log('✅ Backend respondiendo correctamente');
    });
});