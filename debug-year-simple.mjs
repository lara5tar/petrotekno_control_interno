import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        console.log('🚀 Verificando año en Derecho Vehicular...');

        // Ir al login
        await page.goto('http://localhost:8003/login');
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard');
        console.log('✅ Login exitoso');

        // Ir directamente a un vehículo específico si existe
        await page.goto('http://localhost:8003/vehiculos');
        await page.waitForTimeout(2000);

        // Hacer clic en el primer vehículo
        const firstVehicle = page.locator('tbody tr').first();
        await firstVehicle.click();
        await page.waitForTimeout(2000);

        // Buscar la sección de Derecho Vehicular
        const derechoElement = page.locator('.bg-gray-600').filter({ hasText: /Año|Sin documento|Documento cargado/ });

        const derechoText = await derechoElement.textContent();
        console.log(`📄 Texto en Derecho Vehicular: "${derechoText}"`);

        if (derechoText && derechoText.includes('Año')) {
            console.log('✅ ÉXITO: El año se muestra en Derecho Vehicular');
        } else {
            console.log('⚠️  Estado del derecho vehicular:', derechoText);
        }

        await page.screenshot({ path: 'debug-year-final.png' });
        console.log('📸 Screenshot guardado');

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
    }
})();
