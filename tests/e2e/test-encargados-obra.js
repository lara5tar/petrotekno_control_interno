// @ts-check
const { test, expect } = require('@playwright/test');

test('verificar dropdown de responsables de obra', async ({ page }) => {
    // Navegar a la página de inicio
    await page.goto('/');

    // Login (ajusta según tu formulario de login)
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');

    // Navegar a la página de creación de obra
    await page.goto('/obras/create');

    // Esperar a que el formulario cargue
    await page.waitForSelector('#encargado_id');

    // Verificar que exista el selector de responsables
    const responsableSelector = await page.$('#encargado_id');
    expect(responsableSelector).not.toBeNull();

    // Hacer clic en el dropdown para mostrar las opciones
    await page.click('#encargado_id');

    // Esperar a que se muestren las opciones del dropdown
    await page.waitForTimeout(500);

    // Capturar una imagen del dropdown abierto para verificación visual
    await page.screenshot({ path: 'debug-encargados-dropdown-obra.png', fullPage: true });

    // Verificar que existan opciones en el dropdown
    const options = await page.$$('#encargado_id option');

    // Debe haber al menos una opción más que la opción vacía "Seleccione un responsable"
    expect(options.length).toBeGreaterThan(1);

    // Verificar que las opciones tengan el texto "Encargado" en la descripción (categoría)
    const optionTexts = await Promise.all(
        options.slice(1).map(option => page.evaluate(el => el.textContent, option))
    );

    console.log('Opciones disponibles:', optionTexts);

    // Verificar que al menos una opción contenga la palabra "Encargado"
    const hasEncargado = optionTexts.some(text => text.includes('Encargado'));
    expect(hasEncargado).toBeTruthy();

    // Seleccionar la primera opción válida
    if (options.length > 1) {
        await page.selectOption('#encargado_id', { index: 1 });

        // Capturar otra imagen para verificar la selección
        await page.screenshot({ path: 'debug-encargado-seleccionado-obra.png', fullPage: true });
    }
});