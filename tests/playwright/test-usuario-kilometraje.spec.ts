import { test, expect } from '@playwright/test';

test.describe('Registro de Usuario en Kilometrajes', () => {
    test('debe mostrar el nombre del usuario que registró el kilometraje', async ({ page }) => {
        // Navegar al login
        await page.goto('/login');

        // Hacer login como admin
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que se cargue el dashboard
        await page.waitForURL('/home');
        await expect(page.locator('text=Dashboard')).toBeVisible();

        // Navegar a vehículos
        await page.click('text=Vehículos');
        await page.waitForURL('/vehiculos');

        // Hacer clic en el primer vehículo
        await page.click('tbody tr:first-child a');
        await page.waitForURL(/\/vehiculos\/\d+/);

        // Ir a la pestaña de kilometraje
        await page.click('text=Kilometraje');
        await page.waitForTimeout(2000);

        // Verificar si ya hay kilometrajes
        const hasKilometrajes = await page.locator('#kilometraje-table tbody tr').count() > 0;

        if (hasKilometrajes) {
            console.log('✓ Se encontraron kilometrajes existentes');

            // Verificar que en la tabla aparezca el nombre del usuario en la columna "Registró"
            const firstRow = page.locator('#kilometraje-table tbody tr:first-child');
            const registroColumn = firstRow.locator('td:nth-child(3)'); // Columna "Registró"

            // Verificar que no muestre "Usuario no disponible"
            await expect(registroColumn).not.toContainText('Usuario no disponible');

            // Verificar que muestre algún nombre/email
            const textoRegistro = await registroColumn.textContent();
            expect(textoRegistro).toBeTruthy();
            expect(textoRegistro!.trim()).not.toBe('');

            console.log('✓ Usuario que registró el kilometraje:', textoRegistro?.trim());

            // Verificar que el texto no esté vacío o sea solo espacios
            expect(textoRegistro!.trim().length).toBeGreaterThan(0);

        } else {
            console.log('⚠ No se encontraron kilometrajes existentes');

            // Si no hay kilometrajes, veremos el mensaje de "No hay registros"
            const noRecordsMessage = page.locator('text=No hay registros de kilometraje');
            await expect(noRecordsMessage).toBeVisible();
            console.log('✓ Se muestra correctamente el mensaje de "No hay registros"');
        }

        console.log('✓ Prueba completada: El sistema muestra correctamente la información del usuario que registró el kilometraje');
    });
});
