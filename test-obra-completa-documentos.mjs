import { chromium } from 'playwright';

async function probarCreacionObraConDocumentos() {
    console.log('🧪 Iniciando prueba de creación de obra con documentos...');

    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // 1. Ir a la página de crear obra
        console.log('📝 Navegando a crear obra...');
        await page.goto('http://127.0.0.1:8003/obras/create', { waitUntil: 'networkidle' });

        // 2. Llenar datos básicos
        console.log('📋 Llenando datos básicos...');
        await page.fill('#nombre_obra', 'Obra de Prueba Documentos ' + Date.now());
        await page.selectOption('#estatus', 'planificada');
        await page.fill('#fecha_inicio', '2024-01-01');
        await page.selectOption('#encargado_id', { index: 1 }); // Seleccionar primer encargado disponible

        // 3. Subir documentos de prueba
        console.log('📄 Subiendo documentos...');

        // Crear archivos de prueba
        const contratoPath = '/home/lara5tar/Escritorio/proyectos-clipsitos/petrotekno_control_interno/test_simple.pdf';
        const fianzaPath = '/home/lara5tar/Escritorio/proyectos-clipsitos/petrotekno_control_interno/test_ine.pdf';
        const actaPath = '/home/lara5tar/Escritorio/proyectos-clipsitos/petrotekno_control_interno/test_simple.pdf';

        // Subir contrato
        await page.setInputFiles('#archivo_contrato', contratoPath);
        await page.waitForTimeout(1000);

        // Verificar feedback visual del contrato
        const contratoFeedback = await page.locator('#file_info_archivo_contrato');
        if (await contratoFeedback.isVisible()) {
            console.log('✅ Feedback visual del contrato funcionando');
        } else {
            console.log('❌ Feedback visual del contrato NO funcionando');
        }

        // Subir fianza
        await page.setInputFiles('#archivo_fianza', fianzaPath);
        await page.waitForTimeout(1000);

        // Verificar feedback visual de la fianza
        const fianzaFeedback = await page.locator('#file_info_archivo_fianza');
        if (await fianzaFeedback.isVisible()) {
            console.log('✅ Feedback visual de la fianza funcionando');
        } else {
            console.log('❌ Feedback visual de la fianza NO funcionando');
        }

        // Subir acta
        await page.setInputFiles('#archivo_acta_entrega_recepcion', actaPath);
        await page.waitForTimeout(1000);

        // Verificar feedback visual del acta
        const actaFeedback = await page.locator('#file_info_archivo_acta_entrega_recepcion');
        if (await actaFeedback.isVisible()) {
            console.log('✅ Feedback visual del acta funcionando');
        } else {
            console.log('❌ Feedback visual del acta NO funcionando');
        }

        // 4. Asignar vehículos (opcional)
        console.log('🚗 Asignando vehículos...');
        try {
            await page.click('#openVehicleModal');
            await page.waitForSelector('#vehicleModal', { state: 'visible', timeout: 3000 });

            // Seleccionar primer vehículo
            const firstCheckbox = await page.locator('#vehicleModal input[type="checkbox"]').first();
            if (await firstCheckbox.isVisible()) {
                await firstCheckbox.check();
                console.log('✅ Vehículo seleccionado');
            }

            // Cerrar modal
            await page.click('#acceptVehicles');
            await page.waitForTimeout(1000);

        } catch (error) {
            console.log('⚠️ No se pudieron asignar vehículos:', error.message);
        }

        // 5. Enviar formulario
        console.log('📤 Enviando formulario...');
        await page.click('button[type="submit"]');

        // 6. Esperar respuesta y verificar
        await page.waitForTimeout(3000);

        const currentUrl = page.url();
        if (currentUrl.includes('/obras/') && !currentUrl.includes('/create')) {
            console.log('✅ Obra creada exitosamente - Redirigido a:', currentUrl);

            // Obtener ID de la obra de la URL
            const obraIdMatch = currentUrl.match(/obras\/(\d+)/);
            if (obraIdMatch) {
                const obraId = obraIdMatch[1];
                console.log(`📊 ID de la obra creada: ${obraId}`);

                // Verificar que se guardaron los documentos en la tabla documentos
                console.log('🔍 Verificando documentos en base de datos...');
                // Esto lo haremos en el siguiente paso con Tinker
            }
        } else {
            console.log('❌ Error en la creación - URL actual:', currentUrl);

            // Verificar si hay mensajes de error
            const errorMessages = await page.locator('.alert-danger, .text-red-500, .bg-red-50').allTextContents();
            if (errorMessages.length > 0) {
                console.log('❌ Errores encontrados:', errorMessages);
            }
        }

        // Tomar screenshot final
        await page.screenshot({ path: `test-obra-documentos-${Date.now()}.png`, fullPage: true });
        console.log('📸 Screenshot tomado');

    } catch (error) {
        console.error('❌ Error en la prueba:', error);
        await page.screenshot({ path: `error-obra-documentos-${Date.now()}.png`, fullPage: true });
    } finally {
        await browser.close();
    }
}

// Ejecutar la prueba
probarCreacionObraConDocumentos();
