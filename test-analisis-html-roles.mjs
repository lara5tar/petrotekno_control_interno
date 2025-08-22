import { chromium } from 'playwright';

async function testRolesSinLogin() {
    console.log('🎯 VERIFICACIÓN DIRECTA: Revisando HTML del formulario');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 500
    });

    try {
        const context = await browser.newContext();
        const page = await context.newPage();

        console.log('📱 Navegando a la página de edición...');

        // Intentar acceder directamente a la página
        try {
            await page.goto('http://127.0.0.1:8080/personal/1/edit');
            await page.waitForLoadState('networkidle');
        } catch (error) {
            console.log('⚠️ Error en navegación directa, intentando con login...');

            // Si falla, hacer login primero
            await page.goto('http://127.0.0.1:8080/login');
            await page.waitForLoadState('networkidle');

            console.log('🔐 Realizando login...');
            await page.fill('input[name="email"]', 'admin@petrotekno.com');
            await page.fill('input[name="password"]', 'password123');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');

            // Ahora ir a la página de edición
            await page.goto('http://127.0.0.1:8080/personal/1/edit');
            await page.waitForLoadState('networkidle');
        }

        const url = page.url();
        console.log(`📍 URL actual: ${url}`);

        // Capturar screenshot inicial
        await page.screenshot({
            path: 'debug-pagina-inicial.png',
            fullPage: true
        });
        console.log('📸 Screenshot inicial guardado: debug-pagina-inicial.png');

        // Obtener todo el HTML de la página
        const pageContent = await page.content();
        console.log('\n🔍 Buscando elementos relacionados con roles en el HTML...');

        // Buscar select de roles en el HTML
        const hasRoleSelect = pageContent.includes('rol_usuario');
        console.log(`🎯 Select de roles en HTML: ${hasRoleSelect}`);

        if (hasRoleSelect) {
            // Extraer la sección del select
            const selectMatch = pageContent.match(/<select[^>]*name=["\']rol_usuario["\'][^>]*>.*?<\/select>/s);
            if (selectMatch) {
                console.log('\n📋 HTML del select de roles:');
                console.log(selectMatch[0]);

                // Extraer opciones
                const optionMatches = selectMatch[0].match(/<option[^>]*>([^<]+)<\/option>/g);
                if (optionMatches) {
                    console.log('\n📝 Opciones encontradas en HTML:');
                    optionMatches.forEach((option, index) => {
                        const text = option.replace(/<[^>]*>/g, '');
                        console.log(`  ${index + 1}. ${text}`);
                    });

                    // Verificar roles específicos
                    const rolesEsperados = ['Admin', 'Supervisor', 'Operador'];
                    const rolesEncontrados = [];

                    for (const rol of rolesEsperados) {
                        const encontrado = optionMatches.some(option => option.includes(rol));
                        if (encontrado) {
                            rolesEncontrados.push(rol);
                            console.log(`✅ Rol "${rol}" encontrado en HTML`);
                        } else {
                            console.log(`❌ Rol "${rol}" NO encontrado en HTML`);
                        }
                    }

                    console.log('\n🎉 RESULTADO ANÁLISIS HTML:');
                    console.log(`📊 Roles esperados: ${rolesEsperados.length}`);
                    console.log(`📊 Roles encontrados: ${rolesEncontrados.length}`);
                    console.log(`📋 Lista de roles: ${rolesEncontrados.join(', ')}`);

                    if (rolesEncontrados.length === 3) {
                        console.log('🎯 ¡ÉXITO! Los 3 roles están presentes en el HTML');
                    }
                } else {
                    console.log('❌ No se encontraron opciones en el select');
                }
            } else {
                console.log('❌ No se pudo extraer el HTML del select');
            }
        } else {
            console.log('❌ No se encontró select de rol_usuario en el HTML');

            // Buscar todos los selects
            const selectMatches = pageContent.match(/<select[^>]*>.*?<\/select>/gs);
            if (selectMatches) {
                console.log(`\n📋 Encontrados ${selectMatches.length} selects en la página:`);
                selectMatches.forEach((select, index) => {
                    const nameMatch = select.match(/name=["\']([^"\']+)["\']/);
                    const name = nameMatch ? nameMatch[1] : 'sin nombre';
                    console.log(`  ${index + 1}. ${name}`);
                });
            }
        }

        // Buscar también variables de roles en el HTML
        console.log('\n🔍 Buscando variables de roles...');
        const hasRolesVar = pageContent.includes('$roles') || pageContent.includes('roles');
        console.log(`📊 Variables de roles en HTML: ${hasRolesVar}`);

        // Buscar comentarios de debug
        const debugMatches = pageContent.match(/<!--.*?-->/gs);
        if (debugMatches) {
            console.log('\n💭 Comentarios de debug encontrados:');
            debugMatches.forEach((comment, index) => {
                if (comment.includes('roles') || comment.includes('DEBUG')) {
                    console.log(`  ${index + 1}. ${comment}`);
                }
            });
        }

        console.log('\n🏆 ANÁLISIS COMPLETADO');

    } catch (error) {
        console.error('❌ ERROR durante el análisis:', error.message);

        // Capturar screenshot del error
        try {
            await page.screenshot({
                path: 'error-analisis-html.png',
                fullPage: true
            });
            console.log('📸 Screenshot de error guardado: error-analisis-html.png');
        } catch (screenshotError) {
            console.error('Error al capturar screenshot:', screenshotError.message);
        }

        throw error;
    } finally {
        await browser.close();
    }
}

// Ejecutar el análisis
testRolesSinLogin()
    .then(() => {
        console.log('\n🎊 ¡ANÁLISIS COMPLETADO!');
        process.exit(0);
    })
    .catch((error) => {
        console.error('\n💥 ANÁLISIS FALLIDO:', error.message);
        process.exit(1);
    });
