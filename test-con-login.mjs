import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('🔐 Navegando a login...');
        await page.goto('http://localhost:8000/login');
        await page.waitForLoadState('networkidle');

        // Llenar formulario de login (asumiendo credenciales por defecto)
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');

        await page.waitForLoadState('networkidle');
        console.log('✅ Login realizado');

        // Ahora navegar al vehículo
        console.log('🔍 Navegando al vehículo...');
        await page.goto('http://localhost:8000/vehiculos/1');
        await page.waitForLoadState('networkidle');

        // Verificar el título de la página
        const title = await page.title();
        console.log(`📄 Título de página: ${title}`);

        // Verificar si hay contenido en la página
        const hasContent = await page.locator('h1').count() > 0;
        console.log(`📋 Tiene contenido h1: ${hasContent ? '✅' : '❌'}`);

        if (hasContent) {
            const h1Text = await page.locator('h1').first().textContent();
            console.log(`📝 Texto h1: ${h1Text}`);
        }

        // Verificar si hay botones
        const botonesCount = await page.locator('button').count();
        console.log(`🔘 Total botones en página: ${botonesCount}`);

        // Verificar botones específicos
        const botonesOperador = await page.locator('button').filter({ hasText: /Asignar Operador|Cambiar Operador/ }).count();
        const botonesObra = await page.locator('button').filter({ hasText: /Asignar Obra|Cambiar Obra/ }).count();
        const botonesMantenimiento = await page.locator('button').filter({ hasText: /Registrar Mantenimiento/ }).count();

        console.log(`📋 Botones específicos:`);
        console.log(`   Operador: ${botonesOperador}`);
        console.log(`   Obra: ${botonesObra}`);
        console.log(`   Mantenimiento: ${botonesMantenimiento}`);

        // Verificar si las funciones existen
        console.log('\n🔍 Verificando funciones JavaScript...');
        const funcionesExisten = await page.evaluate(() => {
            return {
                openCambiarOperadorModal: typeof window.openCambiarOperadorModal === 'function',
                openCambiarObraModal: typeof window.openCambiarObraModal === 'function',
                openMantenimientoModal: typeof window.openMantenimientoModal === 'function',
                closeAllModals: typeof window.closeAllModals === 'function'
            };
        });

        console.log('📋 Funciones disponibles:');
        Object.entries(funcionesExisten).forEach(([func, exists]) => {
            console.log(`   ${func}: ${exists ? '✅' : '❌'}`);
        });

        // PROBAR BOTÓN DE OPERADOR
        if (botonesOperador > 0) {
            console.log('\n🧪 PROBANDO BOTÓN DE OPERADOR...');
            const botonOperador = page.locator('button').filter({ hasText: /Asignar Operador|Cambiar Operador/ }).first();
            await botonOperador.click();
            await page.waitForTimeout(2000);

            const modalOperador = page.locator('#cambiar-operador-modal');
            const isVisible = await modalOperador.isVisible();
            console.log(`🪟 Modal operador visible: ${isVisible ? '✅' : '❌'}`);

            if (isVisible) {
                // Cerrar modal
                await page.keyboard.press('Escape');
                await page.waitForTimeout(1000);
                console.log('🔒 Modal operador cerrado');
            }
        }

        // PROBAR BOTÓN DE OBRA
        if (botonesObra > 0) {
            console.log('\n🧪 PROBANDO BOTÓN DE OBRA...');
            const botonObra = page.locator('button').filter({ hasText: /Asignar Obra|Cambiar Obra/ }).first();
            await botonObra.click();
            await page.waitForTimeout(2000);

            const modalObra = page.locator('#cambiar-obra-modal');
            const isVisibleObra = await modalObra.isVisible();
            console.log(`🪟 Modal obra visible: ${isVisibleObra ? '✅' : '❌'}`);

            if (isVisibleObra) {
                // Cerrar modal
                await page.keyboard.press('Escape');
                await page.waitForTimeout(1000);
                console.log('🔒 Modal obra cerrado');
            }
        }

        // PROBAR BOTÓN DE MANTENIMIENTO
        if (botonesMantenimiento > 0) {
            console.log('\n🧪 PROBANDO BOTÓN DE MANTENIMIENTO...');
            const botonMantenimiento = page.locator('button').filter({ hasText: /Registrar Mantenimiento/ }).first();
            await botonMantenimiento.click();
            await page.waitForTimeout(2000);

            const modalMantenimiento = page.locator('#registrar-mantenimiento-modal');
            const isVisibleMant = await modalMantenimiento.isVisible();
            console.log(`🪟 Modal mantenimiento visible: ${isVisibleMant ? '✅' : '❌'}`);

            if (isVisibleMant) {
                // Cerrar modal
                await page.keyboard.press('Escape');
                await page.waitForTimeout(1000);
                console.log('🔒 Modal mantenimiento cerrado');
            }
        }

        console.log('\n✅ Todas las pruebas completadas');

        await page.waitForTimeout(3000);

    } catch (error) {
        console.error('❌ Error:', error);
    } finally {
        await browser.close();
    }
})();
