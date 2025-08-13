import { test, expect } from '@playwright/test';

test.describe('Navegación Sistema Petrotekno', () => {

    test('Entrar al sistema y explorar módulos principales', async ({ page }) => {
        console.log('🚀 Iniciando sesión en el sistema...');

        // Login exitoso
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        console.log('✅ Login exitoso! URL actual:', page.url());
        await page.screenshot({ path: 'dashboard-principal.png', fullPage: true });

        // Explorar vehículos
        console.log('🚗 Navegando a módulo de vehículos...');
        await page.goto('/vehiculos');
        await page.waitForLoadState('networkidle');
        console.log('📍 Vehículos URL:', page.url());
        await page.screenshot({ path: 'modulo-vehiculos.png', fullPage: true });

        // Explorar obras
        console.log('🏗️ Navegando a módulo de obras...');
        await page.goto('/obras');
        await page.waitForLoadState('networkidle');
        console.log('📍 Obras URL:', page.url());
        await page.screenshot({ path: 'modulo-obras.png', fullPage: true });

        // Explorar personal
        console.log('👥 Navegando a módulo de personal...');
        await page.goto('/personal');
        await page.waitForLoadState('networkidle');
        console.log('📍 Personal URL:', page.url());
        await page.screenshot({ path: 'modulo-personal.png', fullPage: true });

        // Explorar asignaciones
        console.log('📋 Navegando a módulo de asignaciones...');
        await page.goto('/asignaciones-obra');
        await page.waitForLoadState('networkidle');
        console.log('📍 Asignaciones URL:', page.url());
        await page.screenshot({ path: 'modulo-asignaciones.png', fullPage: true });

        // Explorar mantenimientos
        console.log('🔧 Navegando a módulo de mantenimientos...');
        await page.goto('/mantenimientos');
        await page.waitForLoadState('networkidle');
        console.log('📍 Mantenimientos URL:', page.url());
        await page.screenshot({ path: 'modulo-mantenimientos.png', fullPage: true });

        console.log('🎯 Exploración completa! Todas las capturas guardadas.');
    });

    test('Crear vehículo nuevo', async ({ page }) => {
        // Login
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Ir a vehículos
        await page.goto('/vehiculos');
        await page.waitForLoadState('networkidle');

        console.log('🚗 Intentando crear un vehículo nuevo...');

        // Buscar botón de crear/agregar
        const createButtons = [
            'button:has-text("Agregar")',
            'button:has-text("Nuevo")',
            'button:has-text("Crear")',
            'a:has-text("Agregar")',
            'a:has-text("Nuevo")',
            'a:has-text("Crear")',
            '.btn-primary',
            '[data-modal-target]',
            '#agregarVehiculoBtn'
        ];

        for (const selector of createButtons) {
            const button = page.locator(selector).first();
            if (await button.isVisible()) {
                console.log(`✅ Encontrado botón crear: ${selector}`);
                await button.click();
                await page.waitForLoadState('networkidle');
                await page.screenshot({ path: 'formulario-crear-vehiculo.png', fullPage: true });
                break;
            }
        }

        console.log('📸 Captura del formulario de crear vehículo guardada');
    });

    test('Sesión interactiva - mantener navegador abierto', async ({ page }) => {
        console.log('🎮 SESIÓN INTERACTIVA INICIADA');
        console.log('====================================');

        // Login automático
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        console.log('✅ Sesión iniciada exitosamente');
        console.log('🌐 Estás en:', page.url());
        console.log('');
        console.log('🚀 ENLACES RÁPIDOS:');
        console.log('- Dashboard: /home');
        console.log('- Vehículos: /vehiculos');
        console.log('- Obras: /obras');
        console.log('- Personal: /personal');
        console.log('- Asignaciones: /asignaciones-obra');
        console.log('- Mantenimientos: /mantenimientos');
        console.log('');
        console.log('⏸️ El navegador permanecerá abierto...');
        console.log('   Presiona Ctrl+C en la terminal para cerrar');

        // Mantener el test activo para sesión interactiva
        await page.waitForTimeout(300000); // 5 minutos
    });

});