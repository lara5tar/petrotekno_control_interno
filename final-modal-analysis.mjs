import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('🔍 Navegando al vehículo...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');

        // Verificar login
        const currentUrl = page.url();
        if (currentUrl.includes('login')) {
            console.log('🔐 Haciendo login...');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password123');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(2000);

            await page.goto('http://127.0.0.1:8000/vehiculos/1');
            await page.waitForLoadState('networkidle');
        }

        console.log('✅ En página del vehículo');

        // Buscar botones de operador
        const operatorButtons = await page.locator('button:has-text("Asignar Operador")').all();
        console.log(`🔘 Encontrados ${operatorButtons.length} botones de Asignar Operador`);

        if (operatorButtons.length > 0) {
            console.log('🪟 Abriendo modal de operador...');
            await operatorButtons[0].click();

            // Esperar el modal
            await page.waitForSelector('#cambiar-operador-modal:not(.hidden)', { timeout: 5000 });
            console.log('✅ Modal de operador abierto');

            // Análisis del modal
            const analysis = await page.evaluate(() => {
                const modal = document.querySelector('#cambiar-operador-modal');
                const dialog = modal.querySelector('div.relative');

                const dialogRect = dialog.getBoundingClientRect();
                const dialogStyles = window.getComputedStyle(dialog);

                // Medir contenido real vs espacio disponible
                const children = Array.from(dialog.children);
                let actualContentHeight = 0;
                const contentDetails = [];

                children.forEach((child, index) => {
                    const rect = child.getBoundingClientRect();
                    const styles = window.getComputedStyle(child);

                    const marginTop = parseInt(styles.marginTop) || 0;
                    const marginBottom = parseInt(styles.marginBottom) || 0;
                    const paddingTop = parseInt(styles.paddingTop) || 0;
                    const paddingBottom = parseInt(styles.paddingBottom) || 0;

                    contentDetails.push({
                        index,
                        element: child.tagName,
                        classes: child.className,
                        contentHeight: rect.height,
                        marginTop,
                        marginBottom,
                        paddingTop,
                        paddingBottom,
                        totalSpace: rect.height + marginTop + marginBottom
                    });

                    actualContentHeight += rect.height + marginTop + marginBottom;
                });

                const dialogPaddingTop = parseInt(dialogStyles.paddingTop) || 0;
                const dialogPaddingBottom = parseInt(dialogStyles.paddingBottom) || 0;
                const dialogPadding = dialogPaddingTop + dialogPaddingBottom;

                return {
                    dialogHeight: dialogRect.height,
                    dialogWidth: dialogRect.width,
                    dialogPaddingTop,
                    dialogPaddingBottom,
                    dialogPadding,
                    actualContentHeight,
                    contentDetails,
                    totalUsedSpace: actualContentHeight + dialogPadding,
                    wastedSpace: dialogRect.height - (actualContentHeight + dialogPadding),
                    classes: dialog.className
                };
            });

            console.log('\n📐 ANÁLISIS DETALLADO DEL MODAL:');
            console.log(`   📏 Tamaño: ${analysis.dialogWidth} x ${analysis.dialogHeight}px`);
            console.log(`   🎨 Clases CSS: ${analysis.classes}`);
            console.log(`   📦 Padding dialog: ${analysis.dialogPaddingTop}px + ${analysis.dialogPaddingBottom}px = ${analysis.dialogPadding}px`);

            console.log('\n📊 ANÁLISIS DE CONTENIDO:');
            analysis.contentDetails.forEach(item => {
                console.log(`   [${item.index}] ${item.element}: ${item.contentHeight}px + margins(${item.marginTop}+${item.marginBottom})px = ${item.totalSpace}px`);
                if (item.classes) console.log(`       📝 Classes: ${item.classes}`);
            });

            console.log('\n🔍 RESUMEN DE ESPACIADO:');
            console.log(`   📝 Contenido real: ${analysis.actualContentHeight}px`);
            console.log(`   📦 Padding: ${analysis.dialogPadding}px`);
            console.log(`   📏 Espacio usado: ${analysis.totalUsedSpace}px`);
            console.log(`   📐 Altura total: ${analysis.dialogHeight}px`);
            console.log(`   ⚠️  ESPACIO DESPERDICIADO: ${analysis.wastedSpace}px`);

            // Recomendaciones
            if (analysis.wastedSpace > 15) {
                console.log('\n💡 RECOMENDACIONES:');
                console.log(`   ✨ Reducir altura del modal en ${analysis.wastedSpace - 10}px`);
                console.log(`   🎯 Altura óptima: ${analysis.totalUsedSpace + 10}px`);
                console.log('   🔧 Acciones sugeridas:');
                console.log('      - Reducir padding del dialog');
                console.log('      - Reducir márgenes entre elementos');
                console.log('      - Usar clases de espaciado más compactas');
            }

            // Screenshot
            await page.screenshot({
                path: 'modal-spacing-final.png',
                fullPage: false
            });
            console.log('\n📸 Screenshot guardado: modal-spacing-final.png');

            await page.waitForTimeout(3000);
        } else {
            console.log('❌ No se encontró botón de Asignar Operador');
        }

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
    }
})();
