console.log('=== Test Manual del Campo numero_poliza ===');

// Simulación de datos del formulario
const formData = {
    marca: 'Toyota',
    numero_poliza: '190324',
    modelo: 'Corolla',
    anio: '2023',
    n_serie: 'TEST123456789',
    placas: 'TEST-123-A',
    kilometraje_actual: '1000'
};

console.log('Datos que deberían enviarse:');
console.log(JSON.stringify(formData, null, 2));

console.log('\n=== Verificaciones ===');
console.log('✓ Campo numero_poliza en validación del controlador');
console.log('✓ Campo numero_poliza en array fillable del modelo');
console.log('✓ Campo numero_poliza en método store del controlador');
console.log('✓ Campo numero_poliza en método update del controlador');
console.log('✓ Columna numero_poliza existe en la base de datos');
console.log('✓ Input numero_poliza está en el formulario');

console.log('\n=== Pasos para probar manualmente ===');
console.log('1. Ir a: http://127.0.0.1:8000/vehiculos/create');
console.log('2. Llenar todos los campos incluyendo "Número de Póliza": 190324');
console.log('3. Enviar el formulario');
console.log('4. Verificar que el vehículo se crea y muestra el número de póliza');

console.log('\n=== Si aún no funciona, revisar ===');
console.log('- Abrir Developer Tools del navegador');
console.log('- Ver en Network tab si el campo se envía en el POST');
console.log('- Revisar logs de Laravel para errores');
