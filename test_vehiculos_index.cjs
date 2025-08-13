const { chromium } = require('playwright');

async function testVehiculosIndex() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        console.log('🚀 Iniciando pruebas del índice de vehículos...');

        // Navegar a la página de login
        await page.goto('http://localhost:8001/login');
        console.log('📍 Navegando a la página de login...');

        // Hacer login con credenciales de admin
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        console.log('🔐 Realizando login...');

        // Esperar a que se cargue el dashboard
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso, dashboard cargado');

        // Navegar a la página de vehículos
        await page.goto('http://localhost:8001/vehiculos');
        console.log('📍 Navegando a la página de vehículos...');

        // Esperar a que se cargue la página
        await page.waitForSelector('h2:has-text("Listado de Vehículos")', { timeout: 10000 });
        console.log('✅ Página de vehículos cargada correctamente');

        // Verificar que la tabla está presente
        const tabla = await page.locator('table').first();
        if (await tabla.isVisible()) {
            console.log('✅ Tabla de vehículos visible');
        } else {
            console.log('❌ Tabla de vehículos no visible');
        }

        // Verificar encabezados de la tabla
        const encabezados = await page.locator('th').allTextContents();
        console.log('📋 Encabezados de tabla encontrados:', encabezados);

        // Contar filas de vehículos (excluyendo encabezado)
        const filas = await page.locator('tbody tr').count();
        console.log(`📊 Número de vehículos en la tabla: ${filas}`);

        if (filas > 0) {
            console.log('🧪 Probando botones de acción en el primer vehículo...');

            // Verificar botón Ver detalles (ojo)
            const botonVer = page.locator('tbody tr').first().locator('a[title="Ver detalles"]');
            if (await botonVer.isVisible()) {
                console.log('✅ Botón "Ver detalles" encontrado');

                // Verificar que el href no esté vacío
                const href = await botonVer.getAttribute('href');
                if (href && href.includes('/vehiculos/') && !href.includes('undefined')) {
                    console.log('✅ URL del botón "Ver detalles" es válida:', href);
                } else {
                    console.log('❌ URL del botón "Ver detalles" es inválida:', href);
                }
            } else {
                console.log('❌ Botón "Ver detalles" no encontrado');
            }

            // Verificar botón Editar
            const botonEditar = page.locator('tbody tr').first().locator('a[title="Editar vehículo"]');
            if (await botonEditar.isVisible()) {
                console.log('✅ Botón "Editar vehículo" encontrado');

                // Verificar que el href no esté vacío
                const href = await botonEditar.getAttribute('href');
                if (href && href.includes('/vehiculos/') && href.includes('/edit') && !href.includes('undefined')) {
                    console.log('✅ URL del botón "Editar vehículo" es válida:', href);
                } else {
                    console.log('❌ URL del botón "Editar vehículo" es inválida:', href);
                }
            } else {
                console.log('❌ Botón "Editar vehículo" no encontrado');
            }

            // Verificar botón Eliminar
            const botonEliminar = page.locator('tbody tr').first().locator('button[title="Eliminar vehículo"]');
            if (await botonEliminar.isVisible()) {
                console.log('✅ Botón "Eliminar vehículo" encontrado');
            } else {
                console.log('❌ Botón "Eliminar vehículo" no encontrado');
            }

            // Probar hacer clic en el botón "Ver detalles"
            try {
                console.log('🧪 Probando navegación al detalle del vehículo...');
                await botonVer.click();

                // Esperar a que se cargue la página de detalle
                await page.waitForURL('**/vehiculos/*', { timeout: 5000 });
                const currentUrl = page.url();

                if (currentUrl.includes('/vehiculos/') && !currentUrl.includes('/edit')) {
                    console.log('✅ Navegación a detalle del vehículo exitosa:', currentUrl);

                    // Volver a la página de índice
                    await page.goBack();
                    await page.waitForSelector('h2:has-text("Listado de Vehículos")');
                    console.log('✅ Regreso al índice de vehículos exitoso');
                } else {
                    console.log('❌ Error en navegación a detalle del vehículo');
                }
            } catch (error) {
                console.log('❌ Error al probar navegación a detalle:', error.message);
            }

            // Probar hacer clic en el botón "Editar vehículo"
            try {
                console.log('🧪 Probando navegación a editar vehículo...');
                const botonEditarTest = page.locator('tbody tr').first().locator('a[title="Editar vehículo"]');
                await botonEditarTest.click();

                // Esperar a que se cargue la página de edición
                await page.waitForURL('**/vehiculos/*/edit', { timeout: 5000 });
                const currentUrl = page.url();

                if (currentUrl.includes('/vehiculos/') && currentUrl.includes('/edit')) {
                    console.log('✅ Navegación a editar vehículo exitosa:', currentUrl);

                    // Volver a la página de índice
                    await page.goto('http://localhost:8001/vehiculos');
                    await page.waitForSelector('h2:has-text("Listado de Vehículos")');
                    console.log('✅ Regreso al índice de vehículos exitoso');
                } else {
                    console.log('❌ Error en navegación a editar vehículo');
                }
            } catch (error) {
                console.log('❌ Error al probar navegación a editar:', error.message);
            }
        } else {
            console.log('ℹ️ No hay vehículos en la tabla para probar');
        }

        // Verificar botón "Agregar Vehículo"
        const botonAgregar = page.locator('a:has-text("Agregar Vehículo")');
        if (await botonAgregar.isVisible()) {
            console.log('✅ Botón "Agregar Vehículo" encontrado');

            // Probar navegación a crear vehículo
            try {
                console.log('🧪 Probando navegación a crear vehículo...');
                await botonAgregar.click();

                await page.waitForURL('**/vehiculos/create', { timeout: 5000 });
                const currentUrl = page.url();

                if (currentUrl.includes('/vehiculos/create')) {
                    console.log('✅ Navegación a crear vehículo exitosa:', currentUrl);

                    // Volver a la página de índice
                    await page.goto('http://localhost:8001/vehiculos');
                    await page.waitForSelector('h2:has-text("Listado de Vehículos")');
                    console.log('✅ Regreso al índice de vehículos exitoso');
                } else {
                    console.log('❌ Error en navegación a crear vehículo');
                }
            } catch (error) {
                console.log('❌ Error al probar navegación a crear:', error.message);
            }
        } else {
            console.log('❌ Botón "Agregar Vehículo" no encontrado');
        }

        // Probar filtros
        console.log('🧪 Probando funcionalidad de filtros...');

        // Probar filtro de búsqueda
        const inputBusqueda = page.locator('input[name="search"]');
        if (await inputBusqueda.isVisible()) {
            console.log('✅ Campo de búsqueda encontrado');
            await inputBusqueda.fill('test');
            await page.waitForTimeout(1000);
            console.log('✅ Filtro de búsqueda probado');
        }

        // Probar filtro de estado
        const selectEstado = page.locator('select[name="estado"]');
        if (await selectEstado.isVisible()) {
            console.log('✅ Selector de estado encontrado');
            const opciones = await selectEstado.locator('option').count();
            console.log(`📋 Opciones de estado disponibles: ${opciones}`);
        }

        console.log('🎉 Pruebas del índice de vehículos completadas');

    } catch (error) {
        console.error('❌ Error durante las pruebas:', error);

        // Capturar screenshot del error
        await page.screenshot({ path: 'error_vehiculos_index.png', fullPage: true });
        console.log('📸 Screenshot del error guardado como error_vehiculos_index.png');

        // Intentar obtener más información del error
        const url = page.url();
        console.log('🌐 URL actual:', url);

        // Verificar si hay errores de Laravel en la página
        const errorMessages = await page.locator('.alert-danger, .bg-red-100, .text-red-600').allTextContents();
        if (errorMessages.length > 0) {
            console.log('⚠️ Mensajes de error encontrados:', errorMessages);
        }

        // Verificar consola del navegador
        page.on('console', msg => console.log('🖥️ Consola:', msg.text()));
        page.on('pageerror', error => console.log('💥 Error de página:', error.message));
    } finally {
        await browser.close();
    }
}

// Ejecutar las pruebas
testVehiculosIndex().catch(console.error);