<?php

namespace App\Services;

use App\Models\Vehiculo;
use Illuminate\Support\Collection;

class AlertasService
{
    /**
     * Obtener el número total de alertas de mantenimiento activas
     */
    public static function getAlertasCount(): int
    {
        return static::getAlertas()->count();
    }

    /**
     * Obtener todas las alertas de mantenimiento activas
     */
    public static function getAlertas(): Collection
    {
        $alertas = collect();

        // Obtener vehículos activos con sus últimos kilometrajes
        $vehiculos = Vehiculo::with(['kilometrajes' => function ($query) {
            $query->orderBy('kilometraje', 'desc')->limit(1);
        }])
        ->where('estatus_id', '!=', 4) // No incluir vehículos fuera de servicio
        ->get();

        foreach ($vehiculos as $vehiculo) {
            $ultimoKilometraje = $vehiculo->kilometrajes->first();

            if ($ultimoKilometraje) {
                /** @var \App\Models\Kilometraje $ultimoKilometraje */
                $alertasVehiculo = $ultimoKilometraje->calcularProximosMantenimientos();

                foreach ($alertasVehiculo as $alerta) {
                    $alertas->push([
                        'vehiculo' => $vehiculo,
                        'ultimo_kilometraje' => $ultimoKilometraje,
                        'alerta' => $alerta,
                    ]);
                }
            }
        }

        return $alertas;
    }

    /**
     * Obtener número de alertas urgentes
     */
    public static function getAlertasUrgentesCount(): int
    {
        return static::getAlertas()->where('alerta.urgente', true)->count();
    }

    /**
     * Verificar si hay alertas urgentes
     */
    public static function tieneAlertasUrgentes(): bool
    {
        return static::getAlertasUrgentesCount() > 0;
    }
}