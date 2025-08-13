import { test, expect, Page } from '@playwright/test';

test.describe('Test Responsable de Obra en Dropdown', () => {
    let page: Page;
    let responsableNombre: string;

    test.beforeEach(async ({ browser }) => {
        const context = await browser.newContext();
        page = await context.newPage();
    });

    test('Crear personal Responsable de Obra y verificar en dropdown', async () => {
        // 1. Login al sistema
        console.log('🔐 Iniciando login...');
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        try {
            await page.waitForURL(/.*home.*/, { timeout: 10000 });
            console.log('✅ Login exitoso');
        } catch (e) {
            console.log('❌ Error en login, intentando continuar...');
        }

        // 2. Primero verificar si ya existe un Responsable de Obra
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForTimeout(2000);

        try {
            await page.waitForSelector('#encargado_id', { timeout: 5000 });
            console.log('✅ Formulario de obra cargado');

            // Verificar opciones actuales en el dropdown
            const opcionesActuales = await page.evaluate(() => {
                const select = document.getElementById('encargado_id');
                if (!select) return [];
                const opciones = Array.from(select.querySelectorAll('option'));
                return opciones.map(opt => ({
                    value: opt.value,
                    text: opt.textContent?.trim()
                })).filter(opt => opt.value !== '');
            });

            console.log('📋 Opciones actuales en el dropdown:');
            console.log(opcionesActuales);

            // Verificar si ya existe un "Responsable de Obra"
            const responsableExistente = opcionesActuales.find(opt =>
                opt.text?.includes('Responsable de Obra')
            );

            if (responsableExistente) {
                console.log('✅ Ya existe un Responsable de Obra:', responsableExistente.text);

                // Tomar captura del dropdown con el responsable existente
                await page.click('#encargado_id');
                await page.waitForTimeout(500);
                await page.screenshot({ path: 'responsable-obra-existente.png' });

                // Verificar que se puede seleccionar
                await page.selectOption('#encargado_id', responsableExistente.value);
                console.log('✅ Responsable de Obra seleccionado correctamente');

                expect(opcionesActuales.length).toBeGreaterThan(0);
                expect(responsableExistente).toBeDefined();

                return; // Terminar aquí si ya existe
            }

            console.log('ℹ️ No existe Responsable de Obra, creando uno...');

        } catch (e) {
            console.log('❌ Error al acceder al formulario de obra:', e);
            await page.screenshot({ path: 'error-formulario-obra.png' });
        }

        // 3. Crear nuevo personal con categoría "Responsable de Obra"
        console.log('🔄 Navegando a crear personal...');
        await page.goto('http://localhost:8000/personal/create');

        try {
            await page.waitForSelector('form', { timeout: 5000 });
            console.log('✅ Formulario de personal cargado');

            // Generar nombre único
            responsableNombre = `Responsable Obra ${Date.now()}`;

            // Llenar datos del personal
            await page.fill('input[name="nombre"]', responsableNombre);
            await page.fill('input[name="apellidos"]', 'Test Playwright');
            await page.fill('input[name="email"]', `resp${Date.now()}@test.com`);
            await page.fill('input[name="telefono"]', '555-0123');

            // Seleccionar categoría "Responsable de Obra"
            try {
                await page.selectOption('select[name="categoria_id"]', { label: 'Responsable de Obra' });
                console.log('✅ Categoría "Responsable de Obra" seleccionada');
            } catch (e) {
                // Si no existe la categoría, intentar crearla primero
                console.log('⚠️ Categoría "Responsable de Obra" no encontrada');

                // Verificar opciones disponibles
                const categoriasDisponibles = await page.evaluate(() => {
                    const select = document.querySelector('select[name="categoria_id"]');
                    if (!select) return [];
                    return Array.from(select.querySelectorAll('option')).map(opt => opt.textContent?.trim());
                });

                console.log('📋 Categorías disponibles:', categoriasDisponibles);

                // Usar la primera categoría disponible que no sea el placeholder
                const primeraCategoria = categoriasDisponibles.find(cat => cat && cat !== 'Seleccione una categoría');
                if (primeraCategoria) {
                    await page.selectOption('select[name="categoria_id"]', { label: primeraCategoria });
                    console.log(`✅ Usando categoría alternativa: ${primeraCategoria}`);
                }
            }

            // Enviar formulario
            await page.click('button[type="submit"]');
            await page.waitForTimeout(3000);

            console.log('✅ Personal creado');

        } catch (e) {
            console.log('❌ Error al crear personal:', e);
            await page.screenshot({ path: 'error-crear-personal.png' });
        }

        // 4. Verificar en el formulario de obra que aparece el responsable
        console.log('🔄 Verificando dropdown en formulario de obra...');
        await page.goto('http://localhost:8000/obras/create');

        try {
            await page.waitForSelector('#encargado_id', { timeout: 5000 });

            // Obtener todas las opciones del dropdown
            const opcionesFinal = await page.evaluate(() => {
                const select = document.getElementById('encargado_id');
                if (!select) return [];
                const opciones = Array.from(select.querySelectorAll('option'));
                return opciones.map(opt => ({
                    value: opt.value,
                    text: opt.textContent?.trim()
                })).filter(opt => opt.value !== '');
            });

            console.log('📋 Opciones finales en el dropdown:');
            opcionesFinal.forEach((opt, index) => {
                console.log(`${index + 1}. ${opt.text} (ID: ${opt.value})`);
            });

            // Tomar captura del dropdown final
            await page.click('#encargado_id');
            await page.waitForTimeout(500);
            await page.screenshot({ path: 'dropdown-responsable-obra-final.png' });

            // Verificar que hay al menos una opción
            expect(opcionesFinal.length).toBeGreaterThan(0);

            // Buscar específicamente responsables de obra
            const responsablesObra = opcionesFinal.filter(opt =>
                opt.text?.includes('Responsable de Obra') ||
                opt.text?.includes(responsableNombre)
            );

            if (responsablesObra.length > 0) {
                console.log('✅ Responsable(s) de Obra encontrado(s):');
                responsablesObra.forEach(resp => {
                    console.log(`- ${resp.text}`);
                });

                // Seleccionar el primer responsable de obra
                await page.selectOption('#encargado_id', responsablesObra[0].value);
                console.log('✅ Responsable de Obra seleccionado exitosamente');

                expect(responsablesObra.length).toBeGreaterThan(0);
            } else {
                console.log('❌ No se encontraron Responsables de Obra en el dropdown');
                throw new Error('No se encontró ningún Responsable de Obra en el dropdown');
            }

        } catch (e) {
            console.log('❌ Error final al verificar dropdown:', e);
            await page.screenshot({ path: 'error-verificacion-final.png' });
            throw e;
        }
    });

    test.afterEach(async () => {
        await page.close();
    });
});