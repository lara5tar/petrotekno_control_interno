import { chromium } from 'playwright';

async function activarYVerificarRoles() {
    console.log('🎯 ACTIVANDO CHECKBOX Y VERIFICANDO ROLES...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 300
    });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Login
        console.log('🔐 Haciendo login...');
        await page.goto('http://127.0.0.1:8003/login', { waitUntil: 'networkidle' });
        await page.fill('#email', 'admin@petrotekno.com');
        await page.fill('#password', 'password');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);

        // 2. Ir a editar personal
        console.log('📝 Navegando a editar personal...');
        await page.goto('http://127.0.0.1:8003/personal/3/edit', { waitUntil: 'networkidle' });

        // 3. Buscar y marcar el checkbox
        console.log('🔍 Buscando checkbox "crear usuario"...');
        const checkboxSelector = '#crear_usuario';

        try {
            await page.waitForSelector(checkboxSelector, { timeout: 5000 });
            console.log('✅ Checkbox encontrado');

            const isChecked = await page.isChecked(checkboxSelector);
            console.log(`📋 Checkbox marcado inicialmente: ${isChecked}`);

            if (!isChecked) {
                console.log('🔧 Marcando checkbox "crear usuario"...');
                await page.check(checkboxSelector);
                await page.waitForTimeout(1000);

                const isNowChecked = await page.isChecked(checkboxSelector);
                console.log(`📋 Checkbox marcado después: ${isNowChecked}`);
            }

        } catch (e) {
            console.log('❌ No se pudo encontrar checkbox con #crear_usuario, buscando alternativas...');

            // Buscar por texto
            const checkboxAlternativo = await page.locator('input[type="checkbox"]').filter({ hasText: /crear/i }).first();
            if (await checkboxAlternativo.count() > 0) {
                console.log('✅ Checkbox alternativo encontrado');
                await checkboxAlternativo.check();
                await page.waitForTimeout(1000);
            }
        }

        // 4. Verificar que el select de roles ahora sea visible
        console.log('🔍 Verificando select de roles después de marcar checkbox...');
        const selectRol = await page.locator('#rol_usuario');

        // Esperar a que el select se haga visible
        let intentos = 0;
        let selectVisible = false;

        while (intentos < 10 && !selectVisible) {
            selectVisible = await selectRol.isVisible();
            if (!selectVisible) {
                await page.waitForTimeout(500);
                intentos++;
                console.log(`   Intento ${intentos}/10 - Select visible: ${selectVisible}`);
            }
        }

        if (selectVisible) {
            console.log('✅ ¡SELECT DE ROLES AHORA ES VISIBLE!');

            // 5. Obtener y verificar opciones
            const opciones = await selectRol.locator('option').allTextContents();
            console.log('📋 Opciones del select de roles:', opciones);

            // 6. Verificar cada rol esperado
            const rolesEsperados = ['Admin', 'Supervisor', 'Operador'];
            let rolesEncontrados = 0;

            console.log('🔐 Verificando roles específicos:');
            rolesEsperados.forEach(rol => {
                const encontrado = opciones.some(opt => opt.trim().includes(rol));
                console.log(`   ${rol}: ${encontrado ? '✅ ENCONTRADO' : '❌ NO ENCONTRADO'}`);
                if (encontrado) rolesEncontrados++;
            });

            console.log(`\n📊 RESULTADO FINAL: ${rolesEncontrados}/3 roles encontrados`);

            if (rolesEncontrados === 3) {
                console.log('🎉 ¡ÉXITO COMPLETO! LOS 3 ROLES ESTÁN DISPONIBLES');

                // 7. Probar la funcionalidad de selección
                console.log('🧪 Probando selección de cada rol...');

                for (const rol of rolesEsperados) {
                    try {
                        console.log(`🔄 Seleccionando ${rol}...`);

                        // Buscar la opción que contiene el rol
                        const opcionRol = await selectRol.locator(`option`).filter({ hasText: rol });

                        if (await opcionRol.count() > 0) {
                            const valor = await opcionRol.getAttribute('value');
                            await selectRol.selectOption(valor);
                            await page.waitForTimeout(500);

                            const valorSeleccionado = await selectRol.inputValue();
                            console.log(`   ✅ ${rol} seleccionado correctamente (valor: ${valorSeleccionado})`);
                        } else {
                            console.log(`   ❌ No se pudo encontrar la opción para ${rol}`);
                        }

                    } catch (e) {
                        console.log(`   ❌ Error al seleccionar ${rol}: ${e.message}`);
                    }
                }

                console.log('\n🏆 ¡MISIÓN CUMPLIDA! Los 3 roles funcionan perfectamente');

            } else {
                console.log('❌ PROBLEMA PERSISTENTE: No se encontraron todos los roles');

                // Diagnóstico detallado
                console.log('🔧 Diagnóstico del HTML del select:');
                const selectHTML = await selectRol.innerHTML();
                console.log(selectHTML);

                // Verificar si la variable $roles llegó al template
                const pageContent = await page.content();
                const tieneRolesVariable = pageContent.includes('$roles');
                console.log(`📋 Template contiene referencia a $roles: ${tieneRolesVariable}`);
            }

        } else {
            console.log('❌ El select de roles sigue sin ser visible después de marcar el checkbox');

            // Diagnóstico adicional
            console.log('🔍 Analizando el estado del formulario...');

            const todosSelects = await page.locator('select').all();
            for (let i = 0; i < todosSelects.length; i++) {
                const select = todosSelects[i];
                const id = await select.getAttribute('id');
                const name = await select.getAttribute('name');
                const visible = await select.isVisible();
                const style = await select.getAttribute('style');
                const classes = await select.getAttribute('class');

                console.log(`   Select ${i + 1}:`);
                console.log(`     ID: ${id}`);
                console.log(`     Name: ${name}`);
                console.log(`     Visible: ${visible}`);
                console.log(`     Style: ${style}`);
                console.log(`     Classes: ${classes}`);

                if (id === 'rol_usuario') {
                    console.log('     ⭐ ESTE ES EL SELECT DE ROLES');
                    const opciones = await select.locator('option').allTextContents();
                    console.log(`     Opciones: ${JSON.stringify(opciones)}`);
                }
            }
        }

        // 8. Screenshot final
        await page.screenshot({ path: `verificacion-final-roles-${Date.now()}.png`, fullPage: true });
        console.log('📸 Screenshot final guardado');

    } catch (error) {
        console.error('❌ Error:', error);
        await page.screenshot({ path: `error-verificacion-${Date.now()}.png`, fullPage: true });
    } finally {
        await browser.close();
    }
}

// Ejecutar
activarYVerificarRoles();
