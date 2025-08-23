import { chromium } from 'playwright';

console.log('🔧 VERIFICACIÓN DIRECTA - Vista de vehículo');

const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();

try {
    // Ir directamente a una vista de vehículo si tenemos acceso
    console.log('🔐 Intentando acceso directo...');
    await page.goto('http://127.0.0.1:8000/login');

    // Screenshot de la página de login
    await page.screenshot({ path: 'debug-login-page.png' });
    console.log('📸 Login page screenshot: debug-login-page.png');

    // Verificar elementos de login
    console.log('🔍 Verificando elementos de login...');
    const emailInput = await page.locator('input[name="email"]').count();
    const passwordInput = await page.locator('input[name="password"]').count();
    const submitButton = await page.locator('button[type="submit"]').count();

    console.log(`   Email input: ${emailInput > 0 ? '✅' : '❌'}`);
    console.log(`   Password input: ${passwordInput > 0 ? '✅' : '❌'}`);
    console.log(`   Submit button: ${submitButton > 0 ? '✅' : '❌'}`);

    if (emailInput > 0 && passwordInput > 0 && submitButton > 0) {
        console.log('📝 Realizando login...');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');

        // Click en submit y esperar navegación
        await Promise.all([
            page.waitForNavigation({ timeout: 15000 }),
            page.click('button[type="submit"]')
        ]);

        console.log('✅ Login exitoso, URL actual:', page.url());

        // Ir a vehículos
        console.log('🚗 Navegando a vehículos...');
        await page.goto('http://127.0.0.1:8000/vehiculos');
        await page.waitForLoadState('domcontentloaded');

        // Buscar primer enlace "Ver"
        const verLinks = await page.locator('a').filter({ hasText: 'Ver' }).all();
        if (verLinks.length > 0) {
            console.log('📋 Accediendo a vista de vehículo...');

            // Click y esperar carga
            await Promise.all([
                page.waitForNavigation({ timeout: 10000 }),
                verLinks[0].click()
            ]);

            console.log('✅ En vista de vehículo, URL:', page.url());

            // Esperar un momento para que se cargue completamente
            await page.waitForTimeout(2000);

            // Verificar modales
            console.log('\n🪟 VERIFICANDO ESTADO DE MODALES:');

            const modales = [
                'cambiar-operador-modal',
                'cambiar-obra-modal',
                'registrar-mantenimiento-modal',
                'responsable-obra-modal'
            ];

            let algunModalVisible = false;

            for (const modalId of modales) {
                try {
                    const modal = page.locator(`#${modalId}`);
                    const existe = await modal.count() > 0;

                    if (existe) {
                        const isVisible = await modal.isVisible();
                        const computedStyle = await modal.evaluate(el => {
                            const style = window.getComputedStyle(el);
                            return {
                                display: style.display,
                                visibility: style.visibility,
                                opacity: style.opacity
                            };
                        });

                        if (isVisible) algunModalVisible = true;

                        console.log(`   ${modalId}:`);
                        console.log(`      Visible: ${isVisible ? '🔴 SÍ' : '✅ NO'}`);
                        console.log(`      Display: ${computedStyle.display}`);
                        console.log(`      Visibility: ${computedStyle.visibility}`);
                        console.log(`      Opacity: ${computedStyle.opacity}`);
                    } else {
                        console.log(`   ${modalId}: ❓ No existe en el DOM`);
                    }
                } catch (error) {
                    console.log(`   ${modalId}: ⚠️ Error: ${error.message}`);
                }
            }

            // Screenshot final
            await page.screenshot({ path: 'vista-vehiculo-estado-final.png', fullPage: true });
            console.log('\n📸 Screenshot final: vista-vehiculo-estado-final.png');

            // Resultado
            console.log('\n🏁 RESULTADO FINAL:');
            if (algunModalVisible) {
                console.log('❌ PROBLEMA PERSISTE: Hay modales visibles automáticamente');
            } else {
                console.log('✅ PROBLEMA RESUELTO: No hay modales auto-abiertos');
            }

        } else {
            console.log('❌ No se encontraron enlaces "Ver" en la lista de vehículos');
        }

    } else {
        console.log('❌ Elementos de login no encontrados correctamente');
    }

} catch (error) {
    console.error('💥 Error:', error.message);
    await page.screenshot({ path: 'debug-error-final.png' });
    console.log('📸 Error screenshot: debug-error-final.png');
} finally {
    await browser.close();
}

console.log('🏁 Verificación completada');
