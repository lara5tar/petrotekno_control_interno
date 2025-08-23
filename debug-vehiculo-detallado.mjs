import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    // Capturar errores de JavaScript
    page.on('console', msg => {
        if (msg.type() === 'error') {
            console.log(`❌ Error JS: ${msg.text()}`);
        } else if (msg.type() === 'log') {
            console.log(`📝 Log: ${msg.text()}`);
        }
    });

    page.on('pageerror', error => {
        console.log(`❌ Error de página: ${error.message}`);
    });

    try {
        console.log('🔍 Navegando al vehículo...');
        await page.goto('http://localhost:8000/vehiculos/1', { waitUntil: 'networkidle' });

        // Esperar un poco para que cargue todo
        await page.waitForTimeout(3000);

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

        // Verificar si los botones existen
        console.log('\n🔍 Verificando botones...');
        const botones = await page.evaluate(() => {
            const botonesOperador = document.querySelectorAll('button[onclick*="openCambiarOperadorModal"]');
            const botonesObra = document.querySelectorAll('button[onclick*="openCambiarObraModal"]');
            const botonesMantenimiento = document.querySelectorAll('button[onclick*="openMantenimientoModal"]');

            return {
                operador: botonesOperador.length,
                obra: botonesObra.length,
                mantenimiento: botonesMantenimiento.length
            };
        });

        console.log('📋 Botones encontrados:');
        Object.entries(botones).forEach(([tipo, count]) => {
            console.log(`   ${tipo}: ${count} botones`);
        });

        // Verificar si los modales existen
        console.log('\n🔍 Verificando modales...');
        const modales = await page.evaluate(() => {
            return {
                operador: !!document.getElementById('cambiar-operador-modal'),
                obra: !!document.getElementById('cambiar-obra-modal'),
                mantenimiento: !!document.getElementById('registrar-mantenimiento-modal')
            };
        });

        console.log('📋 Modales en DOM:');
        Object.entries(modales).forEach(([tipo, exists]) => {
            console.log(`   ${tipo}: ${exists ? '✅' : '❌'}`);
        });

        // Intentar hacer clic en un botón si existe
        if (botones.operador > 0) {
            console.log('\n🖱️ Probando clic en botón de operador...');
            await page.click('button[onclick*="openCambiarOperadorModal"]');
            await page.waitForTimeout(2000);

            const modalVisible = await page.locator('#cambiar-operador-modal').isVisible();
            console.log(`🪟 Modal operador visible después del clic: ${modalVisible ? '✅' : '❌'}`);
        }

        if (botones.obra > 0) {
            console.log('\n🖱️ Probando clic en botón de obra...');
            await page.click('button[onclick*="openCambiarObraModal"]');
            await page.waitForTimeout(2000);

            const modalVisible = await page.locator('#cambiar-obra-modal').isVisible();
            console.log(`🪟 Modal obra visible después del clic: ${modalVisible ? '✅' : '❌'}`);
        }

        if (botones.mantenimiento > 0) {
            console.log('\n🖱️ Probando clic en botón de mantenimiento...');
            await page.click('button[onclick*="openMantenimientoModal"]');
            await page.waitForTimeout(2000);

            const modalVisible = await page.locator('#registrar-mantenimiento-modal').isVisible();
            console.log(`🪟 Modal mantenimiento visible después del clic: ${modalVisible ? '✅' : '❌'}`);
        }

    } catch (error) {
        console.error('❌ Error:', error);
    } finally {
        await browser.close();
    }
})();
