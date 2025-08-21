import fs from 'fs';

console.log('🔧 VERIFICANDO CORRECCIÓN DEL ERROR DE COLUMNA...\n');

const vehiculoControllerPath = './app/Http/Controllers/VehiculoController.php';
const vehiculoCode = fs.readFileSync(vehiculoControllerPath, 'utf8');

console.log('=== VERIFICACIÓN DE CORRECCIONES DE COLUMNA ===\n');

// 1. Verificar que se use 'nombre_tipo_documento' en lugar de 'nombre'
const correctColumn = vehiculoCode.includes("'nombre_tipo_documento' => $nombre");
if (correctColumn) {
    console.log('✅ 1. Columna corregida: usa "nombre_tipo_documento" en lugar de "nombre"');
} else {
    console.log('❌ 1. ERROR: Aún usa columna "nombre" incorrecta');
}

// 2. Verificar que se use 'requiere_vencimiento' en lugar de 'categoria'
const correctField = vehiculoCode.includes("'requiere_vencimiento' =>");
if (correctField) {
    console.log('✅ 2. Campo corregido: usa "requiere_vencimiento" en lugar de "categoria"');
} else {
    console.log('❌ 2. ERROR: Aún usa campo "categoria" incorrecto');
}

// 3. Verificar que la consulta WHERE también esté corregida
const correctWhere = vehiculoCode.includes("->where('nombre_tipo_documento', $config['tipo_documento_nombre'])");
if (correctWhere) {
    console.log('✅ 3. Consulta WHERE corregida: usa "nombre_tipo_documento"');
} else {
    console.log('❌ 3. ERROR: Consulta WHERE aún usa columna incorrecta');
}

// 4. Verificar que no queden referencias a la columna 'nombre' incorrecta
const incorrectReferences = (vehiculoCode.match(/where.*'nombre'.*=>/g) || []).length;
if (incorrectReferences === 0) {
    console.log('✅ 4. No hay referencias a columna "nombre" incorrecta');
} else {
    console.log(`❌ 4. ERROR: Aún hay ${incorrectReferences} referencias a columna "nombre" incorrecta`);
}

console.log('\n=== ANÁLISIS DEL ERROR CORREGIDO ===');
console.log('');
console.log('🐛 ERROR ORIGINAL:');
console.log('   SQLSTATE[42S22]: Column not found: 1054 Unknown column "nombre"');
console.log('   SQL: select * from `catalogo_tipos_documento` where (`nombre` = Póliza de Seguro)');
console.log('');
console.log('✅ CORRECCIÓN APLICADA:');
console.log('   ANTES: [\'nombre\' => $nombre]');
console.log('   DESPUÉS: [\'nombre_tipo_documento\' => $nombre]');
console.log('');
console.log('   ANTES: $query->where(\'nombre\', $config[\'tipo_documento_nombre\'])');
console.log('   DESPUÉS: $query->where(\'nombre_tipo_documento\', $config[\'tipo_documento_nombre\'])');
console.log('');
console.log('🗃️ ESTRUCTURA REAL DE LA TABLA:');
console.log('   • id');
console.log('   • nombre_tipo_documento ← COLUMNA CORRECTA');
console.log('   • descripcion');
console.log('   • requiere_vencimiento');
console.log('   • timestamps');
console.log('');

if (correctColumn && correctField && correctWhere && incorrectReferences === 0) {
    console.log('🎉 ¡CORRECCIÓN COMPLETA!');
    console.log('   ✅ Todas las referencias a columnas corregidas');
    console.log('   ✅ El error de "Column not found" debe estar resuelto');
    console.log('   ✅ Ahora debería poder crear vehículos con documentos');
} else {
    console.log('⚠️ CORRECCIÓN INCOMPLETA - revisar manualmente');
}

console.log('\n🧪 PRÓXIMO PASO:');
console.log('   1. Probar crear vehículo con documentos');
console.log('   2. Verificar que no aparezca el error de columna');
console.log('   3. Confirmar que se crean registros en tabla documentos');
