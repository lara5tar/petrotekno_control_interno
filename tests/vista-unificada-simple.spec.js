import { test, expect } from '@playwright/test';

test.describe('Vista Unificada - Test Simple', () => {

    test('debe cargar la vista unificada de alertas con autenticación completa', async ({ page }) => {
        // Timeout más largo
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

        // Ahora navegar a la vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', {
            waitUntil: 'networkidle',
            timeout: 30000
        });

        // Verificar que la página no redirecciona al login
        const currentUrl = page.url();
        console.log('URL actual:', currentUrl);

        // Tomar captura de pantalla para debugging
        await page.screenshot({ path: 'debug-vista-unificada.png' });

        // Verificar contenido de la página
        const content = await page.content();
        console.log('Longitud del contenido:', content.length);

        // Buscar elementos específicos
        const hasTitle = await page.locator('h1').count();
        console.log('Número de elementos h1:', hasTitle);

        const titleText = await page.locator('h1').allTextContents();
        console.log('Títulos encontrados:', titleText);

        // Verificar que no estemos en login
        await expect(page).not.toHaveURL(/login/);

        // Verificar que tengamos contenido de la vista unificada
        await expect(page.locator('body')).toContainText('Alertas');
    });
});
