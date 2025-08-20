import { test, expect } from '@playwright/test';

test.describe('Debug Campanita', () => {
    test('Debuggear valores en campanita', async ({ page }) => {
        // Ir a la página de login
        await page.goto('http://localhost:8003/login');

        // Hacer login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');

        // Esperar a que aparezca el home
        await page.waitForURL('**/home');

        // Buscar directamente la campanita usando el título
        console.log('Buscando campanita...');
        const campanita = page.locator('a[title*="Centro de Alertas"]');
        await campanita.waitFor();

        const campanitaHTML = await campanita.innerHTML();
        console.log('HTML de campanita:', campanitaHTML);

        // Obtener el href real
        const href = await campanita.getAttribute('href');
        console.log('href de campanita:', href);

        // Buscar el badge específicamente
        const badge = campanita.locator('span');
        if (await badge.count() > 0) {
            const badgeText = await badge.textContent();
            console.log('Texto del badge:', badgeText);
        } else {
            console.log('No hay badge visible');
        }

        // Buscar el comentario de debug en toda la página
        const pageHTML = await page.content();
        const debugMatch = pageHTML.match(/<!-- DEBUG: alertasCount = (.*?), tieneAlertasUrgentes = (.*?) -->/);

        if (debugMatch) {
            console.log('Debug encontrado:');
            console.log('alertasCount:', debugMatch[1]);
            console.log('tieneAlertasUrgentes:', debugMatch[2]);
        } else {
            console.log('No se encontró comentario de debug en la página');
        }
    });
});
