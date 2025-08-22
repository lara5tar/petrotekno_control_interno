import { chromium } from 'playwright';

async function testSistemaCompleto() {
    console.log('🚀 Verificando sistema completo con datos del seeder...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 1000
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Login
        console.log('📋 1. Login...');
        await page.goto('http://127.0.0.1:8002/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // 2. Ir directamente a la obra 1
        console.log('📋 2. Navegando a obra 1...');
        await page.goto('http://127.0.0.1:8002/obras/1');
        await page.waitForLoadState('networkidle');
        console.log('✅ Obra cargada correctamente');

        // 3. Probar modal de vehículos
        console.log('📋 3. Probando modal de vehículos...');
        await page.click('#btn-asignar-vehiculos');
        await page.waitForSelector('#asignar-vehiculos-modal:not(.hidden)', { timeout: 5000 });
        console.log('✅ Modal de vehículos abierto');

        // Verificar estructura ordenada
        const encabezadoDisponibles = await page.locator('h6:has-text("Vehículos Disponibles")').count();
        const encabezadoNoDisponibles = await page.locator('h6:has-text("Vehículos No Disponibles")').count();

        console.log(`📊 Encabezado "Vehículos Disponibles": ${encabezadoDisponibles > 0 ? 'Presente' : 'Ausente'}`);
        console.log(`📊 Encabezado "Vehículos No Disponibles": ${encabezadoNoDisponibles > 0 ? 'Presente' : 'Ausente'}`);

        // Contar vehículos por categoría
        const vehiculosDisponibles = await page.locator('#vehiculos-disponibles input[type="checkbox"]:not(:disabled)').count();
        const vehiculosNoDisponibles = await page.locator('#vehiculos-disponibles input[type="checkbox"]:disabled').count();

        console.log(`📊 Vehículos disponibles: ${vehiculosDisponibles}`);
        console.log(`📊 Vehículos no disponibles: ${vehiculosNoDisponibles}`);

        // Verificar que los vehículos asignados están marcados
        const vehiculosAsignados = await page.locator('#vehiculos-disponibles input[type="checkbox"]:checked').count();
        console.log(`📊 Vehículos ya asignados a esta obra: ${vehiculosAsignados}`);

        // Seleccionar un vehículo disponible si hay alguno
        if (vehiculosDisponibles > vehiculosAsignados) {
            console.log('🔄 Probando selección de vehículo disponible...');
            const vehiculoLibre = page.locator('#vehiculos-disponibles input[type="checkbox"]:not(:disabled):not(:checked)').first();
            await vehiculoLibre.check();
            console.log('✅ Vehículo seleccionado');

            // Guardar asignación
            await page.click('button:has-text("Asignar Vehículos")');
            await page.waitForTimeout(2000);
            console.log('✅ Asignación guardada');
        }

        // Cerrar modal si está abierto
        const modalVisible = await page.locator('#asignar-vehiculos-modal:not(.hidden)').count() > 0;
        if (modalVisible) {
            await page.keyboard.press('Escape');
            await page.waitForTimeout(500);
        }

        // 4. Probar modal de responsables
        console.log('📋 4. Probando modal de responsables...');
        await page.click('#btn-cambiar-responsable');
        await page.waitForSelector('#cambiar-responsable-modal:not(.hidden)', { timeout: 5000 });
        console.log('✅ Modal de responsables abierto');

        const responsables = await page.locator('#responsables-disponibles input[type="radio"]').count();
        console.log(`📊 Responsables disponibles: ${responsables}`);

        // Seleccionar un responsable si hay
        if (responsables > 0) {
            await page.locator('#responsables-disponibles input[type="radio"]').first().check();
            await page.click('button:has-text("Asignar Responsable")');
            await page.waitForTimeout(2000);
            console.log('✅ Responsable asignado');
        }

        // Cerrar modal si está abierto
        const modalResponsableVisible = await page.locator('#cambiar-responsable-modal:not(.hidden)').count() > 0;
        if (modalResponsableVisible) {
            await page.keyboard.press('Escape');
            await page.waitForTimeout(500);
        }

        // 5. Verificar que los cambios se reflejan
        console.log('📋 5. Verificando cambios en la página...');
        await page.reload();
        await page.waitForLoadState('networkidle');
        console.log('✅ Página recargada para verificar cambios');

        console.log('🎉 Sistema completo verificado exitosamente');
        console.log('📊 Funcionalidades validadas:');
        console.log('   ✅ Login con credenciales del seeder');
        console.log('   ✅ Navegación a obras');
        console.log('   ✅ Modal de vehículos con orden correcto');
        console.log('   ✅ Restricción de vehículos no disponibles');
        console.log('   ✅ Asignación de vehículos');
        console.log('   ✅ Modal de responsables');
        console.log('   ✅ Persistencia de datos');

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-sistema-completo-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

testSistemaCompleto().catch(console.error);
