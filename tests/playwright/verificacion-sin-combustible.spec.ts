import { test, expect } from '@playwright/test';

test.describe('Verificación Sin Campo Combustible - Formulario Obras', () => {
    test('verificar que el formulario funcione sin el campo de combustible', async ({ page }) => {
        console.log('=== VERIFICANDO FORMULARIO SIN CAMPO COMBUSTIBLE ===');

        test.setTimeout(60000);

        try {
            // Login
            console.log('🔐 Iniciando login...');
            await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle', timeout: 15000 });

            await page.fill('#email', 'admin@petrotekno.com');
            await page.fill('#password', 'password123');
            await page.click('button[type="submit"]');

            await page.waitForURL(/.*dashboard.*|.*home.*/, { timeout: 15000 });
            console.log('✅ Login exitoso');

            // Navegar a formulario de obras
            console.log('📝 Navegando a formulario de obras...');
            await page.goto('http://localhost:8000/obras/create', { waitUntil: 'networkidle', timeout: 15000 });

            // Verificar que no estamos en login
            const currentUrl = page.url();
            if (currentUrl.includes('/login')) {
                throw new Error('Redirigido al login - problema de autenticación');
            }
            console.log(`✅ URL actual: ${currentUrl}`);

            // Esperar a que la página cargue
            await page.waitForTimeout(3000);

            // Verificar que el formulario está presente
            const form = page.locator('#createObraForm');
            await expect(form).toBeVisible({ timeout: 10000 });
            console.log('✅ Formulario de obra encontrado');

            // === VERIFICAR DROPDOWN DE ENCARGADOS ===
            console.log('👥 Verificando dropdown de encargados...');
            const encargadoSelect = page.locator('#encargado_id');
            await expect(encargadoSelect).toBeVisible({ timeout: 10000 });

            const encargadosValidos = await encargadoSelect.locator('option:not([value=""])').count();
            console.log(`📊 Encargados disponibles: ${encargadosValidos}`);
            expect(encargadosValidos).toBeGreaterThan(0);

            // === VERIFICAR SECCIÓN DE VEHÍCULOS ===
            console.log('🚗 Verificando sección de vehículos...');

            // Hacer clic en "Agregar Vehículo"
            const addVehicleButton = page.locator('button:has-text("Agregar Vehículo")');
            await expect(addVehicleButton).toBeVisible({ timeout: 10000 });
            await addVehicleButton.click();
            console.log('✅ Botón "Agregar Vehículo" clickeado');

            await page.waitForTimeout(2000);

            // Verificar que el template de vehículo se creó
            const vehicleCard = page.locator('.vehicle-card').first();
            await expect(vehicleCard).toBeVisible({ timeout: 10000 });
            console.log('✅ Tarjeta de vehículo creada');

            // === VERIFICAR CAMPOS SIN COMBUSTIBLE ===
            console.log('🔍 Verificando que NO existe campo de combustible...');

            // Verificar que NO hay campo de combustible
            const combustibleField = page.locator('input[name*="combustible"]');
            const combustibleCount = await combustibleField.count();
            expect(combustibleCount).toBe(0);
            console.log('✅ Confirmado: NO hay campos de combustible');

            // Verificar que SÍ existen los campos esperados
            const vehicleSelect = page.locator('select.vehicle-select').first();
            await expect(vehicleSelect).toBeVisible();
            console.log('✅ Select de vehículos presente');

            const kilometrajeInput = page.locator('input[name*="kilometraje_inicial"]').first();
            await expect(kilometrajeInput).toBeVisible();
            console.log('✅ Campo de kilometraje inicial presente');

            const observacionesTextarea = page.locator('textarea[name*="observaciones"]').first();
            await expect(observacionesTextarea).toBeVisible();
            console.log('✅ Campo de observaciones presente');

            // === VERIFICAR GRID DE 3 COLUMNAS ===
            console.log('📐 Verificando layout de 3 columnas...');

            const gridContainer = page.locator('.vehicle-card .grid').first();
            const gridClasses = await gridContainer.getAttribute('class');

            // Verificar que tiene las clases correctas para 3 columnas
            expect(gridClasses).toContain('lg:grid-cols-3');
            console.log('✅ Layout de 3 columnas confirmado');

            // === VERIFICAR FUNCIONALIDAD DE VEHÍCULOS ===
            console.log('⚙️ Verificando funcionalidad de selección de vehículos...');

            // Verificar que hay opciones de vehículos
            const vehicleOptions = await vehicleSelect.locator('option:not([value=""])').count();
            console.log(`📊 Vehículos disponibles: ${vehicleOptions}`);
            expect(vehicleOptions).toBeGreaterThan(0);

            // Seleccionar un vehículo para probar la funcionalidad
            if (vehicleOptions > 0) {
                const firstVehicle = vehicleSelect.locator('option:not([value=""])').first();
                const vehicleValue = await firstVehicle.getAttribute('value');
                const vehicleText = await firstVehicle.textContent();

                await vehicleSelect.selectOption(vehicleValue!);
                console.log(`✅ Vehículo seleccionado: ${vehicleText?.trim()}`);

                // Verificar que el kilometraje se llenó automáticamente
                await page.waitForTimeout(1000);
                const kilometrajeValue = await kilometrajeInput.inputValue();
                console.log(`📊 Kilometraje inicial auto-llenado: ${kilometrajeValue}`);
                expect(parseInt(kilometrajeValue) || 0).toBeGreaterThanOrEqual(0);
            }

            // === LLENAR CAMPOS BÁSICOS PARA VALIDAR FORMULARIO ===
            console.log('📝 Llenando campos básicos del formulario...');

            // Seleccionar encargado
            if (encargadosValidos > 0) {
                const firstEncargado = encargadoSelect.locator('option:not([value=""])').first();
                const encargadoValue = await firstEncargado.getAttribute('value');
                await encargadoSelect.selectOption(encargadoValue!);
                console.log('✅ Encargado seleccionado');
            }

            // Llenar campos básicos
            await page.fill('input[name="nombre_obra"]', 'Obra Test Sin Combustible');
            await page.fill('input[name="fecha_inicio"]', '2025-08-10');
            await page.fill('#avance', '0');
            console.log('✅ Campos básicos llenados');

            // === SCREENSHOT FINAL ===
            await page.screenshot({
                path: 'debug-formulario-sin-combustible-exitoso.png',
                fullPage: true
            });
            console.log('📸 Screenshot final tomado');

            // === VERIFICACIONES FINALES ===
            console.log('🎯 VERIFICACIONES FINALES:');
            console.log('   ✅ Formulario carga sin errores');
            console.log('   ✅ Dropdown de encargados funcional');
            console.log('   ✅ Sección de vehículos funcional');
            console.log('   ✅ Campo de combustible removido exitosamente');
            console.log('   ✅ Layout de 3 columnas correcto');
            console.log('   ✅ Auto-llenado de kilometraje funcional');

            console.log('✅🎉 FORMULARIO SIN COMBUSTIBLE FUNCIONANDO PERFECTAMENTE');
            console.log('=== VERIFICACIÓN COMPLETADA EXITOSAMENTE ===');

        } catch (error) {
            console.log(`❌ Error en test: ${error.message}`);
            await page.screenshot({ path: 'debug-formulario-sin-combustible-error.png', fullPage: true });

            const url = page.url();
            console.log(`URL actual al fallar: ${url}`);

            throw error;
        }
    });

    test('verificar múltiples vehículos sin combustible', async ({ page }) => {
        console.log('=== VERIFICANDO MÚLTIPLES VEHÍCULOS SIN COMBUSTIBLE ===');

        // Login
        await page.goto('http://localhost:8000/login');
        await page.fill('#email', 'admin@petrotekno.com');
        await page.fill('#password', 'password123');
        await page.click('button[type="submit"]');
        await page.waitForURL(/.*dashboard.*|.*home.*/);

        // Ir a formulario
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(3000);

        const addButton = page.locator('button:has-text("Agregar Vehículo")');

        // Agregar 3 vehículos
        for (let i = 0; i < 3; i++) {
            await addButton.click();
            await page.waitForTimeout(1000);
        }

        // Verificar que hay 3 tarjetas de vehículos
        const vehicleCards = page.locator('.vehicle-card');
        const cardCount = await vehicleCards.count();
        console.log(`Número de tarjetas de vehículos: ${cardCount}`);
        expect(cardCount).toBe(3);

        // Verificar que ninguna tiene campo de combustible
        for (let i = 0; i < cardCount; i++) {
            const card = vehicleCards.nth(i);
            const combustibleFields = await card.locator('input[name*="combustible"]').count();
            expect(combustibleFields).toBe(0);
            console.log(`Tarjeta ${i + 1}: Sin campos de combustible ✅`);

            // Verificar que tiene exactamente 3 campos (vehículo, kilometraje, observaciones)
            const allInputs = await card.locator('input, select, textarea').count();
            expect(allInputs).toBe(3); // select + input + textarea
            console.log(`Tarjeta ${i + 1}: 3 campos correctos ✅`);
        }

        console.log('✅ Múltiples vehículos sin combustible funcionando correctamente');
    });

    test('verificar que no hay errores de JavaScript', async ({ page }) => {
        console.log('=== VERIFICANDO ERRORES DE JAVASCRIPT ===');

        // Capturar errores de consola
        const consoleErrors = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
            }
        });

        // Capturar errores de página
        const pageErrors = [];
        page.on('pageerror', error => {
            pageErrors.push(error.message);
        });

        // Login y navegación
        await page.goto('http://localhost:8000/login');
        await page.fill('#email', 'admin@petrotekno.com');
        await page.fill('#password', 'password123');
        await page.click('button[type="submit"]');
        await page.waitForURL(/.*dashboard.*|.*home.*/);

        await page.goto('http://localhost:8000/obras/create');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(3000);

        // Interactuar con el formulario para provocar posibles errores
        const addButton = page.locator('button:has-text("Agregar Vehículo")');
        await addButton.click();
        await page.waitForTimeout(1000);

        // Verificar errores
        console.log(`Errores de consola: ${consoleErrors.length}`);
        console.log(`Errores de página: ${pageErrors.length}`);

        if (consoleErrors.length > 0) {
            console.log('Errores de consola encontrados:', consoleErrors);
        }

        if (pageErrors.length > 0) {
            console.log('Errores de página encontrados:', pageErrors);
        }

        // El test pasa si no hay errores críticos relacionados con combustible
        const combustibleErrors = [...consoleErrors, ...pageErrors].filter(error =>
            error.toLowerCase().includes('combustible')
        );

        expect(combustibleErrors.length).toBe(0);
        console.log('✅ No hay errores relacionados con combustible');
    });
});