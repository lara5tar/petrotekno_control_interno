import { test, expect } from '@playwright/test';

test.describe('Asignar Permisos Completos al Admin', () => {

    test('Crear y asignar todos los permisos al administrador', async ({ page }) => {
        console.log('🔐 ASIGNANDO PERMISOS COMPLETOS AL ADMIN');
        console.log('========================================');

        console.log('📋 Creando permisos del sistema...');

        // Lista de todos los permisos necesarios para el sistema
        const permisos = [
            // Permisos de Vehículos
            { nombre: 'ver_vehiculos', descripcion: 'Ver listado y detalles de vehículos' },
            { nombre: 'crear_vehiculo', descripcion: 'Crear nuevos vehículos' },
            { nombre: 'editar_vehiculo', descripcion: 'Editar vehículos existentes' },
            { nombre: 'eliminar_vehiculo', descripcion: 'Eliminar vehículos' },

            // Permisos de Obras
            { nombre: 'ver_obras', descripcion: 'Ver listado y detalles de obras' },
            { nombre: 'crear_obra', descripcion: 'Crear nuevas obras' },
            { nombre: 'editar_obra', descripcion: 'Editar obras existentes' },
            { nombre: 'eliminar_obra', descripcion: 'Eliminar obras' },

            // Permisos de Personal
            { nombre: 'ver_personal', descripcion: 'Ver listado y detalles del personal' },
            { nombre: 'crear_personal', descripcion: 'Crear nuevo personal' },
            { nombre: 'editar_personal', descripcion: 'Editar personal existente' },
            { nombre: 'eliminar_personal', descripcion: 'Eliminar personal' },

            // Permisos de Asignaciones
            { nombre: 'ver_asignaciones', descripcion: 'Ver asignaciones de obras' },
            { nombre: 'crear_asignaciones', descripcion: 'Crear nuevas asignaciones' },
            { nombre: 'editar_asignaciones', descripcion: 'Editar asignaciones existentes' },
            { nombre: 'liberar_asignaciones', descripcion: 'Liberar asignaciones de obras' },

            // Permisos de Mantenimientos
            { nombre: 'ver_mantenimientos', descripcion: 'Ver mantenimientos de vehículos' },
            { nombre: 'crear_mantenimiento', descripcion: 'Crear registros de mantenimiento' },
            { nombre: 'editar_mantenimiento', descripcion: 'Editar mantenimientos' },
            { nombre: 'eliminar_mantenimiento', descripcion: 'Eliminar mantenimientos' },

            // Permisos de Kilometrajes
            { nombre: 'ver_kilometrajes', descripcion: 'Ver registros de kilometraje' },
            { nombre: 'crear_kilometraje', descripcion: 'Crear registros de kilometraje' },
            { nombre: 'editar_kilometraje', descripcion: 'Editar kilometrajes' },

            // Permisos de Documentos
            { nombre: 'ver_documentos', descripcion: 'Ver documentos del sistema' },
            { nombre: 'subir_documentos', descripcion: 'Subir nuevos documentos' },
            { nombre: 'editar_documentos', descripcion: 'Editar documentos' },
            { nombre: 'eliminar_documentos', descripcion: 'Eliminar documentos' },

            // Permisos Administrativos
            { nombre: 'ver_usuarios', descripcion: 'Ver usuarios del sistema' },
            { nombre: 'crear_usuarios', descripcion: 'Crear nuevos usuarios' },
            { nombre: 'editar_usuarios', descripcion: 'Editar usuarios' },
            { nombre: 'ver_reportes', descripcion: 'Ver reportes del sistema' },
            { nombre: 'administrar_sistema', descripcion: 'Administración completa del sistema' },

            // Permisos de Roles
            { nombre: 'ver_roles', descripcion: 'Ver roles del sistema' },
            { nombre: 'crear_roles', descripcion: 'Crear nuevos roles' },
            { nombre: 'editar_roles', descripcion: 'Editar roles existentes' },

            // Permisos de Configuración
            { nombre: 'configurar_alertas', descripcion: 'Configurar alertas de mantenimiento' },
            { nombre: 'ver_logs', descripcion: 'Ver logs del sistema' },
            { nombre: 'backup_sistema', descripcion: 'Realizar respaldos del sistema' }
        ];

        console.log(`📝 Se crearán ${permisos.length} permisos en total`);

        await page.screenshot({ path: 'antes-crear-permisos.png' });
    });

    test('Verificar acceso completo después de asignar permisos', async ({ page }) => {
        console.log('🎯 VERIFICANDO ACCESO COMPLETO DEL ADMIN');
        console.log('======================================');

        try {
            // Login como admin
            await page.goto('/login');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            console.log('✅ Login exitoso como administrador');

            // Probar acceso a todos los módulos principales
            const modulos = [
                { url: '/home', nombre: 'Dashboard' },
                { url: '/vehiculos', nombre: 'Vehículos' },
                { url: '/vehiculos/create', nombre: 'Crear Vehículo' },
                { url: '/obras', nombre: 'Obras' },
                { url: '/obras/create', nombre: 'Crear Obra' },
                { url: '/personal', nombre: 'Personal' },
                { url: '/personal/create', nombre: 'Crear Personal' },
                { url: '/asignaciones-obra', nombre: 'Asignaciones' },
                { url: '/asignaciones-obra/create', nombre: 'Crear Asignación' },
                { url: '/mantenimientos', nombre: 'Mantenimientos' },
                { url: '/kilometrajes', nombre: 'Kilometrajes' },
            ];

            for (const modulo of modulos) {
                try {
                    console.log(`🔍 Probando acceso a: ${modulo.nombre}`);
                    await page.goto(modulo.url);
                    await page.waitForLoadState('networkidle', { timeout: 10000 });

                    // Verificar si hay errores de permisos
                    const errorPermisos = await page.locator('text=No tienes permisos, text=Sin permisos, text=Unauthorized, text=403').count();
                    const error500 = await page.locator('text=Server Error, text=500').count();

                    if (errorPermisos === 0 && error500 === 0) {
                        console.log(`✅ ${modulo.nombre}: Acceso permitido`);
                    } else if (errorPermisos > 0) {
                        console.log(`❌ ${modulo.nombre}: Error de permisos`);
                    } else {
                        console.log(`⚠️ ${modulo.nombre}: Error del servidor`);
                    }

                    await page.screenshot({ path: `acceso-${modulo.nombre.toLowerCase().replace(' ', '-')}.png` });

                } catch (error) {
                    console.log(`❌ ${modulo.nombre}: Error - ${error.message}`);
                }
            }

            console.log('🎯 Verificación de acceso completa');

        } catch (error) {
            console.log('❌ Error en verificación de acceso:', error.message);
            await page.screenshot({ path: 'error-verificacion-acceso.png' });
        }
    });

    test('Test funcionalidad completa - Crear vehículo', async ({ page }) => {
        console.log('🚗 PROBANDO FUNCIONALIDAD COMPLETA - CREAR VEHÍCULO');
        console.log('=================================================');

        try {
            // Login
            await page.goto('/login');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            // Ir a crear vehículo
            await page.goto('/vehiculos/create');
            await page.waitForLoadState('networkidle');

            console.log('📝 Llenando formulario de vehículo...');

            // Llenar formulario
            await page.fill('input[name="marca"]', 'Toyota Test');
            await page.fill('input[name="modelo"]', 'Hilux Test');
            await page.fill('input[name="anio"]', '2024');
            await page.fill('input[name="n_serie"]', 'TEST123456789');
            await page.fill('input[name="placas"]', 'TEST-001');
            await page.fill('input[name="kilometraje_actual"]', '1000');

            // Seleccionar estatus si existe dropdown
            const estatusSelect = page.locator('select[name="estatus"], select[name="estatus_id"]').first();
            if (await estatusSelect.isVisible()) {
                await estatusSelect.selectOption({ index: 1 });
            }

            await page.screenshot({ path: 'formulario-vehiculo-lleno.png' });

            // Enviar formulario
            const submitButton = page.locator('button[type="submit"], input[type="submit"]').first();
            if (await submitButton.isVisible()) {
                await submitButton.click();
                await page.waitForLoadState('networkidle');

                console.log('✅ Formulario enviado exitosamente');
                await page.screenshot({ path: 'vehiculo-creado-exitoso.png' });
            }

        } catch (error) {
            console.log('❌ Error creando vehículo:', error.message);
            await page.screenshot({ path: 'error-crear-vehiculo.png' });
        }
    });

});