<?php

namespace App\Observers;

use App\Models\Vehiculo;
use App\Jobs\VerificarAlertasVehiculo;
use Illuminate\Support\Facades\Log;

class VehiculoObserver
{
    /**
     * Handle the Vehiculo "updated" event.
     */
    public function updated(Vehiculo $vehiculo): void
    {
        // Verificar si el kilometraje actual cambió
        if ($vehiculo->isDirty('kilometraje_actual')) {
            $kilometrajeAnterior = $vehiculo->getOriginal('kilometraje_actual');
            $kilometrajeNuevo = $vehiculo->kilometraje_actual;

            Log::info('Kilometraje de vehículo actualizado', [
                'vehiculo_id' => $vehiculo->id,
                'kilometraje_anterior' => $kilometrajeAnterior,
                'kilometraje_nuevo' => $kilometrajeNuevo,
                'placas' => $vehiculo->placas
            ]);

            // Disparar job para verificar alertas del vehículo en background
            VerificarAlertasVehiculo::dispatch($vehiculo->id)
                ->onQueue('alerts')
                ->delay(now()->addSeconds(5)); // Pequeña demora para evitar procesos simultáneos
        }
    }

    /**
     * Handle the Vehiculo "created" event.
     */
    public function created(Vehiculo $vehiculo): void
    {
        Log::info('Nuevo vehículo creado', [
            'vehiculo_id' => $vehiculo->id,
            'marca' => $vehiculo->marca,
            'modelo' => $vehiculo->modelo,
            'placas' => $vehiculo->placas,
            'kilometraje_inicial' => $vehiculo->kilometraje_actual
        ]);

        // Si el vehículo se crea con kilometraje alto, verificar alertas
        if ($vehiculo->kilometraje_actual > 0) {
            VerificarAlertasVehiculo::dispatch($vehiculo->id)
                ->onQueue('alerts')
                ->delay(now()->addSeconds(10));
        }
    }
}
