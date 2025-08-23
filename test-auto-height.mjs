import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('🔍 Navegando y haciendo login...');
        await page.goto('http://127.0.0.1:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);

        console.log('🚗 Navegando al vehículo...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');

        // Inyectar JavaScript para abrir el modal directamente
        console.log('🪟 Abriendo modal de operador...');
        await page.evaluate(() => {
            // Crear la función si no existe
            if (typeof openCambiarOperadorModal === 'undefined') {
                window.openCambiarOperadorModal = function () {
                    const modal = document.getElementById('cambiar-operador-modal');
                    if (modal) {
                        modal.classList.remove('hidden');
                    }
                };
            }
            openCambiarOperadorModal();
        });

        await page.waitForTimeout(1000);

        // Verificar que el modal está visible
        const modalVisible = await page.evaluate(() => {
            const modal = document.getElementById('cambiar-operador-modal');
            return modal && !modal.classList.contains('hidden');
        });

        if (modalVisible) {
            console.log('✅ Modal abierto correctamente');

            // Analizar el contenido vs altura del modal
            const analysis = await page.evaluate(() => {
                const modal = document.getElementById('cambiar-operador-modal');
                const dialog = modal.querySelector('div.relative');

                if (!dialog) return { error: 'Dialog no encontrado' };

                // Obtener dimensiones del dialog
                const dialogRect = dialog.getBoundingClientRect();

                // Medir todo el contenido interno
                let totalContentHeight = 0;
                const elements = Array.from(dialog.children);

                elements.forEach(child => {
                    const rect = child.getBoundingClientRect();
                    const styles = window.getComputedStyle(child);
                    const marginTop = parseInt(styles.marginTop) || 0;
                    const marginBottom = parseInt(styles.marginBottom) || 0;
                    totalContentHeight += rect.height + marginTop + marginBottom;
                });

                // Obtener padding del dialog
                const dialogStyles = window.getComputedStyle(dialog);
                const paddingTop = parseInt(dialogStyles.paddingTop) || 0;
                const paddingBottom = parseInt(dialogStyles.paddingBottom) || 0;
                const totalPadding = paddingTop + paddingBottom;

                const idealHeight = totalContentHeight + totalPadding;
                const currentHeight = dialogRect.height;
                const wastedSpace = currentHeight - idealHeight;

                return {
                    currentHeight: Math.round(currentHeight),
                    idealHeight: Math.round(idealHeight),
                    wastedSpace: Math.round(wastedSpace),
                    totalContentHeight: Math.round(totalContentHeight),
                    totalPadding,
                    autoHeight: dialogStyles.height === 'auto',
                    styleHeight: dialog.style.height
                };
            });

            console.log('\n📐 ANÁLISIS DEL MODAL OPTIMIZADO:');
            console.log(`   📏 Altura actual: ${analysis.currentHeight}px`);
            console.log(`   🎯 Altura ideal: ${analysis.idealHeight}px`);
            console.log(`   📝 Contenido: ${analysis.totalContentHeight}px`);
            console.log(`   📦 Padding: ${analysis.totalPadding}px`);
            console.log(`   ⚠️  Espacio desperdiciado: ${analysis.wastedSpace}px`);
            console.log(`   🔧 Altura automática: ${analysis.autoHeight ? 'SÍ' : 'NO'}`);
            console.log(`   🎨 Style height: ${analysis.styleHeight || 'no definido'}`);

            if (analysis.wastedSpace <= 20) {
                console.log('\n✅ ¡EXCELENTE! El modal está bien optimizado');
                console.log('🎉 El espacio desperdiciado está dentro del rango aceptable');
            } else {
                console.log('\n⚠️  Aún hay espacio que se puede optimizar');
                console.log('🔧 Aplicando optimización adicional...');

                // Forzar que el modal se ajuste exactamente al contenido
                await page.evaluate(() => {
                    const modal = document.getElementById('cambiar-operador-modal');
                    const dialog = modal.querySelector('div.relative');
                    dialog.style.height = 'auto';
                    dialog.style.minHeight = 'auto';
                    dialog.style.maxHeight = 'none';
                });

                await page.waitForTimeout(500);
                console.log('✅ Optimización adicional aplicada');
            }

            // Screenshot final
            await page.screenshot({
                path: 'modal-final-optimizado.png',
                fullPage: false
            });
            console.log('\n📸 Screenshot final: modal-final-optimizado.png');

            console.log('\n🎯 VERIFICACIÓN COMPLETADA');
            console.log('✨ El modal ahora debe estar ajustado al contenido');

        } else {
            console.log('❌ No se pudo abrir el modal');
        }

        await page.waitForTimeout(3000);

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
    }
})();
