import { test } from '@playwright/test';

test('Verificar botón cambiar obra', async ({ page }) => {
    console.log('🔍 Verificando si existe el botón de cambiar obra...');

    // Login
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'admin@admin.com');
    await page.fill('input[name="password"]', '123456');
    await page.click('button[type="submit"]');
    console.log('✅ Login exitoso');

    // Ir a la página de vehículo
    await page.goto('http://localhost:8000/vehiculos/1');
    await page.waitForLoadState('networkidle');
    console.log('✅ Página cargada');

    // Tomar screenshot para ver la página
    await page.screenshot({ path: 'debug-pagina-vehiculo.png', fullPage: true });

    // Buscar todos los botones
    const botones = await page.locator('button').all();
    console.log(`📋 Encontrados ${botones.length} botones en la página`);

    for (let i = 0; i < botones.length; i++) {
        const texto = await botones[i].textContent();
        console.log(`   Botón ${i + 1}: "${texto}"`);
    }

    // Buscar elementos que contengan "obra"
    const elementosObra = await page.locator('*:has-text("obra")').all();
    console.log(`🏗️ Elementos que contienen "obra": ${elementosObra.length}`);

    for (let i = 0; i < Math.min(elementosObra.length, 10); i++) {
        const texto = await elementosObra[i].textContent();
        console.log(`   Elemento ${i + 1}: "${texto?.slice(0, 100)}"`);
    }

    console.log('🎉 Test completado');
});
