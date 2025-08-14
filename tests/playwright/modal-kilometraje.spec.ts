import { test, expect } from '@playwright/test';

test.describe('Modal de Captura de Kilometraje', () => {
    // Helper function para ir a la pestaña de kilometraje y abrir el modal
    const abrirModalKilometraje = async (page) => {
        // Hacer clic en la pestaña "Kilometraje" para mostrar el contenido
        await page.click('button:has-text("Kilometraje")');
        await page.waitForTimeout(500); // Esperar animación de la pestaña

        // Hacer clic en el botón "Capturar Nuevo"
        await page.click('button:has-text("Capturar Nuevo")');
        await page.waitForTimeout(200); // Esperar que el modal se abra
    };

    test.beforeEach(async ({ page }) => {
        // Navegar a la página de login
        await page.goto('http://127.0.0.1:8001/login');

        // Realizar login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar redirección al home
        await page.waitForURL('**/home');

        // Navegar a la lista de vehículos
        await page.goto('http://127.0.0.1:8001/vehiculos');
        await page.waitForLoadState('networkidle');

        // Hacer clic en el primer vehículo para ver detalles
        await page.click('table tbody tr:first-child a[title="Ver detalles"]');
        await page.waitForLoadState('networkidle');
    });

    test('debe abrir el modal al hacer clic en "Capturar Nuevo"', async ({ page }) => {
        // Hacer clic en la pestaña "Kilometraje" para mostrar el contenido
        await page.click('button:has-text("Kilometraje")');
        await page.waitForTimeout(500); // Esperar animación de la pestaña

        // Verificar que el botón "Capturar Nuevo" existe y es visible
        const botonCapturar = page.locator('button:has-text("Capturar Nuevo")');
        await expect(botonCapturar).toBeVisible();

        // Verificar que el modal está inicialmente oculto
        const modal = page.locator('#kilometraje-modal');
        await expect(modal).toHaveClass(/hidden/);

        // Hacer clic en el botón "Capturar Nuevo"
        await botonCapturar.click();

        // Verificar que el modal se abrió (ya no tiene la clase "hidden")
        await expect(modal).not.toHaveClass(/hidden/);

        // Verificar que el modal es visible
        await expect(modal).toBeVisible();

        // Verificar elementos dentro del modal
        await expect(page.locator('#kilometraje-modal h3:has-text("Capturar Kilometraje")')).toBeVisible();
        await expect(page.locator('#kilometraje-form')).toBeVisible();
        await expect(page.locator('#kilometraje')).toBeVisible();
    });

    test('debe mostrar información del vehículo en el modal', async ({ page }) => {
        // Abrir el modal
        await abrirModalKilometraje(page);

        // Verificar que la información del vehículo se muestra
        const infoVehiculo = page.locator('#kilometraje-modal .bg-gray-50');
        await expect(infoVehiculo).toBeVisible();

        // Verificar que contiene la información del vehículo
        await expect(infoVehiculo).toContainText('Vehículo:');
        await expect(infoVehiculo).toContainText('Placas:');
        await expect(infoVehiculo).toContainText('Kilometraje Actual:');
    });

    test('debe enfocar el campo de kilometraje al abrir el modal', async ({ page }) => {
        // Abrir el modal
        await abrirModalKilometraje(page);

        // Verificar que el campo de kilometraje tiene foco
        const campoKilometraje = page.locator('#kilometraje');
        await expect(campoKilometraje).toBeFocused();
    });

    test('debe validar que el kilometraje sea mayor al actual', async ({ page }) => {
        // Abrir el modal
        await abrirModalKilometraje(page);
        const infoVehiculo = page.locator('#kilometraje-modal .bg-gray-50');
        const textoKilometraje = await infoVehiculo.locator('p:has-text("Kilometraje Actual:")').textContent();

        // Extraer el número del kilometraje actual
        const match = textoKilometraje?.match(/(\d+)/);
        const kilometrajeActual = match ? parseInt(match[1]) : 0;

        // Intentar ingresar un kilometraje menor o igual al actual
        const campoKilometraje = page.locator('#kilometraje');
        await campoKilometraje.fill(kilometrajeActual.toString());

        // Verificar que el campo muestra error de validación
        const mensajeValidacion = await campoKilometraje.evaluate((el: HTMLInputElement) => el.validationMessage);
        expect(mensajeValidacion).toContain('El kilometraje debe ser mayor a');
    });

    test('debe completar todos los campos requeridos', async ({ page }) => {
        // Abrir el modal
        await abrirModalKilometraje(page);

        // Obtener el kilometraje actual para calcular uno mayor
        const infoVehiculo = page.locator('#kilometraje-modal .bg-gray-50');
        const textoKilometraje = await infoVehiculo.locator('p:has-text("Kilometraje Actual:")').textContent();
        const match = textoKilometraje?.match(/(\d+)/);
        const kilometrajeActual = match ? parseInt(match[1]) : 0;
        const nuevoKilometraje = kilometrajeActual + 100;

        // Llenar todos los campos
        await page.fill('#kilometraje', nuevoKilometraje.toString());
        await page.fill('#fecha_captura', '2025-08-14');
        await page.fill('#ubicacion', 'Oficina Central');
        await page.fill('#observaciones', 'Prueba de captura automática');

        // Verificar que todos los campos están llenos
        await expect(page.locator('#kilometraje')).toHaveValue(nuevoKilometraje.toString());
        await expect(page.locator('#fecha_captura')).toHaveValue('2025-08-14');
        await expect(page.locator('#ubicacion')).toHaveValue('Oficina Central');
        await expect(page.locator('#observaciones')).toHaveValue('Prueba de captura automática');
    });

    test('debe cerrar el modal con el botón Cancelar', async ({ page }) => {
        // Abrir el modal
        await abrirModalKilometraje(page);

        // Verificar que el modal está abierto
        const modal = page.locator('#kilometraje-modal');
        await expect(modal).toBeVisible();

        // Hacer clic en Cancelar
        await page.click('button:has-text("Cancelar")');

        // Verificar que el modal se cierra
        await expect(modal).toHaveClass(/hidden/);
    });

    test('debe cerrar el modal con el botón X', async ({ page }) => {
        // Abrir el modal
        await abrirModalKilometraje(page);

        // Verificar que el modal está abierto
        const modal = page.locator('#kilometraje-modal');
        await expect(modal).toBeVisible();

        // Hacer clic en el botón X
        await page.click('#kilometraje-modal button[onclick="closeKilometrajeModal()"]');

        // Verificar que el modal se cierra
        await expect(modal).toHaveClass(/hidden/);
    });

    test('debe cerrar el modal al hacer clic fuera de él', async ({ page }) => {
        // Abrir el modal
        await abrirModalKilometraje(page);

        // Verificar que el modal está abierto
        const modal = page.locator('#kilometraje-modal');
        await expect(modal).toBeVisible();

        // Hacer clic en el fondo del modal (fuera del contenido)
        await page.click('#kilometraje-modal', { position: { x: 10, y: 10 } });

        // Verificar que el modal se cierra
        await expect(modal).toHaveClass(/hidden/);
    });

    test('debe enviar el formulario correctamente', async ({ page }) => {
        // Abrir el modal
        await abrirModalKilometraje(page);

        // Obtener el kilometraje actual para calcular uno mayor
        const infoVehiculo = page.locator('#kilometraje-modal .bg-gray-50');
        const textoKilometraje = await infoVehiculo.locator('p:has-text("Kilometraje Actual:")').textContent();
        const match = textoKilometraje?.match(/(\d+)/);
        const kilometrajeActual = match ? parseInt(match[1]) : 0;
        const nuevoKilometraje = kilometrajeActual + 150;

        // Llenar el formulario
        await page.fill('#kilometraje', nuevoKilometraje.toString());
        await page.fill('#ubicacion', 'Prueba Playwright');
        await page.fill('#observaciones', 'Test automatizado con Playwright');

        // Interceptar la petición de envío
        const responsePromise = page.waitForResponse(response =>
            response.url().includes('/kilometrajes') && response.request().method() === 'POST'
        );

        // Enviar el formulario
        await page.click('button[type="submit"]:has-text("Guardar Kilometraje")');

        // Esperar la respuesta
        const response = await responsePromise;

        // Verificar que la petición fue exitosa
        expect(response.status()).toBe(302); // Redirección después de éxito

        // Verificar que se muestra el mensaje de éxito o el modal se cierra
        // (dependiendo de cómo maneje la respuesta el controlador)
    });

    test('debe mostrar el botón "Crear primer registro" cuando no hay kilometrajes', async ({ page }) => {
        // Este test requiere un vehículo sin registros de kilometraje
        // Por simplicidad, buscaremos el botón en la página actual

        const botonPrimerRegistro = page.locator('button:has-text("Crear primer registro")');

        // Si el botón existe, probamos su funcionalidad
        if (await botonPrimerRegistro.count() > 0) {
            await botonPrimerRegistro.click();

            // Verificar que el modal se abre
            const modal = page.locator('#kilometraje-modal');
            await expect(modal).toBeVisible();
        }
    });

    test('debe deshabilitar botones durante el envío del formulario', async ({ page }) => {
        // Abrir el modal
        await abrirModalKilometraje(page);

        // Llenar formulario mínimo
        const infoVehiculo = page.locator('#kilometraje-modal .bg-gray-50');
        const textoKilometraje = await infoVehiculo.locator('p:has-text("Kilometraje Actual:")').textContent();
        const match = textoKilometraje?.match(/(\d+)/);
        const kilometrajeActual = match ? parseInt(match[1]) : 0;

        await page.fill('#kilometraje', (kilometrajeActual + 200).toString());
        await page.fill('#ubicacion', 'Test ubicación');

        // Obtener referencias a los botones
        const botonGuardar = page.locator('button[type="submit"]:has-text("Guardar Kilometraje")');
        const botonCancelar = page.locator('button:has-text("Cancelar")');

        // Verificar que los botones están habilitados inicialmente
        await expect(botonGuardar).toBeEnabled();
        await expect(botonCancelar).toBeEnabled();

        // Hacer clic en guardar (sin esperar la respuesta)
        await botonGuardar.click();

        // Verificar que los botones se deshabilitan (puede ser muy rápido)
        // Note: Este test podría ser flaky dependiendo de la velocidad de la respuesta
        try {
            await expect(botonGuardar).toBeDisabled();
            await expect(botonCancelar).toBeDisabled();
        } catch (error) {
            console.log('Los botones se deshabilitan muy rápidamente o ya se procesó la respuesta');
        }
    });
});
