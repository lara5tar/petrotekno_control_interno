import { test, expect } from '@playwright/test';

test('Verificar dropdown de responsables en formulario de obras', async ({ page }) => {
    // 1. Login al sistema
    console.log('🔐 Iniciando login...');
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Esperar redirección exitosa
    try {
        await page.waitForURL(/.*home.*/, { timeout: 10000 });
        console.log('✅ Login exitoso');
    } catch (e) {
        console.log('❌ Error en login');
        // Capturar la URL actual
        console.log(`📍 URL actual: ${page.url()}`);
        await page.screenshot({ path: 'login-error.png' });
    }

    // 2. Navegar al formulario de creación de obra
    console.log('🚀 Navegando al formulario de obra...');
    await page.goto('http://localhost:8000/obras/create');

    // 3. Verificar si el dropdown de encargados existe y tiene opciones
    try {
        await page.waitForSelector('#encargado_id', { timeout: 5000 });
        console.log('✅ Dropdown de encargados encontrado');

        // Contar opciones en el dropdown
        const opcionesCount = await page.locator('#encargado_id option').count();
        console.log(`📋 Número de opciones en el dropdown: ${opcionesCount}`);

        // Verificar que hay al menos una opción además del placeholder
        expect(opcionesCount).toBeGreaterThan(1);

        // Capturar las opciones disponibles
        const opcionesTexto = await page.evaluate(() => {
            const opciones = Array.from(document.querySelectorAll('#encargado_id option'));
            return opciones.map(opcion => opcion.textContent?.trim()).filter(texto => texto !== 'Seleccione un responsable');
        });

        console.log('📋 Opciones disponibles en el dropdown:');
        console.log(opcionesTexto);

        // Tomar captura de pantalla con el dropdown abierto
        await page.click('#encargado_id');
        await page.waitForTimeout(500);
        await page.screenshot({ path: 'dropdown-encargados-simple.png' });

        // Verificar con aserción
        expect(opcionesTexto.length).toBeGreaterThan(0);

    } catch (e) {
        console.log('❌ Error al verificar el dropdown de encargados');
        console.log(e);
        await page.screenshot({ path: 'dropdown-error-simple.png', fullPage: true });
        throw e;
    }
});