import { chromium } from 'playwright';

async function testResponsableButton() {
    const browser = await chromium.launch({
        headless: false,
        slowMo: 500
    });

    try {
        const page = await browser.newPage();

        console.log('🚀 Navegando al login...');
        await page.goto('http://localhost:8002/login');

        // Login
        await page.fill('#email', 'admin@petrotekno.com');
        await page.fill('#password', 'admin123');
        await page.click('button[type="submit"]');

        console.log('✅ Login exitoso');
        await page.waitForLoadState('networkidle');

        // Navegar a la lista de vehículos
        console.log('🚗 Navegando a vehículos...');
        await page.goto('http://localhost:8002/vehiculos');
        await page.waitForLoadState('networkidle');

        // Buscar el primer vehículo
        const vehiculoLink = await page.locator('a[href*="/vehiculos/"]').first();
        if (await vehiculoLink.count() > 0) {
            console.log('🔗 Abriendo vehículo...');
            await vehiculoLink.click();
            await page.waitForLoadState('networkidle');

            console.log('🔍 Buscando botón del responsable...');

            // Buscar botón por onclick
            const botonResponsable = page.locator('button[onclick="openResponsableObraModal()"]');
            const count = await botonResponsable.count();

            console.log(`📊 Botones encontrados: ${count}`);

            if (count > 0) {
                const texto = await botonResponsable.textContent();
                console.log(`📝 Texto del botón: "${texto}"`);

                console.log('🖱️ Haciendo clic...');
                await botonResponsable.click();

                await page.waitForTimeout(1000);

                const modal = page.locator('#responsable-obra-modal');
                const visible = await modal.isVisible();

                console.log(`🎯 Modal visible: ${visible}`);

                if (visible) {
                    console.log('🎉 ¡ÉXITO! El modal funciona correctamente');
                } else {
                    console.log('❌ El modal no se abrió');
                }
            } else {
                console.log('❌ No se encontró el botón');
            }
        }

    } catch (error) {
        console.error('❌ Error:', error);
    } finally {
        await browser.close();
    }
}

testResponsableButton().catch(console.error);
