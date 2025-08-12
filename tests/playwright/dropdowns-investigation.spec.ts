import { test, expect } from '@playwright/test';

test.describe('Investigación Dropdowns Obra - Vehículos y Personal', () => {
    test('verificar todos los vehículos y personal en dropdowns', async ({ page }) => {
        console.log('=== INVESTIGANDO DROPDOWNS DE VEHÍCULOS Y PERSONAL ===');

        test.setTimeout(120000);

        try {
            // Capturar errores de consola y red
            const consoleErrors = [];
            const networkErrors = [];

            page.on('console', msg => {
                if (msg.type() === 'error') {
                    consoleErrors.push(msg.text());
                    console.log(`❌ Console Error: ${msg.text()}`);
                }
            });

            page.on('response', response => {
                if (!response.ok() && response.status() >= 400) {
                    networkErrors.push(`${response.status()} - ${response.url()}`);
                    console.log(`❌ Network Error: ${response.status()} - ${response.url()}`);
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

            // Navegar al formulario de crear obra
            console.log('📝 Navegando a formulario de crear obra...');
            await page.goto('http://localhost:8000/obras/create', {
                waitUntil: 'networkidle',
                timeout: 30000
            });

            // Verificar que no estamos en login
            const currentUrl = page.url();
            if (currentUrl.includes('/login')) {
                throw new Error('Redirigido al login - problema de autenticación');
            }
            console.log(`✅ URL actual: ${currentUrl}`);

            // Esperar a que la página cargue completamente
            await page.waitForTimeout(5000);

            // === INVESTIGAR DROPDOWN DE ENCARGADOS ===
            console.log('\n👥 === INVESTIGANDO DROPDOWN DE ENCARGADOS ===');

            const encargadoSelect = page.locator('#encargado_id');

            // Verificar si el dropdown existe
            const encargadoExists = await encargadoSelect.count();
            console.log(`Dropdown encargado existe: ${encargadoExists > 0 ? 'SÍ' : 'NO'}`);

            if (encargadoExists > 0) {
                await expect(encargadoSelect).toBeVisible({ timeout: 10000 });

                // Contar todas las opciones
                const allEncargadoOptions = await encargadoSelect.locator('option').count();
                const validEncargadoOptions = await encargadoSelect.locator('option:not([value=""])').count();

                console.log(`📊 Total opciones encargados: ${allEncargadoOptions}`);
                console.log(`📊 Opciones válidas encargados: ${validEncargadoOptions}`);

                // Listar todas las opciones de encargados
                const encargadoOptions = await encargadoSelect.locator('option').all();
                console.log('\n📋 LISTA DE ENCARGADOS DISPONIBLES:');
                for (let i = 0; i < encargadoOptions.length; i++) {
                    const option = encargadoOptions[i];
                    const value = await option.getAttribute('value');
                    const text = await option.textContent();
                    console.log(`   ${i + 1}. Valor: "${value}" - Texto: "${text?.trim()}"`);
                }
            } else {
                console.log('❌ Dropdown de encargados NO encontrado');
            }

            // === INVESTIGAR SECCIÓN DE VEHÍCULOS ===
            console.log('\n🚗 === INVESTIGANDO SECCIÓN DE VEHÍCULOS ===');

            // Buscar botón "Agregar Vehículo"
            const addVehicleButton = page.locator('button:has-text("Agregar Vehículo"), #addVehicleBtn');
            const addButtonExists = await addVehicleButton.count();
            console.log(`Botón "Agregar Vehículo" existe: ${addButtonExists > 0 ? 'SÍ' : 'NO'}`);

            if (addButtonExists > 0) {
                await expect(addVehicleButton).toBeVisible({ timeout: 10000 });
                console.log('✅ Botón "Agregar Vehículo" visible');

                // Hacer clic para agregar vehículo
                await addVehicleButton.click();
                console.log('✅ Botón "Agregar Vehículo" clickeado');

                await page.waitForTimeout(3000);

                // Buscar el dropdown de vehículos en la nueva tarjeta
                const vehicleSelects = page.locator('select.vehicle-select, select[name*="vehiculo"], select[data-vehicle-select]');
                const vehicleSelectCount = await vehicleSelects.count();
                console.log(`Dropdowns de vehículos encontrados: ${vehicleSelectCount}`);

                if (vehicleSelectCount > 0) {
                    const vehicleSelect = vehicleSelects.first();
                    await expect(vehicleSelect).toBeVisible({ timeout: 10000 });

                    // Contar opciones de vehículos
                    const allVehicleOptions = await vehicleSelect.locator('option').count();
                    const validVehicleOptions = await vehicleSelect.locator('option:not([value=""])').count();

                    console.log(`📊 Total opciones vehículos: ${allVehicleOptions}`);
                    console.log(`📊 Opciones válidas vehículos: ${validVehicleOptions}`);

                    // Listar todas las opciones de vehículos
                    const vehicleOptions = await vehicleSelect.locator('option').all();
                    console.log('\n📋 LISTA DE VEHÍCULOS DISPONIBLES:');
                    for (let i = 0; i < vehicleOptions.length; i++) {
                        const option = vehicleOptions[i];
                        const value = await option.getAttribute('value');
                        const text = await option.textContent();
                        console.log(`   ${i + 1}. Valor: "${value}" - Texto: "${text?.trim()}"`);
                    }

                    // Verificar si las opciones tienen datos adicionales
                    console.log('\n🔍 INVESTIGANDO DATOS DE VEHÍCULOS:');
                    for (let i = 0; i < Math.min(vehicleOptions.length, 5); i++) {
                        const option = vehicleOptions[i];
                        const value = await option.getAttribute('value');
                        if (value && value !== "") {
                            const dataMarca = await option.getAttribute('data-marca');
                            const dataModelo = await option.getAttribute('data-modelo');
                            const dataPlacas = await option.getAttribute('data-placas');
                            const dataKilometraje = await option.getAttribute('data-kilometraje');

                            console.log(`   Vehículo ${value}:`);
                            console.log(`     - Marca: ${dataMarca || 'N/A'}`);
                            console.log(`     - Modelo: ${dataModelo || 'N/A'}`);
                            console.log(`     - Placas: ${dataPlacas || 'N/A'}`);
                            console.log(`     - Kilometraje: ${dataKilometraje || 'N/A'}`);
                        }
                    }
                } else {
                    console.log('❌ No se encontraron dropdowns de vehículos después de agregar');
                }
            } else {
                console.log('❌ Botón "Agregar Vehículo" NO encontrado');
            }

            // === VERIFICAR DATOS EN EL DOM ===
            console.log('\n🔍 === VERIFICANDO DATOS EN EL DOM ===');

            // Buscar scripts o variables JavaScript que contengan datos
            const scripts = await page.locator('script').all();
            console.log(`Scripts encontrados: ${scripts.length}`);

            // Verificar si hay datos de vehículos/encargados en variables JavaScript
            const hasVehiculosData = await page.evaluate(() => {
                return typeof window.vehiculos !== 'undefined' ||
                    typeof vehiculos !== 'undefined' ||
                    document.querySelector('[data-vehiculos]') !== null;
            });

            const hasEncargadosData = await page.evaluate(() => {
                return typeof window.encargados !== 'undefined' ||
                    typeof encargados !== 'undefined' ||
                    document.querySelector('[data-encargados]') !== null;
            });

            console.log(`Datos de vehículos en JS: ${hasVehiculosData ? 'SÍ' : 'NO'}`);
            console.log(`Datos de encargados en JS: ${hasEncargadosData ? 'SÍ' : 'NO'}`);

            // === VERIFICAR ERRORES DE RED ===
            console.log('\n🌐 === VERIFICANDO ERRORES DE RED ===');
            console.log(`Errores de consola: ${consoleErrors.length}`);
            console.log(`Errores de red: ${networkErrors.length}`);

            if (consoleErrors.length > 0) {
                console.log('❌ ERRORES DE CONSOLA:');
                consoleErrors.forEach((error, i) => console.log(`   ${i + 1}. ${error}`));
            }

            if (networkErrors.length > 0) {
                console.log('❌ ERRORES DE RED:');
                networkErrors.forEach((error, i) => console.log(`   ${i + 1}. ${error}`));
            }

            // === VERIFICAR CÓDIGO FUENTE ===
            console.log('\n📄 === VERIFICANDO CÓDIGO FUENTE ===');

            const pageContent = await page.content();
            const hasEncargadosInHTML = pageContent.includes('encargado') || pageContent.includes('Encargado');
            const hasVehiculosInHTML = pageContent.includes('vehiculo') || pageContent.includes('Vehículo');

            console.log(`Menciones de encargados en HTML: ${hasEncargadosInHTML ? 'SÍ' : 'NO'}`);
            console.log(`Menciones de vehículos en HTML: ${hasVehiculosInHTML ? 'SÍ' : 'NO'}`);

            // === SCREENSHOT PARA ANÁLISIS ===
            await page.screenshot({
                path: 'debug-dropdowns-investigacion.png',
                fullPage: true
            });
            console.log('📸 Screenshot de investigación guardado: debug-dropdowns-investigacion.png');

            // === RESUMEN DE INVESTIGACIÓN ===
            console.log('\n📋 === RESUMEN DE INVESTIGACIÓN ===');
            console.log('✅ Login exitoso');
            console.log(`✅ Formulario cargado en: ${currentUrl}`);
            console.log(`📊 Encargados encontrados: ${encargadoExists > 0 ? 'SÍ' : 'NO'}`);
            console.log(`📊 Botón vehículos encontrado: ${addButtonExists > 0 ? 'SÍ' : 'NO'}`);
            console.log(`⚠️  Errores de consola: ${consoleErrors.length}`);
            console.log(`⚠️  Errores de red: ${networkErrors.length}`);

        } catch (error) {
            console.log(`❌ Error en investigación: ${error.message}`);
            await page.screenshot({
                path: 'debug-dropdowns-error.png',
                fullPage: true
            });

            const url = page.url();
            console.log(`URL actual al fallar: ${url}`);

            throw error;
        }
    });

    test('verificar datos del backend para dropdowns', async ({ page }) => {
        console.log('=== VERIFICANDO DATOS DEL BACKEND ===');

        // Login
        await page.goto('http://localhost:8000/login');
        await page.fill('#email', 'admin@petrotekno.com');
        await page.fill('#password', 'password123');
        await page.click('button[type="submit"]');
        await page.waitForURL(/.*dashboard.*|.*home.*/);

        // Interceptar requests para ver qué datos se están enviando/recibiendo
        const responses = [];

        page.on('response', async (response) => {
            const url = response.url();
            if (url.includes('/obras') || url.includes('/vehiculo') || url.includes('/personal')) {
                try {
                    const data = await response.json();
                    responses.push({
                        url: url,
                        status: response.status(),
                        data: data
                    });
                    console.log(`📡 Response capturada: ${response.status()} - ${url}`);
                } catch (e) {
                    // No es JSON, ignorar
                }
            }
        });

        // Ir al formulario
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(5000);

        // Verificar respuestas capturadas
        console.log(`\n📊 Respuestas capturadas: ${responses.length}`);
        responses.forEach((resp, i) => {
            console.log(`${i + 1}. ${resp.status} - ${resp.url}`);
            if (resp.data && typeof resp.data === 'object') {
                console.log(`   Datos: ${JSON.stringify(resp.data).substring(0, 200)}...`);
            }
        });

        // Hacer una petición directa a la API si existe
        try {
            const apiResponse = await page.request.get('http://localhost:8000/api/vehiculos');
            if (apiResponse.ok()) {
                const vehiculosData = await apiResponse.json();
                console.log('\n🚗 DATOS DE API VEHÍCULOS:');
                console.log(`Total vehículos en API: ${vehiculosData.data ? vehiculosData.data.length : 'N/A'}`);
            }
        } catch (e) {
            console.log('⚠️  No se pudo obtener datos de API de vehículos');
        }
    });
});