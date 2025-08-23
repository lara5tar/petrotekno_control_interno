import { chromium } from 'playwright';

console.log('🎨 VERIFICANDO ESPACIADO MEJORADO EN MODALES');

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
    await page.goto('http://127.0.0.1:8000/vehiculos/1');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(3000);

    // Abrir modal de cambiar obra
    console.log('📝 Abriendo modal "Cambiar Obra"...');
    const cambiarObraBtn = page.locator('button').filter({ hasText: 'Cambiar Obra' }).first();
    await cambiarObraBtn.click();
    await page.waitForTimeout(1000);

    // Verificar que el modal está abierto
    const modal = page.locator('#cambiar-obra-modal');
    const isVisible = await modal.isVisible();

    if (isVisible) {
        console.log('✅ Modal abierto correctamente');

        // Screenshot del modal mejorado
        await page.screenshot({ path: 'modal-espaciado-mejorado.png', fullPage: true });
        console.log('📸 Screenshot guardado: modal-espaciado-mejorado.png');

        // Verificar estilos aplicados
        const modalContent = modal.locator('.modal-content-compact');
        const styles = await modalContent.evaluate(el => {
            const computed = window.getComputedStyle(el);
            return {
                padding: computed.padding,
                margin: computed.margin,
                borderRadius: computed.borderRadius
            };
        });

        console.log('🎨 Estilos aplicados:');
        console.log(`   Padding: ${styles.padding}`);
        console.log(`   Margin: ${styles.margin}`);
        console.log(`   Border Radius: ${styles.borderRadius}`);

        // Esperar un poco para que se vea
        await page.waitForTimeout(3000);

    } else {
        console.log('❌ Modal no se abrió');
    }

} catch (error) {
    console.error('💥 Error:', error.message);
    await page.screenshot({ path: 'error-espaciado.png' });
} finally {
    await browser.close();
}

console.log('🏁 Verificación de espaciado completada');
