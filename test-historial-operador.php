<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HistorialOperadorVehiculo;
use App\Models\Personal;
use App\Models\User;
use App\Models\Vehiculo;

echo "🧪 Probando el sistema de historial de operadores...\n\n";

try {
    // 1. Obtener un vehículo y personal para la prueba
    $vehiculo = Vehiculo::first();
    $personal = Personal::where('estatus', 'activo')->first();
    $usuario = User::first();

    if (!$vehiculo) {
        echo "❌ No hay vehículos en el sistema\n";
        exit(1);
    }

    if (!$personal) {
        echo "❌ No hay personal activo en el sistema\n";
        exit(1);
    }

    if (!$usuario) {
        echo "❌ No hay usuarios en el sistema\n";
        exit(1);
    }

    echo "📋 Datos de prueba:\n";
    echo "   Vehículo: {$vehiculo->marca} {$vehiculo->modelo} (ID: {$vehiculo->id})\n";
    echo "   Personal: {$personal->nombre_completo} (ID: {$personal->id})\n";
    echo "   Usuario: {$usuario->email} (ID: {$usuario->id})\n\n";

    // 2. Probar asignación inicial
    echo "🔄 Probando asignación inicial de operador...\n";
    
    $historial = HistorialOperadorVehiculo::registrarMovimiento(
        vehiculoId: $vehiculo->id,
        operadorAnteriorId: null,
        operadorNuevoId: $personal->id,
        usuarioAsignoId: $usuario->id,
        tipoMovimiento: HistorialOperadorVehiculo::TIPO_ASIGNACION_INICIAL,
        observaciones: 'Prueba de asignación inicial desde script de test',
        motivo: 'Prueba del sistema'
    );

    echo "✅ Historial creado exitosamente (ID: {$historial->id})\n";
    echo "   Tipo: {$historial->descripcion_movimiento}\n";
    echo "   Operador: {$historial->nombre_operador_nuevo}\n\n";

    // 3. Actualizar el vehículo con el operador
    $vehiculo->update(['operador_id' => $personal->id]);
    echo "✅ Vehículo actualizado con operador\n\n";

    // 4. Probar cambio de operador (buscar otro personal)
    $otroPersonal = Personal::where('estatus', 'activo')
        ->where('id', '!=', $personal->id)
        ->first();

    if ($otroPersonal) {
        echo "🔄 Probando cambio de operador...\n";
        
        $historialCambio = HistorialOperadorVehiculo::registrarMovimiento(
            vehiculoId: $vehiculo->id,
            operadorAnteriorId: $personal->id,
            operadorNuevoId: $otroPersonal->id,
            usuarioAsignoId: $usuario->id,
            tipoMovimiento: HistorialOperadorVehiculo::TIPO_CAMBIO_OPERADOR,
            observaciones: 'Prueba de cambio de operador desde script de test',
            motivo: 'Cambio de turno'
        );

        echo "✅ Cambio registrado exitosamente (ID: {$historialCambio->id})\n";
        echo "   Anterior: {$historialCambio->nombre_operador_anterior}\n";
        echo "   Nuevo: {$historialCambio->nombre_operador_nuevo}\n\n";

        // Actualizar el vehículo
        $vehiculo->update(['operador_id' => $otroPersonal->id]);
    }

    // 5. Probar remoción de operador
    echo "🔄 Probando remoción de operador...\n";
    
    $historialRemocion = HistorialOperadorVehiculo::registrarMovimiento(
        vehiculoId: $vehiculo->id,
        operadorAnteriorId: $vehiculo->operador_id,
        operadorNuevoId: null,
        usuarioAsignoId: $usuario->id,
        tipoMovimiento: HistorialOperadorVehiculo::TIPO_REMOCION_OPERADOR,
        observaciones: 'Prueba de remoción de operador desde script de test',
        motivo: 'Fin de turno'
    );

    echo "✅ Remoción registrada exitosamente (ID: {$historialRemocion->id})\n";
    echo "   Operador removido: {$historialRemocion->nombre_operador_anterior}\n\n";

    // Remover operador del vehículo
    $vehiculo->update(['operador_id' => null]);

    // 6. Mostrar historial completo del vehículo
    echo "📊 Historial completo del vehículo:\n";
    $historialCompleto = $vehiculo->historialOperadores()
        ->with(['operadorAnterior', 'operadorNuevo', 'usuarioAsigno'])
        ->orderBy('fecha_asignacion', 'desc')
        ->get();

    foreach ($historialCompleto as $registro) {
        echo "   [{$registro->fecha_asignacion->format('Y-m-d H:i:s')}] ";
        echo "{$registro->descripcion_movimiento}";
        
        if ($registro->operador_anterior_id && $registro->operador_nuevo_id) {
            echo " ({$registro->nombre_operador_anterior} → {$registro->nombre_operador_nuevo})";
        } elseif ($registro->operador_nuevo_id) {
            echo " ({$registro->nombre_operador_nuevo})";
        } else {
            echo " ({$registro->nombre_operador_anterior} removido)";
        }
        
        echo "\n";
    }

    echo "\n✅ Todas las pruebas completadas exitosamente!\n";

} catch (\Exception $e) {
    echo "❌ Error durante las pruebas: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . " línea " . $e->getLine() . "\n";
    exit(1);
}
