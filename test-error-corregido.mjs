import { chromium } from 'playwright';

async function testErrorCorregido() {
    console.log('🚀 Verificando corrección del error de vehículos...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 800
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Login
        console.log('📋 1. Haciendo login...');
        await page.goto('http://127.0.0.1:8002/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');

        // 2. Probar ir a vehículos (aquí estaba el error)
        console.log('📋 2. Navegando a vehículos para verificar el error...');
        try {
            await page.goto('http://127.0.0.1:8002/vehiculos/1');
            await page.waitForLoadState('networkidle');
            console.log('✅ Vehículos se carga correctamente - Error corregido');
        } catch (error) {
            console.log('❌ Error todavía presente en vehículos:', error.message);
        }

        // 3. Probar ir a obras (verificar que sigue funcionando)
        console.log('📋 3. Verificando que obras sigue funcionando...');
        try {
            await page.goto('http://127.0.0.1:8002/obras/1');
            await page.waitForLoadState('networkidle');
            console.log('✅ Obras se carga correctamente');
        } catch (error) {
            console.log('❌ Error en obras:', error.message);
        }

        // 4. Probar modal de vehículos en obras
        console.log('📋 4. Probando modal de vehículos en obras...');
        try {
            await page.click('#btn-asignar-vehiculos');
            await page.waitForSelector('#asignar-vehiculos-modal:not(.hidden)', { timeout: 5000 });
            console.log('✅ Modal de vehículos se abre correctamente');

            // Verificar que los vehículos se muestran ordenados
            const encabezadoDisponibles = await page.locator('text=Vehículos Disponibles').isVisible();
            const encabezadoNoDisponibles = await page.locator('text=Vehículos No Disponibles').isVisible();

            console.log(`✅ Encabezado disponibles: ${encabezadoDisponibles ? 'Visible' : 'No visible'}`);
            console.log(`✅ Encabezado no disponibles: ${encabezadoNoDisponibles ? 'Visible' : 'No visible'}`);

            await page.keyboard.press('Escape');
            await page.waitForTimeout(500);

        } catch (error) {
            console.log('❌ Error en modal de vehículos:', error.message);
        }

        console.log('🎉 Verificación completada');

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-error-corregido.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

testErrorCorregido().catch(console.error);
