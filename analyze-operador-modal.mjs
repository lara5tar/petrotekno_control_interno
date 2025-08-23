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

        // Abrir modal de operador que sabemos que funciona
        console.log('🪟 Abriendo modal de operador...');
        await page.evaluate(() => {
            openCambiarOperadorModal();
        });

        await page.waitForSelector('#cambiar-operador-modal:not(.hidden)', { timeout: 5000 });
        console.log('✅ Modal de operador abierto');

        // Análisis detallado del modal de operador
        const analysis = await page.evaluate(() => {
            const modal = document.querySelector('#cambiar-operador-modal');
            const dialog = modal.querySelector('div.relative');

            const dialogRect = dialog.getBoundingClientRect();
            const dialogStyles = window.getComputedStyle(dialog);

            // Medir cada hijo del dialog
            const children = Array.from(dialog.children);
            let totalContentHeight = 0;
            const elements = [];

            children.forEach((child, index) => {
                const rect = child.getBoundingClientRect();
                const styles = window.getComputedStyle(child);
                const marginTop = parseInt(styles.marginTop) || 0;
                const marginBottom = parseInt(styles.marginBottom) || 0;
                const elementHeight = rect.height + marginTop + marginBottom;

                elements.push({
                    index,
                    tagName: child.tagName,
                    className: child.className,
                    height: rect.height,
                    marginTop: marginTop,
                    marginBottom: marginBottom,
                    totalHeight: elementHeight
                });

                totalContentHeight += elementHeight;
            });

            const paddingTop = parseInt(dialogStyles.paddingTop) || 0;
            const paddingBottom = parseInt(dialogStyles.paddingBottom) || 0;
            const totalPadding = paddingTop + paddingBottom;

            const theoreticalHeight = totalContentHeight + totalPadding;
            const actualHeight = dialogRect.height;
            const wastedSpace = actualHeight - theoreticalHeight;

            return {
                dialogWidth: dialogRect.width,
                dialogHeight: actualHeight,
                paddingTop,
                paddingBottom,
                totalPadding,
                elements,
                totalContentHeight,
                theoreticalHeight,
                wastedSpace,
                classes: dialog.className
            };
        });

        console.log('\n📐 ANÁLISIS COMPLETO DEL MODAL DE OPERADOR:');
        console.log(`   📏 Dimensiones: ${analysis.dialogWidth}x${analysis.dialogHeight}px`);
        console.log(`   🎨 Clases: ${analysis.classes}`);
        console.log(`   📦 Padding: ${analysis.paddingTop}px (top) + ${analysis.paddingBottom}px (bottom) = ${analysis.totalPadding}px`);

        console.log('\n📊 ELEMENTOS INTERNOS:');
        analysis.elements.forEach(el => {
            console.log(`   [${el.index}] ${el.tagName}: ${el.height}px + margins(${el.marginTop}+${el.marginBottom}) = ${el.totalHeight}px`);
            if (el.className) console.log(`       Classes: ${el.className}`);
        });

        console.log('\n🔍 CÁLCULOS:');
        console.log(`   📝 Contenido total: ${analysis.totalContentHeight}px`);
        console.log(`   📦 Padding total: ${analysis.totalPadding}px`);
        console.log(`   📐 Altura teórica: ${analysis.theoreticalHeight}px`);
        console.log(`   📏 Altura actual: ${analysis.dialogHeight}px`);
        console.log(`   ⚠️  ESPACIO SOBRANTE: ${analysis.wastedSpace}px`);

        if (analysis.wastedSpace > 10) {
            console.log('\n💡 ACCIÓN REQUERIDA: Hay demasiado espacio en blanco');

            // Proponer nueva altura
            const newHeight = analysis.theoreticalHeight + 10; // Agregamos 10px de espacio mínimo
            console.log(`   ✨ Altura sugerida: ${newHeight}px (ahorro de ${analysis.wastedSpace - 10}px)`);
        }

        // Screenshot para análisis visual
        await page.screenshot({
            path: 'modal-operador-analysis.png',
            fullPage: false
        });
        console.log('\n📸 Screenshot guardado como modal-operador-analysis.png');

        await page.waitForTimeout(3000);

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
    }
})();
