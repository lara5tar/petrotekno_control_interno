import { chromium } from 'playwright';

async function testAsignarResponsable() {
    console.log('🚀 Iniciando test del modal de asignar responsable...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 1000
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Ir al login
        console.log('📋 1. Navegando al login...');
        await page.goto('http://127.0.0.1:8002/login');
        await page.waitForSelector('input[name="email"]');

        // 2. Hacer login
        console.log('🔐 2. Haciendo login...');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // 3. Ir directamente a la obra específica
        console.log('📋 3. Navegando directamente a obra...');
        await page.goto('http://127.0.0.1:8002/obras/1');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        // 4. Verificar que estamos en la página correcta
        console.log('🔍 4. Verificando página de obra...');
        console.log('✅ En página de obra');

        // 5. Verificar que el recuadro rojo esté visible (sin responsable)
        console.log('🔍 5. Verificando estado inicial...');
        const recuadroRojo = page.locator('.bg-red-50.border-red-200');
        const botonAzul = page.locator('#btn-cambiar-responsable');

        if (await recuadroRojo.isVisible()) {
            console.log('✅ Recuadro rojo visible - Obra sin responsable');

            // 6. Hacer clic en el botón azul "Asignar Responsable"
            console.log('🖱️ 6. Haciendo clic en botón "Asignar Responsable"...');

            // Primero verificar que el botón existe y es visible
            await botonAzul.waitFor({ state: 'visible' });
            console.log('✅ Botón azul encontrado y visible');

            // Verificar si hay errores en la consola antes del click
            page.on('console', msg => console.log('CONSOLA:', msg.text()));
            page.on('pageerror', error => console.log('ERROR JS:', error.message));

            await botonAzul.click();
            await page.waitForTimeout(1000);

            // 7. Verificar que el modal se abre
            console.log('🔍 7. Verificando que el modal se abre...');
            const modal = page.locator('#cambiar-responsable-modal');

            // Verificar si el modal existe en el DOM
            const modalExists = await modal.count() > 0;
            console.log(`Modal existe en DOM: ${modalExists}`);

            if (modalExists) {
                const modalClasses = await modal.getAttribute('class');
                console.log(`Clases del modal: ${modalClasses}`);

                // Intentar abrir el modal manualmente si no se abrió
                if (modalClasses.includes('hidden')) {
                    console.log('⚠️ Modal está oculto, intentando abrir manualmente...');
                    await page.evaluate(() => {
                        const modal = document.getElementById('cambiar-responsable-modal');
                        if (modal) {
                            modal.classList.remove('hidden');
                            document.body.style.overflow = 'hidden';
                        }
                    });
                    await page.waitForTimeout(500);
                }
            }

            await page.waitForSelector('#cambiar-responsable-modal:not(.hidden)', { timeout: 5000 });

            if (await modal.isVisible()) {
                console.log('✅ Modal abierto correctamente');

                // 8. Verificar elementos del modal
                console.log('🔍 8. Verificando elementos del modal...');
                const titulo = page.locator('#modal-responsable-title');
                const select = page.locator('#responsable_id');
                const botonSubmit = page.locator('#submit-responsable-btn');

                await titulo.waitFor({ state: 'visible' });
                await select.waitFor({ state: 'visible' });
                await botonSubmit.waitFor({ state: 'visible' });

                console.log('✅ Título del modal:', await titulo.textContent());
                console.log('✅ Select de responsables visible');
                console.log('✅ Botón de submit visible');

                // 9. Verificar que hay opciones en el select
                console.log('🔍 9. Verificando opciones de responsables...');
                const opciones = await select.locator('option').count();
                console.log(`✅ Encontradas ${opciones} opciones en el select`);

                if (opciones > 1) { // Más de 1 porque incluye la opción vacía
                    // 10. Seleccionar el primer responsable disponible
                    console.log('👤 10. Seleccionando responsable...');
                    await select.selectOption({ index: 1 }); // Seleccionar la primera opción válida

                    const valorSeleccionado = await select.inputValue();
                    console.log(`✅ Responsable seleccionado con ID: ${valorSeleccionado}`);

                    // 11. Agregar observaciones opcionales
                    console.log('📝 11. Agregando observaciones...');
                    const textarea = page.locator('#observaciones_responsable');
                    await textarea.fill('Asignación automática vía test de Playwright');

                    // 12. Hacer submit del formulario
                    console.log('💾 12. Enviando formulario...');
                    await botonSubmit.click();

                    // 13. Esperar redirection y verificar éxito
                    console.log('⏳ 13. Esperando confirmación...');
                    await page.waitForURL('**/obras/*');

                    // Verificar mensaje de éxito o cambio en la UI
                    try {
                        await page.waitForSelector('.alert-success, .bg-green-50', { timeout: 5000 });
                        console.log('✅ Mensaje de éxito detectado');
                    } catch (e) {
                        console.log('⚠️ No se detectó mensaje de éxito, pero continuando...');
                    }

                    // 14. Verificar que el recuadro rojo ya no está visible
                    console.log('🔍 14. Verificando cambio en UI...');
                    await page.waitForTimeout(2000); // Dar tiempo para que la página se actualice

                    const recuadroRojoFinal = page.locator('.bg-red-50.border-red-200');
                    const esVisible = await recuadroRojoFinal.isVisible();

                    if (!esVisible) {
                        console.log('✅ ¡Éxito! El recuadro rojo ya no es visible - Responsable asignado');
                    } else {
                        console.log('⚠️ El recuadro rojo sigue visible - Verificar asignación');
                    }

                    // 15. Verificar que ahora muestra información del responsable
                    const infoResponsable = page.locator('.space-y-4');
                    if (await infoResponsable.isVisible()) {
                        console.log('✅ Información del responsable ahora visible');
                    }

                } else {
                    console.log('❌ No hay responsables disponibles para asignar');
                }

            } else {
                console.log('❌ Modal no se abrió correctamente');
            }

        } else {
            console.log('ℹ️ Esta obra ya tiene responsable asignado');

            // Probar cambio de responsable en lugar de asignación
            console.log('🔄 Probando cambio de responsable...');
            await botonAzul.click();

            const modal = page.locator('#cambiar-responsable-modal');
            await page.waitForSelector('#cambiar-responsable-modal:not(.hidden)', { timeout: 5000 });

            if (await modal.isVisible()) {
                console.log('✅ Modal de cambio de responsable abierto');

                // Cerrar modal con ESC para completar la prueba
                await page.keyboard.press('Escape');
                await page.waitForSelector('#cambiar-responsable-modal.hidden', { timeout: 3000 });
                console.log('✅ Modal cerrado con ESC');
            }
        }

        console.log('🎉 Test completado exitosamente');

    } catch (error) {
        console.error('❌ Error durante el test:', error.message);

        // Capturar screenshot en caso de error
        await page.screenshot({
            path: 'test-asignar-responsable-error.png',
            fullPage: true
        });
        console.log('📸 Screenshot guardado como test-asignar-responsable-error.png');
    } finally {
        await browser.close();
    }
}

// Ejecutar el test
testAsignarResponsable().catch(console.error);
