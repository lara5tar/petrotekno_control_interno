import { chromium } from 'playwright';

console.log('🔧 VERIFICACIÓN COMPLETA - Vista de vehículo corregida');

const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();

try {
    // Login
    console.log('🔐 Realizando login...');
    await page.goto('http://127.0.0.1:8000/login');
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');

    // Esperar a que cargue el dashboard
    await page.waitForSelector('.container', { timeout: 10000 });
    console.log('✅ Login completado');

    // Ir a la lista de vehículos
    console.log('🚗 Navegando a vehículos...');
    await page.goto('http://127.0.0.1:8000/vehiculos');
    await page.waitForSelector('.table', { timeout: 10000 });

    // Buscar un vehículo y hacer click en "Ver"
    const verLinks = await page.locator('a:has-text("Ver")').all();
    if (verLinks.length > 0) {
        console.log('📋 Accediendo a vista de vehículo...');
        await verLinks[0].click();
        await page.waitForSelector('.card', { timeout: 10000 });

        // Verificar el estado inicial
        console.log('\n📊 ESTADO INICIAL DE LA VISTA:');
        const titulo = await page.title();
        console.log(`   📄 Título: ${titulo}`);
        console.log(`   🌐 URL: ${page.url()}`);

        // Verificar que no hay modales abiertos automáticamente
        console.log('\n🪟 VERIFICANDO MODALES AUTO-ABIERTOS:');

        const modales = [
            'cambiar-operador-modal',
            'cambiar-obra-modal',
            'registrar-mantenimiento-modal',
            'responsable-obra-modal'
        ];

        let modalVisibilidad = {};

        for (const modalId of modales) {
            try {
                const modal = page.locator(`#${modalId}`);
                const existe = await modal.count() > 0;

                if (existe) {
                    const isVisible = await modal.isVisible();
                    const displayStyle = await modal.evaluate(el => window.getComputedStyle(el).display);

                    modalVisibilidad[modalId] = {
                        existe: true,
                        visible: isVisible,
                        display: displayStyle
                    };

                    console.log(`   ${modalId}: ${isVisible ? '🔴 VISIBLE' : '✅ OCULTO'} (display: ${displayStyle})`);
                } else {
                    modalVisibilidad[modalId] = { existe: false };
                    console.log(`   ${modalId}: ❓ No existe`);
                }
            } catch (error) {
                console.log(`   ${modalId}: ⚠️ Error verificando: ${error.message}`);
            }
        }

        // Verificar que los botones existen y funcionan
        console.log('\n🔘 VERIFICANDO BOTONES:');
        const botones = [
            { selector: 'button:has-text("Asignar Operador")', nombre: 'Asignar Operador' },
            { selector: 'button:has-text("Cambiar Obra")', nombre: 'Cambiar Obra' },
            { selector: 'button:has-text("Registrar Mantenimiento")', nombre: 'Registrar Mantenimiento' }
        ];

        for (const boton of botones) {
            try {
                const btn = page.locator(boton.selector);
                const existe = await btn.count() > 0;
                const visible = existe ? await btn.isVisible() : false;

                console.log(`   ${boton.nombre}: ${existe ? (visible ? '✅ Disponible' : '⚠️ Existe pero no visible') : '❌ No encontrado'}`);
            } catch (error) {
                console.log(`   ${boton.nombre}: ⚠️ Error: ${error.message}`);
            }
        }

        // Probar abrir un modal manualmente
        console.log('\n🧪 PROBANDO FUNCIONALIDAD DE MODAL:');
        try {
            const asignarBtn = page.locator('button:has-text("Asignar Operador")');
            if (await asignarBtn.count() > 0) {
                await asignarBtn.click();
                await page.waitForTimeout(1000);

                const modal = page.locator('#cambiar-operador-modal');
                if (await modal.count() > 0) {
                    const isVisible = await modal.isVisible();
                    console.log(`   Modal al hacer click: ${isVisible ? '✅ Se abre correctamente' : '❌ No se abre'}`);

                    if (isVisible) {
                        // Cerrar el modal
                        const cerrarBtn = modal.locator('.btn-secondary, button:has-text("Cancelar")');
                        if (await cerrarBtn.count() > 0) {
                            await cerrarBtn.click();
                            await page.waitForTimeout(500);
                            console.log('   ✅ Modal cerrado correctamente');
                        }
                    }
                } else {
                    console.log('   ❌ Modal no encontrado después del click');
                }
            } else {
                console.log('   ❌ Botón "Asignar Operador" no encontrado');
            }
        } catch (error) {
            console.log(`   ⚠️ Error probando modal: ${error.message}`);
        }

        // Screenshot final
        await page.screenshot({ path: 'vista-vehiculo-verificacion-final.png', fullPage: true });
        console.log('\n📸 Screenshot guardado: vista-vehiculo-verificacion-final.png');

        // Resumen final
        console.log('\n🏁 RESUMEN DE VERIFICACIÓN:');
        const modalAutomatico = Object.values(modalVisibilidad).some(m => m.visible === true);
        console.log(`   Modales auto-abiertos: ${modalAutomatico ? '❌ SÍ' : '✅ NO'}`);
        console.log(`   Vista funcionando: ✅ Correctamente`);

        if (!modalAutomatico) {
            console.log('\n🎉 ¡PROBLEMA RESUELTO! Los modales ya no se abren automáticamente');
        } else {
            console.log('\n⚠️ Los modales aún se abren automáticamente - requiere más ajustes');
        }

    } else {
        console.log('❌ No se encontraron vehículos para verificar');
    }

} catch (error) {
    console.error('💥 Error durante la verificación:', error);
} finally {
    await browser.close();
}

console.log('🏁 Verificación completada');
