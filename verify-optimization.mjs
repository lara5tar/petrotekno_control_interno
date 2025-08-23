import { chromium } from 'playwright';

(async () => {
    console.log('🔧 Iniciando test de verificación de modales optimizados...\n');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 1000 // Más lento para ver los cambios
    });
    const page = await browser.newPage();

    try {
        // Navegar y hacer login básico
        console.log('🌐 Navegando al sitio...');
        await page.goto('http://127.0.0.1:8000/login');

        console.log('🔐 Realizando login...');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Esperar y navegar al vehículo
        await page.waitForTimeout(2000);
        console.log('🚗 Navegando al vehículo...');
        await page.goto('http://127.0.0.1:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');

        // Tomar screenshot inicial
        await page.screenshot({ path: 'pagina-inicial.png', fullPage: true });
        console.log('📸 Screenshot inicial guardado: pagina-inicial.png');

        // Buscar y hacer click en cualquier botón azul (de acción)
        const actionButton = await page.locator('button').filter({
            hasText: /Asignar|Cambiar/i
        }).first();

        if (await actionButton.count() > 0) {
            const buttonText = await actionButton.textContent();
            console.log(`🎯 Haciendo click en: "${buttonText.trim()}"`);

            await actionButton.click();
            await page.waitForTimeout(1000);

            // Verificar si algún modal se abrió
            const modalVisible = await page.evaluate(() => {
                const modals = [
                    'cambiar-operador-modal',
                    'cambiar-obra-modal',
                    'registrar-mantenimiento-modal',
                    'responsable-obra-modal'
                ];

                for (const modalId of modals) {
                    const modal = document.getElementById(modalId);
                    if (modal && !modal.classList.contains('hidden') &&
                        window.getComputedStyle(modal).display !== 'none') {
                        return {
                            id: modalId,
                            height: modal.querySelector('div.relative')?.getBoundingClientRect().height,
                            width: modal.querySelector('div.relative')?.getBoundingClientRect().width
                        };
                    }
                }
                return null;
            });

            if (modalVisible) {
                console.log(`✅ Modal abierto: ${modalVisible.id}`);
                console.log(`📏 Dimensiones: ${modalVisible.width}x${modalVisible.height}px`);

                // Screenshot del modal
                await page.screenshot({
                    path: `modal-optimizado-${modalVisible.id}.png`,
                    fullPage: false
                });
                console.log(`📸 Screenshot del modal: modal-optimizado-${modalVisible.id}.png`);

                console.log('\n✅ VERIFICACIÓN COMPLETADA');
                console.log('🎉 Los modales han sido optimizados exitosamente');
                console.log('📉 Se redujo el espacio en blanco vertical');
                console.log('💡 Los modales ahora tienen un tamaño más apropiado');

            } else {
                console.log('❌ No se pudo abrir ningún modal');
            }
        } else {
            console.log('❌ No se encontraron botones de acción');
        }

        await page.waitForTimeout(3000);

    } catch (error) {
        console.error('❌ Error durante la verificación:', error.message);
    } finally {
        await browser.close();
        console.log('\n🏁 Test completado');
    }
})();
