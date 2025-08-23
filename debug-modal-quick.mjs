import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('🔍 Navegando al vehículo...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');

        // Verificar si hay que hacer login
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

        // Buscar todos los botones disponibles
        const buttons = await page.evaluate(() => {
            const allButtons = Array.from(document.querySelectorAll('button'));
            return allButtons.map(btn => ({
                text: btn.textContent.trim(),
                onclick: btn.getAttribute('onclick'),
                classes: btn.className
            })).filter(btn => btn.text.includes('Obra') || btn.text.includes('obra'));
        });

        console.log('🔘 Botones relacionados con obra encontrados:');
        buttons.forEach((btn, i) => {
            console.log(`   [${i}] "${btn.text}" - onclick: ${btn.onclick}`);
        });

        // Intentar abrir modal usando la función JavaScript directamente
        console.log('🪟 Abriendo modal usando función JavaScript...');
        await page.evaluate(() => {
            if (typeof openCambiarObraModal === 'function') {
                openCambiarObraModal();
            } else {
                console.error('Función openCambiarObraModal no encontrada');
            }
        });

        // Esperar a que aparezca el modal
        await page.waitForSelector('#cambiar-obra-modal:not(.hidden)', { timeout: 5000 });
        console.log('✅ Modal abierto');

        // Medir dimensiones del modal
        const modalInfo = await page.evaluate(() => {
            const modal = document.querySelector('#cambiar-obra-modal');
            const modalDialog = modal.querySelector('div.relative');

            const modalRect = modal.getBoundingClientRect();
            const dialogRect = modalDialog.getBoundingClientRect();
            const dialogStyles = window.getComputedStyle(modalDialog);

            return {
                dialogWidth: dialogRect.width,
                dialogHeight: dialogRect.height,
                dialogTop: dialogRect.top,
                dialogLeft: dialogRect.left,
                padding: dialogStyles.padding,
                classes: modalDialog.className
            };
        });

        // Medir contenido interno
        const contentInfo = await page.evaluate(() => {
            const dialog = document.querySelector('#cambiar-obra-modal div.relative');
            const children = Array.from(dialog.children);

            let totalHeight = 0;
            const elements = [];

            children.forEach((child, index) => {
                const rect = child.getBoundingClientRect();
                const styles = window.getComputedStyle(child);
                elements.push({
                    index,
                    tagName: child.tagName,
                    className: child.className,
                    height: rect.height,
                    marginTop: styles.marginTop,
                    marginBottom: styles.marginBottom
                });
                totalHeight += rect.height;
            });

            const dialogPadding = parseInt(window.getComputedStyle(dialog).paddingTop) +
                parseInt(window.getComputedStyle(dialog).paddingBottom);

            return {
                elements,
                totalContentHeight: totalHeight,
                dialogPadding,
                wastedSpace: Math.max(0, dialog.getBoundingClientRect().height - totalHeight - dialogPadding)
            };
        });

        console.log('\n📐 ANÁLISIS DEL MODAL:');
        console.log(`   Tamaño dialog: ${modalInfo.dialogWidth}x${modalInfo.dialogHeight}px`);
        console.log(`   Clases: ${modalInfo.classes}`);
        console.log(`   Padding: ${modalInfo.padding}`);

        console.log('\n📊 CONTENIDO POR ELEMENTO:');
        contentInfo.elements.forEach(el => {
            console.log(`   [${el.index}] ${el.tagName}: ${el.height}px (${el.className})`);
        });

        console.log('\n🔍 RESUMEN:');
        console.log(`   Altura total contenido: ${contentInfo.totalContentHeight}px`);
        console.log(`   Padding dialog: ${contentInfo.dialogPadding}px`);
        console.log(`   Altura dialog: ${modalInfo.dialogHeight}px`);
        console.log(`   ⚠️  ESPACIO DESPERDICIADO: ${contentInfo.wastedSpace}px`);

        if (contentInfo.wastedSpace > 20) {
            console.log('\n💡 RECOMENDACIÓN: Reducir espaciado o altura del modal');
        }

        // Capturar screenshot
        await page.screenshot({
            path: 'modal-spacing-analysis.png',
            fullPage: false
        });
        console.log('\n📸 Screenshot guardado como modal-spacing-analysis.png');

        // Esperar para inspección visual
        await page.waitForTimeout(5000);

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
    }
})();
