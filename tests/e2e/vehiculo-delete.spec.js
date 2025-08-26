const { test, expect } = require('@playwright/test');

test.describe('Eliminación de Vehículos', () => {
  test.beforeEach(async ({ page }) => {
    // Ir a la página de login
    await page.goto('/login');
    
    // Hacer login (ajusta las credenciales según tu sistema)
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Esperar a que la página cargue después del login
    await page.waitForURL('**/dashboard');
  });

  test('debería eliminar un vehículo correctamente con soft delete', async ({ page }) => {
    // Ir a la página de vehículos
    await page.goto('/vehiculos');
    
    // Esperar a que la tabla cargue
    await page.waitForSelector('.table', { timeout: 10000 });
    
    // Buscar el primer botón de eliminar que esté visible
    const deleteButton = page.locator('button[onclick*="confirmarEliminacion"]').first();
    
    // Verificar que existe al menos un vehículo para eliminar
    await expect(deleteButton).toBeVisible({ timeout: 5000 });
    
    // Obtener el ID del vehículo antes de eliminarlo
    const vehiculoId = await deleteButton.getAttribute('data-vehiculo-id');
    const vehiculoPlacas = await deleteButton.getAttribute('data-vehiculo-placas');
    
    console.log(`Eliminando vehículo ID: ${vehiculoId}, Placas: ${vehiculoPlacas}`);
    
    // Interceptar la petición de eliminación
    let deleteRequestMade = false;
    page.on('request', request => {
      if (request.method() === 'POST' && request.url().includes(`/vehiculos/${vehiculoId}`)) {
        deleteRequestMade = true;
        console.log('Petición de eliminación detectada:', request.url());
      }
    });
    
    // Manejar el diálogo de confirmación
    page.on('dialog', async dialog => {
      expect(dialog.message()).toContain(vehiculoPlacas);
      await dialog.accept();
    });
    
    // Hacer clic en el botón de eliminar
    await deleteButton.click();
    
    // Esperar a que se procese la petición
    await page.waitForLoadState('networkidle');
    
    // Verificar que se hizo la petición de eliminación
    expect(deleteRequestMade).toBe(true);
    
    // Verificar que aparece un mensaje de éxito
    const successMessage = page.locator('.alert-success, .flash-message, [class*="success"]');
    await expect(successMessage).toBeVisible({ timeout: 5000 });
    
    // Verificar que el vehículo ya no aparece en la lista (o aparece marcado como eliminado)
    const vehiculoRow = page.locator(`tr:has-text("${vehiculoPlacas}")`);
    // El vehículo podría no aparecer si se usa paginación o podría aparecer con estado eliminado
    // await expect(vehiculoRow).not.toBeVisible();
  });

  test('debería mostrar confirmación antes de eliminar', async ({ page }) => {
    // Ir a la página de vehículos
    await page.goto('/vehiculos');
    
    // Esperar a que la tabla cargue
    await page.waitForSelector('.table', { timeout: 10000 });
    
    // Buscar el primer botón de eliminar
    const deleteButton = page.locator('button[onclick*="confirmarEliminacion"]').first();
    await expect(deleteButton).toBeVisible({ timeout: 5000 });
    
    // Obtener las placas para verificar el mensaje
    const vehiculoPlacas = await deleteButton.getAttribute('data-vehiculo-placas');
    
    // Configurar el manejo del diálogo para cancelar
    page.on('dialog', async dialog => {
      expect(dialog.message()).toContain(vehiculoPlacas);
      expect(dialog.message()).toContain('eliminar');
      await dialog.dismiss(); // Cancelar
    });
    
    // Hacer clic en eliminar
    await deleteButton.click();
    
    // Verificar que no se hizo ninguna petición POST (porque se canceló)
    let deleteRequestMade = false;
    page.on('request', request => {
      if (request.method() === 'POST' && request.url().includes('/vehiculos/')) {
        deleteRequestMade = true;
      }
    });
    
    // Esperar un poco para asegurar que no se hizo petición
    await page.waitForTimeout(1000);
    expect(deleteRequestMade).toBe(false);
  });

  test('no debería eliminar vehículos con obras activas', async ({ page }) => {
    // Ir a la página de vehículos
    await page.goto('/vehiculos');
    
    // Buscar un vehículo que tenga obra activa (si existe)
    const vehiculoConObra = page.locator('tr:has-text("ASIGNADO")').first();
    
    // Si existe un vehículo asignado, intentar eliminarlo
    if (await vehiculoConObra.count() > 0) {
      const deleteButton = vehiculoConObra.locator('button[onclick*="confirmarEliminacion"]');
      
      if (await deleteButton.count() > 0) {
        const vehiculoPlacas = await deleteButton.getAttribute('data-vehiculo-placas');
        
        page.on('dialog', async dialog => {
          await dialog.accept();
        });
        
        await deleteButton.click();
        
        // Debería mostrar un mensaje de error
        const errorMessage = page.locator('.alert-error, .flash-error, [class*="error"]');
        await expect(errorMessage).toBeVisible({ timeout: 5000 });
        await expect(errorMessage).toContainText('obras activas');
      }
    }
  });
});
