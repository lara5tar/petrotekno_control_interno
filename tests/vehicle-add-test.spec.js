import { test, expect } from '@playwright/test';

test('Diagnosticar funcionalidad de agregar vehículo', async ({ page }) => {
  // Navegar directamente a una página de prueba local
  await page.setContent(`
    <!DOCTYPE html>
    <html>
    <head>
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body>
        <div x-data="obraFormController()">
            <button type="button" 
                    @click="addVehicle()"
                    id="addVehicleBtn"
                    class="bg-blue-500 text-white px-4 py-2 rounded">
                Agregar Vehículo
            </button>
            
            <div id="vehiculosContainer" class="mt-4">
                <!-- Los vehículos se agregarán dinámicamente aquí -->
            </div>
        </div>

        <!-- Template para vehículos -->
        <template id="vehicleTemplate">
            <div class="bg-white border border-gray-200 rounded-lg p-4 vehicle-card">
                <div class="flex justify-between items-start mb-4">
                    <h4 class="text-lg font-medium text-gray-900 flex items-center">
                        Vehículo <span class="vehicle-number text-gray-700"></span>
                    </h4>
                    <button type="button" 
                            class="text-red-500 hover:text-red-700 remove-vehicle">
                        Eliminar
                    </button>
                </div>
                <div class="form-group">
                    <select class="w-full px-3 py-2 border vehicle-select" 
                            name="vehiculos[INDEX][vehiculo_id]" required>
                        <option value="">Seleccionar vehículo...</option>
                        <option value="1">ABC-123 - Toyota Corolla</option>
                        <option value="2">XYZ-789 - Ford F-150</option>
                    </select>
                </div>
            </div>
        </template>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('obraFormController', () => ({
                    vehicleIndex: 0,
                    
                    addVehicle() {
                        console.log('addVehicle() llamado');
                        const template = document.getElementById('vehicleTemplate');
                        const container = document.getElementById('vehiculosContainer');
                        
                        console.log('Template:', template);
                        console.log('Container:', container);
                        
                        if (!template || !container) {
                            console.error('Template o container no encontrado');
                            return;
                        }

                        // Clonar el template
                        const clone = template.content.cloneNode(true);
                        console.log('Clone creado:', clone);
                        
                        // Actualizar los índices
                        const currentIndex = this.vehicleIndex++;
                        console.log('Índice actual:', currentIndex);
                        
                        // Actualizar el número del vehículo
                        const vehicleNumber = clone.querySelector('.vehicle-number');
                        if (vehicleNumber) {
                            vehicleNumber.textContent = '#' + (currentIndex + 1);
                        }
                        
                        // Actualizar los nombres de los inputs
                        const inputs = clone.querySelectorAll('input, select, textarea');
                        inputs.forEach(input => {
                            if (input.name) {
                                input.name = input.name.replace('INDEX', currentIndex);
                                console.log('Input name actualizado a:', input.name);
                            }
                        });
                        
                        // Agregar event listener para el botón de eliminar
                        const removeButton = clone.querySelector('.remove-vehicle');
                        if (removeButton) {
                            removeButton.addEventListener('click', (e) => {
                                const vehicleCard = e.target.closest('.vehicle-card');
                                if (vehicleCard) {
                                    vehicleCard.remove();
                                }
                            });
                        }
                        
                        // Agregar al container
                        container.appendChild(clone);
                        console.log('Vehículo agregado al DOM');
                        
                        return true;
                    }
                }));
            });
        </script>
    </body>
    </html>
  `);

  // Esperar a que Alpine.js se cargue
  await page.waitForTimeout(2000);

  // Verificar que el botón existe
  const addButton = await page.locator('#addVehicleBtn');
  await expect(addButton).toBeVisible();

  // Verificar el container inicial
  const container = await page.locator('#vehiculosContainer');
  await expect(container).toBeVisible();

  // Contar vehículos iniciales
  const initialCount = await page.locator('.vehicle-card').count();
  console.log('Vehículos iniciales:', initialCount);

  // Agregar event listener para logs de consola
  page.on('console', msg => {
    console.log('BROWSER LOG:', msg.text());
  });

  // Hacer clic en agregar vehículo
  console.log('Haciendo clic en agregar vehículo...');
  await addButton.click();

  // Esperar un momento para que se procese
  await page.waitForTimeout(1000);

  // Verificar que se agregó un vehículo
  const finalCount = await page.locator('.vehicle-card').count();
  console.log('Vehículos finales:', finalCount);

  // Verificar que el contador aumentó
  expect(finalCount).toBe(initialCount + 1);

  // Verificar que el vehículo tiene el número correcto
  const vehicleNumber = await page.locator('.vehicle-number').first().textContent();
  console.log('Número de vehículo:', vehicleNumber);
  expect(vehicleNumber).toBe('#1');

  // Verificar que el select tiene las opciones correctas
  const selectOptions = await page.locator('.vehicle-select option').count();
  console.log('Opciones en select:', selectOptions);
  expect(selectOptions).toBeGreaterThan(0);

  // Agregar un segundo vehículo para verificar el índice
  await addButton.click();
  await page.waitForTimeout(500);

  const secondVehicleCount = await page.locator('.vehicle-card').count();
  expect(secondVehicleCount).toBe(initialCount + 2);

  console.log('Test completado exitosamente');
});
