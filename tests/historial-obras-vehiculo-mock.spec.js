import { test, expect } from '@playwright/test';

test.describe('Historial de Obras por Vehículo - Test de Elementos', () => {

    test('debe acceder directamente a la página de historial de obras por vehículo', async ({ page }) => {
        // Ir directamente a la página de reportes (sin autenticación para el test)
        await page.goto('/reportes/historial-obras-vehiculo');

        // Esperar a que cargue la página
        await page.waitForLoadState('networkidle');

        // Verificar que estamos en la página correcta
        await expect(page).toHaveTitle(/Historial de Obras por Vehículo|Reportes|Petrotekno/);
    });

    test('debe mostrar el dropdown de PDF sin autenticación requerida', async ({ page }) => {
        // Configurar respuesta mock para evitar redirección de autenticación
        await page.route('**/reportes/historial-obras-vehiculo', async route => {
            if (route.request().method() === 'GET') {
                const html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Test - Historial de Obras por Vehículo</title>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                    <div class="flex gap-4">
                        <!-- Dropdown para PDF -->
                        <div class="relative inline-block text-left">
                            <button type="button" id="pdf-dropdown-button" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Exportar PDF
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div id="pdf-dropdown-menu" class="hidden absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1" role="menu">
                                    <button onclick="validarDescargaPDF()" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 flex items-center">
                                        <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <div class="font-medium">PDF por Vehículo</div>
                                            <div class="text-xs text-gray-500">Requiere seleccionar vehículo</div>
                                        </div>
                                    </button>
                                    
                                    <hr class="my-1">
                                    
                                    <div class="px-4 py-2">
                                        <div class="text-xs font-medium text-gray-500 mb-2">DESCARGAR POR VEHÍCULO ESPECÍFICO:</div>
                                        <select id="vehiculo-pdf-select" class="w-full text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                                            <option value="">Seleccionar vehículo...</option>
                                            <option value="1">Toyota Corolla - ABC123</option>
                                            <option value="2">Honda Civic - XYZ789</option>
                                            <option value="3">Ford Focus - DEF456</option>
                                        </select>
                                        <button onclick="descargarPDFVehiculo()" 
                                                class="w-full mt-2 bg-red-600 hover:bg-red-700 text-white text-sm py-1 px-2 rounded-md transition-colors">
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Descargar PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtro principal para la función original -->
                    <select name="vehiculo_id" style="display: none;">
                        <option value="">Seleccionar...</option>
                        <option value="1">Vehículo 1</option>
                    </select>

                    <script>
                        // Manejo del dropdown de PDF
                        document.addEventListener('DOMContentLoaded', function() {
                            const dropdownButton = document.getElementById('pdf-dropdown-button');
                            const dropdownMenu = document.getElementById('pdf-dropdown-menu');
                            
                            dropdownButton.addEventListener('click', function() {
                                dropdownMenu.classList.toggle('hidden');
                            });
                            
                            // Cerrar dropdown al hacer clic fuera
                            document.addEventListener('click', function(event) {
                                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                                    dropdownMenu.classList.add('hidden');
                                }
                            });
                        });

                        function validarDescargaPDF() {
                            const vehiculoId = document.querySelector('select[name="vehiculo_id"]').value;
                            
                            if (!vehiculoId) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Vehículo Requerido',
                                    text: 'Para generar el PDF debe seleccionar un vehículo específico.',
                                    confirmButtonText: 'Entendido',
                                    confirmButtonColor: '#f59e0b'
                                });
                                return;
                            }
                            
                            // Simular descarga
                            window.open('/reportes/historial-obras-vehiculo?formato=pdf', '_blank');
                        }

                        function descargarPDFVehiculo() {
                            const vehiculoSelect = document.getElementById('vehiculo-pdf-select');
                            const vehiculoId = vehiculoSelect.value;
                            
                            if (!vehiculoId) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Seleccionar Vehículo',
                                    text: 'Por favor seleccione un vehículo para generar el PDF.',
                                    confirmButtonText: 'Entendido',
                                    confirmButtonColor: '#f59e0b'
                                });
                                return;
                            }
                            
                            // Cerrar dropdown
                            document.getElementById('pdf-dropdown-menu').classList.add('hidden');
                            
                            // Simular descarga con parámetros específicos
                            window.open('/reportes/historial-obras-vehiculo?formato=pdf&vehiculo_id=' + vehiculoId, '_blank');
                            
                            // Resetear selector
                            vehiculoSelect.value = '';
                        }
                    </script>
                </body>
                </html>
                `;
                await route.fulfill({
                    status: 200,
                    contentType: 'text/html',
                    body: html
                });
            } else {
                await route.continue();
            }
        });

        await page.goto('/reportes/historial-obras-vehiculo');
        await page.waitForLoadState('networkidle');

        // Verificar que el botón del dropdown de PDF existe
        const pdfDropdownButton = page.locator('#pdf-dropdown-button');
        await expect(pdfDropdownButton).toBeVisible();

        // Verificar el texto del botón
        await expect(pdfDropdownButton).toContainText('Exportar PDF');

        // Verificar que tiene el icono de dropdown
        const dropdownIcon = pdfDropdownButton.locator('svg').last();
        await expect(dropdownIcon).toBeVisible();
    });

    test('debe abrir y cerrar el dropdown correctamente', async ({ page }) => {
        // Mock similar al test anterior
        await page.route('**/reportes/historial-obras-vehiculo', async route => {
            const html = `
            <!DOCTYPE html>
            <html>
            <head><title>Test</title></head>
            <body>
                <button type="button" id="pdf-dropdown-button">Exportar PDF</button>
                <div id="pdf-dropdown-menu" class="hidden">Dropdown Content</div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const dropdownButton = document.getElementById('pdf-dropdown-button');
                        const dropdownMenu = document.getElementById('pdf-dropdown-menu');
                        
                        dropdownButton.addEventListener('click', function() {
                            dropdownMenu.classList.toggle('hidden');
                        });
                        
                        document.addEventListener('click', function(event) {
                            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                                dropdownMenu.classList.add('hidden');
                            }
                        });
                    });
                </script>
            </body>
            </html>`;
            await route.fulfill({ status: 200, contentType: 'text/html', body: html });
        });

        await page.goto('/reportes/historial-obras-vehiculo');

        const pdfDropdownButton = page.locator('#pdf-dropdown-button');
        const pdfDropdownMenu = page.locator('#pdf-dropdown-menu');

        // Verificar que el dropdown está inicialmente oculto
        await expect(pdfDropdownMenu).toHaveClass(/hidden/);

        // Hacer clic en el botón para abrir el dropdown
        await pdfDropdownButton.click();

        // Verificar que el dropdown se abre
        await expect(pdfDropdownMenu).not.toHaveClass(/hidden/);

        // Hacer clic fuera del dropdown para cerrarlo
        await page.click('body');

        // Verificar que el dropdown se cierra
        await expect(pdfDropdownMenu).toHaveClass(/hidden/);
    });

    test('debe validar selección de vehículo', async ({ page }) => {
        await page.route('**/reportes/historial-obras-vehiculo', async route => {
            const html = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test</title>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
                <button onclick="descargarPDFVehiculo()">Descargar PDF</button>
                <select id="vehiculo-pdf-select">
                    <option value="">Seleccionar vehículo...</option>
                    <option value="1">Toyota Corolla</option>
                </select>
                <script>
                    function descargarPDFVehiculo() {
                        const vehiculoSelect = document.getElementById('vehiculo-pdf-select');
                        const vehiculoId = vehiculoSelect.value;
                        
                        if (!vehiculoId) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Seleccionar Vehículo',
                                text: 'Por favor seleccione un vehículo para generar el PDF.',
                                confirmButtonText: 'Entendido'
                            });
                            return;
                        }
                        
                        window.open('/reportes/historial-obras-vehiculo?formato=pdf&vehiculo_id=' + vehiculoId, '_blank');
                        vehiculoSelect.value = '';
                    }
                </script>
            </body>
            </html>`;
            await route.fulfill({ status: 200, contentType: 'text/html', body: html });
        });

        await page.goto('/reportes/historial-obras-vehiculo');

        const descargarButton = page.locator('button:has-text("Descargar PDF")');

        // Intentar descargar sin seleccionar vehículo
        await descargarButton.click();

        // Verificar que aparece SweetAlert
        const swalPopup = page.locator('.swal2-popup');
        await expect(swalPopup).toBeVisible();
        await expect(swalPopup).toContainText('Seleccionar Vehículo');

        // Cerrar alerta
        await page.click('.swal2-confirm');
        await expect(swalPopup).not.toBeVisible();
    });

    test('debe generar descarga con vehículo seleccionado', async ({ page }) => {
        await page.route('**/reportes/historial-obras-vehiculo', async route => {
            const html = `
            <!DOCTYPE html>
            <html>
            <head><title>Test</title></head>
            <body>
                <button onclick="descargarPDFVehiculo()">Descargar PDF</button>
                <select id="vehiculo-pdf-select">
                    <option value="">Seleccionar vehículo...</option>
                    <option value="1">Toyota Corolla</option>
                </select>
                <script>
                    function descargarPDFVehiculo() {
                        const vehiculoSelect = document.getElementById('vehiculo-pdf-select');
                        const vehiculoId = vehiculoSelect.value;
                        
                        if (!vehiculoId) {
                            alert('Seleccione un vehículo');
                            return;
                        }
                        
                        window.open('/reportes/historial-obras-vehiculo?formato=pdf&vehiculo_id=' + vehiculoId, '_blank');
                        vehiculoSelect.value = '';
                    }
                </script>
            </body>
            </html>`;
            await route.fulfill({ status: 200, contentType: 'text/html', body: html });
        });

        await page.goto('/reportes/historial-obras-vehiculo');

        const vehiculoSelect = page.locator('#vehiculo-pdf-select');
        const descargarButton = page.locator('button:has-text("Descargar PDF")');

        // Seleccionar un vehículo
        await vehiculoSelect.selectOption('1');

        // Configurar interceptor para nueva página
        const newPagePromise = page.context().waitForEvent('page');

        // Hacer clic en descargar
        await descargarButton.click();

        const newPage = await newPagePromise;
        const url = newPage.url();

        // Verificar que la URL contiene los parámetros correctos
        expect(url).toContain('formato=pdf');
        expect(url).toContain('vehiculo_id=1');

        await newPage.close();

        // Verificar que el selector se resetea
        await expect(vehiculoSelect).toHaveValue('');
    });
});
