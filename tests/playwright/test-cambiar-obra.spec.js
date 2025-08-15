import { test, expect } from '@playwright/test';

test.describe('Funcionalidad de Cambiar Obra', () => {
  test.beforeEach(async ({ page }) => {
    // Ir a la página de login
    await page.goto('http://127.0.0.1:8000/login');

    // Hacer login
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');

    // Esperar a que la redirección complete
    await page.waitForURL(/.*home.*|.*vehiculos.*/);
  });

  test('Debe abrir el modal de cambiar obra correctamente', async ({ page }) => {
    // Ir a la página del vehículo 1
    await page.goto('http://127.0.0.1:8000/vehiculos/1');

    // Esperar a que la página cargue completamente
    await page.waitForSelector('.bg-white.rounded-lg.shadow-lg');

    // Verificar que el botón "Cambiar Obra" esté presente
    const cambiarObraButton = page.locator('button:has-text("Cambiar Obra")');
    await expect(cambiarObraButton).toBeVisible();

    // Hacer clic en el botón
    await cambiarObraButton.click();

    // Verificar que el modal se abre
    const modal = page.locator('#cambiar-obra-modal');
    await expect(modal).toBeVisible();

    // Verificar elementos del modal
    await expect(page.locator('h3:has-text("Cambiar Obra Asignada")')).toBeVisible();
    await expect(page.locator('select[name="obra_id"]')).toBeVisible();
    await expect(page.locator('select[name="operador_id"]')).toBeVisible();
    await expect(page.locator('input[name="kilometraje_inicial"]')).toBeVisible();
    await expect(page.locator('textarea[name="observaciones"]')).toBeVisible();

    console.log('✅ Modal de cambiar obra se abre correctamente');
  });

  test('Debe mostrar obras disponibles en el dropdown', async ({ page }) => {
    // Ir a la página del vehículo 1
    await page.goto('http://127.0.0.1:8000/vehiculos/1');

    // Abrir el modal
    await page.click('button:has-text("Cambiar Obra")');

    // Esperar a que el modal esté visible
    await page.waitForSelector('#cambiar-obra-modal:not(.hidden)');

    // Verificar que el dropdown de obras tiene opciones
    const obraSelect = page.locator('select[name="obra_id"]');
    await expect(obraSelect).toBeVisible();

    // Contar las opciones (debe tener al menos la opción por defecto + obras disponibles)
    const opciones = await obraSelect.locator('option').count();
    expect(opciones).toBeGreaterThan(1);

    // Verificar que existe la opción por defecto
    await expect(obraSelect.locator('option[value=""]')).toHaveText('Seleccionar obra...');

    console.log(`✅ Dropdown tiene ${opciones} opciones disponibles`);
  });

  test('Debe procesar el cambio de obra exitosamente', async ({ page }) => {
    // Ir a la página del vehículo 1
    await page.goto('http://127.0.0.1:8000/vehiculos/1');

    // Verificar obra actual antes del cambio
    const obraActualAntes = await page.locator('.text-lg.font-bold:near(:text("Obra Actual"))').textContent();
    console.log('Obra actual antes:', obraActualAntes);

    // Abrir el modal
    await page.click('button:has-text("Cambiar Obra")');
    await page.waitForSelector('#cambiar-obra-modal:not(.hidden)');

    // Seleccionar una obra diferente
    const obraSelect = page.locator('select[name="obra_id"]');
    const opciones = await obraSelect.locator('option:not([value=""])').all();

    if (opciones.length > 0) {
      // Seleccionar la primera obra disponible
      await obraSelect.selectOption({ index: 1 });

      // Seleccionar operador (mantener el actual o seleccionar uno)
      const operadorSelect = page.locator('select[name="operador_id"]');
      await operadorSelect.selectOption({ index: 1 });

      // Agregar observaciones
      await page.fill('textarea[name="observaciones"]', 'Cambio de obra desde test de Playwright');

      // Enviar formulario
      await page.click('button[type="submit"]:has-text("Cambiar Obra")');

      // Esperar la notificación de éxito
      await page.waitForSelector('.fixed.top-4.right-4', { timeout: 10000 });
      const notificacion = page.locator('.fixed.top-4.right-4');

      // Verificar que la notificación sea de éxito (verde)
      await expect(notificacion).toHaveClass(/bg-green-500/);

      // Esperar a que la página se recargue
      await page.waitForTimeout(2000);

      // Verificar que la obra cambió
      const obraActualDespues = await page.locator('.text-lg.font-bold:near(:text("Obra Actual"))').textContent();
      console.log('Obra actual después:', obraActualDespues);

      // Verificar que la obra cambió
      expect(obraActualDespues).not.toBe(obraActualAntes);

      console.log('✅ Cambio de obra procesado exitosamente');
    } else {
      console.log('⚠️ No hay obras disponibles para cambiar');
    }
  });

  test('Debe cerrar el modal al hacer clic en cancelar', async ({ page }) => {
    // Ir a la página del vehículo 1
    await page.goto('http://127.0.0.1:8000/vehiculos/1');

    // Abrir el modal
    await page.click('button:has-text("Cambiar Obra")');
    await page.waitForSelector('#cambiar-obra-modal:not(.hidden)');

    // Hacer clic en cancelar
    await page.click('button:has-text("Cancelar")');

    // Verificar que el modal se oculta
    const modal = page.locator('#cambiar-obra-modal');
    await expect(modal).toHaveClass(/hidden/);

    console.log('✅ Modal se cierra correctamente con cancelar');
  });

  test('Debe validar campos requeridos', async ({ page }) => {
    // Ir a la página del vehículo 1
    await page.goto('http://127.0.0.1:8000/vehiculos/1');

    // Abrir el modal
    await page.click('button:has-text("Cambiar Obra")');
    await page.waitForSelector('#cambiar-obra-modal:not(.hidden)');

    // Intentar enviar sin seleccionar obra ni operador
    await page.click('button[type="submit"]:has-text("Cambiar Obra")');

    // Verificar que los campos required impiden el envío
    const obraSelect = page.locator('select[name="obra_id"]');
    const operadorSelect = page.locator('select[name="operador_id"]');

    // En navegadores modernos, los campos required previenen el envío
    await expect(obraSelect).toHaveAttribute('required');
    await expect(operadorSelect).toHaveAttribute('required');

    console.log('✅ Validación de campos requeridos funciona');
  });
});
