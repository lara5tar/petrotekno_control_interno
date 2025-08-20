<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Mantenimiento;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class MantenimientoAlertasController extends Controller
{
    /**
     * Umbral de kilómetros para considerar una alerta como "Próximo"
     */
    const UMBRAL_PROXIMO_KM = 1000;

    /**
     * Most    /**
     * Obtener estadísticas de alertas para el ViewComposer
     */
    public static function getEstadisticasAlertas(): array
    {
        $controller = new self();
        
        // Obtener todos los vehículos activos con sus relaciones
        $vehiculos = Vehiculo::with(['mantenimientos' => function($query) {
            $query->orderBy('fecha_fin', 'desc')->orderBy('kilometraje_servicio', 'desc');
        }])->get();

        $alertasUnificadas = [];

        foreach ($vehiculos as $vehiculo) {
            // Calcular alertas de mantenimiento
            $sistemas = [
                'motor' => [
                    'sistema' => Mantenimiento::SISTEMA_MOTOR,
                    'intervalo' => $vehiculo->intervalo_km_motor ?? 10000,
                    'icono' => '🔧',
                    'nombre' => 'Motor'
                ],
                'transmision' => [
                    'sistema' => Mantenimiento::SISTEMA_TRANSMISION,
                    'intervalo' => $vehiculo->intervalo_km_transmision ?? 20000,
                    'icono' => '⚙️',
                    'nombre' => 'Transmisión'
                ],
                'hidraulico' => [
                    'sistema' => Mantenimiento::SISTEMA_HIDRAULICO,
                    'intervalo' => $vehiculo->intervalo_km_hidraulico ?? 15000,
                    'icono' => '🛢️',
                    'nombre' => 'Hidráulico'
                ]
            ];

            foreach ($sistemas as $nombreSistema => $sistema) {
                $alerta = $controller->calcularAlertaMantenimiento(
                    $vehiculo,
                    $sistema['sistema'],
                    $sistema['intervalo']
                );
                
                // Solo agregar si necesita atención (no está OK)
                if ($alerta['estado'] !== 'OK') {
                    $alertasUnificadas[] = [
                        'tipo' => 'mantenimiento',
                        'estado' => $alerta['estado']
                    ];
                }
            }

            // Calcular alertas de documentos
            $documentos = [
                'poliza' => [
                    'campo' => $vehiculo->poliza_vencimiento,
                    'nombre' => 'Póliza de Seguro',
                    'icono' => '🛡️'
                ],
                'derecho' => [
                    'campo' => $vehiculo->derecho_vencimiento,
                    'nombre' => 'Derecho Vehicular',
                    'icono' => '📋'
                ]
            ];

            foreach ($documentos as $tipoDoc => $documento) {
                $alertaDoc = $controller->calcularAlertaDocumento(
                    $documento['campo'],
                    $documento['nombre']
                );

                // Solo agregar si necesita atención (no está OK ni Sin Fecha)
                if (!in_array($alertaDoc['estado'], ['OK', 'Sin Fecha'])) {
                    $alertasUnificadas[] = [
                        'tipo' => 'documento',
                        'estado' => $alertaDoc['estado']
                    ];
                }
            }
        }
        
        // Calcular estadísticas
        $total = count($alertasUnificadas);
        $vencidas = collect($alertasUnificadas)->where('estado', 'Vencido')->count();
        
        return [
            'alertasCount' => $total,
            'tieneAlertasUrgentes' => $vencidas > 0
        ];
    }

    /**
     * Mostrar todas las alertas de mantenimiento
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
                'alertas' => [],
                'documentos' => []
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

            // Calcular alertas de documentos
            $vehiculoData['documentos']['poliza'] = $this->calcularAlertaDocumento(
                $vehiculo->poliza_vencimiento, 
                'Póliza de Seguro'
            );
            
            $vehiculoData['documentos']['derecho'] = $this->calcularAlertaDocumento(
                $vehiculo->derecho_vencimiento, 
                'Derecho Vehicular'
            );

            $alertas[] = $vehiculoData;
        }

        // Ordenar los vehículos priorizando los que tienen alertas más urgentes
        $alertas = collect($alertas)->sortBy(function ($vehiculo) {
            $prioridad = 0;
            
            // Considerar alertas de mantenimiento
            foreach ($vehiculo['alertas'] as $alerta) {
                if ($alerta['estado'] === 'Vencido') {
                    $prioridad += 3;
                } elseif ($alerta['estado'] === 'Próximo') {
                    $prioridad += 2;
                } else {
                    $prioridad += 1;
                }
            }
            
            // Considerar alertas de documentos
            foreach ($vehiculo['documentos'] as $documento) {
                if ($documento['estado'] === 'Vencido') {
                    $prioridad += 3;
                } elseif ($documento['estado'] === 'Próximo a Vencer') {
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
     * Mostrar la vista unificada de todas las alertas (mantenimiento + documentos)
     */
    public function unificada(): View
    {
        // Obtener todos los vehículos activos con sus relaciones
        $vehiculos = Vehiculo::with(['mantenimientos' => function($query) {
            $query->orderBy('fecha_fin', 'desc')->orderBy('kilometraje_servicio', 'desc');
        }])->get();

        $alertasUnificadas = [];

        foreach ($vehiculos as $vehiculo) {
            // Calcular alertas de mantenimiento
            $sistemas = [
                'motor' => [
                    'sistema' => Mantenimiento::SISTEMA_MOTOR,
                    'intervalo' => $vehiculo->intervalo_km_motor ?? 10000,
                    'icono' => '🔧',
                    'nombre' => 'Motor'
                ],
                'transmision' => [
                    'sistema' => Mantenimiento::SISTEMA_TRANSMISION,
                    'intervalo' => $vehiculo->intervalo_km_transmision ?? 20000,
                    'icono' => '⚙️',
                    'nombre' => 'Transmisión'
                ],
                'hidraulico' => [
                    'sistema' => Mantenimiento::SISTEMA_HIDRAULICO,
                    'intervalo' => $vehiculo->intervalo_km_hidraulico ?? 15000,
                    'icono' => '🛢️',
                    'nombre' => 'Hidráulico'
                ]
            ];

            foreach ($sistemas as $nombreSistema => $sistema) {
                $alerta = $this->calcularAlertaMantenimiento(
                    $vehiculo,
                    $sistema['sistema'],
                    $sistema['intervalo']
                );
                
                // Solo agregar si necesita atención (no está OK)
                if ($alerta['estado'] !== 'OK') {
                    $alertasUnificadas[] = [
                        'tipo' => 'mantenimiento',
                        'categoria' => 'Mantenimiento',
                        'subtipo' => $sistema['nombre'],
                        'icono' => $sistema['icono'],
                        'vehiculo_id' => $vehiculo->id,
                        'vehiculo_info' => [
                            'marca' => $vehiculo->marca,
                            'modelo' => $vehiculo->modelo,
                            'placas' => $vehiculo->placas,
                            'kilometraje_actual' => $vehiculo->kilometraje_actual
                        ],
                        'estado' => $alerta['estado'],
                        'urgencia' => $alerta['estado'] === 'Vencido' ? 3 : ($alerta['estado'] === 'Próximo' ? 2 : 1),
                        'descripcion' => $this->generarDescripcionMantenimiento($alerta, $sistema['nombre']),
                        'fecha_limite' => null,
                        'dias_restantes' => null,
                        'kilometros_restantes' => $alerta['kilometros_restantes'],
                        'detalles' => $alerta
                    ];
                }
            }

            // Calcular alertas de documentos
            $documentos = [
                'poliza' => [
                    'campo' => $vehiculo->poliza_vencimiento,
                    'nombre' => 'Póliza de Seguro',
                    'icono' => '🛡️'
                ],
                'derecho' => [
                    'campo' => $vehiculo->derecho_vencimiento,
                    'nombre' => 'Derecho Vehicular',
                    'icono' => '📋'
                ]
            ];

            foreach ($documentos as $tipoDoc => $documento) {
                $alertaDoc = $this->calcularAlertaDocumento(
                    $documento['campo'],
                    $documento['nombre']
                );

                // Solo agregar si necesita atención (no está OK ni Sin Fecha)
                if (!in_array($alertaDoc['estado'], ['OK', 'Sin Fecha'])) {
                    $alertasUnificadas[] = [
                        'tipo' => 'documento',
                        'categoria' => 'Documentos',
                        'subtipo' => $documento['nombre'],
                        'icono' => $documento['icono'],
                        'vehiculo_id' => $vehiculo->id,
                        'vehiculo_info' => [
                            'marca' => $vehiculo->marca,
                            'modelo' => $vehiculo->modelo,
                            'placas' => $vehiculo->placas,
                            'kilometraje_actual' => $vehiculo->kilometraje_actual
                        ],
                        'estado' => $alertaDoc['estado'],
                        'urgencia' => $alertaDoc['estado'] === 'Vencido' ? 3 : ($alertaDoc['estado'] === 'Próximo a Vencer' ? 2 : 1),
                        'descripcion' => $this->generarDescripcionDocumento($alertaDoc),
                        'fecha_limite' => $alertaDoc['fecha_vencimiento'],
                        'dias_restantes' => $alertaDoc['dias_restantes'],
                        'kilometros_restantes' => null,
                        'detalles' => $alertaDoc
                    ];
                }
            }
        }

        // Ordenar por urgencia (más urgente primero) y luego por días/km restantes
        $alertasUnificadas = collect($alertasUnificadas)->sortBy([
            ['urgencia', 'desc'],
            function ($alerta) {
                if ($alerta['tipo'] === 'documento') {
                    return $alerta['dias_restantes'] ?? 999;
                } else {
                    return abs($alerta['kilometros_restantes']) ?? 999;
                }
            }
        ])->values()->toArray();

        // Calcular estadísticas
        $estadisticas = [
            'total' => count($alertasUnificadas),
            'vencidas' => collect($alertasUnificadas)->where('estado', 'Vencido')->count(),
            'proximas' => collect($alertasUnificadas)->whereIn('estado', ['Próximo', 'Próximo a Vencer'])->count(),
            'mantenimiento' => collect($alertasUnificadas)->where('tipo', 'mantenimiento')->count(),
            'documentos' => collect($alertasUnificadas)->where('tipo', 'documento')->count(),
        ];

        return view('alertas.unificada', compact('alertasUnificadas', 'estadisticas'));
    }

    /**
     * Generar descripción legible para alertas de mantenimiento
     */
    private function generarDescripcionMantenimiento(array $alerta, string $sistema): string
    {
        if ($alerta['kilometros_restantes'] <= 0) {
            $excedido = abs($alerta['kilometros_restantes']);
            return "Mantenimiento de {$sistema} vencido. Excedido por " . number_format($excedido) . " km.";
        } else {
            return "Mantenimiento de {$sistema} próximo. Faltan " . number_format($alerta['kilometros_restantes']) . " km.";
        }
    }

    /**
     * Generar descripción legible para alertas de documentos
     */
    private function generarDescripcionDocumento(array $documento): string
    {
        if ($documento['dias_restantes'] < 0) {
            $vencido = abs($documento['dias_restantes']);
            return "{$documento['tipo']} vencido hace {$vencido} día(s).";
        } else {
            return "{$documento['tipo']} vence en {$documento['dias_restantes']} día(s).";
        }
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
        $umbralKm = config('alertas.mantenimiento_km_umbral', self::UMBRAL_PROXIMO_KM);
        
        if ($kilometrosRestantes <= 0) {
            return 'Vencido';
        } elseif ($kilometrosRestantes <= $umbralKm) {
            return 'Próximo';
        } else {
            return 'OK';
        }
    }

    /**
     * Calcula la alerta de documento para un campo de fecha específico
     */
    private function calcularAlertaDocumento($fechaVencimiento, string $tipoDocumento): array
    {
        if (!$fechaVencimiento) {
            return [
                'tipo' => $tipoDocumento,
                'fecha_vencimiento' => null,
                'dias_restantes' => null,
                'estado' => 'Sin Fecha',
                'fecha_vencimiento_formateada' => 'No especificada'
            ];
        }

        $ahora = Carbon::now();
        $fechaVencimiento = Carbon::parse($fechaVencimiento);
        $diasRestantes = $ahora->diffInDays($fechaVencimiento, false);

        // Determinar el estado basado en los días restantes
        $estado = $this->determinarEstadoDocumento($diasRestantes);

        return [
            'tipo' => $tipoDocumento,
            'fecha_vencimiento' => $fechaVencimiento,
            'dias_restantes' => round($diasRestantes),
            'estado' => $estado,
            'fecha_vencimiento_formateada' => $fechaVencimiento->format('d/m/Y')
        ];
    }

    /**
     * Determina el estado de alerta basado en los días restantes para vencimiento
     */
    private function determinarEstadoDocumento(float $diasRestantes): string
    {
        $umbralDias = config('alertas.vencimiento_documentos_dias', 30);

        if ($diasRestantes < 0) {
            return 'Vencido';
        } elseif ($diasRestantes <= $umbralDias) {
            return 'Próximo a Vencer';
        } else {
            return 'OK';
        }
    }
}
