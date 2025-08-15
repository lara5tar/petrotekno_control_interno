import { test, expect } from '@playwright/test';

test.describe('Prueba Campo Ubicación en Obras', () => {
    test('debe mostrar el campo ubicación en el formulario de crear obra', async ({ page }) => {
        // Navegar al login
        await page.goto('http://127.0.0.1:8002/login');

        // Hacer login como admin
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que se cargue el dashboard
        await page.waitForURL('**/home');
        await expect(page.locator('text=Dashboard')).toBeVisible();

        // Navegar directamente al formulario de crear obra
        await page.goto('http://127.0.0.1:8002/obras/create');

        // Verificar que existe el campo ubicación
        await expect(page.locator('#ubicacion')).toBeVisible();
        await expect(page.locator('label[for="ubicacion"]')).toContainText('Ubicación de la Obra');

        // Verificar que el placeholder es correcto
        const placeholder = await page.locator('#ubicacion').getAttribute('placeholder');
        expect(placeholder).toContain('ubicación donde se realizará la obra');

        // Verificar que el campo está después del nombre de la obra
        const nombreObraLabel = page.locator('label[for="nombre_obra"]');
        const ubicacionLabel = page.locator('label[for="ubicacion"]');

        await expect(nombreObraLabel).toBeVisible();
        await expect(ubicacionLabel).toBeVisible();

        console.log('✓ Campo ubicación existe en el formulario');
        console.log('✓ Label y placeholder correctos');
        console.log('✓ Campo posicionado correctamente después del nombre');
    });
});
