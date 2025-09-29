<?php

namespace App\Http\Controllers;

use App\Enums\EstadoVehiculo;
use App\Models\Vehiculo;
use App\Models\Kilometraje;
use App\Traits\PdfGeneratorTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    use PdfGeneratorTrait;
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

        // Obtener operadores disponibles para el dropdown de PDF
        $operadoresDisponibles = \App\Models\Personal::where('estatus', 'activo')
            ->orderBy('nombre_completo')
            ->get();

        return view('reportes.index', compact('vehiculosDisponibles', 'operadoresDisponibles'));
    }

    /**
     * Reporte de inventario de vehículos
     * Genera un reporte completo del inventario de vehículos con filtros y estadísticas
     */
    public function inventarioVehiculos(Request $request)
    {
        $this->checkPermission('ver_reportes');

        // Obtener filtros
        $estatus = $request->get('estatus');
        $marca = $request->get('marca');
        $anio = $request->get('anio');
        $formato = $request->get('formato', 'html'); // html, excel, pdf

        // Query base para vehículos
        $query = Vehiculo::select([
            'vehiculos.id',
            'vehiculos.tipo_activo_id',
            'vehiculos.marca',
            'vehiculos.modelo',
            'vehiculos.anio',
            'vehiculos.placas',
            'vehiculos.n_serie',
            'vehiculos.estatus',
            'vehiculos.estado',
            'vehiculos.municipio',
            'vehiculos.kilometraje_actual',
            'vehiculos.intervalo_km_motor',
            'vehiculos.intervalo_km_transmision',
            'vehiculos.intervalo_km_hidraulico',
            'vehiculos.created_at'
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
        $vehiculos = $query->with([
                            'tipoActivo',
                            'asignacionesObra' => function($query) {
                                $query->where('estado', 'activa')
                                      ->with('obra:id,nombre_obra');
                            }
                        ])
                          ->orderBy('vehiculos.marca')
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
            // Estadísticas principales
            'total_vehiculos' => $vehiculos->count(),
            'total' => $vehiculos->count(), // Para la vista PDF
            'por_estatus' => $vehiculos->groupBy('estatus')->map->count(),
            'kilometraje_total' => $vehiculos->sum('kilometraje_actual'),
            'kilometraje_promedio' => $vehiculos->avg('kilometraje_actual'),
            
            // Estadísticas específicas por estado para la vista PDF
            'vehiculos_disponibles' => $vehiculos->where('estatus', EstadoVehiculo::DISPONIBLE)->count(),
            'vehiculos_asignados' => $vehiculos->where('estatus', EstadoVehiculo::ASIGNADO)->count(),
            'vehiculos_mantenimiento' => $vehiculos->where('estatus', EstadoVehiculo::EN_MANTENIMIENTO)->count(),
            'vehiculos_fuera_servicio' => $vehiculos->where('estatus', EstadoVehiculo::FUERA_DE_SERVICIO)->count(),
            'vehiculos_baja' => $vehiculos->where('estatus', EstadoVehiculo::BAJA)->count(),
            
            // Mapeo para la vista PDF (estructura esperada por la vista)
            'por_estado' => [
                'disponible' => $vehiculos->where('estatus', EstadoVehiculo::DISPONIBLE)->count(),
                'asignado' => $vehiculos->where('estatus', EstadoVehiculo::ASIGNADO)->count(),
                'mantenimiento' => $vehiculos->where('estatus', EstadoVehiculo::EN_MANTENIMIENTO)->count(),
                'fuera_servicio' => $vehiculos->where('estatus', EstadoVehiculo::FUERA_DE_SERVICIO)->count(),
                'baja' => $vehiculos->where('estatus', EstadoVehiculo::BAJA)->count(),
            ],
            
            // Estadísticas de kilometraje para el PDF
            'vehiculos_con_kilometraje_registrado' => $vehiculos->where('kilometraje_actual', '>', 0)->count(),
            'vehiculos_sin_kilometraje_registrado' => $vehiculos->where('kilometraje_actual', '<=', 0)->count(),
        ];

        // Procesar cada vehículo para agregar información adicional
        $vehiculos = $vehiculos->map(function($vehiculo) {
            // Determinar estado del enum
            $vehiculo->estado_enum = $vehiculo->estatus instanceof EstadoVehiculo 
                ? $vehiculo->estatus 
                : EstadoVehiculo::fromValue($vehiculo->estatus);
            
            // Asegurar que tipoActivo esté disponible incluso si es null
            if (!$vehiculo->relationLoaded('tipoActivo')) {
                $vehiculo->load('tipoActivo');
            }
            
            // Asegurar que el tipo de activo esté disponible para el reporte
            if ($vehiculo->tipoActivo) {
                $vehiculo->tipo_activo_nombre = $vehiculo->tipoActivo->nombre;
            } else {
                $vehiculo->tipo_activo_nombre = 'Sin tipo';
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
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\InventarioVehiculosExport($vehiculos), 
            'inventario_vehiculos_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    /**
     * Exportar inventario a PDF - Usando Trait Unificado
     */
    private function exportarInventarioPdf($vehiculos, $estadisticas, $filtros = [])
    {
        return $this->createInventarioPdf($vehiculos, $estadisticas, $filtros);
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
            'obra.encargado:id,nombre_completo',
            'operador:id,nombre_completo'
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
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\HistorialObrasExport($asignaciones), 
            'historial_obras_vehiculo_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    /**
     * Exportar historial de obras a PDF - Usando Trait Unificado
     */
    private function exportarHistorialObrasPdf($asignaciones, $estadisticas, $filtros = [])
    {
        // Obtener información del vehículo para el PDF
        $vehiculo = null;
        if (isset($filtros['vehiculo_id'])) {
            $vehiculo = \App\Models\Vehiculo::find($filtros['vehiculo_id']);
        }
        
        return $this->createHistorialObrasVehiculoPdf($asignaciones, $estadisticas, $filtros, $vehiculo);
    }

    /**
     * Reporte de historial de obras por operador
     */
    public function historialObrasPorOperador(Request $request)
    {
        $this->checkPermission('ver_reportes');

        // Obtener filtros
        $operadorId = $request->get('operador_id');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $estadoAsignacion = $request->get('estado_asignacion');
        $obraId = $request->get('obra_id');
        $formato = $request->get('formato', 'html');

        // Query base para obtener historial del operador usando la tabla historial_operador_vehiculo
        $query = \App\Models\HistorialOperadorVehiculo::with([
            'vehiculo:id,marca,modelo,anio,placas,n_serie',
            'operadorNuevo:id,nombre_completo',
            'operadorAnterior:id,nombre_completo',
            'usuarioAsigno:id,email',
            'obra:id,nombre_obra,estatus'
        ]);

        // Aplicar filtros
        if ($operadorId) {
            $query->where(function($q) use ($operadorId) {
                $q->where('operador_nuevo_id', $operadorId)
                  ->orWhere('operador_anterior_id', $operadorId);
            });
        }

        if ($fechaInicio) {
            $query->whereDate('fecha_asignacion', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->whereDate('fecha_asignacion', '<=', $fechaFin);
        }

        if ($obraId) {
            $query->where('obra_id', $obraId);
        }

        // Ordenar por fecha más reciente
        $query->orderBy('fecha_asignacion', 'desc');

        $asignaciones = $query->get();

        // Calcular estadísticas
        $estadisticas = [
            'total_movimientos' => $asignaciones->count(),
            'asignaciones_iniciales' => $asignaciones->where('tipo_movimiento', 'asignacion_inicial')->count(),
            'cambios_operador' => $asignaciones->where('tipo_movimiento', 'cambio_operador')->count(),
            'remociones' => $asignaciones->where('tipo_movimiento', 'remocion_operador')->count(),
            'vehiculos_diferentes' => $asignaciones->pluck('vehiculo_id')->unique()->count(),
            'obras_diferentes' => $asignaciones->whereNotNull('obra_id')->pluck('obra_id')->unique()->count()
        ];

        // Obtener datos para los filtros
        $operadoresDisponibles = \App\Models\Personal::where('estatus', 'activo')
            ->whereHas('historialComoOperadorNuevo')
            ->orWhereHas('historialComoOperadorAnterior')
            ->orderBy('nombre_completo')
            ->get();

        $obrasDisponibles = \App\Models\Obra::whereIn('id', 
            $asignaciones->whereNotNull('obra_id')->pluck('obra_id')->unique()
        )->orderBy('nombre_obra')->get();

        // Manejar exportación
        if ($formato === 'excel') {
            return $this->exportarHistorialOperadorExcel($asignaciones, $estadisticas);
        }

        if ($formato === 'pdf') {
            return $this->exportarHistorialOperadorPdf($asignaciones, $estadisticas, [
                'operador_id' => $operadorId,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'estado_asignacion' => $estadoAsignacion,
                'obra_id' => $obraId
            ]);
        }

        // Filtros aplicados para la vista
        $filtrosAplicados = [
            'operador_id' => $operadorId,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado_asignacion' => $estadoAsignacion,
            'obra_id' => $obraId,
            'total_filtros' => collect([$operadorId, $fechaInicio, $fechaFin, $estadoAsignacion, $obraId])->filter()->count()
        ];

        return view('reportes.historial-obras-operador', compact([
            'asignaciones',
            'estadisticas',
            'operadoresDisponibles',
            'obrasDisponibles',
            'filtrosAplicados'
        ]));
    }

    /**
     * Exportar historial de operador a Excel
     */
    private function exportarHistorialOperadorExcel($asignaciones, $estadisticas)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\HistorialOperadorExport($asignaciones), 
            'historial_obras_operador_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    /**
     * Exportar historial de operador a PDF - Usando Trait Unificado
     */
    private function exportarHistorialOperadorPdf($asignaciones, $estadisticas, $filtros = [])
    {
        // Obtener información del operador para el PDF
        $operador = null;
        if (isset($filtros['operador_id'])) {
            $operador = \App\Models\Personal::find($filtros['operador_id']);
        }
        
        return $this->createHistorialObrasOperadorPdf($asignaciones, $estadisticas, $filtros, $operador);
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

    public function historialMantenimientosPorVehiculo(Request $request)
    {
        $this->checkPermission('ver_reportes');

        // Obtener filtros
        $vehiculoId = $request->get('vehiculo_id');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $tipoServicio = $request->get('tipo_servicio'); // Cambio: usar tipo_servicio en lugar de tipo_mantenimiento
        $formato = $request->get('formato', 'html');

        // Query base para obtener mantenimientos
        $query = \App\Models\Mantenimiento::with([
            'vehiculo:id,marca,modelo,anio,placas,n_serie'
        ]);

        // Aplicar filtros
        if ($vehiculoId) {
            $query->where('vehiculo_id', $vehiculoId);
        }

        if ($fechaInicio) {
            $query->whereDate('fecha_inicio', '>=', $fechaInicio); // Cambio: usar fecha_inicio
        }

        if ($fechaFin) {
            $query->whereDate('fecha_fin', '<=', $fechaFin); // Cambio: usar fecha_fin
        }

        if ($tipoServicio) {
            $query->where('tipo_servicio', $tipoServicio); // Cambio: usar tipo_servicio
        }

        // Ordenar por fecha más reciente
        $query->orderBy('fecha_inicio', 'desc'); // Cambio: usar fecha_inicio

        $mantenimientos = $query->get();

        // Calcular estadísticas
        $estadisticas = [
            'total_mantenimientos' => $mantenimientos->count(),
            'mantenimiento_preventivo' => $mantenimientos->where('tipo_servicio', 'PREVENTIVO')->count(),
            'mantenimiento_correctivo' => $mantenimientos->where('tipo_servicio', 'CORRECTIVO')->count(),
            'costo_total' => $mantenimientos->sum('costo'),
            'costo_promedio' => $mantenimientos->count() > 0 ? $mantenimientos->average('costo') : 0,
        ];

        // Obtener vehículos disponibles para los filtros
        $vehiculosDisponibles = \App\Models\Vehiculo::orderBy('marca')->orderBy('modelo')->get();

        if ($formato === 'pdf') {
            // Obtener información del vehículo si se especifica
            $vehiculoInfo = null;
            if ($vehiculoId) {
                $vehiculoInfo = \App\Models\Vehiculo::find($vehiculoId);
            }

            // Usar trait unificado para generar PDF
            return $this->createHistorialMantenimientosPdf(
                $mantenimientos,
                $estadisticas,
                compact('fechaInicio', 'fechaFin') + ['tipo_mantenimiento' => $tipoServicio],
                $vehiculoInfo
            );
        }

        if ($formato === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\MantenimientosExport($mantenimientos), 
                'historial_mantenimientos_vehiculo_' . now()->format('Y_m_d_H_i_s') . '.xlsx'
            );
        }

        return view('reportes.historial-mantenimientos-vehiculo', compact(
            'mantenimientos',
            'estadisticas',
            'vehiculosDisponibles',
            'vehiculoId',
            'fechaInicio',
            'fechaFin',
            'tipoServicio'
        ));
    }

    /**
     * Reporte de kilometrajes de vehículos
     */
    public function kilometrajes(Request $request)
    {
        $this->checkPermission('ver_reportes');

        // Obtener filtros
        $vehiculoId = $request->get('vehiculo_id');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $formato = $request->get('formato', 'html');

        // Query base
        $query = Kilometraje::with(['vehiculo', 'obra'])
            ->join('vehiculos', 'kilometrajes.vehiculo_id', '=', 'vehiculos.id')
            ->select('kilometrajes.*');

        // Aplicar filtros
        if ($vehiculoId) {
            $query->where('kilometrajes.vehiculo_id', $vehiculoId);
        }

        if ($fechaInicio) {
            $query->where('kilometrajes.fecha_captura', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('kilometrajes.fecha_captura', '<=', $fechaFin);
        }

        // Ordenar por vehículo y luego por fecha de creación del registro descendente (más nuevo al más viejo)
        if ($formato === 'pdf') {
            // Para PDF obtener todos los registros sin paginación
            $kilometrajes = $query->orderBy('vehiculos.marca')
                ->orderBy('vehiculos.modelo')
                ->orderBy('kilometrajes.created_at_registro', 'desc')
                ->get();
        } else {
            // Para vista HTML usar paginación
            $kilometrajes = $query->orderBy('vehiculos.marca')
                ->orderBy('vehiculos.modelo')
                ->orderBy('kilometrajes.created_at_registro', 'desc')
                ->paginate(50);
        }

        // Obtener vehículos disponibles para el filtro
        $vehiculosDisponibles = Vehiculo::select('id', 'marca', 'modelo', 'anio', 'placas')
            ->orderBy('marca')
            ->orderBy('modelo')
            ->get();

        // Estadísticas generales
        if ($formato === 'pdf') {
            $estadisticas = [
                'total_registros' => $kilometrajes->count(),
                'vehiculos_con_kilometraje' => Kilometraje::distinct('vehiculo_id')->count(),
                'kilometraje_promedio' => Kilometraje::avg('kilometraje')
            ];
        } else {
            $estadisticas = [
                'total_registros' => $kilometrajes->total(),
                'vehiculos_con_kilometraje' => Kilometraje::distinct('vehiculo_id')->count(),
                'kilometraje_promedio' => Kilometraje::avg('kilometraje')
            ];
        }

        // Si se solicita Excel, generar y retornar
        if ($formato === 'excel') {
            return Excel::download(
                new \App\Exports\KilometrajesExport($kilometrajes), 
                'reporte_kilometrajes_' . date('Y-m-d_H-i-s') . '.xlsx'
            );
        }

        // Si se solicita PDF, generar y retornar
        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('pdf.reportes.kilometrajes', compact(
                'kilometrajes',
                'estadisticas',
                'vehiculoId',
                'fechaInicio',
                'fechaFin'
            ))->setPaper('a4', 'landscape');

            return $pdf->download('reporte-kilometrajes-' . date('Y-m-d') . '.pdf');
        }

        return view('reportes.kilometrajes', compact(
            'kilometrajes',
            'estadisticas',
            'vehiculosDisponibles',
            'vehiculoId',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Reporte de mantenimientos pendientes
     */
    public function mantenimientosPendientes(Request $request)
    {
        $this->checkPermission('ver_reportes');

        // Obtener filtros
        $vehiculoId = $request->get('vehiculo_id');
        $tipoServicio = $request->get('tipo_servicio');
        $sistemaVehiculo = $request->get('sistema_vehiculo');
        $proveedor = $request->get('proveedor');
        $formato = $request->get('formato', 'html');

        // Query base para mantenimientos pendientes
        $query = \App\Models\Mantenimiento::with(['vehiculo'])
            ->pending()  // Solo mantenimientos sin fecha_fin
            ->join('vehiculos', 'mantenimientos.vehiculo_id', '=', 'vehiculos.id')
            ->select('mantenimientos.*');

        // Aplicar filtros
        if ($vehiculoId) {
            $query->where('mantenimientos.vehiculo_id', $vehiculoId);
        }

        if ($tipoServicio) {
            $query->where('mantenimientos.tipo_servicio', $tipoServicio);
        }

        if ($sistemaVehiculo) {
            $query->where('mantenimientos.sistema_vehiculo', $sistemaVehiculo);
        }

        if ($proveedor) {
            $query->where('mantenimientos.proveedor', 'LIKE', '%' . $proveedor . '%');
        }

        // Ordenar por fecha de inicio descendente (más recientes primero)
        $mantenimientos = $query->orderBy('mantenimientos.fecha_inicio', 'desc')
            ->orderBy('vehiculos.marca')
            ->orderBy('vehiculos.modelo')
            ->paginate(50);

        // Obtener datos para filtros
        $vehiculosDisponibles = Vehiculo::select('id', 'marca', 'modelo', 'anio', 'placas')
            ->orderBy('marca')
            ->orderBy('modelo')
            ->get();

        $tiposServicio = \App\Models\Mantenimiento::getTiposServicio();
        $sistemasVehiculo = \App\Models\Mantenimiento::getSistemasVehiculo();
        
        $proveedoresDisponibles = \App\Models\Mantenimiento::select('proveedor')
            ->whereNotNull('proveedor')
            ->where('proveedor', '!=', '')
            ->distinct()
            ->orderBy('proveedor')
            ->pluck('proveedor');

        // Estadísticas generales
        $estadisticas = [
            'total_pendientes' => $mantenimientos->total(),
            'mantenimientos_correctivos' => \App\Models\Mantenimiento::pending()->where('tipo_servicio', 'CORRECTIVO')->count(),
            'mantenimientos_preventivos' => \App\Models\Mantenimiento::pending()->where('tipo_servicio', 'PREVENTIVO')->count(),
            'vehiculos_en_mantenimiento' => \App\Models\Mantenimiento::pending()->distinct('vehiculo_id')->count(),
            'costo_estimado' => \App\Models\Mantenimiento::pending()->sum('costo'),
            'dias_promedio_pendiente' => \App\Models\Mantenimiento::pending()
                ->selectRaw('AVG(DATEDIFF(CURDATE(), fecha_inicio)) as promedio')
                ->value('promedio') ?? 0
        ];

        // Si se solicita Excel, generar y retornar
        if ($formato === 'excel') {
            return Excel::download(
                new \App\Exports\MantenimientosPendientesExport($mantenimientos), 
                'mantenimientos_pendientes_' . date('Y-m-d_H-i-s') . '.xlsx'
            );
        }

        // Si se solicita PDF, generar y retornar
        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('pdf.reportes.mantenimientos-pendientes', compact(
                'mantenimientos',
                'estadisticas',
                'vehiculoId',
                'tipoServicio',
                'sistemaVehiculo',
                'proveedor'
            ))->setPaper('a4', 'landscape');

            return $pdf->download('reporte-mantenimientos-pendientes-' . date('Y-m-d') . '.pdf');
        }

        return view('reportes.mantenimientos-pendientes', compact(
            'mantenimientos',
            'estadisticas',
            'vehiculosDisponibles',
            'tiposServicio',
            'sistemasVehiculo',
            'proveedoresDisponibles',
            'vehiculoId',
            'tipoServicio',
            'sistemaVehiculo',
            'proveedor'
        ));
    }
}
