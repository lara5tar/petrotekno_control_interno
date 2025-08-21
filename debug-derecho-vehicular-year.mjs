import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        console.log('🚀 Iniciando verificación del año en Derecho Vehicular...');

        // Ir a la página de login
        console.log('📱 Navegando a login...');
        await page.goto('http://localhost:8002/login');

        // Login
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que cargue el dashboard
        await page.waitForURL('**/dashboard');
        console.log('✅ Login exitoso');

        // Ir a la lista de vehículos
        console.log('🚗 Navegando a lista de vehículos...');
        await page.goto('http://localhost:8002/vehiculos');
        await page.waitForTimeout(2000);

        // Buscar un vehículo que tenga derecho vehicular
        const vehiculoConDerecho = await page.locator('tbody tr').first();
        await vehiculoConDerecho.click();

        console.log('👀 Verificando vehículo con derecho vehicular...');
        await page.waitForTimeout(2000);

        // Buscar la sección de Derecho Vehicular
        const derechoSection = page.locator('text=Derecho Vehicular').first();
        const derechoContainer = derechoSection.locator('..').locator('..').locator('.bg-gray-600');

        // Verificar si muestra el año
        const derechoText = await derechoContainer.textContent();
        console.log(`📄 Texto en Derecho Vehicular: "${derechoText}"`);

        if (derechoText.includes('Año')) {
            console.log('✅ ÉXITO: El año se muestra correctamente en Derecho Vehicular');

            // Extraer el año del texto
            const yearMatch = derechoText.match(/Año (\d{4})/);
            if (yearMatch) {
                console.log(`📅 Año mostrado: ${yearMatch[1]}`);
            }
        } else if (derechoText.includes('Sin documento')) {
            console.log('⚠️  Este vehículo no tiene derecho vehicular cargado');
        } else {
            console.log('❌ ERROR: No se muestra el año en Derecho Vehicular');
            console.log(`   Texto actual: "${derechoText}"`);
        }

        // Tomar screenshot para verificación visual
        await page.screenshot({
            path: 'debug-derecho-vehicular-year.png',
            fullPage: false
        });
        console.log('📸 Screenshot guardado como debug-derecho-vehicular-year.png');

    } catch (error) {
        console.error('❌ Error en verificación:', error);
        await page.screenshot({
            path: 'debug-derecho-vehicular-error.png',
            fullPage: true
        });
    } finally {
        await browser.close();
    }
})();
