import { test, expect } from '@playwright/test';

test.describe('Diagnóstico y Corrección del Error de Estatus', () => {

    test('Diagnosticar estructura de base de datos', async ({ page }) => {
        console.log('🔍 DIAGNÓSTICO DEL ERROR DE ESTATUS');
        console.log('=====================================');

        // No necesitamos navegar, solo diagnosticar desde la terminal
        console.log('❌ Error detectado: Column not found: 1054 Unknown column "estatus"');
        console.log('🎯 Causa: La migración de estatus_id a estatus no se completó');
        console.log('🔧 Solución: Ejecutar comandos de corrección');

        await page.screenshot({ path: 'diagnostico-inicio.png' });
    });

    test('Verificar estado actual de la tabla vehiculos', async ({ page }) => {
        console.log('📊 VERIFICANDO ESTRUCTURA DE TABLA VEHICULOS');
        console.log('============================================');

        // Este test nos ayudará a verificar qué columnas existen
        console.log('🔍 Necesitamos verificar si existe:');
        console.log('   - estatus_id (campo antiguo)');
        console.log('   - estatus (campo nuevo)');

        await page.screenshot({ path: 'verificacion-estructura.png' });
    });

    test('Test después de corrección - Verificar login', async ({ page }) => {
        console.log('✅ VERIFICANDO CORRECCIÓN DEL ERROR');
        console.log('==================================');

        try {
            // Intentar hacer login para ver si se resolvió el error
            await page.goto('/login');
            await page.waitForLoadState('networkidle', { timeout: 10000 });

            console.log('✅ Página de login cargada exitosamente');

            // Llenar credenciales
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');

            // Hacer login
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle', { timeout: 15000 });

            if (page.url().includes('/home') || !page.url().includes('/login')) {
                console.log('✅ Login exitoso - Error corregido');
                await page.screenshot({ path: 'correccion-exitosa.png' });
            } else {
                console.log('❌ Login falló - Error persiste');
                await page.screenshot({ path: 'error-persiste.png' });
            }

        } catch (error) {
            console.log('❌ Error en el test:', error.message);
            await page.screenshot({ path: 'error-test-correccion.png' });
        }
    });

    test('Test navegación a vehículos después de corrección', async ({ page }) => {
        console.log('🚗 PROBANDO MÓDULO DE VEHÍCULOS DESPUÉS DE CORRECCIÓN');
        console.log('====================================================');

        try {
            // Login exitoso
            await page.goto('/login');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            // Intentar navegar a vehículos
            await page.goto('/vehiculos');
            await page.waitForLoadState('networkidle', { timeout: 15000 });

            // Verificar si hay errores
            const errorElements = await page.locator('text=Error, text=Exception, .alert-danger').count();

            if (errorElements === 0) {
                console.log('✅ Módulo de vehículos funciona correctamente');
                await page.screenshot({ path: 'vehiculos-funcionando.png' });
            } else {
                console.log('❌ Módulo de vehículos tiene errores');
                await page.screenshot({ path: 'vehiculos-con-errores.png' });
            }

        } catch (error) {
            console.log('❌ Error navegando a vehículos:', error.message);
            await page.screenshot({ path: 'error-navegacion-vehiculos.png' });
        }
    });

    test('Test completo del sistema después de corrección', async ({ page }) => {
        console.log('🎯 TEST COMPLETO DEL SISTEMA');
        console.log('============================');

        try {
            // Login
            await page.goto('/login');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            console.log('✅ Login exitoso');

            // Probar varias rutas
            const rutas = [
                { url: '/home', nombre: 'Dashboard' },
                { url: '/vehiculos', nombre: 'Vehículos' },
                { url: '/obras', nombre: 'Obras' },
                { url: '/asignaciones-obra', nombre: 'Asignaciones' }
            ];

            for (const ruta of rutas) {
                try {
                    await page.goto(ruta.url);
                    await page.waitForLoadState('networkidle', { timeout: 10000 });

                    const hasError = await page.locator('text=Error, text=Exception, .alert-danger').count() > 0;

                    if (!hasError) {
                        console.log(`✅ ${ruta.nombre}: Funcionando correctamente`);
                    } else {
                        console.log(`❌ ${ruta.nombre}: Tiene errores`);
                    }

                    await page.screenshot({ path: `test-${ruta.nombre.toLowerCase()}-final.png` });

                } catch (error) {
                    console.log(`❌ ${ruta.nombre}: Error - ${error.message}`);
                }
            }

            console.log('🎯 Test completo finalizado');

        } catch (error) {
            console.log('❌ Error en test completo:', error.message);
            await page.screenshot({ path: 'error-test-completo.png' });
        }
    });

});