const { chromium } = require('playwright');

/**
 * Script para verificar que la solución del modal de mantenimiento funciona
 */
async function verificarModal() {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('🔍 VERIFICANDO SOLUCIÓN DEL MODAL DE MANTENIMIENTO...');

        // Ir directamente a la página de detalles de un vehículo con ID 1
        await page.goto('http://localhost:8000/vehiculos/1');
        console.log('📄 Página de detalles del vehículo cargada');
        
        // Tomar captura del estado inicial
        await page.screenshot({ path: './debug-pagina-inicial.png' });
        
        // Esperar que la página cargue completamente
        await page.waitForLoadState('networkidle');
        
        // Buscar la pestaña de mantenimientos usando un selector más específico
        console.log('Buscando la pestaña Mantenimientos...');
        const mantTab = await page.$('button:has-text("Mantenimientos"), [x-on\\:click="activeTab = \'mantenimientos\'"]');
        
        if (!mantTab) {
            console.log('❌ No se encontró la pestaña de Mantenimientos');
            
            // Capturar todos los botones para diagnosticar
            const botones = await page.$$eval('button', btns => btns.map(b => ({ 
                text: b.textContent.trim(),
                classes: b.className
            })));
            
            console.log('Botones encontrados en la página:', botones);
            return;
        }
        
        console.log('✅ Pestaña Mantenimientos encontrada, haciendo clic...');
        await mantTab.click();
        await page.waitForTimeout(1000);
        
        // Tomar captura después de hacer clic en la pestaña
        await page.screenshot({ path: './debug-despues-clic-pestana.png' });
        
        // Buscar el botón de registrar mantenimiento
        console.log('Buscando botón "Registrar Mantenimiento"...');
        const boton = await page.$('button:has-text("Registrar Mantenimiento")');
        
        if (!boton) {
            console.log('❌ No se encontró el botón Registrar Mantenimiento');
            return;
        }
        
        console.log('✅ Botón encontrado, haciendo clic...');
        await boton.click();
        
        // Esperar un momento para que se abra el modal
        await page.waitForTimeout(1000);
        
        // Tomar captura después del clic
        await page.screenshot({ path: './debug-despues-clic-boton.png' });
        
        // Verificar si el modal está visible
        const modalVisible = await page.isVisible('h3:has-text("Registrar Nuevo Mantenimiento")');
        
        console.log(`Modal visible después del clic: ${modalVisible ? '✅ SÍ' : '❌ NO'}`);
        
        // Tomar captura final
        await page.screenshot({ path: './solucion-exitosa.png' });
        
        // Mensaje de conclusión
        if (modalVisible) {
            console.log('✅ SOLUCIÓN EXITOSA: El modal de mantenimiento ahora se abre correctamente.');
        } else {
            console.log('❌ PROBLEMA PERSISTENTE: El modal de mantenimiento sigue sin abrirse.');
        }
        
    } catch (error) {
        console.error('❌ Error:', error);
    } finally {
        await browser.close();
    }
}

verificarModal().catch(console.error);
