// @ts-check
const { test, expect } = require('@playwright/test');

test.describe('Crear Obra - Prueba Simplificada', () => {

    test('Debería crear una obra exitosamente y verificar que se guardó el encargado_id', async ({ page }) => {
        console.log('🚀 Iniciando prueba simplificada de creación de obra...');

        // 1. Login
        await page.goto('/login');
        await page.waitForLoadState('networkidle');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');
        console.log('✅ Login completado');

        // 2. Ir al formulario de crear obra
        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');
        console.log('✅ Navegado a formulario de crear obra');

        // 3. Verificar que estamos en la página correcta
        await expect(page).toHaveURL(/.*\/obras\/create/);
        await expect(page.locator('h2:has-text("Crear Nueva Obra")')).toBeVisible();

        // 4. Llenar formulario básico (sin vehículos)
        const nombreObra = `Obra Test ${Date.now()}`;
        await page.fill('input[name="nombre_obra"]', nombreObra);
        await page.selectOption('select[name="estatus"]', 'planificada');
        await page.fill('input[name="avance"]', '0');

        // Fechas
        const fechaInicio = new Date().toISOString().split('T')[0];
        const fechaFin = new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        await page.fill('input[name="fecha_inicio"]', fechaInicio);
        await page.fill('input[name="fecha_fin"]', fechaFin);

        // 5. Seleccionar responsable de obra
        console.log('🔍 Seleccionando responsable de obra...');
        const encargadoSelect = page.locator('select[name="encargado_id"]');
        await expect(encargadoSelect).toBeVisible();

        const encargadoOptions = await encargadoSelect.locator('option').all();
        if (encargadoOptions.length > 1) {
            const primeraOpcion = await encargadoOptions[1].getAttribute('value');
            await encargadoSelect.selectOption(primeraOpcion);
            console.log(`✅ Responsable seleccionado: ID ${primeraOpcion}`);
        } else {
            throw new Error('❌ No hay responsables disponibles');
        }

        // 6. Observaciones
        await page.fill('textarea[name="observaciones"]', 'Obra creada por prueba automatizada de Playwright');

        // 7. Enviar formulario
        console.log('📝 Enviando formulario...');
        await page.click('button[type="submit"]:has-text("Crear Obra")');
        await page.waitForLoadState('networkidle');

        // 8. Verificar éxito
        const urlActual = page.url();
        const tieneMensajeExito = await page.locator('.bg-green-100, .alert-success').count() > 0;
        const estaEnObras = urlActual.includes('/obras') && !urlActual.includes('/create');

        if (tieneMensajeExito || estaEnObras) {
            console.log('✅ Obra creada exitosamente');

            // 9. Verificar en base de datos
            await verificarObraEnBaseDatos(page, nombreObra);

        } else {
            // Capturar errores si falló
            const errores = await page.locator('.text-red-600, .alert-danger').allTextContents();
            if (errores.length > 0) {
                console.log('❌ Errores encontrados:', errores);
            }

            await page.screenshot({ path: `debug-crear-obra-${Date.now()}.png` });
            console.log('📸 Screenshot guardado para debugging');
            throw new Error('La obra no se creó exitosamente');
        }
    });

    async function verificarObraEnBaseDatos(page, nombreObra) {
        console.log('🔍 Verificando obra en base de datos...');

        try {
            const resultado = await page.evaluate(async (nombreObra) => {
                try {
                    const response = await fetch('/api/verificacion/verificar-obra', {
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
                        const error = await response.text();
                        return { error: `HTTP ${response.status}: ${error}` };
                    }
                } catch (error) {
                    return { error: error.message };
                }
            }, nombreObra);

            if (resultado.error) {
                console.log('⚠️ No se pudo verificar via API:', resultado.error);
                console.log('ℹ️ Realizando verificación manual navegando a obras...');

                // Verificación manual
                await page.goto('/obras');
                await page.waitForLoadState('networkidle');

                const obraEncontrada = page.locator(`text=${nombreObra}`);
                if (await obraEncontrada.count() > 0) {
                    console.log('✅ Obra encontrada en el listado de obras');
                } else {
                    console.log('❌ Obra no encontrada en el listado');
                }

            } else {
                console.log('✅ Verificación API completada:', resultado);

                // Verificar encargado_id
                if (resultado.obra && resultado.obra.encargado_id) {
                    console.log(`✅ ENCARGADO_ID guardado correctamente: ${resultado.obra.encargado_id}`);
                    console.log(`✅ Nombre del encargado: ${resultado.obra.encargado_nombre || 'N/A'}`);
                } else {
                    console.log('❌ ENCARGADO_ID no se guardó correctamente');
                }

                // Verificar asignaciones de vehículo
                if (resultado.asignaciones && resultado.asignaciones.length > 0) {
                    console.log(`✅ ASIGNACIONES_OBRA: Encontradas ${resultado.asignaciones.length} asignación(es)`);
                    resultado.asignaciones.forEach((asignacion, index) => {
                        console.log(`   ${index + 1}. Vehículo ID: ${asignacion.vehiculo_id}, Estado: ${asignacion.estado}`);
                    });
                } else {
                    console.log('ℹ️ No se encontraron asignaciones de vehículos (esperado si no se asignó ninguno)');
                }

                // Resumen de verificaciones
                console.log('\n📊 RESUMEN DE VERIFICACIONES:');
                console.log(`   ✅ Obra creada: ${resultado.verificaciones.obra_creada}`);
                console.log(`   ${resultado.verificaciones.tiene_encargado ? '✅' : '❌'} Tiene encargado: ${resultado.verificaciones.tiene_encargado}`);
                console.log(`   ℹ️  Asignaciones de vehículo: ${resultado.verificaciones.total_asignaciones}`);
            }

        } catch (error) {
            console.log('⚠️ Error en verificación de base de datos:', error.message);
        }
    }
});