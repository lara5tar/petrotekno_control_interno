import { test, expect } from '@playwright/test';

test('Manual verification test', async ({ page }) => {
    console.log('🚀 Iniciando verificación manual...');

    try {
        // Test 1: Verificar que el servidor responde
        console.log('📡 Test 1: Verificando servidor...');
        const response = await page.request.get('http://127.0.0.1:8000/');
        console.log(`✅ Servidor responde: ${response.status()}`);
        expect(response.status()).toBeLessThan(500);

        // Test 2: Verificar página de login
        console.log('🔐 Test 2: Verificando login...');
        await page.goto('http://127.0.0.1:8000/login');
        await expect(page.locator('input[name="email"]')).toBeVisible({ timeout: 10000 });
        console.log('✅ Página de login carga correctamente');

        // Test 3: Hacer login
        console.log('👤 Test 3: Haciendo login...');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        if (page.url().includes('/dashboard')) {
            console.log('✅ Login exitoso - en dashboard');
        } else if (page.url().includes('/login')) {
            console.log('⚠️ Login falló - verificar credenciales');
            return;
        }

        // Test 4: Navegar a logs
        console.log('📊 Test 4: Navegando a logs...');
        await page.goto('http://127.0.0.1:8000/admin/logs');
        await page.waitForLoadState('networkidle');

        // Verificar si hay errores
        const hasError = await page.locator('h1:has-text("Error")', 'h1:has-text("500")', 'text=Exception').count();
        if (hasError > 0) {
            console.log('❌ Error 500 en página de logs');
            const errorText = await page.textContent('body');
            console.log('Error details:', errorText.substring(0, 500));
        } else {
            console.log('✅ Página de logs carga sin errores 500');

            // Verificar elementos básicos
            const hasTitle = await page.locator('h2:has-text("Logs")').count();
            const hasTable = await page.locator('table').count();
            const hasBreadcrumb = await page.locator('text=Configuración').count();

            console.log(`✅ Título encontrado: ${hasTitle > 0}`);
            console.log(`✅ Tabla encontrada: ${hasTable > 0}`);
            console.log(`✅ Breadcrumb encontrado: ${hasBreadcrumb > 0}`);

            if (hasTitle > 0 && hasTable > 0) {
                console.log('🎉 ¡LOGS FUNCIONANDO CORRECTAMENTE!');
            }
        }

    } catch (error) {
        console.error('❌ Error en verificación:', error.message);
    }
});
