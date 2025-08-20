import { test, expect } from '@playwright/test';

test.describe('Alertas de Documentos de Vehículos', () => {

    test.beforeEach(async ({ page }) => {
        // Agregar timeout más largo para la navegación
        page.setDefaultTimeout(30000);

        // Esperar un poco antes de iniciar para asegurar que el servidor esté listo
        await page.waitForTimeout(1000);

        // Ir a la página de login
        await page.goto('http://localhost:8001/login', {
            waitUntil: 'load',
            timeout: 30000
        });

        // Hacer login con el usuario admin
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que se complete el login con timeout más largo
        await page.waitForURL('**/home', { timeout: 30000 });
    });

    test('debe mostrar alertas de documentos próximos a vencer', async ({ page }) => {
        // Navegar a la página de alertas de mantenimiento
        await page.goto('http://localhost:8001/alertas/mantenimiento', { timeout: 30000 });

        // Esperar a que la página cargue completamente
        await page.waitForSelector('table', { timeout: 30000 });

        // Verificar que el título de la página sea correcto
        await expect(page.locator('h1').filter({ hasText: 'Alertas de Mantenimiento' })).toBeVisible();

        // Verificar que existan las columnas de documentos
        await expect(page.locator('th').filter({ hasText: '🛡️ Póliza' })).toBeVisible();
        await expect(page.locator('th').filter({ hasText: '📋 Derecho' })).toBeVisible();

        // Buscar el vehículo de prueba específico (TEST-DOC)
        const vehiculoRow = page.locator('tr').filter({ hasText: 'TEST-DOC' });
        await expect(vehiculoRow).toBeVisible();

        // Verificar que el vehículo sea el esperado
        await expect(vehiculoRow).toContainText('Nissan');
        await expect(vehiculoRow).toContainText('Frontier Test Documentos');
        await expect(vehiculoRow).toContainText('TEST-DOC');

        // Verificar alertas de la póliza - debe estar "Próximo a Vencer"
        const polizaCell = vehiculoRow.locator('td').nth(5); // Columna de póliza
        await expect(polizaCell).toContainText('Próximo a Vencer');
        await expect(polizaCell).toContainText('14 días'); // Aproximadamente 14 días
        await expect(polizaCell).toContainText('03/09/2025'); // Fecha de vencimiento

        // Verificar alertas del derecho vehicular - debe estar "Próximo a Vencer"
        const derechoCell = vehiculoRow.locator('td').nth(6); // Columna de derecho
        await expect(derechoCell).toContainText('Próximo a Vencer');
        await expect(derechoCell).toContainText('4 días'); // Aproximadamente 4 días
        await expect(derechoCell).toContainText('24/08/2025'); // Fecha de vencimiento

        // Verificar que los badges tengan el color correcto (amarillo para "Próximo a Vencer")
        const polizaBadge = polizaCell.locator('.bg-yellow-100');
        await expect(polizaBadge).toBeVisible();

        const derechoBadge = derechoCell.locator('.bg-yellow-100');
        await expect(derechoBadge).toBeVisible();

        // Verificar los indicadores visuales (puntos de color)
        const polizaDot = polizaCell.locator('.bg-yellow-500');
        await expect(polizaDot).toBeVisible();

        const derechoDot = derechoCell.locator('.bg-yellow-500');
        await expect(derechoDot).toBeVisible();
    });

    test('debe filtrar correctamente por alertas de documentos', async ({ page }) => {
        // Navegar a la página de alertas
        await page.goto('http://localhost:8001/alertas/mantenimiento', { timeout: 30000 });
        await page.waitForSelector('table', { timeout: 30000 });

        // Usar el filtro para mostrar solo "Próximo a Vencer"
        await page.selectOption('select', 'Próximo a Vencer');

        // El vehículo TEST-DOC debe seguir siendo visible porque tiene documentos próximos a vencer
        const vehiculoRow = page.locator('tr').filter({ hasText: 'TEST-DOC' });
        await expect(vehiculoRow).toBeVisible();

        // Cambiar filtro a "Solo al día"
        await page.selectOption('select', 'OK');

        // Verificar que hay vehículos con estado OK (como Toyota Hilux)
        const vehiculosOK = page.locator('tr').filter({ hasText: 'OK' });
        await expect(vehiculosOK.first()).toBeVisible();
    });

    test('debe mostrar estadísticas actualizadas incluyendo documentos', async ({ page }) => {
        // Navegar a la página de alertas
        await page.goto('http://localhost:8001/alertas/mantenimiento', { timeout: 30000 });
        await page.waitForSelector('.grid', { timeout: 30000 });

        // Verificar que las estadísticas incluyan conteos
        const totalVehiculos = page.locator('text=Total Vehículos').locator('..').locator('p').nth(1);
        await expect(totalVehiculos).toContainText(/\d+/); // Al menos 1 vehículo

        const vencidos = page.locator('text=Vencidos').locator('..').locator('p').nth(1);
        await expect(vencidos).toContainText(/\d+/); // Debe haber algunos vencidos

        const proximos = page.locator('text=Próximos').locator('..').locator('p').nth(1);
        await expect(proximos).toContainText(/\d+/); // Debe haber algunos próximos
    });

    test('debe mostrar información de ayuda actualizada', async ({ page }) => {
        // Navegar a la página de alertas
        await page.goto('http://localhost:8001/alertas/mantenimiento', { timeout: 30000 });
        await page.waitForSelector('.bg-blue-50', { timeout: 30000 });

        // Verificar que la información de ayuda incluya documentos
        const infoSection = page.locator('.bg-blue-50');
        await expect(infoSection).toContainText('🔧 Alertas de Mantenimiento');
        await expect(infoSection).toContainText('📋 Alertas de Documentos');
        await expect(infoSection).toContainText('Próximo a Vencer');
        await expect(infoSection).toContainText('30 días'); // Umbral configurado
        await expect(infoSection).toContainText('Sin Fecha');
    });
});
