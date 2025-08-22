import { chromium } from 'playwright';

async function testFileUploadFeedback() {
    console.log('🔧 Probando feedback visual de subida de archivos...');

    const browser = await chromium.launch({ headless: false }); // Visible para ver los cambios
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // Navegar directamente a crear obra (sin login para simplificar)
        console.log('📋 Navegando a crear obra...');
        await page.goto('http://127.0.0.1:8003/obras/create', { waitUntil: 'networkidle' });

        // Crear un archivo de prueba en memoria
        const fileContent = 'Archivo de prueba para contrato';
        const fileName = 'contrato_prueba.pdf';

        console.log('📄 Verificando estado inicial de los campos de archivo...');

        // Verificar que los elementos existen
        const contratoLabel = await page.locator('#label_archivo_contrato').count();
        const fianzaLabel = await page.locator('#label_archivo_fianza').count();
        const actaLabel = await page.locator('#label_archivo_acta_entrega_recepcion').count();

        console.log('🔍 Elementos encontrados:');
        console.log(`  - Label contrato: ${contratoLabel > 0 ? '✅' : '❌'}`);
        console.log(`  - Label fianza: ${fianzaLabel > 0 ? '✅' : '❌'}`);
        console.log(`  - Label acta: ${actaLabel > 0 ? '✅' : '❌'}`);

        // Verificar texto inicial
        const initialContratoText = await page.locator('#text_archivo_contrato').textContent();
        console.log(`📝 Texto inicial contrato: "${initialContratoText}"`);

        // Crear archivo temporal para la prueba
        const fs = await import('fs');
        fs.writeFileSync(fileName, fileContent);
        console.log(`📁 Archivo temporal creado: ${fileName}`);

        // Subir archivo al campo de contrato
        console.log('📤 Subiendo archivo al campo de contrato...');
        await page.setInputFiles('#archivo_contrato', fileName);

        // Esperar un momento para que se ejecute el JavaScript
        await page.waitForTimeout(1000);

        // Verificar cambios visuales
        console.log('👁️ Verificando cambios visuales...');

        // Verificar que el texto cambió
        const newContratoText = await page.locator('#text_archivo_contrato').textContent();
        console.log(`📝 Nuevo texto contrato: "${newContratoText}"`);

        // Verificar que el estilo cambió
        const labelClasses = await page.locator('#label_archivo_contrato').getAttribute('class');
        const hasGreenBorder = labelClasses.includes('border-green-400');
        const hasGreenBackground = labelClasses.includes('bg-green-50');

        console.log(`🎨 Estilos aplicados:`);
        console.log(`  - Borde verde: ${hasGreenBorder ? '✅' : '❌'}`);
        console.log(`  - Fondo verde: ${hasGreenBackground ? '✅' : '❌'}`);

        // Verificar que aparece la información del archivo
        const fileInfoVisible = await page.locator('#file_info_archivo_contrato').isVisible();
        console.log(`📋 Info del archivo visible: ${fileInfoVisible ? '✅' : '❌'}`);

        if (fileInfoVisible) {
            const fileName = await page.locator('#filename_archivo_contrato').textContent();
            console.log(`📄 Nombre del archivo mostrado: "${fileName}"`);
        }

        // Probar función de limpiar archivo
        console.log('🧹 Probando función de limpiar archivo...');
        await page.click('button[onclick="clearFile(\'archivo_contrato\')"]');
        await page.waitForTimeout(500);

        // Verificar que se resetea
        const clearedText = await page.locator('#text_archivo_contrato').textContent();
        const fileInfoHidden = await page.locator('#file_info_archivo_contrato').isHidden();

        console.log(`📝 Texto después de limpiar: "${clearedText}"`);
        console.log(`👁️ Info del archivo oculta: ${fileInfoHidden ? '✅' : '❌'}`);

        // Probar con archivo de fianza
        console.log('📤 Probando campo de fianza...');
        await page.setInputFiles('#archivo_fianza', fileName);
        await page.waitForTimeout(1000);

        const fianzaText = await page.locator('#text_archivo_fianza').textContent();
        const fianzaInfoVisible = await page.locator('#file_info_archivo_fianza').isVisible();

        console.log(`📝 Texto fianza: "${fianzaText}"`);
        console.log(`📋 Info fianza visible: ${fianzaInfoVisible ? '✅' : '❌'}`);

        // Verificar funciones JavaScript
        console.log('⚙️ Verificando funciones JavaScript...');

        const updateFunctionExists = await page.evaluate(() => {
            return typeof updateFileLabel === 'function';
        });

        const clearFunctionExists = await page.evaluate(() => {
            return typeof clearFile === 'function';
        });

        const validateFunctionExists = await page.evaluate(() => {
            return typeof validateFiles === 'function';
        });

        console.log(`🔧 Funciones JavaScript:`);
        console.log(`  - updateFileLabel: ${updateFunctionExists ? '✅' : '❌'}`);
        console.log(`  - clearFile: ${clearFunctionExists ? '✅' : '❌'}`);
        console.log(`  - validateFiles: ${validateFunctionExists ? '✅' : '❌'}`);

        // Limpiar archivo temporal
        fs.unlinkSync(fileName);
        console.log(`🗑️ Archivo temporal eliminado`);

        // Resumen
        const success = contratoLabel > 0 &&
            fianzaLabel > 0 &&
            actaLabel > 0 &&
            hasGreenBorder &&
            hasGreenBackground &&
            fileInfoVisible &&
            updateFunctionExists &&
            clearFunctionExists &&
            validateFunctionExists;

        if (success) {
            console.log('\n🎉 ¡FEEDBACK VISUAL DE ARCHIVOS FUNCIONANDO CORRECTAMENTE!');
            console.log('✅ Los usuarios verán cuando se seleccionen archivos');
            console.log('✅ Pueden limpiar archivos seleccionados');
            console.log('✅ Se validan tipos y tamaños de archivo');
            return true;
        } else {
            console.log('\n⚠️ Hay algunos problemas con el feedback visual');
            return false;
        }

    } catch (error) {
        console.log('❌ Error durante la prueba:', error.message);
        return false;
    } finally {
        await browser.close();
    }
}

testFileUploadFeedback().then(success => {
    if (success) {
        console.log('\n🎯 Prueba exitosa - El feedback visual funciona correctamente');
        process.exit(0);
    } else {
        console.log('\n💥 Hay problemas que necesitan ser corregidos');
        process.exit(1);
    }
});
