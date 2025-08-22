import { chromium } from 'playwright';

async function testBotonAsignarResponsable() {
    console.log('🚀 Iniciando test del botón Asignar Responsable...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 500
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Ir al login
        console.log('📋 1. Navegando al login...');
        await page.goto('http://127.0.0.1:8002/login');
        await page.waitForSelector('input[name="email"]');

        // 2. Hacer login
        console.log('🔐 2. Haciendo login...');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // 3. Ir directamente a la obra
        console.log('📋 3. Navegando a obra...');
        await page.goto('http://127.0.0.1:8002/obras/1');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        // 4. Configurar listeners para errores
        page.on('console', msg => {
            if (msg.type() === 'error') {
                console.log('❌ Error JS:', msg.text());
            }
        });
        page.on('pageerror', error => console.log('❌ Error de página:', error.message));

        // 5. Verificar que el botón azul existe
        console.log('🔍 5. Buscando botón azul "Asignar Responsable"...');
        const botonAzul = page.locator('#btn-cambiar-responsable');
        await botonAzul.waitFor({ state: 'visible', timeout: 5000 });
        console.log('✅ Botón azul encontrado');

        // 6. Hacer clic en el botón
        console.log('🖱️ 6. Haciendo clic en el botón...');
        await botonAzul.click();
        await page.waitForTimeout(1000);

        // 7. Verificar que el modal se abre
        console.log('🔍 7. Verificando modal...');
        const modal = page.locator('#cambiar-responsable-modal');
        const isModalVisible = await modal.evaluate(el => !el.classList.contains('hidden'));

        if (isModalVisible) {
            console.log('✅ ¡Modal abierto correctamente!');

            // 8. Verificar elementos del modal
            const titulo = await page.locator('#modal-responsable-title').textContent();
            console.log(`✅ Título: ${titulo.trim()}`);

            const selectOptions = await page.locator('#responsable_id option').count();
            console.log(`✅ Opciones de responsables: ${selectOptions}`);

            // 9. Cerrar modal con ESC
            console.log('⌨️ 9. Cerrando modal con ESC...');
            await page.keyboard.press('Escape');
            await page.waitForTimeout(500);

            const isModalHidden = await modal.evaluate(el => el.classList.contains('hidden'));
            if (isModalHidden) {
                console.log('✅ Modal cerrado correctamente');
            }

        } else {
            console.log('❌ Modal no se abrió');
        }

        console.log('🎉 Test completado');

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({
            path: 'test-boton-error.png',
            fullPage: true
        });
    } finally {
        await browser.close();
    }
}

testBotonAsignarResponsable().catch(console.error);
