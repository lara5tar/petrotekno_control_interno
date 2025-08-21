import { test, expect } from '@playwright/test';
import fs from 'fs';
import path from 'path';

test.describe('Test de PDF con Autenticación', () => {
    test('debe generar PDF después del login exitoso', async ({ page }) => {
        console.log('🔄 Iniciando test de PDF con autenticación...');

        // Paso 1: Ir a la página de login
        await page.goto('http://127.0.0.1:8000/login');
        console.log('📄 Navegando a la página de login');

        // Paso 2: Llenar el formulario de login
        await page.fill('input[name="email"]', 'test@petrotekno.com');
        await page.fill('input[name="password"]', 'test123');
        console.log('📝 Credenciales ingresadas');

        // Paso 3: Hacer clic en el botón de login
        await page.click('button[type="submit"]');
        console.log('🔐 Enviando formulario de login');

        // Paso 4: Esperar a que se complete el login (puede redirigir al dashboard)
        await page.waitForLoadState('networkidle');
        console.log('⏳ Esperando carga completa después del login');

        // Paso 5: Verificar que no estamos en la página de login
        const url = page.url();
        console.log(`📍 URL actual después del login: ${url}`);

        if (url.includes('/login')) {
            console.log('❌ Login falló, aún estamos en la página de login');
            const content = await page.content();
            if (content.includes('error') || content.includes('invalid')) {
                console.log('🚨 Posibles errores de login detectados en la página');
            }
            // Si el login falla, detener el test
            throw new Error('Login falló - credenciales incorrectas o problema de autenticación');
        }

        // Paso 6: Navegar a reportes
        console.log('🗂️ Navegando a la página de reportes...');
        await page.goto('http://127.0.0.1:8000/reportes');
        await page.waitForLoadState('networkidle');

        // Paso 7: Verificar que podemos acceder a reportes
        const reportesContent = await page.content();
        if (reportesContent.includes('login') || reportesContent.includes('Iniciar Sesión')) {
            console.log('⚠️ Aún no autenticado, intentando acceso directo al PDF...');
        } else {
            console.log('✅ Acceso exitoso a la página de reportes');
            expect(reportesContent).toContain('Inventario de Vehículos');
        }

        // Paso 8: Configurar el manejo de descarga ANTES de hacer la petición
        const downloadPromise = page.waitForEvent('download', { timeout: 30000 });

        // Paso 9: Navegar a la URL del PDF (esto debería trigger la descarga)
        console.log('📊 Intentando generar PDF...');

        try {
            // En lugar de page.goto, usar page.evaluate para hacer la petición
            await page.evaluate(() => {
                window.location.href = 'http://127.0.0.1:8000/reportes/inventario-vehiculos?formato=pdf';
            });

            // Esperar la descarga
            const download = await downloadPromise;
            console.log('✅ PDF descargado exitosamente');

            // Verificar que el archivo fue descargado
            const filename = download.suggestedFilename();
            console.log(`📄 Nombre del archivo: ${filename}`);
            expect(filename).toMatch(/inventario_vehiculos_.*\.pdf/);

            // Guardar el archivo para inspección
            const downloadsPath = './test-results/downloads';
            if (!fs.existsSync(downloadsPath)) {
                fs.mkdirSync(downloadsPath, { recursive: true });
            }

            const savePath = path.join(downloadsPath, filename);
            await download.saveAs(savePath);
            console.log(`💾 PDF guardado en: ${savePath}`);

            // Verificar que el archivo existe y tiene contenido
            const stats = fs.statSync(savePath);
            console.log(`📏 Tamaño del archivo: ${stats.size} bytes`);
            expect(stats.size).toBeGreaterThan(1000); // El PDF debe tener al menos 1KB

        } catch (error) {
            if (error.message.includes('ERR_ABORTED')) {
                console.log('⚠️ Navegación abortada (posiblemente por descarga) - esto es normal para PDFs');

                // Intentar esperar la descarga de todas formas
                try {
                    const download = await downloadPromise;
                    console.log('✅ PDF descargado exitosamente después de ERR_ABORTED');

                    const filename = download.suggestedFilename();
                    console.log(`� Nombre del archivo: ${filename}`);
                    expect(filename).toMatch(/inventario_vehiculos_.*\.pdf/);

                } catch (downloadError) {
                    console.log('❌ No se pudo completar la descarga:', downloadError.message);
                    throw downloadError;
                }
            } else {
                console.log('⚠️ Error inesperado:', error.message);
                throw error;
            }
        }
    });

    test('debe verificar que el endpoint responde correctamente', async ({ page }) => {
        console.log('🔄 Verificando respuesta del endpoint...');

        // Intentar acceso sin autenticación para verificar redirección
        const response = await page.goto('http://127.0.0.1:8000/reportes/inventario-vehiculos?formato=pdf');
        console.log(`📊 Status de respuesta: ${response?.status()}`);
        console.log(`📍 URL final: ${page.url()}`);

        // Si nos redirige al login, es esperado
        if (page.url().includes('/login')) {
            console.log('✅ Redirección a login correcta (endpoint protegido)');
            expect(page.url()).toContain('/login');
        } else {
            // Si no redirige, verificar que no hay errores 500
            expect(response?.status()).not.toBe(500);
            console.log('✅ No hay errores 500 en el endpoint');
        }
    });
});
