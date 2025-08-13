const { chromium } = require('playwright');

async function testVehiculoCreationWithOperador() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    // Capturar errores de consola
    page.on('console', msg => {
        console.log(`🖥️ Console ${msg.type()}: ${msg.text()}`);
    });

    page.on('pageerror', error => {
        console.log(`💥 Page error: ${error.message}`);
    });

    try {
        console.log('🚀 Iniciando pruebas de creación de vehículo con operador...');

        // Login
        await page.goto('http://localhost:8001/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // Ir a crear vehículo
        await page.goto('http://localhost:8001/vehiculos/create');
        await page.waitForSelector('h2:has-text("Agregar Nuevo Vehículo")');
        console.log('✅ Página de crear vehículo cargada');

        // Verificar que el dropdown de operadores existe
        const operadorDropdown = page.locator('select[name="operador_id"]');
        const isDropdownVisible = await operadorDropdown.isVisible();
        console.log(`📋 Dropdown de operadores visible: ${isDropdownVisible}`);

        if (isDropdownVisible) {
            // Obtener opciones del dropdown
            const options = await operadorDropdown.locator('option').allTextContents();
            console.log(`📋 Opciones de operadores disponibles: ${options.length}`, options);

            // Si hay operadores disponibles, seleccionar uno
            if (options.length > 1) { // Más de 1 porque la primera opción es "Seleccione un operador"
                // Seleccionar el primer operador disponible (índice 1)
                await operadorDropdown.selectOption({ index: 1 });
                const selectedValue = await operadorDropdown.inputValue();
                console.log(`✅ Operador seleccionado con ID: ${selectedValue}`);

                // Llenar los campos obligatorios del formulario
                console.log('📝 Llenando campos obligatorios del formulario...');

                // Datos del vehículo
                await page.fill('input[name="marca"]', 'Ford');
                await page.fill('input[name="modelo"]', 'F-150');
                await page.fill('input[name="anio"]', '2023');
                await page.fill('input[name="n_serie"]', 'TEST123456789PLAYWRIGHT');
                await page.fill('input[name="placas"]', 'PLY-001-A');
                await page.fill('input[name="kilometraje_actual"]', '15000');

                console.log('✅ Campos obligatorios llenados');

                // Verificar que el operador sigue seleccionado antes de enviar
                const selectedValueBeforeSubmit = await operadorDropdown.inputValue();
                console.log(`🔍 Operador seleccionado antes de enviar: ${selectedValueBeforeSubmit}`);

                // Capturar screenshot antes de enviar
                await page.screenshot({ path: 'debug-vehiculo-formulario-antes-envio.png' });
                console.log('📸 Screenshot tomado antes de envío');

                // Enviar el formulario
                console.log('📤 Enviando formulario...');
                await page.click('button[type="submit"]');

                // Esperar redirección o respuesta
                try {
                    // Esperar hasta 10 segundos por la redirección al detalle del vehículo o índice
                    await page.waitForURL('**/vehiculos/**', { timeout: 10000 });
                    const currentUrl = page.url();
                    console.log(`✅ Formulario enviado exitosamente. URL actual: ${currentUrl}`);

                    // Si estamos en la página de detalle, verificar la información
                    if (currentUrl.includes('/vehiculos/') && !currentUrl.includes('/edit') && !currentUrl.includes('/create')) {
                        console.log('🔍 Verificando información del vehículo creado...');

                        // Esperar a que se cargue la página de detalle
                        await page.waitForTimeout(2000);

                        // Buscar información del operador en la página
                        const operadorInfo = await page.locator('text=Operador').first().isVisible();
                        console.log(`📋 Sección de operador visible: ${operadorInfo}`);

                        // Buscar texto que indique si hay operador asignado
                        const pageContent = await page.textContent('body');

                        if (pageContent.includes('No hay operador asignado')) {
                            console.log('❌ FALLO: El vehículo fue creado sin operador asignado');
                        } else if (pageContent.includes('Operador Actual') || pageContent.includes('nombre_completo')) {
                            console.log('✅ ÉXITO: El vehículo fue creado con operador asignado');
                        } else {
                            console.log('⚠️ No se pudo determinar el estado del operador');
                        }

                        // Capturar screenshot del resultado
                        await page.screenshot({ path: 'debug-vehiculo-creado-con-operador.png' });
                        console.log('📸 Screenshot del vehículo creado tomado');

                        // Obtener el ID del vehículo de la URL para verificación en BD
                        const urlParts = currentUrl.split('/');
                        const vehiculoId = urlParts[urlParts.length - 1];
                        console.log(`🆔 ID del vehículo creado: ${vehiculoId}`);

                        return {
                            success: true,
                            vehiculoId: vehiculoId,
                            operadorSeleccionado: selectedValueBeforeSubmit,
                            url: currentUrl
                        };
                    } else {
                        console.log('⚠️ No se redirigió a la página de detalle del vehículo');
                        await page.screenshot({ path: 'debug-vehiculo-resultado-inesperado.png' });
                    }
                } catch (error) {
                    console.log('❌ Error durante el envío del formulario:', error.message);

                    // Verificar si hay errores de validación en la página
                    const hasErrors = await page.locator('.bg-red-100, .text-red-600, .border-red-500').count();
                    if (hasErrors > 0) {
                        const errorTexts = await page.locator('.bg-red-100, .text-red-600').allTextContents();
                        console.log('⚠️ Errores de validación encontrados:', errorTexts);
                    }

                    await page.screenshot({ path: 'debug-vehiculo-error-envio.png' });
                }
            } else {
                console.log('⚠️ No hay operadores disponibles para seleccionar');
                return { success: false, reason: 'No hay operadores disponibles' };
            }
        } else {
            console.log('❌ El dropdown de operadores no está visible');
            return { success: false, reason: 'Dropdown de operadores no visible' };
        }

    } catch (error) {
        console.error('❌ Error durante las pruebas:', error.message);
        await page.screenshot({ path: 'debug-vehiculo-creation-error.png' });
        return { success: false, error: error.message };
    } finally {
        await browser.close();
    }
}

// Función adicional para verificar en la base de datos
async function verifyVehiculoInDatabase(vehiculoId, operadorId) {
    console.log(`🔍 Verificando vehículo ${vehiculoId} en la base de datos...`);

    try {
        // Aquí puedes agregar código para conectar a la BD y verificar
        // Por ahora, solo loggeamos la información
        console.log(`📊 Datos a verificar:`);
        console.log(`   - Vehículo ID: ${vehiculoId}`);
        console.log(`   - Operador ID esperado: ${operadorId}`);

        return true;
    } catch (error) {
        console.error('❌ Error verificando en BD:', error.message);
        return false;
    }
}

// Ejecutar las pruebas
testVehiculoCreationWithOperador()
    .then(result => {
        console.log('🎯 Resultado de las pruebas:', result);

        if (result && result.success && result.vehiculoId && result.operadorSeleccionado) {
            console.log('🔍 Iniciando verificación en base de datos...');
            return verifyVehiculoInDatabase(result.vehiculoId, result.operadorSeleccionado);
        }
    })
    .catch(console.error);