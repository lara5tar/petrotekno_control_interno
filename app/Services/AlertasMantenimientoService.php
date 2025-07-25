<?php

namespace App\Services;

use App\Models\Vehiculo;
use App\Models\Mantenimiento;
use Illuminate\Support\Facades\Log;

class AlertasMantenimientoService
{
    /**
     * Verificar alertas para un vehículo específico
     */
    public static function verificarVehiculo(int $vehiculoId): array
    {
        try {
            $vehiculo = Vehiculo::with(['estatus', 'kilometrajes', 'mantenimientos'])
                ->find($vehiculoId);

            if (!$vehiculo) {
                Log::warning('Vehículo no encontrado para verificar alertas', ['vehiculo_id' => $vehiculoId]);
                return [];
            }

            // Solo procesar vehículos disponibles, en obra o activos
            $estatusPermitidos = ['disponible', 'en_obra', 'activo'];
            if (!in_array(strtolower($vehiculo->estatus->nombre_estatus), $estatusPermitidos)) {
                Log::info('Vehículo omitido por estatus', [
                    'vehiculo_id' => $vehiculoId,
                    'estatus' => $vehiculo->estatus->nombre_estatus
                ]);
                return [];
            }

            $alertas = [];

            // Verificar cada sistema de mantenimiento
            $sistemas = ['motor', 'transmision', 'hidraulico'];

            foreach ($sistemas as $sistema) {
                $alerta = self::verificarSistema($vehiculo, $sistema);
                if ($alerta) {
                    $alertas[] = $alerta;
                }
            }

            return $alertas;
        } catch (\Exception $e) {
            Log::error('Error verificando alertas de vehículo', [
                'vehiculo_id' => $vehiculoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }

    /**
     * Verificar alerta para un sistema específico del vehículo
     */
    private static function verificarSistema(Vehiculo $vehiculo, string $sistema): ?array
    {
        $campo_intervalo = "intervalo_km_{$sistema}";
        $intervalo = $vehiculo->$campo_intervalo;

        // Si no hay intervalo configurado, omitir
        if (!$intervalo || $intervalo <= 0) {
            return null;
        }

        // Buscar último mantenimiento de este sistema
        $ultimoMantenimiento = Mantenimiento::where('vehiculo_id', $vehiculo->id)
            ->where('sistema_vehiculo', $sistema)
            ->orderBy('kilometraje_servicio', 'desc')
            ->first();

        // Calcular kilometraje base (último mantenimiento o 0)
        $kmBaseMantenimiento = $ultimoMantenimiento?->kilometraje_servicio ?? 0;

        // Calcular próximo mantenimiento
        $proximoMantenimiento = $kmBaseMantenimiento + $intervalo;

        // Solo alertar si el kilometraje actual supera el próximo mantenimiento
        if ($vehiculo->kilometraje_actual >= $proximoMantenimiento) {

            // No alertar si el último mantenimiento fue hoy (evitar alertas inmediatas incorrectas)
            if ($ultimoMantenimiento && $ultimoMantenimiento->created_at->isToday()) {
                Log::info('Alerta omitida por mantenimiento reciente del mismo día', [
                    'vehiculo_id' => $vehiculo->id,
                    'sistema' => $sistema,
                    'ultimo_mantenimiento_fecha' => $ultimoMantenimiento->created_at
                ]);
                return null;
            }

            $kmVencidoPor = $vehiculo->kilometraje_actual - $proximoMantenimiento;

            return [
                'vehiculo_id' => $vehiculo->id,
                'sistema' => ucfirst($sistema),
                'vehiculo_info' => [
                    'marca' => $vehiculo->marca,
                    'modelo' => $vehiculo->modelo,
                    'placas' => $vehiculo->placas,
                    'nombre_completo' => "{$vehiculo->marca} {$vehiculo->modelo} - {$vehiculo->placas}"
                ],
                'kilometraje_actual' => $vehiculo->kilometraje_actual,
                'intervalo_configurado' => $intervalo,
                'ultimo_mantenimiento' => [
                    'fecha' => $ultimoMantenimiento?->fecha_inicio?->format('d/m/Y') ?? 'Nunca',
                    'kilometraje' => $kmBaseMantenimiento,
                    'descripcion' => $ultimoMantenimiento?->descripcion ?? 'Sin mantenimientos previos'
                ],
                'proximo_mantenimiento_km' => $proximoMantenimiento,
                'km_vencido_por' => $kmVencidoPor,
                'urgencia' => self::determinarUrgencia($kmVencidoPor, $intervalo),
                'porcentaje_sobrepaso' => round(($kmVencidoPor / $intervalo) * 100, 1),
                'fecha_deteccion' => now()->format('d/m/Y H:i:s')
            ];
        }

        return null;
    }

    /**
     * Determinar urgencia basada en el sobrepaso del intervalo
     */
    private static function determinarUrgencia(int $kmVencidoPor, int $intervalo): string
    {
        $porcentajeSobrepaso = ($kmVencidoPor / $intervalo) * 100;

        if ($porcentajeSobrepaso >= 20) {
            return 'critica'; // 20% o más de sobrepaso
        } elseif ($porcentajeSobrepaso >= 10) {
            return 'alta'; // 10-19% de sobrepaso
        } else {
            return 'normal'; // Menos del 10% de sobrepaso
        }
    }

    /**
     * Verificar alertas para todos los vehículos activos
     */
    public static function verificarTodosLosVehiculos(): array
    {
        $vehiculos = Vehiculo::whereHas('estatus', function ($query) {
            $query->whereRaw('LOWER(nombre_estatus) IN (?, ?, ?)', ['disponible', 'en_obra', 'activo']);
        })
            ->get();

        $todasLasAlertas = [];

        foreach ($vehiculos as $vehiculo) {
            $alertas = self::verificarVehiculo($vehiculo->id);
            $todasLasAlertas = array_merge($todasLasAlertas, $alertas);
        }

        // Ordenar por urgencia y luego por km vencido
        usort($todasLasAlertas, function ($a, $b) {
            $urgencias = ['critica' => 3, 'alta' => 2, 'normal' => 1];

            if ($urgencias[$a['urgencia']] !== $urgencias[$b['urgencia']]) {
                return $urgencias[$b['urgencia']] - $urgencias[$a['urgencia']]; // Descendente por urgencia
            }

            return $b['km_vencido_por'] - $a['km_vencido_por']; // Descendente por km vencido
        });

        return [
            'alertas' => $todasLasAlertas,
            'resumen' => self::obtenerResumen($todasLasAlertas)
        ];
    }

    /**
     * Obtener resumen de alertas basado en verificación de vehículos
     */
    public static function obtenerResumenAlertas(): array
    {
        $resultado = self::verificarTodosLosVehiculos();
        return [
            'resumen' => $resultado['resumen'],
            'alertas' => $resultado['alertas']
        ];
    }

    /**
     * Obtener resumen de alertas
     */
    public static function obtenerResumen(array $alertas): array
    {
        if (empty($alertas)) {
            return [
                'total_alertas' => 0,
                'vehiculos_afectados' => 0,
                'por_urgencia' => ['critica' => 0, 'alta' => 0, 'normal' => 0],
                'por_sistema' => ['Motor' => 0, 'Transmision' => 0, 'Hidraulico' => 0]
            ];
        }

        $vehiculosAfectados = array_unique(array_column($alertas, 'vehiculo_id'));

        $porUrgencia = array_count_values(array_column($alertas, 'urgencia'));
        $porSistema = array_count_values(array_column($alertas, 'sistema'));

        return [
            'total_alertas' => count($alertas),
            'vehiculos_afectados' => count($vehiculosAfectados),
            'por_urgencia' => [
                'critica' => $porUrgencia['critica'] ?? 0,
                'alta' => $porUrgencia['alta'] ?? 0,
                'normal' => $porUrgencia['normal'] ?? 0
            ],
            'por_sistema' => [
                'Motor' => $porSistema['Motor'] ?? 0,
                'Transmision' => $porSistema['Transmision'] ?? 0,
                'Hidraulico' => $porSistema['Hidraulico'] ?? 0
            ]
        ];
    }
}
