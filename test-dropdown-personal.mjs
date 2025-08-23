import { chromium } from 'playwright';

console.log('🧪 VERIFICANDO DROPDOWN DE TODO EL PERSONAL');

const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();

try {
    // Login
    console.log('🔐 Haciendo login...');
    await page.goto('http://127.0.0.1:8000/login');
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');

    await Promise.all([
        page.waitForNavigation(),
        page.click('button[type="submit"]')
    ]);

    console.log('✅ Login exitoso');

    // Ir a vehículo
    console.log('🚗 Navegando a vehículo...');
    await page.goto('http://127.0.0.1:8000/vehiculos/2'); // Usando vehículo 2 como en la imagen
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(3000);

    // Verificar que estamos en la página correcta
    const titulo = await page.textContent('h1, h2, h3');
    console.log(`📋 Página cargada: ${titulo || 'Sin título'}`);

    // Abrir modal de asignar operador
    console.log('👤 Abriendo modal "Asignar Operador"...');
    const asignarBtn = page.locator('button').filter({ hasText: 'Asignar Operador' }).first();
    await asignarBtn.click();
    await page.waitForTimeout(1500);

    // Verificar que el modal está abierto
    const modal = page.locator('#cambiar-operador-modal');
    const isVisible = await modal.isVisible();

    if (isVisible) {
        console.log('✅ Modal abierto correctamente');

        // Verificar el nuevo título del modal
        const modalTitle = await modal.locator('h3').textContent();
        console.log(`📝 Título del modal: ${modalTitle}`);

        // Verificar el nuevo label del dropdown
        const label = await modal.locator('label[for="operador_id"]').textContent();
        console.log(`🏷️ Label del campo: ${label}`);

        // Abrir el dropdown y contar opciones
        const select = modal.locator('#operador_id');
        await select.click();
        await page.waitForTimeout(1000);

        // Obtener todas las opciones del dropdown
        const options = await select.locator('option').allTextContents();
        console.log(`\n📊 OPCIONES DEL DROPDOWN (${options.length} total):`);

        options.forEach((option, index) => {
            if (index === 0) {
                console.log(`   ${index + 1}. ${option} (placeholder)`);
            } else {
                console.log(`   ${index + 1}. ${option}`);
            }
        });

        // Verificar si hay opciones con diferentes categorías
        const opcionesConCategoria = options.filter(option =>
            option.includes('(') && option.includes(')')
        );

        console.log(`\n🎯 ANÁLISIS:`);
        console.log(`   Total de opciones: ${options.length - 1} (sin contar placeholder)`);
        console.log(`   Opciones con categoría visible: ${opcionesConCategoria.length}`);

        if (opcionesConCategoria.length > 0) {
            console.log(`✅ ÉXITO: Se muestran diferentes categorías de personal`);

            // Mostrar ejemplos de categorías encontradas
            const categorias = new Set();
            opcionesConCategoria.forEach(option => {
                const match = option.match(/\(([^)]+)\)/);
                if (match) {
                    categorias.add(match[1]);
                }
            });

            console.log(`📋 Categorías encontradas: ${Array.from(categorias).join(', ')}`);
        } else {
            console.log(`⚠️ No se detectaron categorías en las opciones`);
        }

        // Screenshot del modal con el dropdown abierto
        await page.screenshot({ path: 'dropdown-todo-personal.png', fullPage: true });
        console.log('📸 Screenshot guardado: dropdown-todo-personal.png');

        // Esperar un poco para que se vea
        await page.waitForTimeout(3000);

    } else {
        console.log('❌ Modal no se abrió');
        await page.screenshot({ path: 'error-modal-no-abierto.png' });
    }

} catch (error) {
    console.error('💥 Error:', error.message);
    await page.screenshot({ path: 'error-test-personal.png' });
} finally {
    await browser.close();
}

console.log('🏁 Verificación completada');
