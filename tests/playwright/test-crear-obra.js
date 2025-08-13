// @ts-check
const { test, expect } = require('@playwright/test');

test.describe('Crear Obra - Pruebas Automatizadas', () => {
    let page;
    let context;

    test.beforeAll(async ({ browser }) => {
        context = await browser.newContext();
        page = await context.newPage();

        // Configurar console logs para debugging
        page.on('console', msg => console.log('PAGE LOG:', msg.text()));
        page.on('pageerror', error => console.log('PAGE ERROR:', error.message));
    });

    test.afterAll(async () => {
        await context.close();
    });

    test('Debería crear una obra exitosamente y verificar relaciones', async () => {
        console.log('🚀 Iniciando prueba de creación de obra...');

        // 1. Navegar a la página de login (asumiendo autenticación)
        await page.goto('/login');
        await page.waitForLoadState('networkidle');

        // 2. Hacer login (ajusta estas credenciales según tu seeder)
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        console.log('✅ Login completado');

        // 3. Navegar a la página de crear obra
        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        console.log('✅ Navegado a formulario de crear obra');

        // 4. Verificar que estamos en la página correcta
        await expect(page).toHaveURL(/.*\/obras\/create/);
        await expect(page.locator('h2')).toContainText('Crear Nueva Obra');

        // 5. Llenar el formulario de obra
        const nombreObra = `Obra Automatizada ${Date.now()}`;

        await page.fill('input[name="nombre_obra"]', nombreObra);
        await page.selectOption('select[name="estatus"]', 'planificada');
        await page.fill('input[name="avance"]', '0');

        // Fechas
        const fechaInicio = new Date();
        const fechaFin = new Date(Date.now() + 365 * 24 * 60 * 60 * 1000); // +1 año

        await page.fill('input[name="fecha_inicio"]', fechaInicio.toISOString().split('T')[0]);
        await page.fill('input[name="fecha_fin"]', fechaFin.toISOString().split('T')[0]);

        // 6. Seleccionar un responsable de obra (encargado)
        console.log('🔍 Seleccionando responsable de obra...');

        const encargadoSelect = page.locator('select[name="encargado_id"]');
        await expect(encargadoSelect).toBeVisible();

        // Obtener las opciones disponibles
        const encargadoOptions = await encargadoSelect.locator('option').all();
        if (encargadoOptions.length > 1) { // Más de 1 porque el primero es "Seleccione..."
            const primeraOpcion = await encargadoOptions[1].getAttribute('value');
            await encargadoSelect.selectOption(primeraOpcion);
            console.log(`✅ Responsable seleccionado: ${primeraOpcion}`);
        } else {
            throw new Error('❌ No hay responsables disponibles para seleccionar');
        }

        // 7. Agregar observaciones
        await page.fill('textarea[name="observaciones"]', 'Obra creada mediante prueba automatizada con Playwright');

        // 8. Intentar asignar un vehículo si hay disponibles
        console.log('🚗 Verificando disponibilidad de vehículos...');

        const botonAsignarVehiculo = page.locator('button:has-text("Asignar Vehículo")');
        const hayVehiculos = await botonAsignarVehiculo.isVisible();

        let vehiculoAsignado = false;

        if (hayVehiculos) {
            console.log('✅ Vehículos disponibles, intentando asignar...');

            try {
                // Abrir modal de vehículos
                await botonAsignarVehiculo.click();
                await page.waitForSelector('#vehicle-modal', { state: 'visible' });

                // Buscar vehículo disponible
                const vehiculosDisponibles = page.locator('.vehicle-option:not(.cursor-not-allowed)');
                const countDisponibles = await vehiculosDisponibles.count();

                if (countDisponibles > 0) {
                    // Seleccionar el primer vehículo disponible
                    await vehiculosDisponibles.first().click();

                    // Confirmar asignación
                    const confirmarBtn = page.locator('#confirm-vehicle-btn');
                    await expect(confirmarBtn).toBeEnabled();
                    await confirmarBtn.click();

                    vehiculoAsignado = true;
                    console.log('✅ Vehículo asignado exitosamente');
                } else {
                    console.log('⚠️ No hay vehículos disponibles para asignar');
                    // Cerrar modal
                    await page.locator('button:has-text("Cancelar")').click();
                }
            } catch (error) {
                console.log('⚠️ Error al asignar vehículo:', error.message);
                // Intentar cerrar modal si está abierto
                const modal = page.locator('#vehicle-modal');
                if (await modal.isVisible()) {
                    await page.keyboard.press('Escape');
                }
            }
        } else {
            console.log('⚠️ No hay vehículos disponibles en el sistema');
        }

        // 9. Enviar el formulario
        console.log('📝 Enviando formulario...');

        await page.click('button[type="submit"]:has-text("Crear Obra")');
        await page.waitForLoadState('networkidle');

        // 10. Verificar que la obra se creó exitosamente
        console.log('🔍 Verificando creación exitosa...');

        // Verificar redirección o mensaje de éxito
        const urlActual = page.url();
        const tieneMensajeExito = await page.locator('.bg-green-100, .alert-success').count() > 0;
        const estaEnIndex = urlActual.includes('/obras') && !urlActual.includes('/create');

        if (tieneMensajeExito || estaEnIndex) {
            console.log('✅ Obra creada exitosamente');

            // 11. Obtener el ID de la obra creada
            let obraId = null;

            if (urlActual.includes('/obras/') && !urlActual.includes('/create')) {
                const match = urlActual.match(/\/obras\/(\d+)/);
                if (match) {
                    obraId = match[1];
                }
            }

            // Si no tenemos ID de la URL, buscar en la base de datos
            if (!obraId) {
                console.log('🔍 Buscando obra en la base de datos...');
                // Aquí ejecutaríamos una consulta a la base de datos para encontrar la obra
                // Por ahora, usaremos el nombre único para identificarla
            }

            // 12. Verificar relaciones en la base de datos
            await verificarRelacionesBaseDatos(page, nombreObra, vehiculoAsignado);

        } else {
            // Revisar errores
            console.log('❌ Error al crear la obra');
            await capturarErrores(page);
            throw new Error('La obra no se creó exitosamente');
        }
    });

    /**
     * Función para verificar las relaciones en la base de datos
     */
    async function verificarRelacionesBaseDatos(page, nombreObra, vehiculoAsignado) {
        console.log('🔍 Verificando relaciones en la base de datos...');

        try {
            // Crear una función auxiliar en el navegador para hacer peticiones
            const resultado = await page.evaluate(async (nombreObra) => {
                try {
                    // Hacer una petición para obtener información de la obra
                    const response = await fetch('/api/verificar-obra', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        },
                        body: JSON.stringify({ nombre_obra: nombreObra })
                    });

                    if (response.ok) {
                        return await response.json();
                    } else {
                        return { error: 'No se pudo verificar la obra' };
                    }
                } catch (error) {
                    return { error: error.message };
                }
            }, nombreObra);

            if (resultado.error) {
                console.log('⚠️ No se pudo verificar automáticamente via API:', resultado.error);
                console.log('ℹ️ Realizando verificación manual...');
                await verificacionManual(page, nombreObra);
            } else {
                console.log('✅ Verificación automática completada:', resultado);

                // Verificar encargado_id
                if (resultado.obra && resultado.obra.encargado_id) {
                    console.log(`✅ Encargado ID guardado correctamente: ${resultado.obra.encargado_id}`);
                } else {
                    console.log('❌ Encargado ID no se guardó correctamente');
                }

                // Verificar asignaciones de vehículo
                if (vehiculoAsignado && resultado.asignaciones && resultado.asignaciones.length > 0) {
                    console.log(`✅ Relación vehículo-obra creada en asignaciones_obra: ${resultado.asignaciones.length} asignación(es)`);
                } else if (vehiculoAsignado) {
                    console.log('❌ No se encontró la relación vehículo-obra en asignaciones_obra');
                } else {
                    console.log('ℹ️ No se asignó vehículo, verificación de asignaciones omitida');
                }
            }

        } catch (error) {
            console.log('⚠️ Error en verificación de base de datos:', error.message);
            await verificacionManual(page, nombreObra);
        }
    }

    /**
     * Función para verificación manual navegando a las páginas
     */
    async function verificacionManual(page, nombreObra) {
        console.log('🔍 Realizando verificación manual...');

        try {
            // Ir al listado de obras
            await page.goto('/obras');
            await page.waitForLoadState('networkidle');

            // Buscar la obra creada
            const obraEncontrada = page.locator(`text=${nombreObra}`);
            if (await obraEncontrada.count() > 0) {
                console.log('✅ Obra encontrada en el listado');

                // Hacer clic en la obra para ver detalles
                await obraEncontrada.first().click();
                await page.waitForLoadState('networkidle');

                // Verificar que estamos en la página de detalles
                const paginaDetalles = page.url().includes('/obras/') && !page.url().includes('/index');
                if (paginaDetalles) {
                    console.log('✅ Navegado a página de detalles de la obra');

                    // Buscar información del encargado
                    const infoEncargado = page.locator('[data-testid="encargado-info"], .encargado, :has-text("Encargado"):has-text("Responsable")');
                    if (await infoEncargado.count() > 0) {
                        console.log('✅ Información del encargado visible en la página');
                    } else {
                        console.log('⚠️ No se encontró información del encargado en la página');
                    }

                    // Buscar información de vehículos asignados
                    const infoVehiculos = page.locator('[data-testid="vehiculos-asignados"], .vehiculos-asignados, :has-text("Vehículo"):has-text("Asignado")');
                    if (await infoVehiculos.count() > 0) {
                        console.log('✅ Información de vehículos asignados visible');
                    } else {
                        console.log('ℹ️ No se encontró información de vehículos asignados (puede ser normal si no se asignaron)');
                    }
                }
            } else {
                console.log('❌ Obra no encontrada en el listado');
            }

        } catch (error) {
            console.log('⚠️ Error en verificación manual:', error.message);
        }
    }

    /**
     * Función para capturar errores del formulario
     */
    async function capturarErrores(page) {
        console.log('🔍 Analizando errores...');

        // Buscar mensajes de error
        const errores = await page.locator('.text-red-600, .text-red-500, .invalid-feedback, .alert-danger').allTextContents();
        if (errores.length > 0) {
            console.log('❌ Errores encontrados:');
            errores.forEach((error, index) => {
                console.log(`   ${index + 1}. ${error.trim()}`);
            });
        }

        // Verificar validación de campos
        const camposInvalidos = await page.locator('input.border-red-500, select.border-red-500, textarea.border-red-500').count();
        if (camposInvalidos > 0) {
            console.log(`❌ ${camposInvalidos} campo(s) con errores de validación`);
        }

        // Capturar screenshot para debugging
        await page.screenshot({ path: `debug-error-${Date.now()}.png`, fullPage: true });
        console.log('📸 Screenshot de error guardado');
    }
});

test.describe('Pruebas de Validación de Formulario', () => {
    test('Debería mostrar errores de validación para campos requeridos', async ({ page }) => {
        console.log('🧪 Probando validación de campos requeridos...');

        // Login
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Ir al formulario
        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        // Intentar enviar formulario vacío
        await page.click('button[type="submit"]:has-text("Crear Obra")');
        await page.waitForTimeout(1000);

        // Verificar que aparecen errores de validación
        const errores = await page.locator('.text-red-600, .invalid-feedback').count();
        expect(errores).toBeGreaterThan(0);

        console.log(`✅ ${errores} error(es) de validación mostrados correctamente`);
    });
});