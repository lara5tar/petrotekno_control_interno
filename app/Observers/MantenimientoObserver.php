<?php

namespace App\Observers;

use App\Jobs\RecalcularAlertasVehiculo;
use App\Models\Mantenimiento;
use App\Models\Kilometraje;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MantenimientoObserver
{
    /**
     * Handle the Mantenimiento "created" event.
     */
    public function created(Mantenimiento $mantenimiento): void
    {
        Log::info('MantenimientoObserver: Mantenimiento creado', [
            'mantenimiento_id' => $mantenimiento->id,
            'vehiculo_id' => $mantenimiento->vehiculo_id,
            'kilometraje_servicio' => $mantenimiento->kilometraje_servicio
        ]);

        // Actualizar kilometraje del vehículo si es necesario
        $this->actualizarKilometrajeVehiculo($mantenimiento);

        // Recalcular alertas
        $this->recalcularAlertas($mantenimiento, 'mantenimiento_created');
    }

    /**
     * Handle the Mantenimiento "updated" event.
     */
    public function updated(Mantenimiento $mantenimiento): void
    {
        // Solo procesar si cambió el kilometraje del servicio
        if ($mantenimiento->wasChanged('kilometraje_servicio')) {
            Log::info('MantenimientoObserver: Kilometraje de servicio actualizado', [
                'mantenimiento_id' => $mantenimiento->id,
                'vehiculo_id' => $mantenimiento->vehiculo_id,
                'kilometraje_anterior' => $mantenimiento->getOriginal('kilometraje_servicio'),
                'kilometraje_nuevo' => $mantenimiento->kilometraje_servicio
            ]);

            // Actualizar kilometraje del vehículo si es necesario
            $this->actualizarKilometrajeVehiculo($mantenimiento);

            // Recalcular alertas
            $this->recalcularAlertas($mantenimiento, 'mantenimiento_updated');
        }

        // También recalcular si cambió el sistema del vehículo
        if ($mantenimiento->wasChanged('sistema_vehiculo')) {
            $this->recalcularAlertas($mantenimiento, 'sistema_vehiculo_updated');
        }
    }

    /**
     * Handle the Mantenimiento "deleted" event.
     */
    public function deleted(Mantenimiento $mantenimiento): void
    {
        Log::info('MantenimientoObserver: Mantenimiento eliminado', [
            'mantenimiento_id' => $mantenimiento->id,
            'vehiculo_id' => $mantenimiento->vehiculo_id
        ]);

        // Al eliminar un mantenimiento, recalcular el kilometraje del vehículo
        $this->recalcularKilometrajeVehiculo($mantenimiento->vehiculo_id);

        // Recalcular alertas
        $this->recalcularAlertas($mantenimiento, 'mantenimiento_deleted');
    }

    /**
     * Handle the Mantenimiento "restored" event.
     */
    public function restored(Mantenimiento $mantenimiento): void
    {
        Log::info('MantenimientoObserver: Mantenimiento restaurado', [
            'mantenimiento_id' => $mantenimiento->id,
            'vehiculo_id' => $mantenimiento->vehiculo_id
        ]);

        // Al restaurar, actualizar kilometraje si es necesario
        $this->actualizarKilometrajeVehiculo($mantenimiento);

        // Recalcular alertas
        $this->recalcularAlertas($mantenimiento, 'mantenimiento_restored');
    }

    /**
     * Handle the Mantenimiento "force deleted" event.
     */
    public function forceDeleted(Mantenimiento $mantenimiento): void
    {
        Log::info('MantenimientoObserver: Mantenimiento eliminado permanentemente', [
            'mantenimiento_id' => $mantenimiento->id,
            'vehiculo_id' => $mantenimiento->vehiculo_id
        ]);

        // Al eliminar permanentemente, recalcular kilometraje del vehículo
        $this->recalcularKilometrajeVehiculo($mantenimiento->vehiculo_id);

        // Recalcular alertas
        $this->recalcularAlertas($mantenimiento, 'mantenimiento_force_deleted');
    }

    /**
     * Actualizar kilometraje del vehículo basado en el mantenimiento
     * VALIDACIÓN CRÍTICA: Solo actualizar si kilometraje_servicio > kilometraje_actual
     */
    private function actualizarKilometrajeVehiculo(Mantenimiento $mantenimiento): void
    {
        try {
            $vehiculo = $mantenimiento->vehiculo;

            if (!$vehiculo) {
                Log::warning('Vehículo no encontrado para actualizar kilometraje', [
                    'vehiculo_id' => $mantenimiento->vehiculo_id
                ]);
                return;
            }

            // VALIDACIÓN CRÍTICA: Solo actualizar si el kilometraje del servicio es mayor
            if ($mantenimiento->kilometraje_servicio > $vehiculo->kilometraje_actual) {
                $kmAnterior = $vehiculo->kilometraje_actual;

                $vehiculo->update([
                    'kilometraje_actual' => $mantenimiento->kilometraje_servicio
                ]);

                Log::info('Kilometraje del vehículo actualizado por mantenimiento', [
                    'vehiculo_id' => $vehiculo->id,
                    'km_anterior' => $kmAnterior,
                    'km_nuevo' => $mantenimiento->kilometraje_servicio,
                    'mantenimiento_id' => $mantenimiento->id,
                    'diferencia' => $mantenimiento->kilometraje_servicio - $kmAnterior
                ]);
            } else {
                Log::info('Kilometraje del vehículo NO actualizado (mantenimiento menor o igual)', [
                    'vehiculo_id' => $vehiculo->id,
                    'km_vehiculo' => $vehiculo->kilometraje_actual,
                    'km_mantenimiento' => $mantenimiento->kilometraje_servicio,
                    'mantenimiento_id' => $mantenimiento->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error actualizando kilometraje del vehículo', [
                'vehiculo_id' => $mantenimiento->vehiculo_id,
                'mantenimiento_id' => $mantenimiento->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Recalcular kilometraje del vehículo basado en registros existentes
     * (usado cuando se elimina un mantenimiento)
     */
    private function recalcularKilometrajeVehiculo(int $vehiculoId): void
    {
        try {
            // Obtener el último mantenimiento
            $ultimoMantenimiento = Mantenimiento::where('vehiculo_id', $vehiculoId)
                ->orderBy('kilometraje_servicio', 'desc')
                ->first();

            // Obtener el último kilometraje registrado
            $ultimoKilometraje = Kilometraje::where('vehiculo_id', $vehiculoId)
                ->orderBy('kilometraje', 'desc')
                ->first();

            // Determinar el kilometraje final (el mayor entre mantenimiento y registro de kilometraje)
            $kmFinal = max(
                $ultimoMantenimiento?->kilometraje_servicio ?? 0,
                $ultimoKilometraje?->kilometraje ?? 0
            );

            if ($kmFinal > 0) {
                $vehiculo = \App\Models\Vehiculo::find($vehiculoId);
                if ($vehiculo) {
                    $kmAnterior = $vehiculo->kilometraje_actual;
                    $vehiculo->update(['kilometraje_actual' => $kmFinal]);

                    Log::info('Kilometraje del vehículo recalculado', [
                        'vehiculo_id' => $vehiculoId,
                        'km_anterior' => $kmAnterior,
                        'km_nuevo' => $kmFinal,
                        'fuente_ultimo_mantenimiento' => $ultimoMantenimiento?->kilometraje_servicio,
                        'fuente_ultimo_kilometraje' => $ultimoKilometraje?->kilometraje
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error recalculando kilometraje del vehículo', [
                'vehiculo_id' => $vehiculoId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Enviar job para recalcular alertas
     */
    private function recalcularAlertas(Mantenimiento $mantenimiento, string $trigger): void
    {
        try {
            // Obtener ID del usuario autenticado si existe
            $usuarioId = Auth::check() ? Auth::user()->id : null;

            // Enviar job asíncrono para recalcular alertas
            dispatch(new RecalcularAlertasVehiculo(
                $mantenimiento->vehiculo_id,
                $trigger,
                $usuarioId
            ));

            Log::info('Job de recálculo de alertas enviado', [
                'vehiculo_id' => $mantenimiento->vehiculo_id,
                'trigger' => $trigger,
                'usuario_id' => $usuarioId
            ]);
        } catch (\Exception $e) {
            Log::error('Error enviando job de recálculo de alertas', [
                'vehiculo_id' => $mantenimiento->vehiculo_id,
                'trigger' => $trigger,
                'error' => $e->getMessage()
            ]);
        }
    }
}
