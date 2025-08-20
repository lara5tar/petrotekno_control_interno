import { test, expect } from '@playwright/test';

test.describe('Verificación del Contador de Campanita', () => {
    test('La campanita debe mostrar el número correcto de alertas', async ({ page }) => {
        // Ir a la página de login
        await page.goto('http://localhost:8003/login');

        // Hacer login
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');

        // Esperar a que cargue el dashboard
        await page.waitForLoadState('networkidle');

        // Ir directamente a la vista de alertas unificadas para obtener el total real
        await page.goto('http://localhost:8003/alertas/unificada');
        await page.waitForLoadState('networkidle');

        // Obtener el número total de alertas de la vista
        const totalAlertas = await page.locator('div:has-text("Total Alertas") + div p:nth-child(2)').textContent();
        const numeroTotalAlertas = parseInt(totalAlertas.trim());

        console.log('Total de alertas en la vista:', numeroTotalAlertas);

        // Verificar que hay alertas
        expect(numeroTotalAlertas).toBeGreaterThan(0);

        // Ahora verificar que la campanita muestre el mismo número
        // Buscar el badge de la campanita
        const badgeCampanita = page.locator('a[title*="Centro de Alertas"] span.absolute');

        if (await badgeCampanita.count() > 0) {
            const numeroCampanita = await badgeCampanita.textContent();
            const numeroEnCampanita = parseInt(numeroCampanita.replace('+', '').trim());

            console.log('Número en campanita:', numeroEnCampanita);
            console.log('Número total en vista:', numeroTotalAlertas);

            // Verificar que los números coincidan (o que la campanita muestre 99+ si hay más de 99)
            if (numeroTotalAlertas <= 99) {
                expect(numeroEnCampanita).toBe(numeroTotalAlertas);
            } else {
                expect(numeroCampanita).toBe('99+');
            }

            console.log('✅ La campanita muestra el número correcto de alertas');
        } else {
            // Si no hay badge, debería significar que no hay alertas
            expect(numeroTotalAlertas).toBe(0);
            console.log('✅ No hay alertas, campanita sin badge - correcto');
        }
    });

    test('Verificar detalles de las alertas en la vista', async ({ page }) => {
        // Login
        await page.goto('http://localhost:8003/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Ir a alertas
        await page.goto('http://localhost:8003/alertas/unificada');
        await page.waitForLoadState('networkidle');        // Verificar estadísticas
        const totalAlertas = await page.locator('div:has-text("Total Alertas") + div p:nth-child(2)').textContent();
        const vencidas = await page.locator('div:has-text("Vencidas") + div p:nth-child(2)').textContent();
        const proximas = await page.locator('div:has-text("Próximas") + div p:nth-child(2)').textContent();
        const mantenimiento = await page.locator('div:has-text("Mantenimiento") + div p:nth-child(2)').textContent();
        const documentos = await page.locator('div:has-text("Documentos") + div p:nth-child(2)').textContent();

        console.log('Estadísticas de alertas:');
        console.log('- Total:', totalAlertas.trim());
        console.log('- Vencidas:', vencidas.trim());
        console.log('- Próximas:', proximas.trim());
        console.log('- Mantenimiento:', mantenimiento.trim());
        console.log('- Documentos:', documentos.trim());

        // Verificar que la suma de categorías no exceda el total
        const numVencidas = parseInt(vencidas.trim());
        const numProximas = parseInt(proximas.trim());
        const numTotal = parseInt(totalAlertas.trim());

        expect(numVencidas + numProximas).toBeLessThanOrEqual(numTotal);

        console.log('✅ Estadísticas de alertas coherentes');
    });
});
