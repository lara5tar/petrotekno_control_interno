import { chromium } from 'playwright';

async function testCreacionPersonalConDocumentos() {
    console.log('🚀 Probando creación de personal con documentos...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 800
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Login con admin
        console.log('📋 1. Login con usuario administrador...');
        await page.goto('http://127.0.0.1:8002/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // 2. Ir al formulario de creación de personal
        console.log('📋 2. Navegando al formulario de personal...');
        await page.goto('http://127.0.0.1:8002/personal/create');
        await page.waitForLoadState('networkidle');
        console.log('✅ Formulario de personal cargado');

        // 3. Llenar datos básicos
        console.log('📋 3. Llenando datos básicos del personal...');
        await page.fill('input[name="nombre_completo"]', 'Operador Test Documentos');

        // Seleccionar categoría "Operador"
        await page.selectOption('select[name="categoria_id"]', { label: 'Operador' });
        console.log('✅ Datos básicos llenados');

        // 4. Subir archivo INE (el más importante)
        console.log('📋 4. Subiendo archivo INE...');
        const fileInput = await page.locator('input[type="file"]').first();
        if (await fileInput.count() > 0) {
            // Usar el archivo de prueba que ya existe
            await fileInput.setInputFiles('/home/lara5tar/Escritorio/proyectos-clipsitos/petrotekno_control_interno/test_ine.pdf');
            console.log('✅ Archivo INE subido');
        } else {
            console.log('⚠️ No se encontró input de archivo');
        }

        // 5. Verificar que no hay errores antes de enviar
        console.log('📋 5. Verificando formulario antes de enviar...');

        // Buscar posibles errores de validación ya visibles
        const errorsVisible = await page.locator('.text-red-500, .text-danger, .alert-danger').count();
        if (errorsVisible > 0) {
            console.log('⚠️ Hay errores de validación visibles');
            const errorTexts = await page.locator('.text-red-500, .text-danger, .alert-danger').allTextContents();
            errorTexts.forEach(error => console.log(`   - ${error}`));
        } else {
            console.log('✅ No hay errores de validación visibles');
        }

        // 6. Intentar enviar el formulario
        console.log('📋 6. Enviando formulario...');
        const submitButton = await page.locator('button[type="submit"], input[type="submit"]').first();

        if (await submitButton.count() > 0) {
            await submitButton.click();

            // Esperar a ver si hay redirección o errores
            await page.waitForTimeout(3000);

            const currentUrl = page.url();
            console.log(`📍 URL actual: ${currentUrl}`);

            if (currentUrl.includes('/personal') && !currentUrl.includes('/create')) {
                console.log('🎉 ¡Personal creado exitosamente!');

                // Verificar si aparece en la lista
                const tablaPersonal = await page.locator('table tbody tr').count();
                console.log(`📊 Total de personal en sistema: ${tablaPersonal}`);

                // Buscar el personal recién creado
                const contenidoTabla = await page.locator('table tbody').textContent();
                if (contenidoTabla?.includes('Operador Test Documentos')) {
                    console.log('✅ Personal encontrado en la lista');
                } else {
                    console.log('⚠️ Personal no encontrado en la lista');
                }

            } else {
                console.log('📋 Aún en formulario, verificando errores...');

                // Buscar errores específicos
                const errorsAfterSubmit = await page.locator('.text-red-500, .text-danger, .alert-danger, .invalid-feedback').count();
                if (errorsAfterSubmit > 0) {
                    console.log('❌ Errores encontrados después del envío:');
                    const errorTexts = await page.locator('.text-red-500, .text-danger, .alert-danger, .invalid-feedback').allTextContents();
                    errorTexts.forEach(error => {
                        if (error.trim()) {
                            console.log(`   - ${error.trim()}`);
                        }
                    });
                } else {
                    console.log('🤔 No se encontraron errores específicos');
                }
            }
        } else {
            console.log('❌ No se encontró botón de envío');
        }

        // 7. Tomar screenshot para documentación
        await page.screenshot({ path: 'test-personal-con-documentos.png', fullPage: true });
        console.log('📸 Screenshot guardado como test-personal-con-documentos.png');

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-personal-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

testCreacionPersonalConDocumentos().catch(console.error);
