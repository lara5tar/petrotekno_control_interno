<?php

namespace App\Http\Controllers;

use App\Http\Requests\MantenimientoRequest;
use App\Models\CatalogoTipoServicio;
use App\Models\LogAccion;
use App\Models\Mantenimiento;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MantenimientoController extends Controller
{
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

        $query = Mantenimiento::with(['vehiculo', 'tipoServicio']);

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
        if ($request->filled('tipo_servicio_id')) {
            $query->where('tipo_servicio_id', $request->tipo_servicio_id);
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

        // Orden
        $query->orderBy('fecha_inicio', 'desc');

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

        $tiposServicioOptions = CatalogoTipoServicio::select('id', 'nombre_tipo_servicio')
            ->orderBy('nombre_tipo_servicio')
            ->get();

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

        $tiposServicioOptions = CatalogoTipoServicio::select('id', 'nombre_tipo_servicio')
            ->orderBy('nombre_tipo_servicio')
            ->get();

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
    public function store(MantenimientoRequest $request)
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
            $mantenimiento->load(['vehiculo', 'tipoServicio']);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_mantenimiento',
                'tabla_afectada' => 'mantenimientos',
                'registro_id' => $mantenimiento->id,
                'detalles' => "Mantenimiento creado: {$mantenimiento->vehiculo->marca} {$mantenimiento->vehiculo->modelo} ({$mantenimiento->vehiculo->placas}) - {$mantenimiento->tipoServicio->nombre_tipo_servicio}",
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
            $mantenimiento = Mantenimiento::with(['vehiculo', 'tipoServicio', 'documentos'])->findOrFail($id);

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
            $mantenimiento = Mantenimiento::with(['vehiculo', 'tipoServicio'])->findOrFail($id);

            $vehiculosOptions = Vehiculo::select('id', 'marca', 'modelo', 'placas')
                ->orderBy('marca')
                ->get();

            $tiposServicioOptions = CatalogoTipoServicio::select('id', 'nombre_tipo_servicio')
                ->orderBy('nombre_tipo_servicio')
                ->get();

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
    public function update(MantenimientoRequest $request, $id)
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
            $mantenimiento->load(['vehiculo', 'tipoServicio']);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_mantenimiento',
                'tabla_afectada' => 'mantenimientos',
                'registro_id' => $mantenimiento->id,
                'detalles' => "Mantenimiento actualizado: {$mantenimiento->vehiculo->marca} {$mantenimiento->vehiculo->modelo} ({$mantenimiento->vehiculo->placas}) - {$mantenimiento->tipoServicio->nombre_tipo_servicio}",
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
            $infoMantenimiento = "{$mantenimiento->vehiculo->marca} {$mantenimiento->vehiculo->modelo} ({$mantenimiento->vehiculo->placas}) - {$mantenimiento->tipoServicio->nombre_tipo_servicio}";

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
            $mantenimiento->load(['vehiculo', 'tipoServicio']);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'restaurar_mantenimiento',
                'tabla_afectada' => 'mantenimientos',
                'registro_id' => $mantenimiento->id,
                'detalles' => "Mantenimiento restaurado: {$mantenimiento->vehiculo->marca} {$mantenimiento->vehiculo->modelo} ({$mantenimiento->vehiculo->placas}) - {$mantenimiento->tipoServicio->nombre_tipo_servicio}",
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

        $mantenimientos = Mantenimiento::with(['vehiculo', 'tipoServicio'])
            ->select('mantenimientos.*')
            ->join('vehiculos', 'mantenimientos.vehiculo_id', '=', 'vehiculos.id')
            ->whereRaw('(vehiculos.kilometraje_actual - mantenimientos.kilometraje_servicio) >= ?', [$limite])
            ->orderBy('fecha_inicio', 'desc')
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
