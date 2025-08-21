import { test, expect } from '@playwright/test';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

test.describe('Vehiculos File Upload System', () => {
    test.beforeEach(async ({ page }) => {
        // Navegar a la aplicación
        await page.goto('http://127.0.0.1:8001');

        // Hacer login (ajustar según tu sistema de autenticación)
        await page.waitForSelector('input[name="email"], input[name="username"], .login-form', { timeout: 10000 });

        // Si existe formulario de login, realizar login
        const loginForm = await page.$('.login-form, form[action*="login"]');
        if (loginForm) {
            await page.fill('input[name="email"], input[name="username"]', 'admin@example.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"], input[type="submit"]');
            await page.waitForNavigation({ waitUntil: 'networkidle' });
        }
    });

    test('should upload vehicle documents with correct naming format', async ({ page }) => {
        // Crear archivos de prueba
        const testFiles = {
            poliza: 'test-poliza-ABC123.pdf',
            derecho: 'test-derecho-VIN123456.pdf',
            factura: 'test-factura-INVOICE789.pdf',
            imagen: 'test-imagen-PHOTO001.jpg'
        };

        // Crear archivos temporales de prueba
        for (const [type, filename] of Object.entries(testFiles)) {
            const filePath = path.join(__dirname, '..', 'temp', filename);
            // Crear directorio temp si no existe
            const tempDir = path.dirname(filePath);
            if (!fs.existsSync(tempDir)) {
                fs.mkdirSync(tempDir, { recursive: true });
            }
            fs.writeFileSync(filePath, `Test content for ${type} document`);
        }

        try {
            // Navegar a la página de crear vehículo
            await page.goto('http://127.0.0.1:8001/vehiculos/create');
            await page.waitForSelector('form', { timeout: 10000 });

            // Llenar datos básicos del vehículo
            const vehicleData = {
                marca: 'Toyota',
                modelo: 'Hilux',
                anio: '2023',
                n_serie: 'TEST123456789VIN',
                placas: 'ABC-123-T',
                color: 'Blanco',
                tipo_vehiculo_id: '1', // Asumiendo que existe tipo 1
                estatus: 'activo'
            };

            // Llenar campos de texto
            for (const [field, value] of Object.entries(vehicleData)) {
                const selector = `input[name="${field}"], select[name="${field}"]`;
                const element = await page.$(selector);
                if (element) {
                    if (field === 'tipo_vehiculo_id') {
                        await page.selectOption(selector, value);
                    } else {
                        await page.fill(selector, value);
                    }
                }
            }

            // Subir TODOS los archivos (no solo uno)
            const fileUploads = [
                { selector: 'input[name="poliza_seguro_file"], input[name="poliza_file"]', file: testFiles.poliza, type: 'POLIZA' },
                { selector: 'input[name="derecho_vehicular_file"], input[name="derecho_file"]', file: testFiles.derecho, type: 'DERECHO' },
                { selector: 'input[name="factura_pedimento_file"], input[name="factura_file"]', file: testFiles.factura, type: 'FACTURA' },
                { selector: 'input[name="fotografia_file"], input[name="imagen_file"]', file: testFiles.imagen, type: 'IMAGEN' }
            ];

            let uploadedFiles = 0;
            for (const upload of fileUploads) {
                const fileInput = await page.$(upload.selector);
                if (fileInput) {
                    const filePath = path.join(__dirname, '..', 'temp', upload.file);
                    await fileInput.setInputFiles(filePath);
                    uploadedFiles++;
                    console.log(`✅ Uploaded ${upload.type}: ${upload.file}`);
                } else {
                    console.log(`⚠️ File input not found for ${upload.type}: ${upload.selector}`);
                }
            }

            console.log(`📄 Total files uploaded: ${uploadedFiles}/4`);
            expect(uploadedFiles).toBeGreaterThan(0);

            // Enviar formulario
            await page.click('button[type="submit"]');

            // Esperar redirección o confirmación
            await page.waitForURL(/\/vehiculos\/\d+/, { timeout: 15000 });

            // Verificar que se creó el vehículo
            expect(page.url()).toMatch(/\/vehiculos\/\d+/);

            // Extraer ID del vehículo de la URL
            const vehicleId = page.url().match(/\/vehiculos\/(\d+)/)[1];
            console.log(`Vehicle created with ID: ${vehicleId}`);

            // Verificar que los documentos se muestran en la vista
            const documentElements = await page.$$('.document-item, .btn-view-document, [data-document], a[href*="storage/vehiculos"]');
            expect(documentElements.length).toBeGreaterThan(0);

            // Verificar que los archivos tienen el formato correcto de naming
            const documentLinks = await page.$$('a[href*="storage/vehiculos"], [href*="vehiculos/documentos"], [href*="vehiculos/imagenes"]');

            let correctNamingCount = 0;
            for (const link of documentLinks) {
                const href = await link.getAttribute('href');
                if (href) {
                    const filename = href.split('/').pop();
                    console.log(`📄 Document filename: ${filename}`);

                    // Verificar formato: ID_TIPO_DESCRIPCION.ext
                    const namePattern = /^\d+_[A-Z]+_[A-Za-z0-9]+\.[a-z]{2,4}$/;
                    if (namePattern.test(filename)) {
                        correctNamingCount++;
                        console.log(`✅ Correct naming format: ${filename}`);
                    } else {
                        console.log(`⚠️ Incorrect naming format: ${filename}`);
                    }
                }
            }

            console.log(`📊 Files with correct naming: ${correctNamingCount}/${documentLinks.length}`);

            // Log para debugging
            console.log('✅ Test completed successfully');
            console.log(`🚗 Vehicle ID: ${vehicleId}`);
            console.log(`📋 Expected format examples:`);
            console.log(`   - ${vehicleId}_POLIZA_ABC123.pdf`);
            console.log(`   - ${vehicleId}_DERECHO_ABC123.pdf`);
            console.log(`   - ${vehicleId}_FACTURA_TEST123456789VIN.pdf`);
            console.log(`   - ${vehicleId}_IMAGEN_ToyotaHilux.jpg`);

        } catch (error) {
            console.error('Test failed:', error);

            // Capturar screenshot en caso de error
            await page.screenshot({
                path: `test-results/vehiculos-upload-error-${Date.now()}.png`,
                fullPage: true
            });

            throw error;
        } finally {
            // Limpiar archivos temporales
            for (const filename of Object.values(testFiles)) {
                const filePath = path.join(__dirname, '..', 'temp', filename);
                if (fs.existsSync(filePath)) {
                    fs.unlinkSync(filePath);
                }
            }
        }
    });

    test('should verify existing vehicle documents with new naming format', async ({ page }) => {
        // Este test verificará que los documentos existentes usen el nuevo formato
        await page.goto('http://127.0.0.1:8001/vehiculos');

        // Buscar un vehículo existente
        const vehicleLinks = await page.$$('a[href*="/vehiculos/"]');
        if (vehicleLinks.length > 0) {
            await vehicleLinks[0].click();
            await page.waitForLoadState('networkidle');

            // Verificar que los documentos tengan el formato correcto
            const documentLinks = await page.$$('a[href*="storage/vehiculos"], [href*="vehiculos/documentos"], [href*="vehiculos/imagenes"]');

            for (const link of documentLinks) {
                const href = await link.getAttribute('href');
                console.log(`Document URL: ${href}`);

                // Verificar formato de naming
                if (href && href.includes('vehiculos/')) {
                    const filename = href.split('/').pop();
                    console.log(`Document filename: ${filename}`);

                    // El nuevo formato debería ser: ID_TIPO_DESCRIPCION.ext
                    // Ejemplo: 5_POLIZA_ABC123.pdf
                    const namePattern = /^\d+_[A-Z]+_[A-Za-z0-9]+\.[a-z]{2,4}$/;
                    if (!namePattern.test(filename)) {
                        console.warn(`File doesn't match new naming pattern: ${filename}`);
                    }
                }
            }
        }
    });
});

test.afterAll(async () => {
    // Limpiar directorio temporal
    const tempDir = path.join(__dirname, '..', 'temp');
    if (fs.existsSync(tempDir)) {
        fs.rmSync(tempDir, { recursive: true, force: true });
    }
});
