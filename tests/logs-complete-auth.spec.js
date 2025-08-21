import { test, expect } from '@playwright/test';

test('Logs system con autenticación completa', async ({ page }) => {
    console.log('🔐 Iniciando test completo con autenticación...');

    // Habilitar logging
    page.on('console', msg => console.log('BROWSER:', msg.text()));
    page.on('response', response => {
        if (!response.ok() && response.status() !== 302) {
            console.log('ERROR RESPONSE:', response.status(), response.url());
        }
    });

    try {
        // Paso 1: Ir al login
        console.log('🚪 Paso 1: Accediendo al login...');
        await page.goto('http://127.0.0.1:8000/login');

        // Verificar que estamos en la página de login
        await expect(page.locator('text=Iniciar Sesión')).toBeVisible();
        console.log('✅ Página de login cargada');

        // Paso 2: Hacer login
        console.log('👤 Paso 2: Realizando login...');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Esperar a que se complete la redirección
        await page.waitForURL('**/home', { timeout: 10000 });
        console.log('✅ Login exitoso, redirigido al dashboard');

        // Paso 3: Ir a los logs
        console.log('📊 Paso 3: Navegando a logs...');
        const response = await page.goto('http://127.0.0.1:8000/admin/logs');
        console.log('📡 Respuesta de logs:', response.status());

        // Esperar a que cargue la página
        await page.waitForLoadState('networkidle');

        // Paso 4: Verificar contenido
        console.log('🔍 Paso 4: Verificando contenido de logs...');

        // Verificar título
        const title = await page.title();
        console.log('📋 Título:', title);
        expect(title).toContain('Logs del Sistema');

        // Verificar H2
        const h2 = await page.locator('h2:has-text("Logs del Sistema")');
        await expect(h2).toBeVisible();
        console.log('✅ H2 encontrado:', await h2.textContent());

        // Verificar breadcrumb
        const breadcrumb = await page.locator('text=Inicio');
        if (await breadcrumb.count() > 0) {
            console.log('✅ Breadcrumb encontrado');
        }

        // Verificar botón volver
        const volverBtn = await page.locator('text=Volver a Configuración');
        await expect(volverBtn).toBeVisible();
        console.log('✅ Botón volver encontrado');

        // Verificar filtros
        const filtrosForm = await page.locator('form#filtrosForm');
        await expect(filtrosForm).toBeVisible();
        console.log('✅ Formulario de filtros encontrado');

        // Verificar select de usuarios
        const usuarioSelect = await page.locator('select[name="usuario_id"]');
        await expect(usuarioSelect).toBeVisible();
        console.log('✅ Select de usuarios encontrado');

        // Verificar tabla
        const table = await page.locator('table');
        await expect(table).toBeVisible();
        console.log('✅ Tabla encontrada');

        // Verificar headers de tabla
        const headers = await page.locator('thead th').allTextContents();
        console.log('📋 Headers de tabla:', headers);
        expect(headers.length).toBeGreaterThan(0);

        // Verificar si hay datos o mensaje de "no hay registros"
        const hasData = await page.locator('tbody tr').count();
        if (hasData > 0) {
            console.log('✅ Tabla con datos:', hasData, 'filas');
        } else {
            const noDataMessage = await page.locator('text=No hay registros de logs');
            if (await noDataMessage.count() > 0) {
                console.log('ℹ️ Tabla sin datos (mensaje mostrado correctamente)');
            }
        }

        console.log('🎉 ¡Test completado exitosamente! Sistema de logs funcionando correctamente.');

    } catch (error) {
        console.log('💥 Error durante la prueba:', error.message);

        // Capturar screenshot para debug
        await page.screenshot({ path: 'debug-logs-auth-error.png', fullPage: true });
        console.log('📸 Screenshot guardado como debug-logs-auth-error.png');

        // Mostrar URL actual para debug
        console.log('🌐 URL actual:', page.url());

        throw error;
    }
});
