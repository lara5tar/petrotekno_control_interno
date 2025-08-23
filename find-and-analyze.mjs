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

        // Encontrar todos los botones disponibles
        const allButtons = await page.evaluate(() => {
            const buttons = Array.from(document.querySelectorAll('button'));
            return buttons.map(btn => ({
                text: btn.textContent.trim(),
                onclick: btn.getAttribute('onclick'),
                classes: btn.className,
                visible: btn.offsetParent !== null
            })).filter(btn => btn.visible && (btn.text || btn.onclick));
        });

        console.log('\n🔘 BOTONES DISPONIBLES:');
        allButtons.forEach((btn, i) => {
            console.log(`   [${i}] "${btn.text}" (onclick: ${btn.onclick})`);
        });

        // Buscar botón que contenga "Operador" o que tenga onclick con "operador"
        const operatorButton = allButtons.find(btn =>
            btn.text.toLowerCase().includes('operador') ||
            (btn.onclick && btn.onclick.includes('operador'))
        );

        if (operatorButton) {
            console.log(`\n🎯 Botón encontrado: "${operatorButton.text}"`);

            // Click en el botón
            await page.click(`button:has-text("${operatorButton.text}")`);
            console.log('🪟 Botón clickeado');

            // Esperar modal
            await page.waitForTimeout(1000);

            // Verificar qué modal se abrió
            const openModals = await page.evaluate(() => {
                const modals = [
                    '#cambiar-operador-modal',
                    '#cambiar-obra-modal',
                    '#registrar-mantenimiento-modal',
                    '#responsable-obra-modal'
                ];

                return modals.map(selector => {
                    const modal = document.querySelector(selector);
                    return {
                        selector,
                        exists: !!modal,
                        visible: modal && !modal.classList.contains('hidden'),
                        display: modal ? window.getComputedStyle(modal).display : 'none'
                    };
                }).filter(m => m.visible && m.display !== 'none');
            });

            console.log('\n🪟 MODALES ABIERTOS:');
            openModals.forEach(modal => {
                console.log(`   ✅ ${modal.selector}`);
            });

            if (openModals.length > 0) {
                const modalSelector = openModals[0].selector;
                console.log(`\n📐 Analizando: ${modalSelector}`);

                // Análisis del modal abierto
                const analysis = await page.evaluate((selector) => {
                    const modal = document.querySelector(selector);
                    const dialog = modal.querySelector('div.relative');

                    if (!dialog) return { error: 'Dialog no encontrado' };

                    const dialogRect = dialog.getBoundingClientRect();
                    const dialogStyles = window.getComputedStyle(dialog);

                    // Analizar cada elemento hijo
                    const children = Array.from(dialog.children);
                    let totalContentHeight = 0;
                    const elements = [];

                    children.forEach((child, index) => {
                        const rect = child.getBoundingClientRect();
                        const styles = window.getComputedStyle(child);

                        const marginTop = parseInt(styles.marginTop) || 0;
                        const marginBottom = parseInt(styles.marginBottom) || 0;
                        const elementTotal = rect.height + marginTop + marginBottom;

                        elements.push({
                            index,
                            tag: child.tagName,
                            className: child.className,
                            height: rect.height,
                            marginTop,
                            marginBottom,
                            total: elementTotal
                        });

                        totalContentHeight += elementTotal;
                    });

                    const paddingTop = parseInt(dialogStyles.paddingTop) || 0;
                    const paddingBottom = parseInt(dialogStyles.paddingBottom) || 0;
                    const totalPadding = paddingTop + paddingBottom;

                    const usedSpace = totalContentHeight + totalPadding;
                    const wastedSpace = dialogRect.height - usedSpace;

                    return {
                        selector,
                        dialogHeight: dialogRect.height,
                        dialogWidth: dialogRect.width,
                        paddingTop,
                        paddingBottom,
                        totalPadding,
                        elements,
                        totalContentHeight,
                        usedSpace,
                        wastedSpace,
                        classes: dialog.className
                    };
                }, modalSelector);

                if (analysis.error) {
                    console.log(`❌ ${analysis.error}`);
                } else {
                    console.log('\n📊 ANÁLISIS COMPLETO:');
                    console.log(`   📏 Dimensiones: ${analysis.dialogWidth}x${analysis.dialogHeight}px`);
                    console.log(`   🎨 Clases: ${analysis.classes}`);
                    console.log(`   📦 Padding: ${analysis.paddingTop}px + ${analysis.paddingBottom}px = ${analysis.totalPadding}px`);

                    console.log('\n📋 ELEMENTOS:');
                    analysis.elements.forEach(el => {
                        console.log(`   [${el.index}] ${el.tag}: ${el.height}px + margins(${el.marginTop}+${el.marginBottom}) = ${el.total}px`);
                        if (el.className) console.log(`       📝 ${el.className}`);
                    });

                    console.log('\n🔍 CÁLCULO FINAL:');
                    console.log(`   📝 Contenido: ${analysis.totalContentHeight}px`);
                    console.log(`   📦 Padding: ${analysis.totalPadding}px`);
                    console.log(`   📐 Usado: ${analysis.usedSpace}px`);
                    console.log(`   📏 Total: ${analysis.dialogHeight}px`);
                    console.log(`   ⚠️  DESPERDICIO: ${analysis.wastedSpace}px`);

                    if (analysis.wastedSpace > 20) {
                        console.log('\n🚨 ACCIÓN REQUERIDA: Demasiado espacio en blanco');
                        console.log(`   💡 Reducir en: ${Math.floor(analysis.wastedSpace - 10)}px`);
                        console.log(`   🎯 Altura ideal: ${analysis.usedSpace + 10}px`);
                    }
                }

                // Screenshot
                await page.screenshot({
                    path: 'modal-current-analysis.png',
                    fullPage: false
                });
                console.log('\n📸 Screenshot: modal-current-analysis.png');

                await page.waitForTimeout(5000);
            }

        } else {
            console.log('\n❌ No se encontró botón de operador');
        }

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
    }
})();
