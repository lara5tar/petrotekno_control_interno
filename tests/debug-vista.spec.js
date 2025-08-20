import { test, expect } from '@playwright/test';

test.describe('Debug Vista Unificada', () => {

    test('debug contenido de la vista', async ({ page }) => {
        page.setDefaultTimeout(60000);

        // Navegar a login
        await page.goto('http://localhost:8001/login', {
            waitUntil: 'networkidle',
            timeout: 30000
        });

        // Login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar redirección a home
        await page.waitForURL('**/home', { timeout: 30000 });

        // Navegar a la vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', {
            waitUntil: 'networkidle',
            timeout: 30000
        });

        // Buscar diferentes elementos
        const hasAlertasText = await page.locator('text=Alertas').count();
        console.log('Textos con "Alertas":', hasAlertasText);

        const hasCentroText = await page.locator('text=Centro').count();
        console.log('Textos con "Centro":', hasCentroText);

        const hasUnificadoText = await page.locator('text=Unificado').count();
        console.log('Textos con "Unificado":', hasUnificadoText);

        // Buscar específicamente por clases
        const hasMainContent = await page.locator('.min-h-screen').count();
        console.log('Divs con clase min-h-screen:', hasMainContent);

        const hasMaxWidth = await page.locator('.max-w-7xl').count();
        console.log('Divs con clase max-w-7xl:', hasMaxWidth);

        // Verificar si hay algún emoji de alerta
        const hasEmojiAlerta = await page.locator('text=🚨').count();
        console.log('Emojis de alerta 🚨:', hasEmojiAlerta);

        // Buscar el div de estadísticas
        const hasStats = await page.locator('text=Total Alertas').count();
        console.log('Textos "Total Alertas":', hasStats);

        // Tomar screenshot
        await page.screenshot({ path: 'debug-full-page.png', fullPage: true });

        // Intentar verificar contenido básico
        const bodyText = await page.locator('body').textContent();
        console.log('Primeros 500 caracteres del body:', bodyText?.substring(0, 500));

        // Si no encontramos el contenido esperado, busquemos errores
        const hasError = await page.locator('text=Error').count();
        console.log('Textos con "Error":', hasError);

        // Verificar que no estemos en login
        await expect(page).not.toHaveURL(/login/);
    });
});
