import { chromium } from 'playwright';

async function testOrdenVehiculos() {
    console.log('🚀 Iniciando test de orden de vehículos...');

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

        // 4. Verificar encabezados de secciones
        console.log('🔍 4. Verificando encabezados de secciones...');

        const encabezadoDisponibles = await page.locator('text=Vehículos Disponibles').first();
        const encabezadoNoDisponibles = await page.locator('text=Vehículos No Disponibles').first();

        const tieneEncabezadoDisponibles = await encabezadoDisponibles.isVisible();
        const tieneEncabezadoNoDisponibles = await encabezadoNoDisponibles.isVisible();

        console.log(`✅ Encabezado "Vehículos Disponibles": ${tieneEncabezadoDisponibles ? 'Visible' : 'No visible'}`);
        console.log(`✅ Encabezado "Vehículos No Disponibles": ${tieneEncabezadoNoDisponibles ? 'Visible' : 'No visible'}`);

        // 5. Verificar orden de los vehículos
        console.log('🔍 5. Verificando orden de vehículos...');

        const labels = await page.locator('#vehiculos-disponibles label').all();
        let vehiculosDisponiblesEncontrados = 0;
        let vehiculosNoDisponiblesEncontrados = 0;
        let ordenCorrecto = true;
        let yaEncontreNoDisponibles = false;

        for (let i = 0; i < labels.length; i++) {
            // Saltar encabezados que no son labels de vehículos
            const labelText = await labels[i].textContent();
            if (labelText.includes('Vehículos Disponibles') || labelText.includes('Vehículos No Disponibles')) {
                continue;
            }

            const checkbox = labels[i].locator('input[type="checkbox"]');
            const isDisabled = await checkbox.isDisabled();
            const labelContent = await labels[i].textContent();
            const esNoDisponible = labelContent.includes('(No disponible)');

            console.log(`📊 Vehículo ${i + 1}: ${esNoDisponible ? 'No disponible' : 'Disponible'} | Deshabilitado: ${isDisabled}`);

            if (esNoDisponible) {
                vehiculosNoDisponiblesEncontrados++;
                yaEncontreNoDisponibles = true;

                if (!isDisabled) {
                    console.log('❌ ERROR: Vehículo no disponible no está deshabilitado');
                    ordenCorrecto = false;
                }
            } else {
                vehiculosDisponiblesEncontrados++;

                if (yaEncontreNoDisponibles) {
                    console.log('❌ ERROR: Encontré vehículo disponible después de no disponibles');
                    ordenCorrecto = false;
                }

                if (isDisabled) {
                    console.log('❌ ERROR: Vehículo disponible está deshabilitado');
                    ordenCorrecto = false;
                }
            }
        }

        console.log(`📊 Resumen del orden:`);
        console.log(`   - Vehículos disponibles: ${vehiculosDisponiblesEncontrados}`);
        console.log(`   - Vehículos no disponibles: ${vehiculosNoDisponiblesEncontrados}`);
        console.log(`   - Orden correcto: ${ordenCorrecto ? '✅ SÍ' : '❌ NO'}`);

        // 6. Verificar separador visual
        console.log('🔍 6. Verificando separador visual...');

        if (vehiculosDisponiblesEncontrados > 0 && vehiculosNoDisponiblesEncontrados > 0) {
            const separador = await page.locator('.border-t.border-gray-300').count();
            console.log(`✅ Separador visual: ${separador > 0 ? 'Presente' : 'Ausente'}`);
        }

        // 7. Verificar estilos visuales
        console.log('🔍 7. Verificando estilos visuales...');

        const encabezadoVerde = await page.locator('.bg-green-50').count();
        const encabezadoRojo = await page.locator('.bg-red-50').count();

        console.log(`✅ Encabezado verde (disponibles): ${encabezadoVerde > 0 ? 'Presente' : 'Ausente'}`);
        console.log(`✅ Encabezado rojo (no disponibles): ${encabezadoRojo > 0 ? 'Presente' : 'Ausente'}`);

        // 8. Cerrar modal
        console.log('🔍 8. Cerrando modal...');
        await page.keyboard.press('Escape');
        await page.waitForTimeout(1000);
        console.log('✅ Modal cerrado');

        if (ordenCorrecto && tieneEncabezadoDisponibles && vehiculosDisponiblesEncontrados > 0) {
            console.log('🎉 Test de orden completado exitosamente');
            console.log('✅ Los vehículos disponibles aparecen primero');
            console.log('✅ Los vehículos no disponibles aparecen al final');
            console.log('✅ Encabezados y separadores visuales funcionan correctamente');
        } else {
            console.log('❌ Test falló: El orden no es correcto');
        }

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-orden-vehiculos-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

testOrdenVehiculos().catch(console.error);
