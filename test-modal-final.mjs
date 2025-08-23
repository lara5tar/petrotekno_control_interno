import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('🔄 Recargando página para aplicar los nuevos estilos...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');

        // Verificar si necesita login
        if (page.url().includes('login')) {
            console.log('🔐 Haciendo login...');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password123');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(2000);

            await page.goto('http://127.0.0.1:8000/vehiculos/1');
            await page.waitForLoadState('networkidle');
        }

        console.log('✅ Página cargada');

        // Buscar cualquier botón que abra modales
        const buttons = await page.evaluate(() => {
            const allButtons = Array.from(document.querySelectorAll('button'));
            return allButtons
                .filter(btn => btn.offsetParent !== null)
                .map(btn => ({
                    text: btn.textContent.trim(),
                    hasOperador: btn.textContent.toLowerCase().includes('operador'),
                    hasAsignar: btn.textContent.toLowerCase().includes('asignar'),
                    classes: btn.className,
                    onclick: btn.getAttribute('onclick')
                }))
                .filter(btn => btn.hasOperador || btn.hasAsignar);
        });

        console.log('🔘 Botones encontrados:');
        buttons.forEach((btn, i) => {
            console.log(`   [${i}] "${btn.text}" (${btn.onclick || 'sin onclick'})`);
        });

        if (buttons.length > 0) {
            // Hacer click en el primer botón relevante
            const targetButton = buttons[0];
            console.log(`\n🎯 Haciendo click en: "${targetButton.text}"`);

            await page.click(`button:has-text("${targetButton.text}")`);
            await page.waitForTimeout(1000);

            // Verificar qué modal se abrió
            const openModal = await page.evaluate(() => {
                const modalIds = ['cambiar-operador-modal', 'cambiar-obra-modal'];

                for (const id of modalIds) {
                    const modal = document.getElementById(id);
                    if (modal && !modal.classList.contains('hidden')) {
                        const dialog = modal.querySelector('div.modal-dialog-auto, div.relative');
                        const dialogRect = dialog ? dialog.getBoundingClientRect() : null;

                        return {
                            id,
                            isOpen: true,
                            height: dialogRect ? Math.round(dialogRect.height) : 0,
                            width: dialogRect ? Math.round(dialogRect.width) : 0,
                            hasNewClasses: dialog ? dialog.classList.contains('modal-dialog-auto') : false
                        };
                    }
                }
                return { isOpen: false };
            });

            if (openModal.isOpen) {
                console.log(`\n✅ Modal abierto: ${openModal.id}`);
                console.log(`📏 Dimensiones: ${openModal.width}x${openModal.height}px`);
                console.log(`🎨 Clases nuevas aplicadas: ${openModal.hasNewClasses ? 'SÍ' : 'NO'}`);

                // Medir el contenido vs la altura del modal
                const contentAnalysis = await page.evaluate((modalId) => {
                    const modal = document.getElementById(modalId);
                    const dialog = modal.querySelector('div.modal-dialog-auto, div.relative');

                    if (!dialog) return { error: 'Dialog no encontrado' };

                    // Calcular altura del contenido real
                    let contentHeight = 0;
                    Array.from(dialog.children).forEach(child => {
                        const rect = child.getBoundingClientRect();
                        const styles = window.getComputedStyle(child);
                        const marginTop = parseInt(styles.marginTop) || 0;
                        const marginBottom = parseInt(styles.marginBottom) || 0;
                        contentHeight += rect.height + marginTop + marginBottom;
                    });

                    const dialogStyles = window.getComputedStyle(dialog);
                    const padding = (parseInt(dialogStyles.paddingTop) || 0) + (parseInt(dialogStyles.paddingBottom) || 0);

                    const dialogHeight = dialog.getBoundingClientRect().height;
                    const idealHeight = contentHeight + padding;
                    const efficiency = ((idealHeight / dialogHeight) * 100).toFixed(1);

                    return {
                        dialogHeight: Math.round(dialogHeight),
                        contentHeight: Math.round(contentHeight),
                        padding,
                        idealHeight: Math.round(idealHeight),
                        wastedSpace: Math.round(dialogHeight - idealHeight),
                        efficiency: efficiency + '%',
                        isOptimal: (dialogHeight - idealHeight) <= 20
                    };
                }, openModal.id);

                console.log('\n📊 ANÁLISIS DE OPTIMIZACIÓN:');
                console.log(`   📏 Altura del modal: ${contentAnalysis.dialogHeight}px`);
                console.log(`   📝 Altura del contenido: ${contentAnalysis.contentHeight}px`);
                console.log(`   📦 Padding: ${contentAnalysis.padding}px`);
                console.log(`   🎯 Altura ideal: ${contentAnalysis.idealHeight}px`);
                console.log(`   ⚠️  Espacio desperdiciado: ${contentAnalysis.wastedSpace}px`);
                console.log(`   ⚡ Eficiencia: ${contentAnalysis.efficiency}`);

                if (contentAnalysis.isOptimal) {
                    console.log('\n🎉 ¡PERFECTO! El modal está óptimamente ajustado');
                    console.log('✨ El espacio en blanco está minimizado');
                } else {
                    console.log('\n⚠️  El modal aún puede mejorar');
                    console.log(`💡 Se puede reducir en ${contentAnalysis.wastedSpace}px`);
                }

                // Screenshot final
                await page.screenshot({
                    path: 'modal-auto-height-final.png',
                    fullPage: false
                });
                console.log('\n📸 Screenshot guardado: modal-auto-height-final.png');

            } else {
                console.log('\n❌ No se abrió ningún modal');
            }
        } else {
            console.log('\n❌ No se encontraron botones relevantes');
        }

        await page.waitForTimeout(3000);

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
    }
})();
