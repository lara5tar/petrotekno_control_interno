<?php

namespace Database\Seeders;

use App\Models\HistorialOperadorVehiculo;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistorialOperadorVehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer usuario activo para usar como usuario que "asignó"
        $usuarioAdmin = User::whereNotNull('rol_id')->first();
        
        if (!$usuarioAdmin) {
            $this->command->error('No se encontró ningún usuario activo. Se necesita al menos uno para crear el historial.');
            return;
        }

        $this->command->info('Iniciando población del historial de operadores...');

        // Obtener todos los vehículos que actualmente tienen un operador asignado
        $vehiculosConOperador = Vehiculo::whereNotNull('operador_id')
            ->with('operador')
            ->get();

        $registrosCreados = 0;

        foreach ($vehiculosConOperador as $vehiculo) {
            // Verificar si ya existe un registro en el historial para este vehículo
            $yaExisteHistorial = HistorialOperadorVehiculo::where('vehiculo_id', $vehiculo->id)->exists();
            
            if (!$yaExisteHistorial) {
                // Crear un registro de asignación inicial
                HistorialOperadorVehiculo::create([
                    'vehiculo_id' => $vehiculo->id,
                    'operador_anterior_id' => null, // No había operador anterior
                    'operador_nuevo_id' => $vehiculo->operador_id,
                    'usuario_asigno_id' => $usuarioAdmin->id,
                    'fecha_asignacion' => $vehiculo->created_at ?? now()->subMonths(rand(1, 6)), // Fecha aproximada
                    'tipo_movimiento' => HistorialOperadorVehiculo::TIPO_ASIGNACION_INICIAL,
                    'observaciones' => 'Registro histórico creado automáticamente al implementar el sistema de historial',
                    'motivo' => 'Migración de datos existentes',
                ]);

                $registrosCreados++;
                
                $this->command->info("✓ Historial creado para vehículo: {$vehiculo->marca} {$vehiculo->modelo} (Operador: {$vehiculo->operador->nombre_completo})");
            } else {
                $this->command->line("- Vehículo {$vehiculo->marca} {$vehiculo->modelo} ya tiene historial registrado");
            }
        }

        // También crear registros para vehículos que pueden haber tenido operadores en asignaciones de obra
        $this->crearHistorialDesdeAsignacionesObra($usuarioAdmin->id);

        $this->command->info("Historial poblado exitosamente. Total de registros nuevos creados: {$registrosCreados}");
    }

    /**
     * Crear historial basado en las asignaciones de obra que tienen operadores
     */
    private function crearHistorialDesdeAsignacionesObra(int $usuarioAdminId): void
    {
        $this->command->info('Revisando asignaciones de obra para crear historial adicional...');

        // Obtener asignaciones de obra que tienen operadores
        $asignacionesConOperador = DB::table('asignaciones_obra')
            ->whereNotNull('operador_id')
            ->orderBy('fecha_asignacion', 'asc')
            ->get();

        $registrosAdicionales = 0;

        foreach ($asignacionesConOperador as $asignacion) {
            // Verificar si ya existe un registro en el historial para esta combinación
            $yaExiste = HistorialOperadorVehiculo::where('vehiculo_id', $asignacion->vehiculo_id)
                ->where('operador_nuevo_id', $asignacion->operador_id)
                ->where('fecha_asignacion', $asignacion->fecha_asignacion)
                ->exists();

            if (!$yaExiste) {
                // Buscar si el vehículo tenía un operador diferente antes de esta asignación
                $operadorAnterior = null;
                $historialAnterior = HistorialOperadorVehiculo::where('vehiculo_id', $asignacion->vehiculo_id)
                    ->where('fecha_asignacion', '<', $asignacion->fecha_asignacion)
                    ->orderBy('fecha_asignacion', 'desc')
                    ->first();

                if ($historialAnterior) {
                    $operadorAnterior = $historialAnterior->operador_nuevo_id;
                }

                // Determinar el tipo de movimiento
                $tipoMovimiento = $operadorAnterior 
                    ? HistorialOperadorVehiculo::TIPO_CAMBIO_OPERADOR 
                    : HistorialOperadorVehiculo::TIPO_ASIGNACION_INICIAL;

                HistorialOperadorVehiculo::create([
                    'vehiculo_id' => $asignacion->vehiculo_id,
                    'operador_anterior_id' => $operadorAnterior,
                    'operador_nuevo_id' => $asignacion->operador_id,
                    'usuario_asigno_id' => $usuarioAdminId,
                    'fecha_asignacion' => $asignacion->fecha_asignacion,
                    'tipo_movimiento' => $tipoMovimiento,
                    'observaciones' => 'Registro histórico creado desde asignación de obra: ' . ($asignacion->observaciones ?? 'Sin observaciones'),
                    'motivo' => 'Migración desde asignaciones de obra',
                ]);

                $registrosAdicionales++;
            }
        }

        $this->command->info("Registros adicionales creados desde asignaciones de obra: {$registrosAdicionales}");
    }
}
