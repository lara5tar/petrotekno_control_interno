import { chromium } from 'playwright';

async function testRolesFormularioPersonal() {
    console.log('🎯 VERIFICACIÓN FINAL: Probando que aparecen los 3 roles en editar personal');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 1000
    });

    try {
        const context = await browser.newContext();
        const page = await context.newPage();

        console.log('📱 Navegando al login...');
        await page.goto('http://127.0.0.1:8080/login');
        await page.waitForLoadState('networkidle');

        console.log('🔐 Realizando login...');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        console.log('👥 Navegando a lista de personal...');
        await page.goto('http://127.0.0.1:8080/personal');
        await page.waitForLoadState('networkidle');

        console.log('✏️ Buscando botón de editar...');
        const editButton = page.locator('a[href*="/personal/"][href*="/edit"]').first();
        await editButton.waitFor({ timeout: 10000 });
        await editButton.click();
        await page.waitForLoadState('networkidle');

        console.log('🔍 Verificando formulario de edición...');

        // Verificar que estamos en página de edición
        const url = page.url();
        console.log(`📍 URL actual: ${url}`);

        if (!url.includes('/edit')) {
            throw new Error('No estamos en la página de edición');
        }

        // Buscar el checkbox "Crear Usuario"
        console.log('☑️ Activando checkbox "Crear Usuario"...');
        const checkbox = page.locator('input[type="checkbox"][x-model="crearUsuario"]');
        await checkbox.waitFor({ timeout: 5000 });
        await checkbox.check();

        // Esperar a que aparezcan los campos de usuario
        await page.waitForTimeout(1000);

        // Buscar el select de roles
        console.log('🎯 Buscando select de roles...');
        const roleSelect = page.locator('select[name="rol_usuario"]');
        await roleSelect.waitFor({ timeout: 5000 });

        // Verificar que el select existe y es visible
        const isVisible = await roleSelect.isVisible();
        console.log(`👁️ Select de roles visible: ${isVisible}`);

        if (!isVisible) {
            throw new Error('El select de roles no es visible');
        }

        // Obtener todas las opciones del select
        console.log('📋 Obteniendo opciones del select...');
        const options = await roleSelect.locator('option').allTextContents();
        console.log('📝 Opciones encontradas:', options);

        // Verificar que existen exactamente 4 opciones (incluyendo "Seleccione un rol")
        if (options.length < 4) {
            throw new Error(`Se esperaban 4 opciones (incluyendo placeholder), pero se encontraron ${options.length}`);
        }

        // Verificar que contiene los 3 roles esperados
        const rolesEsperados = ['Admin', 'Supervisor', 'Operador'];
        const rolesEncontrados = [];

        for (const rol of rolesEsperados) {
            const encontrado = options.some(option => option.includes(rol));
            if (encontrado) {
                rolesEncontrados.push(rol);
                console.log(`✅ Rol "${rol}" encontrado`);
            } else {
                console.log(`❌ Rol "${rol}" NO encontrado`);
            }
        }

        console.log('\n🎉 RESULTADO FINAL:');
        console.log(`📊 Roles esperados: ${rolesEsperados.length}`);
        console.log(`📊 Roles encontrados: ${rolesEncontrados.length}`);
        console.log(`📋 Lista de roles encontrados: ${rolesEncontrados.join(', ')}`);

        if (rolesEncontrados.length === 3) {
            console.log('🎯 ¡ÉXITO! Los 3 roles aparecen correctamente en el formulario');

            // Probar seleccionar cada rol
            console.log('\n🔄 Probando selección de roles...');
            for (const rol of rolesEsperados) {
                const option = page.locator(`select[name="rol_usuario"] option:has-text("${rol}")`);
                if (await option.count() > 0) {
                    await roleSelect.selectOption({ label: rol });
                    console.log(`✅ Rol "${rol}" seleccionado correctamente`);
                    await page.waitForTimeout(500);
                }
            }

        } else {
            throw new Error(`Solo se encontraron ${rolesEncontrados.length} de 3 roles esperados`);
        }

        console.log('\n🏆 VERIFICACIÓN COMPLETADA CON ÉXITO');
        console.log('✨ Los 3 roles (Admin, Supervisor, Operador) aparecen correctamente');

    } catch (error) {
        console.error('❌ ERROR durante la verificación:', error.message);

        // Capturar screenshot del error
        try {
            await page.screenshot({
                path: 'error-verificacion-final-roles.png',
                fullPage: true
            });
            console.log('📸 Screenshot guardado: error-verificacion-final-roles.png');
        } catch (screenshotError) {
            console.error('Error al capturar screenshot:', screenshotError.message);
        }

        throw error;
    } finally {
        await browser.close();
    }
}

// Ejecutar la verificación
testRolesFormularioPersonal()
    .then(() => {
        console.log('\n🎊 ¡VERIFICACIÓN FINAL COMPLETADA!');
        console.log('🎯 Los 3 roles ya aparecen en el formulario de editar personal');
        process.exit(0);
    })
    .catch((error) => {
        console.error('\n💥 VERIFICACIÓN FALLIDA:', error.message);
        process.exit(1);
    });
