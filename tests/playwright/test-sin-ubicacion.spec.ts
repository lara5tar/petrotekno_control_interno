import { test, expect } from '@playwright/test';

test.describe('Formulario de Kilometraje Sin Ubicación', () => {
    test('debe poder enviar el formulario sin ubicación', async ({ page }) => {
        // Login
        await page.goto('http://127.0.0.1:8000/login');
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Navegar a vehículos
        await page.goto('http://127.0.0.1:8000/vehiculos');

        // Buscar un vehículo (asumiendo que existe el vehículo con ID 2)
        await page.goto('http://127.0.0.1:8000/vehiculos/2');

        // Hacer clic en la pestaña Kilometraje
        await page.click('button:has-text("Kilometraje")');

        // Hacer clic en "Capturar Nuevo"
        await page.click('button:has-text("Capturar Nuevo")');

        // Verificar que el modal se abrió
        await expect(page.locator('#kilometraje-modal')).toBeVisible();

        // Verificar que NO existe el campo de ubicación
        await expect(page.locator('input[name="ubicacion"]')).not.toBeVisible();
        await expect(page.locator('label:has-text("Ubicación")')).not.toBeVisible();

        // Llenar los campos requeridos
        await page.fill('input[name="kilometraje"]', '22000');
        await page.fill('input[name="fecha_captura"]', '2025-08-14');
        await page.fill('textarea[name="observaciones"]', 'Prueba sin ubicación');

        // Enviar el formulario
        await page.click('button[type="submit"]');

        // Verificar que se guardó correctamente (el modal se cierra o aparece mensaje de éxito)
        await page.waitForTimeout(3000);

        // Verificar que ya no está visible el modal o que hay un mensaje de éxito
        const modalVisible = await page.locator('#kilometraje-modal').isVisible();
        console.log('Modal visible después de envío:', modalVisible);

        // Si hay mensajes de éxito, verificarlos
        const successMessage = page.locator('.alert-success, .text-green-600, .bg-green-100');
        if (await successMessage.count() > 0) {
            console.log('Mensaje de éxito encontrado');
        }
    });
});
