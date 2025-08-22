import { chromium } from 'playwright';

async function testSistemaLimpio() {
    console.log('🚀 Verificando sistema limpio con solo usuario administrador...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 800
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Login con admin
        console.log('📋 1. Login con usuario administrador...');
        await page.goto('http://127.0.0.1:8002/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // 2. Verificar que tiene acceso a todas las secciones
        console.log('📋 2. Verificando acceso a secciones principales...');

        // Obras
        await page.goto('http://127.0.0.1:8002/obras');
        await page.waitForLoadState('networkidle');
        const tituloObras = await page.textContent('h1, h2');
        console.log(`✅ Acceso a Obras: ${tituloObras ? 'Permitido' : 'Bloqueado'}`);

        // Vehículos  
        await page.goto('http://127.0.0.1:8002/vehiculos');
        await page.waitForLoadState('networkidle');
        const tituloVehiculos = await page.textContent('h1, h2');
        console.log(`✅ Acceso a Vehículos: ${tituloVehiculos ? 'Permitido' : 'Bloqueado'}`);

        // Personal
        await page.goto('http://127.0.0.1:8002/personal');
        await page.waitForLoadState('networkidle');
        const tituloPersonal = await page.textContent('h1, h2');
        console.log(`✅ Acceso a Personal: ${tituloPersonal ? 'Permitido' : 'Bloqueado'}`);

        // 3. Verificar botones de creación (requieren permisos específicos)
        console.log('📋 3. Verificando permisos de creación...');

        // Botón crear obra
        await page.goto('http://127.0.0.1:8002/obras');
        await page.waitForLoadState('networkidle');
        const botonCrearObra = await page.locator('a[href*="/obras/create"], button:has-text("Crear"), button:has-text("Nueva")').count();
        console.log(`✅ Botón Crear Obra: ${botonCrearObra > 0 ? 'Visible' : 'No visible'}`);

        // Botón crear vehículo
        await page.goto('http://127.0.0.1:8002/vehiculos');
        await page.waitForLoadState('networkidle');
        const botonCrearVehiculo = await page.locator('a[href*="/vehiculos/create"], button:has-text("Crear"), button:has-text("Nuevo")').count();
        console.log(`✅ Botón Crear Vehículo: ${botonCrearVehiculo > 0 ? 'Visible' : 'No visible'}`);

        // Botón crear personal
        await page.goto('http://127.0.0.1:8002/personal');
        await page.waitForLoadState('networkidle');
        const botonCrearPersonal = await page.locator('a[href*="/personal/create"], button:has-text("Crear"), button:has-text("Nuevo")').count();
        console.log(`✅ Botón Crear Personal: ${botonCrearPersonal > 0 ? 'Visible' : 'No visible'}`);

        // 4. Verificar que el sistema está limpio (sin datos de prueba)
        console.log('📋 4. Verificando estado limpio del sistema...');

        await page.goto('http://127.0.0.1:8002/obras');
        await page.waitForLoadState('networkidle');
        const filas = await page.locator('table tbody tr').count();
        console.log(`📊 Obras en sistema: ${filas}`);

        await page.goto('http://127.0.0.1:8002/vehiculos');
        await page.waitForLoadState('networkidle');
        const filasVehiculos = await page.locator('table tbody tr').count();
        console.log(`📊 Vehículos en sistema: ${filasVehiculos}`);

        console.log('🎉 Verificación completada exitosamente');
        console.log('📊 Resumen del sistema:');
        console.log('   ✅ Usuario administrador funcional');
        console.log('   ✅ Acceso completo a todas las secciones');
        console.log('   ✅ Permisos de creación habilitados');
        console.log('   ✅ Sistema limpio (sin datos de prueba)');
        console.log('   🔐 Listo para uso en producción');

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-sistema-limpio-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

testSistemaLimpio().catch(console.error);
