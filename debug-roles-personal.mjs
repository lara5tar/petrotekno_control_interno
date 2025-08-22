import { chromium } from 'playwright';

async function diagnosticarRolesPersonal() {
    console.log('🔍 DIAGNÓSTICO: Roles en editar personal...');

    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Ir a la página de editar personal
        console.log('📝 Navegando a editar personal ID 3...');
        await page.goto('http://127.0.0.1:8003/personal/3/edit', { waitUntil: 'networkidle' });

        // 2. Esperar a que la página cargue completamente
        await page.waitForTimeout(2000);

        // 3. Buscar el select de roles
        console.log('🔍 Buscando select de roles...');
        const selectRol = await page.locator('#rol_usuario');

        if (await selectRol.isVisible()) {
            console.log('✅ Select de rol encontrado');

            // 4. Obtener todas las opciones del select
            const opciones = await selectRol.locator('option').allTextContents();
            console.log('📋 Opciones encontradas:', opciones);

            // 5. Verificar si hay roles específicos
            const tieneAdmin = opciones.some(opt => opt.includes('Admin'));
            const tieneSupervisor = opciones.some(opt => opt.includes('Supervisor'));
            const tieneOperador = opciones.some(opt => opt.includes('Operador'));

            console.log('🔐 Roles encontrados:');
            console.log(`   Admin: ${tieneAdmin ? '✅' : '❌'}`);
            console.log(`   Supervisor: ${tieneSupervisor ? '✅' : '❌'}`);
            console.log(`   Operador: ${tieneOperador ? '✅' : '❌'}`);

            if (opciones.length <= 1) {
                console.log('❌ PROBLEMA: Solo se encuentra la opción por defecto');

                // 6. Verificar errores en consola
                const logs = [];
                page.on('console', msg => logs.push(`${msg.type()}: ${msg.text()}`));

                // 7. Revisar si hay errores de JavaScript
                const errors = [];
                page.on('pageerror', error => errors.push(error.message));

                await page.waitForTimeout(1000);

                if (logs.length > 0) {
                    console.log('📊 Logs de consola:', logs);
                }

                if (errors.length > 0) {
                    console.log('❌ Errores JavaScript:', errors);
                }

                // 8. Inspeccionar el HTML del select
                const selectHTML = await selectRol.innerHTML();
                console.log('🔍 HTML del select:');
                console.log(selectHTML);

                // 9. Verificar si la variable $roles está disponible en Blade
                const pageContent = await page.content();
                const tieneVariableRoles = pageContent.includes('@foreach($roles as $rol)');
                console.log(`📋 ¿Template tiene @foreach($roles...? ${tieneVariableRoles ? '✅' : '❌'}`);

            } else {
                console.log('✅ ÉXITO: Se encontraron múltiples opciones de roles');
            }

        } else {
            console.log('❌ PROBLEMA: Select de rol no encontrado');

            // Buscar elementos similares
            const selectsSimilares = await page.locator('select').count();
            console.log(`🔍 Selects encontrados en la página: ${selectsSimilares}`);

            const selectsIds = await page.locator('select').evaluateAll(selects =>
                selects.map(s => s.id || s.name || 'sin-id')
            );
            console.log('🆔 IDs de selects:', selectsIds);
        }

        // 10. Tomar screenshot para debugging
        await page.screenshot({ path: `debug-roles-personal-${Date.now()}.png`, fullPage: true });
        console.log('📸 Screenshot tomado para debugging');

    } catch (error) {
        console.error('❌ Error durante el diagnóstico:', error);
        await page.screenshot({ path: `error-roles-personal-${Date.now()}.png`, fullPage: true });
    } finally {
        await browser.close();
    }
}

// Ejecutar diagnóstico
diagnosticarRolesPersonal();
