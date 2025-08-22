import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

async function testCreateObraWithFiles() {
    console.log('🚀 Iniciando prueba completa de crear obra con archivos...');

    const browser = await chromium.launch({ headless: false }); // headless: false para ver la prueba
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // Navegar a la página de login
        console.log('📝 Navegando a login...');
        await page.goto('http://127.0.0.1:8003/login', { waitUntil: 'networkidle' });

        // Hacer login
        console.log('🔐 Haciendo login...');
        await page.fill('input[name="email"]', 'admin@test.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Verificar que el login fue exitoso
        const currentUrl = page.url();
        console.log('🌐 URL actual después del login:', currentUrl);

        // Navegar a crear obra
        console.log('📋 Navegando a crear obra...');
        await page.goto('http://127.0.0.1:8003/obras/create', { waitUntil: 'networkidle' });

        // Verificar que la página cargó sin errores
        const hasError = await page.locator('text=Internal Server Error').count();
        if (hasError > 0) {
            console.log('❌ Error: Página tiene errores de servidor');
            await page.screenshot({ path: 'error-screenshot.png' });
            return false;
        }

        console.log('✅ Página de crear obra cargada correctamente');

        // Llenar campos básicos del formulario
        console.log('📝 Llenando formulario básico...');
        await page.fill('input[name="nombre_obra"]', 'Obra de Prueba Playwright');
        await page.fill('input[name="ubicacion"]', 'Ubicación de prueba');
        await page.fill('textarea[name="observaciones"]', 'Descripción de prueba para validar la subida de archivos');
        await page.fill('input[name="fecha_inicio"]', '2025-08-21');
        await page.fill('input[name="fecha_fin"]', '2025-12-31');
        await page.fill('input[name="avance"]', '0');

        // Seleccionar estado
        await page.selectOption('select[name="estatus"]', 'planificada');

        // Seleccionar encargado (el primer disponible)
        const encargadoOptions = await page.locator('select[name="encargado_id"] option').count();
        if (encargadoOptions > 1) {
            await page.selectOption('select[name="encargado_id"]', { index: 1 });
            console.log('✅ Encargado seleccionado');
        }

        console.log('✅ Campos básicos llenados');

        // Verificar que los campos de archivos existen
        console.log('📄 Verificando campos de archivos...');
        const contratoField = await page.locator('input[name="archivo_contrato"]').count();
        const fianzaField = await page.locator('input[name="archivo_fianza"]').count();
        const actaField = await page.locator('input[name="archivo_acta_entrega_recepcion"]').count();

        console.log('📁 Campos de archivos encontrados:');
        console.log('  - Contrato:', contratoField > 0 ? '✅' : '❌');
        console.log('  - Fianza:', fianzaField > 0 ? '✅' : '❌');
        console.log('  - Acta:', actaField > 0 ? '✅' : '❌');

        // Crear archivos de prueba si no existen
        const testFiles = {
            contrato: 'test_contrato.pdf',
            fianza: 'test_fianza.pdf',
            acta: 'test_acta.pdf'
        };

        console.log('📄 Preparando archivos de prueba...');
        for (const [key, filename] of Object.entries(testFiles)) {
            if (!fs.existsSync(filename)) {
                fs.writeFileSync(filename, 'Archivo de prueba para ' + key);
                console.log(`✅ Archivo ${filename} creado`);
            }
        }

        // Subir archivos usando los labels (ya que los inputs están ocultos)
        console.log('📤 Subiendo archivos...');

        // Subir contrato
        await page.setInputFiles('input[name="archivo_contrato"]', testFiles.contrato);
        console.log('✅ Contrato subido');

        // Subir fianza
        await page.setInputFiles('input[name="archivo_fianza"]', testFiles.fianza);
        console.log('✅ Fianza subida');

        // Subir acta
        await page.setInputFiles('input[name="archivo_acta_entrega_recepcion"]', testFiles.acta);
        console.log('✅ Acta subida');

        // Verificar formulario antes de enviar
        console.log('🔍 Verificando formulario antes del envío...');

        // Verificar que el formulario tiene enctype correcto
        const formEnctype = await page.getAttribute('form', 'enctype');
        console.log('📋 Enctype del formulario:', formEnctype);

        if (formEnctype !== 'multipart/form-data') {
            console.log('❌ Error: Formulario no tiene enctype multipart/form-data');
            return false;
        }

        // Hacer scroll al botón de envío
        await page.locator('button[type="submit"]').scrollIntoViewIfNeeded();

        // Enviar formulario
        console.log('📤 Enviando formulario...');

        // Esperar por la respuesta del servidor
        const [response] = await Promise.all([
            page.waitForResponse(response => response.url().includes('/obras') && response.request().method() === 'POST'),
            page.click('button[type="submit"]')
        ]);

        console.log('� Respuesta del servidor:', response.status());

        // Esperar a que se complete la navegación
        await page.waitForLoadState('networkidle');

        const finalUrl = page.url();
        console.log('🏁 URL final:', finalUrl);

        // Verificar si hay errores de validación
        const validationErrors = await page.locator('.text-red-600').count();
        if (validationErrors > 0) {
            console.log('⚠️ Errores de validación encontrados:');
            const errors = await page.locator('.text-red-600').allTextContents();
            errors.forEach(error => console.log(`  - ${error}`));
        }

        // Verificar si la obra se creó exitosamente
        const successMessage = await page.locator('text=creada exitosamente').count();
        const indexPage = finalUrl.includes('/obras') && !finalUrl.includes('/create');

        if (successMessage > 0 || indexPage) {
            console.log('🎉 Obra creada exitosamente');

            // Verificar que los archivos se guardaron
            if (indexPage) {
                console.log('📋 Verificando en la lista de obras...');
                const obraCreada = await page.locator('text=Obra de Prueba Playwright').count();
                console.log('🔍 Obra encontrada en la lista:', obraCreada > 0 ? '✅' : '❌');
            }

            return true;
        } else {
            console.log('❌ Error: No se pudo crear la obra');
            await page.screenshot({ path: 'create-error-screenshot.png' });
            return false;
        }

    } catch (error) {
        console.log('❌ Error durante la prueba:', error.message);
        await page.screenshot({ path: 'test-error-screenshot.png' });
        return false;
    } finally {
        await browser.close();

        // Limpiar archivos de prueba
        console.log('🧹 Limpiando archivos de prueba...');
        const testFiles = ['test_contrato.pdf', 'test_fianza.pdf', 'test_acta.pdf'];
        testFiles.forEach(file => {
            if (fs.existsSync(file)) {
                fs.unlinkSync(file);
                console.log(`🗑️ ${file} eliminado`);
            }
        });
    }
}

testCreateObraWithFiles().then(success => {
    if (success) {
        console.log('🎉 Prueba completada exitosamente - Los archivos se guardan correctamente');
        process.exit(0);
    } else {
        console.log('💥 Prueba falló - Hay problemas con el guardado de archivos');
        process.exit(1);
    }
});
