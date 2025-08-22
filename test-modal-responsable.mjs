import { chromium } from 'playwright';

async function testAsignarResponsableModal() {
    console.log('🔧 Probando modal de asignar responsable en obras...');

    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        // Navegar a la página de login
        await page.goto('http://127.0.0.1:8002/login');
        console.log('📍 Navegando a página de login...');

        // Hacer login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard');
        console.log('✅ Login exitoso');

        // Navegar a obras
        await page.click('a[href*="/obras"]');
        await page.waitForURL('**/obras');
        console.log('📋 Navegando a lista de obras...');

        // Buscar una obra y hacer click en "Ver Detalles"
        const verDetallesButton = page.locator('a:has-text("Ver Detalles")').first();
        await verDetallesButton.click();
        await page.waitForURL('**/obras/*');
        console.log('👁️ Navegando a detalles de obra...');

        // Hacer click en la pestaña "Recursos" para asegurar que estamos en la vista correcta
        await page.click('button:has-text("Recursos")');
        await page.waitForTimeout(500);
        console.log('📁 Cambiando a pestaña Recursos...');

        // Verificar que existe la sección "Encargado de la Obra"
        const encargadoSection = page.locator('text=Encargado de la Obra');
        await encargadoSection.waitFor({ timeout: 5000 });
        console.log('👤 Sección "Encargado de la Obra" encontrada');

        // Buscar botones de asignar/cambiar responsable
        let botonAsignar = null;

        // Primero intentar encontrar el botón en el header de la sección
        const botonCambiarHeader = page.locator('button:has-text("Cambiar Responsable")');
        const botonAsignarHeader = page.locator('button:has-text("Asignar Responsable")').first();

        if (await botonCambiarHeader.count() > 0) {
            botonAsignar = botonCambiarHeader;
            console.log('🔄 Encontrado botón "Cambiar Responsable" en header');
        } else if (await botonAsignarHeader.count() > 0) {
            botonAsignar = botonAsignarHeader;
            console.log('➕ Encontrado botón "Asignar Responsable" en header');
        }

        // Si no hay botón en el header, buscar en la zona de "No hay responsable asignado"
        if (!botonAsignar) {
            const botonAsignarCentro = page.locator('button:has-text("Asignar Responsable")').last();
            if (await botonAsignarCentro.count() > 0) {
                botonAsignar = botonAsignarCentro;
                console.log('🎯 Encontrado botón "Asignar Responsable" en zona central');
            }
        }

        if (!botonAsignar) {
            throw new Error('❌ No se encontró ningún botón de asignar/cambiar responsable');
        }

        // Hacer click en el botón para abrir el modal
        console.log('🖱️ Haciendo click en el botón...');
        await botonAsignar.click();
        await page.waitForTimeout(1000);

        // Verificar que el modal se abrió
        const modal = page.locator('#cambiar-encargado-modal');
        await modal.waitFor({ state: 'visible', timeout: 5000 });
        console.log('✅ Modal de asignar responsable se abrió correctamente');

        // Verificar elementos del modal
        const modalTitle = page.locator('#modal-encargado-title');
        await modalTitle.waitFor({ timeout: 3000 });
        const titleText = await modalTitle.textContent();
        console.log(`📝 Título del modal: "${titleText}"`);

        // Verificar que existe el dropdown de responsables
        const selectResponsable = page.locator('select[name="encargado_id"]');
        await selectResponsable.waitFor({ timeout: 3000 });
        console.log('📋 Dropdown de responsables encontrado');

        // Verificar opciones en el dropdown
        const opciones = await selectResponsable.locator('option').count();
        console.log(`👥 Encontradas ${opciones} opciones en el dropdown`);

        // Verificar campo de observaciones
        const observaciones = page.locator('textarea[name="observaciones"]');
        await observaciones.waitFor({ timeout: 3000 });
        console.log('📝 Campo de observaciones encontrado');

        // Verificar botones del modal
        const botonCancelar = page.locator('button:has-text("Cancelar")');
        const botonConfirmar = page.locator('button[type="submit"]');

        await botonCancelar.waitFor({ timeout: 3000 });
        await botonConfirmar.waitFor({ timeout: 3000 });
        console.log('🔘 Botones de Cancelar y Confirmar encontrados');

        // Probar cerrar el modal con el botón de cancelar
        await botonCancelar.click();
        await page.waitForTimeout(500);

        // Verificar que el modal se cerró
        await modal.waitFor({ state: 'hidden', timeout: 3000 });
        console.log('❌ Modal cerrado correctamente con botón Cancelar');

        // Probar abrir el modal de nuevo y cerrar con la X
        await botonAsignar.click();
        await modal.waitFor({ state: 'visible', timeout: 3000 });
        console.log('🔄 Modal reabierto para probar botón X');

        const botonCerrarX = page.locator('button[onclick="closeCambiarEncargadoModal()"]');
        await botonCerrarX.click();
        await page.waitForTimeout(500);
        await modal.waitFor({ state: 'hidden', timeout: 3000 });
        console.log('✖️ Modal cerrado correctamente con botón X');

        console.log('🎉 ¡Todas las pruebas del modal pasaron exitosamente!');

        // Captura de pantalla final
        await page.screenshot({ path: 'test-modal-responsable-success.png', fullPage: true });
        console.log('📸 Captura de pantalla guardada: test-modal-responsable-success.png');

    } catch (error) {
        console.error('❌ Error en test:', error.message);

        // Captura de pantalla del error
        await page.screenshot({ path: 'test-modal-responsable-error.png', fullPage: true });
        console.log('📸 Captura de error guardada: test-modal-responsable-error.png');

        // Información de debug
        console.log('🔍 Estado actual de la página:');
        console.log('URL:', page.url());

        // Verificar si existen elementos relevantes
        const elementos = [
            'text=Encargado de la Obra',
            'button:has-text("Asignar Responsable")',
            'button:has-text("Cambiar Responsable")',
            '#cambiar-encargado-modal',
            'text=No hay responsable asignado'
        ];

        for (const elemento of elementos) {
            const count = await page.locator(elemento).count();
            console.log(`  ${elemento}: ${count} elementos encontrados`);
        }
    } finally {
        await browser.close();
    }
}

// Ejecutar el test
testAsignarResponsableModal().catch(console.error);
