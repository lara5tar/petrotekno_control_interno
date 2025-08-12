import { test, expect } from '@playwright/test';

test.describe('Test Directo Funcionalidad Vehículos', () => {
    test('verificar funcionalidad JavaScript directamente', async ({ page }) => {
        console.log('=== TEST DIRECTO JAVASCRIPT VEHÍCULOS ===');

        // Crear página HTML de prueba directa
        const htmlContent = `
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Vehículos</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        .vehicle-card { border: 1px solid #ccc; padding: 16px; margin: 8px 0; border-radius: 8px; }
        .remove-vehicle { color: red; cursor: pointer; }
        button { padding: 8px 16px; margin: 4px; cursor: pointer; }
        .bg-blue-500 { background-color: #3b82f6; color: white; }
    </style>
</head>
<body>
    <div x-data="obraFormController()">
        <h2>Test Agregar Vehículos</h2>
        
        <button type="button" 
                @click="addVehicle()"
                class="bg-blue-500">
            Agregar Vehículo
        </button>

        <div id="vehiculosContainer" class="space-y-4">
            <!-- Los vehículos se agregarán aquí -->
        </div>
    </div>

    <!-- Template para vehículos -->
    <template id="vehicleTemplate">
        <div class="vehicle-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h4>Vehículo <span class="vehicle-number"></span></h4>
                <button type="button" class="remove-vehicle">Eliminar</button>
            </div>
            <div>
                <label>Vehículo:</label>
                <select name="vehiculos[INDEX][vehiculo_id]" required>
                    <option value="">Seleccionar vehículo...</option>
                    <option value="1">ABC-123 - Toyota Corolla</option>
                    <option value="2">XYZ-789 - Ford F-150</option>
                </select>
            </div>
            <div>
                <label>Kilometraje Inicial:</label>
                <input type="number" name="vehiculos[INDEX][kilometraje_inicial]" placeholder="0">
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('obraFormController', () => ({
                vehicleIndex: 0,
                
                addVehicle() {
                    console.log('🔄 Ejecutando addVehicle()...');
                    
                    const template = document.getElementById('vehicleTemplate');
                    const container = document.getElementById('vehiculosContainer');
                    
                    if (!template || !container) {
                        console.error('❌ Template o container no encontrado');
                        console.log('Template exists:', !!template);
                        console.log('Container exists:', !!container);
                        return;
                    }

                    console.log('✅ Template y container encontrados');
                    
                    // Clonar el template
                    const clone = template.content.cloneNode(true);
                    
                    // Actualizar los índices
                    const currentIndex = this.vehicleIndex++;
                    console.log('📊 Índice actual:', currentIndex);
                    
                    // Actualizar el número del vehículo
                    const vehicleNumber = clone.querySelector('.vehicle-number');
                    if (vehicleNumber) {
                        vehicleNumber.textContent = \`#\${currentIndex + 1}\`;
                    }
                    
                    // Actualizar los nombres de los inputs
                    const inputs = clone.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        if (input.name) {
                            input.name = input.name.replace('INDEX', currentIndex);
                        }
                    });
                    
                    // Agregar event listener para el botón de eliminar
                    const removeButton = clone.querySelector('.remove-vehicle');
                    if (removeButton) {
                        removeButton.addEventListener('click', (e) => {
                            const vehicleCard = e.target.closest('.vehicle-card');
                            if (vehicleCard) {
                                vehicleCard.remove();
                                this.updateVehicleNumbers();
                            }
                        });
                    }
                    
                    // Agregar al container
                    container.appendChild(clone);
                    
                    console.log('✅ Vehículo agregado exitosamente');
                },

                updateVehicleNumbers() {
                    const vehicleCards = document.querySelectorAll('.vehicle-card');
                    vehicleCards.forEach((card, index) => {
                        const numberSpan = card.querySelector('.vehicle-number');
                        if (numberSpan) {
                            numberSpan.textContent = \`#\${index + 1}\`;
                        }
                    });
                }
            }));
        });
    </script>
</body>
</html>
        `;

        // Cargar el HTML directamente
        await page.setContent(htmlContent);

        // Esperar a que Alpine.js se cargue
        await page.waitForFunction(() => typeof window.Alpine !== 'undefined');
        console.log('✅ Alpine.js cargado');

        // Esperar a que el componente se inicialice
        await page.waitForSelector('[x-data="obraFormController()"]');
        console.log('✅ Componente Alpine inicializado');

        // Verificar elementos necesarios
        const elementCheck = await page.evaluate(() => {
            return {
                template: document.getElementById('vehicleTemplate') !== null,
                container: document.getElementById('vehiculosContainer') !== null,
                button: document.querySelector('button[\\@click="addVehicle()"]') !== null,
                alpineComponent: document.querySelector('[x-data="obraFormController()"]') !== null
            };
        });

        console.log(`📋 Template existe: ${elementCheck.template ? '✅ SÍ' : '❌ NO'}`);
        console.log(`📦 Container existe: ${elementCheck.container ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🔘 Botón existe: ${elementCheck.button ? '✅ SÍ' : '❌ NO'}`);
        console.log(`🔧 Componente Alpine: ${elementCheck.alpineComponent ? '✅ SÍ' : '❌ NO'}`);

        // Escuchar logs de consola
        const consoleLogs = [];
        page.on('console', msg => {
            consoleLogs.push(msg.text());
            console.log(`📝 CONSOLE: ${msg.text()}`);
        });

        // Probar la funcionalidad
        console.log('🔄 Probando funcionalidad...');

        // Contar vehículos antes
        const vehiclesBefore = await page.locator('.vehicle-card').count();
        console.log(`🚗 Vehículos antes: ${vehiclesBefore}`);

        // Hacer click en el botón
        await page.click('button[\\@click="addVehicle()"]');

        // Esperar un momento
        await page.waitForTimeout(1000);

        // Contar vehículos después
        const vehiclesAfter = await page.locator('.vehicle-card').count();
        console.log(`🚗 Vehículos después: ${vehiclesAfter}`);

        const success = vehiclesAfter > vehiclesBefore;
        console.log(`✅ ¿Funciona?: ${success ? '🎉 SÍ' : '❌ NO'}`);

        if (success) {
            console.log('🎉 ¡ÉXITO! La funcionalidad JavaScript funciona correctamente');

            // Verificar que el vehículo tiene los elementos correctos
            const vehicleContent = await page.evaluate(() => {
                const vehicleCard = document.querySelector('.vehicle-card');
                if (!vehicleCard) return null;

                return {
                    hasSelect: vehicleCard.querySelector('select') !== null,
                    hasInput: vehicleCard.querySelector('input') !== null,
                    hasRemoveButton: vehicleCard.querySelector('.remove-vehicle') !== null,
                    selectOptions: vehicleCard.querySelectorAll('select option').length
                };
            });

            if (vehicleContent) {
                console.log(`🔍 Select presente: ${vehicleContent.hasSelect ? '✅ SÍ' : '❌ NO'}`);
                console.log(`🔍 Input presente: ${vehicleContent.hasInput ? '✅ SÍ' : '❌ NO'}`);
                console.log(`🔍 Botón eliminar: ${vehicleContent.hasRemoveButton ? '✅ SÍ' : '❌ NO'}`);
                console.log(`🔍 Opciones en select: ${vehicleContent.selectOptions}`);
            }

            // Probar agregar otro vehículo
            console.log('🔄 Probando agregar segundo vehículo...');
            await page.click('button[\\@click="addVehicle()"]');
            await page.waitForTimeout(500);

            const vehiclesAfterSecond = await page.locator('.vehicle-card').count();
            console.log(`🚗 Vehículos después del segundo: ${vehiclesAfterSecond}`);

            // Probar eliminar vehículo
            if (vehiclesAfterSecond > 1) {
                console.log('🔄 Probando eliminar vehículo...');
                await page.click('.remove-vehicle');
                await page.waitForTimeout(500);

                const vehiclesAfterRemove = await page.locator('.vehicle-card').count();
                console.log(`🚗 Vehículos después de eliminar: ${vehiclesAfterRemove}`);
            }

        } else {
            console.log('❌ La funcionalidad NO funciona');

            // Debug adicional
            const debugInfo = await page.evaluate(() => {
                const alpineElement = document.querySelector('[x-data="obraFormController()"]');
                return {
                    alpineInitialized: alpineElement && alpineElement._x_dataStack && alpineElement._x_dataStack.length > 0,
                    hasAddVehicleFunction: alpineElement && alpineElement._x_dataStack && alpineElement._x_dataStack[0] && typeof alpineElement._x_dataStack[0].addVehicle === 'function'
                };
            });

            console.log(`🔍 Alpine inicializado: ${debugInfo.alpineInitialized ? '✅ SÍ' : '❌ NO'}`);
            console.log(`🔍 Función addVehicle: ${debugInfo.hasAddVehicleFunction ? '✅ SÍ' : '❌ NO'}`);
        }

        console.log('\n📋 === RESUMEN ===');
        console.log(`📊 Funcionalidad funciona: ${success ? '✅ SÍ' : '❌ NO'}`);
        console.log(`📊 Logs de consola: ${consoleLogs.length}`);

        // Verificar que la funcionalidad básica funciona
        expect(success).toBe(true);
    });
});