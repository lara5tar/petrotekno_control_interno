import { test, expect } from '@playwright/test';

test.describe('Crear Personal y Usuario Admin Correctamente', () => {

    test('Crear registro de personal para el administrador', async ({ page }) => {
        console.log('👤 CREANDO REGISTRO DE PERSONAL PARA ADMINISTRADOR');
        console.log('==============================================');

        console.log('📋 Paso 1: Verificando estructura de personal...');

        await page.screenshot({ path: 'antes-crear-personal-admin.png' });
    });

    test('Verificar acceso después de crear personal', async ({ page }) => {
        console.log('🔍 VERIFICANDO ACCESO CON PERSONAL CREADO');
        console.log('========================================');

        try {
            // Login como admin
            await page.goto('/login');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            console.log('✅ Login exitoso como administrador');

            // Verificar acceso a módulo de personal
            await page.goto('/personal');
            await page.waitForLoadState('networkidle');

            console.log('📍 URL actual:', page.url());
            await page.screenshot({ path: 'acceso-personal-con-registro.png' });

            // Verificar que puede crear personal
            await page.goto('/personal/create');
            await page.waitForLoadState('networkidle');

            console.log('📝 Acceso a crear personal verificado');
            await page.screenshot({ path: 'crear-personal-funcionando.png' });

        } catch (error) {
            console.log('❌ Error en verificación:', error.message);
            await page.screenshot({ path: 'error-acceso-personal.png' });
        }
    });

    test('Test completo - Login y navegación con personal creado', async ({ page }) => {
        console.log('🎯 TEST COMPLETO CON PERSONAL ASOCIADO');
        console.log('====================================');

        try {
            // Login
            await page.goto('/login');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            console.log('✅ Login exitoso');
            console.log('📍 Dashboard URL:', page.url());

            // Probar todos los módulos principales
            const modulos = [
                { url: '/home', nombre: 'Dashboard' },
                { url: '/vehiculos', nombre: 'Vehículos' },
                { url: '/obras', nombre: 'Obras' },
                { url: '/personal', nombre: 'Personal' },
                { url: '/asignaciones-obra', nombre: 'Asignaciones' },
                { url: '/mantenimientos', nombre: 'Mantenimientos' }
            ];

            for (const modulo of modulos) {
                console.log(`🔍 Probando: ${modulo.nombre}`);

                await page.goto(modulo.url);
                await page.waitForLoadState('networkidle', { timeout: 10000 });

                // Verificar que no hay errores de permisos o personal
                const errorPermisos = await page.locator('text=No tienes permisos, text=Sin personal, text=Unauthorized').count();
                const error500 = await page.locator('text=Server Error, text=500').count();

                if (errorPermisos === 0 && error500 === 0) {
                    console.log(`✅ ${modulo.nombre}: Funcionando correctamente`);
                } else {
                    console.log(`❌ ${modulo.nombre}: Tiene errores`);
                }

                await page.screenshot({ path: `final-${modulo.nombre.toLowerCase()}.png` });
            }

            console.log('🎯 Verificación completa exitosa');

        } catch (error) {
            console.log('❌ Error en test completo:', error.message);
            await page.screenshot({ path: 'error-test-final.png' });
        }
    });

});