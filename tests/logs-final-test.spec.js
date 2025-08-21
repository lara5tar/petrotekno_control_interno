import { test, expect } from '@playwright/test';

test('Test logs page functionality', async ({ page }) => {
    try {
        console.log('🚀 Iniciando test de logs...');

        // Ir al login
        console.log('📝 Navegando al login...');
        await page.goto('http://127.0.0.1:8000/login');
        await page.waitForLoadState('networkidle');

        // Hacer login
        console.log('🔐 Haciendo login...');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Verificar que estamos en el dashboard
        console.log('✅ Login exitoso, verificando dashboard...');
        expect(page.url()).toContain('/dashboard');

        // Navegar a la página de logs
        console.log('📊 Navegando a logs...');
        await page.goto('http://127.0.0.1:8000/admin/logs');
        await page.waitForLoadState('networkidle');

        // Verificar que la página de logs carga correctamente
        console.log('🔍 Verificando elementos de la página...');

        // Verificar el título
        await expect(page.locator('h2:has-text("Logs del Sistema")')).toBeVisible({ timeout: 10000 });
        console.log('✅ Título encontrado');

        // Verificar breadcrumb
        await expect(page.locator('text=Inicio')).toBeVisible();
        await expect(page.locator('text=Configuración')).toBeVisible();
        await expect(page.locator('text=Logs del Sistema')).toBeVisible();
        console.log('✅ Breadcrumb correcto');

        // Verificar filtros
        await expect(page.locator('select[name="usuario_id"]')).toBeVisible();
        await expect(page.locator('select[name="accion"]')).toBeVisible();
        await expect(page.locator('select[name="tabla_afectada"]')).toBeVisible();
        console.log('✅ Filtros visibles');

        // Verificar tabla
        await expect(page.locator('table')).toBeVisible();
        await expect(page.locator('th:has-text("Fecha y Hora")')).toBeVisible();
        await expect(page.locator('th:has-text("Usuario")')).toBeVisible();
        await expect(page.locator('th:has-text("Acción")')).toBeVisible();
        console.log('✅ Tabla visible con headers correctos');

        // Verificar botón de volver
        await expect(page.locator('a:has-text("Volver a Configuración")')).toBeVisible();
        console.log('✅ Botón de volver visible');

        // Verificar que no hay errores 500
        const response = await page.request.get('http://127.0.0.1:8000/admin/logs');
        expect(response.status()).toBe(200);
        console.log('✅ Status 200 - No hay errores de servidor');

        console.log('🎉 ¡Test de logs completado exitosamente!');

    } catch (error) {
        console.error('❌ Error en el test:', error);
        throw error;
    }
});
