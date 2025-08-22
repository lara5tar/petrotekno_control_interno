import { chromium } from 'playwright';

async function testBasicConnection() {
    console.log('🔧 Probando conectividad básica y presencia de botones...');

    const browser = await chromium.launch({
        headless: false,
        args: ['--disable-web-security', '--disable-features=VizDisplayCompositor']
    });
    const page = await browser.newPage();

    try {
        // Intentar conectar directamente a una obra específica
        console.log('📍 Intentando conectar directamente a obra...');
        await page.goto('http://127.0.0.1:8002/obras/1', { waitUntil: 'networkidle' });

        console.log('🔍 URL actual:', page.url());

        // Si nos redirige al login, hacer login
        if (page.url().includes('/login')) {
            console.log('🔐 Detectado redirect a login, haciendo login...');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(2000);

            // Intentar ir a obras de nuevo
            await page.goto('http://127.0.0.1:8002/obras/1');
            await page.waitForTimeout(2000);
        }

        console.log('📍 URL final:', page.url());

        // Buscar la pestaña "Recursos"
        const tabRecursos = page.locator('button:has-text("Recursos")');
        if (await tabRecursos.count() > 0) {
            console.log('📁 Encontrada pestaña Recursos, haciendo click...');
            await tabRecursos.click();
            await page.waitForTimeout(1000);
        }

        // Buscar texto "Encargado de la Obra"
        const encargadoText = page.locator('text=Encargado de la Obra');
        const encargadoCount = await encargadoText.count();
        console.log(`👤 "Encargado de la Obra" encontrado: ${encargadoCount} veces`);

        // Buscar botones
        const botones = [
            'button:has-text("Asignar Responsable")',
            'button:has-text("Cambiar Responsable")',
            'text=Asignar Responsable',
            'text=Cambiar Responsable'
        ];

        for (const boton of botones) {
            const count = await page.locator(boton).count();
            console.log(`🔘 "${boton}": ${count} elementos`);
        }

        // Intentar hacer click en cualquier botón que exista
        let botonEncontrado = false;

        for (const selector of ['button:has-text("Asignar Responsable")', 'button:has-text("Cambiar Responsable")']) {
            const elemento = page.locator(selector).first();
            if (await elemento.count() > 0) {
                console.log(`🖱️ Intentando click en: ${selector}`);
                await elemento.click();
                await page.waitForTimeout(1000);

                // Verificar si el modal apareció
                const modal = page.locator('#cambiar-encargado-modal');
                const modalVisible = await modal.isVisible().catch(() => false);

                if (modalVisible) {
                    console.log('✅ ¡Modal apareció correctamente!');
                    botonEncontrado = true;

                    // Captura de pantalla del éxito
                    await page.screenshot({ path: 'modal-success.png', fullPage: true });
                    console.log('📸 Captura guardada: modal-success.png');
                    break;
                } else {
                    console.log('❌ Modal no apareció después del click');
                }
            }
        }

        if (!botonEncontrado) {
            console.log('⚠️ No se encontraron botones clickeables');

            // Captura de pantalla para debug
            await page.screenshot({ path: 'debug-no-buttons.png', fullPage: true });
            console.log('📸 Captura de debug guardada: debug-no-buttons.png');

            // Mostrar HTML de la sección
            const html = await page.innerHTML('body').catch(() => 'No se pudo obtener HTML');
            console.log('📝 Verificando si contiene elementos clave...');

            if (html.includes('Encargado de la Obra')) {
                console.log('✅ Texto "Encargado de la Obra" presente en HTML');
            }
            if (html.includes('openCambiarEncargadoModal')) {
                console.log('✅ Función "openCambiarEncargadoModal" presente en HTML');
            }
            if (html.includes('Asignar Responsable')) {
                console.log('✅ Texto "Asignar Responsable" presente en HTML');
            }
        }

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-error.png', fullPage: true });
        console.log('📸 Captura de error guardada: test-error.png');
    } finally {
        await browser.close();
    }
}

testBasicConnection().catch(console.error);
