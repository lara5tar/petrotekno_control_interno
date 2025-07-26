<?php

namespace App\Services;

use App\Models\Vehiculo;
use App\Models\Mantenimiento;
use App\Models\ConfiguracionAlerta;
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

            // Obtener los dos últimos mantenimientos de este mismo sistema
            $ultimosMantenimientos = Mantenimiento::where('vehiculo_id', $vehiculo->id)
                ->where('sistema_vehiculo', $sistema)
                ->orderBy('kilometraje_servicio', 'desc')
                ->limit(2)
                ->get()
                ->map(function ($mantenimiento) {
                    return [
                        'fecha' => $mantenimiento->fecha_inicio?->format('d/m/Y') ?? 'No especificada',
                        'kilometraje' => $mantenimiento->kilometraje_servicio,
                        'tipo_servicio' => $mantenimiento->tipo_servicio,
                        'descripcion' => $mantenimiento->descripcion ?? 'Sin descripción',
                        'proveedor' => $mantenimiento->proveedor ?? 'No especificado',
                        'costo' => $mantenimiento->costo ? '$' . number_format($mantenimiento->costo, 2) : 'No especificado'
                    ];
                });

            return [
                'vehiculo_id' => $vehiculo->id,
                'sistema' => ucfirst($sistema), // Para compatibilidad con tests
                'vehiculo_info' => [
                    'marca' => $vehiculo->marca,
                    'modelo' => $vehiculo->modelo,
                    'placas' => $vehiculo->placas,
                    'nombre_completo' => "{$vehiculo->marca} {$vehiculo->modelo} - {$vehiculo->placas}",
                    'kilometraje_actual' => number_format($vehiculo->kilometraje_actual) . ' km'
                ],
                'sistema_mantenimiento' => [
                    'nombre_sistema' => ucfirst($sistema),
                    'intervalo_km' => number_format($intervalo) . ' km',
                    'tipo_mantenimiento' => self::obtenerTipoMantenimientoPorSistema($sistema),
                    'descripcion_sistema' => self::obtenerDescripcionSistema($sistema)
                ],
                'intervalo_alcanzado' => [
                    'intervalo_configurado' => $intervalo,
                    'kilometraje_base' => $kmBaseMantenimiento,
                    'proximo_mantenimiento_esperado' => $proximoMantenimiento,
                    'kilometraje_actual' => $vehiculo->kilometraje_actual,
                    'km_exceso' => $kmVencidoPor,
                    'porcentaje_sobrepaso' => round(($kmVencidoPor / $intervalo) * 100, 1) . '%'
                ],
                'historial_mantenimientos' => [
                    'cantidad_encontrada' => $ultimosMantenimientos->count(),
                    'mantenimientos' => $ultimosMantenimientos->toArray()
                ],
                'urgencia' => self::determinarUrgencia($kmVencidoPor, $intervalo),
                'fecha_deteccion' => now()->format('d/m/Y H:i:s'),
                'mensaje_resumen' => self::generarMensajeResumen($vehiculo, $sistema, $intervalo, $kmVencidoPor, $proximoMantenimiento)
            ];
        }

        return null;
    }

    /**
     * Obtener tipo de mantenimiento según el sistema
     */
    private static function obtenerTipoMantenimientoPorSistema(string $sistema): string
    {
        $tipos = [
            'motor' => 'Mantenimiento Preventivo de Motor',
            'transmision' => 'Mantenimiento Preventivo de Transmisión',
            'hidraulico' => 'Mantenimiento Preventivo del Sistema Hidráulico'
        ];

        return $tipos[$sistema] ?? 'Mantenimiento Preventivo';
    }

    /**
     * Obtener descripción detallada del sistema
     */
    private static function obtenerDescripcionSistema(string $sistema): string
    {
        $descripciones = [
            'motor' => 'Cambio de aceite, filtros y revisión general del motor',
            'transmision' => 'Cambio de aceite de transmisión, filtros y ajustes',
            'hidraulico' => 'Cambio de aceite hidráulico, filtros y revisión de mangueras'
        ];

        return $descripciones[$sistema] ?? 'Mantenimiento general del sistema';
    }

    /**
     * Generar mensaje resumen más descriptivo
     */
    private static function generarMensajeResumen(Vehiculo $vehiculo, string $sistema, int $intervalo, int $kmVencidoPor, int $proximoMantenimiento): string
    {
        $sistemaTexto = ucfirst($sistema);
        $vehiculoInfo = "{$vehiculo->marca} {$vehiculo->modelo} ({$vehiculo->placas})";

        return "El vehículo {$vehiculoInfo} ha superado su intervalo de mantenimiento del {$sistemaTexto} " .
            "por " . number_format($kmVencidoPor) . " km. " .
            "Se esperaba mantenimiento en los " . number_format($proximoMantenimiento) . " km, " .
            "pero actualmente tiene " . number_format($vehiculo->kilometraje_actual) . " km.";
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

            return $b['intervalo_alcanzado']['km_exceso'] - $a['intervalo_alcanzado']['km_exceso']; // Descendente por km vencido
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

    /**
     * Obtener lista completa de destinatarios (configuración + prueba)
     */
    public static function obtenerDestinatarios(): array
    {
        try {
            $destinatarios = [];

            // 1. Obtener destinatarios de configuración del usuario
            $configuracion = ConfiguracionAlerta::where('activo', true)->first();
            if ($configuracion && !empty($configuracion->emails_principales)) {
                $emailsConfiguracion = is_array($configuracion->emails_principales)
                    ? $configuracion->emails_principales
                    : json_decode($configuracion->emails_principales, true) ?? [];

                $destinatarios = array_merge($destinatarios, $emailsConfiguracion);
            }

            // 2. Obtener destinatarios de prueba desde .env
            $emailsPrueba = self::obtenerEmailsPrueba();
            $destinatarios = array_merge($destinatarios, $emailsPrueba);

            // 3. Limpiar y validar emails
            $destinatarios = array_filter(array_unique($destinatarios), function ($email) {
                return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
            });

            Log::info('Destinatarios de alertas obtenidos', [
                'total_destinatarios' => count($destinatarios),
                'destinatarios' => $destinatarios
            ]);

            return array_values($destinatarios);
        } catch (\Exception $e) {
            Log::error('Error obteniendo destinatarios', ['error' => $e->getMessage()]);

            // Fallback a emails de prueba
            return self::obtenerEmailsPrueba();
        }
    }

    /**
     * Obtener emails de prueba desde .env
     */
    public static function obtenerEmailsPrueba(): array
    {
        $emailsPrueba = env('MAIL_TEST_RECIPIENTS', '');

        if (empty($emailsPrueba)) {
            Log::warning('MAIL_TEST_RECIPIENTS no configurado en .env');
            return [];
        }

        $emails = array_map('trim', explode(',', $emailsPrueba));

        // Validar emails
        $emailsValidos = array_filter($emails, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        Log::info('Emails de prueba obtenidos', [
            'emails_raw' => $emailsPrueba,
            'emails_validos' => $emailsValidos
        ]);

        return array_values($emailsValidos);
    }
}
