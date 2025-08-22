/**
 * Script para probar específicamente el botón y modal de responsable de obra
 * en la vista de detalle de vehículos
 */
import { chromium } from 'playwright';

(async () => {
    console.log('🔧 Probando el botón de asignar/cambiar responsable en vista de vehículos...');

    const browser = await chromium.launch({ headless: false });
    try {
        const context = await browser.newContext({
            viewport: { width: 1280, height: 720 },
            acceptDownloads: true
        });

        const page = await context.newPage();

        // Cargar la página
        console.log('🌐 Accediendo a la página de vehiculos...');
        await page.goto('http://127.0.0.1:8002/login');

        // Iniciar sesión
        console.log('🔑 Iniciando sesión...');
        await page.fill('#email', 'admin@petrotekno.com');
        await page.fill('#password', 'password');
        await page.click('button[type="submit"]');

        // Esperar a estar en el dashboard
        await page.waitForURL('**/dashboard');
        console.log('✅ Login exitoso');

        // Navegar a la página de vehículos
        console.log('🚗 Navegando a la lista de vehículos...');
        await page.goto('http://127.0.0.1:8002/vehiculos');

        // Hacer clic en el primer vehículo (asumiendo que hay al menos uno)
        console.log('🔍 Seleccionando un vehículo...');
        await page.waitForTimeout(2000);

        const primerVehiculo = page.locator('a[href*="/vehiculos/"]').first();

        if (await primerVehiculo.count() === 0) {
            console.error('❌ No se encontraron vehículos en la lista');
            await page.screenshot({ path: 'test-responsable-vehiculo-error.png' });
            throw new Error('No se encontraron vehículos');
        }

        await primerVehiculo.click();
        await page.waitForTimeout(2000);

        // Verificar que estamos en la página de detalle
        console.log('✅ Verificando la página de detalle del vehículo...');

        // Tomar una captura antes de buscar el botón
        await page.screenshot({ path: 'test-vehiculo-detalle.png' });
        console.log('📸 Captura guardada: test-vehiculo-detalle.png');

        // Buscar el botón de asignar/cambiar responsable
        console.log('🔍 Buscando el botón de responsable de obra...');
        const selector = 'button[onclick="openResponsableObraModal()"]';

        const existeBoton = await page.locator(selector).count() > 0;
        if (!existeBoton) {
            console.error('❌ No se encontró el botón de responsable de obra');

            // Verificar secciones relevantes
            const seccionObra = await page.locator('text=Responsable de la Obra').count();
            console.log(`  Sección "Responsable de la Obra": ${seccionObra} elementos encontrados`);

            // Buscar cualquier botón relevante
            const botones = await page.locator('button:has-text("Asignar"), button:has-text("Cambiar")').count();
            console.log(`  Botones con "Asignar" o "Cambiar": ${botones} elementos encontrados`);

            throw new Error('No se encontró el botón de responsable de obra');
        }

        console.log('✅ Botón de responsable encontrado, haciendo clic...');
        await page.click(selector);
        await page.waitForTimeout(2000);

        // Verificar que el modal aparece
        console.log('🔍 Verificando que el modal aparezca...');

        // Tomar una captura después de hacer clic
        await page.screenshot({ path: 'test-modal-responsable-vehiculo.png' });
        console.log('📸 Captura guardada: test-modal-responsable-vehiculo.png');

        // Verificar visibilidad del modal
        const modalSelector = '#responsable-obra-modal';
        const modalVisible = await page.locator(modalSelector).isVisible();

        // Inspeccionar el estado del modal
        const jsLogs = await page.evaluate((selector) => {
            const modal = document.querySelector(selector);
            if (!modal) return { exists: false, message: 'El modal no existe' };

            return {
                exists: true,
                className: modal.className,
                style: {
                    display: modal.style.display,
                    zIndex: modal.style.zIndex
                },
                computedStyle: {
                    display: window.getComputedStyle(modal).display,
                    zIndex: window.getComputedStyle(modal).zIndex,
                    position: window.getComputedStyle(modal).position,
                    visibility: window.getComputedStyle(modal).visibility
                },
                containsHidden: modal.classList.contains('hidden')
            };
        }, modalSelector);

        console.log('📊 Estado del modal:', jsLogs);
        console.log(`Modal visible según Playwright: ${modalVisible ? '✅ SÍ' : '❌ NO'}`);

        if (modalVisible) {
            console.log('✅ El modal se muestra correctamente');

            // Verificar contenido del modal
            const tituloModal = await page.locator(`${modalSelector} h3`).textContent();
            console.log(`📝 Título del modal: "${tituloModal}"`);

            // Verificar elementos del formulario
            const selectExists = await page.locator(`${modalSelector} select#personal_id`).count() > 0;
            console.log(`Select de personal: ${selectExists ? '✅ Existe' : '❌ No existe'}`);

            // Botones en el modal
            const botonesCerrar = await page.locator(`${modalSelector} button:has-text("Cancelar")`).count();
            const botonesGuardar = await page.locator(`${modalSelector} button[type="submit"]`).count();

            console.log(`Botón cancelar: ${botonesCerrar > 0 ? '✅ Existe' : '❌ No existe'}`);
            console.log(`Botón guardar: ${botonesGuardar > 0 ? '✅ Existe' : '❌ No existe'}`);

            // Cerrar el modal
            await page.click(`${modalSelector} button:has-text("Cancelar")`);
            await page.waitForTimeout(1000);

            // Verificar que se cerró
            const modalVisibleDespues = await page.locator(modalSelector).isVisible();
            console.log(`Modal cerrado: ${!modalVisibleDespues ? '✅ SÍ' : '❌ NO'}`);
        } else {
            console.error('❌ El modal no se muestra');
        }

        // Esperar un momento antes de cerrar
        await page.waitForTimeout(3000);

    } catch (error) {
        console.error('❌ Error en test:', error);
        // Guardar captura del error
        try {
            await page.screenshot({ path: 'test-responsable-vehiculo-error.png' });
            console.log('📸 Captura de error guardada: test-responsable-vehiculo-error.png');
        } catch (screenshotError) {
            console.error('Error al tomar captura:', screenshotError);
        }
    } finally {
        await browser.close();
    }
})();
