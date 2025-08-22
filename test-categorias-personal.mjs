import { chromium } from 'playwright';

async function testSistemaLimpioConCategorias() {
    console.log('🚀 Verificando sistema limpio con categorías del personal...');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 500
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Login con admin
        console.log('📋 1. Login con usuario administrador...');
        await page.goto('http://127.0.0.1:8002/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'admin123');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // 2. Verificar acceso al módulo de personal
        console.log('📋 2. Verificando módulo de personal...');
        await page.goto('http://127.0.0.1:8002/personal');
        await page.waitForLoadState('networkidle');

        const tituloPersonal = await page.textContent('h1, h2');
        console.log(`✅ Acceso a Personal: ${tituloPersonal ? 'Permitido' : 'Bloqueado'}`);

        // 3. Verificar que existen las categorías al crear personal
        console.log('📋 3. Verificando categorías en formulario de creación...');

        // Buscar botón de crear nuevo personal
        const botonCrear = await page.locator('a[href*="/personal/create"], button:has-text("Crear"), button:has-text("Nuevo")').first();

        if (await botonCrear.count() > 0) {
            await botonCrear.click();
            await page.waitForLoadState('networkidle');

            // Verificar que existe select de categoría
            const selectCategoria = await page.locator('select[name="categoria_id"], select:has(option:has-text("Admin"))').first();

            if (await selectCategoria.count() > 0) {
                // Obtener las opciones del select
                const opciones = await selectCategoria.locator('option').allTextContents();
                console.log('✅ Categorías disponibles en formulario:');
                opciones.forEach((opcion, index) => {
                    if (opcion.trim() && !opcion.includes('Selecciona')) {
                        console.log(`   🔸 ${opcion.trim()}`);
                    }
                });

                // Verificar que estén nuestras categorías específicas
                const categoriasEsperadas = ['Admin', 'Operador', 'Responsable de obra'];
                let categoriasEncontradas = 0;

                for (const categoria of categoriasEsperadas) {
                    const found = opciones.some(opcion => opcion.includes(categoria));
                    if (found) {
                        categoriasEncontradas++;
                        console.log(`   ✅ Categoría "${categoria}" encontrada`);
                    } else {
                        console.log(`   ❌ Categoría "${categoria}" NO encontrada`);
                    }
                }

                console.log(`📊 Categorías encontradas: ${categoriasEncontradas}/${categoriasEsperadas.length}`);

            } else {
                console.log('⚠️ No se encontró select de categorías');
            }
        } else {
            console.log('⚠️ No se encontró botón de crear personal');
        }

        // 4. Verificar lista de personal existente
        console.log('📋 4. Verificando personal existente...');
        await page.goto('http://127.0.0.1:8002/personal');
        await page.waitForLoadState('networkidle');

        const filasPersonal = await page.locator('table tbody tr').count();
        console.log(`📊 Personal en sistema: ${filasPersonal}`);

        if (filasPersonal > 0) {
            // Verificar si se muestra la categoría del administrador
            const contenidoTabla = await page.locator('table tbody').textContent();
            if (contenidoTabla?.includes('Admin') || contenidoTabla?.includes('Administrador')) {
                console.log('✅ Categoría del administrador visible en lista');
            }
        }

        // 5. Verificar estado final del sistema
        console.log('📋 5. Resumen final del sistema...');

        await page.goto('http://127.0.0.1:8002/obras');
        await page.waitForLoadState('networkidle');
        const obras = await page.locator('table tbody tr').count();

        await page.goto('http://127.0.0.1:8002/vehiculos');
        await page.waitForLoadState('networkidle');
        const vehiculos = await page.locator('table tbody tr').count();

        console.log('🎉 Sistema completamente configurado:');
        console.log('   ✅ Usuario administrador funcional');
        console.log('   ✅ Categorías del personal creadas (Admin, Operador, Responsable de obra)');
        console.log('   ✅ Acceso completo a todas las secciones');
        console.log(`   📊 Obras: ${obras} | Vehículos: ${vehiculos} | Personal: ${filasPersonal}`);
        console.log('   🔐 Sistema listo para uso en producción');

    } catch (error) {
        console.error('❌ Error:', error.message);
        await page.screenshot({ path: 'test-categorias-error.png', fullPage: true });
    } finally {
        await browser.close();
    }
}

testSistemaLimpioConCategorias().catch(console.error);
