import { chromium } from 'playwright';

console.log('🔧 VERIFICACIÓN FINAL COMPLETA - Sistema de modales');

const browser = await chromium.launch({ headless: false });
const context = await browser.newContext();
const page = await context.newPage();

try {
    console.log('🔐 Iniciando proceso de login...');

    // Paso 1: Obtener la página de login y el token CSRF
    await page.goto('http://127.0.0.1:8000/login');

    // Esperar que la página cargue completamente
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(2000);

    // Obtener el token CSRF
    const csrfToken = await page.locator('input[name="_token"]').getAttribute('value');
    console.log('✅ Token CSRF obtenido');

    // Llenar el formulario de login
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');

    // Hacer click en submit y esperar la redirección
    console.log('🔑 Enviando credenciales...');
    await page.click('button[type="submit"]');

    // Esperar a que se complete la redirección (pueden ser varios pasos)
    await page.waitForTimeout(3000);

    // Verificar si el login fue exitoso
    const currentUrl = page.url();
    console.log(`📍 URL actual después del login: ${currentUrl}`);

    if (currentUrl.includes('/login')) {
        console.log('⚠️ Aún en login, verificando errores...');

        // Buscar mensajes de error
        const errorMessages = await page.locator('.alert-danger, .text-red-500, .error').count();
        if (errorMessages > 0) {
            const errorText = await page.locator('.alert-danger, .text-red-500, .error').first().textContent();
            console.log(`❌ Error de login: ${errorText}`);
        } else {
            console.log('ℹ️ No hay mensajes de error visibles');
        }
    } else {
        console.log('✅ Login exitoso, usuario autenticado');
    }

    // Intentar navegar directamente a un vehículo
    console.log('🚗 Navegando directamente a vehículo...');
    await page.goto('http://127.0.0.1:8000/vehiculos/1');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(4000); // Esperar que cargue completamente incluyendo JS

    const finalUrl = page.url();
    console.log(`📍 URL final: ${finalUrl}`);

    if (finalUrl.includes('/vehiculos/1')) {
        console.log('✅ Acceso exitoso a la vista de vehículo');

        // VERIFICACIÓN PRINCIPAL: Estado de los modales
        console.log('\n🔍 VERIFICANDO ESTADO DE MODALES:');

        const modales = [
            'cambiar-operador-modal',
            'cambiar-obra-modal',
            'registrar-mantenimiento-modal',
            'responsable-obra-modal',
            'kilometraje-modal'
        ];

        let hayProblemas = false;
        let detallesModales = {};

        for (const modalId of modales) {
            try {
                const modal = page.locator(`#${modalId}`);
                const existe = await modal.count() > 0;

                if (existe) {
                    const [isVisible, computedStyles, classes] = await Promise.all([
                        modal.isVisible(),
                        modal.evaluate(el => {
                            const styles = window.getComputedStyle(el);
                            return {
                                display: styles.display,
                                visibility: styles.visibility,
                                opacity: styles.opacity,
                                zIndex: styles.zIndex,
                                position: styles.position
                            };
                        }),
                        modal.getAttribute('class')
                    ]);

                    detallesModales[modalId] = {
                        existe: true,
                        visible: isVisible,
                        styles: computedStyles,
                        classes: classes
                    };

                    console.log(`\n   📋 ${modalId}:`);
                    console.log(`      ✓ Existe: SÍ`);
                    console.log(`      ✓ Visible: ${isVisible ? '🔴 SÍ - PROBLEMA!' : '✅ NO'}`);
                    console.log(`      ✓ Display: ${computedStyles.display}`);
                    console.log(`      ✓ Classes: ${classes || 'ninguna'}`);

                    if (isVisible) {
                        hayProblemas = true;
                        console.log(`      🚨 MODAL AUTO-ABIERTO DETECTADO!`);
                    }
                } else {
                    detallesModales[modalId] = { existe: false };
                    console.log(`\n   📋 ${modalId}: ❓ No existe en DOM`);
                }
            } catch (error) {
                console.log(`\n   📋 ${modalId}: ⚠️ Error: ${error.message}`);
                detallesModales[modalId] = { error: error.message };
            }
        }

        // Screenshot del estado actual
        await page.screenshot({ path: 'verificacion-final-modales.png', fullPage: true });
        console.log('\n📸 Screenshot guardado: verificacion-final-modales.png');

        // Verificar funcionalidad de botones
        console.log('\n🧪 VERIFICANDO FUNCIONALIDAD DE BOTONES:');

        const botones = [
            { selector: 'button:has-text("Asignar Operador")', modal: 'cambiar-operador-modal' },
            { selector: 'button:has-text("Cambiar Obra")', modal: 'cambiar-obra-modal' },
            { selector: 'button:has-text("Registrar Mantenimiento")', modal: 'registrar-mantenimiento-modal' }
        ];

        for (const boton of botones) {
            try {
                const btn = page.locator(boton.selector);
                const btnCount = await btn.count();

                if (btnCount > 0) {
                    console.log(`\n   🔘 Probando: ${boton.selector}`);
                    console.log(`      ✓ Botón encontrado: SÍ`);

                    // Click en el botón
                    await btn.click();
                    await page.waitForTimeout(1000);

                    // Verificar si el modal se abre
                    const modal = page.locator(`#${boton.modal}`);
                    const modalAbierto = await modal.isVisible();

                    console.log(`      ✓ Modal se abre: ${modalAbierto ? '✅ SÍ' : '❌ NO'}`);

                    if (modalAbierto) {
                        // Cerrar el modal
                        const cerrarBtn = modal.locator('button:has-text("Cancelar"), .btn-secondary');
                        if (await cerrarBtn.count() > 0) {
                            await cerrarBtn.click();
                            await page.waitForTimeout(500);
                            console.log(`      ✓ Modal cerrado: ✅ SÍ`);
                        }
                    }
                } else {
                    console.log(`\n   🔘 ${boton.selector}: ❌ No encontrado`);
                }
            } catch (error) {
                console.log(`\n   🔘 Error con ${boton.selector}: ${error.message}`);
            }
        }

        // RESULTADO FINAL
        console.log('\n🏁 RESULTADO FINAL DE LA VERIFICACIÓN:');
        console.log('='.repeat(50));

        if (hayProblemas) {
            console.log('🔴 ESTADO: PROBLEMAS DETECTADOS');
            console.log('   ❌ Hay modales que se abren automáticamente');
            console.log('   🔧 Requiere ajustes adicionales');

            // Mostrar cuáles modales tienen problemas
            Object.entries(detallesModales).forEach(([modalId, info]) => {
                if (info.visible) {
                    console.log(`   🚨 Problemático: ${modalId}`);
                }
            });
        } else {
            console.log('🟢 ESTADO: CORRECTO');
            console.log('   ✅ No hay modales auto-abiertos');
            console.log('   ✅ La vista funciona correctamente');
            console.log('   🎉 ¡PROBLEMA RESUELTO!');
        }

        console.log('='.repeat(50));

    } else {
        console.log('❌ No se pudo acceder a la vista de vehículo');
        console.log(`   Redirigido a: ${finalUrl}`);

        // Screenshot del problema
        await page.screenshot({ path: 'error-acceso-vehiculo.png' });
        console.log('📸 Screenshot de error: error-acceso-vehiculo.png');
    }

} catch (error) {
    console.error('💥 Error durante la verificación:', error.message);
    await page.screenshot({ path: 'error-verificacion-final.png' });
    console.log('📸 Screenshot de error: error-verificacion-final.png');
} finally {
    await browser.close();
}

console.log('\n🏁 Verificación completada');
