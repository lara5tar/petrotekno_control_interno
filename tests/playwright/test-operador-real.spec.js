import { test, expect } from '@playwright/test';

test('Verificar cambio de operador - debe mostrar Operador Test', async ({ page }) => {

    page.on('response', async response => {
        if (response.url().includes('cambiar-operador')) {
            const responseText = await response.text().catch(() => '');
            console.log(`🎯 RESPUESTA CAMBIAR OPERADOR:`);
            console.log(`Status: ${response.status()}`);
            console.log(`Body: ${responseText}`);
        }
    });

    try {
        console.log('🚀 Probando cambio de operador vehículo 2...');

        // Login
        await page.goto('http://127.0.0.1:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Ir al vehículo 2
        await page.goto('http://127.0.0.1:8000/vehiculos/2');
        await page.waitForTimeout(2000);

        // Hacer clic en cambiar operador
        const btnCambiar = page.locator('button:has-text("Cambiar Operador"), button:has-text("Asignar Operador")');
        await btnCambiar.first().click();
        await page.waitForTimeout(1000);

        // Verificar operadores en el dropdown
        console.log('🔍 Verificando operadores en dropdown...');
        const selectOperador = page.locator('#operador_id');

        // Obtener todas las opciones
        const opciones = await selectOperador.locator('option').all();
        console.log(`Total opciones: ${opciones.length}`);

        for (let i = 0; i < opciones.length; i++) {
            const option = opciones[i];
            const value = await option.getAttribute('value');
            const text = await option.textContent();
            console.log(`Opción ${i}: value="${value}" text="${text}"`);
        }

        // Buscar específicamente "Operador Test"
        const operadorTestOption = page.locator('#operador_id option:has-text("Operador Test")');
        const operadorTestExists = await operadorTestOption.count() > 0;
        console.log(`¿Existe "Operador Test"?: ${operadorTestExists}`);

        if (operadorTestExists) {
            console.log('✅ Operador Test encontrado, seleccionando...');
            await selectOperador.selectOption('3'); // Usar el value directamente
            await page.waitForTimeout(500);

            // Verificar que se seleccionó
            const selectedValue = await selectOperador.inputValue();
            console.log(`Valor seleccionado: ${selectedValue}`);

            // Intentar guardar
            console.log('💾 Guardando cambio...');

            const responsePromise = page.waitForResponse(
                response => response.url().includes('cambiar-operador'),
                { timeout: 10000 }
            ).catch(() => null);

            await page.click('button[type="submit"]:has-text("Cambiar Operador"), button[type="submit"]:has-text("Asignar Operador")');

            const response = await responsePromise;

            if (response) {
                const responseText = await response.text();
                console.log(`📊 Respuesta del servidor:`);
                console.log(`Status: ${response.status()}`);
                console.log(`Body: ${responseText}`);

                // Verificar si es exitoso
                if (responseText.includes('"success":true')) {
                    console.log('✅ ¡Cambio exitoso!');
                } else if (responseText.includes('"success":false')) {
                    console.log('❌ Error en el cambio');
                    if (responseText.includes('"error"')) {
                        const errorMatch = responseText.match(/"error":"([^"]*)"/);
                        if (errorMatch) {
                            console.log(`Error específico: ${errorMatch[1]}`);
                        }
                    }
                } else {
                    console.log('⚠️ Respuesta inesperada');
                }
            }

            // Esperar un momento y buscar mensajes
            await page.waitForTimeout(3000);

            // Buscar elementos rojos/alertas
            const alertElements = await page.locator('.alert, .error, .message, [role="alert"]').all();
            console.log(`Elementos de alerta encontrados: ${alertElements.length}`);

            for (const alert of alertElements) {
                const text = await alert.textContent().catch(() => '');
                const isVisible = await alert.isVisible().catch(() => false);
                const classes = await alert.getAttribute('class').catch(() => '');

                if (isVisible) {
                    console.log(`🚨 Alerta visible: "${text}" (${classes})`);
                }
            }

            // Buscar específicamente elementos rojos
            const redElements = await page.evaluate(() => {
                const elements = document.querySelectorAll('*');
                const redOnes = [];

                for (let el of elements) {
                    const style = window.getComputedStyle(el);
                    const hasRedColor = style.color.includes('red') ||
                        style.backgroundColor.includes('red') ||
                        style.borderColor.includes('red');

                    if (hasRedColor && el.offsetParent !== null) {
                        redOnes.push({
                            tag: el.tagName,
                            text: el.textContent.trim().substring(0, 100),
                            classes: el.className,
                            id: el.id
                        });
                    }
                }
                return redOnes;
            });

            console.log(`Elementos rojos visibles: ${redElements.length}`);
            redElements.forEach((el, i) => {
                console.log(`🔴 ${i + 1}: ${el.tag} "${el.text}" (${el.classes})`);
            });

        } else {
            console.log('❌ No se encontró "Operador Test" como opción');
            console.log('Esto confirma que el problema es la lógica de filtrado');
        }

        await page.screenshot({ path: 'test-operador-final.png', fullPage: true });

    } catch (error) {
        console.error('💥 Error:', error);
        await page.screenshot({ path: 'test-error.png', fullPage: true });
    }
});
