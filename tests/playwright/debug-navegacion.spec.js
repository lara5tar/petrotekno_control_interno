import { test } from '@playwright/test';

test('Debug navegación completa', async ({ page }) => {
    console.log('🔍 Debug navegación paso a paso...');

    // Interceptar respuestas para detectar redirecciones
    page.on('response', response => {
        if (response.status() >= 400) {
            console.log(`❌ Error HTTP: ${response.status()} ${response.url()}`);
        } else if (response.status() >= 300) {
            console.log(`🔄 Redirección: ${response.status()} ${response.url()}`);
        }
    });

    // Login
    await page.goto('http://localhost:8000/login');
    console.log('📍 En página de login');

    await page.fill('input[name="email"]', 'admin@admin.com');
    await page.fill('input[name="password"]', '123456');
    await page.click('button[type="submit"]');

    await page.waitForLoadState('networkidle');
    console.log(`📍 Después del login: ${page.url()}`);

    // Verificar si llegamos al dashboard
    const titulo = await page.title();
    console.log(`📄 Título de la página: ${titulo}`);

    // Intentar ir a vehículos directamente
    await page.goto('http://localhost:8000/vehiculos');
    await page.waitForLoadState('networkidle');
    console.log(`📍 En página de vehículos: ${page.url()}`);

    // Buscar enlaces o botones relacionados con vehículos
    const enlaces = await page.locator('a').all();
    console.log(`🔗 Enlaces encontrados: ${enlaces.length}`);

    for (let i = 0; i < Math.min(enlaces.length, 10); i++) {
        const texto = await enlaces[i].textContent();
        const href = await enlaces[i].getAttribute('href');
        if (texto && texto.toLowerCase().includes('vehic')) {
            console.log(`   Enlace vehículo: "${texto}" -> ${href}`);
        }
    }

    // Buscar si hay algún vehículo listado
    const vehiculos = await page.locator('*:has-text("vehículo"), *:has-text("Vehículo")').all();
    console.log(`🚗 Referencias a vehículos: ${vehiculos.length}`);

    // Tomar screenshot
    await page.screenshot({ path: 'debug-navegacion-completa.png', fullPage: true });

    console.log('🎉 Debug completado');
});
