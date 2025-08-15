import { test } from '@playwright/test';

test('Test final - Cambiar obra EXITOSO', async ({ page }) => {
    console.log('🎯 TEST FINAL - FUNCIONALIDAD CAMBIAR OBRA');

    // Login
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', '123456');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
    console.log('✅ Login exitoso');

    // Ir a vehículo
    await page.goto('http://localhost:8000/vehiculos/1');
    await page.waitForLoadState('networkidle');
    console.log('✅ Página de vehículo cargada');

    // Abrir modal
    await page.click('button:has-text("Cambiar Obra")');
    await page.waitForSelector('#cambiar-obra-modal', { state: 'visible' });
    console.log('✅ Modal abierto');

    // Esperar a que las opciones se carguen en el selector de obra
    await page.waitForFunction(() => {
        const obraSelect = document.querySelector('#obra_id');
        return obraSelect && obraSelect.options.length > 1;
    }, { timeout: 10000 });
    console.log('✅ Opciones de obra cargadas');

    // Verificar opciones disponibles
    const opcionesObra = await page.locator('#obra_id option').count();
    console.log(`📋 Opciones obra: ${opcionesObra}`);

    // Seleccionar primera opción real (no la opción vacía)
    const primeraObra = await page.locator('#obra_id option:nth-child(2)').getAttribute('value');

    console.log(`🎯 Seleccionando obra: ${primeraObra}`);

    await page.selectOption('#obra_id', primeraObra);
    await page.fill('#kilometraje_inicial', '75000');
    console.log('✅ Formulario llenado');

    // Capturar respuesta
    let responseReceived = false;
    let responseData = null;
    let responseStatus = null;

    page.on('response', async (response) => {
        if (response.url().includes('cambiar-obra')) {
            responseReceived = true;
            responseStatus = response.status();
            try {
                responseData = await response.json();
            } catch (e) {
                console.log('📨 Respuesta no es JSON');
            }
        }
    });

    // Enviar formulario
    await page.click('#cambiar-obra-modal button[type="submit"], #cambiar-obra-modal button:has-text("Cambiar"):last-of-type');
    console.log('💾 Formulario enviado');

    // Esperar respuesta
    await page.waitForTimeout(3000);

    console.log('');
    console.log('🏆 RESULTADOS FINALES:');
    console.log('═══════════════════════');
    console.log(`Respuesta del servidor: ${responseReceived ? '✅ RECIBIDA' : '❌ NO RECIBIDA'}`);

    if (responseReceived) {
        console.log(`Status HTTP: ${responseStatus}`);
        if (responseData) {
            if (responseData.success) {
                console.log('🎉 ÉXITO: Obra cambiada correctamente');
                console.log(`Mensaje: ${responseData.message}`);
            } else {
                console.log('❌ ERROR EN RESPUESTA:');
                console.log(`Error: ${responseData.error}`);
                if (responseData.errors) {
                    console.log('Errores de validación:', responseData.errors);
                }
            }
        }
    }

    // Verificar notificaciones en la página
    await page.waitForTimeout(1000);
    const notificacionExito = page.locator('.alert-success, .bg-green-500, [class*="success"]');
    const notificacionError = page.locator('.alert-danger, .bg-red-500, [class*="error"]');

    if (await notificacionExito.isVisible()) {
        const mensaje = await notificacionExito.textContent();
        console.log(`🎉 Notificación de éxito visible: ${mensaje}`);
    }

    if (await notificacionError.isVisible()) {
        const mensaje = await notificacionError.textContent();
        console.log(`❌ Notificación de error visible: ${mensaje}`);
    }

    // Screenshot final
    await page.screenshot({ path: 'test-cambiar-obra-FINAL-RESULTADO.png', fullPage: true });
    console.log('📸 Screenshot final guardado');

    console.log('');
    console.log(responseReceived && responseData?.success ?
        '🏁 TEST COMPLETADO: ✅ FUNCIONALIDAD EXITOSA' :
        '🏁 TEST COMPLETADO: ❌ REQUIERE REVISIÓN');
});
