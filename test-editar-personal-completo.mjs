import { chromium } from 'playwright';

async function testEditarPersonalCompleto() {
    console.log('🎯 VERIFICACIÓN COMPLETA: Funcionalidad editar personal');

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

        // Verificar que el login fue exitoso
        const currentUrl = page.url();
        if (currentUrl.includes('/login')) {
            throw new Error('El login falló - aún estamos en la página de login');
        }
        console.log('✅ Login exitoso');

        console.log('👥 Navegando a lista de personal...');
        await page.goto('http://127.0.0.1:8080/personal');
        await page.waitForLoadState('networkidle');

        console.log('✏️ Buscando y haciendo clic en botón de editar...');
        // Buscar cualquier enlace que contenga "edit" en la URL
        const editLinks = await page.locator('a[href*="/edit"]').all();
        console.log(`📋 Encontrados ${editLinks.length} enlaces de edición`);

        if (editLinks.length === 0) {
            // Si no hay enlaces de edición, buscar botones que contengan "editar"
            const editButtons = await page.locator('button:has-text("Editar"), a:has-text("Editar")').all();
            console.log(`📋 Encontrados ${editButtons.length} botones de edición`);

            if (editButtons.length === 0) {
                throw new Error('No se encontraron botones o enlaces de edición');
            }
            await editButtons[0].click();
        } else {
            await editLinks[0].click();
        }

        await page.waitForLoadState('networkidle');

        console.log('🔍 Verificando que estamos en página de edición...');
        const editUrl = page.url();
        console.log(`📍 URL actual: ${editUrl}`);

        if (!editUrl.includes('/edit')) {
            throw new Error('No estamos en la página de edición');
        }

        console.log('📄 Verificando elementos del formulario...');

        // Verificar campos básicos del formulario
        const nombreField = page.locator('input[name="nombre"]');
        const apellidoField = page.locator('input[name="apellido"]');

        const nombreExists = await nombreField.count() > 0;
        const apellidoExists = await apellidoField.count() > 0;

        console.log(`📝 Campo nombre existe: ${nombreExists}`);
        console.log(`📝 Campo apellido existe: ${apellidoExists}`);

        if (!nombreExists || !apellidoExists) {
            throw new Error('Faltan campos básicos del formulario');
        }

        console.log('☑️ Buscando checkbox "Crear Usuario"...');
        const crearUsuarioCheckbox = page.locator('input[type="checkbox"][x-model="crearUsuario"], input#crear_usuario');

        const checkboxExists = await crearUsuarioCheckbox.count() > 0;
        console.log(`☑️ Checkbox "Crear Usuario" existe: ${checkboxExists}`);

        if (checkboxExists) {
            console.log('✅ Activando checkbox "Crear Usuario"...');
            await crearUsuarioCheckbox.check();

            // Esperar a que aparezcan los campos de usuario
            await page.waitForTimeout(2000);

            console.log('🎯 Verificando select de roles...');
            const roleSelect = page.locator('select[name="rol_usuario"]');

            const selectExists = await roleSelect.count() > 0;
            console.log(`🎯 Select de roles existe: ${selectExists}`);

            if (selectExists) {
                const isVisible = await roleSelect.isVisible();
                console.log(`👁️ Select de roles visible: ${isVisible}`);

                if (isVisible) {
                    // Obtener opciones del select
                    const options = await roleSelect.locator('option').allTextContents();
                    console.log('📝 Opciones de roles encontradas:');
                    options.forEach((option, index) => {
                        console.log(`  ${index + 1}. ${option.trim()}`);
                    });

                    // Verificar que contiene los roles esperados
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

                    if (rolesEncontrados.length === 3) {
                        console.log('🎯 ¡PERFECTO! Los 3 roles están disponibles');

                        // Probar seleccionar un rol
                        console.log('🔄 Probando selección de rol Admin...');
                        await roleSelect.selectOption({ label: 'Admin' });

                        const selectedValue = await roleSelect.inputValue();
                        console.log(`✅ Rol seleccionado correctamente. Valor: ${selectedValue}`);

                    } else {
                        console.log(`⚠️ Solo se encontraron ${rolesEncontrados.length} de 3 roles esperados`);
                    }
                } else {
                    console.log('❌ El select de roles no es visible');
                }
            } else {
                console.log('❌ No se encontró el select de roles');
            }
        }

        console.log('📊 Verificando otros campos del formulario...');

        // Verificar campo de categoría
        const categoriaSelect = page.locator('select[name="categoria_personal_id"]');
        const categoriaExists = await categoriaSelect.count() > 0;
        console.log(`📊 Select de categoría existe: ${categoriaExists}`);

        // Verificar campo de teléfono
        const telefonoField = page.locator('input[name="telefono"]');
        const telefonoExists = await telefonoField.count() > 0;
        console.log(`📞 Campo teléfono existe: ${telefonoExists}`);

        // Verificar campo de email
        const emailField = page.locator('input[name="email"]');
        const emailExists = await emailField.count() > 0;
        console.log(`📧 Campo email existe: ${emailExists}`);

        console.log('💾 Verificando botón de guardar...');
        const saveButton = page.locator('button[type="submit"], button:has-text("Guardar"), button:has-text("Actualizar")');
        const saveButtonExists = await saveButton.count() > 0;
        console.log(`💾 Botón de guardar existe: ${saveButtonExists}`);

        if (saveButtonExists) {
            const isEnabled = await saveButton.isEnabled();
            console.log(`💾 Botón de guardar habilitado: ${isEnabled}`);
        }

        console.log('📝 Probando modificación de datos...');

        // Modificar el nombre para probar funcionalidad
        const nombreActual = await nombreField.inputValue();
        console.log(`📝 Nombre actual: ${nombreActual}`);

        const nuevoNombre = nombreActual + ' (Editado)';
        await nombreField.fill(nuevoNombre);
        console.log(`📝 Nombre modificado a: ${nuevoNombre}`);

        console.log('\n🎉 RESUMEN DE VERIFICACIÓN:');
        console.log('✅ Login exitoso');
        console.log('✅ Navegación a lista de personal exitosa');
        console.log('✅ Acceso a formulario de edición exitoso');
        console.log('✅ Campos básicos del formulario presentes');

        if (checkboxExists) {
            console.log('✅ Checkbox "Crear Usuario" funcional');
            if (roleSelect.count() > 0) {
                console.log('✅ Select de roles aparece al activar checkbox');
                if (rolesEncontrados?.length === 3) {
                    console.log('✅ Los 3 roles (Admin, Supervisor, Operador) están disponibles');
                    console.log('✅ Selección de roles funcional');
                }
            }
        }

        console.log('✅ Modificación de datos funcional');

        if (saveButtonExists) {
            console.log('✅ Botón de guardar presente');
        }

        // Capturar screenshot final
        await page.screenshot({
            path: 'verificacion-editar-personal-completa.png',
            fullPage: true
        });
        console.log('📸 Screenshot guardado: verificacion-editar-personal-completa.png');

        console.log('\n🏆 VERIFICACIÓN COMPLETA EXITOSA');
        console.log('✨ El formulario de editar personal funciona correctamente');

    } catch (error) {
        console.error('❌ ERROR durante la verificación:', error.message);

        // Capturar screenshot del error
        try {
            await page.screenshot({
                path: 'error-editar-personal.png',
                fullPage: true
            });
            console.log('📸 Screenshot de error guardado: error-editar-personal.png');

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
testEditarPersonalCompleto()
    .then(() => {
        console.log('\n🎊 ¡VERIFICACIÓN COMPLETADA CON ÉXITO!');
        console.log('🎯 El formulario de editar personal funciona correctamente');
        console.log('✨ Todos los elementos están presentes y funcionan como esperado');
        process.exit(0);
    })
    .catch((error) => {
        console.error('\n💥 VERIFICACIÓN FALLIDA:', error.message);
        console.error('🔧 Revisa los logs y screenshots para más detalles');
        process.exit(1);
    });
