import { chromium } from 'playwright';

async function solucionarRolesPersonal() {
    console.log('🔧 SOLUCIONANDO: Roles en editar personal...');

    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Hacer login primero
        console.log('🔐 Haciendo login...');
        await page.goto('http://127.0.0.1:8003/login', { waitUntil: 'networkidle' });

        await page.fill('#email', 'admin@petrotekno.com');
        await page.fill('#password', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que redirija (más flexible)
        await page.waitForTimeout(3000);
        const currentUrl = page.url();
        console.log(`📍 URL después de login: ${currentUrl}`);

        if (currentUrl.includes('/login')) {
            console.log('❌ Login falló, intentando de nuevo...');
            // Intentar con credenciales alternativas
            await page.fill('#email', 'admin@example.com');
            await page.fill('#password', 'admin123');
            await page.click('button[type="submit"]');
            await page.waitForTimeout(3000);
        }

        console.log('✅ Procediendo (con o sin login)');

        // 2. Navegar a editar personal
        console.log('📝 Navegando a editar personal ID 3...');
        await page.goto('http://127.0.0.1:8003/personal/3/edit', { waitUntil: 'networkidle' });

        // 3. Esperar a que la página cargue
        await page.waitForTimeout(3000);

        // 4. Buscar el checkbox de "Crear usuario"
        console.log('🔍 Buscando checkbox "Crear usuario"...');
        const checkboxCrearUsuario = await page.locator('input[type="checkbox"][x-model="crearUsuario"]');

        if (await checkboxCrearUsuario.isVisible()) {
            console.log('✅ Checkbox "Crear usuario" encontrado');

            // 5. Verificar si está marcado
            const estaMarcado = await checkboxCrearUsuario.isChecked();
            console.log(`📋 Checkbox marcado: ${estaMarcado}`);

            if (!estaMarcado) {
                console.log('🔧 Marcando checkbox "Crear usuario"...');
                await checkboxCrearUsuario.check();
                await page.waitForTimeout(1000);
            }

            // 6. Ahora buscar el select de roles
            console.log('🔍 Buscando select de roles...');
            const selectRol = await page.locator('#rol_usuario');

            if (await selectRol.isVisible()) {
                console.log('✅ Select de rol encontrado después de marcar checkbox');

                // 7. Obtener opciones del select
                const opciones = await selectRol.locator('option').allTextContents();
                console.log('📋 Opciones encontradas:', opciones);

                // 8. Verificar roles específicos
                const rolesEsperados = ['Admin', 'Supervisor', 'Operador'];
                let rolesEncontrados = 0;

                rolesEsperados.forEach(rol => {
                    const encontrado = opciones.some(opt => opt.includes(rol));
                    console.log(`   ${rol}: ${encontrado ? '✅' : '❌'}`);
                    if (encontrado) rolesEncontrados++;
                });

                if (rolesEncontrados === 3) {
                    console.log('🎉 ¡ÉXITO! Los 3 roles están disponibles');

                    // 9. Probar seleccionar cada rol
                    for (let i = 0; i < rolesEsperados.length; i++) {
                        const rol = rolesEsperados[i];
                        console.log(`🔄 Probando seleccionar rol: ${rol}`);

                        // Buscar la opción que contiene el rol
                        const opcionRol = await selectRol.locator(`option:has-text("${rol}")`);
                        if (await opcionRol.count() > 0) {
                            await selectRol.selectOption({ label: rol });
                            await page.waitForTimeout(500);

                            const valorSeleccionado = await selectRol.inputValue();
                            console.log(`   ✅ Rol ${rol} seleccionado (valor: ${valorSeleccionado})`);
                        }
                    }

                } else {
                    console.log(`❌ Solo se encontraron ${rolesEncontrados} de 3 roles esperados`);

                    // Diagnóstico adicional
                    const selectHTML = await selectRol.innerHTML();
                    console.log('🔍 HTML completo del select:');
                    console.log(selectHTML);
                }

            } else {
                console.log('❌ Select de rol aún no visible después de marcar checkbox');

                // Buscar todos los selects visibles
                const todosLosSelects = await page.locator('select').count();
                console.log(`🔍 Total selects en página: ${todosLosSelects}`);

                if (todosLosSelects > 0) {
                    const selectsIds = await page.locator('select').evaluateAll(selects =>
                        selects.map(s => ({ id: s.id, name: s.name, visible: s.offsetParent !== null }))
                    );
                    console.log('📋 Selects encontrados:', selectsIds);
                }
            }

        } else {
            console.log('❌ Checkbox "Crear usuario" no encontrado');

            // Buscar directamente el select de roles
            const selectRol = await page.locator('#rol_usuario');
            if (await selectRol.isVisible()) {
                console.log('✅ Select de rol visible sin checkbox');
                const opciones = await selectRol.locator('option').allTextContents();
                console.log('📋 Opciones:', opciones);
            } else {
                console.log('❌ Tampoco se encuentra el select de rol directamente');
            }
        }

        // 10. Screenshot final
        await page.screenshot({ path: `solucion-roles-personal-${Date.now()}.png`, fullPage: true });
        console.log('📸 Screenshot final tomado');

    } catch (error) {
        console.error('❌ Error:', error);
        await page.screenshot({ path: `error-solucion-roles-${Date.now()}.png`, fullPage: true });
    } finally {
        await browser.close();
    }
}

// Ejecutar
solucionarRolesPersonal();
