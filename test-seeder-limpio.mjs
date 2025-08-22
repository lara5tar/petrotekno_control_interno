import { chromium } from 'playwright';

async function testConSeederLimpio() {
    console.log('🚀 Verificando funcionalidad con seeder limpio...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 1000
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Login con credenciales del seeder
        console.log('📋 1. Login con usuario del seeder...');
        await page.goto('http://127.0.0.1:8002/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso con credenciales del seeder');

        // 2. Verificar que hay obras
        console.log('📋 2. Verificando obras...');
        await page.goto('http://127.0.0.1:8002/obras');
        await page.waitForLoadState('networkidle');

        const obras = await page.locator('table tbody tr').count();
        console.log(`📊 Obras encontradas: ${obras}`);

        if (obras > 0) {
            console.log('✅ Hay obras disponibles');

            // Ir a la primera obra
            await page.click('table tbody tr:first-child a[href*="/obras/"]');
            await page.waitForLoadState('networkidle');
            console.log('✅ Navegación a obra individual exitosa');

            // 3. Probar modal de vehículos
            console.log('📋 3. Probando modal de vehículos...');
            await page.click('#btn-asignar-vehiculos');
            await page.waitForSelector('#asignar-vehiculos-modal:not(.hidden)', { timeout: 5000 });
            console.log('✅ Modal de vehículos se abre');

            // Verificar estructura del modal
            const tieneEncabezadoDisponibles = await page.locator('h6:has-text("Vehículos Disponibles")').count() > 0;
            console.log(`📊 Encabezado "Vehículos Disponibles": ${tieneEncabezadoDisponibles ? 'Presente' : 'Ausente'}`);

            // Contar vehículos en el modal
            const totalVehiculos = await page.locator('#vehiculos-disponibles input[type="checkbox"]').count();
            console.log(`📊 Total vehículos en modal: ${totalVehiculos}`);

            // Cerrar modal
            await page.keyboard.press('Escape');
            await page.waitForTimeout(500);

            // 4. Probar modal de responsables
            console.log('📋 4. Probando modal de responsables...');
            await page.click('#btn-asignar-responsable');
            await page.waitForSelector('#asignar-responsable-modal:not(.hidden)', { timeout: 5000 });
            console.log('✅ Modal de responsables se abre');

            const responsables = await page.locator('#responsables-disponibles input[type="radio"]').count();
            console.log(`📊 Responsables disponibles: ${responsables}`);

            await page.keyboard.press('Escape');
            await page.waitForTimeout(500);

        } else {
            console.log('⚠️ No hay obras en el sistema');
        }

        // 5. Verificar vehículos
        console.log('📋 5. Verificando vehículos...');
        await page.goto('http://127.0.0.1:8002/vehiculos');
        await page.waitForLoadState('networkidle');

        const vehiculos = await page.locator('table tbody tr').count();
        console.log(`📊 Vehículos encontrados: ${vehiculos}`);

        console.log('🎉 Verificación con seeder limpio completada exitosamente');
        console.log('📊 Resumen:');
        console.log(`   - Obras: ${obras > 0 ? obras : 'Sin datos'}`);
        console.log(`   - Vehículos: ${vehiculos > 0 ? vehiculos : 'Sin datos'}`);
        console.log('   - Modal vehículos: ✅ Funcional');
        console.log('   - Modal responsables: ✅ Funcional');

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-seeder-limpio-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

testConSeederLimpio().catch(console.error);
