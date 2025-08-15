import { test, expect } from '@playwright/test';

test.describe('Verificar Ubicación en Vista de Vehículo', () => {
    test('debe mostrar la ubicación de la obra actual en la vista del vehículo', async ({ page }) => {
        // Navegar al login
        await page.goto('http://127.0.0.1:8003/login');

        // Hacer login como admin
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que se cargue el dashboard
        await page.waitForURL('**/home');
        await expect(page.locator('text=Dashboard')).toBeVisible();

        // Navegar directamente al vehículo con ID 1
        await page.goto('http://127.0.0.1:8003/vehiculos/1');

        // Verificar que se carga la página del vehículo
        await expect(page.locator('h2')).toContainText('Detalles del Vehículo');

        // Buscar la sección "Obra Actual"
        await expect(page.locator('text=Obra Actual')).toBeVisible();

        // Verificar que existe el campo de ubicación
        await expect(page.locator('text=Ubicación de la Obra')).toBeVisible();

        // Verificar que la ubicación específica aparece
        await expect(page.locator('text=Ciudad de México, CDMX - Zona Centro')).toBeVisible();

        // Verificar que también aparece el nombre de la obra
        await expect(page.locator('text=Obra Test 1')).toBeVisible();

        console.log('✓ Campo "Ubicación de la Obra" visible');
        console.log('✓ Ubicación "Ciudad de México, CDMX - Zona Centro" mostrada');
        console.log('✓ Sección de obra actual funcionando correctamente');
    });
});
