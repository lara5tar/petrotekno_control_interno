<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMantenimientoRequest;
use App\Models\LogAccion;
use App\Models\Mantenimiento;
use App\Models\Vehiculo;
use App\Services\AlertasMantenimientoService;
use App\Exports\MantenimientosFiltradosExport;
use App\Traits\PdfGeneratorTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MantenimientoController extends Controller
{
    use PdfGeneratorTrait;
    /**
     * Display a listing of the resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function index(Request $request)
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para ver mantenimientos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para acceder a esta sección']);
        }

        $query = Mantenimiento::with(['vehiculo']);

        // Filtros de búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('proveedor', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhereHas('vehiculo', function ($vq) use ($buscar) {
                        $vq->where('marca', 'like', "%{$buscar}%")
                            ->orWhere('modelo', 'like', "%{$buscar}%")
                            ->orWhere('placas', 'like', "%{$buscar}%");
                    });
            });
        }

        // Filtro por vehículo
        if ($request->filled('vehiculo_id')) {
            $query->where('vehiculo_id', $request->vehiculo_id);
        }

        // Filtro por tipo de servicio
        if ($request->filled('tipo_servicio')) {
            $query->where('tipo_servicio', $request->tipo_servicio);
        }

        // Filtro por proveedor
        if ($request->filled('proveedor')) {
            $query->where('proveedor', 'like', "%{$request->proveedor}%");
        }

        // Filtros de fecha
        if ($request->filled('fecha_inicio_desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->fecha_inicio_desde);
        }

        if ($request->filled('fecha_inicio_hasta')) {
            $query->whereDate('fecha_inicio', '<=', $request->fecha_inicio_hasta);
        }

        // Filtros de kilometraje
        if ($request->filled('kilometraje_min')) {
            $query->where('kilometraje_servicio', '>=', $request->kilometraje_min);
        }

        if ($request->filled('kilometraje_max')) {
            $query->where('kilometraje_servicio', '<=', $request->kilometraje_max);
        }

        // Filtros de costo
        if ($request->filled('costo_min')) {
            $query->where('costo', '>=', $request->costo_min);
        }

        if ($request->filled('costo_max')) {
            $query->where('costo', '<=', $request->costo_max);
        }

        // Orden por ID descendente (más reciente primero)
        $query->orderBy('id', 'desc');

        // Paginación
        $perPage = $request->get('per_page', 15);
        $perPage = max(1, min($perPage, 100)); // Asegurar que esté entre 1 y 100

        $mantenimientos = $query->paginate($perPage);

        // Respuesta híbrida
        if ($request->expectsJson()) {
            $paginationData = $mantenimientos->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Mantenimientos obtenidos correctamente',
                'data' => $paginationData['data'],
                'meta' => [
                    'current_page' => $paginationData['current_page'],
                    'last_page' => $paginationData['last_page'],
                    'per_page' => $paginationData['per_page'],
                    'total' => $paginationData['total'],
                    'from' => $paginationData['from'],
                    'to' => $paginationData['to'],
                ],
            ]);
        }

        // Obtener opciones para filtros
        $vehiculosOptions = Vehiculo::select('id', 'marca', 'modelo', 'placas')
            ->orderBy('marca')
            ->get();

        $tiposServicioOptions = collect([
            (object) ['id' => Mantenimiento::TIPO_CORRECTIVO, 'nombre_tipo_servicio' => 'Correctivo'],
            (object) ['id' => Mantenimiento::TIPO_PREVENTIVO, 'nombre_tipo_servicio' => 'Preventivo'],
        ]);

        $proveedoresDisponibles = Mantenimiento::selectRaw('DISTINCT proveedor')
            ->whereNotNull('proveedor')
            ->where('proveedor', '!=', '')
            ->orderBy('proveedor')
            ->pluck('proveedor');

        return view('mantenimientos.index', compact(
            'mantenimientos',
            'vehiculosOptions',
            'tiposServicioOptions',
            'proveedoresDisponibles'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function create(Request $request)
    {
        // Verificar permisos
        if (! $this->hasPermission('crear_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para crear mantenimientos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para crear mantenimientos']);
        }

        $vehiculosOptions = Vehiculo::select('id', 'marca', 'modelo', 'placas')
            ->orderBy('marca')
            ->get();

        $tiposServicioOptions = collect([
            (object) ['id' => Mantenimiento::TIPO_CORRECTIVO, 'nombre_tipo_servicio' => 'Correctivo'],
            (object) ['id' => Mantenimiento::TIPO_PREVENTIVO, 'nombre_tipo_servicio' => 'Preventivo'],
        ]);

        // Respuesta híbrida
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Formulario de creación de mantenimiento',
                'data' => [
                    'vehiculos_options' => $vehiculosOptions,
                    'tipos_servicio_options' => $tiposServicioOptions,
                ],
            ]);
        }

        return view('mantenimientos.create', compact(
            'vehiculosOptions',
            'tiposServicioOptions'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function store(StoreMantenimientoRequest $request)
    {
        // Verificar permisos
        if (! $this->hasPermission('crear_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para crear mantenimientos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para crear mantenimientos']);
        }

        try {
            $mantenimiento = Mantenimiento::create($request->validated());
            $mantenimiento->load(['vehiculo']);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_mantenimiento',
                'tabla_afectada' => 'mantenimientos',
                'registro_id' => $mantenimiento->id,
                'detalles' => "Mantenimiento creado: {$mantenimiento->vehiculo->marca} {$mantenimiento->vehiculo->modelo} ({$mantenimiento->vehiculo->placas}) - {$mantenimiento->tipo_servicio}",
            ]);

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mantenimiento creado correctamente',
                    'data' => $mantenimiento,
                ], 201);
            }

            return redirect()
                ->route('mantenimientos.show', $mantenimiento)
                ->with('success', 'Mantenimiento creado correctamente');
        } catch (\Exception $e) {
            // Respuesta de error híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el mantenimiento',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el mantenimiento: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function show(Request $request, $id)
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para ver mantenimientos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para ver mantenimientos']);
        }

        try {
            $mantenimiento = Mantenimiento::with(['vehiculo', 'documentos'])->findOrFail($id);

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mantenimiento obtenido correctamente',
                    'data' => $mantenimiento,
                ]);
            }

            return view('mantenimientos.show', compact('mantenimiento'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado',
                ], 404);
            }

            return redirect()
                ->route('mantenimientos.index')
                ->withErrors(['error' => 'Mantenimiento no encontrado']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function edit(Request $request, $id)
    {
        // Verificar permisos
        if (! $this->hasPermission('actualizar_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para editar mantenimientos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para editar mantenimientos']);
        }

        try {
            $mantenimiento = Mantenimiento::with(['vehiculo'])->findOrFail($id);

            $vehiculosOptions = Vehiculo::select('id', 'marca', 'modelo', 'placas')
                ->orderBy('marca')
                ->get();

            $tiposServicioOptions = collect([
                (object) ['id' => Mantenimiento::TIPO_CORRECTIVO, 'nombre_tipo_servicio' => 'Correctivo'],
                (object) ['id' => Mantenimiento::TIPO_PREVENTIVO, 'nombre_tipo_servicio' => 'Preventivo'],
            ]);

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Formulario de edición de mantenimiento',
                    'data' => [
                        'mantenimiento' => $mantenimiento,
                        'vehiculos_options' => $vehiculosOptions,
                        'tipos_servicio_options' => $tiposServicioOptions,
                    ],
                ]);
            }

            return view('mantenimientos.edit', compact(
                'mantenimiento',
                'vehiculosOptions',
                'tiposServicioOptions'
            ));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado',
                ], 404);
            }

            return redirect()
                ->route('mantenimientos.index')
                ->withErrors(['error' => 'Mantenimiento no encontrado']);
        }
    }

    /**
     * Update the specified resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function update(StoreMantenimientoRequest $request, $id)
    {
        // Verificar permisos
        if (! $this->hasPermission('actualizar_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para editar mantenimientos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para editar mantenimientos']);
        }

        try {
            $mantenimiento = Mantenimiento::findOrFail($id);
            $mantenimiento->update($request->validated());
            $mantenimiento->load(['vehiculo']);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_mantenimiento',
                'tabla_afectada' => 'mantenimientos',
                'registro_id' => $mantenimiento->id,
                'detalles' => "Mantenimiento actualizado: {$mantenimiento->vehiculo->marca} {$mantenimiento->vehiculo->modelo} ({$mantenimiento->vehiculo->placas}) - {$mantenimiento->tipo_servicio}",
            ]);

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mantenimiento actualizado correctamente',
                    'data' => $mantenimiento,
                ]);
            }

            return redirect()
                ->route('mantenimientos.show', $mantenimiento)
                ->with('success', 'Mantenimiento actualizado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado',
                ], 404);
            }

            return redirect()
                ->route('mantenimientos.index')
                ->withErrors(['error' => 'Mantenimiento no encontrado']);
        } catch (\Exception $e) {
            // Respuesta de error híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el mantenimiento',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el mantenimiento: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function destroy(Request $request, $id)
    {
        // Verificar permisos
        if (! $this->hasPermission('eliminar_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para eliminar mantenimientos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para eliminar mantenimientos']);
        }

        try {
            $mantenimiento = Mantenimiento::findOrFail($id);

            // Guardamos información para el log antes de eliminar
            $vehiculoInfo = $mantenimiento->vehiculo 
                ? "{$mantenimiento->vehiculo->marca} {$mantenimiento->vehiculo->modelo} ({$mantenimiento->vehiculo->placas})"
                : 'Activo no disponible';
            $infoMantenimiento = "{$vehiculoInfo} - {$mantenimiento->tipo_servicio}";

            $mantenimiento->delete();

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_mantenimiento',
                'tabla_afectada' => 'mantenimientos',
                'registro_id' => $id,
                'detalles' => "Mantenimiento eliminado: {$infoMantenimiento}",
            ]);

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mantenimiento eliminado correctamente',
                ]);
            }

            return redirect()
                ->route('mantenimientos.index')
                ->with('success', 'Mantenimiento eliminado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado',
                ], 404);
            }

            return redirect()
                ->route('mantenimientos.index')
                ->withErrors(['error' => 'Mantenimiento no encontrado']);
        } catch (\Exception $e) {
            // Respuesta de error híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el mantenimiento',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withErrors(['error' => 'Error al eliminar el mantenimiento: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore the specified resource from storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function restore(Request $request, $id)
    {
        // Verificar permisos
        if (! $this->hasPermission('restaurar_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para restaurar mantenimientos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para restaurar mantenimientos']);
        }

        try {
            $mantenimiento = Mantenimiento::withTrashed()->findOrFail($id);
            $mantenimiento->restore();
            $mantenimiento->load(['vehiculo']);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'restaurar_mantenimiento',
                'tabla_afectada' => 'mantenimientos',
                'registro_id' => $mantenimiento->id,
                'detalles' => "Mantenimiento restaurado: {$mantenimiento->vehiculo->marca} {$mantenimiento->vehiculo->modelo} ({$mantenimiento->vehiculo->placas}) - {$mantenimiento->tipo_servicio}",
            ]);

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mantenimiento restaurado correctamente',
                    'data' => $mantenimiento,
                ]);
            }

            return redirect()
                ->route('mantenimientos.show', $mantenimiento)
                ->with('success', 'Mantenimiento restaurado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado',
                ], 404);
            }

            return redirect()
                ->route('mantenimientos.index')
                ->withErrors(['error' => 'Mantenimiento no encontrado']);
        } catch (\Exception $e) {
            // Respuesta de error híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al restaurar el mantenimiento',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withErrors(['error' => 'Error al restaurar el mantenimiento: ' . $e->getMessage()]);
        }
    }

    /**
     * Get mantenimientos próximos (por kilometraje)
     */
    public function proximosPorKilometraje(Request $request)
    {
        $limite = $request->get('limite_km', 5000); // 5000 km por defecto

        $mantenimientos = Mantenimiento::with(['vehiculo'])
            ->select('mantenimientos.*')
            ->join('vehiculos', 'mantenimientos.vehiculo_id', '=', 'vehiculos.id')
            ->whereRaw('(vehiculos.kilometraje_actual - mantenimientos.kilometraje_servicio) >= ?', [$limite])
            ->orderBy('mantenimientos.id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mantenimientos,
        ]);
    }

    /**
     * Get estadísticas de mantenimientos
     */
    public function estadisticas(Request $request)
    {
        $año = $request->get('año', now()->year);

        $estadisticas = [
            'total_mantenimientos' => Mantenimiento::whereYear('fecha_inicio', $año)->count(),
            'costo_total' => Mantenimiento::whereYear('fecha_inicio', $año)->sum('costo'),
            'mantenimientos_por_mes' => Mantenimiento::whereYear('fecha_inicio', $año)
                ->selectRaw('MONTH(fecha_inicio) as mes, COUNT(*) as total, SUM(costo) as costo_mes')
                ->groupBy('mes')
                ->orderBy('mes')
                ->get(),
            'top_proveedores' => Mantenimiento::whereYear('fecha_inicio', $año)
                ->selectRaw('proveedor, COUNT(*) as total_servicios, SUM(costo) as costo_total')
                ->groupBy('proveedor')
                ->orderBy('total_servicios', 'desc')
                ->limit(10)
                ->get(),
            'vehiculos_mas_mantenimientos' => Mantenimiento::with('vehiculo')
                ->whereYear('fecha_inicio', $año)
                ->selectRaw('vehiculo_id, COUNT(*) as total_mantenimientos, SUM(costo) as costo_total')
                ->groupBy('vehiculo_id')
                ->orderBy('total_mantenimientos', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas,
        ]);
    }

    /**
     * Mostrar alertas de mantenimiento
     */
    public function alertas(Request $request)
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para ver alertas de mantenimiento'], 403);
            }
            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para acceder a esta sección']);
        }

        // Obtener alertas de mantenimiento
        $resultadoAlertas = AlertasMantenimientoService::verificarTodosLosVehiculos();
        $alertas = $resultadoAlertas['alertas'] ?? [];
        $resumen = $resultadoAlertas['resumen'] ?? [];
        
        // Para API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $alertas,
                'resumen' => $resumen,
                'total' => count($alertas)
            ]);
        }

        // Para Blade
        return view('mantenimientos.alertas', compact('alertas', 'resumen'));
    }

    /**
     * Descargar reporte de mantenimientos filtrados en formato PDF
     */
    public function descargarReportePdf(Request $request)
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para descargar reportes de mantenimiento'], 403);
            }
            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para acceder a esta sección']);
        }

        // Aplicar los mismos filtros que en el index
        $query = Mantenimiento::with(['vehiculo']);

        // Aplicar filtros de búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('proveedor', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhereHas('vehiculo', function ($vq) use ($buscar) {
                        $vq->where('marca', 'like', "%{$buscar}%")
                            ->orWhere('modelo', 'like', "%{$buscar}%")
                            ->orWhere('placas', 'like', "%{$buscar}%");
                    });
            });
        }

        // Aplicar filtros específicos
        if ($request->filled('vehiculo_id')) {
            $query->where('vehiculo_id', $request->vehiculo_id);
        }

        if ($request->filled('tipo_servicio')) {
            $query->where('tipo_servicio', $request->tipo_servicio);
        }

        if ($request->filled('sistema_vehiculo')) {
            $query->where('sistema_vehiculo', $request->sistema_vehiculo);
        }

        if ($request->filled('proveedor')) {
            $query->where('proveedor', 'like', "%{$request->proveedor}%");
        }

        // Filtros de fecha
        if ($request->filled('fecha_inicio_desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->fecha_inicio_desde);
        }

        if ($request->filled('fecha_inicio_hasta')) {
            $query->whereDate('fecha_inicio', '<=', $request->fecha_inicio_hasta);
        }

        // Filtros de kilometraje
        if ($request->filled('kilometraje_min')) {
            $query->where('kilometraje_servicio', '>=', $request->kilometraje_min);
        }

        if ($request->filled('kilometraje_max')) {
            $query->where('kilometraje_servicio', '<=', $request->kilometraje_max);
        }

        // Filtros de costo
        if ($request->filled('costo_min')) {
            $query->where('costo', '>=', $request->costo_min);
        }

        if ($request->filled('costo_max')) {
            $query->where('costo', '<=', $request->costo_max);
        }

        // Obtener mantenimientos con límite para PDF (máximo 2000 registros)
        $mantenimientos = $query->orderBy('id', 'desc')->limit(2000)->get();

        // Preparar estadísticas
        $estadisticas = [
            'total' => $mantenimientos->count(),
            'costo_total' => $mantenimientos->sum('costo') ?? 0,
            'costo_promedio' => $mantenimientos->avg('costo') ?? 0,
            'por_tipo_servicio' => [
                'PREVENTIVO' => $mantenimientos->where('tipo_servicio', 'PREVENTIVO')->count(),
                'CORRECTIVO' => $mantenimientos->where('tipo_servicio', 'CORRECTIVO')->count(),
            ],
            'por_sistema' => [
                'motor' => $mantenimientos->where('sistema_vehiculo', 'motor')->count(),
                'transmision' => $mantenimientos->where('sistema_vehiculo', 'transmision')->count(),
                'hidraulico' => $mantenimientos->where('sistema_vehiculo', 'hidraulico')->count(),
                'general' => $mantenimientos->where('sistema_vehiculo', 'general')->count(),
            ],
            'por_estado' => [
                'completados' => $mantenimientos->whereNotNull('fecha_fin')->count(),
                'en_proceso' => $mantenimientos->whereNull('fecha_fin')->count(),
            ],
        ];

        // Preparar filtros aplicados para mostrar en el reporte
        $filtrosAplicados = [
            'buscar' => $request->get('buscar'),
            'vehiculo_id' => $request->get('vehiculo_id'),
            'tipo_servicio' => $request->get('tipo_servicio'),
            'sistema_vehiculo' => $request->get('sistema_vehiculo'),
            'proveedor' => $request->get('proveedor'),
            'fecha_inicio_desde' => $request->get('fecha_inicio_desde'),
            'fecha_inicio_hasta' => $request->get('fecha_inicio_hasta'),
            'kilometraje_min' => $request->get('kilometraje_min'),
            'kilometraje_max' => $request->get('kilometraje_max'),
            'costo_min' => $request->get('costo_min'),
            'costo_max' => $request->get('costo_max'),
        ];

        // Generar PDF usando el trait
        $pdf = $this->createStandardPdf(
            'pdf.reportes.mantenimientos-filtrados', 
            compact('mantenimientos', 'estadisticas', 'filtrosAplicados'),
            'landscape' // Horizontal para más columnas
        );

        return $pdf->download('reporte-mantenimientos-filtrados-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    /**
     * Descargar reporte de mantenimientos filtrados en formato Excel
     */
    public function descargarReporteExcel(Request $request)
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_mantenimientos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para descargar reportes de mantenimiento'], 403);
            }
            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para acceder a esta sección']);
        }

        // Aplicar los mismos filtros que en el index
        $query = Mantenimiento::with(['vehiculo']);

        // Aplicar filtros de búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('proveedor', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhereHas('vehiculo', function ($vq) use ($buscar) {
                        $vq->where('marca', 'like', "%{$buscar}%")
                            ->orWhere('modelo', 'like', "%{$buscar}%")
                            ->orWhere('placas', 'like', "%{$buscar}%");
                    });
            });
        }

        // Aplicar filtros específicos
        if ($request->filled('vehiculo_id')) {
            $query->where('vehiculo_id', $request->vehiculo_id);
        }

        if ($request->filled('tipo_servicio')) {
            $query->where('tipo_servicio', $request->tipo_servicio);
        }

        if ($request->filled('sistema_vehiculo')) {
            $query->where('sistema_vehiculo', $request->sistema_vehiculo);
        }

        if ($request->filled('proveedor')) {
            $query->where('proveedor', 'like', "%{$request->proveedor}%");
        }

        // Filtros de fecha
        if ($request->filled('fecha_inicio_desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->fecha_inicio_desde);
        }

        if ($request->filled('fecha_inicio_hasta')) {
            $query->whereDate('fecha_inicio', '<=', $request->fecha_inicio_hasta);
        }

        // Filtros de kilometraje
        if ($request->filled('kilometraje_min')) {
            $query->where('kilometraje_servicio', '>=', $request->kilometraje_min);
        }

        if ($request->filled('kilometraje_max')) {
            $query->where('kilometraje_servicio', '<=', $request->kilometraje_max);
        }

        // Filtros de costo
        if ($request->filled('costo_min')) {
            $query->where('costo', '>=', $request->costo_min);
        }

        if ($request->filled('costo_max')) {
            $query->where('costo', '<=', $request->costo_max);
        }

        // Para Excel podemos manejar más registros (máximo 5000)
        $mantenimientos = $query->orderBy('id', 'desc')->limit(5000)->get();

        // Preparar estadísticas
        $estadisticas = [
            'total' => $mantenimientos->count(),
            'costo_total' => $mantenimientos->sum('costo') ?? 0,
            'costo_promedio' => $mantenimientos->avg('costo') ?? 0,
            'por_tipo_servicio' => [
                'PREVENTIVO' => $mantenimientos->where('tipo_servicio', 'PREVENTIVO')->count(),
                'CORRECTIVO' => $mantenimientos->where('tipo_servicio', 'CORRECTIVO')->count(),
            ],
            'por_sistema' => [
                'motor' => $mantenimientos->where('sistema_vehiculo', 'motor')->count(),
                'transmision' => $mantenimientos->where('sistema_vehiculo', 'transmision')->count(),
                'hidraulico' => $mantenimientos->where('sistema_vehiculo', 'hidraulico')->count(),
                'general' => $mantenimientos->where('sistema_vehiculo', 'general')->count(),
            ],
            'por_estado' => [
                'completados' => $mantenimientos->whereNotNull('fecha_fin')->count(),
                'en_proceso' => $mantenimientos->whereNull('fecha_fin')->count(),
            ],
        ];

        // Preparar filtros aplicados
        $filtrosAplicados = [
            'buscar' => $request->get('buscar'),
            'vehiculo_id' => $request->get('vehiculo_id'),
            'tipo_servicio' => $request->get('tipo_servicio'),
            'sistema_vehiculo' => $request->get('sistema_vehiculo'),
            'proveedor' => $request->get('proveedor'),
            'fecha_inicio_desde' => $request->get('fecha_inicio_desde'),
            'fecha_inicio_hasta' => $request->get('fecha_inicio_hasta'),
            'kilometraje_min' => $request->get('kilometraje_min'),
            'kilometraje_max' => $request->get('kilometraje_max'),
            'costo_min' => $request->get('costo_min'),
            'costo_max' => $request->get('costo_max'),
        ];

        // Usar la clase Export optimizada
        return Excel::download(
            new MantenimientosFiltradosExport($mantenimientos, $filtrosAplicados, $estadisticas),
            'reporte-mantenimientos-filtrados-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
        );
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    private function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        if (! $user || ! $user->rol) {
            return false;
        }

        return $user->rol->permisos()->where('nombre_permiso', $permission)->exists();
    }
}
