import { chromium } from 'playwright';

async function testVehiculosNoDisponibles() {
    console.log('🚀 Iniciando test de vehículos no disponibles...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 1000
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

        // 2. Ir a la obra 1
        console.log('📋 2. Navegando a obra 1...');
        await page.goto('http://127.0.0.1:8002/obras/1');
        await page.waitForLoadState('networkidle');

        // 3. Abrir modal de vehículos
        console.log('🔍 3. Abriendo modal de vehículos...');
        await page.click('#btn-asignar-vehiculos');
        await page.waitForSelector('#asignar-vehiculos-modal:not(.hidden)', { timeout: 5000 });
        console.log('✅ Modal abierto');

        // 4. Verificar vehículos disponibles y no disponibles
        console.log('🔍 4. Verificando estado de vehículos...');

        const checkboxes = await page.locator('#vehiculos-disponibles input[type="checkbox"]').all();
        console.log(`📊 Total de vehículos en el modal: ${checkboxes.length}`);

        let vehiculosDisponibles = 0;
        let vehiculosNoDisponibles = 0;

        for (let i = 0; i < checkboxes.length; i++) {
            const checkbox = checkboxes[i];
            const isDisabled = await checkbox.isDisabled();

            if (isDisabled) {
                vehiculosNoDisponibles++;
                console.log(`❌ Vehículo ${i + 1}: No disponible (deshabilitado)`);

                // Verificar que tiene el texto "(No disponible)"
                const label = checkbox.locator('xpath=ancestor::label');
                const labelText = await label.textContent();
                if (labelText.includes('(No disponible)')) {
                    console.log('✅ Vehículo correctamente marcado como "No disponible"');
                }

                // Verificar que tiene el estilo correcto (opacidad reducida)
                const hasOpacity = await label.evaluate(el => {
                    const style = window.getComputedStyle(el);
                    return style.opacity !== '1' || el.classList.contains('opacity-60');
                });
                if (hasOpacity) {
                    console.log('✅ Vehículo tiene estilo visual correcto (opacidad reducida)');
                }

            } else {
                vehiculosDisponibles++;
                console.log(`✅ Vehículo ${i + 1}: Disponible`);
            }
        }

        console.log(`📊 Resumen: ${vehiculosDisponibles} disponibles, ${vehiculosNoDisponibles} no disponibles`);

        // 5. Intentar seleccionar un vehículo no disponible (debe fallar)
        if (vehiculosNoDisponibles > 0) {
            console.log('🔍 5. Probando selección de vehículo no disponible...');

            const vehiculoNoDisponible = page.locator('#vehiculos-disponibles input[type="checkbox"]:disabled').first();

            // Intentar hacer click (no debería funcionar)
            try {
                await vehiculoNoDisponible.click({ timeout: 1000 });
                console.log('❌ ERROR: Se pudo seleccionar un vehículo no disponible');
            } catch (e) {
                console.log('✅ Correcto: No se puede seleccionar vehículo no disponible');
            }

            // Verificar que sigue sin estar seleccionado
            const isChecked = await vehiculoNoDisponible.isChecked();
            if (!isChecked) {
                console.log('✅ Vehículo no disponible permanece sin seleccionar');
            } else {
                console.log('❌ ERROR: Vehículo no disponible fue seleccionado');
            }
        } else {
            console.log('ℹ️ No hay vehículos no disponibles para probar');
        }

        // 6. Seleccionar algunos vehículos disponibles
        if (vehiculosDisponibles > 0) {
            console.log('🔍 6. Seleccionando vehículos disponibles...');

            const vehiculosDisponiblesCheckboxes = page.locator('#vehiculos-disponibles input[type="checkbox"]:not(:disabled)');
            const cantidadASeleccionar = Math.min(2, await vehiculosDisponiblesCheckboxes.count());

            for (let i = 0; i < cantidadASeleccionar; i++) {
                const checkbox = vehiculosDisponiblesCheckboxes.nth(i);
                const isChecked = await checkbox.isChecked();

                if (!isChecked) {
                    await checkbox.check();
                    console.log(`✅ Vehículo disponible ${i + 1} seleccionado`);
                } else {
                    console.log(`ℹ️ Vehículo disponible ${i + 1} ya estaba seleccionado`);
                }
            }
        }

        // 7. Cerrar modal
        console.log('🔍 7. Cerrando modal...');
        await page.keyboard.press('Escape');
        await page.waitForSelector('#asignar-vehiculos-modal.hidden', { timeout: 3000 });
        console.log('✅ Modal cerrado');

        console.log('🎉 Test de vehículos no disponibles completado exitosamente');

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-vehiculos-no-disponibles-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

testVehiculosNoDisponibles().catch(console.error);
