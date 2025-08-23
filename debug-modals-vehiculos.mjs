import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        // Navegar a la página de vehículos
        console.log('🔍 Navegando a la página de vehículos...');
        await page.goto('http://localhost:8000/vehiculos');
        await page.waitForLoadState('networkidle');

        // Buscar y hacer clic en el primer vehículo para ver detalles
        console.log('📋 Buscando vehículo para ver detalles...');
        const vehiculoLink = page.locator('a[href*="/vehiculos/"]').first();
        if (await vehiculoLink.count() > 0) {
            await vehiculoLink.click();
            await page.waitForLoadState('networkidle');
            console.log('✅ Navegado a página de detalles del vehículo');
        } else {
            console.log('❌ No se encontraron vehículos');
            return;
        }

        // Verificar que estamos en la página correcta
        await page.waitForSelector('h1', { timeout: 5000 });
        const titulo = await page.locator('h1').textContent();
        console.log(`📄 Página actual: ${titulo}`);

        // 1. PROBAR BOTÓN "ASIGNAR/CAMBIAR OPERADOR"
        console.log('\n🧪 PROBANDO BOTÓN "ASIGNAR/CAMBIAR OPERADOR"...');

        // Buscar el botón de asignar/cambiar operador
        const botonOperador = page.locator('button').filter({ hasText: /Asignar Operador|Cambiar Operador/ });
        const countOperador = await botonOperador.count();
        console.log(`🔍 Botones de operador encontrados: ${countOperador}`);

        if (countOperador > 0) {
            const textoBoton = await botonOperador.first().textContent();
            console.log(`📝 Texto del botón: "${textoBoton}"`);

            // Verificar si el botón tiene onclick
            const onclickOperador = await botonOperador.first().getAttribute('onclick');
            console.log(`🔗 Atributo onclick: ${onclickOperador}`);

            // Verificar si está visible y habilitado
            const isVisible = await botonOperador.first().isVisible();
            const isEnabled = await botonOperador.first().isEnabled();
            console.log(`👁️ Visible: ${isVisible}, Habilitado: ${isEnabled}`);

            // Intentar hacer clic
            console.log('🖱️ Haciendo clic en botón de operador...');
            await botonOperador.first().click();

            // Esperar un momento para que aparezca el modal
            await page.waitForTimeout(1000);

            // Verificar si apareció el modal
            const modalOperador = page.locator('#cambiar-operador-modal');
            const modalVisible = await modalOperador.isVisible();
            console.log(`🪟 Modal de operador visible: ${modalVisible}`);

            if (modalVisible) {
                console.log('✅ Modal de operador se abrió correctamente');
                // Cerrar el modal
                const botonCerrar = modalOperador.locator('button').filter({ hasText: /Cancelar|×/ });
                if (await botonCerrar.count() > 0) {
                    await botonCerrar.first().click();
                    await page.waitForTimeout(500);
                }
            } else {
                console.log('❌ Modal de operador NO se abrió');

                // Verificar si existe el modal en el DOM
                const modalExists = await modalOperador.count() > 0;
                console.log(`🔍 Modal existe en DOM: ${modalExists}`);

                if (modalExists) {
                    const modalClass = await modalOperador.getAttribute('class');
                    console.log(`📝 Clases del modal: ${modalClass}`);
                }
            }
        } else {
            console.log('❌ No se encontró botón de asignar/cambiar operador');
        }

        // 2. PROBAR BOTÓN "CAMBIAR OBRA"
        console.log('\n🧪 PROBANDO BOTÓN "CAMBIAR OBRA"...');

        const botonObra = page.locator('button').filter({ hasText: /Cambiar Obra|Asignar Obra/ });
        const countObra = await botonObra.count();
        console.log(`🔍 Botones de obra encontrados: ${countObra}`);

        if (countObra > 0) {
            const textoBotonObra = await botonObra.first().textContent();
            console.log(`📝 Texto del botón: "${textoBotonObra}"`);

            const onclickObra = await botonObra.first().getAttribute('onclick');
            console.log(`🔗 Atributo onclick: ${onclickObra}`);

            const isVisibleObra = await botonObra.first().isVisible();
            const isEnabledObra = await botonObra.first().isEnabled();
            console.log(`👁️ Visible: ${isVisibleObra}, Habilitado: ${isEnabledObra}`);

            console.log('🖱️ Haciendo clic en botón de obra...');
            await botonObra.first().click();
            await page.waitForTimeout(1000);

            // Verificar si apareció algún modal de obra
            const modalsObra = [
                '#asignar-obra-modal',
                '#modal-cambiar-obra',
                '.modal'
            ];

            let modalObraVisible = false;
            for (const modalSelector of modalsObra) {
                const modal = page.locator(modalSelector);
                if (await modal.count() > 0 && await modal.isVisible()) {
                    modalObraVisible = true;
                    console.log(`✅ Modal de obra se abrió: ${modalSelector}`);
                    break;
                }
            }

            if (!modalObraVisible) {
                console.log('❌ Modal de obra NO se abrió');
            }
        } else {
            console.log('❌ No se encontró botón de cambiar obra');
        }

        // 3. PROBAR BOTÓN "REGISTRAR MANTENIMIENTO" (QUE SÍ FUNCIONA)
        console.log('\n🧪 PROBANDO BOTÓN "REGISTRAR MANTENIMIENTO" (FUNCIONAL)...');

        const botonMantenimiento = page.locator('button').filter({ hasText: /Registrar Mantenimiento/ });
        const countMantenimiento = await botonMantenimiento.count();
        console.log(`🔍 Botones de mantenimiento encontrados: ${countMantenimiento}`);

        if (countMantenimiento > 0) {
            const textoBotonMant = await botonMantenimiento.first().textContent();
            console.log(`📝 Texto del botón: "${textoBotonMant}"`);

            const onclickMant = await botonMantenimiento.first().getAttribute('onclick');
            console.log(`🔗 Atributo onclick: ${onclickMant}`);

            const isVisibleMant = await botonMantenimiento.first().isVisible();
            const isEnabledMant = await botonMantenimiento.first().isEnabled();
            console.log(`👁️ Visible: ${isVisibleMant}, Habilitado: ${isEnabledMant}`);

            console.log('🖱️ Haciendo clic en botón de mantenimiento...');
            await botonMantenimiento.first().click();
            await page.waitForTimeout(1000);

            const modalMantenimiento = page.locator('#registrar-mantenimiento-modal');
            const modalMantVisible = await modalMantenimiento.isVisible();
            console.log(`🪟 Modal de mantenimiento visible: ${modalMantVisible}`);

            if (modalMantVisible) {
                console.log('✅ Modal de mantenimiento se abrió correctamente');
                // Cerrar el modal
                const botonCerrarMant = modalMantenimiento.locator('button').filter({ hasText: /Cancelar|×/ });
                if (await botonCerrarMant.count() > 0) {
                    await botonCerrarMant.first().click();
                    await page.waitForTimeout(500);
                }
            }
        }

        // 4. REVISAR FUNCIONES JAVASCRIPT EN LA PÁGINA
        console.log('\n🔍 REVISANDO FUNCIONES JAVASCRIPT...');

        // Verificar si las funciones existen
        const funcionesJS = await page.evaluate(() => {
            return {
                openCambiarOperadorModal: typeof openCambiarOperadorModal !== 'undefined',
                openAsignarObraModal: typeof openAsignarObraModal !== 'undefined',
                openRegistrarMantenimientoModal: typeof openRegistrarMantenimientoModal !== 'undefined',
                showModal: typeof showModal !== 'undefined',
                hideModal: typeof hideModal !== 'undefined'
            };
        });

        console.log('📋 Funciones JavaScript disponibles:');
        Object.entries(funcionesJS).forEach(([func, exists]) => {
            console.log(`   ${func}: ${exists ? '✅' : '❌'}`);
        });

        // 5. REVISAR ERRORES EN CONSOLA
        console.log('\n🚨 ERRORES EN CONSOLA:');
        page.on('console', msg => {
            if (msg.type() === 'error') {
                console.log(`❌ Error JS: ${msg.text()}`);
            }
        });

        page.on('pageerror', error => {
            console.log(`❌ Error de página: ${error.message}`);
        });

        // Recargar la página para capturar errores
        await page.reload();
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        console.log('\n✅ Diagnóstico completado');

    } catch (error) {
        console.error('❌ Error durante el diagnóstico:', error);
    } finally {
        await browser.close();
    }
})();
