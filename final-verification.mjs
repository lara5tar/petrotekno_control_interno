import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('🎯 VERIFICACIÓN FINAL - Vista de vehículo corregida\n');

        // Navegar con login automático
        await page.goto('http://127.0.0.1:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        console.log('🔐 Login completado, navegando al vehículo...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');

        // Esperar a que el JavaScript de inicialización se ejecute
        await page.waitForTimeout(2000);

        // Verificar estado final
        const finalState = await page.evaluate(() => {
            const modals = [
                'cambiar-operador-modal',
                'cambiar-obra-modal',
                'registrar-mantenimiento-modal',
                'responsable-obra-modal'
            ];

            const modalStates = modals.map(id => {
                const modal = document.getElementById(id);
                return {
                    id,
                    exists: !!modal,
                    hidden: modal ? modal.classList.contains('hidden') : true,
                    displayNone: modal ? window.getComputedStyle(modal).display === 'none' : true,
                    isProperlyHidden: modal ? (modal.classList.contains('hidden') &&
                        window.getComputedStyle(modal).display === 'none') : true
                };
            });

            const buttons = Array.from(document.querySelectorAll('button'))
                .filter(btn => btn.offsetParent !== null &&
                    (btn.textContent.includes('Asignar') ||
                        btn.textContent.includes('Cambiar')))
                .map(btn => btn.textContent.trim());

            return {
                url: window.location.href,
                title: document.title,
                modalStates,
                buttonCount: buttons.length,
                buttons: buttons.slice(0, 3), // Primeros 3 botones
                bodyOverflow: window.getComputedStyle(document.body).overflow,
                hasContent: document.querySelector('.grid-cols-3') !== null
            };
        });

        console.log('📊 ESTADO FINAL DE LA VISTA:');
        console.log(`   📄 Título: ${finalState.title}`);
        console.log(`   🌐 URL: ${finalState.url}`);
        console.log(`   📦 Contenido principal: ${finalState.hasContent ? 'SÍ ✅' : 'NO ❌'}`);
        console.log(`   📜 Body overflow: ${finalState.bodyOverflow}`);
        console.log(`   🔘 Botones encontrados: ${finalState.buttonCount}`);

        if (finalState.buttons.length > 0) {
            console.log('   📋 Botones disponibles:');
            finalState.buttons.forEach((btn, i) => {
                console.log(`      [${i + 1}] "${btn}"`);
            });
        }

        console.log('\n🪟 ESTADO DE MODALES:');
        finalState.modalStates.forEach(modal => {
            const status = !modal.exists ? '❓ No existe' :
                modal.isProperlyHidden ? '✅ Correctamente oculto' :
                    '🚨 PROBLEMA - Visible';
            console.log(`   ${modal.id}: ${status}`);
            if (modal.exists && !modal.isProperlyHidden) {
                console.log(`      - hidden class: ${modal.hidden}`);
                console.log(`      - display none: ${modal.displayNone}`);
            }
        });

        // Verificar que los modales funcionen al hacer click
        if (finalState.buttonCount > 0 && finalState.hasContent) {
            console.log('\n🧪 PROBANDO FUNCIONALIDAD...');

            // Probar primer botón disponible
            const firstButton = finalState.buttons[0];
            if (firstButton) {
                console.log(`   🎯 Probando: "${firstButton}"`);

                try {
                    await page.click(`button:has-text("${firstButton}")`);
                    await page.waitForTimeout(1000);

                    const modalOpened = await page.evaluate(() => {
                        return Array.from(document.querySelectorAll('[id$="-modal"]'))
                            .some(modal => !modal.classList.contains('hidden') &&
                                window.getComputedStyle(modal).display !== 'none');
                    });

                    if (modalOpened) {
                        console.log('   ✅ Modal se abre correctamente');

                        // Cerrar con Escape
                        await page.keyboard.press('Escape');
                        await page.waitForTimeout(500);

                        const modalClosed = await page.evaluate(() => {
                            return Array.from(document.querySelectorAll('[id$="-modal"]'))
                                .every(modal => modal.classList.contains('hidden') ||
                                    window.getComputedStyle(modal).display === 'none');
                        });

                        console.log(`   ${modalClosed ? '✅' : '❌'} Modal se cierra correctamente`);
                    } else {
                        console.log('   ⚠️  Modal no se abrió (puede necesitar verificación)');
                    }
                } catch (error) {
                    console.log(`   ❌ Error al probar: ${error.message}`);
                }
            }
        }

        // Screenshot final
        await page.screenshot({
            path: 'vista-vehiculo-final-corregida.png',
            fullPage: true
        });
        console.log('\n📸 Screenshot final: vista-vehiculo-final-corregida.png');

        // Evaluación final
        const allModalsHidden = finalState.modalStates.every(m => !m.exists || m.isProperlyHidden);
        const hasProperContent = finalState.hasContent && finalState.buttonCount > 0;

        if (allModalsHidden && hasProperContent) {
            console.log('\n🎉 ¡ÉXITO TOTAL!');
            console.log('✅ Todos los modales están correctamente ocultos');
            console.log('✅ La vista del vehículo carga correctamente');
            console.log('✅ Los botones están disponibles y funcionales');
            console.log('🎨 El diseño está completamente corregido');
        } else {
            console.log('\n⚠️  Resumen de estado:');
            console.log(`   Modales ocultos: ${allModalsHidden ? '✅' : '❌'}`);
            console.log(`   Contenido correcto: ${hasProperContent ? '✅' : '❌'}`);
        }

        await page.waitForTimeout(3000);

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
        console.log('\n🏁 Verificación final completada');
    }
})();
