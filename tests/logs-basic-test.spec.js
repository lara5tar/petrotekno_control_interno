import { test, expect } from '@playwright/test';

test('Direct logs page access test', async ({ page }) => {
    try {
        console.log('🚀 Probando acceso directo a logs...');

        // Probar acceso directo a la página de logs
        console.log('📊 Navegando a logs (debería redirigir al login)...');
        const response = await page.goto('http://127.0.0.1:8000/admin/logs');

        // Verificar que el servidor responde (aunque redirija)
        expect(response.status()).toBeLessThan(500);
        console.log(`✅ Servidor responde con status: ${response.status()}`);

        // Si redirige al login, es correcto (el middleware de auth funciona)
        if (page.url().includes('/login')) {
            console.log('✅ Middleware de autenticación funciona - redirige al login');

            // Verificar que la página de login carga
            await expect(page.locator('input[name="email"]')).toBeVisible({ timeout: 5000 });
            console.log('✅ Página de login carga correctamente');

        } else if (page.url().includes('/admin/logs')) {
            console.log('✅ Acceso directo a logs (usuario ya autenticado)');

            // Verificar elementos básicos de la página de logs
            await expect(page.locator('h2')).toBeVisible({ timeout: 5000 });
            console.log('✅ Página de logs carga correctamente');
        }

        console.log('🎉 ¡Test básico completado exitosamente!');

    } catch (error) {
        console.error('❌ Error en el test:', error);
        throw error;
    }
});
