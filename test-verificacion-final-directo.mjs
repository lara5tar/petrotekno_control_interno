import { chromium } from 'playwright';

async function testRolesFormularioPersonalDirecto() {
    console.log('🎯 VERIFICACIÓN FINAL DIRECTA: Probando roles en editar personal');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 500
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

        console.log('✏️ Navegando directamente a editar personal (ID: 1)...');
        await page.goto('http://127.0.0.1:8080/personal/1/edit');
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
        const checkbox = page.locator('input[type="checkbox"]').filter({ hasText: /crear|usuario/i }).or(
            page.locator('input[type="checkbox"][x-model="crearUsuario"]')
        ).or(page.locator('label:has-text("Crear Usuario") input[type="checkbox"]'));

        await page.waitForTimeout(2000);

        // Intentar encontrar cualquier checkbox relacionado con usuario
        const allCheckboxes = await page.locator('input[type="checkbox"]').all();
        console.log(`📋 Encontrados ${allCheckboxes.length} checkboxes`);

        let checkboxEncontrado = false;
        for (let i = 0; i < allCheckboxes.length; i++) {
            const cb = allCheckboxes[i];
            const id = await cb.getAttribute('id');
            const name = await cb.getAttribute('name');
            const xModel = await cb.getAttribute('x-model');
            console.log(`Checkbox ${i}: id="${id}", name="${name}", x-model="${xModel}"`);

            if (xModel === 'crearUsuario' || id === 'crear_usuario') {
                console.log(`✅ Encontrado checkbox de crear usuario: ${i}`);
                await cb.check();
                checkboxEncontrado = true;
                break;
            }
        }

        if (!checkboxEncontrado) {
            console.log('⚠️ No se encontró checkbox específico, intentando con el primero...');
            if (allCheckboxes.length > 0) {
                await allCheckboxes[0].check();
            }
        }

        // Esperar a que aparezcan los campos de usuario
        await page.waitForTimeout(2000);

        // Buscar el select de roles
        console.log('🎯 Buscando select de roles...');
        const roleSelect = page.locator('select[name="rol_usuario"]');

        // Verificar si existe el select
        const selectExists = await roleSelect.count() > 0;
        console.log(`🔍 Select de roles existe: ${selectExists}`);

        if (!selectExists) {
            // Buscar todos los selects
            const allSelects = await page.locator('select').all();
            console.log(`📋 Encontrados ${allSelects.length} selects en la página`);

            for (let i = 0; i < allSelects.length; i++) {
                const select = allSelects[i];
                const name = await select.getAttribute('name');
                const id = await select.getAttribute('id');
                console.log(`Select ${i}: name="${name}", id="${id}"`);

                if (name === 'rol_usuario' || id === 'rol_usuario') {
                    console.log(`✅ Encontrado select de roles: ${i}`);
                    const options = await select.locator('option').allTextContents();
                    console.log('📝 Opciones:', options);
                }
            }

            throw new Error('No se encontró el select de roles');
        }

        // Verificar que el select es visible
        await roleSelect.waitFor({ timeout: 5000 });
        const isVisible = await roleSelect.isVisible();
        console.log(`👁️ Select de roles visible: ${isVisible}`);

        if (!isVisible) {
            throw new Error('El select de roles no es visible');
        }

        // Obtener todas las opciones del select
        console.log('📋 Obteniendo opciones del select...');
        const options = await roleSelect.locator('option').allTextContents();
        console.log('📝 Opciones encontradas:', options);

        // Verificar que existen al menos 4 opciones (incluyendo "Seleccione un rol")
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

        // Capturar screenshot del éxito
        await page.screenshot({
            path: 'exito-verificacion-roles.png',
            fullPage: true
        });
        console.log('📸 Screenshot de éxito guardado: exito-verificacion-roles.png');

    } catch (error) {
        console.error('❌ ERROR durante la verificación:', error.message);

        // Capturar screenshot del error
        try {
            await page.screenshot({
                path: 'error-verificacion-final-roles.png',
                fullPage: true
            });
            console.log('📸 Screenshot guardado: error-verificacion-final-roles.png');

            // Información adicional de debug
            console.log('\n🔍 INFORMACIÓN DE DEBUG:');
            const currentUrl = await page.url();
            console.log(`URL actual: ${currentUrl}`);

            const title = await page.title();
            console.log(`Título: ${title}`);

        } catch (screenshotError) {
            console.error('Error al capturar screenshot:', screenshotError.message);
        }

        throw error;
    } finally {
        await browser.close();
    }
}

// Ejecutar la verificación
testRolesFormularioPersonalDirecto()
    .then(() => {
        console.log('\n🎊 ¡VERIFICACIÓN FINAL COMPLETADA!');
        console.log('🎯 Los 3 roles ya aparecen en el formulario de editar personal');
        process.exit(0);
    })
    .catch((error) => {
        console.error('\n💥 VERIFICACIÓN FALLIDA:', error.message);
        process.exit(1);
    });
