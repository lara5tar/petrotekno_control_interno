import { chromium } from 'playwright';

// Test simplificado que se conecta directamente y usa un headless browser más confiable
(async () => {
    console.log('🚀 Iniciando test simplificado...');

    const browser = await chromium.launch({
        headless: false, // Usar headless para más confiabilidad
        args: ['--no-sandbox', '--disable-setuid-sandbox'] // Argumentos adicionales para Linux
    });

    const context = await browser.newContext({
        // Deshabilitar algunas características que pueden causar problemas
        javaScriptEnabled: true,
        acceptDownloads: true,
    });

    const page = await context.newPage();

    try {
        console.log('📱 Intentando acceder al login...');
        await page.goto('http://127.0.0.1:8000/login', {
            waitUntil: 'networkidle',
            timeout: 10000
        });

        console.log('✅ Login accesible, página cargada');

        // Verificar que estamos en la página de login
        const title = await page.title();
        console.log('🏷️ Título de la página:', title);

        // Tomar una captura de pantalla para debug
        await page.screenshot({ path: 'login-screenshot.png' });
        console.log('📸 Captura guardada: login-screenshot.png');

        // Intentar hacer login con un usuario administrador
        // (primero necesitamos conocer las credenciales)
        const emailField = await page.locator('input[name="email"], input[type="email"]').first();
        const passwordField = await page.locator('input[name="password"], input[type="password"]').first();

        if (await emailField.count() > 0 && await passwordField.count() > 0) {
            console.log('📝 Campos de login encontrados, intentando hacer login...');

            await emailField.fill('admin@admin.com');  // Usuario común en seeders
            await passwordField.fill('password');

            // Buscar el botón de envío
            const submitButton = await page.locator('button[type="submit"], input[type="submit"]').first();
            await submitButton.click();

            await page.waitForLoadState('networkidle');

            const currentUrl = page.url();
            console.log('🔗 URL después del login:', currentUrl);

            if (currentUrl.includes('/dashboard') || currentUrl.includes('/personal') || !currentUrl.includes('/login')) {
                console.log('✅ Login exitoso');

                // Ahora intentar acceder al formulario de personal
                console.log('📱 Navegando al formulario de personal...');
                await page.goto('http://127.0.0.1:8000/personal/create', {
                    waitUntil: 'networkidle',
                    timeout: 10000
                });

                console.log('✅ Formulario de personal accesible');

                // Tomar captura del formulario
                await page.screenshot({ path: 'personal-form-screenshot.png' });
                console.log('📸 Captura del formulario guardada: personal-form-screenshot.png');

                // Verificar que los campos están presentes
                const campos = {
                    'nombre_completo': await page.locator('input[name="nombre_completo"]').count(),
                    'categoria_personal_id': await page.locator('select[name="categoria_personal_id"]').count(),
                    'ine': await page.locator('input[name="ine"]').count(),
                    'curp_numero': await page.locator('input[name="curp_numero"]').count(),
                    'rfc': await page.locator('input[name="rfc"]').count(),
                    'nss': await page.locator('input[name="nss"]').count(),
                    'no_licencia': await page.locator('input[name="no_licencia"]').count(),
                    'direccion': await page.locator('textarea[name="direccion"]').count(),
                    'archivo_ine': await page.locator('input[name="archivo_ine"]').count(),
                };

                console.log('🔍 Campos encontrados en el formulario:');
                for (const [campo, count] of Object.entries(campos)) {
                    if (count > 0) {
                        console.log(`  ✅ ${campo}: encontrado`);
                    } else {
                        console.log(`  ❌ ${campo}: NO encontrado`);
                    }
                }

                console.log('🎉 Test de conectividad completado exitosamente!');

            } else {
                console.log('❌ Login falló, aún en página de login');
            }
        } else {
            console.log('❌ No se encontraron campos de login');
        }

    } catch (error) {
        console.log('💥 Error durante el test:', error.message);

        // Tomar una captura en caso de error
        try {
            await page.screenshot({ path: 'error-screenshot.png' });
            console.log('📸 Captura de error guardada: error-screenshot.png');
        } catch (screenshotError) {
            console.log('❌ No se pudo tomar captura de error');
        }
    } finally {
        await browser.close();
        console.log('🔚 Test finalizado');
    }
})();
