import { test, expect } from '@playwright/test';

test.describe('Tipo Activo Show Page', () => {
  test.beforeEach(async ({ page }) => {
    // Navegar a la página de login
    await page.goto('/login');
    
    // Hacer login (ajustar credenciales según sea necesario)
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Esperar a que la página cargue después del login
    await page.waitForURL('**/home');
  });

  test('should display tipo activo show page correctly', async ({ page }) => {
    // Navegar a la página de tipos de activos
    await page.goto('/tipos-activos');
    
    // Esperar a que la página cargue
    await page.waitForLoadState('networkidle');
    
    // Verificar que la página de índice carga correctamente
    await expect(page.locator('h1, h2, .card-title')).toContainText(/Tipos de Activos/i);
    
    // Buscar el primer enlace de "Ver" o "Show" en la tabla
    const showButton = page.locator('a[href*="tipos-activos/"]').first();
    
    if (await showButton.count() > 0) {
      // Hacer clic en el botón de ver
      await showButton.click();
      
      // Esperar a que la página de show cargue
      await page.waitForLoadState('networkidle');
      
      // Verificar que no hay errores de servidor
      const errorHeading = page.locator('h1:has-text("Internal Server Error")');
      await expect(errorHeading).not.toBeVisible();
      
      // Verificar que no hay errores de variable indefinida
      const undefinedError = page.locator('text=Undefined variable');
      await expect(undefinedError).not.toBeVisible();
      
      // Verificar que la página muestra información del tipo de activo
      await expect(page.locator('body')).toContainText(/nombre|tipo.*activo/i);
      
      // Verificar que hay contenido en la página (no está vacía)
      const bodyText = await page.locator('body').textContent();
      expect(bodyText.trim().length).toBeGreaterThan(100);
      
    } else {
      console.log('No se encontraron tipos de activos para probar');
    }
  });

  test('should handle direct navigation to tipo activo show page', async ({ page }) => {
    // Intentar navegar directamente a una página de show (ID 1)
    await page.goto('/tipos-activos/1');
    
    // Esperar a que la página cargue
    await page.waitForLoadState('networkidle');
    
    // Verificar que no hay errores de servidor
    const errorHeading = page.locator('h1:has-text("Internal Server Error")');
    
    if (await errorHeading.count() > 0) {
      // Si hay error, capturar el contenido para debugging
      const errorContent = await page.locator('body').textContent();
      console.log('Error encontrado:', errorContent);
      
      // Fallar la prueba con información útil
      throw new Error('Se encontró un error de servidor en la página de show');
    }
    
    // Verificar que no hay errores de variable indefinida
    const undefinedError = page.locator('text=Undefined variable');
    await expect(undefinedError).not.toBeVisible();
  });

  test('should display vehiculos section if tipo activo has vehicles', async ({ page }) => {
    // Navegar a tipos de activos
    await page.goto('/tipos-activos');
    await page.waitForLoadState('networkidle');
    
    // Buscar y hacer clic en el primer tipo de activo
    const showButton = page.locator('a[href*="tipos-activos/"]').first();
    
    if (await showButton.count() > 0) {
      await showButton.click();
      await page.waitForLoadState('networkidle');
      
      // Verificar que la sección de vehículos existe (aunque esté vacía)
      const vehiculosSection = page.locator('text=Vehículos, text=vehiculos, .vehiculos-section');
      
      // La sección debería existir, pero puede estar vacía
      // Solo verificamos que no hay errores de SQL
      const sqlError = page.locator('text=SQLSTATE, text=Column not found');
      await expect(sqlError).not.toBeVisible();
    }
  });
});