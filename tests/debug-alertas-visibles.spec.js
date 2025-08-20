import { test, expect } from '@playwright/test';

test.describe('Debug Alertas Visibles', () => {

    test('verificar que las alertas se muestran después de la corrección', async ({ page }) => {
        page.setDefaultTimeout(30000);

        // Login
        await page.goto('http://localhost:8001/login', {
            waitUntil: 'load',
            timeout: 30000
        });

        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        await page.waitForURL('**/home', { timeout: 30000 });

        // Navegar a vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });

        // Esperar que cargue
        await page.waitForSelector('h1', { timeout: 30000 });

        // Contar las alertas mostradas
        const alertasVisibles = await page.locator('.divide-y > div').count();
        console.log('Número de alertas visibles:', alertasVisibles);

        // Verificar que hay alertas visibles
        expect(alertasVisibles).toBeGreaterThan(0);

        // Verificar contenido específico
        if (alertasVisibles > 0) {
            // Buscar la primera alerta
            const primeraAlerta = page.locator('.divide-y > div').first();
            await expect(primeraAlerta).toBeVisible();

            // Tomar screenshot para debugging
            await page.screenshot({ path: 'alertas-visibles.png', fullPage: true });

            console.log('✅ Alertas están siendo mostradas correctamente');
        }
    });
});
