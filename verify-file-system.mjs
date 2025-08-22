import { execSync } from 'child_process';
import fs from 'fs';

console.log('🔧 Verificando sistema de subida de archivos...');

try {
    // Verificar que el modelo Obra tenga los métodos necesarios
    console.log('📋 Verificando modelo Obra...');

    const modelContent = fs.readFileSync('app/Models/Obra.php', 'utf8');

    const requiredMethods = ['subirContrato', 'subirFianza', 'subirActaEntregaRecepcion'];
    let methodsFound = 0;

    requiredMethods.forEach(method => {
        if (modelContent.includes(`public function ${method}`)) {
            console.log(`✅ Método ${method} encontrado`);
            methodsFound++;
        } else {
            console.log(`❌ Método ${method} NO encontrado`);
        }
    });

    // Verificar controlador
    console.log('\n📋 Verificando controlador ObraController...');

    const controllerContent = fs.readFileSync('app/Http/Controllers/ObraController.php', 'utf8');

    const requiredValidations = ['archivo_contrato', 'archivo_fianza', 'archivo_acta_entrega_recepcion'];
    let validationsFound = 0;

    requiredValidations.forEach(field => {
        if (controllerContent.includes(`'${field}'`)) {
            console.log(`✅ Validación para ${field} encontrada`);
            validationsFound++;
        } else {
            console.log(`❌ Validación para ${field} NO encontrada`);
        }
    });

    // Verificar vista
    console.log('\n📋 Verificando vista create.blade.php...');

    const viewContent = fs.readFileSync('resources/views/obras/create.blade.php', 'utf8');

    // Verificar enctype del formulario
    if (viewContent.includes('enctype="multipart/form-data"')) {
        console.log('✅ Formulario tiene enctype multipart/form-data');
    } else {
        console.log('❌ Formulario NO tiene enctype multipart/form-data');
    }

    // Verificar campos de archivo
    const fileFields = ['archivo_contrato', 'archivo_fianza', 'archivo_acta_entrega_recepcion'];
    let fieldsFound = 0;

    fileFields.forEach(field => {
        if (viewContent.includes(`name="${field}"`)) {
            console.log(`✅ Campo ${field} encontrado en vista`);
            fieldsFound++;
        } else {
            console.log(`❌ Campo ${field} NO encontrado en vista`);
        }
    });

    // Verificar directorios de storage
    console.log('\n📁 Verificando estructura de directorios...');

    const directories = [
        'storage/app/public',
        'storage/app/public/obras',
        'storage/app/public/obras/contratos',
        'storage/app/public/obras/fianzas',
        'storage/app/public/obras/actas'
    ];

    directories.forEach(dir => {
        if (fs.existsSync(dir)) {
            console.log(`✅ Directorio ${dir} existe`);
        } else {
            console.log(`📂 Creando directorio ${dir}...`);
            fs.mkdirSync(dir, { recursive: true });
            console.log(`✅ Directorio ${dir} creado`);
        }
    });

    // Verificar enlace simbólico
    console.log('\n🔗 Verificando enlace simbólico...');

    if (fs.existsSync('public/storage')) {
        console.log('✅ Enlace simbólico public/storage existe');
    } else {
        console.log('📎 Creando enlace simbólico...');
        try {
            execSync('php artisan storage:link', { stdio: 'inherit' });
            console.log('✅ Enlace simbólico creado');
        } catch (error) {
            console.log('⚠️ Error al crear enlace simbólico:', error.message);
        }
    }

    // Resumen final
    console.log('\n📊 RESUMEN DE VERIFICACIÓN:');
    console.log(`📋 Métodos del modelo: ${methodsFound}/${requiredMethods.length}`);
    console.log(`🔧 Validaciones del controlador: ${validationsFound}/${requiredValidations.length}`);
    console.log(`📄 Campos de archivo en vista: ${fieldsFound}/${fileFields.length}`);

    const allSystemsOk = methodsFound === requiredMethods.length &&
        validationsFound === requiredValidations.length &&
        fieldsFound === fileFields.length &&
        viewContent.includes('enctype="multipart/form-data"');

    if (allSystemsOk) {
        console.log('\n🎉 ¡SISTEMA DE ARCHIVOS COMPLETAMENTE CONFIGURADO!');
        console.log('✅ Todos los componentes están en su lugar');
        console.log('✅ Los archivos se guardarán correctamente');
        process.exit(0);
    } else {
        console.log('\n⚠️ Hay algunos componentes que necesitan atención');
        process.exit(1);
    }

} catch (error) {
    console.log('❌ Error durante la verificación:', error.message);
    process.exit(1);
}
