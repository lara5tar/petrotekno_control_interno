import { chromium } from 'playwright';
import { test, expect } from '@playwright/test';
import path from 'path';

test.describe('Formulario de Personal - Subida de Archivos', () => {
    test('debe subir archivo INE y guardar datos correctamente', async ({ page }) => {
        // Navegar al formulario de creación
        await page.goto('http://127.0.0.1:8001/personal/create');

        // Esperar a que cargue completamente la página
        await page.waitForLoadState('networkidle');

        // Llenar datos básicos (usando nombres exactos de la BD)
        await page.fill('input[name="nombre_completo"]', 'Juan Carlos Pérez García');

        // Seleccionar categoría
        await page.selectOption('select[name="categoria_personal_id"]', { index: 1 });

        // Llenar campo INE (texto)
        await page.fill('input[name="ine"]', 'INETEST123456789');

        // Subir archivo INE
        const archivoPath = path.join(process.cwd(), 'test_ine.pdf');
        await page.setInputFiles('input[name="archivo_ine"]', archivoPath);

        // Verificar que el archivo fue seleccionado
        const archivoSeleccionado = await page.getAttribute('input[name="archivo_ine"]', 'files');
        console.log('Archivo seleccionado:', archivoSeleccionado);

        // Llenar otros campos opcionales para la prueba
        await page.fill('input[name="curp_numero"]', 'PEGJ801015HDFXXX01');
        await page.fill('input[name="rfc"]', 'PEGJ801015ABC');
        await page.fill('input[name="nss"]', '12345678901');
        await page.fill('input[name="no_licencia"]', 'LIC123456');
        await page.fill('textarea[name="direccion"]', 'Calle Falsa 123, Col. Centro, Ciudad, Estado');

        // Enviar formulario
        await page.click('button[type="submit"]');

        // Esperar redirección y verificar éxito
        await page.waitForLoadState('networkidle');

        // Verificar que se haya creado correctamente
        const url = page.url();
        console.log('URL después del envío:', url);

        // Si se redirigió a show, obtener el ID del personal
        if (url.includes('/personal/')) {
            const personalId = url.split('/personal/')[1];
            console.log('ID del personal creado:', personalId);

            // Verificar datos en la base de datos via API
            const response = await page.request.get(`http://127.0.0.1:8001/web-api/personal/${personalId}`);
            const data = await response.json();

            console.log('Datos del personal desde API:', JSON.stringify(data, null, 2));

            if (data.success) {
                const personal = data.data;

                // Verificar que los datos se guardaron correctamente
                expect(personal.nombre_completo).toBe('Juan Carlos Pérez García');
                expect(personal.ine).toBe('INETEST123456789');
                expect(personal.curp_numero).toBe('PEGJ801015HDFXXX01');
                expect(personal.rfc).toBe('PEGJ801015ABC');
                expect(personal.nss).toBe('12345678901');
                expect(personal.no_licencia).toBe('LIC123456');
                expect(personal.direccion).toBe('Calle Falsa 123, Col. Centro, Ciudad, Estado');

                // Verificar que el archivo se subió y el URL se guardó
                expect(personal.url_ine).toBeTruthy();
                expect(personal.url_ine).toContain('.pdf');

                console.log('✅ Todos los datos se guardaron correctamente');
                console.log('✅ Archivo INE subido correctamente:', personal.url_ine);

                // Verificar que el archivo existe en el sistema
                const archivoResponse = await page.request.get(`http://127.0.0.1:8001/storage/${personal.url_ine}`);
                expect(archivoResponse.status()).toBe(200);
                console.log('✅ Archivo físico existe en storage');

                // Verificar en la página que los datos se muestran correctamente
                await page.goto(`http://127.0.0.1:8001/personal/${personalId}`);
                await page.waitForLoadState('networkidle');

                // Verificar que los datos aparecen en la página
                await expect(page.locator('text=Juan Carlos Pérez García')).toBeVisible();
                await expect(page.locator('text=INETEST123456789')).toBeVisible();
                await expect(page.locator('text=PEGJ801015HDFXXX01')).toBeVisible();
                await expect(page.locator('text=PEGJ801015ABC')).toBeVisible();

                console.log('✅ Datos visibles correctamente en la página de detalle');

            } else {
                throw new Error(`Error al obtener datos: ${data.message}`);
            }
        } else {
            // Si no se redirigió, puede haber un error
            console.log('No se redirigió correctamente. Verificando errores...');
            const errores = await page.locator('.text-red-600, .alert-danger, .error').allTextContents();
            console.log('Errores encontrados:', errores);

            if (errores.length > 0) {
                throw new Error(`Errores en el formulario: ${errores.join(', ')}`);
            }
        }
    });
});
