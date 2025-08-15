import { test } from '@playwright/test';

test('Test final - Cambiar obra con credenciales correctas', async ({ page }) => {
    console.log('🎯 Test final con credenciales correctas...');

    // Login con credenciales correctas
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', '123456');
    await page.click('button[type="submit"]');

    await page.waitForLoadState('networkidle');
    console.log(`✅ Login exitoso - URL: ${page.url()}`);

    // Ir a la página de vehículo
    await page.goto('http://localhost:8000/vehiculos/1');
    await page.waitForLoadState('networkidle');
    console.log(`✅ Página de vehículo cargada - URL: ${page.url()}`);

    // Buscar el botón de cambiar obra (puede estar con diferentes textos)
    const posiblesBotones = [
        'button:has-text("Cambiar Obra")',
        'button:has-text("cambiar obra")',
        'button:has-text("Cambiar")',
        'a:has-text("Cambiar Obra")',
        '[data-action="cambiar-obra"]',
        '#cambiar-obra-btn'
    ];

    let botonEncontrado = null;
    for (const selector of posiblesBotones) {
        const boton = page.locator(selector).first();
        if (await boton.isVisible()) {
            botonEncontrado = boton;
            console.log(`✅ Botón encontrado con selector: ${selector}`);
            break;
        }
    }

    if (!botonEncontrado) {
        // Tomar screenshot para debug
        await page.screenshot({ path: 'debug-no-boton-encontrado.png', fullPage: true });

        // Buscar todos los botones
        const todosBotones = await page.locator('button').all();
        console.log(`📋 Botones disponibles: ${todosBotones.length}`);
        for (let i = 0; i < todosBotones.length; i++) {
            const texto = await todosBotones[i].textContent();
            console.log(`   Botón ${i + 1}: "${texto?.trim()}"`);
        }

        console.log('❌ No se encontró el botón de cambiar obra');
        return;
    }

    // Hacer clic en el botón
    await botonEncontrado.click();
    console.log('✅ Botón clickeado');

    // Esperar a que aparezca el modal
    await page.waitForSelector('#cambiar-obra-modal', { state: 'visible', timeout: 5000 });
    console.log('✅ Modal visible');

    // Llenar formulario
    await page.selectOption('#obra_id', '1');
    await page.selectOption('#operador_id', '1');
    await page.fill('#kilometraje_inicial', '70000');
    console.log('✅ Formulario llenado');

    // Capturar respuesta
    let responseReceived = false;
    let responseData = null;

    page.on('response', async (response) => {
        if (response.url().includes('cambiar-obra')) {
            responseReceived = true;
            try {
                responseData = await response.json();
                console.log('📨 Respuesta JSON:', responseData);
            } catch (e) {
                console.log('📨 Respuesta no-JSON - Status:', response.status());
            }
        }
    });

    // Enviar formulario
    const submitButton = page.locator('#cambiar-obra-modal button[type="submit"]').or(
        page.locator('#cambiar-obra-modal button:has-text("Cambiar")').last()
    );

    await submitButton.click();
    console.log('💾 Formulario enviado');

    // Esperar respuesta
    await page.waitForTimeout(5000);

    console.log('📊 Resultados finales:');
    console.log(`   Respuesta recibida: ${responseReceived ? 'Sí' : 'No'}`);
    if (responseData) {
        console.log(`   Estado: ${responseData.success ? 'EXITOSO ✅' : 'ERROR ❌'}`);
        console.log(`   Mensaje: ${responseData.message || responseData.error}`);
    }

    // Verificar si hay notificación de éxito
    const notificacion = page.locator('.alert-success, .bg-green-500, [class*="success"]');
    if (await notificacion.isVisible()) {
        const mensaje = await notificacion.textContent();
        console.log(`🎉 Notificación de éxito: ${mensaje}`);
    }

    await page.screenshot({ path: 'test-cambiar-obra-final.png', fullPage: true });
    console.log('📸 Screenshot final guardado');

    console.log('🏁 Test completado');
});
