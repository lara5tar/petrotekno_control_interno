import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false, slowMo: 1000 });
    const page = await browser.newPage();

    try {
        console.log('🔍 Navegando a la página de vehículos...');
        await page.goto('http://localhost:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');

        // PROBAR BOTÓN ASIGNAR/CAMBIAR OPERADOR
        console.log('\n🧪 PROBANDO BOTÓN OPERADOR...');
        const botonOperador = page.locator('button').filter({ hasText: /Asignar Operador|Cambiar Operador/ }).first();

        if (await botonOperador.count() > 0) {
            console.log('✅ Botón operador encontrado');
            await botonOperador.click();
            await page.waitForTimeout(2000);

            const modalOperador = page.locator('#cambiar-operador-modal');
            const isVisible = await modalOperador.isVisible();
            console.log(`🪟 Modal operador visible: ${isVisible ? '✅' : '❌'}`);

            if (isVisible) {
                // Cerrar modal
                await page.keyboard.press('Escape');
                await page.waitForTimeout(1000);
            }
        } else {
            console.log('❌ Botón operador no encontrado');
        }

        // PROBAR BOTÓN CAMBIAR OBRA
        console.log('\n🧪 PROBANDO BOTÓN OBRA...');
        const botonObra = page.locator('button').filter({ hasText: /Cambiar Obra|Asignar Obra/ }).first();

        if (await botonObra.count() > 0) {
            console.log('✅ Botón obra encontrado');
            await botonObra.click();
            await page.waitForTimeout(2000);

            const modalObra = page.locator('#cambiar-obra-modal');
            const isVisibleObra = await modalObra.isVisible();
            console.log(`🪟 Modal obra visible: ${isVisibleObra ? '✅' : '❌'}`);

            if (isVisibleObra) {
                // Cerrar modal
                await page.keyboard.press('Escape');
                await page.waitForTimeout(1000);
            }
        } else {
            console.log('❌ Botón obra no encontrado');
        }

        // PROBAR BOTÓN MANTENIMIENTO (REFERENCIA)
        console.log('\n🧪 PROBANDO BOTÓN MANTENIMIENTO...');
        const botonMantenimiento = page.locator('button').filter({ hasText: /Registrar Mantenimiento/ }).first();

        if (await botonMantenimiento.count() > 0) {
            console.log('✅ Botón mantenimiento encontrado');
            await botonMantenimiento.click();
            await page.waitForTimeout(2000);

            const modalMantenimiento = page.locator('#registrar-mantenimiento-modal');
            const isVisibleMant = await modalMantenimiento.isVisible();
            console.log(`🪟 Modal mantenimiento visible: ${isVisibleMant ? '✅' : '❌'}`);

            if (isVisibleMant) {
                // Cerrar modal
                await page.keyboard.press('Escape');
                await page.waitForTimeout(1000);
            }
        } else {
            console.log('❌ Botón mantenimiento no encontrado');
        }

        console.log('\n✅ Prueba completada');

    } catch (error) {
        console.error('❌ Error:', error);
    } finally {
        await browser.close();
    }
})();
