import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        // Ir directo al login
        console.log('🔐 Accediendo al login...');
        await page.goto('http://127.0.0.1:8000/login');
        await page.waitForSelector('input[name="email"]');

        // Login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Esperar redirección
        await page.waitForURL(/\/home/, { timeout: 10000 });
        console.log('✅ Login exitoso');

        // Navegar al vehículo específico
        console.log('🚗 Navegando al vehículo...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1', { waitUntil: 'networkidle' });

        // Verificar que estamos en la página correcta
        const title = await page.title();
        console.log(`📄 Título: ${title}`);

        // Buscar botones azules (que suelen ser los de acción)
        const actionButtons = await page.evaluate(() => {
            const buttons = Array.from(document.querySelectorAll('button'));
            return buttons
                .filter(btn => btn.offsetParent !== null) // Solo visibles
                .map(btn => ({
                    text: btn.textContent.trim(),
                    classes: btn.className,
                    onclick: btn.getAttribute('onclick'),
                    isBlue: btn.className.includes('blue')
                }))
                .filter(btn => btn.text && (btn.isBlue || btn.onclick));
        });

        console.log('\n🔘 BOTONES DE ACCIÓN ENCONTRADOS:');
        actionButtons.forEach((btn, i) => {
            console.log(`   [${i}] "${btn.text}" ${btn.isBlue ? '🔵' : ''}`);
            if (btn.onclick) console.log(`       onclick: ${btn.onclick}`);
        });

        // Buscar específicamente el botón de Asignar Operador
        const operatorButtonFound = actionButtons.find(btn =>
            btn.text.toLowerCase().includes('operador') ||
            btn.text.toLowerCase().includes('asignar')
        );

        if (operatorButtonFound) {
            console.log(`\n🎯 Encontrado: "${operatorButtonFound.text}"`);

            // Hacer click en el botón
            await page.click(`button:has-text("${operatorButtonFound.text}")`);
            console.log('✅ Click realizado');

            // Esperar un momento para que aparezca el modal
            await page.waitForTimeout(1000);

            // Verificar todos los modales posibles
            const modalStates = await page.evaluate(() => {
                const modalIds = [
                    'cambiar-operador-modal',
                    'cambiar-obra-modal',
                    'registrar-mantenimiento-modal',
                    'responsable-obra-modal',
                    'kilometraje-modal'
                ];

                return modalIds.map(id => {
                    const modal = document.getElementById(id);
                    if (!modal) return { id, status: 'No existe' };

                    const isHidden = modal.classList.contains('hidden');
                    const display = window.getComputedStyle(modal).display;
                    const visible = !isHidden && display !== 'none';

                    return {
                        id,
                        status: visible ? 'ABIERTO' : 'Cerrado',
                        classes: modal.className,
                        display
                    };
                });
            });

            console.log('\n🪟 ESTADO DE MODALES:');
            modalStates.forEach(modal => {
                const status = modal.status === 'ABIERTO' ? '✅ ABIERTO' : `❌ ${modal.status}`;
                console.log(`   ${modal.id}: ${status}`);
            });

            // Encontrar el modal abierto
            const openModal = modalStates.find(m => m.status === 'ABIERTO');

            if (openModal) {
                console.log(`\n📐 ANALIZANDO MODAL: ${openModal.id}`);

                // Hacer análisis detallado del modal abierto
                const spacing = await page.evaluate((modalId) => {
                    const modal = document.getElementById(modalId);
                    const dialog = modal.querySelector('div.relative');

                    if (!dialog) return { error: 'Dialog container not found' };

                    // Información básica del dialog
                    const dialogRect = dialog.getBoundingClientRect();
                    const dialogStyles = window.getComputedStyle(dialog);

                    // Análisis de cada elemento hijo
                    const elements = Array.from(dialog.children).map((child, index) => {
                        const rect = child.getBoundingClientRect();
                        const styles = window.getComputedStyle(child);

                        return {
                            index,
                            tag: child.tagName,
                            height: rect.height,
                            marginTop: parseInt(styles.marginTop) || 0,
                            marginBottom: parseInt(styles.marginBottom) || 0,
                            paddingTop: parseInt(styles.paddingTop) || 0,
                            paddingBottom: parseInt(styles.paddingBottom) || 0,
                            className: child.className
                        };
                    });

                    // Calcular espacio total usado
                    const contentHeight = elements.reduce((sum, el) =>
                        sum + el.height + el.marginTop + el.marginBottom, 0);

                    const dialogPadding = (parseInt(dialogStyles.paddingTop) || 0) +
                        (parseInt(dialogStyles.paddingBottom) || 0);

                    const totalNeeded = contentHeight + dialogPadding;
                    const currentHeight = dialogRect.height;
                    const wastedSpace = currentHeight - totalNeeded;

                    return {
                        currentHeight,
                        currentWidth: dialogRect.width,
                        dialogPadding,
                        contentHeight,
                        totalNeeded,
                        wastedSpace,
                        elements,
                        classes: dialog.className
                    };
                }, openModal.id);

                if (spacing.error) {
                    console.log(`❌ Error: ${spacing.error}`);
                } else {
                    console.log('\n📊 ANÁLISIS DE ESPACIADO:');
                    console.log(`   📏 Tamaño actual: ${spacing.currentWidth}x${spacing.currentHeight}px`);
                    console.log(`   📦 Padding del dialog: ${spacing.dialogPadding}px`);
                    console.log(`   📝 Altura del contenido: ${spacing.contentHeight}px`);
                    console.log(`   📐 Espacio necesario: ${spacing.totalNeeded}px`);
                    console.log(`   ⚠️  ESPACIO DESPERDICIADO: ${spacing.wastedSpace}px`);

                    console.log('\n📋 ELEMENTOS INTERNOS:');
                    spacing.elements.forEach(el => {
                        const totalEl = el.height + el.marginTop + el.marginBottom + el.paddingTop + el.paddingBottom;
                        console.log(`   [${el.index}] ${el.tag}: ${el.height}px + spacing = ${totalEl}px`);
                    });

                    if (spacing.wastedSpace > 15) {
                        console.log('\n🚨 OPTIMIZACIÓN NECESARIA');
                        console.log(`   💡 Reducir altura en: ${Math.floor(spacing.wastedSpace - 10)}px`);
                        console.log(`   🎯 Altura óptima: ${spacing.totalNeeded + 10}px`);

                        // Realizar la optimización automáticamente
                        console.log('\n🔧 APLICANDO OPTIMIZACIÓN...');

                        const newHeight = spacing.totalNeeded + 10;
                        await page.evaluate((modalId, newHeight) => {
                            const modal = document.getElementById(modalId);
                            const dialog = modal.querySelector('div.relative');
                            dialog.style.minHeight = `${newHeight}px`;
                            dialog.style.height = 'auto';
                        }, openModal.id, newHeight);

                        console.log('✅ Optimización aplicada temporalmente');

                        // Screenshot del antes y después
                        await page.screenshot({
                            path: 'modal-optimized.png',
                            fullPage: false
                        });
                        console.log('📸 Screenshot optimizado: modal-optimized.png');
                    }
                }

                await page.waitForTimeout(5000);
            } else {
                console.log('❌ No se abrió ningún modal');
            }

        } else {
            console.log('❌ No se encontró botón de operador');
        }

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
    }
})();
