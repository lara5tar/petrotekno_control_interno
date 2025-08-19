<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Mantenimiento;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MantenimientoAlertasController extends Controller
{
    /**
     * Umbral de kilómetros para considerar una alerta como "Próximo"
     */
    const UMBRAL_PROXIMO_KM = 1000;

    /**
     * Mostrar la vista de alertas de mantenimiento
     */
    public function index(): View
    {
        // Obtener todos los vehículos activos con sus relaciones
        $vehiculos = Vehiculo::with(['mantenimientos' => function($query) {
            $query->orderBy('fecha_fin', 'desc')->orderBy('kilometraje_servicio', 'desc');
        }])->get();

        $alertas = [];

        foreach ($vehiculos as $vehiculo) {
            $vehiculoData = [
                'id' => $vehiculo->id,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                'placas' => $vehiculo->placas,
                'kilometraje_actual' => $vehiculo->kilometraje_actual,
                'alertas' => []
            ];

            // Calcular alertas para cada sistema
            $sistemas = [
                'motor' => [
                    'sistema' => Mantenimiento::SISTEMA_MOTOR,
                    'intervalo' => $vehiculo->intervalo_km_motor ?? 10000
                ],
                'transmision' => [
                    'sistema' => Mantenimiento::SISTEMA_TRANSMISION,
                    'intervalo' => $vehiculo->intervalo_km_transmision ?? 20000
                ],
                'hidraulico' => [
                    'sistema' => Mantenimiento::SISTEMA_HIDRAULICO,
                    'intervalo' => $vehiculo->intervalo_km_hidraulico ?? 15000
                ]
            ];

            foreach ($sistemas as $nombreSistema => $sistema) {
                $alerta = $this->calcularAlertaMantenimiento(
                    $vehiculo,
                    $sistema['sistema'],
                    $sistema['intervalo']
                );
                
                $vehiculoData['alertas'][$nombreSistema] = $alerta;
            }

            $alertas[] = $vehiculoData;
        }

        // Ordenar los vehículos priorizando los que tienen alertas más urgentes
        $alertas = collect($alertas)->sortBy(function ($vehiculo) {
            $prioridad = 0;
            foreach ($vehiculo['alertas'] as $alerta) {
                if ($alerta['estado'] === 'Vencido') {
                    $prioridad += 3;
                } elseif ($alerta['estado'] === 'Próximo') {
                    $prioridad += 2;
                } else {
                    $prioridad += 1;
                }
            }
            return -$prioridad; // Negativo para orden descendente
        })->values()->toArray();

        return view('alertas.mantenimiento', compact('alertas'));
    }

    /**
     * Calcula la alerta de mantenimiento para un sistema específico de un vehículo
     */
    private function calcularAlertaMantenimiento(Vehiculo $vehiculo, string $sistema, int $intervalo): array
    {
        // Buscar el último mantenimiento para este vehículo y sistema
        $ultimoMantenimiento = $vehiculo->mantenimientos
            ->where('sistema_vehiculo', $sistema)
            ->first();

        // Si no hay mantenimiento previo, asumir kilometraje 0
        $kilometrajeUltimoServicio = $ultimoMantenimiento ? $ultimoMantenimiento->kilometraje_servicio : 0;

        // Calcular el kilometraje del próximo servicio
        $kilometrajeProximoServicio = $kilometrajeUltimoServicio + $intervalo;

        // Calcular kilómetros restantes
        $kilometrosRestantes = $kilometrajeProximoServicio - $vehiculo->kilometraje_actual;

        // Determinar el estado de la alerta
        $estado = $this->determinarEstadoAlerta($kilometrosRestantes);

        return [
            'kilometraje_ultimo_servicio' => $kilometrajeUltimoServicio,
            'kilometraje_proximo_servicio' => $kilometrajeProximoServicio,
            'kilometros_restantes' => $kilometrosRestantes,
            'estado' => $estado,
            'intervalo' => $intervalo,
            'fecha_ultimo_servicio' => $ultimoMantenimiento ? $ultimoMantenimiento->fecha_fin : null
        ];
    }

    /**
     * Determina el estado de alerta basado en los kilómetros restantes
     */
    private function determinarEstadoAlerta(int $kilometrosRestantes): string
    {
        if ($kilometrosRestantes <= 0) {
            return 'Vencido';
        } elseif ($kilometrosRestantes <= self::UMBRAL_PROXIMO_KM) {
            return 'Próximo';
        } else {
            return 'OK';
        }
    }
}
