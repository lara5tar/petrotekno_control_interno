import fs from 'fs';

console.log('🖼️ VERIFICANDO CORRECCIÓN DE IMAGEN EN VISTA VEHÍCULO...\n');

const showViewPath = './resources/views/vehiculos/show.blade.php';
const showViewCode = fs.readFileSync(showViewPath, 'utf8');

console.log('=== VERIFICACIÓN DE CORRECCIÓN DE IMAGEN ===\n');

// 1. Verificar que se use url_imagen en lugar de imagen en datos generales
const correctImageField = showViewCode.includes("$vehiculo->url_imagen");
if (correctImageField) {
    console.log('✅ 1. Vista usa $vehiculo->url_imagen correctamente');
} else {
    console.log('❌ 1. ERROR: Vista no usa $vehiculo->url_imagen');
}

// 2. Verificar que no haya referencias incorrectas a $vehiculo->imagen
const incorrectReferences = (showViewCode.match(/\$vehiculo->imagen(?!\s)/g) || []).length;
if (incorrectReferences === 0) {
    console.log('✅ 2. No hay referencias incorrectas a $vehiculo->imagen');
} else {
    console.log(`❌ 2. ERROR: Hay ${incorrectReferences} referencias incorrectas a $vehiculo->imagen`);
}

// 3. Verificar que la condición del if esté corregida
const correctCondition = showViewCode.includes("!empty($vehiculo->url_imagen)");
if (correctCondition) {
    console.log('✅ 3. Condición @if corregida para usar url_imagen');
} else {
    console.log('❌ 3. ERROR: Condición @if no corregida');
}

// 4. Verificar que ambas secciones usen el mismo campo
const imageReferences = (showViewCode.match(/\$vehiculo->url_imagen/g) || []).length;
if (imageReferences >= 2) {
    console.log(`✅ 4. Múltiples referencias a url_imagen encontradas: ${imageReferences}`);
} else {
    console.log(`❌ 4. ERROR: Solo ${imageReferences} referencia(s) a url_imagen`);
}

console.log('\n=== ANÁLISIS DEL PROBLEMA ===');
console.log('');
console.log('🐛 PROBLEMA IDENTIFICADO:');
console.log('   • La vista leía de $vehiculo->imagen');
console.log('   • Pero VehiculoController guarda en url_imagen');
console.log('   • Resultado: "Sin imagen disponible" aunque la imagen existía');
console.log('');
console.log('✅ CORRECCIÓN APLICADA:');
console.log('   • Cambió: @if(!empty($vehiculo->imagen)');
console.log('   • Por: @if(!empty($vehiculo->url_imagen)');
console.log('   • Cambió: src="{{ $vehiculo->imagen }}"');
console.log('   • Por: src="{{ $vehiculo->url_imagen }}"');
console.log('');

// 5. Verificar consistencia con el VehiculoController
console.log('🔗 VERIFICANDO CONSISTENCIA CON CONTROLLER...');
const vehiculoControllerPath = './app/Http/Controllers/VehiculoController.php';
const controllerCode = fs.readFileSync(vehiculoControllerPath, 'utf8');

const controllerUsesUrlImagen = controllerCode.includes("'url_imagen'");
if (controllerUsesUrlImagen) {
    console.log('✅ 5. VehiculoController usa url_imagen (consistente con vista)');
} else {
    console.log('❌ 5. ERROR: VehiculoController no usa url_imagen');
}

if (correctImageField && incorrectReferences === 0 && correctCondition && controllerUsesUrlImagen) {
    console.log('\n🎉 ¡CORRECCIÓN COMPLETA!');
    console.log('   ✅ Vista corregida para usar url_imagen');
    console.log('   ✅ Consistencia entre Controller y Vista');
    console.log('   ✅ La imagen debería mostrarse ahora en "Datos Generales"');
} else {
    console.log('\n⚠️ CORRECCIÓN INCOMPLETA - revisar manualmente');
}

console.log('\n🧪 PRÓXIMO PASO:');
console.log('   1. Refrescar la página del vehículo');
console.log('   2. Verificar que la imagen aparezca en "Datos Generales"');
console.log('   3. Confirmar que coincida con la imagen en "Documentos"');
