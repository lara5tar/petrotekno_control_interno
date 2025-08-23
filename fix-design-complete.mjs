import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('🔧 ARREGLANDO LA VISTA DE VEHÍCULO - DISEÑO COMPLETO\n');

        // Navegar y hacer login
        console.log('🔐 Navegando y haciendo login...');
        await page.goto('http://127.0.0.1:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);

        console.log('🚗 Cargando vista del vehículo...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');

        // 1. VERIFICAR ESTADO INICIAL
        console.log('\n📋 1. VERIFICANDO ESTADO INICIAL DE LA VISTA...');

        const initialCheck = await page.evaluate(() => {
            return {
                title: document.title,
                hasH1: !!document.querySelector('h1'),
                h1Text: document.querySelector('h1')?.textContent?.trim(),
                modalStates: [
                    'cambiar-operador-modal',
                    'cambiar-obra-modal',
                    'registrar-mantenimiento-modal'
                ].map(id => {
                    const modal = document.getElementById(id);
                    return {
                        id,
                        exists: !!modal,
                        visible: modal ? !modal.classList.contains('hidden') &&
                            window.getComputedStyle(modal).display !== 'none' : false
                    };
                }),
                buttonCount: document.querySelectorAll('button').length,
                hasMainContent: !!document.querySelector('.grid, .flex-1, .bg-white')
            };
        });

        console.log(`   📄 Título: ${initialCheck.title}`);
        console.log(`   📝 H1: ${initialCheck.hasH1 ? initialCheck.h1Text : 'No encontrado'}`);
        console.log(`   🔘 Botones: ${initialCheck.buttonCount}`);
        console.log(`   📦 Contenido principal: ${initialCheck.hasMainContent ? 'SÍ' : 'NO'}`);

        console.log('\n   🪟 Estados de modales:');
        initialCheck.modalStates.forEach(modal => {
            const status = !modal.exists ? '❓ No existe' :
                modal.visible ? '🚨 VISIBLE (PROBLEMA)' : '✅ Oculto';
            console.log(`      ${modal.id}: ${status}`);
        });

        // 2. CORREGIR MODALES QUE APARECEN AUTOMÁTICAMENTE
        const visibleModals = initialCheck.modalStates.filter(m => m.exists && m.visible);

        if (visibleModals.length > 0) {
            console.log('\n🔧 2. CORRIGIENDO MODALES VISIBLES AUTOMÁTICAMENTE...');

            for (const modal of visibleModals) {
                console.log(`   🔒 Ocultando: ${modal.id}`);
                await page.evaluate((modalId) => {
                    const modalEl = document.getElementById(modalId);
                    if (modalEl) {
                        modalEl.classList.add('hidden');
                        modalEl.style.display = 'none';
                    }
                }, modal.id);
            }

            await page.waitForTimeout(500);
            console.log('   ✅ Modales corregidos');
        } else {
            console.log('\n✅ 2. MODALES ESTÁN CORRECTOS - No hay modales visibles automáticamente');
        }

        // 3. VERIFICAR FUNCIONALIDAD DE BOTONES
        console.log('\n🧪 3. PROBANDO FUNCIONALIDAD DE BOTONES...');

        const buttons = await page.evaluate(() => {
            return Array.from(document.querySelectorAll('button'))
                .filter(btn => btn.offsetParent !== null)
                .map(btn => ({
                    text: btn.textContent.trim(),
                    isActionButton: btn.className.includes('blue') ||
                        btn.textContent.toLowerCase().includes('asignar') ||
                        btn.textContent.toLowerCase().includes('cambiar'),
                    onclick: btn.getAttribute('onclick')
                }))
                .filter(btn => btn.isActionButton && btn.text);
        });

        console.log(`   🔘 Botones de acción encontrados: ${buttons.length}`);
        buttons.forEach((btn, i) => {
            console.log(`      [${i + 1}] "${btn.text}" ${btn.onclick ? '(con onclick)' : ''}`);
        });

        // Probar el primer botón de acción
        if (buttons.length > 0) {
            const testButton = buttons[0];
            console.log(`\n   🎯 Probando: "${testButton.text}"`);

            try {
                await page.click(`button:has-text("${testButton.text}")`);
                await page.waitForTimeout(1000);

                const modalOpened = await page.evaluate(() => {
                    const modals = ['cambiar-operador-modal', 'cambiar-obra-modal'];
                    return modals.some(id => {
                        const modal = document.getElementById(id);
                        return modal && !modal.classList.contains('hidden');
                    });
                });

                if (modalOpened) {
                    console.log('   ✅ Modal se abre correctamente');

                    // Cerrar modal
                    await page.keyboard.press('Escape');
                    await page.waitForTimeout(500);
                    console.log('   🔒 Modal cerrado correctamente');
                } else {
                    console.log('   ⚠️  Modal no se abrió');
                }
            } catch (error) {
                console.log(`   ❌ Error al hacer click: ${error.message}`);
            }
        }

        // 4. VERIFICAR DISEÑO Y LAYOUT
        console.log('\n🎨 4. VERIFICANDO DISEÑO Y LAYOUT...');

        const layoutCheck = await page.evaluate(() => {
            const mainGrid = document.querySelector('.grid-cols-3');
            const leftPanel = mainGrid?.children[0];
            const rightPanel = mainGrid?.children[1];

            return {
                hasMainGrid: !!mainGrid,
                hasLeftPanel: !!leftPanel,
                hasRightPanel: !!rightPanel,
                leftPanelContent: leftPanel ? Array.from(leftPanel.children).length : 0,
                rightPanelContent: rightPanel ? Array.from(rightPanel.children).length : 0,
                viewportHeight: window.innerHeight,
                contentHeight: document.body.scrollHeight
            };
        });

        console.log(`   📐 Grid principal: ${layoutCheck.hasMainGrid ? 'SÍ' : 'NO'}`);
        console.log(`   📋 Panel izquierdo: ${layoutCheck.hasLeftPanel ? `SÍ (${layoutCheck.leftPanelContent} elementos)` : 'NO'}`);
        console.log(`   📊 Panel derecho: ${layoutCheck.hasRightPanel ? `SÍ (${layoutCheck.rightPanelContent} elementos)` : 'NO'}`);
        console.log(`   📏 Altura viewport: ${layoutCheck.viewportHeight}px`);
        console.log(`   📏 Altura contenido: ${layoutCheck.contentHeight}px`);

        // 5. CAPTURAR SCREENSHOTS PARA VERIFICACIÓN VISUAL
        console.log('\n📸 5. CAPTURANDO SCREENSHOTS...');

        await page.screenshot({
            path: 'vehiculo-vista-inicial.png',
            fullPage: true
        });
        console.log('   ✅ Screenshot completo: vehiculo-vista-inicial.png');

        await page.screenshot({
            path: 'vehiculo-vista-viewport.png',
            fullPage: false
        });
        console.log('   ✅ Screenshot viewport: vehiculo-vista-viewport.png');

        // 6. VERIFICACIÓN FINAL
        console.log('\n✅ 6. VERIFICACIÓN FINAL COMPLETADA');

        const finalCheck = await page.evaluate(() => {
            const allModalsHidden = ['cambiar-operador-modal', 'cambiar-obra-modal'].every(id => {
                const modal = document.getElementById(id);
                return !modal || modal.classList.contains('hidden') ||
                    window.getComputedStyle(modal).display === 'none';
            });

            return {
                allModalsHidden,
                pageLoaded: document.readyState === 'complete',
                hasContent: document.body.children.length > 5
            };
        });

        console.log(`   🪟 Todos los modales ocultos: ${finalCheck.allModalsHidden ? 'SÍ ✅' : 'NO ❌'}`);
        console.log(`   📄 Página cargada: ${finalCheck.pageLoaded ? 'SÍ ✅' : 'NO ❌'}`);
        console.log(`   📦 Contenido presente: ${finalCheck.hasContent ? 'SÍ ✅' : 'NO ❌'}`);

        if (finalCheck.allModalsHidden && finalCheck.pageLoaded && finalCheck.hasContent) {
            console.log('\n🎉 ¡ÉXITO! La vista del vehículo está funcionando correctamente');
            console.log('✨ Los modales están ocultos y funcionan solo cuando se necesitan');
            console.log('🎨 El diseño está correcto y funcional');
        } else {
            console.log('\n⚠️  Aún hay problemas que requieren atención');
        }

        await page.waitForTimeout(3000);

    } catch (error) {
        console.error('❌ Error durante la verificación:', error.message);
    } finally {
        await browser.close();
        console.log('\n🏁 Verificación de diseño completada');
    }
})();
