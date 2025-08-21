import { chromium } from 'playwright';
import path from 'path';

(async () => {
    console.log('🚀 Iniciando test de formulario de personal...');

    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // Navegar al formulario de creación
        console.log('📱 Navegando al formulario de creación...');
        await page.goto('http://127.0.0.1:8000/personal/create');

        // Esperar a que cargue completamente la página
        await page.waitForLoadState('networkidle');
        console.log('✅ Página cargada');

        // Llenar datos básicos (usando nombres exactos de la BD)
        console.log('📝 Llenando datos básicos...');
        await page.fill('input[name="nombre_completo"]', 'Juan Carlos Pérez García');

        // Seleccionar categoría
        await page.selectOption('select[name="categoria_personal_id"]', { index: 1 });

        // Llenar campo INE (texto)
        await page.fill('input[name="ine"]', 'INETEST123456789');

        // Subir archivo INE
        console.log('📎 Subiendo archivo INE...');
        const archivoPath = path.join(process.cwd(), 'test_ine.pdf');
        await page.setInputFiles('input[name="archivo_ine"]', archivoPath);

        // Llenar otros campos opcionales para la prueba
        console.log('📝 Llenando campos adicionales...');
        await page.fill('input[name="curp_numero"]', 'PEGJ801015HDFXXX01');
        await page.fill('input[name="rfc"]', 'PEGJ801015ABC');
        await page.fill('input[name="nss"]', '12345678901');
        await page.fill('input[name="no_licencia"]', 'LIC123456');
        await page.fill('textarea[name="direccion"]', 'Calle Falsa 123, Col. Centro, Ciudad, Estado');

        // Enviar formulario
        console.log('📤 Enviando formulario...');
        await page.click('button[type="submit"]');

        // Esperar redirección y verificar éxito
        await page.waitForLoadState('networkidle');

        // Verificar que se haya creado correctamente
        const url = page.url();
        console.log('🔗 URL después del envío:', url);

        // Si se redirigió a show, obtener el ID del personal
        if (url.includes('/personal/')) {
            const personalId = url.split('/personal/')[1];
            console.log('🆔 ID del personal creado:', personalId);

            // Verificar datos en la base de datos via API
            console.log('🔍 Verificando datos via API...');
            const response = await page.request.get(`http://127.0.0.1:8000/web-api/personal/${personalId}`);
            const data = await response.json();

            console.log('📊 Datos del personal desde API:', JSON.stringify(data, null, 2));

            if (data.success) {
                const personal = data.data;

                // Verificar que los datos se guardaron correctamente
                console.log('✅ Verificando datos guardados...');

                const verificaciones = [
                    { campo: 'nombre_completo', esperado: 'Juan Carlos Pérez García', actual: personal.nombre_completo },
                    { campo: 'ine', esperado: 'INETEST123456789', actual: personal.ine },
                    { campo: 'curp_numero', esperado: 'PEGJ801015HDFXXX01', actual: personal.curp_numero },
                    { campo: 'rfc', esperado: 'PEGJ801015ABC', actual: personal.rfc },
                    { campo: 'nss', esperado: '12345678901', actual: personal.nss },
                    { campo: 'no_licencia', esperado: 'LIC123456', actual: personal.no_licencia },
                    { campo: 'direccion', esperado: 'Calle Falsa 123, Col. Centro, Ciudad, Estado', actual: personal.direccion },
                ];

                let todosCorrectos = true;
                for (const v of verificaciones) {
                    if (v.actual === v.esperado) {
                        console.log(`✅ ${v.campo}: ${v.actual}`);
                    } else {
                        console.log(`❌ ${v.campo}: esperado "${v.esperado}", actual "${v.actual}"`);
                        todosCorrectos = false;
                    }
                }

                // Verificar que el archivo se subió y el URL se guardó
                if (personal.url_ine && personal.url_ine.includes('.pdf')) {
                    console.log('✅ Archivo INE subido correctamente:', personal.url_ine);

                    // Verificar que el archivo existe en el sistema
                    try {
                        const archivoResponse = await page.request.get(`http://127.0.0.1:8000/storage/${personal.url_ine}`);
                        if (archivoResponse.status() === 200) {
                            console.log('✅ Archivo físico existe en storage');
                        } else {
                            console.log(`❌ Archivo no encontrado en storage (status: ${archivoResponse.status()})`);
                            todosCorrectos = false;
                        }
                    } catch (error) {
                        console.log('❌ Error al verificar archivo en storage:', error.message);
                        todosCorrectos = false;
                    }
                } else {
                    console.log('❌ URL del archivo INE no se guardó correctamente:', personal.url_ine);
                    todosCorrectos = false;
                }

                // Verificar en la página que los datos se muestran correctamente
                console.log('🔍 Verificando visualización en la página...');
                await page.goto(`http://127.0.0.1:8000/personal/${personalId}`);
                await page.waitForLoadState('networkidle');

                // Verificar que los datos aparecen en la página
                const datosVisibles = await Promise.all([
                    page.locator('text=Juan Carlos Pérez García').isVisible(),
                    page.locator('text=INETEST123456789').isVisible(),
                    page.locator('text=PEGJ801015HDFXXX01').isVisible(),
                    page.locator('text=PEGJ801015ABC').isVisible(),
                ]);

                if (datosVisibles.every(visible => visible)) {
                    console.log('✅ Datos visibles correctamente en la página de detalle');
                } else {
                    console.log('❌ Algunos datos no son visibles en la página');
                    todosCorrectos = false;
                }

                if (todosCorrectos) {
                    console.log('🎉 ¡TODAS LAS PRUEBAS PASARON EXITOSAMENTE!');
                } else {
                    console.log('💥 Algunas pruebas fallaron');
                }

            } else {
                console.log('❌ Error al obtener datos:', data.message);
            }
        } else {
            // Si no se redirigió, puede haber un error
            console.log('❌ No se redirigió correctamente. Verificando errores...');
            const errores = await page.locator('.text-red-600, .alert-danger, .error').allTextContents();
            console.log('🚨 Errores encontrados:', errores);
        }

    } catch (error) {
        console.log('💥 Error durante el test:', error);
    } finally {
        console.log('🔚 Cerrando navegador...');
        await browser.close();
    }
})();
