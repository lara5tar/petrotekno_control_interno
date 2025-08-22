import { chromium } from 'playwright';

async function capturarModalOrden() {
    console.log('📸 Capturando screenshot del modal con orden mejorado...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 500
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // Login
        await page.goto('http://127.0.0.1:8002/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');

        // Ir a obra 1
        await page.goto('http://127.0.0.1:8002/obras/1');
        await page.waitForLoadState('networkidle');

        // Abrir modal
        await page.click('#btn-asignar-vehiculos');
        await page.waitForSelector('#asignar-vehiculos-modal:not(.hidden)', { timeout: 5000 });

        // Esperar un momento para que todo se renderice
        await page.waitForTimeout(1000);

        // Capturar screenshot del modal
        await page.screenshot({
            path: 'modal-vehiculos-ordenado.png',
            fullPage: true
        });

        console.log('✅ Screenshot capturada: modal-vehiculos-ordenado.png');

        // También capturar solo el contenido del modal
        const modal = page.locator('#asignar-vehiculos-modal .bg-white');
        await modal.screenshot({
            path: 'modal-vehiculos-contenido-ordenado.png'
        });

        console.log('✅ Screenshot del contenido capturada: modal-vehiculos-contenido-ordenado.png');

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
    }
}

capturarModalOrden().catch(console.error);
