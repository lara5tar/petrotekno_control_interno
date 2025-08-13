import { test, expect, Page } from '@playwright/test';

test.describe('Test Encargados en Dropdown de Obras', () => {
    let page: Page;
    let personalNombre: string;
    let personalId: string;

    test.beforeEach(async ({ browser }) => {
        // Crear un nuevo contexto para cada prueba
        const context = await browser.newContext();
        page = await context.newPage();
    });

    test('Crear personal con categoría Responsable y verificar en dropdown', async () => {
        // 1. Login al sistema
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Esperar redirección exitosa
        await page.waitForURL(/.*home.*/);
        console.log('✅ Login exitoso');

        // 2. Navegar a la sección de personal
        await page.goto('http://localhost:8000/personal');
        await page.waitForSelector('h2:has-text("Listado de Personal")');
        console.log('✅ Navegación a listado de personal exitosa');

        // 3. Ir a crear nuevo personal
        await page.click('a:has-text("Agregar Personal")');
        await page.waitForSelector('#createPersonalForm');
        console.log('✅ Formulario de creación de personal cargado');

        // 4. Generar datos aleatorios para el personal
        personalNombre = `Responsable Test ${Date.now()}`;
        const apellidos = 'de Prueba Playwright';
        const email = `resp${Date.now()}@test.com`;

        // 5. Completar formulario de personal
        await page.fill('input[name="nombre"]', personalNombre);
        await page.fill('input[name="apellidos"]', apellidos);
        await page.fill('input[name="email"]', email);
        await page.fill('input[name="telefono"]', '555-123-4567');

        // Seleccionar categoría "Responsable" o "Encargado" (según la que exista)
        try {
            await page.selectOption('select[name="categoria"]', { label: 'Responsable' });
        } catch (e) {
            try {
                await page.selectOption('select[name="categoria"]', { label: 'Encargado' });
                console.log('🔄 Usando categoría "Encargado" en lugar de "Responsable"');
            } catch (e2) {
                console.log('❌ No se encontró categoría Responsable ni Encargado, usando la primera opción');
                await page.click('select[name="categoria"]');
                await page.keyboard.press('ArrowDown');
                await page.keyboard.press('Enter');
            }
        }

        // 6. Enviar formulario
        await page.click('button:has-text("Guardar")');

        // 7. Verificar redirección exitosa
        try {
            await page.waitForURL('**/personal', { timeout: 10000 });
            console.log('✅ Personal creado exitosamente');

            // Intentar obtener el ID del personal recién creado
            try {
                const row = await page.locator(`tr:has-text("${personalNombre}")`).first();
                const editLink = await row.locator('a:has-text("Editar")').first();
                const href = await editLink.getAttribute('href');

                // Extraer ID del href
                if (href) {
                    const match = href.match(/\/personal\/(\d+)\/edit/);
                    if (match && match[1]) {
                        personalId = match[1];
                        console.log(`✅ ID del personal creado: ${personalId}`);
                    }
                }
            } catch (e) {
                console.log('⚠️ No se pudo obtener el ID del personal, pero continuamos');
            }

        } catch (e) {
            console.log('⚠️ No se detectó redirección a listado de personal, verificando mensaje de éxito');
            const successMessage = await page.locator('.bg-green-100').first();
            if (await successMessage.isVisible()) {
                console.log('✅ Personal creado con éxito (detectado por mensaje)');
            } else {
                console.log('❌ Posible error al crear personal');
                await page.screenshot({ path: 'error-crear-personal.png', fullPage: true });
            }
        }

        // 8. Navegar a la creación de obra para verificar el dropdown
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForSelector('#encargado_id');
        console.log('✅ Formulario de creación de obra cargado');

        // 9. Verificar que el nuevo responsable aparece en el dropdown
        const dropdownContent = await page.evaluate(() => {
            const select = document.getElementById('encargado_id');
            return select ? select.innerHTML : '';
        });

        console.log('📋 Contenido del dropdown:');
        console.log(dropdownContent);

        // 10. Buscar el nuevo personal en el dropdown
        const personalEnDropdown = await page.locator(`#encargado_id option:has-text("${personalNombre}")`);
        const existe = await personalEnDropdown.count() > 0;

        if (existe) {
            console.log('✅ Encargado creado encontrado en el dropdown');

            // Tomar captura del dropdown abierto para verificación visual
            await page.click('#encargado_id');
            await page.waitForTimeout(500); // Esperar que el dropdown se abra completamente
            await page.screenshot({ path: 'dropdown-encargados.png' });

            // Verificar mediante assertion
            expect(await personalEnDropdown.count()).toBeGreaterThan(0);
        } else {
            console.log('❌ No se encontró al encargado en el dropdown');

            // Tomar screenshot para diagnóstico
            await page.screenshot({ path: 'dropdown-error.png', fullPage: true });

            // Mostrar las opciones disponibles
            const opciones = await page.locator('#encargado_id option').all();
            console.log(`📋 Total opciones disponibles: ${opciones.length}`);

            for (const opcion of opciones) {
                console.log(`- ${await opcion.textContent()}`);
            }

            throw new Error('El personal creado no aparece en el dropdown de encargados');
        }
    });

    test.afterEach(async () => {
        // Cerrar la página después de cada prueba
        await page.close();
    });
});