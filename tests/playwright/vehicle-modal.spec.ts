import { test, expect } from '@playwright/test';

test.describe('Modal de Asignación de Vehículos en Crear Obra', () => {
  test.beforeEach(async ({ page }) => {
    // Navegar a la página de login
    await page.goto('http://localhost:8000/login');
    
    // Hacer login con las credenciales proporcionadas
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    
    // Esperar a que aparezca el dashboard o la página principal
    await page.waitForLoadState('networkidle');
    
    // Navegar a la página de crear obra
    await page.goto('http://localhost:8000/obras/create');
    await page.waitForLoadState('networkidle');
  });

  test('debería abrir y cerrar el modal de asignación de vehículos', async ({ page }) => {
    // Verificar que estamos en la página correcta
    await expect(page).toHaveTitle(/Agregar Obra/);
    
    // Buscar el botón "Asignar Vehículo"
    const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
    await expect(assignButton).toBeVisible();
    
    // Verificar que el modal no está visible inicialmente
    const modal = page.locator('[role="dialog"]');
    await expect(modal).not.toBeVisible();
    
    // Hacer click en el botón "Asignar Vehículo"
    await assignButton.click();
    
    // Verificar que el modal se abre
    await expect(modal).toBeVisible();
    await expect(page.locator('h3:has-text("Asignar Vehículo a la Obra")')).toBeVisible();
    
    // Verificar que los elementos del modal están presentes
    await expect(page.locator('select[x-model="modalVehicle.vehiculo_id"]')).toBeVisible();
    await expect(page.locator('input[x-model="modalVehicle.kilometraje_inicial"]')).toBeVisible();
    await expect(page.locator('textarea[x-model="modalVehicle.observaciones"]')).toBeVisible();
    
    // Cerrar el modal haciendo click en el botón "Cancelar"
    await page.click('button:has-text("Cancelar")');
    
    // Verificar que el modal se cierra
    await expect(modal).not.toBeVisible();
  });

  test('debería cerrar el modal al hacer click en el overlay', async ({ page }) => {
    // Abrir el modal
    const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
    await assignButton.click();
    
    // Verificar que el modal está abierto
    const modal = page.locator('[role="dialog"]');
    await expect(modal).toBeVisible();
    
    // Hacer click en el overlay (fondo del modal)
    await page.locator('.fixed.inset-0.bg-gray-500.bg-opacity-75').click();
    
    // Verificar que el modal se cierra
    await expect(modal).not.toBeVisible();
  });

  test('debería cerrar el modal al hacer click en el botón X', async ({ page }) => {
    // Abrir el modal
    const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
    await assignButton.click();
    
    // Verificar que el modal está abierto
    const modal = page.locator('[role="dialog"]');
    await expect(modal).toBeVisible();
    
    // Hacer click en el botón X (cerrar)
    await page.locator('button[x-on\\:click="closeVehicleModal()"]').click();
    
    // Verificar que el modal se cierra
    await expect(modal).not.toBeVisible();
  });

  test('debería mostrar información del vehículo cuando se selecciona uno', async ({ page }) => {
    // Abrir el modal
    const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
    await assignButton.click();
    
    const modal = page.locator('[role="dialog"]');
    await expect(modal).toBeVisible();
    
    // Seleccionar un vehículo en el dropdown
    const vehicleSelect = page.locator('select[x-model="modalVehicle.vehiculo_id"]');
    
    // Verificar que hay opciones disponibles
    const options = await vehicleSelect.locator('option').count();
    if (options > 1) { // Más de 1 porque la primera es "-- Seleccionar vehículo --"
      // Seleccionar el primer vehículo disponible
      await vehicleSelect.selectOption({ index: 1 });
      
      // Esperar a que aparezca la información del vehículo
      await page.waitForTimeout(500); // Dar tiempo a Alpine.js para reaccionar
      
      // Verificar que aparece la información del vehículo
      const vehicleInfo = page.locator('div:has-text("Información del Vehículo")');
      await expect(vehicleInfo).toBeVisible();
      
      // Verificar que el kilometraje inicial se auto-completa
      const kilometrajeInput = page.locator('input[x-model="modalVehicle.kilometraje_inicial"]');
      const kilometrajeValue = await kilometrajeInput.inputValue();
      expect(parseInt(kilometrajeValue) >= 0).toBeTruthy();
    }
  });

  test('debería validar que se seleccione un vehículo antes de guardar', async ({ page }) => {
    // Abrir el modal
    const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
    await assignButton.click();
    
    const modal = page.locator('[role="dialog"]');
    await expect(modal).toBeVisible();
    
    // Intentar guardar sin seleccionar vehículo
    const saveButton = page.locator('button:has-text("Asignar Vehículo")').last();
    
    // Verificar que el botón está deshabilitado cuando no hay vehículo seleccionado
    await expect(saveButton).toBeDisabled();
  });

  test('debería asignar un vehículo correctamente', async ({ page }) => {
    // Abrir el modal
    const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
    await assignButton.click();
    
    const modal = page.locator('[role="dialog"]');
    await expect(modal).toBeVisible();
    
    // Seleccionar un vehículo
    const vehicleSelect = page.locator('select[x-model="modalVehicle.vehiculo_id"]');
    const options = await vehicleSelect.locator('option').count();
    
    if (options > 1) {
      await vehicleSelect.selectOption({ index: 1 });
      
      // Agregar observaciones opcionales
      await page.fill('textarea[x-model="modalVehicle.observaciones"]', 'Vehículo en excelente estado para esta obra');
      
      // Esperar a que el botón se habilite
      const saveButton = page.locator('button[type="submit"]').last();
      await expect(saveButton).toBeEnabled();
      
      // Guardar la asignación
      await saveButton.click();
      
      // Verificar que el modal se cierra
      await expect(modal).not.toBeVisible();
      
      // Verificar que aparece el vehículo en la lista de asignados
      await expect(page.locator('div:has-text("vehículo asignado")')).toBeVisible();
    }
  });

  test('debería permitir editar una asignación de vehículo', async ({ page }) => {
    // Primero asignar un vehículo
    const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
    await assignButton.click();
    
    let modal = page.locator('[role="dialog"]');
    await expect(modal).toBeVisible();
    
    const vehicleSelect = page.locator('select[x-model="modalVehicle.vehiculo_id"]');
    const options = await vehicleSelect.locator('option').count();
    
    if (options > 1) {
      await vehicleSelect.selectOption({ index: 1 });
      await page.fill('textarea[x-model="modalVehicle.observaciones"]', 'Observación inicial');
      
      const saveButton = page.locator('button[type="submit"]').last();
      await saveButton.click();
      
      await expect(modal).not.toBeVisible();
      
      // Ahora editar la asignación
      const editButton = page.locator('button[title="Editar asignación"]');
      await expect(editButton).toBeVisible();
      await editButton.click();
      
      // Verificar que el modal se abre en modo edición
      modal = page.locator('[role="dialog"]');
      await expect(modal).toBeVisible();
      await expect(page.locator('h3:has-text("Editar Asignación de Vehículo")')).toBeVisible();
      
      // Modificar las observaciones
      await page.fill('textarea[x-model="modalVehicle.observaciones"]', 'Observación editada');
      
      // Guardar los cambios
      const updateButton = page.locator('button:has-text("Actualizar Asignación")');
      await updateButton.click();
      
      // Verificar que el modal se cierra
      await expect(modal).not.toBeVisible();
      
      // Verificar que la observación se actualizó
      await expect(page.locator('text=Obs: Observación editada')).toBeVisible();
    }
  });

  test('debería permitir eliminar una asignación de vehículo', async ({ page }) => {
    // Primero asignar un vehículo
    const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
    await assignButton.click();
    
    const modal = page.locator('[role="dialog"]');
    await expect(modal).toBeVisible();
    
    const vehicleSelect = page.locator('select[x-model="modalVehicle.vehiculo_id"]');
    const options = await vehicleSelect.locator('option').count();
    
    if (options > 1) {
      await vehicleSelect.selectOption({ index: 1 });
      
      const saveButton = page.locator('button[type="submit"]').last();
      await saveButton.click();
      
      await expect(modal).not.toBeVisible();
      
      // Verificar que el vehículo aparece en la lista
      await expect(page.locator('div:has-text("vehículo asignado")')).toBeVisible();
      
      // Eliminar la asignación
      const deleteButton = page.locator('button[title="Eliminar asignación"]');
      await expect(deleteButton).toBeVisible();
      await deleteButton.click();
      
      // Verificar que el vehículo desaparece de la lista
      await expect(page.locator('text=Ningún vehículo asignado')).toBeVisible();
    }
  });

  test('debería funcionar correctamente en dispositivos móviles', async ({ page }) => {
    // Cambiar a vista móvil
    await page.setViewportSize({ width: 375, height: 667 });
    
    // Abrir el modal
    const assignButton = page.locator('button:has-text("Asignar Vehículo")').first();
    await assignButton.click();
    
    const modal = page.locator('[role="dialog"]');
    await expect(modal).toBeVisible();
    
    // Verificar que el modal es responsive
    await expect(modal).toBeInViewport();
    
    // Verificar que los elementos son accesibles en móvil
    await expect(page.locator('select[x-model="modalVehicle.vehiculo_id"]')).toBeVisible();
    await expect(page.locator('button:has-text("Cancelar")')).toBeVisible();
    
    // Cerrar el modal
    await page.click('button:has-text("Cancelar")');
    await expect(modal).not.toBeVisible();
  });
});