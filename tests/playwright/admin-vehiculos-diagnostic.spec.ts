import { test, expect } from '@playwright/test';

test.describe('Diagnóstico Admin - Crear Vehículos', () => {

    test('verificar acceso admin a crear vehículos', async ({ page }) => {
        console.log('🔍 Iniciando diagnóstico de acceso admin a crear vehículos...');

        // 1. Ir al login
        await page.goto('/login');
        await page.waitForLoadState('networkidle');

        console.log('📍 Página de login cargada');

        // 2. Login como admin (usando credenciales típicas de admin)
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        await page.waitForLoadState('networkidle');
        console.log('🔐 Intento de login realizado');

        // 3. Verificar si el login fue exitoso
        const currentUrl = page.url();
        console.log(`📍 URL actual después del login: ${currentUrl}`);

        // Verificar que no estamos en la página de login
        const isStillInLogin = currentUrl.includes('/login');
        if (isStillInLogin) {
            console.log('❌ PROBLEMA: Aún estamos en la página de login');

            // Verificar si hay mensajes de error
            const errorMessages = await page.locator('.alert-danger, .text-red-500, .error').allTextContents();
            console.log('🚨 Mensajes de error encontrados:', errorMessages);

            // Intentar con otras credenciales comunes
            console.log('🔄 Intentando con credenciales alternativas...');

            const credencialesAlternativas = [
                { email: 'admin@admin.com', password: 'admin' },
                { email: 'admin@localhost', password: 'admin123' },
                { email: 'administrador@petrotekno.com', password: 'admin' },
                { email: 'admin', password: 'admin' }
            ];

            for (const creds of credencialesAlternativas) {
                await page.fill('input[name="email"]', creds.email);
                await page.fill('input[name="password"]', creds.password);
                await page.click('button[type="submit"]');
                await page.waitForLoadState('networkidle');

                const newUrl = page.url();
                if (!newUrl.includes('/login')) {
                    console.log(`✅ Login exitoso con: ${creds.email}`);
                    break;
                }
            }
        } else {
            console.log('✅ Login exitoso');
        }

        // 4. Verificar información del usuario logueado
        const userInfo = await page.locator('.user-name, .username, [data-testid="user-name"]').first().textContent().catch(() => null);
        console.log(`👤 Usuario logueado: ${userInfo || 'No detectado'}`);

        // 5. Intentar acceder al módulo de vehículos
        console.log('🚗 Intentando acceder al módulo de vehículos...');

        // Buscar el enlace/botón de vehículos en el menú
        const vehiculosLink = page.locator('a:has-text("Vehículos"), button:has-text("Vehículos")').first();
        const vehiculosExists = await vehiculosLink.count() > 0;

        if (vehiculosExists) {
            console.log('✅ Enlace de vehículos encontrado en el menú');
            await vehiculosLink.click();
            await page.waitForLoadState('networkidle');
        } else {
            console.log('❌ Enlace de vehículos NO encontrado en el menú');

            // Intentar acceso directo por URL
            console.log('🔄 Intentando acceso directo a /vehiculos');
            await page.goto('/vehiculos');
            await page.waitForLoadState('networkidle');
        }

        // 6. Verificar si llegamos a la página de vehículos
        const vehiculosPageUrl = page.url();
        console.log(`📍 URL después de intentar acceder a vehículos: ${vehiculosPageUrl}`);

        if (vehiculosPageUrl.includes('/vehiculos')) {
            console.log('✅ Acceso exitoso a la página de vehículos');

            // 7. Buscar el botón "Agregar Vehículo" o "Crear Vehículo"
            const agregarBtn = page.locator('a:has-text("Agregar"), a:has-text("Crear"), a:has-text("Nuevo"), button:has-text("Agregar"), button:has-text("Crear")').first();
            const agregarBtnExists = await agregarBtn.count() > 0;

            if (agregarBtnExists) {
                console.log('✅ Botón "Agregar Vehículo" encontrado');

                const btnText = await agregarBtn.textContent();
                console.log(`📝 Texto del botón: ${btnText}`);

                // Intentar hacer clic en el botón
                await agregarBtn.click();
                await page.waitForLoadState('networkidle');

                const createUrl = page.url();
                console.log(`📍 URL después de hacer clic en agregar: ${createUrl}`);

                if (createUrl.includes('/create') || createUrl.includes('/crear')) {
                    console.log('✅ ÉXITO: Acceso al formulario de crear vehículo logrado');

                    // Verificar que el formulario se carga correctamente
                    const formExists = await page.locator('form').count() > 0;
                    const marcaField = await page.locator('input[name="marca"]').count() > 0;

                    console.log(`📋 Formulario presente: ${formExists ? 'Sí' : 'No'}`);
                    console.log(`📝 Campo marca presente: ${marcaField ? 'Sí' : 'No'}`);

                } else {
                    console.log('❌ PROBLEMA: No se pudo acceder al formulario de crear vehículo');
                    console.log(`📍 Redirigido a: ${createUrl}`);
                }

            } else {
                console.log('❌ PROBLEMA: Botón "Agregar Vehículo" NO encontrado');

                // Intentar acceso directo al create
                console.log('🔄 Intentando acceso directo a /vehiculos/create');
                await page.goto('/vehiculos/create');
                await page.waitForLoadState('networkidle');

                const directCreateUrl = page.url();
                console.log(`📍 URL acceso directo create: ${directCreateUrl}`);

                if (directCreateUrl.includes('/create')) {
                    console.log('✅ Acceso directo al formulario exitoso');
                } else {
                    console.log('❌ Acceso directo al formulario FALLIDO');

                    // Verificar si hay mensajes de error o falta de permisos
                    const errorMessages = await page.locator('.alert-danger, .text-red-500, .error, .unauthorized').allTextContents();
                    console.log('🚨 Mensajes de error:', errorMessages);
                }
            }

        } else {
            console.log('❌ PROBLEMA: No se pudo acceder a la página de vehículos');

            // Verificar si fuimos redirigidos a algún lugar específico
            if (vehiculosPageUrl.includes('/login')) {
                console.log('🚨 Redirigido al login - posible problema de autenticación');
            } else if (vehiculosPageUrl.includes('/403') || vehiculosPageUrl.includes('unauthorized')) {
                console.log('🚨 Error 403 - falta de permisos');
            } else {
                console.log(`🚨 Redirigido a: ${vehiculosPageUrl}`);
            }
        }

        // 8. Verificar permisos del usuario actual
        console.log('🔒 Verificando permisos del usuario...');

        // Intentar acceder a otras páginas para verificar nivel de acceso
        const paginasTest = ['/personal', '/obras', '/dashboard'];

        for (const pagina of paginasTest) {
            await page.goto(pagina);
            await page.waitForLoadState('networkidle');
            const testUrl = page.url();
            const tieneAcceso = testUrl.includes(pagina.substring(1));
            console.log(`📄 Acceso a ${pagina}: ${tieneAcceso ? 'SÍ' : 'NO'}`);
        }

        // 9. Capturar screenshot para análisis visual
        await page.screenshot({ path: 'debug-admin-vehiculos-acceso.png', fullPage: true });
        console.log('📸 Screenshot guardado como debug-admin-vehiculos-acceso.png');

        // 10. Verificar HTML del menú para análisis
        const menuHtml = await page.locator('nav, .sidebar, .menu').first().innerHTML().catch(() => 'Menu no encontrado');
        console.log('🔍 HTML del menú principal:', menuHtml.substring(0, 500) + '...');

    });

    test('verificar permisos específicos de vehículos', async ({ page }) => {
        console.log('🔐 Verificando permisos específicos para vehículos...');

        // Login (asumir que ya sabemos las credenciales del test anterior)
        await page.goto('/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Verificar si existe una página de permisos o configuración
        const urlsPermisosTest = [
            '/admin/permisos',
            '/permisos',
            '/admin/usuarios',
            '/configuracion',
            '/profile',
            '/admin'
        ];

        for (const url of urlsPermisosTest) {
            await page.goto(url);
            await page.waitForLoadState('networkidle');
            const currentUrl = page.url();

            if (currentUrl.includes(url.substring(1))) {
                console.log(`✅ Acceso a ${url} disponible`);

                // Buscar información sobre permisos de vehículos
                const vehiculosPermissions = await page.locator('*:has-text("vehiculo"), *:has-text("vehículo")').allTextContents();
                if (vehiculosPermissions.length > 0) {
                    console.log('🔍 Permisos relacionados con vehículos encontrados:', vehiculosPermissions);
                }
            }
        }
    });

});