import { chromium } from 'playwright';

async function testBotonAsignarResponsable() {
    console.log('🔧 Probando botón "Asignar Responsable" en obras...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 1000 // Hacer más lento para ver qué pasa
    });
    const page = await browser.newPage();

    try {
        // Navegar a login
        console.log('📍 Navegando a login...');
        await page.goto('http://127.0.0.1:8005/login');

        // Login
        console.log('🔐 Haciendo login...');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard', { timeout: 10000 });
        console.log('✅ Login exitoso');

        // Ir a obras
        console.log('📋 Navegando a obras...');
        await page.goto('http://127.0.0.1:8005/obras');
        await page.waitForLoadState('networkidle');

        // Buscar primera obra y ver detalles
        console.log('👁️ Buscando obra para ver detalles...');
        const primerDetalles = page.locator('a:has-text("Ver Detalles")').first();
        await primerDetalles.waitFor({ timeout: 5000 });
        await primerDetalles.click();
        await page.waitForLoadState('networkidle');

        console.log('📍 URL actual:', page.url());

        // Ir a pestaña Recursos
        console.log('📁 Cambiando a pestaña Recursos...');
        const tabRecursos = page.locator('button:has-text("Recursos")');
        await tabRecursos.waitFor({ timeout: 5000 });
        await tabRecursos.click();
        await page.waitForTimeout(1000);

        // Captura de pantalla antes de buscar botón
        await page.screenshot({ path: 'antes-buscar-boton.png', fullPage: true });
        console.log('📸 Captura guardada: antes-buscar-boton.png');

        // Verificar que existe la sección
        console.log('🔍 Buscando sección "Encargado de la Obra"...');
        const seccionEncargado = page.locator('text=Encargado de la Obra');
        await seccionEncargado.waitFor({ timeout: 5000 });
        console.log('✅ Sección encontrada');

        // Buscar todos los botones posibles
        console.log('🔍 Buscando botones...');

        const botonesSelectores = [
            'button:has-text("Asignar Responsable")',
            'button:has-text("Cambiar Responsable")',
            'button[onclick*="openCambiarEncargadoModal"]',
            '*[onclick*="openCambiarEncargadoModal"]'
        ];

        let botonEncontrado = null;
        let selectorUsado = '';

        for (const selector of botonesSelectores) {
            const elementos = page.locator(selector);
            const count = await elementos.count();
            console.log(`  ${selector}: ${count} elementos`);

            if (count > 0 && !botonEncontrado) {
                botonEncontrado = elementos.first();
                selectorUsado = selector;
            }
        }

        if (!botonEncontrado) {
            console.log('❌ No se encontró ningún botón');

            // Verificar HTML completo de la sección
            const htmlSeccion = await page.locator('.bg-white').filter({ hasText: 'Encargado de la Obra' }).innerHTML();
            console.log('📝 HTML de la sección:');
            console.log(htmlSeccion.substring(0, 500) + '...');

            // Buscar cualquier elemento con openCambiarEncargadoModal
            const elementosModal = page.locator('*').filter({ hasText: 'openCambiarEncargadoModal' });
            const countModal = await elementosModal.count();
            console.log(`🔍 Elementos con openCambiarEncargadoModal: ${countModal}`);

            throw new Error('No se encontró ningún botón para abrir el modal');
        }

        console.log(`✅ Botón encontrado con selector: ${selectorUsado}`);

        // Verificar que el botón es visible y clickeable
        const esVisible = await botonEncontrado.isVisible();
        const esHabilitado = await botonEncontrado.isEnabled();

        console.log(`👁️ Botón visible: ${esVisible}`);
        console.log(`🖱️ Botón habilitado: ${esHabilitado}`);

        if (!esVisible) {
            throw new Error('El botón existe pero no es visible');
        }

        if (!esHabilitado) {
            throw new Error('El botón existe pero no está habilitado');
        }

        // Verificar que existe la función JavaScript
        console.log('🔍 Verificando función JavaScript...');
        const funcionExiste = await page.evaluate(() => {
            return typeof window.openCambiarEncargadoModal === 'function';
        });

        console.log(`⚙️ Función openCambiarEncargadoModal existe: ${funcionExiste}`);

        if (!funcionExiste) {
            console.log('❌ La función JavaScript no existe, buscando en el HTML...');
            const scriptContent = await page.content();
            const tieneScript = scriptContent.includes('openCambiarEncargadoModal');
            console.log(`📜 Script en HTML: ${tieneScript}`);
        }

        // Hacer click en el botón
        console.log('🖱️ Haciendo click en el botón...');
        await botonEncontrado.click();
        await page.waitForTimeout(2000);

        // Verificar si apareció el modal
        console.log('🔍 Verificando si apareció el modal...');
        const modal = page.locator('#cambiar-encargado-modal');
        const modalVisible = await modal.isVisible();

        console.log(`📱 Modal visible: ${modalVisible}`);

        if (modalVisible) {
            console.log('🎉 ¡Modal apareció correctamente!');

            // Verificar elementos del modal
            const titulo = page.locator('#modal-encargado-title');
            const tituloTexto = await titulo.textContent();
            console.log(`📝 Título del modal: "${tituloTexto}"`);

            const dropdown = page.locator('select[name="encargado_id"]');
            const dropdownVisible = await dropdown.isVisible();
            console.log(`📋 Dropdown visible: ${dropdownVisible}`);

            // Captura de pantalla del éxito
            await page.screenshot({ path: 'modal-funcionando.png', fullPage: true });
            console.log('📸 Captura del modal guardada: modal-funcionando.png');

            // Cerrar modal
            const botonCerrar = page.locator('button[onclick="closeCambiarEncargadoModal()"]');
            await botonCerrar.click();
            await page.waitForTimeout(1000);

            console.log('✅ Test completado exitosamente - El botón funciona correctamente');

        } else {
            console.log('❌ El modal no apareció después del click');

            // Captura de pantalla del problema
            await page.screenshot({ path: 'modal-no-aparece.png', fullPage: true });
            console.log('📸 Captura del problema guardada: modal-no-aparece.png');

            // Verificar errores de consola
            page.on('console', msg => {
                console.log(`🖥️ Console ${msg.type()}: ${msg.text()}`);
            });

            // Verificar errores de JavaScript
            const errores = await page.evaluate(() => {
                return window.lastError || 'No hay errores capturados';
            });
            console.log(`⚠️ Errores JS: ${errores}`);

            throw new Error('El botón existe pero el modal no se abre');
        }

    } catch (error) {
        console.error('❌ Error en test:', error.message);

        await page.screenshot({ path: 'test-error-detallado.png', fullPage: true });
        console.log('📸 Captura de error guardada: test-error-detallado.png');

        // Debug adicional
        console.log('🔍 Debug adicional:');
        console.log('URL:', page.url());

        const elementos = await page.evaluate(() => {
            const encargado = document.querySelector('*:has-text("Encargado de la Obra")');
            const botones = document.querySelectorAll('button');
            return {
                encargadoExiste: !!encargado,
                totalBotones: botones.length,
                botonesTexto: Array.from(botones).map(b => b.textContent?.trim()).filter(t => t)
            };
        });

        console.log('📊 Elementos encontrados:', elementos);

    } finally {
        await browser.close();
    }
}

testBotonAsignarResponsable().catch(console.error);
