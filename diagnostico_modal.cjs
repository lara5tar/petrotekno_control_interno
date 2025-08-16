const { chromium } = require('playwright');

(async () => {
    console.log('🔍 DIAGNÓSTICO: Modal de Mantenimientos');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 500
    });
    const page = await browser.newPage();

    // Habilitar logs de consola para ver errores de JavaScript
    page.on('console', msg => {
        console.log('🟡 CONSOLE:', msg.type(), msg.text());
    });

    page.on('pageerror', error => {
        console.log('🔴 PAGE ERROR:', error.message);
    });

    try {
        console.log('🚗 Navegando al vehículo 1...');
        await page.goto('http://127.0.0.1:8001/vehiculos/1');

        // Login si es necesario
        if (page.url().includes('/login')) {
            console.log('🔐 Haciendo login...');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'admin');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(3000);

            // Verificar si el login fue exitoso
            const currentUrl = page.url();
            console.log('📍 URL después del login:', currentUrl);

            if (currentUrl.includes('/login')) {
                console.log('❌ Login falló, intentando con credenciales alternativas...');

                // Probar con admin/admin
                await page.fill('input[name="email"]', 'admin@admin.com');
                await page.fill('input[name="password"]', 'admin');
                await page.click('button[type="submit"]');
                await page.waitForTimeout(3000);
            }

            // Navegar al vehículo otra vez
            console.log('🔄 Navegando al vehículo...');
            await page.goto('http://127.0.0.1:8001/vehiculos/1');
            await page.waitForTimeout(2000);
        }

        console.log('📍 URL final:', page.url());

        // Verificar que estamos en la página correcta
        const titulo = await page.locator('h1, h2, h3').first().textContent();
        console.log('📋 Título de la página:', titulo);

        // DIAGNÓSTICO 1: Verificar si Alpine.js está cargado
        console.log('\n🔍 DIAGNÓSTICO 1: Verificando Alpine.js...');
        const alpineLoaded = await page.evaluate(() => {
            return typeof window.Alpine !== 'undefined';
        });
        console.log('Alpine.js cargado:', alpineLoaded ? '✅' : '❌');

        // DIAGNÓSTICO 2: Verificar la estructura de pestañas
        console.log('\n🔍 DIAGNÓSTICO 2: Verificando pestañas...');
        const pestanas = await page.locator('button').all();
        console.log(`Número total de botones encontrados: ${pestanas.length}`);

        for (let i = 0; i < pestanas.length; i++) {
            const texto = await pestanas[i].textContent();
            if (texto && texto.toLowerCase().includes('mantenimiento')) {
                console.log(`✅ Pestaña encontrada: "${texto}"`);
            }
        }

        // DIAGNÓSTICO 3: Buscar específicamente la pestaña de mantenimientos
        console.log('\n🔍 DIAGNÓSTICO 3: Buscando pestaña de mantenimientos...');
        const tabMantenimientos = page.locator('button:has-text("Mantenimientos")');
        const tabCount = await tabMantenimientos.count();
        console.log(`Pestañas "Mantenimientos" encontradas: ${tabCount}`);

        if (tabCount > 0) {
            console.log('✅ Pestaña encontrada, haciendo click...');
            await tabMantenimientos.click();
            await page.waitForTimeout(1000);

            // DIAGNÓSTICO 4: Verificar que la pestaña se activó
            console.log('\n🔍 DIAGNÓSTICO 4: Verificando activación de pestaña...');
            const contenidoMantenimientos = page.locator('[x-show="activeTab === \'mantenimientos\'"]');
            const esVisible = await contenidoMantenimientos.isVisible();
            console.log(`Contenido de mantenimientos visible: ${esVisible ? '✅' : '❌'}`);

            if (esVisible) {
                // DIAGNÓSTICO 5: Verificar los botones "Registrar Mantenimiento"
                console.log('\n🔍 DIAGNÓSTICO 5: Verificando botones "Registrar Mantenimiento"...');
                const botonesRegistrar = page.locator('button:has-text("Registrar Mantenimiento")');
                const numBotones = await botonesRegistrar.count();
                console.log(`Botones "Registrar Mantenimiento" encontrados: ${numBotones}`);

                if (numBotones > 0) {
                    // Verificar atributos del botón
                    for (let i = 0; i < numBotones; i++) {
                        const boton = botonesRegistrar.nth(i);
                        const onclick = await boton.getAttribute('@click');
                        const xClick = await boton.getAttribute('x-on:click');
                        const classes = await boton.getAttribute('class');

                        console.log(`\n  Botón ${i + 1}:`);
                        console.log(`    @click: ${onclick || 'NO'}`);
                        console.log(`    x-on:click: ${xClick || 'NO'}`);
                        console.log(`    classes: ${classes}`);
                    }

                    // DIAGNÓSTICO 6: Verificar el x-data del contenedor
                    console.log('\n🔍 DIAGNÓSTICO 6: Verificando x-data...');
                    const contenedorData = await page.evaluate(() => {
                        const elementos = document.querySelectorAll('[x-data]');
                        const dataInfo = [];
                        elementos.forEach((el, index) => {
                            const xData = el.getAttribute('x-data');
                            if (xData && xData.includes('modalMantenimiento')) {
                                dataInfo.push({ index, xData: xData.substring(0, 100) + '...' });
                            }
                        });
                        return dataInfo;
                    });

                    console.log('Elementos con modalMantenimiento en x-data:', contenedorData.length);
                    contenedorData.forEach(info => {
                        console.log(`  Elemento ${info.index}: ${info.xData}`);
                    });

                    // DIAGNÓSTICO 7: Verificar estado inicial de modalMantenimiento
                    console.log('\n🔍 DIAGNÓSTICO 7: Verificando estado de modalMantenimiento...');
                    const modalState = await page.evaluate(() => {
                        // Buscar el elemento que tiene Alpine.js
                        const alpineElements = document.querySelectorAll('[x-data]');
                        for (let el of alpineElements) {
                            if (el._x_dataStack && el._x_dataStack[0] && 'modalMantenimiento' in el._x_dataStack[0]) {
                                return el._x_dataStack[0].modalMantenimiento;
                            }
                        }
                        return 'NO_ENCONTRADO';
                    });
                    console.log('Estado inicial de modalMantenimiento:', modalState);

                    // DIAGNÓSTICO 8: Intentar hacer click y monitorear cambios
                    console.log('\n🔍 DIAGNÓSTICO 8: Haciendo click en el botón...');
                    const primerBoton = botonesRegistrar.first();

                    // Verificar si el modal existe antes del click
                    const modalAntes = page.locator('[x-show="modalMantenimiento"]');
                    const modalExisteAntes = await modalAntes.count();
                    console.log(`Modal existe antes del click: ${modalExisteAntes > 0 ? '✅' : '❌'}`);

                    await primerBoton.click();
                    await page.waitForTimeout(1000);

                    // Verificar estado después del click
                    const modalStateAfter = await page.evaluate(() => {
                        const alpineElements = document.querySelectorAll('[x-data]');
                        for (let el of alpineElements) {
                            if (el._x_dataStack && el._x_dataStack[0] && 'modalMantenimiento' in el._x_dataStack[0]) {
                                return el._x_dataStack[0].modalMantenimiento;
                            }
                        }
                        return 'NO_ENCONTRADO';
                    });
                    console.log('Estado después del click:', modalStateAfter);

                    // Verificar si el modal es visible
                    const modalDespues = page.locator('[x-show="modalMantenimiento"]');
                    const modalVisibleDespues = await modalDespues.isVisible();
                    console.log(`Modal visible después del click: ${modalVisibleDespues ? '✅' : '❌'}`);

                    if (!modalVisibleDespues && await modalDespues.count() > 0) {
                        // El modal existe pero no es visible, verificar CSS
                        const displayStyle = await modalDespues.evaluate(el => window.getComputedStyle(el).display);
                        console.log(`CSS display del modal: ${displayStyle}`);
                    }

                } else {
                    console.log('❌ No se encontraron botones "Registrar Mantenimiento"');
                }

            } else {
                console.log('❌ El contenido de mantenimientos no está visible');
            }

        } else {
            console.log('❌ No se encontró la pestaña de mantenimientos');

            // Mostrar todas las pestañas disponibles
            console.log('\n📋 Pestañas disponibles:');
            const todasLasPestanas = await page.locator('button').allTextContents();
            todasLasPestanas.forEach((texto, index) => {
                if (texto.trim()) {
                    console.log(`  ${index + 1}. "${texto.trim()}"`);
                }
            });
        }

    } catch (error) {
        console.error('❌ Error durante el diagnóstico:', error.message);
    } finally {
        console.log('\n⏰ Esperando 15 segundos para que puedas inspeccionar...');
        await page.waitForTimeout(15000);
        await browser.close();
        console.log('🏁 Diagnóstico completado');
    }
})();
