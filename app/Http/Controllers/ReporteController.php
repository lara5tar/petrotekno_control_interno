<?php

namespace App\Http\Controllers;

use App\Enums\EstadoVehiculo;
use App\Models\Vehiculo;
use App\Models\Kilometraje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Mostrar el índice de reportes disponibles
     */
    public function index()
    {
        $this->checkPermission('ver_reportes');

        // Obtener vehículos disponibles para el dropdown de PDF
        $vehiculosDisponibles = Vehiculo::select('id', 'marca', 'modelo', 'anio', 'placas')
            ->orderBy('marca')
            ->orderBy('modelo')
            ->get();

        return view('reportes.index', compact('vehiculosDisponibles'));
    }

    /**
     * Reporte de inventario de vehículos con último kilometraje
     */
    public function inventarioVehiculos(Request $request)
    {
        $this->checkPermission('ver_reportes');

        // Obtener filtros
        $estatus = $request->get('estatus');
        $marca = $request->get('marca');
        $anio = $request->get('anio');
        $formato = $request->get('formato', 'html'); // html, excel, pdf

        // Query base para vehículos con su último kilometraje
        $query = Vehiculo::select([
            'vehiculos.id',
            'vehiculos.marca',
            'vehiculos.modelo',
            'vehiculos.anio',
            'vehiculos.placas',
            'vehiculos.n_serie',
            'vehiculos.estatus',
            'vehiculos.kilometraje_actual',
            'vehiculos.intervalo_km_motor',
            'vehiculos.intervalo_km_transmision',
            'vehiculos.intervalo_km_hidraulico',
            'vehiculos.created_at'
        ])
        ->leftJoin('kilometrajes as k', function($join) {
            $join->on('vehiculos.id', '=', 'k.vehiculo_id')
                 ->whereRaw('k.id = (SELECT MAX(id) FROM kilometrajes WHERE vehiculo_id = vehiculos.id)');
        })
        ->addSelect([
            'k.kilometraje as ultimo_kilometraje_registrado',
            'k.fecha_captura as fecha_ultimo_kilometraje',
            'k.observaciones as observaciones_ultimo_km'
        ]);

        // Aplicar filtros
        if ($estatus) {
            $query->where('vehiculos.estatus', $estatus);
        }

        if ($marca) {
            $query->where('vehiculos.marca', 'like', "%{$marca}%");
        }

        if ($anio) {
            $query->where('vehiculos.anio', $anio);
        }

        // Ordenar por marca, modelo y año
        $vehiculos = $query->orderBy('vehiculos.marca')
                          ->orderBy('vehiculos.modelo')
                          ->orderBy('vehiculos.anio')
                          ->get();

        // Obtener datos para filtros
        $marcasDisponibles = Vehiculo::distinct()
            ->pluck('marca')
            ->sort()
            ->values();

        $aniosDisponibles = Vehiculo::distinct()
            ->pluck('anio')
            ->sort()
            ->values();

        $estatusDisponibles = EstadoVehiculo::cases();

        // Estadísticas del reporte
        $estadisticas = [
            'total_vehiculos' => $vehiculos->count(),
            'por_estatus' => $vehiculos->groupBy('estatus')->map->count(),
            'kilometraje_total' => $vehiculos->sum('kilometraje_actual'),
            'kilometraje_promedio' => $vehiculos->avg('kilometraje_actual'),
            'vehiculos_con_kilometraje_registrado' => $vehiculos->whereNotNull('ultimo_kilometraje_registrado')->count(),
            'vehiculos_sin_kilometraje_registrado' => $vehiculos->whereNull('ultimo_kilometraje_registrado')->count(),
            // Agregar estadísticas específicas por estado para la vista PDF
            'vehiculos_disponibles' => $vehiculos->where('estatus', EstadoVehiculo::DISPONIBLE)->count(),
            'vehiculos_asignados' => $vehiculos->where('estatus', EstadoVehiculo::ASIGNADO)->count(),
            'vehiculos_mantenimiento' => $vehiculos->where('estatus', EstadoVehiculo::EN_MANTENIMIENTO)->count(),
            'vehiculos_fuera_servicio' => $vehiculos->where('estatus', EstadoVehiculo::FUERA_DE_SERVICIO)->count(),
            'vehiculos_baja' => $vehiculos->where('estatus', EstadoVehiculo::BAJA)->count(),
        ];

        // Procesar cada vehículo para agregar información adicional
        $vehiculos = $vehiculos->map(function($vehiculo) {
            // Determinar estado del enum
            $vehiculo->estado_enum = $vehiculo->estatus instanceof EstadoVehiculo 
                ? $vehiculo->estatus 
                : EstadoVehiculo::fromValue($vehiculo->estatus);
            
            // Convertir fecha_ultimo_kilometraje a Carbon si es string
            if ($vehiculo->fecha_ultimo_kilometraje && is_string($vehiculo->fecha_ultimo_kilometraje)) {
                $vehiculo->fecha_ultimo_kilometraje = \Carbon\Carbon::parse($vehiculo->fecha_ultimo_kilometraje);
            }
            
            // Calcular diferencia entre kilometraje actual y último registrado
            if ($vehiculo->ultimo_kilometraje_registrado) {
                $vehiculo->diferencia_kilometraje = $vehiculo->kilometraje_actual - $vehiculo->ultimo_kilometraje_registrado;
            } else {
                $vehiculo->diferencia_kilometraje = null;
            }

            // Determinar si necesita registro de kilometraje (más de 7 días sin registrar)
            $vehiculo->necesita_registro_km = false;
            if ($vehiculo->fecha_ultimo_kilometraje) {
                $diasSinRegistro = now()->diffInDays($vehiculo->fecha_ultimo_kilometraje);
                $vehiculo->necesita_registro_km = $diasSinRegistro > 7;
                $vehiculo->dias_sin_registro = $diasSinRegistro;
            } else {
                $vehiculo->necesita_registro_km = true;
                $vehiculo->dias_sin_registro = null;
            }

            return $vehiculo;
        });

        // Manejar diferentes formatos de respuesta
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'vehiculos' => $vehiculos,
                    'estadisticas' => $estadisticas,
                    'filtros' => [
                        'marcas' => $marcasDisponibles,
                        'anios' => $aniosDisponibles,
                        'estatus' => $estatusDisponibles,
                    ]
                ]
            ]);
        }

        // Exportar a Excel si se solicita
        if ($formato === 'excel') {
            return $this->exportarInventarioExcel($vehiculos, $estadisticas);
        }

        // Exportar a PDF si se solicita
        if ($formato === 'pdf') {
            return $this->exportarInventarioPdf($vehiculos, $estadisticas, [
                'estatus' => $estatus,
                'marca' => $marca,
                'anio' => $anio
            ]);
        }

        // Vista HTML por defecto
        return view('reportes.inventario-vehiculos', compact(
            'vehiculos',
            'estadisticas',
            'marcasDisponibles',
            'aniosDisponibles',
            'estatusDisponibles',
            'estatus',
            'marca',
            'anio'
        ))->with([
            'filtrosAplicados' => [
                'estatus' => $estatus,
                'marca' => $marca,
                'anio' => $anio,
                'total_filtros' => collect([$estatus, $marca, $anio])->filter()->count()
            ]
        ]);
    }

    /**
     * Exportar inventario a Excel
     */
    private function exportarInventarioExcel($vehiculos, $estadisticas)
    {
        // Esta funcionalidad se puede implementar con Laravel Excel
        // Por ahora devolvemos un CSV básico
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="inventario_vehiculos_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($vehiculos) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fputs($file, "\xEF\xBB\xBF");
            
            // Encabezados
            fputcsv($file, [
                'ID',
                'Marca',
                'Modelo',
                'Año',
                'Placas',
                'No. Serie',
                'Estatus',
                'Kilometraje Actual',
                'Último Km Registrado',
                'Fecha Último Registro',
                'Diferencia Km',
                'Días Sin Registro',
                'Necesita Registro'
            ], ';');

            // Datos
            foreach ($vehiculos as $vehiculo) {
                fputcsv($file, [
                    $vehiculo->id,
                    $vehiculo->marca,
                    $vehiculo->modelo,
                    $vehiculo->anio,
                    $vehiculo->placas,
                    $vehiculo->n_serie,
                    $vehiculo->estado_enum->nombre(),
                    number_format($vehiculo->kilometraje_actual, 0, ',', '.'),
                    $vehiculo->ultimo_kilometraje_registrado ? number_format($vehiculo->ultimo_kilometraje_registrado, 0, ',', '.') : 'Sin registro',
                    $vehiculo->fecha_ultimo_kilometraje ? $vehiculo->fecha_ultimo_kilometraje->format('d/m/Y') : 'Sin registro',
                    $vehiculo->diferencia_kilometraje !== null ? number_format($vehiculo->diferencia_kilometraje, 0, ',', '.') : 'N/A',
                    $vehiculo->dias_sin_registro ?? 'Sin registro',
                    $vehiculo->necesita_registro_km ? 'Sí' : 'No'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar inventario a PDF
     */
    private function exportarInventarioPdf($vehiculos, $estadisticas, $filtros = [])
    {
        // Configurar DomPDF
        $pdf = Pdf::loadView('reportes.inventario-vehiculos-pdf', compact(
            'vehiculos',
            'estadisticas',
            'filtros'
        ));
        
        // Configurar opciones del PDF
        $pdf->setPaper('A4', 'landscape'); // Horizontal para mejor visualización de la tabla
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial'
        ]);
        
        // Generar nombre del archivo con fecha
        $fecha = now()->format('Y-m-d_H-i-s');
        $nombreArchivo = "inventario_vehiculos_{$fecha}.pdf";
        
        // Descargar el PDF
        return $pdf->download($nombreArchivo);
    }

    /**
     * Reporte de vehículos disponibles
     */
    public function vehiculosDisponibles(Request $request)
    {
        $request->merge(['estatus' => EstadoVehiculo::DISPONIBLE->value]);
        return $this->inventarioVehiculos($request);
    }

    /**
     * Reporte de vehículos asignados
     */
    public function vehiculosAsignados(Request $request)
    {
        $request->merge(['estatus' => EstadoVehiculo::ASIGNADO->value]);
        return $this->inventarioVehiculos($request);
    }

    /**
     * Reporte de vehículos en mantenimiento
     */
    public function vehiculosEnMantenimiento(Request $request)
    {
        $request->merge(['estatus' => EstadoVehiculo::EN_MANTENIMIENTO->value]);
        return $this->inventarioVehiculos($request);
    }

    /**
     * Reporte de vehículos fuera de servicio
     */
    public function vehiculosFueraServicio(Request $request)
    {
        $request->merge(['estatus' => EstadoVehiculo::FUERA_DE_SERVICIO->value]);
        return $this->inventarioVehiculos($request);
    }

    /**
     * Reporte de vehículos dados de baja
     */
    public function vehiculosBaja(Request $request)
    {
        $request->merge(['estatus' => EstadoVehiculo::BAJA->value]);
        return $this->inventarioVehiculos($request);
    }

    /**
     * Reporte de historial de obras por vehículo
     */
    public function historialObrasVehiculo(Request $request)
    {
        $this->checkPermission('ver_reportes');

        // Obtener filtros
        $vehiculoId = $request->get('vehiculo_id');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $estadoAsignacion = $request->get('estado_asignacion');
        $obraId = $request->get('obra_id');
        $formato = $request->get('formato', 'html');

        // Query base para asignaciones con relaciones
        $query = \App\Models\AsignacionObra::with([
            'vehiculo:id,marca,modelo,anio,placas,n_serie',
            'obra:id,nombre_obra,estatus,fecha_inicio,fecha_fin,encargado_id',
            'obra.encargado:id,nombre,apellido_paterno,apellido_materno',
            'operador:id,nombre,apellido_paterno,apellido_materno'
        ]);

        // Aplicar filtros
        if ($vehiculoId) {
            $query->where('vehiculo_id', $vehiculoId);
        }

        if ($fechaInicio) {
            $query->where('fecha_asignacion', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha_asignacion', '<=', $fechaFin);
        }

        if ($estadoAsignacion) {
            $query->where('estado', $estadoAsignacion);
        }

        if ($obraId) {
            $query->where('obra_id', $obraId);
        }

        // Ordenar por fecha de asignación más reciente
        $asignaciones = $query->orderBy('fecha_asignacion', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->get();

        // Obtener datos para filtros
        $vehiculosDisponibles = \App\Models\Vehiculo::select('id', 'marca', 'modelo', 'anio', 'placas')
            ->orderBy('marca')
            ->orderBy('modelo')
            ->get();

        $obrasDisponibles = \App\Models\Obra::select('id', 'nombre_obra as nombre')
            ->orderBy('nombre_obra')
            ->get();

        $estadosAsignacion = [
            \App\Models\AsignacionObra::ESTADO_ACTIVA,
            \App\Models\AsignacionObra::ESTADO_LIBERADA,
            \App\Models\AsignacionObra::ESTADO_TRANSFERIDA
        ];

        // Calcular estadísticas
        $estadisticas = [
            'total_asignaciones' => $asignaciones->count(),
            'asignaciones_activas' => $asignaciones->where('estado', \App\Models\AsignacionObra::ESTADO_ACTIVA)->count(),
            'asignaciones_liberadas' => $asignaciones->where('estado', \App\Models\AsignacionObra::ESTADO_LIBERADA)->count(),
            'asignaciones_transferidas' => $asignaciones->where('estado', \App\Models\AsignacionObra::ESTADO_TRANSFERIDA)->count(),
            'vehiculos_involucrados' => $asignaciones->pluck('vehiculo_id')->unique()->count(),
            'obras_involucradas' => $asignaciones->pluck('obra_id')->unique()->count(),
            'kilometraje_total_recorrido' => $asignaciones->whereNotNull('kilometraje_final')
                ->sum(function($asignacion) {
                    return $asignacion->kilometraje_final - ($asignacion->kilometraje_inicial ?? 0);
                }),
            'promedio_dias_asignacion' => $asignaciones->where('estado', \App\Models\AsignacionObra::ESTADO_LIBERADA)
                ->avg(function($asignacion) {
                    if ($asignacion->fecha_liberacion && $asignacion->fecha_asignacion) {
                        return $asignacion->fecha_asignacion->diffInDays($asignacion->fecha_liberacion);
                    }
                    return 0;
                })
        ];

        // Procesar asignaciones para agregar información calculada
        $asignaciones = $asignaciones->map(function($asignacion) {
            // Calcular duración
            if ($asignacion->fecha_liberacion && $asignacion->fecha_asignacion) {
                $asignacion->duracion_dias = $asignacion->fecha_asignacion->diffInDays($asignacion->fecha_liberacion);
            } elseif ($asignacion->estado === \App\Models\AsignacionObra::ESTADO_ACTIVA) {
                $asignacion->duracion_dias = $asignacion->fecha_asignacion->diffInDays(now());
            } else {
                $asignacion->duracion_dias = null;
            }

            // Calcular kilometraje recorrido
            if ($asignacion->kilometraje_final && $asignacion->kilometraje_inicial) {
                $asignacion->kilometraje_recorrido = $asignacion->kilometraje_final - $asignacion->kilometraje_inicial;
            } else {
                $asignacion->kilometraje_recorrido = null;
            }

            // Estado formateado
            $asignacion->estado_formateado = match($asignacion->estado) {
                \App\Models\AsignacionObra::ESTADO_ACTIVA => 'Activa',
                \App\Models\AsignacionObra::ESTADO_LIBERADA => 'Liberada',
                \App\Models\AsignacionObra::ESTADO_TRANSFERIDA => 'Transferida',
                default => ucfirst($asignacion->estado)
            };

            return $asignacion;
        });

        // Manejar respuesta JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'asignaciones' => $asignaciones,
                    'estadisticas' => $estadisticas,
                    'filtros' => [
                        'vehiculos' => $vehiculosDisponibles,
                        'obras' => $obrasDisponibles,
                        'estados' => $estadosAsignacion,
                    ]
                ]
            ]);
        }

        // Exportar a Excel si se solicita
        if ($formato === 'excel') {
            return $this->exportarHistorialObrasExcel($asignaciones, $estadisticas);
        }

        // Exportar a PDF si se solicita
        if ($formato === 'pdf') {
            // Para PDF, es obligatorio seleccionar un vehículo específico
            if (!$vehiculoId) {
                return redirect()->back()->with('error', 'Para generar el PDF debe seleccionar un vehículo específico.');
            }
            
            return $this->exportarHistorialObrasPdf($asignaciones, $estadisticas, [
                'vehiculo_id' => $vehiculoId,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'estado_asignacion' => $estadoAsignacion,
                'obra_id' => $obraId
            ]);
        }

        // Vista HTML por defecto
        return view('reportes.historial-obras-vehiculo', compact(
            'asignaciones',
            'estadisticas',
            'vehiculosDisponibles',
            'obrasDisponibles',
            'estadosAsignacion',
            'vehiculoId',
            'fechaInicio',
            'fechaFin',
            'estadoAsignacion',
            'obraId'
        ))->with([
            'filtrosAplicados' => [
                'vehiculo_id' => $vehiculoId,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'estado_asignacion' => $estadoAsignacion,
                'obra_id' => $obraId,
                'total_filtros' => collect([$vehiculoId, $fechaInicio, $fechaFin, $estadoAsignacion, $obraId])->filter()->count()
            ]
        ]);
    }

    /**
     * Exportar historial de obras a Excel
     */
    private function exportarHistorialObrasExcel($asignaciones, $estadisticas)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="historial_obras_vehiculo_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($asignaciones) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fputs($file, "\xEF\xBB\xBF");
            
            // Encabezados
            fputcsv($file, [
                'ID Asignación',
                'Vehículo',
                'Placas',
                'Obra',
                'Encargado',
                'Operador',
                'Fecha Asignación',
                'Fecha Liberación',
                'Estado',
                'Duración (días)',
                'Km Inicial',
                'Km Final',
                'Km Recorrido',
                'Observaciones'
            ], ';');

            // Datos
            foreach ($asignaciones as $asignacion) {
                fputcsv($file, [
                    $asignacion->id,
                    $asignacion->vehiculo ? "{$asignacion->vehiculo->marca} {$asignacion->vehiculo->modelo} {$asignacion->vehiculo->anio}" : 'Sin vehículo',
                    $asignacion->vehiculo->placas ?? 'Sin placas',
                    $asignacion->obra->nombre_obra ?? 'Sin obra',
                    $asignacion->obra && $asignacion->obra->encargado ? 
                        "{$asignacion->obra->encargado->nombre} {$asignacion->obra->encargado->apellido_paterno}" : 'Sin encargado',
                    $asignacion->operador ? 
                        "{$asignacion->operador->nombre} {$asignacion->operador->apellido_paterno}" : 'Sin operador',
                    $asignacion->fecha_asignacion ? $asignacion->fecha_asignacion->format('d/m/Y') : 'Sin fecha',
                    $asignacion->fecha_liberacion ? $asignacion->fecha_liberacion->format('d/m/Y') : 'Sin fecha',
                    $asignacion->estado_formateado,
                    $asignacion->duracion_dias ?? 'N/A',
                    $asignacion->kilometraje_inicial ?? 'Sin registro',
                    $asignacion->kilometraje_final ?? 'Sin registro',
                    $asignacion->kilometraje_recorrido ? number_format($asignacion->kilometraje_recorrido, 0, ',', '.') : 'N/A',
                    $asignacion->observaciones ?? 'Sin observaciones'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar historial de obras a PDF
     */
    private function exportarHistorialObrasPdf($asignaciones, $estadisticas, $filtros = [])
    {
        // Obtener información del vehículo para el PDF
        $vehiculo = null;
        if (isset($filtros['vehiculo_id'])) {
            $vehiculo = \App\Models\Vehiculo::find($filtros['vehiculo_id']);
        }
        
        // Configurar DomPDF
        $pdf = Pdf::loadView('reportes.historial-obras-vehiculo-pdf', compact(
            'asignaciones',
            'estadisticas',
            'filtros',
            'vehiculo'
        ));
        
        // Configurar opciones del PDF
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial'
        ]);
        
        // Generar nombre del archivo específico para el vehículo
        $fecha = now()->format('Y-m-d_H-i-s');
        if ($vehiculo) {
            $vehiculoInfo = $vehiculo->marca . '_' . $vehiculo->modelo . '_' . $vehiculo->placas;
            $vehiculoInfo = str_replace([' ', '-', '.'], '_', $vehiculoInfo);
            $nombreArchivo = "historial_obras_{$vehiculoInfo}_{$fecha}.pdf";
        } else {
            $nombreArchivo = "historial_obras_vehiculo_{$fecha}.pdf";
        }
        
        // Descargar el PDF
        return $pdf->download($nombreArchivo);
    }

    /**
     * Verificar permisos de manera centralizada
     */
    private function checkPermission($ability)
    {
        if (!Auth::user()->hasPermission($ability)) {
            abort(403, 'No tienes permisos para acceder a los reportes.');
        }
    }
}
