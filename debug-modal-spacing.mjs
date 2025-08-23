import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        // Navegar directamente al vehículo (asumiendo sesión activa)
        console.log('� Navegando al vehículo...');
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

            // Navegar de nuevo al vehículo
            await page.goto('http://127.0.0.1:8000/vehiculos/1');
            await page.waitForLoadState('networkidle');
        }

        console.log('✅ En página del vehículo');

        // Abrir modal de obra
        console.log('🪟 Abriendo modal de cambiar obra...');
        await page.click('button:has-text("Cambiar Obra")');
        await page.waitForSelector('#cambiar-obra-modal:not(.hidden)', { timeout: 5000 });

        // Medir dimensiones del modal
        const modalInfo = await page.evaluate(() => {
            const modal = document.querySelector('#cambiar-obra-modal');
            const modalDialog = modal.querySelector('div.relative');

            const modalRect = modal.getBoundingClientRect();
            const dialogRect = modalDialog.getBoundingClientRect();

            // Obtener información de padding y margins
            const dialogStyles = window.getComputedStyle(modalDialog);

            return {
                modalWidth: modalRect.width,
                modalHeight: modalRect.height,
                dialogWidth: dialogRect.width,
                dialogHeight: dialogRect.height,
                dialogTop: dialogRect.top,
                dialogLeft: dialogRect.left,
                padding: dialogStyles.padding,
                paddingTop: dialogStyles.paddingTop,
                paddingBottom: dialogStyles.paddingBottom,
                marginTop: dialogStyles.marginTop,
                marginBottom: dialogStyles.marginBottom,
                classes: modalDialog.className
            };
        });

        console.log('📐 Dimensiones del modal:');
        console.log(`   Modal completo: ${modalInfo.modalWidth}x${modalInfo.modalHeight}`);
        console.log(`   Dialog interno: ${modalInfo.dialogWidth}x${modalInfo.dialogHeight}`);
        console.log(`   Posición dialog: top=${modalInfo.dialogTop}, left=${modalInfo.dialogLeft}`);
        console.log(`   Padding: ${modalInfo.padding}`);
        console.log(`   Classes: ${modalInfo.classes}`);

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
                    marginBottom: styles.marginBottom,
                    paddingTop: styles.paddingTop,
                    paddingBottom: styles.paddingBottom
                });
                totalHeight += rect.height;
            });

            return {
                elements,
                totalContentHeight: totalHeight,
                dialogPaddingTop: parseInt(window.getComputedStyle(dialog).paddingTop),
                dialogPaddingBottom: parseInt(window.getComputedStyle(dialog).paddingBottom)
            };
        });

        console.log('📊 Análisis detallado de contenido:');
        contentInfo.elements.forEach(el => {
            console.log(`   [${el.index}] ${el.tagName}: ${el.height}px (margin: ${el.marginTop}/${el.marginBottom}, padding: ${el.paddingTop}/${el.paddingBottom})`);
            console.log(`       Classes: ${el.className}`);
        });
        console.log(`   Total contenido: ${contentInfo.totalContentHeight}px`);
        console.log(`   Padding dialog: ${contentInfo.dialogPaddingTop}px / ${contentInfo.dialogPaddingBottom}px`);
        console.log(`   Altura total dialog: ${modalInfo.dialogHeight}px`);
        console.log(`   Espacio sobrante: ${modalInfo.dialogHeight - contentInfo.totalContentHeight - contentInfo.dialogPaddingTop - contentInfo.dialogPaddingBottom}px`);

        // Capturar screenshot
        await page.screenshot({
            path: 'modal-spacing-analysis.png',
            fullPage: false
        });

        console.log('📸 Screenshot guardado como modal-spacing-analysis.png');

        // Esperar para inspección visual
        await page.waitForTimeout(5000);

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
    }
})();
