const { test, expect } = require('@playwright/test');

test.describe('Funcionalidad de Agregar Vehículos', () => {
    test('debe agregar vehículos correctamente al formulario de obra', async ({ page }) => {
        // Interceptar errores de consola
        const consoleMessages = [];
        page.on('console', (msg) => {
            consoleMessages.push(`${msg.type()}: ${msg.text()}`);
        });

        // Navegar directamente al formulario (evitando problemas de autenticación)
        await page.goto('http://localhost:8000/obras/create', {
            waitUntil: 'networkidle',
            timeout: 30000
        });

        // Esperar a que Alpine.js se cargue
        await page.waitForFunction(() => typeof window.Alpine !== 'undefined', { timeout: 10000 });

        // Verificar que el botón "Agregar Vehículo" está presente
        const addButton = page.locator('button:has-text("Agregar Vehículo")');
        await expect(addButton).toBeVisible();

        // Verificar que el container de vehículos está presente
        const vehicleContainer = page.locator('#vehiculosContainer');
        await expect(vehicleContainer).toBeVisible();

        // Verificar que el template está presente
        const template = page.locator('#vehicleTemplate');
        await expect(template).toBeAttached();

        // Contar vehículos iniciales (debería ser 0)
        const initialVehicleCount = await page.locator('.vehicle-card').count();
        console.log(`Vehículos iniciales: ${initialVehicleCount}`);

        // Hacer clic en "Agregar Vehículo"
        await addButton.click();

        // Esperar un momento para que se procese
        await page.waitForTimeout(1000);

        // Verificar que se agregó un vehículo
        const vehicleCards = page.locator('.vehicle-card');
        await expect(vehicleCards).toHaveCount(1);

        // Verificar que el vehículo tiene el número correcto
        const vehicleNumber = page.locator('.vehicle-number').first();
        await expect(vehicleNumber).toHaveText('#1');

        // Verificar que los campos del vehículo están presentes
        const vehicleSelect = page.locator('select[name="vehiculos[0][vehiculo_id]"]');
        const kilometrajeInput = page.locator('input[name="vehiculos[0][kilometraje_inicial]"]');
        const observacionesTextarea = page.locator('textarea[name="vehiculos[0][observaciones]"]');

        await expect(vehicleSelect).toBeVisible();
        await expect(kilometrajeInput).toBeVisible();
        await expect(observacionesTextarea).toBeVisible();

        // Agregar un segundo vehículo
        await addButton.click();
        await page.waitForTimeout(1000);

        // Verificar que ahora hay 2 vehículos
        await expect(vehicleCards).toHaveCount(2);

        // Verificar numeración correcta
        const secondVehicleNumber = page.locator('.vehicle-number').nth(1);
        await expect(secondVehicleNumber).toHaveText('#2');

        // Verificar que el segundo vehículo tiene campos con índices correctos
        const secondVehicleSelect = page.locator('select[name="vehiculos[1][vehiculo_id]"]');
        await expect(secondVehicleSelect).toBeVisible();

        // Probar eliminar el primer vehículo
        const firstRemoveButton = page.locator('.remove-vehicle').first();
        await firstRemoveButton.click();
        await page.waitForTimeout(1000);

        // Verificar que ahora hay 1 vehículo
        await expect(vehicleCards).toHaveCount(1);

        // Verificar que la numeración se actualizó
        const remainingVehicleNumber = page.locator('.vehicle-number').first();
        await expect(remainingVehicleNumber).toHaveText('#1');

        // Imprimir mensajes de consola para debugging
        console.log('Mensajes de consola:');
        consoleMessages.forEach(msg => console.log(`  ${msg}`));

        // Verificar que no hay errores críticos de JavaScript
        const jsErrors = consoleMessages.filter(msg => msg.includes('error') && !msg.includes('404'));
        if (jsErrors.length > 0) {
            console.log('Errores de JavaScript encontrados:');
            jsErrors.forEach(error => console.log(`  ${error}`));
        }

        expect(jsErrors.length).toBe(0);
    });

    test('debe manejar la selección de vehículos y actualización de kilometraje', async ({ page }) => {
        // Navegar al formulario
        await page.goto('http://localhost:8000/obras/create', {
            waitUntil: 'networkidle',
            timeout: 30000
        });

        // Esperar a que Alpine.js se cargue
        await page.waitForFunction(() => typeof window.Alpine !== 'undefined', { timeout: 10000 });

        // Agregar un vehículo
        const addButton = page.locator('button:has-text("Agregar Vehículo")');
        await addButton.click();
        await page.waitForTimeout(1000);

        // Seleccionar un vehículo si hay opciones disponibles
        const vehicleSelect = page.locator('select[name="vehiculos[0][vehiculo_id]"]');
        const options = await vehicleSelect.locator('option').count();

        if (options > 1) {
            // Seleccionar la primera opción (no vacía)
            await vehicleSelect.selectOption({ index: 1 });

            // Verificar que el kilometraje se actualizó
            const kilometrajeInput = page.locator('input[name="vehiculos[0][kilometraje_inicial]"]');
            const kilometrajeValue = await kilometrajeInput.inputValue();

            console.log(`Kilometraje actualizado a: ${kilometrajeValue}`);

            // El kilometraje debería ser un número válido (mayor o igual a 0)
            expect(parseInt(kilometrajeValue) >= 0).toBeTruthy();
        } else {
            console.log('No hay vehículos disponibles para seleccionar');
        }
    });
});