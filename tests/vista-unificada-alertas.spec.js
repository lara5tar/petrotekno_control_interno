import { test, expect } from '@playwright/test';

test.describe('Vista Unificada de Alertas', () => {

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

        // Esperar a que se complete el login
        await page.waitForURL('**/home', { timeout: 30000 });
    });

    test('debe mostrar vista unificada con todas las alertas', async ({ page }) => {
        // Navegar a la vista unificada de alertas
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });

        // Esperar a que la página cargue completamente esperando el título específico
        await page.waitForSelector('h1', { timeout: 30000 });

        // Verificar que el título sea correcto
        await expect(page.locator('h1').filter({ hasText: 'Centro de Alertas Unificado' })).toBeVisible();

        // Verificar que existan las estadísticas
        await expect(page.locator('text=Total Alertas')).toBeVisible();
        await expect(page.locator('p').filter({ hasText: 'Vencidas' }).first()).toBeVisible();
        await expect(page.locator('p').filter({ hasText: 'Próximas' }).first()).toBeVisible();
        await expect(page.locator('p').filter({ hasText: 'Mantenimiento' }).first()).toBeVisible();
        await expect(page.locator('p').filter({ hasText: 'Documentos' }).first()).toBeVisible();

        // Verificar que haya alertas mostradas
        const alertasContainer = page.locator('.divide-y');
        await expect(alertasContainer).toBeVisible();

        // Verificar que existan alertas de diferentes tipos
        await expect(page.locator('span').filter({ hasText: 'Mantenimiento' }).first()).toBeVisible();
        await expect(page.locator('span').filter({ hasText: 'Documentos' }).first()).toBeVisible();
    });

    test('debe mostrar alertas de documentos próximos a vencer', async ({ page }) => {
        // Navegar a la vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });
        await page.waitForSelector('h1', { timeout: 30000 });

        // Buscar alertas específicas de documentos próximos a vencer
        const alertaPoliza = page.locator('h3').filter({ hasText: 'Póliza de Seguro' }).first();
        const alertaDerecho = page.locator('h3').filter({ hasText: 'Derecho Vehicular' }).first();

        // Verificar que al menos una alerta de documentos esté visible
        await expect(alertaPoliza.or(alertaDerecho)).toBeVisible();

        // Verificar que las alertas muestren información relevante
        await expect(page.locator('text=días')).toBeVisible();
    });

    test('debe mostrar alertas de mantenimiento vencidas y próximas', async ({ page }) => {
        // Navegar a la vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });
        await page.waitForSelector('h1', { timeout: 30000 });

        // Verificar que existan alertas de diferentes sistemas de mantenimiento
        const alertaMotor = page.locator('h3').filter({ hasText: 'Motor' }).first();
        const alertaTransmision = page.locator('h3').filter({ hasText: 'Transmisión' }).first();

        // Al menos una alerta de mantenimiento debe estar visible
        await expect(alertaMotor.or(alertaTransmision)).toBeVisible();

        // Verificar información de kilometraje
        await expect(page.locator('text=km')).toBeVisible();
    });

    test('debe permitir filtrar por tipo de alerta', async ({ page }) => {
        // Navegar a la vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });
        await page.waitForSelector('h1', { timeout: 30000 });

        // Filtrar solo por alertas de mantenimiento
        await page.selectOption('select >> nth=0', 'mantenimiento');

        // Verificar que solo se muestren alertas de mantenimiento
        await expect(page.locator('span').filter({ hasText: 'Mantenimiento' }).first()).toBeVisible();

        // Cambiar filtro a solo documentos
        await page.selectOption('select >> nth=0', 'documento');

        // Verificar que solo se muestren alertas de documentos
        await expect(page.locator('span').filter({ hasText: 'Documentos' }).first()).toBeVisible();
    });

    test('debe permitir filtrar por estado de alerta', async ({ page }) => {
        // Navegar a la vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });
        await page.waitForSelector('h1', { timeout: 30000 });

        // Filtrar solo alertas vencidas
        await page.selectOption('select >> nth=1', 'Vencido');

        // Verificar que se muestren alertas vencidas
        await expect(page.locator('span').filter({ hasText: 'Vencido' }).first()).toBeVisible();

        // Cambiar filtro a próximas (documentos)
        await page.selectOption('select >> nth=1', 'Próximo a Vencer');

        // Verificar que se muestren alertas próximas a vencer
        await expect(page.locator('span').filter({ hasText: 'Próximo a Vencer' }).first()).toBeVisible();
    });

    test('debe mostrar vehículo TEST-DOC con documentos próximos a vencer', async ({ page }) => {
        // Navegar a la vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });
        await page.waitForSelector('h1', { timeout: 30000 });

        // Buscar específicamente el vehículo TEST-DOC
        const vehiculoTestDoc = page.locator('span').filter({ hasText: 'TEST-DOC' }).first();
        await expect(vehiculoTestDoc).toBeVisible();

        // Verificar que aparezca Nissan Frontier
        await expect(page.locator('text=Nissan Frontier Test Documentos')).toBeVisible();

        // Verificar que tenga alertas de documentos próximos a vencer
        const alertaContainer = page.locator('div').filter({ hasText: 'TEST-DOC' }).first();
        await expect(alertaContainer).toContainText('Próximo a Vencer');
    });

    test('debe mostrar información detallada al expandir alertas', async ({ page }) => {
        // Navegar a la vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });
        await page.waitForSelector('h1', { timeout: 30000 });

        // Buscar el botón "Ver detalles"
        const botonDetalles = page.locator('button:has-text("Ver detalles")').first();
        await expect(botonDetalles).toBeVisible({ timeout: 10000 });

        // Hacer clic en "Ver detalles" de la primera alerta
        await botonDetalles.click();

        // Verificar que se muestren detalles adicionales
        await expect(page.locator('text=Detalles técnicos')).toBeVisible();
    });

    test('debe tener enlaces de navegación funcionando', async ({ page }) => {
        // Navegar a la vista unificada
        await page.goto('http://localhost:8001/alertas/unificada', { timeout: 30000 });
        await page.waitForSelector('h1', { timeout: 30000 });

        // Verificar que exista el enlace a la vista tradicional
        const enlaceTradicional = page.locator('a:has-text("Ver vista de tabla tradicional")');
        await expect(enlaceTradicional).toBeVisible({ timeout: 10000 });

        // Verificar que existan enlaces a acciones específicas
        await expect(page.locator('a:has-text("Ver vehículo")').first()).toBeVisible({ timeout: 10000 });
    });
});
