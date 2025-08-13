const { chromium } = require('playwright');

async function debugVehiculoCreationDetailed() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    // Capturar todos los eventos de red
    const responses = [];
    page.on('response', response => {
        responses.push({
            url: response.url(),
            status: response.status(),
            statusText: response.statusText()
        });
    });

    // Capturar errores de consola
    page.on('console', msg => {
        console.log(`🖥️ Console ${msg.type()}: ${msg.text()}`);
    });

    page.on('pageerror', error => {
        console.log(`💥 Page error: ${error.message}`);
    });

    try {
        console.log('🔍 Debug detallado de creación de vehículo...');

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

        // Llenar formulario
        await page.fill('input[name="marca"]', 'Toyota');
        await page.fill('input[name="modelo"]', 'Hilux');
        await page.fill('input[name="anio"]', '2024');
        await page.fill('input[name="n_serie"]', 'DEBUG123456789TEST');
        await page.fill('input[name="placas"]', 'DBG-002-B');
        await page.fill('input[name="kilometraje_actual"]', '5000');

        // Seleccionar operador
        await page.selectOption('select[name="operador_id"]', '1');
        const selectedOperador = await page.inputValue('select[name="operador_id"]');
        console.log(`✅ Operador seleccionado: ${selectedOperador}`);

        // Verificar que todos los campos estén llenos
        const formData = {
            marca: await page.inputValue('input[name="marca"]'),
            modelo: await page.inputValue('input[name="modelo"]'),
            anio: await page.inputValue('input[name="anio"]'),
            n_serie: await page.inputValue('input[name="n_serie"]'),
            placas: await page.inputValue('input[name="placas"]'),
            kilometraje_actual: await page.inputValue('input[name="kilometraje_actual"]'),
            operador_id: await page.inputValue('select[name="operador_id"]')
        };
        console.log('📋 Datos del formulario antes de enviar:', formData);

        // Screenshot antes de enviar
        await page.screenshot({ path: 'debug-formulario-completo.png' });

        // Interceptar la request POST
        const [response] = await Promise.all([
            page.waitForResponse(response =>
                response.url().includes('/vehiculos') &&
                response.request().method() === 'POST'
            ),
            page.click('button[type="submit"]')
        ]);

        console.log(`📤 Respuesta del servidor: ${response.status()} ${response.statusText()}`);
        console.log(`🌐 URL de respuesta: ${response.url()}`);

        // Esperar a que se procese la respuesta
        await page.waitForTimeout(3000);

        const currentUrl = page.url();
        console.log(`🌐 URL actual después del envío: ${currentUrl}`);

        // Verificar si hay mensajes de error o éxito en la página
        const successMessage = await page.locator('.bg-green-100').textContent().catch(() => null);
        const errorMessage = await page.locator('.bg-red-100').textContent().catch(() => null);

        if (successMessage) {
            console.log('✅ Mensaje de éxito:', successMessage);
        }

        if (errorMessage) {
            console.log('❌ Mensaje de error:', errorMessage);
        }

        // Verificar si hay errores de validación específicos
        const validationErrors = await page.locator('.text-red-600').allTextContents();
        if (validationErrors.length > 0) {
            console.log('⚠️ Errores de validación:', validationErrors);
        }

        // Screenshot después del envío
        await page.screenshot({ path: 'debug-resultado-envio.png' });

        // Si estamos en la página de detalle, verificar el operador
        if (currentUrl.includes('/vehiculos/') && !currentUrl.includes('/create') && !currentUrl.includes('/edit')) {
            console.log('🔍 Verificando operador en página de detalle...');

            await page.waitForTimeout(1000);
            const pageContent = await page.textContent('body');

            if (pageContent.includes('No hay operador asignado')) {
                console.log('❌ PROBLEMA: Vehículo creado SIN operador');
            } else if (pageContent.includes('Administrador Sistema') || pageContent.includes('Test Operador')) {
                console.log('✅ ÉXITO: Vehículo creado CON operador asignado');
            } else {
                console.log('⚠️ Estado del operador no determinado');
            }

            // Extraer ID del vehículo para verificación adicional
            const vehiculoId = currentUrl.split('/').pop();
            console.log(`🆔 ID del vehículo creado: ${vehiculoId}`);

            return {
                success: true,
                vehiculoId,
                operadorAsignado: selectedOperador,
                url: currentUrl
            };
        }

        return {
            success: false,
            url: currentUrl,
            responses: responses.slice(-5) // Últimas 5 respuestas
        };

    } catch (error) {
        console.error('❌ Error durante debug:', error.message);
        await page.screenshot({ path: 'debug-error-detallado.png' });
        return { success: false, error: error.message };
    } finally {
        await browser.close();
    }
}

// Ejecutar debug
debugVehiculoCreationDetailed()
    .then(result => {
        console.log('🎯 Resultado del debug detallado:', result);
    })
    .catch(console.error);