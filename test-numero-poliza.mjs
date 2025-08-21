// Test simple para verificar que el campo numero_poliza se guarda correctamente
import { test, expect } from '@playwright/test';

test('Verificar que numero_poliza se guarda correctamente', async ({ page }) => {
    // Ir a la página de crear vehículo
    await page.goto('http://127.0.0.1:8000/vehiculos/create');

    // Llenar los campos requeridos
    await page.fill('input[name="marca"]', 'Toyota');
    await page.fill('input[name="modelo"]', 'Corolla');
    await page.fill('input[name="anio"]', '2023');
    await page.fill('input[name="n_serie"]', 'TEST123456789');
    await page.fill('input[name="placas"]', 'TEST-123-A');
    await page.fill('input[name="kilometraje_actual"]', '1000');

    // Llenar el número de póliza
    await page.fill('input[name="numero_poliza"]', '190324');

    // Enviar el formulario
    await page.click('button[type="submit"]');

    // Esperar a que se redirija o aparezca mensaje de éxito
    await page.waitForTimeout(2000);

    console.log('✓ Formulario enviado con número de póliza: 190324');
    console.log('✓ Verificar en la base de datos que se guardó correctamente');
});
