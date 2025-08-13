import { test, expect } from '@playwright/test';

test('Verificar Responsable de Obra en dropdown - Test Simple', async ({ page }) => {
    console.log('🔐 Iniciando test simple del dropdown...');

    // Intentar acceder directamente al formulario
    try {
        await page.goto('http://localhost:8000/obras/create');
        await page.waitForTimeout(3000);

        // Verificar si existe el elemento dropdown
        const dropdownExists = await page.locator('#encargado_id').count() > 0;

        if (dropdownExists) {
            console.log('✅ Dropdown encontrado');

            // Obtener opciones del dropdown
            const opciones = await page.evaluate(() => {
                const select = document.getElementById('encargado_id');
                if (!select) return [];

                const options = Array.from(select.querySelectorAll('option'));
                return options.map(opt => ({
                    value: opt.value,
                    text: opt.textContent?.trim()
                })).filter(opt => opt.value !== '');
            });

            console.log('📋 Opciones encontradas en el dropdown:');
            opciones.forEach((opt, index) => {
                console.log(`${index + 1}. ${opt.text} (ID: ${opt.value})`);
            });

            // Buscar responsables de obra
            const responsablesObra = opciones.filter(opt =>
                opt.text?.includes('Responsable de Obra')
            );

            console.log(`✅ Responsables de Obra encontrados: ${responsablesObra.length}`);
            responsablesObra.forEach(resp => {
                console.log(`- ${resp.text}`);
            });

            expect(responsablesObra.length).toBeGreaterThan(0);

        } else {
            console.log('❌ Dropdown no encontrado');
            await page.screenshot({ path: 'dropdown-no-encontrado.png' });
        }

    } catch (error) {
        console.log('❌ Error en el test:', error);
        await page.screenshot({ path: 'error-test-simple.png' });

        // Intentar obtener información de la página actual
        const url = page.url();
        const title = await page.title();
        console.log(`URL actual: ${url}`);
        console.log(`Título: ${title}`);
    }
});