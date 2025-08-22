import { chromium } from 'playwright';

async function solucionarRolesConLogin() {
    console.log('🔧 SOLUCIONANDO ROLES CON LOGIN COMPLETO...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 500 // Ralentizar para ver lo que pasa
    });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Ir al login
        console.log('🔐 Navegando al login...');
        await page.goto('http://127.0.0.1:8003/login', { waitUntil: 'networkidle' });

        // 2. Hacer login paso a paso
        console.log('📝 Llenando credenciales...');
        await page.waitForSelector('#email', { timeout: 5000 });
        await page.fill('#email', 'admin@petrotekno.com');

        await page.waitForSelector('#password', { timeout: 5000 });
        await page.fill('#password', 'password');

        console.log('🚀 Enviando formulario de login...');
        await page.waitForSelector('button[type="submit"]', { timeout: 5000 });

        // Capturar la respuesta del login
        const submitPromise = page.waitForResponse(response =>
            response.url().includes('/login') && response.request().method() === 'POST'
        );

        await page.click('button[type="submit"]');

        try {
            const response = await submitPromise;
            console.log(`📊 Respuesta del login: ${response.status()}`);

            if (response.status() === 302) {
                console.log('✅ Login exitoso (redirección)');
            } else {
                console.log('❌ Login falló');
                const content = await page.content();
                if (content.includes('error') || content.includes('invalid')) {
                    console.log('🔍 Hay errores de validación en la página');
                }
            }
        } catch (e) {
            console.log('⚠️ No se pudo capturar la respuesta del login');
        }

        // 3. Esperar a la redirección
        await page.waitForTimeout(3000);
        const currentUrl = page.url();
        console.log(`📍 URL actual después del login: ${currentUrl}`);

        // 4. Navegar a editar personal
        console.log('📝 Navegando a editar personal...');
        await page.goto('http://127.0.0.1:8003/personal/3/edit', { waitUntil: 'networkidle' });

        const editUrl = page.url();
        console.log(`📍 URL de editar personal: ${editUrl}`);

        if (editUrl.includes('/login')) {
            console.log('❌ Aún redirige al login - problema de autenticación');
            return;
        }

        // 5. Buscar el formulario
        console.log('🔍 Buscando formulario...');
        await page.waitForTimeout(2000);

        // 6. Verificar si hay algún error en la página
        const pageText = await page.textContent('body');
        if (pageText.includes('403') || pageText.includes('Forbidden') || pageText.includes('permisos')) {
            console.log('❌ Error de permisos en la página');
            console.log('📋 Contenido parcial:', pageText.substring(0, 200));
        }

        // 7. Buscar el checkbox de crear usuario
        const crearUsuarioCheckbox = await page.locator('input[type="checkbox"]').filter({ hasText: /crear.*usuario/i });
        const checkboxCount = await crearUsuarioCheckbox.count();
        console.log(`🔍 Checkboxes "crear usuario" encontrados: ${checkboxCount}`);

        if (checkboxCount > 0) {
            console.log('✅ Checkbox "crear usuario" encontrado');

            // Marcar el checkbox si no está marcado
            const isChecked = await crearUsuarioCheckbox.first().isChecked();
            console.log(`📋 Checkbox está marcado: ${isChecked}`);

            if (!isChecked) {
                console.log('🔧 Marcando checkbox...');
                await crearUsuarioCheckbox.first().check();
                await page.waitForTimeout(1000);
            }
        } else {
            // Buscar por otros selectores
            console.log('🔍 Buscando checkbox por otros métodos...');
            const checkboxes = await page.locator('input[type="checkbox"]').all();
            console.log(`📊 Total checkboxes en página: ${checkboxes.length}`);

            for (let i = 0; i < checkboxes.length; i++) {
                const checkbox = checkboxes[i];
                const label = await checkbox.getAttribute('id');
                const nearby = await page.locator(`label[for="${label}"]`).textContent().catch(() => '');
                console.log(`   Checkbox ${i + 1}: ${label} - ${nearby}`);
            }
        }

        // 8. Buscar el select de roles
        console.log('🔍 Buscando select de roles...');
        const selectRol = await page.locator('#rol_usuario');
        const selectVisible = await selectRol.isVisible();
        console.log(`📋 Select #rol_usuario visible: ${selectVisible}`);

        if (selectVisible) {
            console.log('✅ ¡SELECT DE ROL ENCONTRADO!');

            // Obtener todas las opciones
            const opciones = await selectRol.locator('option').allTextContents();
            console.log('📋 Opciones del select:', opciones);

            // Verificar roles específicos
            const rolesEsperados = ['Admin', 'Supervisor', 'Operador'];
            let rolesEncontrados = 0;

            rolesEsperados.forEach(rol => {
                const encontrado = opciones.some(opt => opt.includes(rol));
                console.log(`🔐 ${rol}: ${encontrado ? '✅ ENCONTRADO' : '❌ NO ENCONTRADO'}`);
                if (encontrado) rolesEncontrados++;
            });

            console.log(`📊 RESULTADO: ${rolesEncontrados}/3 roles encontrados`);

            if (rolesEncontrados === 3) {
                console.log('🎉 ¡ÉXITO TOTAL! Los 3 roles están disponibles');

                // Probar seleccionar cada rol
                for (const rol of rolesEsperados) {
                    try {
                        await selectRol.selectOption({ label: rol });
                        await page.waitForTimeout(500);
                        const valorSeleccionado = await selectRol.inputValue();
                        console.log(`✅ Rol ${rol} seleccionado correctamente (valor: ${valorSeleccionado})`);
                    } catch (e) {
                        console.log(`❌ Error al seleccionar ${rol}:`, e.message);
                    }
                }
            } else {
                console.log('❌ PROBLEMA: No se encontraron todos los roles esperados');
                console.log('🔧 Investigando el problema...');

                // Inspeccionar el HTML del select
                const selectHTML = await selectRol.innerHTML();
                console.log('🔍 HTML completo del select:');
                console.log(selectHTML);
            }

        } else {
            console.log('❌ Select de rol no visible');

            // Buscar todos los selects
            const todosSelects = await page.locator('select').count();
            console.log(`📊 Total selects en página: ${todosSelects}`);

            if (todosSelects > 0) {
                console.log('🔍 Analizando todos los selects...');
                const selects = await page.locator('select').all();

                for (let i = 0; i < selects.length; i++) {
                    const select = selects[i];
                    const id = await select.getAttribute('id');
                    const name = await select.getAttribute('name');
                    const visible = await select.isVisible();
                    console.log(`   Select ${i + 1}: id="${id}" name="${name}" visible=${visible}`);

                    if (visible) {
                        const opciones = await select.locator('option').allTextContents();
                        console.log(`     Opciones:`, opciones);
                    }
                }
            }
        }

        // 9. Screenshot final
        await page.screenshot({ path: `resultado-final-roles-${Date.now()}.png`, fullPage: true });
        console.log('📸 Screenshot final guardado');

    } catch (error) {
        console.error('❌ Error durante la ejecución:', error);
        await page.screenshot({ path: `error-final-roles-${Date.now()}.png`, fullPage: true });
    } finally {
        await browser.close();
    }
}

// Ejecutar
solucionarRolesConLogin();
