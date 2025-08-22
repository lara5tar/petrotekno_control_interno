import { chromium } from 'playwright';

async function inspectFormularioEditarPersonal() {
    console.log('🔍 INSPECCIÓN: Analizando formulario editar personal');

    const browser = await chromium.launch({
        headless: false,
        slowMo: 1000
    });

    try {
        const context = await browser.newContext();
        const page = await context.newPage();

        console.log('📱 Navegando al login...');
        await page.goto('http://127.0.0.1:8080/login');
        await page.waitForLoadState('networkidle');

        console.log('🔐 Realizando login...');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        console.log('👥 Navegando a lista de personal...');
        await page.goto('http://127.0.0.1:8080/personal');
        await page.waitForLoadState('networkidle');

        console.log('✏️ Haciendo clic en primer enlace de editar...');
        const editLinks = await page.locator('a[href*="/edit"]').all();
        if (editLinks.length > 0) {
            await editLinks[0].click();
            await page.waitForLoadState('networkidle');
        } else {
            throw new Error('No se encontraron enlaces de edición');
        }

        console.log('🔍 INSPECCIÓN COMPLETA DEL FORMULARIO:');
        console.log('📍 URL actual:', page.url());

        // Capturar screenshot inicial
        await page.screenshot({
            path: 'inspeccion-formulario-editar.png',
            fullPage: true
        });
        console.log('📸 Screenshot inicial guardado: inspeccion-formulario-editar.png');

        console.log('\n📝 ANÁLISIS DE INPUTS:');
        const allInputs = await page.locator('input').all();
        console.log(`Total de inputs encontrados: ${allInputs.length}`);

        for (let i = 0; i < allInputs.length; i++) {
            const input = allInputs[i];
            const type = await input.getAttribute('type');
            const name = await input.getAttribute('name');
            const id = await input.getAttribute('id');
            const placeholder = await input.getAttribute('placeholder');
            const value = await input.inputValue();

            console.log(`Input ${i + 1}:`);
            console.log(`  - Tipo: ${type}`);
            console.log(`  - Name: ${name}`);
            console.log(`  - ID: ${id}`);
            console.log(`  - Placeholder: ${placeholder}`);
            console.log(`  - Valor: ${value}`);
            console.log('');
        }

        console.log('\n📋 ANÁLISIS DE SELECTS:');
        const allSelects = await page.locator('select').all();
        console.log(`Total de selects encontrados: ${allSelects.length}`);

        for (let i = 0; i < allSelects.length; i++) {
            const select = allSelects[i];
            const name = await select.getAttribute('name');
            const id = await select.getAttribute('id');
            const options = await select.locator('option').allTextContents();

            console.log(`Select ${i + 1}:`);
            console.log(`  - Name: ${name}`);
            console.log(`  - ID: ${id}`);
            console.log(`  - Opciones: ${options.map(o => o.trim()).join(', ')}`);
            console.log('');
        }

        console.log('\n☑️ ANÁLISIS DE CHECKBOXES:');
        const allCheckboxes = await page.locator('input[type="checkbox"]').all();
        console.log(`Total de checkboxes encontrados: ${allCheckboxes.length}`);

        for (let i = 0; i < allCheckboxes.length; i++) {
            const checkbox = allCheckboxes[i];
            const name = await checkbox.getAttribute('name');
            const id = await checkbox.getAttribute('id');
            const xModel = await checkbox.getAttribute('x-model');
            const checked = await checkbox.isChecked();

            console.log(`Checkbox ${i + 1}:`);
            console.log(`  - Name: ${name}`);
            console.log(`  - ID: ${id}`);
            console.log(`  - x-model: ${xModel}`);
            console.log(`  - Checked: ${checked}`);
            console.log('');
        }

        console.log('\n🔲 ANÁLISIS DE BUTTONS:');
        const allButtons = await page.locator('button').all();
        console.log(`Total de botones encontrados: ${allButtons.length}`);

        for (let i = 0; i < allButtons.length; i++) {
            const button = allButtons[i];
            const type = await button.getAttribute('type');
            const text = await button.textContent();
            const classes = await button.getAttribute('class');

            console.log(`Button ${i + 1}:`);
            console.log(`  - Tipo: ${type}`);
            console.log(`  - Texto: ${text?.trim()}`);
            console.log(`  - Classes: ${classes}`);
            console.log('');
        }

        // Probar activar checkbox si existe
        if (allCheckboxes.length > 0) {
            console.log('\n🧪 PROBANDO ACTIVACIÓN DE CHECKBOX:');
            const checkbox = allCheckboxes[0];
            const isChecked = await checkbox.isChecked();

            if (!isChecked) {
                console.log('Activando primer checkbox...');
                await checkbox.check();
                await page.waitForTimeout(2000);

                // Volver a analizar selects después de activar checkbox
                console.log('\n📋 SELECTS DESPUÉS DE ACTIVAR CHECKBOX:');
                const newSelects = await page.locator('select').all();
                console.log(`Total de selects después del checkbox: ${newSelects.length}`);

                for (let i = 0; i < newSelects.length; i++) {
                    const select = newSelects[i];
                    const name = await select.getAttribute('name');
                    const isVisible = await select.isVisible();
                    const options = await select.locator('option').allTextContents();

                    console.log(`Select ${i + 1} (después checkbox):`);
                    console.log(`  - Name: ${name}`);
                    console.log(`  - Visible: ${isVisible}`);
                    console.log(`  - Opciones: ${options.map(o => o.trim()).join(', ')}`);
                    console.log('');
                }

                // Capturar screenshot después del checkbox
                await page.screenshot({
                    path: 'inspeccion-formulario-con-checkbox.png',
                    fullPage: true
                });
                console.log('📸 Screenshot con checkbox guardado: inspeccion-formulario-con-checkbox.png');
            }
        }

        console.log('\n🏆 INSPECCIÓN COMPLETADA');

    } catch (error) {
        console.error('❌ ERROR durante la inspección:', error.message);

        try {
            await page.screenshot({
                path: 'error-inspeccion-formulario.png',
                fullPage: true
            });
            console.log('📸 Screenshot de error guardado: error-inspeccion-formulario.png');
        } catch (screenshotError) {
            console.error('Error al capturar screenshot:', screenshotError.message);
        }

        throw error;
    } finally {
        await browser.close();
    }
}

// Ejecutar la inspección
inspectFormularioEditarPersonal()
    .then(() => {
        console.log('\n🎊 ¡INSPECCIÓN COMPLETADA!');
        process.exit(0);
    })
    .catch((error) => {
        console.error('\n💥 INSPECCIÓN FALLIDA:', error.message);
        process.exit(1);
    });
