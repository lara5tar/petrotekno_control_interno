import { test, expect } from '@playwright/test';

test('Logs system debug - step by step', async ({ page }) => {
    console.log('🔍 Iniciando diagnóstico completo del sistema de logs...');

    // Habilitar logging de requests y errores
    page.on('console', msg => console.log('BROWSER:', msg.text()));
    page.on('request', request => console.log('REQUEST:', request.url()));
    page.on('response', response => {
        if (!response.ok()) {
            console.log('ERROR RESPONSE:', response.status(), response.url());
        }
    });

    try {
        // Paso 1: Verificar que el servidor responde
        console.log('📡 Paso 1: Verificando servidor...');
        const response = await page.goto('http://127.0.0.1:8001/admin/logs', {
            waitUntil: 'networkidle',
            timeout: 30000
        });

        console.log('✅ Respuesta del servidor:', response.status());
        expect(response.ok()).toBeTruthy();

        // Paso 2: Verificar el contenido HTML
        console.log('📄 Paso 2: Verificando contenido HTML...');
        const htmlContent = await page.content();
        console.log('📝 Longitud del HTML:', htmlContent.length);

        if (htmlContent.length < 100) {
            console.log('❌ Contenido HTML muy corto, posible error');
            console.log('HTML recibido:', htmlContent);
        }

        // Paso 3: Verificar errores en consola
        console.log('🐛 Paso 3: Verificando errores...');

        // Paso 4: Verificar elementos básicos
        console.log('🔍 Paso 4: Verificando elementos básicos...');

        // Verificar si hay contenido visible
        const bodyText = await page.textContent('body');
        console.log('📖 Texto del body:', bodyText ? bodyText.substring(0, 200) + '...' : 'NO HAY TEXTO');

        // Verificar si existe el título
        const title = await page.title();
        console.log('📋 Título de la página:', title);

        // Verificar elementos específicos
        const h2 = await page.locator('h2').first();
        if (await h2.count() > 0) {
            console.log('✅ Encontrado H2:', await h2.textContent());
        } else {
            console.log('❌ No se encontró H2');
        }

        // Verificar breadcrumb
        const breadcrumb = await page.locator('[class*="breadcrumb"], nav, .breadcrumb');
        if (await breadcrumb.count() > 0) {
            console.log('✅ Breadcrumb encontrado');
        } else {
            console.log('❌ No se encontró breadcrumb');
        }

        // Verificar tabla
        const table = await page.locator('table');
        if (await table.count() > 0) {
            console.log('✅ Tabla encontrada');
        } else {
            console.log('❌ No se encontró tabla');
        }

        // Paso 5: Verificar layout
        console.log('🎨 Paso 5: Verificando layout...');
        const layoutMain = await page.locator('main, [id*="app"], .container');
        if (await layoutMain.count() > 0) {
            console.log('✅ Layout principal encontrado');
        } else {
            console.log('❌ No se encontró layout principal');
        }

    } catch (error) {
        console.log('💥 Error durante la prueba:', error.message);

        // Capturar screenshot para debug
        await page.screenshot({ path: 'debug-logs-error.png', fullPage: true });
        console.log('📸 Screenshot guardado como debug-logs-error.png');

        throw error;
    }
});
