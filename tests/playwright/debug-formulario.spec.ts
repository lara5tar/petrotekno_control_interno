import { test, expect } from '@playwright/test';

test.describe('Debug Formulario Obras', () => {
    test('debug detallado del envío del formulario', async ({ page }) => {
        console.log('=== DEBUG DETALLADO DEL FORMULARIO ===');

        test.setTimeout(90000);

        // Capturar todos los requests y responses
        const requests = [];
        const responses = [];
        const errors = [];

        page.on('request', request => {
            requests.push({
                url: request.url(),
                method: request.method(),
                postData: request.postData()
            });
            console.log(`📤 REQUEST: ${request.method()} ${request.url()}`);
        });

        page.on('response', response => {
            responses.push({
                url: response.url(),
                status: response.status(),
                statusText: response.statusText()
            });
            if (response.status() >= 400) {
                console.log(`❌ RESPONSE ERROR: ${response.status()} ${response.url()}`);
            }
        });

        page.on('console', msg => {
            if (msg.type() === 'error') {
                errors.push(msg.text());
                console.log(`🚨 CONSOLE ERROR: ${msg.text()}`);
            }
        });

        try {
            // Login
            await page.goto('http://localhost:8000/login');
            await page.fill('#email', 'admin@petrotekno.com');
            await page.fill('#password', 'password123');
            await page.click('button[type="submit"]');
            await page.waitForURL(/.*dashboard.*|.*home.*/);
            console.log('✅ Login exitoso');

            // Ir al formulario
            await page.goto('http://localhost:8000/obras/create');
            await page.waitForLoadState('networkidle');
            await page.waitForTimeout(3000);
            console.log('✅ Formulario cargado');

            // Verificar que Alpine.js está cargado
            const alpineLoaded = await page.evaluate(() => {
                return typeof window.Alpine !== 'undefined';
            });
            console.log(`🔍 Alpine.js cargado: ${alpineLoaded ? 'SÍ' : 'NO'}`);

            // Verificar CSRF token específico del formulario
            const csrfToken = await page.locator('#createObraForm input[name="_token"]').getAttribute('value');
            console.log(`🔐 CSRF Token presente: ${csrfToken ? 'SÍ' : 'NO'}`);

            // Llenar formulario paso a paso
            console.log('📝 Llenando formulario...');

            await page.fill('input[name="nombre_obra"]', 'Debug Test Obra');
            await page.selectOption('select[name="estatus"]', 'planificada');
            await page.fill('input[name="fecha_inicio"]', '2025-08-15');

            // Seleccionar encargado
            const encargadoSelect = page.locator('#encargado_id');
            await encargadoSelect.selectOption('1');
            console.log('✅ Encargado seleccionado');

            // Verificar que el botón de agregar vehículo funciona
            const addButton = page.locator('button:has-text("Agregar Vehículo")');
            await addButton.click();
            await page.waitForTimeout(2000);
            console.log('✅ Botón agregar vehículo clickeado');

            // Verificar que aparece el vehículo
            const vehicleSelect = page.locator('select.vehicle-select').first();
            const vehicleVisible = await vehicleSelect.isVisible();
            console.log(`🔍 Select de vehículo visible: ${vehicleVisible ? 'SÍ' : 'NO'}`);

            if (vehicleVisible) {
                await vehicleSelect.selectOption('1');
                const kilometrajeInput = page.locator('input[name*="kilometraje_inicial"]').first();
                await kilometrajeInput.fill('1000');
                console.log('✅ Vehículo configurado');
            }

            // Screenshot antes del envío
            await page.screenshot({ path: 'debug-antes-envio.png', fullPage: true });

            // Verificar que el botón de envío está presente y habilitado
            const submitButton = page.locator('button[type="submit"]:has-text("Crear Obra")');
            const submitVisible = await submitButton.isVisible();
            const submitEnabled = await submitButton.isEnabled();
            console.log(`🔍 Botón envío visible: ${submitVisible ? 'SÍ' : 'NO'}`);
            console.log(`🔍 Botón envío habilitado: ${submitEnabled ? 'SÍ' : 'NO'}`);

            if (!submitVisible || !submitEnabled) {
                throw new Error('Botón de envío no disponible');
            }

            // Enviar formulario
            console.log('📤 Enviando formulario...');
            await submitButton.click();

            // Esperar un poco para que se procese
            await page.waitForTimeout(10000);

            // Verificar URL después del envío
            const finalUrl = page.url();
            console.log(`📍 URL final: ${finalUrl}`);

            // Verificar si hay mensajes
            const allText = await page.textContent('body');
            const hasSuccessText = allText.includes('exitosamente') || allText.includes('éxito');
            const hasErrorText = allText.includes('error') || allText.includes('Error');

            console.log(`📊 Texto de éxito encontrado: ${hasSuccessText ? 'SÍ' : 'NO'}`);
            console.log(`📊 Texto de error encontrado: ${hasErrorText ? 'SÍ' : 'NO'}`);

            // Screenshot final
            await page.screenshot({ path: 'debug-despues-envio.png', fullPage: true });

            // Resumen de requests
            console.log('\n📡 === RESUMEN DE REQUESTS ===');
            const postRequests = requests.filter(r => r.method === 'POST');
            console.log(`POST requests enviados: ${postRequests.length}`);

            postRequests.forEach((req, i) => {
                console.log(`${i + 1}. ${req.method} ${req.url}`);
                if (req.postData) {
                    console.log(`   Datos: ${req.postData.substring(0, 200)}...`);
                }
            });

            // Resumen de responses con error
            const errorResponses = responses.filter(r => r.status >= 400);
            console.log(`\nResponses con error: ${errorResponses.length}`);
            errorResponses.forEach((resp, i) => {
                console.log(`${i + 1}. ${resp.status} ${resp.statusText} - ${resp.url}`);
            });

            // Resumen de errores JavaScript
            console.log(`\nErrores JavaScript: ${errors.length}`);
            errors.forEach((error, i) => {
                console.log(`${i + 1}. ${error}`);
            });

        } catch (error) {
            console.log(`❌ Error: ${error.message}`);
            await page.screenshot({ path: 'debug-error-final.png', fullPage: true });
            throw error;
        }
    });
});