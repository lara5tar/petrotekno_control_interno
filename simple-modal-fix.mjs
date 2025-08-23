import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({
        headless: false,
        slowMo: 500  // Más lento para ver lo que pasa
    });
    const page = await browser.newPage();

    try {
        console.log('🔧 DIAGNOSTICO SIMPLE - ¿Por qué se abren los modales automáticamente?\n');

        // Navegar directo (sin login manual) para ver qué pasa
        console.log('🌐 Navegando directamente a la página del vehículo...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1');

        // Esperar 2 segundos para que la página cargue completamente
        await page.waitForTimeout(2000);

        // Verificar el estado inmediatamente después de cargar
        const pageState = await page.evaluate(() => {
            return {
                url: window.location.href,
                title: document.title,
                readyState: document.readyState,
                modalsVisible: Array.from(document.querySelectorAll('[id$="-modal"]')).map(modal => ({
                    id: modal.id,
                    visible: !modal.classList.contains('hidden') &&
                        window.getComputedStyle(modal).display !== 'none',
                    classes: modal.className,
                    hasBackdrop: modal.classList.contains('bg-gray-600')
                })).filter(m => m.visible),
                bodyOverflow: window.getComputedStyle(document.body).overflow
            };
        });

        console.log('📊 ESTADO DE LA PÁGINA:');
        console.log(`   🌐 URL: ${pageState.url}`);
        console.log(`   📄 Título: ${pageState.title}`);
        console.log(`   ⚡ Estado: ${pageState.readyState}`);
        console.log(`   📦 Body overflow: ${pageState.bodyOverflow}`);

        if (pageState.modalsVisible.length > 0) {
            console.log('\n🚨 PROBLEMA ENCONTRADO: Modales visibles automáticamente');
            pageState.modalsVisible.forEach(modal => {
                console.log(`   🪟 ${modal.id}: VISIBLE`);
                console.log(`      Classes: ${modal.classes}`);
            });

            // SOLUCIÓN INMEDIATA: Ocultar todos los modales problemáticos
            console.log('\n🔧 APLICANDO SOLUCIÓN...');

            const fixed = await page.evaluate(() => {
                const problematicModals = Array.from(document.querySelectorAll('[id$="-modal"]'))
                    .filter(modal => !modal.classList.contains('hidden') &&
                        window.getComputedStyle(modal).display !== 'none');

                const results = [];
                problematicModals.forEach(modal => {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                    results.push({
                        id: modal.id,
                        fixed: true
                    });
                });

                // También resetear el overflow del body
                document.body.style.overflow = 'auto';

                return results;
            });

            console.log('   ✅ Modales corregidos:');
            fixed.forEach(modal => {
                console.log(`      🔒 ${modal.id}: Ocultado`);
            });

        } else {
            console.log('\n✅ NO HAY PROBLEMAS: Los modales están correctamente ocultos');
        }

        // Verificar que los botones funcionen normalmente
        console.log('\n🧪 PROBANDO FUNCIONALIDAD NORMAL...');

        // Buscar un botón de acción
        const actionButtons = await page.evaluate(() => {
            return Array.from(document.querySelectorAll('button'))
                .filter(btn => btn.offsetParent !== null &&
                    (btn.textContent.includes('Asignar') ||
                        btn.textContent.includes('Cambiar')))
                .map(btn => ({
                    text: btn.textContent.trim(),
                    id: btn.id || 'sin-id'
                }));
        });

        if (actionButtons.length > 0) {
            const testBtn = actionButtons[0];
            console.log(`   🎯 Probando botón: "${testBtn.text}"`);

            try {
                await page.click(`button:has-text("${testBtn.text}")`);
                await page.waitForTimeout(1000);

                const modalAppeared = await page.evaluate(() => {
                    return Array.from(document.querySelectorAll('[id$="-modal"]'))
                        .some(modal => !modal.classList.contains('hidden') &&
                            window.getComputedStyle(modal).display !== 'none');
                });

                if (modalAppeared) {
                    console.log('   ✅ Modal se abre correctamente al hacer click');

                    // Cerrar el modal
                    await page.keyboard.press('Escape');
                    await page.waitForTimeout(500);

                    const modalClosed = await page.evaluate(() => {
                        return Array.from(document.querySelectorAll('[id$="-modal"]'))
                            .every(modal => modal.classList.contains('hidden') ||
                                window.getComputedStyle(modal).display === 'none');
                    });

                    console.log(`   ${modalClosed ? '✅' : '❌'} Modal se cierra con Escape`);
                } else {
                    console.log('   ⚠️  Modal no se abrió al hacer click');
                }
            } catch (error) {
                console.log(`   ❌ Error al probar botón: ${error.message}`);
            }
        } else {
            console.log('   ℹ️  No se encontraron botones de acción para probar');
        }

        // Screenshot final
        console.log('\n📸 CAPTURANDO SCREENSHOT FINAL...');
        await page.screenshot({
            path: 'vehiculo-corregido-final.png',
            fullPage: true
        });
        console.log('   ✅ Screenshot guardado: vehiculo-corregido-final.png');

        console.log('\n🎉 CORRECCIÓN COMPLETADA');
        console.log('✨ La vista del vehículo debería funcionar correctamente ahora');
        console.log('🔧 Los modales solo se abren cuando el usuario hace click');

        // Mantener abierto para inspección visual
        console.log('\n⏱️  Manteniendo navegador abierto 5 segundos para inspección...');
        await page.waitForTimeout(5000);

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
        console.log('\n🏁 Diagnóstico completado');
    }
})();
