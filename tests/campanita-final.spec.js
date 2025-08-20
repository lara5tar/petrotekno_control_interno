import { test, expect } from '@playwright/test';

test.describe('Verificación Final de Campanita', () => {
    test('La campanita debe mostrar exactamente 8 alertas', async ({ page }) => {
        // Ir a login
        await page.goto('http://localhost:8003/login');

        // Login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');

        // Esperar el home
        await page.waitForURL('**/home');

        // Buscar la campanita y verificar que muestre 8
        const campanita = page.locator('a[title*="Centro de Alertas"]');
        await campanita.waitFor();

        const badge = campanita.locator('span');
        const badgeText = await badge.textContent();

        console.log('Número en campanita:', badgeText?.trim());

        // Verificar que muestre exactamente 8
        expect(badgeText?.trim()).toBe('8');

        console.log('✅ ÉXITO: La campanita muestra exactamente 8 alertas unificadas');
    });
});
