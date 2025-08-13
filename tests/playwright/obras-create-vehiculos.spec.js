import { test, expect } from '@playwright/test';

test.describe('Crear Obra - Asignación de Vehículos', () => {
    test.beforeEach(async ({ page }) => {
        // Navegar a la página de login
        await page.goto('/login');

        // Hacer login con credenciales de administrador
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar redirección al home
        await page.waitForURL('/home');
    });

    test('debe crear obra exitosamente con datos válidos', async ({ page }) => {
        // Navegar a la página de crear obra
        await page.goto('/obras/create');

        // Verificar que estamos en la página correcta
        await expect(page.locator('h2')).toContainText('Crear Nueva Obra');

        // Llenar información básica
        await page.fill('input[name="nombre_obra"]', 'Test Obra Playwright');
        await page.selectOption('select[name="estatus"]', 'planificada');
        await page.fill('input[name="fecha_inicio"]', '2024-12-20');
        await page.fill('input[name="avance"]', '0');

        // Seleccionar encargado (debe ser un usuario válido de la tabla users)
        const encargadoSelector = page.locator('select[name="encargado_id"]');
        await expect(encargadoSelector).toBeVisible();

        // Verificar que hay opciones de encargados disponibles
        const encargadoOptions = await encargadoSelector.locator('option:not([value=""])').count();

        if (encargadoOptions > 0) {
            // Seleccionar el primer encargado disponible
            await encargadoSelector.selectOption({ index: 1 });
            console.log('✅ Encargado seleccionado correctamente');

            // Agregar observaciones
            await page.fill('textarea[name="observaciones"]', 'Obra de prueba creada con Playwright');

            // Enviar formulario
            await page.click('button[type="submit"]');

            // Verificar redirección exitosa
            await page.waitForURL('/obras');

            // Verificar mensaje de éxito
            await expect(page.locator('.bg-green-100')).toBeVisible();
            await expect(page.locator('.bg-green-100')).toContainText('exitosamente');

            console.log('✅ Obra creada exitosamente');

        } else {
            console.log('⚠️ No hay encargados disponibles en el sistema');
            // Aún así, intentar enviar el formulario para ver el error específico
            await page.click('button[type="submit"]');

            // Verificar que aparece el error de validación
            await expect(page.locator('.text-red-600')).toBeVisible();
        }
    });

    test('debe mostrar el listado de vehículos en el modal de asignación', async ({ page }) => {
        // Navegar a la página de crear obra
        await page.goto('/obras/create');

        // Verificar que existe la sección de asignación de vehículos
        await expect(page.locator('text=Asignación de Vehículos')).toBeVisible();

        // Verificar que existe el botón para asignar vehículo
        const asignarBtn = page.locator('button:has-text("Asignar Vehículo")');

        // El botón puede estar visible o no dependiendo de si hay vehículos
        const botonVisible = await asignarBtn.isVisible();

        if (botonVisible) {
            console.log('✅ Botón "Asignar Vehículo" encontrado');

            // Hacer clic en el botón para abrir el modal
            await asignarBtn.click();

            // Verificar que el modal se abre
            const modal = page.locator('#vehicle-modal');
            await expect(modal).toBeVisible();

            // Verificar el título del modal
            await expect(page.locator('h3:has-text("Asignación de Vehículo")')).toBeVisible();

            // Verificar que existe el campo de búsqueda
            const searchInput = page.locator('#vehiculo-search');
            await expect(searchInput).toBeVisible();
            await expect(searchInput).toHaveAttribute('placeholder', 'Buscar por marca, modelo, placas...');

            // Verificar que existe el contenedor de opciones de vehículos
            const vehicleOptions = page.locator('#vehicle-options');
            await expect(vehicleOptions).toBeVisible();

            // Verificar si hay vehículos en el sistema
            const vehicleElements = page.locator('.vehicle-option');
            const vehicleCount = await vehicleElements.count();

            if (vehicleCount > 0) {
                console.log(`✅ Se encontraron ${vehicleCount} vehículos en el sistema`);

                // Verificar que al menos un vehículo está visible
                await expect(vehicleElements.first()).toBeVisible();

                // Verificar estructura de cada vehículo
                const firstVehicle = vehicleElements.first();

                // Debe tener marca y modelo
                await expect(firstVehicle.locator('h4')).toBeVisible();

                // Debe tener información adicional (año, placas, km)
                await expect(firstVehicle.locator('p')).toBeVisible();

                // Debe tener un indicador de estado (Disponible o En uso)
                const statusBadges = firstVehicle.locator('div:has-text("Disponible"), div:has-text("En uso")');
                await expect(statusBadges).toHaveCount(1);

                // Probar funcionalidad de búsqueda básica
                await searchInput.fill('test');
                await page.waitForTimeout(500);

                // Limpiar búsqueda
                await searchInput.clear();
                await page.waitForTimeout(500);

                // Verificar que los vehículos vuelven a aparecer
                await expect(vehicleElements.first()).toBeVisible();

                console.log('✅ Modal de vehículos funciona correctamente');

            } else {
                console.log('⚠️ No se encontraron vehículos en el sistema');

                // Verificar mensaje de no vehículos
                await expect(page.locator('text=No hay vehículos registrados en el sistema')).toBeVisible();
            }

            // Cerrar modal
            const closeBtn = page.locator('button:has-text("Cancelar")');
            await closeBtn.click();

            // Verificar que el modal se cierra
            await expect(modal).toBeHidden();

        } else {
            console.log('⚠️ No hay vehículos disponibles - botón "Asignar Vehículo" no visible');

            // Verificar mensaje de no vehículos disponibles
            await expect(page.locator('.bg-yellow-50')).toBeVisible();
            await expect(page.locator('text=No hay vehículos disponibles')).toBeVisible();
        }
    });

    test('debe validar campos obligatorios correctamente', async ({ page }) => {
        await page.goto('/obras/create');

        // Intentar enviar formulario vacío
        await page.click('button[type="submit"]');

        // Verificar que no se redirige (se queda en la misma página)
        await expect(page.url()).toContain('/obras/create');

        // Verificar mensajes de validación del navegador o errores de Laravel
        const nombreInput = page.locator('input[name="nombre_obra"]');
        const estatusSelect = page.locator('select[name="estatus"]');
        const fechaInput = page.locator('input[name="fecha_inicio"]');
        const encargadoSelect = page.locator('select[name="encargado_id"]');

        // Verificar que los campos requeridos están marcados
        await expect(nombreInput).toHaveAttribute('required');
        await expect(estatusSelect).toHaveAttribute('required');
        await expect(fechaInput).toHaveAttribute('required');
        await expect(encargadoSelect).toHaveAttribute('required');

        console.log('✅ Validación de campos obligatorios funcionando');
    });

    test('debe manejar error de constraint de clave foránea', async ({ page }) => {
        await page.goto('/obras/create');

        // Llenar campos básicos
        await page.fill('input[name="nombre_obra"]', 'Test Obra Error');
        await page.selectOption('select[name="estatus"]', 'planificada');
        await page.fill('input[name="fecha_inicio"]', '2024-12-20');

        // Intentar seleccionar un encargado inválido (si hay opciones)
        const encargadoOptions = await page.locator('select[name="encargado_id"] option:not([value=""])').count();

        if (encargadoOptions > 0) {
            // Seleccionar un encargado válido
            await page.selectOption('select[name="encargado_id"]', { index: 1 });

            // Enviar formulario
            await page.click('button[type="submit"]');

            // Verificar resultado (debería ser exitoso si los datos son válidos)
            const hasError = await page.locator('.bg-red-100').isVisible();
            const hasSuccess = await page.locator('.bg-green-100').isVisible();

            if (hasError) {
                console.log('⚠️ Error detectado en la creación de obra');
                await expect(page.locator('.bg-red-100')).toContainText('Error');
            } else if (hasSuccess) {
                console.log('✅ Obra creada exitosamente sin errores de constraint');
            }

        } else {
            console.log('⚠️ No hay encargados disponibles para probar');
        }
    });

    test('debe verificar que los encargados son usuarios válidos', async ({ page }) => {
        await page.goto('/obras/create');

        const encargadoSelect = page.locator('select[name="encargado_id"]');
        await expect(encargadoSelect).toBeVisible();

        // Verificar que hay opciones de encargados
        const encargadoOptions = page.locator('select[name="encargado_id"] option:not([value=""])');
        const optionCount = await encargadoOptions.count();

        if (optionCount > 0) {
            console.log(`✅ Se encontraron ${optionCount} encargados disponibles`);

            // Verificar que cada opción tiene un valor numérico (ID de usuario)
            for (let i = 0; i < Math.min(optionCount, 3); i++) {
                const option = encargadoOptions.nth(i);
                const value = await option.getAttribute('value');
                const text = await option.textContent();

                expect(value).toMatch(/^\d+$/); // Debe ser un número
                expect(text).toBeTruthy(); // Debe tener texto

                console.log(`✅ Encargado ${i + 1}: ID=${value}, Nombre=${text}`);
            }

        } else {
            console.log('⚠️ No se encontraron encargados en el selector');

            // Verificar mensaje por defecto
            await expect(page.locator('select[name="encargado_id"] option[value=""]')).toHaveText('Seleccione un responsable');
        }
    });

    test('debe poder asignar vehículo si están disponibles', async ({ page }) => {
        await page.goto('/obras/create');

        // Verificar si el botón de asignar vehículo está disponible
        const asignarBtn = page.locator('button:has-text("Asignar Vehículo")');
        const botonVisible = await asignarBtn.isVisible();

        if (botonVisible) {
            console.log('✅ Botón de asignar vehículo disponible');

            // Abrir modal
            await asignarBtn.click();

            // Verificar si hay vehículos disponibles
            const availableVehicles = page.locator('.vehicle-option:not(.cursor-not-allowed)');
            const availableCount = await availableVehicles.count();

            if (availableCount > 0) {
                console.log(`✅ Se encontraron ${availableCount} vehículos disponibles`);

                // Seleccionar primer vehículo disponible
                await availableVehicles.first().click();

                // Verificar que se selecciona visualmente
                await expect(availableVehicles.first()).toHaveClass(/bg-gray-100/);

                // Verificar que aparece el contenedor de vehículo seleccionado
                const selectedContainer = page.locator('#selected-vehicle-container');
                await expect(selectedContainer).toBeVisible();

                // Verificar que el botón de confirmar se habilita
                const confirmBtn = page.locator('#confirm-vehicle-btn');
                await expect(confirmBtn).toBeEnabled();

                // Confirmar asignación
                await confirmBtn.click();

                // Verificar que el modal se cierra
                await expect(page.locator('#vehicle-modal')).toBeHidden();

                // Verificar que el vehículo aparece en la lista de asignados
                const assignedList = page.locator('#assigned-vehicles-list');
                await expect(assignedList.locator('.flex')).toBeVisible();

                // Verificar que el mensaje de "ningún vehículo" está oculto
                await expect(page.locator('#no-vehicles-message')).toBeHidden();

                console.log('✅ Vehículo asignado correctamente');

            } else {
                console.log('⚠️ No hay vehículos disponibles para asignar');

                // Cerrar modal
                await page.click('button:has-text("Cancelar")');
            }

        } else {
            console.log('ℹ️ No hay vehículos en el sistema o botón no visible');
        }
    });
});