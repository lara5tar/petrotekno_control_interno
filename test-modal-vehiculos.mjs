import { chromium } from 'playwright';

async function testModalVehiculos() {
    console.log('🚀 Iniciando test del modal de asignar vehículos...');

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

        // 2. Ir a la obra
        console.log('📋 2. Navegando a obra...');
        await page.goto('http://127.0.0.1:8002/obras/1');
        await page.waitForLoadState('networkidle');

        // 3. Buscar el botón "Asignar Vehículos"
        console.log('🔍 3. Buscando botón "Asignar Vehículos"...');
        const botonVehiculos = page.locator('#btn-asignar-vehiculos');

        if (await botonVehiculos.isVisible()) {
            console.log('✅ Botón "Asignar Vehículos" encontrado');

            // 4. Hacer clic en el botón
            console.log('🖱️ 4. Haciendo clic en el botón...');
            await botonVehiculos.click();

            // 5. Verificar que el modal se abre
            console.log('🔍 5. Verificando modal...');
            const modal = page.locator('#asignar-vehiculos-modal');
            await page.waitForSelector('#asignar-vehiculos-modal:not(.hidden)', { timeout: 5000 });

            if (await modal.isVisible()) {
                console.log('✅ ¡Modal de vehículos abierto correctamente!');

                // 6. Verificar elementos del modal
                const titulo = await page.locator('#modal-vehiculos-title').textContent();
                console.log(`✅ Título: ${titulo.trim()}`);

                const vehiculosDisponibles = await page.locator('#vehiculos-disponibles input[type="checkbox"]').count();
                console.log(`✅ Vehículos disponibles: ${vehiculosDisponibles}`);

                if (vehiculosDisponibles > 0) {
                    // 7. Seleccionar algunos vehículos
                    console.log('✅ 7. Seleccionando vehículos...');
                    const checkboxes = page.locator('#vehiculos-disponibles input[type="checkbox"]');

                    // Seleccionar los primeros 2 vehículos
                    for (let i = 0; i < Math.min(2, vehiculosDisponibles); i++) {
                        const checkbox = checkboxes.nth(i);
                        if (!(await checkbox.isChecked())) {
                            await checkbox.check();
                            console.log(`✅ Vehículo ${i + 1} seleccionado`);
                        }
                    }

                    // 8. Agregar observaciones
                    console.log('📝 8. Agregando observaciones...');
                    await page.fill('#observaciones_vehiculos', 'Asignación vía test automatizado');

                    // 9. Enviar formulario
                    console.log('💾 9. Enviando formulario...');
                    await page.click('#submit-vehiculos-btn');
                    await page.waitForURL('**/obras/*');

                    // 10. Verificar éxito
                    console.log('🔍 10. Verificando asignación...');
                    await page.waitForTimeout(2000);

                    // Verificar que ya no aparece "Sin vehículo asignado"
                    const sinVehiculo = page.locator('text=Sin vehículo asignado');
                    const esSinVehiculoVisible = await sinVehiculo.isVisible();

                    if (!esSinVehiculoVisible) {
                        console.log('✅ ¡ÉXITO! Vehículos asignados correctamente');
                    } else {
                        console.log('⚠️ Aún muestra "Sin vehículo asignado"');
                    }

                } else {
                    console.log('⚠️ No hay vehículos disponibles para asignar');
                }

                // Cerrar modal si aún está abierto
                const modalStillOpen = await modal.isVisible();
                if (modalStillOpen) {
                    await page.keyboard.press('Escape');
                    console.log('✅ Modal cerrado');
                }

            } else {
                console.log('❌ Modal no se abrió correctamente');
            }

        } else {
            console.log('❌ Botón "Asignar Vehículos" no encontrado o no visible');
        }

        console.log('🎉 Test de modal de vehículos completado');

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-modal-vehiculos-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

testModalVehiculos().catch(console.error);
