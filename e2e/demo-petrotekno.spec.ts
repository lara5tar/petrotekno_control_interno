import { test, expect } from '@playwright/test';

test.describe('Demo básico de Playwright para Petrotekno', () => {
  test('Verificar navegación a Playwright.dev', async ({ page }) => {
    // Navegar a la página oficial de Playwright
    await page.goto('https://playwright.dev/');
    
    // Verificar que el título contiene "Playwright"
    await expect(page).toHaveTitle(/Playwright/);
    
    // Verificar que el encabezado principal está visible
    await expect(page.getByRole('heading', { name: /Playwright/i })).toBeVisible();
    
    // Tomar una captura de pantalla
    await page.screenshot({ path: 'test-results/playwright-homepage.png' });
    
    console.log('✅ Navegación a Playwright.dev completada exitosamente');
  });

  test('Verificar funcionalidad de búsqueda en documentación', async ({ page }) => {
    // Navegar a la documentación
    await page.goto('https://playwright.dev/docs/intro');
    
    // Verificar que estamos en la página de documentación
    await expect(page).toHaveTitle(/Getting started/);
    
    // Buscar el botón de instalación
    const installationSection = page.locator('text=Installation');
    await expect(installationSection).toBeVisible();
    
    console.log('✅ Documentación de Playwright verificada correctamente');
  });

  test('Demostrar interacciones básicas', async ({ page }) => {
    // Navegar a una página de ejemplo
    await page.goto('https://demo.playwright.dev/todomvc/');
    
    // Agregar una tarea
    await page.fill('.new-todo', 'Implementar Playwright en Petrotekno');
    await page.press('.new-todo', 'Enter');
    
    // Verificar que la tarea se agregó
    await expect(page.locator('.todo-list li')).toContainText('Implementar Playwright en Petrotekno');
    
    // Marcar como completada
    await page.check('.todo-list li .toggle');
    
    // Verificar que está marcada como completada
    await expect(page.locator('.todo-list li')).toHaveClass(/completed/);
    
    console.log('✅ Interacciones básicas completadas exitosamente');
  });
});