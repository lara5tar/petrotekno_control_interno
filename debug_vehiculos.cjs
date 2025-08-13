const { chromium } = require('playwright');

async function debugVehiculosPage() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    // Capturar errores de consola
    page.on('console', msg => {
        console.log(`🖥️ Console ${msg.type()}: ${msg.text()}`);
    });

    page.on('pageerror', error => {
        console.log(`💥 Page error: ${error.message}`);
    });

    try {
        console.log('🔍 Depurando página de vehículos...');

        // Login
        await page.goto('http://localhost:8001/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // Ir a vehículos
        await page.goto('http://localhost:8001/vehiculos');
        console.log('📍 Navegando a vehículos...');

        // Esperar un poco y capturar el contenido de la página
        await page.waitForTimeout(3000);

        const title = await page.title();
        console.log('📄 Título de la página:', title);

        const url = page.url();
        console.log('🌐 URL actual:', url);

        // Verificar si hay algún error visible en la página
        const errorElements = await page.locator('.alert-danger, .bg-red-100, .text-red-600, .error').allTextContents();
        if (errorElements.length > 0) {
            console.log('⚠️ Errores encontrados en la página:', errorElements);
        }

        // Capturar el contenido del body
        const bodyText = await page.locator('body').textContent();
        if (bodyText.includes('Exception') || bodyText.includes('Error') || bodyText.includes('Fatal')) {
            console.log('🚨 Error en el contenido de la página:');
            console.log(bodyText.substring(0, 500) + '...');
        }

        // Verificar si existe el h2 específico
        const h2Elements = await page.locator('h2').allTextContents();
        console.log('📋 Elementos H2 encontrados:', h2Elements);

        // Capturar screenshot
        await page.screenshot({ path: 'debug_vehiculos_page.png', fullPage: true });
        console.log('📸 Screenshot guardado como debug_vehiculos_page.png');

    } catch (error) {
        console.error('❌ Error durante debug:', error.message);
        await page.screenshot({ path: 'debug_error.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

debugVehiculosPage().catch(console.error);