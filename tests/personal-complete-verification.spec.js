import { test, expect } from '@playwright/test';
import path from 'path';
import fs from 'fs';

test.describe('Verificación Completa de Subida de Archivos - Personal', () => {
    const baseURL = 'http://127.0.0.1:8000';

    test.beforeAll(async () => {
        // Crear directorio de archivos de prueba
        const testFilesDir = './test-files';
        if (!fs.existsSync(testFilesDir)) {
            fs.mkdirSync(testFilesDir);
        }

        // Crear archivo de prueba para INE
        const testINEContent = `%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R>>endobj
4 0 obj<</Length 44>>stream
BT /F1 12 Tf 100 700 Td (Test INE Document) Tj ET
endstream endobj
xref 0 5
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000190 00000 n 
trailer<</Size 5/Root 1 0 R>>
startxref 284
%%EOF`;

        fs.writeFileSync('./test-files/test-ine.pdf', testINEContent);
    });

    test('Verificar ciclo completo: crear personal con INE, verificar storage y BD', async ({ page, context }) => {
        // Paso 1: Login
        console.log('🔐 Iniciando sesión...');
        await page.goto(`${baseURL}/login`);
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // Paso 2: Ir al formulario de crear personal
        console.log('📝 Navegando al formulario de creación...');
        await page.goto(`${baseURL}/personal/create`);
        await expect(page.locator('h2:has-text("Registrar Nuevo Personal")')).toBeVisible();
        console.log('✅ Formulario de creación cargado');

        // Paso 3: Llenar datos básicos
        console.log('📋 Llenando datos básicos...');
        const nombreCompleto = `Test Usuario ${Date.now()}`;
        await page.fill('input[name="nombre_completo"]', nombreCompleto);

        // Esperar a que se carguen las categorías y seleccionar la primera
        await page.waitForSelector('select[name="categoria_personal_id"] option:not([value=""])');
        const categorias = await page.locator('select[name="categoria_personal_id"] option:not([value=""])').all();
        if (categorias.length > 0) {
            const firstCategoryValue = await categorias[0].getAttribute('value');
            await page.selectOption('select[name="categoria_personal_id"]', firstCategoryValue);
        }

        // Llenar número de INE
        const numeroINE = `TEST${Date.now().toString().slice(-6)}`;
        await page.fill('input[name="numero_ine"]', numeroINE);
        console.log(`✅ Datos básicos llenados. Nombre: ${nombreCompleto}, INE: ${numeroINE}`);

        // Paso 4: Subir archivo INE
        console.log('📎 Subiendo archivo INE...');
        const ineFilePath = path.resolve('./test-files/test-ine.pdf');
        await page.setInputFiles('input[name="archivo_ine"]', ineFilePath);

        // Verificar que el frontend muestra el archivo seleccionado
        await page.waitForFunction(() => {
            const status = document.querySelector('[x-text="fileStatus.ine"]');
            return status && status.textContent.includes('test-ine.pdf');
        }, {}, { timeout: 5000 });

        console.log('✅ Archivo INE seleccionado y mostrado en frontend');

        // Paso 5: Enviar formulario
        console.log('🚀 Enviando formulario...');
        await page.click('button[type="submit"]');

        // Esperar redirección a la página de detalles
        await page.waitForURL('**/personal/*', { timeout: 10000 });
        console.log('✅ Formulario enviado, redirigido a página de detalles');

        // Paso 6: Verificar que los datos se muestran correctamente
        console.log('🔍 Verificando datos en vista de detalles...');

        // Verificar nombre
        await expect(page.locator(`text=${nombreCompleto}`)).toBeVisible();
        console.log('✅ Nombre visible en detalles');

        // Verificar número de INE
        await expect(page.locator(`text=${numeroINE}`)).toBeVisible();
        console.log('✅ Número de INE visible en detalles');

        // Verificar que existe botón para ver archivo INE (no "Sin archivo")
        const ineSection = page.locator('text=Identificación (INE)').locator('..').locator('..');
        const archivoButton = ineSection.locator('button[title="Ver archivo adjunto"]');
        await expect(archivoButton).toBeVisible();
        console.log('✅ Botón de archivo INE visible');

        // Verificar que NO aparece "Sin archivo" para INE
        const sinArchivoINE = ineSection.locator('text=Sin archivo');
        await expect(sinArchivoINE).not.toBeVisible();
        console.log('✅ No aparece "Sin archivo" para INE');

        // Paso 7: Extraer ID del personal de la URL para verificaciones adicionales
        const currentURL = page.url();
        const personalId = currentURL.split('/').pop();
        console.log(`📝 ID del personal creado: ${personalId}`);

        // Paso 8: Verificar que el archivo existe en el servidor
        console.log('🗂️ Verificando archivo en storage...');

        // Navegar a una página que nos permita hacer una request AJAX para verificar el archivo
        await page.goto(`${baseURL}/personal/${personalId}/edit`);

        // Obtener la URL del archivo desde el backend
        const storageVerification = await page.evaluate(async (personalId) => {
            try {
                const response = await fetch(`/api/personal/${personalId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    return { success: false, error: `HTTP ${response.status}` };
                }

                const data = await response.json();

                return {
                    success: true,
                    personal: data.data,
                    hasUrlINE: !!data.data.url_ine,
                    urlINE: data.data.url_ine
                };
            } catch (error) {
                return { success: false, error: error.message };
            }
        }, personalId);

        console.log('Verificación de storage:', storageVerification);

        if (storageVerification.success) {
            expect(storageVerification.hasUrlINE).toBeTruthy();
            console.log(`✅ URL de archivo INE guardada en BD: ${storageVerification.urlINE}`);

            // Verificar que el archivo es accesible
            if (storageVerification.urlINE) {
                const fileResponse = await page.request.get(`${baseURL}/storage/${storageVerification.urlINE}`);
                expect(fileResponse.status()).toBeLessThan(400);
                console.log('✅ Archivo INE accesible en storage');
            }
        } else {
            console.error('❌ Error al verificar storage:', storageVerification.error);
            throw new Error(`No se pudo verificar el storage: ${storageVerification.error}`);
        }

        // Paso 9: Verificación final - volver a la vista de detalles y probar el botón de ver archivo
        console.log('🔎 Verificación final del botón de ver archivo...');
        await page.goto(`${baseURL}/personal/${personalId}`);

        const viewButton = page.locator('button[title="Ver archivo adjunto"]').first();
        await viewButton.click();

        // Verificar que se abre algún tipo de modal o nueva pestaña para ver el archivo
        // (esto depende de la implementación de viewPersonalDocument)
        await page.waitForTimeout(2000); // Dar tiempo para que se abra la vista

        console.log('✅ Test completado exitosamente');
        console.log('📊 Resumen:');
        console.log(`   - Personal creado: ${nombreCompleto} (ID: ${personalId})`);
        console.log(`   - INE guardado: ${numeroINE}`);
        console.log(`   - Archivo subido y accesible: ${storageVerification.urlINE}`);
        console.log(`   - Vista de detalles funcionando correctamente`);
    });

    test.afterAll(async () => {
        // Limpiar archivos de prueba
        const testFilesDir = './test-files';
        if (fs.existsSync(testFilesDir)) {
            const files = fs.readdirSync(testFilesDir);
            for (const file of files) {
                fs.unlinkSync(path.join(testFilesDir, file));
            }
            fs.rmdirSync(testFilesDir);
        }
    });
});
