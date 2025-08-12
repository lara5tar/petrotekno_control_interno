import { test, expect } from '@playwright/test';

test.describe('Test Guardado Encargado y Asignaciones Obra', () => {
    test('verificar que se guardan encargado_id y asignaciones de vehículos', async ({ page }) => {
        console.log('=== TESTING GUARDADO DE ENCARGADO Y ASIGNACIONES ===');

        test.setTimeout(120000);

        try {
            // Capturar requests de red
            const networkRequests = [];
            page.on('request', request => {
                if (request.method() === 'POST' && request.url().includes('/obras')) {
                    networkRequests.push({
                        url: request.url(),
                        method: request.method(),
                        headers: request.headers(),
                        postData: request.postData()
                    });
                    console.log(`📡 POST Request captured: ${request.url()}`);
                }
            });

            // Login
            console.log('🔐 Iniciando login...');
            await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle', timeout: 30000 });

            await page.fill('#email', 'admin@petrotekno.com');
            await page.fill('#password', 'password123');
            await page.click('button[type="submit"]');

            await page.waitForURL(/.*dashboard.*|.*home.*/, { timeout: 30000 });
            console.log('✅ Login exitoso');

            // Ir al formulario de crear obra
            console.log('📝 Navegando a formulario de crear obra...');
            await page.goto('http://localhost:8000/obras/create', {
                waitUntil: 'networkidle',
                timeout: 30000
            });

            await page.waitForTimeout(3000);

            // === LLENAR DATOS BÁSICOS ===
            console.log('📝 Llenando datos básicos...');

            await page.fill('input[name="nombre_obra"]', 'Test Obra con Encargado y Vehículos');
            await page.selectOption('select[name="estatus"]', 'planificada');
            await page.fill('input[name="fecha_inicio"]', '2025-08-15');
            await page.fill('input[name="avance"]', '10');
            await page.fill('textarea[name="observaciones"]', 'Obra de prueba para verificar guardado');

            console.log('✅ Datos básicos llenados');

            // === SELECCIONAR ENCARGADO ===
            console.log('👥 Seleccionando encargado...');

            const encargadoSelect = page.locator('#encargado_id');
            await expect(encargadoSelect).toBeVisible();

            // Verificar opciones de encargados
            const encargadoOptions = await encargadoSelect.locator('option:not([value=""])').count();
            console.log(`📊 Opciones de encargados disponibles: ${encargadoOptions}`);

            if (encargadoOptions > 0) {
                // Seleccionar el primer encargado
                const firstOption = encargadoSelect.locator('option:not([value=""])').first();
                const encargadoId = await firstOption.getAttribute('value');
                const encargadoText = await firstOption.textContent();

                await encargadoSelect.selectOption(encargadoId);
                console.log(`✅ Encargado seleccionado: ID=${encargadoId}, Nombre="${encargadoText?.trim()}"`);
            } else {
                throw new Error('No hay encargados disponibles para seleccionar');
            }

            // === AGREGAR VEHÍCULO ===
            console.log('🚗 Agregando vehículo...');

            const addVehicleButton = page.locator('button:has-text("Agregar Vehículo")');
            await expect(addVehicleButton).toBeVisible();
            await addVehicleButton.click();

            await page.waitForTimeout(2000);

            // Seleccionar vehículo
            const vehicleSelect = page.locator('select.vehicle-select').first();
            await expect(vehicleSelect).toBeVisible();

            const vehicleOptions = await vehicleSelect.locator('option:not([value=""])').count();
            console.log(`📊 Opciones de vehículos disponibles: ${vehicleOptions}`);

            if (vehicleOptions > 0) {
                const firstVehicleOption = vehicleSelect.locator('option:not([value=""])').first();
                const vehicleId = await firstVehicleOption.getAttribute('value');
                const vehicleText = await firstVehicleOption.textContent();

                await vehicleSelect.selectOption(vehicleId);
                console.log(`✅ Vehículo seleccionado: ID=${vehicleId}, Texto="${vehicleText?.trim()}"`);

                // Llenar kilometraje inicial
                const kilometrajeInput = page.locator('input[name*="kilometraje_inicial"]').first();
                await kilometrajeInput.fill('1500');

                // Llenar observaciones del vehículo
                const observacionesVehiculo = page.locator('textarea[name*="observaciones"]').first();
                await observacionesVehiculo.fill('Vehículo asignado para prueba');

                console.log('✅ Datos del vehículo completados');
            } else {
                throw new Error('No hay vehículos disponibles para seleccionar');
            }

            // === TOMAR SCREENSHOT ANTES DE ENVIAR ===
            await page.screenshot({
                path: 'debug-formulario-antes-envio.png',
                fullPage: true
            });
            console.log('📸 Screenshot tomado antes de envío');

            // === ENVIAR FORMULARIO ===
            console.log('📤 Enviando formulario...');

            const submitButton = page.locator('button[type="submit"]:has-text("Crear Obra")');
            await expect(submitButton).toBeVisible();

            // Hacer click y esperar navegación
            await Promise.all([
                page.waitForNavigation({ waitUntil: 'networkidle', timeout: 30000 }),
                submitButton.click()
            ]);

            const currentUrl = page.url();
            console.log(`📍 URL después del envío: ${currentUrl}`);

            // === VERIFICAR DATOS ENVIADOS ===
            console.log('\n📡 === VERIFICANDO DATOS ENVIADOS ===');

            if (networkRequests.length > 0) {
                const postRequest = networkRequests[0];
                console.log(`📤 Request URL: ${postRequest.url}`);
                console.log(`📤 Request Method: ${postRequest.method}`);

                if (postRequest.postData) {
                    console.log('📦 Datos POST enviados:');
                    console.log(postRequest.postData);

                    // Verificar que incluye encargado_id
                    const includesEncargado = postRequest.postData.includes('encargado_id');
                    console.log(`🔍 Incluye encargado_id: ${includesEncargado ? '✅ SÍ' : '❌ NO'}`);

                    // Verificar que incluye datos de vehículos
                    const includesVehiculos = postRequest.postData.includes('vehiculos');
                    console.log(`🔍 Incluye datos de vehículos: ${includesVehiculos ? '✅ SÍ' : '❌ NO'}`);
                } else {
                    console.log('❌ No se capturaron datos POST');
                }
            } else {
                console.log('❌ No se capturaron requests POST');
            }

            // === VERIFICAR RESULTADO ===
            await page.waitForTimeout(3000);

            // Verificar si hay mensajes de éxito/error
            const successMessage = page.locator('.bg-green-100, .alert-success, [role="alert"]:has-text("éxito")');
            const errorMessage = page.locator('.bg-red-100, .alert-danger, [role="alert"]:has-text("error")');

            const hasSuccess = await successMessage.count() > 0;
            const hasError = await errorMessage.count() > 0;

            console.log(`📊 Mensaje de éxito: ${hasSuccess ? '✅ SÍ' : '❌ NO'}`);
            console.log(`📊 Mensaje de error: ${hasError ? '❌ SÍ' : '✅ NO'}`);

            if (hasSuccess) {
                const successText = await successMessage.first().textContent();
                console.log(`✅ Mensaje de éxito: "${successText?.trim()}"`);
            }

            if (hasError) {
                const errorText = await errorMessage.first().textContent();
                console.log(`❌ Mensaje de error: "${errorText?.trim()}"`);
            }

            // === VERIFICAR EN BASE DE DATOS ===
            console.log('\n🗄️ === VERIFICANDO EN BASE DE DATOS ===');

            // Hacer request para verificar la obra creada
            try {
                const apiResponse = await page.request.get('http://localhost:8000/api/obras');
                if (apiResponse.ok()) {
                    const obrasData = await apiResponse.json();
                    console.log(`📊 Total obras en sistema: ${obrasData.data ? obrasData.data.length : 'N/A'}`);

                    if (obrasData.data && obrasData.data.length > 0) {
                        const ultimaObra = obrasData.data[obrasData.data.length - 1];
                        console.log('🔍 Última obra creada:');
                        console.log(`   - ID: ${ultimaObra.id}`);
                        console.log(`   - Nombre: ${ultimaObra.nombre_obra}`);
                        console.log(`   - Encargado ID: ${ultimaObra.encargado_id || 'NO GUARDADO ❌'}`);
                        console.log(`   - Asignaciones: ${ultimaObra.asignaciones ? ultimaObra.asignaciones.length : 'NO GUARDADAS ❌'}`);
                    }
                }
            } catch (e) {
                console.log('⚠️ No se pudo verificar mediante API');
            }

            // === SCREENSHOT FINAL ===
            await page.screenshot({
                path: 'debug-resultado-guardado.png',
                fullPage: true
            });
            console.log('📸 Screenshot final tomado');

            // === RESUMEN ===
            console.log('\n📋 === RESUMEN DEL TEST ===');
            console.log('✅ Formulario llenado correctamente');
            console.log('✅ Encargado seleccionado');
            console.log('✅ Vehículo agregado y configurado');
            console.log('✅ Formulario enviado');
            console.log(`📍 URL final: ${currentUrl}`);
            console.log(`📊 Éxito: ${hasSuccess}`);
            console.log(`📊 Error: ${hasError}`);

        } catch (error) {
            console.log(`❌ Error en test: ${error.message}`);
            await page.screenshot({
                path: 'debug-test-guardado-error.png',
                fullPage: true
            });

            throw error;
        }
    });

    test('verificar estructura del formulario y nombres de campos', async ({ page }) => {
        console.log('=== VERIFICANDO ESTRUCTURA DEL FORMULARIO ===');

        // Login
        await page.goto('http://localhost:8000/login');
        await page.fill('#email', 'admin@petrotekno.com');
        await page.fill('#password', 'password123');
        await page.click('button[type="submit"]');
        await page.waitForURL(/.*dashboard.*|.*home.*/);

        // Ir al formulario
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(3000);

        // Verificar presencia del campo encargado_id
        const encargadoField = page.locator('#encargado_id, input[name="encargado_id"], select[name="encargado_id"]');
        const encargadoExists = await encargadoField.count() > 0;
        console.log(`🔍 Campo encargado_id existe: ${encargadoExists ? '✅ SÍ' : '❌ NO'}`);

        if (encargadoExists) {
            const fieldType = await encargadoField.first().tagName();
            const fieldName = await encargadoField.first().getAttribute('name');
            console.log(`   - Tipo: ${fieldType}`);
            console.log(`   - Name: ${fieldName}`);
        }

        // Agregar vehículo y verificar estructura
        const addButton = page.locator('button:has-text("Agregar Vehículo")');
        if (await addButton.count() > 0) {
            await addButton.click();
            await page.waitForTimeout(2000);

            // Verificar campos de vehículos
            const vehiculoSelect = page.locator('select[name*="vehiculo"]');
            const kilometrajeInput = page.locator('input[name*="kilometraje"]');
            const observacionesTextarea = page.locator('textarea[name*="observaciones"]');

            console.log(`🔍 Campo vehículo existe: ${await vehiculoSelect.count() > 0 ? '✅ SÍ' : '❌ NO'}`);
            console.log(`🔍 Campo kilometraje existe: ${await kilometrajeInput.count() > 0 ? '✅ SÍ' : '❌ NO'}`);
            console.log(`🔍 Campo observaciones existe: ${await observacionesTextarea.count() > 0 ? '✅ SÍ' : '❌ NO'}`);

            if (await vehiculoSelect.count() > 0) {
                const vehiculoName = await vehiculoSelect.first().getAttribute('name');
                console.log(`   - Nombre campo vehículo: ${vehiculoName}`);
            }

            if (await kilometrajeInput.count() > 0) {
                const kilometrajeName = await kilometrajeInput.first().getAttribute('name');
                console.log(`   - Nombre campo kilometraje: ${kilometrajeName}`);
            }
        }

        console.log('✅ Verificación de estructura completada');
    });
});