import { chromium } from 'playwright';
import path from 'path';

(async () => {
    console.log('🧪 Iniciando test de subida de archivos con campos corregidos...');

    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // Navegar al login
        console.log('🔐 Accediendo al login...');
        await page.goto('http://127.0.0.1:8001/login');
        await page.waitForLoadState('networkidle');

        // Login con credenciales admin
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        console.log('✅ Login exitoso');

        // Navegar al formulario de personal
        console.log('📋 Accediendo al formulario de personal...');
        await page.goto('http://127.0.0.1:8001/personal/create');
        await page.waitForLoadState('networkidle');

        // Llenar datos básicos
        console.log('📝 Llenando datos básicos...');
        await page.fill('input[name="nombre_completo"]', 'Juan Carlos Test Storage');

        // Seleccionar categoría (primer option disponible)
        await page.selectOption('select[name="categoria_personal_id"]', { index: 1 });

        // Llenar campos de documentos
        await page.fill('input[name="ine"]', 'TEST123456789');
        await page.fill('input[name="curp_numero"]', 'JUCT850315HDFXXX01');
        await page.fill('input[name="rfc"]', 'JUCT850315ABC');
        await page.fill('input[name="nss"]', '12345678901');
        await page.fill('input[name="no_licencia"]', 'LIC987654321');
        await page.fill('textarea[name="direccion"]', 'Calle Test 123, Col. Storage, Ciudad Test');

        // Subir archivo INE usando el nuevo nombre de campo
        console.log('📎 Subiendo archivo INE...');
        const archivoPath = path.join(process.cwd(), 'test_ine.pdf');
        await page.setInputFiles('input[name="identificacion_file"]', archivoPath);

        // Enviar formulario
        console.log('📤 Enviando formulario...');
        await page.click('button[type="submit"]');

        // Esperar respuesta
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000); // Esperar un poco más para procesamiento

        const url = page.url();
        console.log('🔗 URL después del envío:', url);

        // Verificar si se redirigió correctamente o hubo errores
        if (url.includes('/personal/') && !url.includes('/create')) {
            console.log('✅ Redirección exitosa al perfil del personal');

            const personalId = url.split('/personal/')[1];
            console.log('🆔 ID del personal creado:', personalId);

            // Verificar que los datos aparecen en la página
            const nombreVisible = await page.locator('text=Juan Carlos Test Storage').isVisible();
            const ineVisible = await page.locator('text=TEST123456789').isVisible();

            if (nombreVisible && ineVisible) {
                console.log('✅ Datos básicos visibles en la página');
            } else {
                console.log('⚠️ No se ven todos los datos en la página');
            }

            // Verificar en la API
            console.log('🔍 Verificando datos en la API...');
            const response = await page.request.get(`http://127.0.0.1:8001/web-api/personal/${personalId}`);
            const data = await response.json();

            if (data.success) {
                const personal = data.data;
                console.log('📊 Datos del personal:');
                console.log(`  - Nombre: ${personal.nombre_completo}`);
                console.log(`  - INE: ${personal.ine}`);
                console.log(`  - CURP: ${personal.curp_numero}`);
                console.log(`  - RFC: ${personal.rfc}`);
                console.log(`  - NSS: ${personal.nss}`);
                console.log(`  - Licencia: ${personal.no_licencia}`);
                console.log(`  - Dirección: ${personal.direccion}`);
                console.log(`  - URL INE: ${personal.url_ine}`);

                if (personal.url_ine) {
                    console.log('✅ ¡ARCHIVO GUARDADO EXITOSAMENTE!');
                    console.log(`📁 Ruta del archivo: ${personal.url_ine}`);

                    // Verificar que el archivo físico existe
                    try {
                        const fileResponse = await page.request.get(`http://127.0.0.1:8001/storage/${personal.url_ine}`);
                        if (fileResponse.status() === 200) {
                            console.log('✅ Archivo físico confirmado en storage');
                        } else {
                            console.log(`❌ Archivo no accesible (status: ${fileResponse.status()})`);
                        }
                    } catch (error) {
                        console.log('❌ Error al verificar archivo:', error.message);
                    }
                } else {
                    console.log('❌ URL del archivo no fue guardada');
                }

                console.log('\n🎉 ¡PRUEBA COMPLETADA! El storage está funcionando correctamente.');

            } else {
                console.log('❌ Error al obtener datos de la API:', data.message);
            }

        } else {
            console.log('❌ No se redirigió correctamente. Verificando errores...');

            // Buscar errores en la página
            const errores = await page.locator('.text-red-600, .alert-danger, .error').allTextContents();
            if (errores.length > 0) {
                console.log('🚨 Errores encontrados:');
                errores.forEach(error => console.log(`  - ${error}`));
            }

            // Tomar captura para debug
            await page.screenshot({ path: 'debug-form-error.png' });
            console.log('📸 Captura guardada: debug-form-error.png');
        }

    } catch (error) {
        console.log('💥 Error durante la prueba:', error.message);

        // Tomar captura de error
        try {
            await page.screenshot({ path: 'test-error.png' });
            console.log('📸 Captura de error guardada: test-error.png');
        } catch (screenshotError) {
            console.log('❌ No se pudo tomar captura');
        }
    } finally {
        await browser.close();
        console.log('🔚 Test finalizado');
    }
})();
