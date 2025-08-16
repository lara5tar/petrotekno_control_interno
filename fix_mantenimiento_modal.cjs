// @ts-check
const { chromium } = require('playwright');

/**
 * Script para probar y solucionar el problema del modal de mantenimiento
 */
(async () => {
    const browser = await chromium.launch({ headless: false });
    const page = await browser.newPage();

    try {
        console.log('🔍 INICIANDO DIAGNÓSTICO Y SOLUCIÓN DEL MODAL DE MANTENIMIENTO...');

        // Ir directamente a la página de detalles de un vehículo
        await page.goto('http://localhost:8000/vehiculos/1');
        console.log('📄 Página de detalles del vehículo cargada');

        // Ir a la pestaña de mantenimientos
        await page.click('button:has-text("Mantenimientos")');
        await page.waitForTimeout(1000);
        console.log('✅ Clic en la pestaña Mantenimientos realizado');
        
        // Verificar si el botón existe
        const botonExiste = await page.isVisible('button:has-text("Registrar Mantenimiento")');
        console.log(`Botón existe: ${botonExiste ? '✅ SÍ' : '❌ NO'}`);
        
        if (botonExiste) {
            // Probar si el botón funciona haciendo clic
            await page.click('button:has-text("Registrar Mantenimiento")');
            await page.waitForTimeout(1000);
            
            // Verificar si el modal está visible
            const modalVisible = await page.isVisible('h3:has-text("Registrar Nuevo Mantenimiento")');
            console.log(`Modal visible: ${modalVisible ? '✅ SÍ' : '❌ NO'}`);
            
            if (!modalVisible) {
                console.log('⚠️ El modal no es visible. Analizando el problema...');
                
                // Verificar si Alpine.js está cargado correctamente
                const alpineLoaded = await page.evaluate(() => typeof window.Alpine !== 'undefined');
                console.log(`Alpine.js cargado: ${alpineLoaded ? '✅ SÍ' : '❌ NO'}`);
                
                // Probar la solución - Modificar dinámicamente el botón
                console.log('🛠️ Aplicando solución...');
                
                // Solución: Modificar el atributo @click del botón para asegurar que funciona
                const solucionAplicada = await page.evaluate(() => {
                    // Buscar el botón
                    const boton = document.querySelector('button:has-text("Registrar Mantenimiento")');
                    if (!boton) return false;
                    
                    // Obtener el elemento padre que tiene el x-data
                    const xDataElement = document.querySelector('[x-data*="modalMantenimiento"]');
                    if (!xDataElement) return false;
                    
                    // Asegurarnos que el botón tiene el evento correcto
                    boton.setAttribute('@click', 'modalMantenimiento = true');
                    
                    // Para asegurarnos que el modal tiene el x-show correcto
                    const modal = document.querySelector('div.fixed.inset-0.z-50');
                    if (modal) {
                        modal.setAttribute('x-show', 'modalMantenimiento');
                    }
                    
                    return true;
                });
                
                console.log(`Solución aplicada: ${solucionAplicada ? '✅ SÍ' : '❌ NO'}`);
                
                // Probar si ahora funciona
                await page.click('button:has-text("Registrar Mantenimiento")');
                await page.waitForTimeout(1000);
                
                // Verificar de nuevo
                const modalAhoraVisible = await page.isVisible('h3:has-text("Registrar Nuevo Mantenimiento")');
                console.log(`Modal ahora visible: ${modalAhoraVisible ? '✅ SÍ' : '❌ NO'}`);
                
                if (modalAhoraVisible) {
                    console.log('✅ SOLUCIÓN EXITOSA! El problema era con los eventos de Alpine.js');
                    console.log('📋 Para solucionar permanentemente, asegúrate que:');
                    console.log('   1. El botón tenga correctamente el atributo @click="modalMantenimiento = true"');
                    console.log('   2. El modal tenga correctamente el atributo x-show="modalMantenimiento"');
                    console.log('   3. Ambos estén dentro del mismo componente Alpine.js con x-data');
                } else {
                    console.log('❌ La solución no funcionó. Problema más complejo.');
                }
            } else {
                console.log('✅ El modal funciona correctamente!');
            }
        } else {
            console.log('❌ No se encontró el botón "Registrar Mantenimiento"');
        }
        
        await page.waitForTimeout(3000);
    } catch (error) {
        console.error('❌ Error:', error);
    } finally {
        await browser.close();
    }
})();
