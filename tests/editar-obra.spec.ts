import { test, expect } from '@playwright/test';

test.describe('Vista Editar Obra', () => {
  test.beforeEach(async ({ page }) => {
    // Ir a la página de login
    await page.goto('http://localhost:8000/login');
    
    // Hacer login con credenciales de administrador
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Esperar a que redirija al dashboard
    await page.waitForURL('**/home');
  });

  test('debería cargar la vista de editar obra sin errores', async ({ page }) => {
    // Ir a la lista de obras
    await page.goto('http://localhost:8000/obras');
    
    // Esperar que cargue la página
    await page.waitForSelector('h2:has-text("Listado de Obras")');
    
    // Buscar el primer botón de "Editar" y hacer click
    const editarBoton = page.locator('a[href*="/obras/"][href*="/edit"]').first();
    await expect(editarBoton).toBeVisible();
    await editarBoton.click();
    
    // Verificar que estamos en la página de editar
    await page.waitForSelector('h2:has-text("Editar Obra")');
    
    // Verificar que los elementos principales están presentes
    await expect(page.locator('h2:has-text("Editar Obra")')).toBeVisible();
    await expect(page.locator('input[name="nombre_obra"]')).toBeVisible();
    await expect(page.locator('select[name="estatus"]')).toBeVisible();
    await expect(page.locator('input[name="avance"]')).toBeVisible();
    await expect(page.locator('input[name="fecha_inicio"]')).toBeVisible();
    await expect(page.locator('button:has-text("Actualizar Obra")')).toBeVisible();
  });

  test('debería mostrar los botones de navegación correctamente', async ({ page }) => {
    // Ir directamente a editar la primera obra
    await page.goto('http://localhost:8000/obras/1/edit');
    
    // Verificar que los botones de navegación están presentes
    await expect(page.locator('a:has-text("Ver Asignaciones")').or(page.locator('a:has-text("Nueva Asignación")'))).toBeVisible();
    await expect(page.locator('a:has-text("Ver Detalles")')).toBeVisible();
    await expect(page.locator('a:has-text("Volver al Listado")')).toBeVisible();
    await expect(page.locator('a:has-text("Cancelar")')).toBeVisible();
  });

  test('debería permitir cambiar el estado del proyecto', async ({ page }) => {
    // Ir directamente a editar la primera obra
    await page.goto('http://localhost:8000/obras/1/edit');
    
    // Cambiar el estado
    const estatusSelect = page.locator('select[name="estatus"]');
    await estatusSelect.selectOption('en_progreso');
    
    // Verificar que se seleccionó correctamente
    await expect(estatusSelect).toHaveValue('en_progreso');
  });

  test('debería actualizar la barra de progreso cuando cambia el avance', async ({ page }) => {
    // Ir directamente a editar la primera obra
    await page.goto('http://localhost:8000/obras/1/edit');
    
    // Cambiar el avance
    const avanceInput = page.locator('input[name="avance"]');
    await avanceInput.fill('75');
    
    // Verificar que la barra de progreso se actualiza
    const progressBar = page.locator('#progressBar');
    await expect(progressBar).toHaveAttribute('style', /width:\s*75%/);
    
    // Verificar que el texto se actualiza
    const progressText = page.locator('#progressText');
    await expect(progressText).toHaveText('75% completado');
  });

  test('debería permitir navegar a ver detalles sin errores', async ({ page }) => {
    // Ir directamente a editar la primera obra
    await page.goto('http://localhost:8000/obras/1/edit');
    
    // Hacer click en "Ver Detalles"
    const verDetallesBoton = page.locator('a:has-text("Ver Detalles")');
    await verDetallesBoton.click();
    
    // Verificar que navega correctamente (no debe haber errores 404)
    await page.waitForURL('**/obras/1');
    await expect(page.locator('h2')).toBeVisible();
  });

  test('debería mostrar el breadcrumb correctamente', async ({ page }) => {
    // Ir directamente a editar la primera obra
    await page.goto('http://localhost:8000/obras/1/edit');
    
    // Verificar breadcrumb
    await expect(page.locator('nav[aria-label="breadcrumb"]').or(page.locator('.breadcrumb'))).toBeVisible();
    await expect(page.locator('a:has-text("Inicio")')).toBeVisible();
    await expect(page.locator('a:has-text("Obras")')).toBeVisible();
    await expect(page.locator('text=Editar Obra')).toBeVisible();
  });

  test('debería validar fechas correctamente', async ({ page }) => {
    // Ir directamente a editar la primera obra
    await page.goto('http://localhost:8000/obras/1/edit');
    
    // Establecer fecha de inicio posterior a fecha de fin (debería dar error)
    await page.fill('input[name="fecha_inicio"]', '2025-12-31');
    await page.fill('input[name="fecha_fin"]', '2025-01-01');
    
    // Intentar enviar el formulario
    await page.click('button:has-text("Actualizar Obra")');
    
    // Debería mostrar una alerta o no permitir el envío
    page.on('dialog', async dialog => {
      expect(dialog.message()).toContain('fecha de finalización debe ser posterior');
      await dialog.accept();
    });
  });
});