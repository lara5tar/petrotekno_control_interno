<?php

namespace App\Observers;

use App\Models\Vehiculo;
use App\Models\Kilometraje;
use App\Jobs\VerificarAlertasVehiculo;
use Illuminate\Support\Facades\Auth;
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

            // Registrar el cambio de kilometraje si es significativo (más de 1 km de incremento)
            if ($kilometrajeNuevo > $kilometrajeAnterior && ($kilometrajeNuevo - $kilometrajeAnterior) > 1) {
                try {
                    Kilometraje::create([
                        'vehiculo_id' => $vehiculo->id,
                        'obra_id' => null,
                        'kilometraje' => $kilometrajeNuevo,
                        'fecha_captura' => now(),
                        'usuario_captura_id' => Auth::id() ?? 1,
                        'observaciones' => "Kilometraje actualizado de {$kilometrajeAnterior} a {$kilometrajeNuevo} km.",
                    ]);

                    Log::info("Kilometraje actualizado registrado para vehículo {$vehiculo->id}: {$kilometrajeAnterior} -> {$kilometrajeNuevo} km");
                } catch (\Exception $e) {
                    Log::error("Error al registrar actualización de kilometraje para vehículo {$vehiculo->id}: " . $e->getMessage());
                }
            }

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

        // Registrar kilometraje inicial automáticamente
        if ($vehiculo->kilometraje_actual && $vehiculo->kilometraje_actual > 0) {
            try {
                Kilometraje::create([
                    'vehiculo_id' => $vehiculo->id,
                    'obra_id' => null, // No hay obra específica para el kilometraje inicial
                    'kilometraje' => $vehiculo->kilometraje_actual,
                    'fecha_captura' => now(),
                    'usuario_captura_id' => Auth::id() ?? 1, // Usuario actual o admin por defecto
                    'observaciones' => 'Kilometraje inicial del vehículo registrado automáticamente al crear el vehículo.',
                ]);

                Log::info("Kilometraje inicial registrado automáticamente para vehículo {$vehiculo->id}: {$vehiculo->kilometraje_actual} km");
            } catch (\Exception $e) {
                Log::error("Error al registrar kilometraje inicial para vehículo {$vehiculo->id}: " . $e->getMessage());
            }
        }

        // Si el vehículo se crea con kilometraje alto, verificar alertas
        if ($vehiculo->kilometraje_actual > 0) {
            VerificarAlertasVehiculo::dispatch($vehiculo->id)
                ->onQueue('alerts')
                ->delay(now()->addSeconds(10));
        }
    }
}
