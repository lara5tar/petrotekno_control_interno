import { test, expect } from '@playwright/test';

test('Debug - Cambiar operador error investigation', async ({ page }) => {
    console.log('🔍 INVESTIGANDO ERROR EN CAMBIAR OPERADOR');

    // Interceptar todas las respuestas de red
    let responseReceived = false;
    let responseStatus = null;
    let responseData = null;
    let responseError = null;

    page.on('response', async response => {
        if (response.url().includes('cambiar-operador')) {
            responseReceived = true;
            responseStatus = response.status();
            console.log(`📡 Respuesta del servidor: ${response.status()}`);
            console.log(`📡 URL: ${response.url()}`);

            try {
                responseData = await response.json();
                console.log('📊 Datos de respuesta:', JSON.stringify(responseData, null, 2));
            } catch (e) {
                console.log('⚠️ Respuesta no es JSON válido');
                const textResponse = await response.text();
                console.log('📄 Respuesta como texto:', textResponse);
            }
        }
    });

    // Interceptar errores de consola
    page.on('console', msg => {
        if (msg.type() === 'error') {
            console.log('❌ Error de consola:', msg.text());
        }
    });

    // Interceptar errores de página
    page.on('pageerror', error => {
        console.log('💥 Error de página:', error.message);
    });

    // Login
    await page.goto('http://127.0.0.1:8001/login');
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', '123456');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
    console.log('✅ Login exitoso');

    // Ir a un vehículo
    await page.goto('http://127.0.0.1:8001/vehiculos/2');
    await page.waitForLoadState('networkidle');
    console.log('✅ Página de vehículo cargada');

    // Verificar que el botón existe y hacer clic
    const botonCambiarOperador = page.locator('button:has-text("Cambiar Operador"), button:has-text("Asignar Operador")');
    await expect(botonCambiarOperador).toBeVisible();
    console.log('✅ Botón de cambiar operador encontrado');

    // Screenshot antes de abrir modal
    await page.screenshot({ path: 'debug-antes-modal.png', fullPage: true });

    // Abrir modal
    await botonCambiarOperador.click();
    await page.waitForTimeout(1000);
    console.log('✅ Modal abierto');

    // Verificar que el modal se abrió
    const modal = page.locator('#cambiar-operador-modal');
    await expect(modal).toBeVisible();
    console.log('✅ Modal visible');

    // Screenshot del modal abierto
    await page.screenshot({ path: 'debug-modal-abierto.png', fullPage: true });

    // Esperar a que las opciones se carguen
    await page.waitForFunction(() => {
        const operadorSelect = document.querySelector('#operador_id');
        return operadorSelect && operadorSelect.options.length > 1;
    }, { timeout: 10000 });
    console.log('✅ Opciones de operador cargadas');

    // Verificar opciones disponibles
    const opcionesOperador = await page.locator('#operador_id option').count();
    console.log(`📋 Opciones operador: ${opcionesOperador}`);

    if (opcionesOperador > 1) {
        // Seleccionar primera opción real (no la opción vacía)
        const primerOperador = await page.locator('#operador_id option:nth-child(2)').getAttribute('value');
        console.log(`🎯 Seleccionando operador: ${primerOperador}`);

        await page.selectOption('#operador_id', primerOperador);
        await page.fill('#observaciones', 'Test de cambio de operador');
        console.log('✅ Formulario llenado');

        // Screenshot antes de enviar
        await page.screenshot({ path: 'debug-antes-envio.png', fullPage: true });

        // Enviar formulario
        await page.click('#cambiar-operador-form button[type="submit"]');
        console.log('💾 Formulario enviado');

        // Esperar respuesta o mensaje
        await page.waitForTimeout(5000);

        // Screenshot después de envío
        await page.screenshot({ path: 'debug-despues-envio.png', fullPage: true });

        // Verificar si hay mensajes de error o éxito visibles
        const mensajesError = await page.locator('.alert-danger, .bg-red-500, [class*="error"], [class*="red"]').all();
        const mensajesExito = await page.locator('.alert-success, .bg-green-500, [class*="success"], [class*="green"]').all();

        console.log(`🔴 Mensajes de error encontrados: ${mensajesError.length}`);
        console.log(`🟢 Mensajes de éxito encontrados: ${mensajesExito.length}`);

        // Examinar cada mensaje de error
        for (let i = 0; i < mensajesError.length; i++) {
            const mensaje = mensajesError[i];
            const isVisible = await mensaje.isVisible();
            const texto = await mensaje.textContent();
            const clases = await mensaje.getAttribute('class');

            console.log(`❌ Error ${i + 1}:`);
            console.log(`   Visible: ${isVisible}`);
            console.log(`   Texto: "${texto}"`);
            console.log(`   Clases: "${clases}"`);
        }

        // Examinar cada mensaje de éxito
        for (let i = 0; i < mensajesExito.length; i++) {
            const mensaje = mensajesExito[i];
            const isVisible = await mensaje.isVisible();
            const texto = await mensaje.textContent();
            const clases = await mensaje.getAttribute('class');

            console.log(`✅ Éxito ${i + 1}:`);
            console.log(`   Visible: ${isVisible}`);
            console.log(`   Texto: "${texto}"`);
            console.log(`   Clases: "${clases}"`);
        }

        console.log('');
        console.log('🏆 RESULTADOS FINALES:');
        console.log('═══════════════════════');
        console.log(`Respuesta del servidor: ${responseReceived ? '✅ RECIBIDA' : '❌ NO RECIBIDA'}`);

        if (responseReceived) {
            console.log(`Status HTTP: ${responseStatus}`);
            if (responseData) {
                console.log('Datos de respuesta:', JSON.stringify(responseData, null, 2));
            }
        }

    } else {
        console.log('❌ No hay operadores disponibles para seleccionar');
    }

    // Screenshot final
    await page.screenshot({ path: 'debug-cambiar-operador-final.png', fullPage: true });
    console.log('📸 Screenshots guardados para análisis');
});
