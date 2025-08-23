import { chromium } from 'playwright';

console.log('🧹 VERIFICANDO DROPDOWN SIN NSS');

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

    // Ir a vehículo
    console.log('🚗 Navegando a vehículo...');
    await page.goto('http://127.0.0.1:8000/vehiculos/2');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(3000);

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

        // Abrir el dropdown
        const select = modal.locator('#operador_id');
        await select.click();
        await page.waitForTimeout(1000);

        // Obtener todas las opciones del dropdown
        const options = await select.locator('option').allTextContents();
        console.log(`\n📊 OPCIONES DEL DROPDOWN (${options.length} total):`);

        options.forEach((option, index) => {
            console.log(`   ${index + 1}. "${option.trim()}"`);
        });

        // Verificar que no hay NSS en las opciones
        const opcionesConNSS = options.filter(option =>
            option.includes('Sin NSS') || option.includes('NSS')
        );

        console.log(`\n🎯 VERIFICACIÓN:`);
        if (opcionesConNSS.length === 0) {
            console.log(`✅ ÉXITO: No se encontraron referencias a NSS`);
            console.log(`✅ El dropdown solo muestra nombre y categoría`);
        } else {
            console.log(`❌ PROBLEMA: Aún se encontraron ${opcionesConNSS.length} referencias a NSS`);
            opcionesConNSS.forEach(opcion => console.log(`   - "${opcion}"`));
        }

        // Screenshot del modal actualizado
        await page.screenshot({ path: 'dropdown-sin-nss.png', fullPage: true });
        console.log('📸 Screenshot guardado: dropdown-sin-nss.png');

        await page.waitForTimeout(2000);

    } else {
        console.log('❌ Modal no se abrió');
    }

} catch (error) {
    console.error('💥 Error:', error.message);
    await page.screenshot({ path: 'error-test-sin-nss.png' });
} finally {
    await browser.close();
}

console.log('🏁 Verificación completada');
