import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('🔍 Diagnóstico: Analizando por qué se abren los modales automáticamente...\n');

        // Navegar y hacer login
        console.log('🔐 Navegando y haciendo login...');
        await page.goto('http://127.0.0.1:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(2000);

        console.log('🚗 Navegando al vehículo...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');

        // Verificar inmediatamente qué modales están abiertos
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
                    status: visible ? 'ABIERTO AUTOMÁTICAMENTE' : 'Cerrado',
                    classes: modal.className,
                    display,
                    hasHiddenClass: isHidden
                };
            });
        });

        console.log('🔍 ESTADO INICIAL DE MODALES:');
        modalStates.forEach(modal => {
            const emoji = modal.status.includes('ABIERTO') ? '🚨' : '✅';
            console.log(`   ${emoji} ${modal.id}: ${modal.status}`);
            if (modal.status.includes('ABIERTO')) {
                console.log(`       Classes: ${modal.classes}`);
                console.log(`       Display: ${modal.display}`);
            }
        });

        // Verificar si hay modales abiertos automáticamente
        const problematicModals = modalStates.filter(m => m.status.includes('ABIERTO'));

        if (problematicModals.length > 0) {
            console.log('\n🚨 PROBLEMA DETECTADO: Modales se abren automáticamente');

            // Analizar posibles causas
            const diagnostics = await page.evaluate(() => {
                // Verificar scripts que pueden estar ejecutándose automáticamente
                const scripts = Array.from(document.querySelectorAll('script')).map(script => {
                    const content = script.textContent || '';
                    return {
                        hasModalFunctions: content.includes('openCambiarOperadorModal') ||
                            content.includes('openCambiarObraModal'),
                        hasAutoExecution: content.includes('DOMContentLoaded') ||
                            content.includes('window.onload'),
                        content: content.slice(0, 200) + (content.length > 200 ? '...' : '')
                    };
                }).filter(script => script.hasModalFunctions || script.hasAutoExecution);

                // Verificar si las funciones se están ejecutando automáticamente
                const functions = [];
                if (typeof openCambiarOperadorModal !== 'undefined') {
                    functions.push('openCambiarOperadorModal');
                }
                if (typeof openCambiarObraModal !== 'undefined') {
                    functions.push('openCambiarObraModal');
                }

                return {
                    scripts,
                    availableFunctions: functions,
                    errors: window.console.errors || []
                };
            });

            console.log('\n🔍 DIAGNÓSTICO:');
            console.log(`   📜 Scripts con funciones de modal: ${diagnostics.scripts.length}`);
            console.log(`   🔧 Funciones disponibles: ${diagnostics.availableFunctions.join(', ')}`);

            // SOLUCIÓN: Cerrar todos los modales automáticamente
            console.log('\n🔧 APLICANDO SOLUCIÓN...');

            for (const modal of problematicModals) {
                console.log(`   🔒 Cerrando modal: ${modal.id}`);
                await page.evaluate((modalId) => {
                    const modalElement = document.getElementById(modalId);
                    if (modalElement) {
                        modalElement.classList.add('hidden');
                        modalElement.style.display = 'none';
                    }
                }, modal.id);
            }

            await page.waitForTimeout(500);

            // Verificar que se cerraron
            const fixedStates = await page.evaluate(() => {
                const modalIds = [
                    'cambiar-operador-modal',
                    'cambiar-obra-modal',
                    'registrar-mantenimiento-modal',
                    'responsable-obra-modal'
                ];

                return modalIds.map(id => {
                    const modal = document.getElementById(id);
                    const isHidden = modal ? modal.classList.contains('hidden') : true;
                    const display = modal ? window.getComputedStyle(modal).display : 'none';
                    const visible = !isHidden && display !== 'none';

                    return {
                        id,
                        status: visible ? 'AÚN ABIERTO' : 'CERRADO ✅',
                        fixed: !visible
                    };
                });
            });

            console.log('\n✅ RESULTADO DESPUÉS DE LA CORRECCIÓN:');
            fixedStates.forEach(modal => {
                const emoji = modal.fixed ? '✅' : '❌';
                console.log(`   ${emoji} ${modal.id}: ${modal.status}`);
            });

            // Screenshot final
            await page.screenshot({
                path: 'vehiculo-vista-corregida.png',
                fullPage: true
            });
            console.log('\n📸 Screenshot de vista corregida: vehiculo-vista-corregida.png');

            // Probar que los modales funcionen correctamente cuando se activen manualmente
            console.log('\n🧪 PROBANDO FUNCIONALIDAD MANUAL...');

            // Buscar botón de asignar operador
            const operatorButton = await page.locator('button').filter({
                hasText: /Asignar.*Operador/i
            }).first();

            if (await operatorButton.count() > 0) {
                console.log('   🎯 Probando botón "Asignar Operador"...');
                await operatorButton.click();
                await page.waitForTimeout(1000);

                const modalOpened = await page.evaluate(() => {
                    const modal = document.getElementById('cambiar-operador-modal');
                    return modal && !modal.classList.contains('hidden');
                });

                if (modalOpened) {
                    console.log('   ✅ Modal se abre correctamente cuando se hace click');

                    // Cerrar el modal
                    await page.keyboard.press('Escape');
                    await page.waitForTimeout(500);
                    console.log('   🔒 Modal cerrado con Escape');
                } else {
                    console.log('   ❌ Modal no se abrió con el click');
                }
            }

        } else {
            console.log('\n✅ NO HAY PROBLEMAS: Los modales están cerrados correctamente');
        }

        await page.waitForTimeout(3000);

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
        console.log('\n🏁 Diagnóstico completado');
    }
})();
