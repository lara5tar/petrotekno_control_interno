import { test, expect } from '@playwright/test';
import path from 'path';
import fs from 'fs';

test.describe('Formulario de Personal - Subida de Archivos', () => {
    const baseURL = 'http://127.0.0.1:8000';

    // Configuración de archivos de prueba
    const testFiles = {
        ine: path.resolve('./test-files/test-ine.pdf'),
        curp: path.resolve('./test-files/test-curp.pdf'),
        rfc: path.resolve('./test-files/test-rfc.pdf'),
        nss: path.resolve('./test-files/test-nss.pdf'),
        licencia: path.resolve('./test-files/test-licencia.pdf'),
        comprobante: path.resolve('./test-files/test-comprobante.pdf'),
        cv: path.resolve('./test-files/test-cv.pdf')
    };

    // Crear archivos de prueba si no existen
    test.beforeAll(async () => {
        const testFilesDir = './test-files';
        if (!fs.existsSync(testFilesDir)) {
            fs.mkdirSync(testFilesDir);
        }

        // Crear archivos PDF simples para pruebas
        for (const [type, filePath] of Object.entries(testFiles)) {
            if (!fs.existsSync(filePath)) {
                const pdfContent = `%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Resources <<
/Font <<
/F1 4 0 R
>>
>>
/Contents 5 0 R
>>
endobj

4 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica
>>
endobj

5 0 obj
<<
/Length 44
>>
stream
BT
/F1 12 Tf
100 700 Td
(Test ${type.toUpperCase()} Document) Tj
ET
endstream
endobj

xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000244 00000 n 
0000000317 00000 n 
trailer
<<
/Size 6
/Root 1 0 R
>>
startxref
411
%%EOF`;
                fs.writeFileSync(filePath, pdfContent);
            }
        }
    });

    test('Debe subir archivo INE correctamente y mostrarlo en la vista de detalles', async ({ page }) => {
        // 1. Ir a la página de login
        await page.goto(`${baseURL}/login`);

        // 2. Hacer login (asumo credenciales de test)
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // 3. Esperar a que cargue el dashboard
        await page.waitForURL('**/home');

        // 4. Ir al formulario de crear personal
        await page.goto(`${baseURL}/personal/create`);

        // 5. Llenar los campos básicos obligatorios
        await page.fill('input[name="nombre_completo"]', 'Juan Carlos Pérez García');
        await page.selectOption('select[name="categoria_personal_id"]', { index: 1 }); // Seleccionar primera categoría disponible

        // 6. Llenar campos de documentos
        await page.fill('input[name="numero_ine"]', 'PEJU801015HDFXXX01');
        await page.fill('input[name="curp"]', 'PEGJ801015HDFXXX01');
        await page.fill('input[name="rfc"]', 'PEGJ801015ABC');
        await page.fill('input[name="nss"]', '12345678901');

        // 7. Subir archivo INE
        await page.setInputFiles('input[name="archivo_ine"]', testFiles.ine);

        // 8. Verificar que el estado del archivo se actualiza en el frontend
        await expect(page.locator('[x-text="fileStatus.ine"]')).toContainText('test-ine.pdf');

        // 9. Subir archivo CURP
        await page.setInputFiles('input[name="archivo_curp"]', testFiles.curp);
        await expect(page.locator('[x-text="fileStatus.curp"]')).toContainText('test-curp.pdf');

        // 10. Subir archivo RFC
        await page.setInputFiles('input[name="archivo_rfc"]', testFiles.rfc);
        await expect(page.locator('[x-text="fileStatus.rfc"]')).toContainText('test-rfc.pdf');

        // 11. Subir archivo NSS
        await page.setInputFiles('input[name="archivo_nss"]', testFiles.nss);
        await expect(page.locator('[x-text="fileStatus.nss"]')).toContainText('test-nss.pdf');

        // 12. Llenar dirección y subir comprobante
        await page.fill('textarea[name="direccion_completa"]', 'Calle Falsa 123, Colonia Centro, Ciudad de México, CDMX, CP 01000');
        await page.setInputFiles('input[name="archivo_comprobante_domicilio"]', testFiles.comprobante);
        await expect(page.locator('[x-text="fileStatus.comprobante"]')).toContainText('test-comprobante.pdf');

        // 13. Enviar el formulario
        await page.click('button[type="submit"]');

        // 14. Esperar a ser redirigido a la página de detalles
        await page.waitForURL('**/personal/*');

        // 15. Verificar que estamos en la página de detalles del personal creado
        await expect(page.locator('h2')).toContainText('Juan Carlos Pérez García');

        // 16. Verificar que los datos generales se muestran correctamente
        await expect(page.locator('text=PEJU801015HDFXXX01')).toBeVisible();
        await expect(page.locator('text=PEGJ801015HDFXXX01')).toBeVisible();
        await expect(page.locator('text=PEGJ801015ABC')).toBeVisible();
        await expect(page.locator('text=12345678901')).toBeVisible();

        // 17. Verificar que los botones de "Ver archivo" están presentes y son clicables
        const botonesArchivo = page.locator('button[title="Ver archivo adjunto"]');
        const countBotones = await botonesArchivo.count();
        console.log(`Encontrados ${countBotones} botones de archivo`);

        // Debe haber al menos 4 botones (INE, CURP, RFC, NSS)
        expect(countBotones).toBeGreaterThanOrEqual(4);

        // 18. Verificar que no hay mensajes de "Sin archivo"
        const sinArchivo = page.locator('text=Sin archivo');
        const countSinArchivo = await sinArchivo.count();

        // Verificar que los campos que subimos no tienen "Sin archivo"
        // (puede haber algunos "Sin archivo" para campos que no subimos como licencia)
        expect(countSinArchivo).toBeLessThan(4);

        // 19. Verificar que al menos uno de los botones de archivo funciona
        const primerBoton = botonesArchivo.first();
        if (await primerBoton.isVisible()) {
            await primerBoton.click();
            // Verificar que se abre la ventana de visualización del documento
            // (esto depende de cómo esté implementada la función viewPersonalDocument)
        }

        console.log('✅ Test completado exitosamente: archivo INE subido y mostrado correctamente');
    });

    test('Debe validar archivos demasiado grandes', async ({ page }) => {
        // Crear un archivo demasiado grande (más de 10MB)
        const largeFilePath = './test-files/large-file.pdf';
        const largeContent = 'x'.repeat(11 * 1024 * 1024); // 11MB
        fs.writeFileSync(largeFilePath, largeContent);

        await page.goto(`${baseURL}/login`);
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        await page.goto(`${baseURL}/personal/create`);

        // Intentar subir archivo demasiado grande
        await page.setInputFiles('input[name="archivo_ine"]', largeFilePath);

        // Verificar que aparece mensaje de error
        await expect(page.locator('text=demasiado grande')).toBeVisible();

        // Limpiar archivo de prueba
        fs.unlinkSync(largeFilePath);
    });

    test('Debe validar tipos de archivo incorrectos', async ({ page }) => {
        // Crear un archivo de tipo incorrecto
        const invalidFilePath = './test-files/invalid-file.txt';
        fs.writeFileSync(invalidFilePath, 'Este es un archivo de texto inválido');

        await page.goto(`${baseURL}/login`);
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        await page.goto(`${baseURL}/personal/create`);

        // Intentar subir archivo de tipo incorrecto
        await page.setInputFiles('input[name="archivo_ine"]', invalidFilePath);

        // Verificar que aparece mensaje de error
        await expect(page.locator('text=Tipo de archivo no válido')).toBeVisible();

        // Limpiar archivo de prueba
        fs.unlinkSync(invalidFilePath);
    });

    // Limpiar archivos de prueba después de todos los tests
    test.afterAll(async () => {
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
