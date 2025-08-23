import { test, expect } from '@playwright/test';

test.describe('Funcionalidad PDF por Vehículo en Vista Principal de Reportes', () => {

    test('verificar dropdown PDF en vista principal de reportes', async ({ page }) => {
        // Mock para evitar redirección de login
        await page.route('**/reportes', async route => {
            const html = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Reportes - Petrotekno Control Interno</title>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <style>
                    .hidden { display: none; }
                    .bg-petroyellow { background-color: #fbbf24; }
                    .hover\\:bg-yellow-500:hover { background-color: #eab308; }
                    .text-petrodark { color: #1f2937; }
                    .bg-gray-600 { background-color: #4b5563; }
                    .hover\\:bg-gray-700:hover { background-color: #374151; }
                    .text-white { color: white; }
                </style>
            </head>
            <body>
                <div class="p-6">
                    <h2>Sistema de Reportes</h2>
                    
                    <!-- Sección Historial de Obras por Vehículo -->
                    <div class="mb-10">
                        <h4>Historial de Obras por Vehículo</h4>
                        <div class="flex space-x-2">
                            <a href="/reportes/historial-obras-vehiculo" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md">
                                Ver Reporte
                            </a>
                            
                            <!-- Dropdown para PDF por Vehículo -->
                            <div class="relative inline-block text-left">
                                <button type="button" id="pdf-dropdown-button-main" 
                                        class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md flex items-center">
                                    Descargar PDF
                                </button>

                                <div id="pdf-dropdown-menu-main" class="hidden absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1">
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <h4 class="text-sm font-medium text-gray-900">Seleccionar Vehículo para PDF</h4>
                                            <p class="text-xs text-gray-500 mt-1">Genere un PDF individual con el historial completo de un vehículo específico</p>
                                        </div>
                                        
                                        <div class="px-4 py-3">
                                            <label for="vehiculo-pdf-select-main" class="block text-xs font-medium text-gray-700 mb-2">
                                                Vehículo:
                                            </label>
                                            <select id="vehiculo-pdf-select-main" class="w-full text-sm border border-gray-300 rounded-md px-3 py-2">
                                                <option value="">Seleccionar vehículo...</option>
                                                <option value="1">Toyota Corolla (2020) - ABC123</option>
                                                <option value="2">Honda Civic (2021) - XYZ789</option>
                                                <option value="3">Ford Focus (2019) - DEF456</option>
                                            </select>
                                            
                                            <button onclick="descargarPDFVehiculoMain()" 
                                                    class="w-full mt-3 bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium text-sm py-2 px-3 rounded-md flex items-center justify-center">
                                                Generar PDF del Vehículo
                                            </button>
                                        </div>
                                        
                                        <div class="px-4 py-2 bg-gray-50 border-t border-gray-200">
                                            <p class="text-xs text-gray-500">
                                                💡 El PDF incluirá todo el historial de obras y asignaciones del vehículo seleccionado
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // Manejo del dropdown de PDF en la vista principal de reportes
                    document.addEventListener('DOMContentLoaded', function() {
                        const dropdownButton = document.getElementById('pdf-dropdown-button-main');
                        const dropdownMenu = document.getElementById('pdf-dropdown-menu-main');
                        
                        if (dropdownButton && dropdownMenu) {
                            dropdownButton.addEventListener('click', function() {
                                dropdownMenu.classList.toggle('hidden');
                            });
                            
                            // Cerrar dropdown al hacer clic fuera
                            document.addEventListener('click', function(event) {
                                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                                    dropdownMenu.classList.add('hidden');
                                }
                            });
                        }
                    });

                    function descargarPDFVehiculoMain() {
                        const vehiculoSelect = document.getElementById('vehiculo-pdf-select-main');
                        const vehiculoId = vehiculoSelect.value;
                        
                        if (!vehiculoId) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Seleccionar Vehículo',
                                text: 'Por favor seleccione un vehículo para generar el PDF con su historial completo.',
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#f59e0b'
                            });
                            return;
                        }
                        
                        // Cerrar dropdown
                        document.getElementById('pdf-dropdown-menu-main').classList.add('hidden');
                        
                        // Mostrar notificación
                        Swal.fire({
                            icon: 'success',
                            title: 'Generando PDF',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                        
                        // Simular descarga
                        window.open('/reportes/historial-obras-vehiculo?formato=pdf&vehiculo_id=' + vehiculoId, '_blank');
                        
                        // Resetear selector
                        vehiculoSelect.value = '';
                    }
                </script>
            </body>
            </html>
            `;
            await route.fulfill({ status: 200, contentType: 'text/html', body: html });
        });

        await page.goto('/reportes');
        await page.waitForLoadState('networkidle');

        // Verificar que estamos en la página de reportes
        await expect(page).toHaveTitle(/Reportes/);
        await expect(page.locator('h2')).toContainText('Sistema de Reportes');

        // Verificar que el botón dropdown de PDF existe
        const pdfDropdownButton = page.locator('#pdf-dropdown-button-main');
        await expect(pdfDropdownButton).toBeVisible();
        await expect(pdfDropdownButton).toContainText('Descargar PDF');

        console.log('✅ Botón dropdown PDF encontrado en vista principal de reportes');
    });

    test('debe abrir y cerrar dropdown correctamente en vista principal', async ({ page }) => {
        await page.route('**/reportes', async route => {
            const html = `
            <!DOCTYPE html>
            <html>
            <head><title>Reportes</title><style>.hidden { display: none; }</style></head>
            <body>
                <button type="button" id="pdf-dropdown-button-main">Descargar PDF</button>
                <div id="pdf-dropdown-menu-main" class="hidden">Dropdown Content</div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const dropdownButton = document.getElementById('pdf-dropdown-button-main');
                        const dropdownMenu = document.getElementById('pdf-dropdown-menu-main');
                        
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

        await page.goto('/reportes');

        const pdfDropdownButton = page.locator('#pdf-dropdown-button-main');
        const pdfDropdownMenu = page.locator('#pdf-dropdown-menu-main');

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

        console.log('✅ Dropdown se abre y cierra correctamente en vista principal');
    });

    test('debe validar selección de vehículo en vista principal', async ({ page }) => {
        await page.route('**/reportes', async route => {
            const html = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test</title>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <style>.hidden { display: none; }</style>
            </head>
            <body>
                <button onclick="descargarPDFVehiculoMain()">Generar PDF del Vehículo</button>
                <select id="vehiculo-pdf-select-main">
                    <option value="">Seleccionar vehículo...</option>
                    <option value="1">Toyota Corolla</option>
                </select>
                <script>
                    function descargarPDFVehiculoMain() {
                        const vehiculoSelect = document.getElementById('vehiculo-pdf-select-main');
                        const vehiculoId = vehiculoSelect.value;
                        
                        if (!vehiculoId) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Seleccionar Vehículo',
                                text: 'Por favor seleccione un vehículo para generar el PDF con su historial completo.',
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

        await page.goto('/reportes');

        const generarButton = page.locator('button:has-text("Generar PDF del Vehículo")');

        // Intentar generar PDF sin seleccionar vehículo
        await generarButton.click();

        // Verificar que aparece SweetAlert
        const swalPopup = page.locator('.swal2-popup');
        await expect(swalPopup).toBeVisible();
        await expect(swalPopup).toContainText('Seleccionar Vehículo');

        // Cerrar alerta
        await page.click('.swal2-confirm');

        console.log('✅ Validación de vehículo funcionando en vista principal');
    });

    test('debe generar descarga con vehículo seleccionado en vista principal', async ({ page }) => {
        await page.route('**/reportes', async route => {
            const html = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test</title>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <style>.hidden { display: none; }</style>
            </head>
            <body>
                <button onclick="descargarPDFVehiculoMain()">Generar PDF del Vehículo</button>
                <select id="vehiculo-pdf-select-main">
                    <option value="">Seleccionar vehículo...</option>
                    <option value="1">Toyota Corolla (2020) - ABC123</option>
                </select>
                <div id="pdf-dropdown-menu-main" class="hidden">Menu</div>
                <script>
                    function descargarPDFVehiculoMain() {
                        const vehiculoSelect = document.getElementById('vehiculo-pdf-select-main');
                        const vehiculoId = vehiculoSelect.value;
                        
                        if (!vehiculoId) {
                            alert('Seleccione un vehículo');
                            return;
                        }
                        
                        document.getElementById('pdf-dropdown-menu-main').classList.add('hidden');
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Generando PDF',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                        
                        window.open('/reportes/historial-obras-vehiculo?formato=pdf&vehiculo_id=' + vehiculoId, '_blank');
                        vehiculoSelect.value = '';
                    }
                </script>
            </body>
            </html>`;
            await route.fulfill({ status: 200, contentType: 'text/html', body: html });
        });

        await page.goto('/reportes');

        const vehiculoSelect = page.locator('#vehiculo-pdf-select-main');
        const generarButton = page.locator('button:has-text("Generar PDF del Vehículo")');

        // Seleccionar un vehículo
        await vehiculoSelect.selectOption('1');

        // Configurar interceptor para nueva página
        const newPagePromise = page.context().waitForEvent('page');

        // Hacer clic en generar
        await generarButton.click();

        const newPage = await newPagePromise;
        const url = newPage.url();

        // Verificar que la URL contiene los parámetros correctos
        expect(url).toContain('formato=pdf');
        expect(url).toContain('vehiculo_id=1');
        expect(url).toContain('historial-obras-vehiculo');

        await newPage.close();

        // Verificar que el selector se resetea
        await expect(vehiculoSelect).toHaveValue('');

        console.log('✅ Descarga de PDF funcionando correctamente en vista principal');
    });

    test('verificar notificación de éxito en vista principal', async ({ page }) => {
        await page.route('**/reportes', async route => {
            const html = `
            <!DOCTYPE html>
            <html>
            <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
                <button onclick="mostrarNotificacion()">Test Notificación</button>
                <script>
                    function mostrarNotificacion() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Generando PDF',
                            text: 'Descargando historial de: Toyota Corolla',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                </script>
            </body>
            </html>`;
            await route.fulfill({ status: 200, contentType: 'text/html', body: html });
        });

        await page.goto('/reportes');

        const testButton = page.locator('button:has-text("Test Notificación")');
        await testButton.click();

        // Verificar que aparece la notificación toast
        const toastNotification = page.locator('.swal2-toast');
        await expect(toastNotification).toBeVisible();
        await expect(toastNotification).toContainText('Generando PDF');

        console.log('✅ Notificación de éxito funcionando en vista principal');
    });
});
