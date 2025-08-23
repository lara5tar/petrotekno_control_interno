import { chromium } from 'playwright';

console.log('🎯 TEST DIRECTO - Vista vehículo ID 1');

const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();

try {
    console.log('🔐 Login...');
    await page.goto('http://127.0.0.1:8000/login');

    // Login con manejo de errores
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');

    await Promise.all([
        page.waitForLoadState('domcontentloaded'),
        page.click('button[type="submit"]')
    ]);

    // Esperar un poco y luego ir directamente al vehículo
    await page.waitForTimeout(2000);

    console.log('🚗 Accediendo directamente a vehículo ID 1...');
    await page.goto('http://127.0.0.1:8000/vehiculos/1');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(3000); // Dar tiempo para que cargue completamente

    console.log('✅ En vista de vehículo, URL:', page.url());

    // Verificar el estado inicial de los modales
    console.log('\n🔍 VERIFICANDO MODALES AL CARGAR LA PÁGINA:');

    const modales = [
        'cambiar-operador-modal',
        'cambiar-obra-modal',
        'registrar-mantenimiento-modal',
        'responsable-obra-modal'
    ];

    let modalProblematico = null;

    for (const modalId of modales) {
        try {
            const modal = page.locator(`#${modalId}`);
            const existe = await modal.count() > 0;

            if (existe) {
                // Verificar múltiples propiedades
                const [isVisible, styles, classes] = await Promise.all([
                    modal.isVisible(),
                    modal.evaluate(el => {
                        const computed = window.getComputedStyle(el);
                        return {
                            display: computed.display,
                            visibility: computed.visibility,
                            opacity: computed.opacity,
                            zIndex: computed.zIndex
                        };
                    }),
                    modal.getAttribute('class')
                ]);

                console.log(`\n   📋 ${modalId}:`);
                console.log(`      ✓ Existe: SÍ`);
                console.log(`      ✓ Visible: ${isVisible ? '🔴 SÍ - PROBLEMA!' : '✅ NO'}`);
                console.log(`      ✓ Display: ${styles.display}`);
                console.log(`      ✓ Visibility: ${styles.visibility}`);
                console.log(`      ✓ Opacity: ${styles.opacity}`);
                console.log(`      ✓ Classes: ${classes}`);

                if (isVisible) {
                    modalProblematico = modalId;
                }
            } else {
                console.log(`\n   📋 ${modalId}: ❓ No existe en DOM`);
            }
        } catch (error) {
            console.log(`\n   📋 ${modalId}: ⚠️ Error verificando: ${error.message}`);
        }
    }

    // Screenshot del estado actual
    await page.screenshot({ path: 'estado-modales-inicial.png', fullPage: true });
    console.log('\n📸 Screenshot: estado-modales-inicial.png');

    // Si hay un modal problemático, intentar arreglarlo
    if (modalProblematico) {
        console.log(`\n🔧 INTENTANDO ARREGLAR MODAL PROBLEMÁTICO: ${modalProblematico}`);

        try {
            // Forzar que se oculte
            await page.evaluate((modalId) => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    console.log(`Modal ${modalId} forzado a oculto`);
                }
            }, modalProblematico);

            await page.waitForTimeout(1000);

            // Verificar si se ocultó
            const modal = page.locator(`#${modalProblematico}`);
            const isVisibleAfter = await modal.isVisible();
            console.log(`   Resultado: ${isVisibleAfter ? '❌ Sigue visible' : '✅ Ocultado exitosamente'}`);

        } catch (error) {
            console.log(`   ❌ Error forzando ocultación: ${error.message}`);
        }
    }

    // Verificar que los botones funcionan correctamente
    console.log('\n🧪 PROBANDO FUNCIONALIDAD DE BOTONES:');

    try {
        // Buscar botón "Asignar Operador"
        const asignarBtn = page.locator('button').filter({ hasText: 'Asignar Operador' });
        const btnCount = await asignarBtn.count();

        console.log(`   Botón "Asignar Operador": ${btnCount > 0 ? '✅ Encontrado' : '❌ No encontrado'}`);

        if (btnCount > 0) {
            console.log('   🖱️ Haciendo click en "Asignar Operador"...');
            await asignarBtn.click();
            await page.waitForTimeout(1000);

            // Verificar si el modal se abre
            const modal = page.locator('#cambiar-operador-modal');
            const modalAbierto = await modal.isVisible();

            console.log(`   Resultado: ${modalAbierto ? '✅ Modal se abre correctamente' : '❌ Modal no se abre'}`);

            if (modalAbierto) {
                // Cerrar el modal
                const cerrarBtn = modal.locator('button').filter({ hasText: 'Cancelar' });
                if (await cerrarBtn.count() > 0) {
                    await cerrarBtn.click();
                    await page.waitForTimeout(500);
                    console.log('   ✅ Modal cerrado');
                }
            }
        }
    } catch (error) {
        console.log(`   ⚠️ Error probando botones: ${error.message}`);
    }

    // Screenshot final
    await page.screenshot({ path: 'vista-vehiculo-test-final.png', fullPage: true });
    console.log('\n📸 Screenshot final: vista-vehiculo-test-final.png');

    // Resultado final
    console.log('\n🏁 RESULTADO FINAL:');
    if (modalProblematico) {
        console.log('🔧 ESTADO: Hay modales que se abren automáticamente');
        console.log(`   Modal problemático: ${modalProblematico}`);
        console.log('   🚨 Necesita más ajustes en el CSS/JavaScript');
    } else {
        console.log('🎉 ¡ÉXITO! No hay modales abriéndose automáticamente');
        console.log('   ✅ La vista de vehículo funciona correctamente');
    }

} catch (error) {
    console.error('💥 Error:', error.message);
    await page.screenshot({ path: 'error-test-final.png' });
    console.log('📸 Error screenshot: error-test-final.png');
} finally {
    await browser.close();
}

console.log('\n🏁 Test completado');
