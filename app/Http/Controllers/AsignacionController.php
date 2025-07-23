<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAsignacionRequest;
use App\Http\Requests\TransferirAsignacionRequest;
use App\Http\Requests\UpdateAsignacionRequest;
use App\Models\Asignacion;
use App\Models\LogAccion;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AsignacionController extends Controller
{
    /**
     * Verificar permisos del usuario autenticado
     */
    private function hasPermission(string $permission): bool
    {
        $user = Auth::user();

        return $user ? $user->hasPermission($permission) : false;
    }

    /**
     * Display a listing of the resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function index(Request $request)
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_asignaciones')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para ver asignaciones'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para ver asignaciones']);
        }

        try {
            $query = Asignacion::with(['vehiculo', 'obra', 'personal', 'creadoPor']);

            // Filtros
            if ($request->has('estado')) {
                if ($request->estado === 'activas') {
                    $query->activas();
                } elseif ($request->estado === 'liberadas') {
                    $query->liberadas();
                }
            }

            if ($request->has('vehiculo_id')) {
                $query->porVehiculo($request->vehiculo_id);
            }

            if ($request->has('obra_id')) {
                $query->porObra($request->obra_id);
            }

            if ($request->has('personal_id')) {
                $query->porOperador($request->personal_id);
            }

            if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
                $query->porFecha($request->fecha_inicio, $request->fecha_fin);
            }

            // Búsqueda por texto
            if ($request->filled('buscar')) {
                $search = $request->buscar;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('vehiculo', function ($vq) use ($search) {
                        $vq->where('marca', 'like', "%{$search}%")
                            ->orWhere('modelo', 'like', "%{$search}%")
                            ->orWhere('placas', 'like', "%{$search}%");
                    })
                        ->orWhereHas('personal', function ($pq) use ($search) {
                            $pq->where('nombre_completo', 'like', "%{$search}%");
                        })
                        ->orWhereHas('obra', function ($oq) use ($search) {
                            $oq->where('nombre_obra', 'like', "%{$search}%");
                        })
                        ->orWhere('observaciones', 'like', "%{$search}%");
                });
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'fecha_asignacion');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $asignaciones = $query->paginate($perPage);

            $meta = [
                'total_activas' => Asignacion::activas()->count(),
                'total_liberadas' => Asignacion::liberadas()->count(),
            ];

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asignaciones obtenidas exitosamente',
                    'data' => $asignaciones,
                    'meta' => $meta,
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            // Obtener opciones para filtros
            $vehiculosOptions = Vehiculo::select('id', 'marca', 'modelo', 'placas')->orderBy('marca')->get();
            $obrasOptions = Obra::select('id', 'nombre_obra')->where('estatus', '!=', 'cancelada')->orderBy('nombre_obra')->get();
            $personalOptions = Personal::select('id', 'nombre_completo')->where('estatus', 'activo')->orderBy('nombre_completo')->get();

            return view('asignaciones.index', compact('asignaciones', 'vehiculosOptions', 'obrasOptions', 'personalOptions', 'meta'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener las asignaciones',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al obtener las asignaciones: '.$e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function create(Request $request)
    {
        // Verificar permisos
        if (! $this->hasPermission('crear_asignaciones')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para crear asignaciones'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para crear asignaciones']);
        }

        try {
            // Obtener opciones para formulario
            $vehiculosDisponibles = Vehiculo::select('id', 'marca', 'modelo', 'placas', 'kilometraje_actual')
                ->whereNotIn('id', function ($query) {
                    $query->select('vehiculo_id')->from('asignaciones')->whereNull('fecha_liberacion');
                })
                ->orderBy('marca')
                ->get();

            $obrasActivas = Obra::select('id', 'nombre_obra', 'estatus')
                ->whereIn('estatus', ['planificada', 'en_progreso'])
                ->orderBy('nombre_obra')
                ->get();

            $operadoresDisponibles = Personal::select('id', 'nombre_completo', 'categoria_id')
                ->where('estatus', 'activo')
                ->whereNotIn('id', function ($query) {
                    $query->select('personal_id')->from('asignaciones')->whereNull('fecha_liberacion');
                })
                ->with('categoria:id,nombre_categoria')
                ->orderBy('nombre_completo')
                ->get();

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'vehiculos_disponibles' => $vehiculosDisponibles,
                        'obras_activas' => $obrasActivas,
                        'operadores_disponibles' => $operadoresDisponibles,
                    ],
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return view('asignaciones.create', compact('vehiculosDisponibles', 'obrasActivas', 'operadoresDisponibles'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener los datos para crear asignación',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al cargar el formulario: '.$e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function store(StoreAsignacionRequest $request)
    {
        try {
            // Los datos ya vienen validados desde StoreAsignacionRequest
            $validated = $request->validated();

            // Agregar usuario que crea la asignación
            $validated['creado_por_id'] = Auth::id();

            $asignacion = Asignacion::create($validated);
            $asignacion->load(['vehiculo', 'obra', 'personal', 'creadoPor']);

            // Registrar en log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_asignacion',
                'tabla_afectada' => 'asignaciones',
                'registro_id' => $asignacion->id,
                'detalles' => json_encode([
                    'vehiculo' => $asignacion->vehiculo->nombre_completo ?? 'N/A',
                    'operador' => $asignacion->personal->nombre_completo ?? 'N/A',
                    'obra' => $asignacion->obra->nombre_obra ?? 'N/A',
                ]),
            ]);

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asignación creada exitosamente',
                    'data' => $asignacion,
                ], 201);
            }

            // Si es solicitud web (navegador tradicional)
            return redirect()->route('asignaciones.index')->with('success', 'Asignación creada exitosamente');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la asignación',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al crear la asignación: '.$e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function show(Request $request, string $id)
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_asignaciones')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para ver asignaciones'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para ver asignaciones']);
        }

        try {
            $asignacion = Asignacion::with(['vehiculo', 'obra', 'personal', 'creadoPor'])->findOrFail($id);

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asignación obtenida exitosamente',
                    'data' => $asignacion,
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return view('asignaciones.show', compact('asignacion'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada',
                ], 404);
            }

            return redirect()->route('asignaciones.index')->withErrors(['error' => 'Asignación no encontrada']);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener la asignación',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al obtener la asignación: '.$e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function edit(Request $request, string $id)
    {
        // Verificar permisos
        if (! $this->hasPermission('editar_asignaciones')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para editar asignaciones'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para editar asignaciones']);
        }

        try {
            $asignacion = Asignacion::with(['vehiculo', 'obra', 'personal', 'creadoPor'])->findOrFail($id);

            // Solo permitir editar si está activa
            if (! $asignacion->esta_activa) {
                $message = 'No se puede modificar una asignación liberada';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->route('asignaciones.show', $id)->withErrors(['error' => $message]);
            }

            // Obtener opciones para formulario
            $obrasActivas = Obra::select('id', 'nombre_obra', 'estatus')
                ->whereIn('estatus', ['planificada', 'en_progreso'])
                ->orderBy('nombre_obra')
                ->get();

            $operadoresDisponibles = Personal::select('id', 'nombre_completo', 'categoria_id')
                ->where('estatus', 'activo')
                ->where(function ($query) use ($asignacion) {
                    $query->whereNotIn('id', function ($subQuery) {
                        $subQuery->select('personal_id')->from('asignaciones')->whereNull('fecha_liberacion');
                    })
                        ->orWhere('id', $asignacion->personal_id);
                })
                ->with('categoria:id,nombre_categoria')
                ->orderBy('nombre_completo')
                ->get();

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'asignacion' => $asignacion,
                        'obras_activas' => $obrasActivas,
                        'operadores_disponibles' => $operadoresDisponibles,
                    ],
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return view('asignaciones.edit', compact('asignacion', 'obrasActivas', 'operadoresDisponibles'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada',
                ], 404);
            }

            return redirect()->route('asignaciones.index')->withErrors(['error' => 'Asignación no encontrada']);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener la asignación',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al cargar el formulario: '.$e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function update(UpdateAsignacionRequest $request, string $id)
    {
        try {
            $asignacion = Asignacion::findOrFail($id);

            // Solo permitir actualizar si está activa
            if (! $asignacion->esta_activa) {
                $message = 'No se puede modificar una asignación liberada';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->back()->withErrors(['error' => $message]);
            }

            // Los datos ya vienen validados desde UpdateAsignacionRequest
            $validated = $request->validated();

            $asignacion->update($validated);
            $asignacion->load(['vehiculo', 'obra', 'personal', 'creadoPor']);

            // Registrar en log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_asignacion',
                'tabla_afectada' => 'asignaciones',
                'registro_id' => $asignacion->id,
                'detalles' => json_encode($validated),
            ]);

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asignación actualizada exitosamente',
                    'data' => $asignacion,
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return redirect()->route('asignaciones.show', $id)->with('success', 'Asignación actualizada exitosamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada',
                ], 404);
            }

            return redirect()->route('asignaciones.index')->withErrors(['error' => 'Asignación no encontrada']);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la asignación',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al actualizar la asignación: '.$e->getMessage()]);
        }
    }

    /**
     * Liberar una asignación
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function liberar(Request $request, string $id)
    {
        // Verificar permisos
        if (! $this->hasPermission('liberar_asignaciones')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para liberar asignaciones'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para liberar asignaciones']);
        }

        try {
            $asignacion = Asignacion::findOrFail($id);

            if (! $asignacion->esta_activa) {
                $message = 'La asignación ya está liberada';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->back()->withErrors(['error' => $message]);
            }

            $validated = $request->validate([
                'kilometraje_final' => 'required|integer|min:'.$asignacion->kilometraje_inicial,
                'observaciones_liberacion' => 'nullable|string|max:500',
            ]);

            $asignacion->liberar(
                $validated['kilometraje_final'],
                $validated['observaciones_liberacion'] ?? null
            );

            $asignacion->load(['vehiculo', 'obra', 'personal', 'creadoPor']);

            // Registrar en log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'liberar_asignacion',
                'tabla_afectada' => 'asignaciones',
                'registro_id' => $asignacion->id,
                'detalles' => json_encode([
                    'kilometraje_final' => $validated['kilometraje_final'],
                    'kilometraje_recorrido' => $asignacion->kilometraje_recorrido,
                ]),
            ]);

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asignación liberada exitosamente',
                    'data' => $asignacion,
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return redirect()->route('asignaciones.show', $id)->with('success', 'Asignación liberada exitosamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada',
                ], 404);
            }

            return redirect()->route('asignaciones.index')->withErrors(['error' => 'Asignación no encontrada']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al liberar la asignación',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al liberar la asignación: '.$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function destroy(Request $request, string $id)
    {
        // Verificar permisos
        if (! $this->hasPermission('eliminar_asignaciones')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para eliminar asignaciones'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para eliminar asignaciones']);
        }

        try {
            $asignacion = Asignacion::findOrFail($id);

            // Solo permitir eliminar si está activa y no tiene mucho tiempo
            if (! $asignacion->esta_activa) {
                $message = 'No se puede eliminar una asignación liberada';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->back()->withErrors(['error' => $message]);
            }

            // Registrar en log antes de eliminar
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_asignacion',
                'tabla_afectada' => 'asignaciones',
                'registro_id' => $asignacion->id,
                'detalles' => json_encode([
                    'vehiculo' => $asignacion->vehiculo->nombre_completo ?? 'N/A',
                    'operador' => $asignacion->personal->nombre_completo ?? 'N/A',
                    'obra' => $asignacion->obra->nombre_obra ?? 'N/A',
                ]),
            ]);

            $asignacion->delete();

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asignación eliminada exitosamente',
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return redirect()->route('asignaciones.index')->with('success', 'Asignación eliminada exitosamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada',
                ], 404);
            }

            return redirect()->route('asignaciones.index')->withErrors(['error' => 'Asignación no encontrada']);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la asignación',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al eliminar la asignación: '.$e->getMessage()]);
        }
    }

    /**
     * Obtener estadísticas de asignaciones
     */
    public function estadisticas(): JsonResponse
    {
        try {
            $stats = [
                'total_asignaciones' => Asignacion::count(),
                'asignaciones_activas' => Asignacion::activas()->count(),
                'asignaciones_liberadas' => Asignacion::liberadas()->count(),
                'vehiculos_asignados' => Asignacion::activas()->distinct('vehiculo_id')->count(),
                'operadores_activos' => Asignacion::activas()->distinct('personal_id')->count(),
                'obras_con_asignaciones' => Asignacion::activas()->distinct('obra_id')->count(),
                'promedio_dias_asignacion' => round(Asignacion::liberadas()->avg('duracion_en_dias') ?? 0, 1),
                'promedio_kilometraje_recorrido' => round(Asignacion::liberadas()->whereNotNull('kilometraje_final')->avg('kilometraje_recorrido') ?? 0, 0),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las estadísticas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener asignaciones por vehículo
     */
    public function porVehiculo(string $vehiculoId): JsonResponse
    {
        try {
            $vehiculo = Vehiculo::findOrFail($vehiculoId);

            $asignaciones = Asignacion::with(['obra', 'personal', 'creadoPor'])
                ->porVehiculo($vehiculoId)
                ->orderBy('fecha_asignacion', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Historial de asignaciones del vehículo obtenido exitosamente',
                'data' => [
                    'vehiculo' => $vehiculo,
                    'asignaciones' => $asignaciones,
                    'total' => $asignaciones->count(),
                    'activa' => $asignaciones->where('esta_activa', true)->first(),
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vehículo no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones del vehículo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener asignaciones por operador
     */
    public function porOperador(string $personalId): JsonResponse
    {
        try {
            $operador = Personal::findOrFail($personalId);

            $asignaciones = Asignacion::with(['vehiculo', 'obra', 'creadoPor'])
                ->porOperador($personalId)
                ->orderBy('fecha_asignacion', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Historial de asignaciones del operador obtenido exitosamente',
                'data' => [
                    'operador' => $operador,
                    'asignaciones' => $asignaciones,
                    'total' => $asignaciones->count(),
                    'activa' => $asignaciones->where('esta_activa', true)->first(),
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Operador no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones del operador',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener estadísticas avanzadas de productividad por operador
     * Endpoint: GET /api/asignaciones/estadisticas/operador/{id}
     * Blade: GET /asignaciones/estadisticas/operador/{id}
     */
    public function estadisticasOperador(Request $request, string $personalId)
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_estadisticas_asignaciones')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para ver estadísticas'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para ver estadísticas']);
        }

        try {
            $operador = Personal::findOrFail($personalId);

            // Parámetros de fecha (últimos 12 meses por defecto)
            $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subMonths(12)->startOfMonth());
            $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());

            $asignaciones = Asignacion::with(['vehiculo', 'obra'])
                ->porOperador($personalId)
                ->porFecha($fechaInicio, $fechaFin)
                ->get();

            // Calcular estadísticas
            $totalAsignaciones = $asignaciones->count();
            $asignacionesCompletadas = $asignaciones->where('fecha_liberacion', '!=', null)->count();
            $asignacionesActivas = $asignaciones->where('fecha_liberacion', null)->count();

            $kilometrajeTotalRecorrido = $asignaciones->sum('kilometraje_recorrido') ?? 0;
            $promedioKilometrajeAsignacion = $totalAsignaciones > 0 ? $kilometrajeTotalRecorrido / $totalAsignaciones : 0;

            $duracionPromedio = $asignaciones->where('fecha_liberacion', '!=', null)->avg('duracion_en_dias') ?? 0;

            // Estadísticas por mes
            $estadisticasPorMes = $asignaciones->groupBy(function ($asignacion) {
                return Carbon::parse($asignacion->fecha_asignacion)->format('Y-m');
            })->map(function ($mes) {
                return [
                    'total' => $mes->count(),
                    'completadas' => $mes->where('fecha_liberacion', '!=', null)->count(),
                    'kilometraje' => $mes->sum('kilometraje_recorrido') ?? 0,
                ];
            });

            // Top vehículos más utilizados
            $vehiculosMasUtilizados = $asignaciones->groupBy('vehiculo_id')->map(function ($grupo) {
                $vehiculo = $grupo->first()->vehiculo;

                return [
                    'vehiculo' => $vehiculo->nombre_completo ?? 'N/A',
                    'total_asignaciones' => $grupo->count(),
                    'kilometraje_total' => $grupo->sum('kilometraje_recorrido') ?? 0,
                ];
            })->sortByDesc('total_asignaciones')->take(5);

            $estadisticas = [
                'operador' => $operador,
                'periodo' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                ],
                'resumen' => [
                    'total_asignaciones' => $totalAsignaciones,
                    'asignaciones_completadas' => $asignacionesCompletadas,
                    'asignaciones_activas' => $asignacionesActivas,
                    'tasa_completitud' => $totalAsignaciones > 0 ? round(($asignacionesCompletadas / $totalAsignaciones) * 100, 2) : 0,
                    'kilometraje_total_recorrido' => $kilometrajeTotalRecorrido,
                    'promedio_kilometraje_asignacion' => round($promedioKilometrajeAsignacion, 2),
                    'duracion_promedio_dias' => round($duracionPromedio, 1),
                ],
                'estadisticas_por_mes' => $estadisticasPorMes,
                'vehiculos_mas_utilizados' => $vehiculosMasUtilizados->values(),
            ];

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Estadísticas del operador obtenidas exitosamente',
                    'data' => $estadisticas,
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return view('asignaciones.estadisticas-operador', compact('estadisticas'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Operador no encontrado'], 404);
            }

            return redirect()->route('asignaciones.index')->withErrors(['error' => 'Operador no encontrado']);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener estadísticas',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al obtener estadísticas: '.$e->getMessage()]);
        }
    }

    /**
     * Obtener asignaciones que requieren atención (cerca de vencer)
     * Endpoint: GET /api/asignaciones/alertas
     * Blade: GET /asignaciones/alertas
     */
    public function alertasAsignaciones(Request $request)
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_asignaciones')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para ver alertas'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para ver alertas']);
        }

        try {
            $diasAlerta = $request->get('dias_alerta', 30);
            $fechaLimite = Carbon::now()->subDays($diasAlerta);

            $asignacionesVencidas = Asignacion::with(['vehiculo', 'obra', 'personal'])
                ->activas()
                ->where('fecha_asignacion', '<=', $fechaLimite)
                ->orderBy('fecha_asignacion', 'asc')
                ->get();

            $alertas = $asignacionesVencidas->map(function ($asignacion) {
                return [
                    'id' => $asignacion->id,
                    'vehiculo' => $asignacion->vehiculo->nombre_completo ?? 'N/A',
                    'operador' => $asignacion->personal->nombre_completo ?? 'N/A',
                    'obra' => $asignacion->obra->nombre_obra ?? 'N/A',
                    'fecha_asignacion' => $asignacion->fecha_asignacion->format('d/m/Y'),
                    'dias_activa' => $asignacion->duracion_en_dias,
                    'nivel_alerta' => $asignacion->duracion_en_dias > 60 ? 'critico' : ($asignacion->duracion_en_dias > 45 ? 'alto' : 'medio'),
                    'kilometraje_inicial' => $asignacion->kilometraje_inicial,
                ];
            });

            $resumen = [
                'total_alertas' => $alertas->count(),
                'nivel_critico' => $alertas->where('nivel_alerta', 'critico')->count(),
                'nivel_alto' => $alertas->where('nivel_alerta', 'alto')->count(),
                'nivel_medio' => $alertas->where('nivel_alerta', 'medio')->count(),
                'criterio_dias' => $diasAlerta,
            ];

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alertas de asignaciones obtenidas exitosamente',
                    'data' => [
                        'alertas' => $alertas,
                        'resumen' => $resumen,
                    ],
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return view('asignaciones.alertas', compact('alertas', 'resumen'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener alertas',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al obtener alertas: '.$e->getMessage()]);
        }
    }

    /**
     * Mostrar formulario de transferencia de asignación
     */
    public function mostrarTransferencia(string $id)
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('gestionar_asignaciones')) {
            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para transferir asignaciones']);
        }

        try {
            $asignacion = Asignacion::with(['vehiculo', 'personal', 'obra'])->findOrFail($id);

            // Verificar que la asignación esté activa
            if ($asignacion->fecha_liberacion) {
                return redirect()->route('asignaciones.show', $id)
                    ->withErrors(['error' => 'No se puede transferir una asignación que ya ha sido liberada']);
            }

            // Obtener operadores disponibles (activos y sin asignaciones activas)
            $operadoresDisponibles = Personal::where('estatus', 'activo')
                ->where('id', '!=', $asignacion->personal_id)
                ->whereDoesntHave('asignaciones', function ($query) {
                    $query->whereNull('fecha_liberacion');
                })
                ->with('categoria')
                ->orderBy('nombre_completo')
                ->get();

            return view('asignaciones.transferir', compact('asignacion', 'operadoresDisponibles'));
        } catch (\Exception $e) {
            return redirect()->route('asignaciones.index')
                ->withErrors(['error' => 'Error al cargar formulario de transferencia: '.$e->getMessage()]);
        }
    }

    /**
     * Transferir una asignación a otro operador
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function transferir(TransferirAsignacionRequest $request, string $id)
    {
        try {
            $asignacion = Asignacion::with(['vehiculo', 'personal', 'obra'])->findOrFail($id);

            // Verificar que la asignación esté activa
            if ($asignacion->fecha_liberacion) {
                $error = 'No se puede transferir una asignación que ya ha sido liberada';

                return $this->handleResponse($request, null, $error, 400);
            }

            $validatedData = $request->validated();

            DB::beginTransaction();

            // Obtener datos para el histórico
            $operadorAnterior = $asignacion->personal;
            $nuevoOperador = Personal::findOrFail($validatedData['nuevo_operador_id']);

            // Crear registro histórico de la transferencia
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'fecha_hora' => now(),
                'accion' => 'transferencia_asignacion',
                'tabla_afectada' => 'asignaciones',
                'registro_id' => $asignacion->id,
                'detalles' => json_encode([
                    'vehiculo' => $asignacion->vehiculo->nombre_completo,
                    'obra' => $asignacion->obra->nombre_obra,
                    'operador_anterior' => $operadorAnterior->nombre_completo,
                    'nuevo_operador' => $nuevoOperador->nombre_completo,
                    'motivo' => $validatedData['motivo_transferencia'],
                    'kilometraje_transferencia' => $validatedData['kilometraje_transferencia'],
                ]),
            ]);

            // Actualizar la asignación
            $observacionesActuales = $asignacion->observaciones ?? '';
            $nuevasObservaciones = $observacionesActuales.
                "\n[TRANSFERENCIA ".now()->format('d/m/Y H:i').'] '.
                "De: {$operadorAnterior->nombre_completo} a: {$nuevoOperador->nombre_completo}. ".
                "Motivo: {$validatedData['motivo_transferencia']}. ".
                "Km transferencia: {$validatedData['kilometraje_transferencia']}.";

            if (! empty($validatedData['observaciones_transferencia'])) {
                $nuevasObservaciones .= " Observaciones: {$validatedData['observaciones_transferencia']}.";
            }

            $asignacion->update([
                'personal_id' => $validatedData['nuevo_operador_id'],
                'observaciones' => trim($nuevasObservaciones),
                'fecha_actualizacion' => now(),
            ]);

            // Actualizar el kilometraje del vehículo si es necesario
            $vehiculo = $asignacion->vehiculo;
            if ($validatedData['kilometraje_transferencia'] > $vehiculo->kilometraje_actual) {
                $vehiculo->update([
                    'kilometraje_actual' => $validatedData['kilometraje_transferencia'],
                ]);
            }

            DB::commit();

            $mensaje = "Asignación transferida exitosamente de {$operadorAnterior->nombre_completo} a {$nuevoOperador->nombre_completo}";

            return $this->handleResponse($request, [
                'asignacion' => $asignacion->load(['vehiculo', 'personal', 'obra']),
                'transferencia' => [
                    'operador_anterior' => $operadorAnterior->nombre_completo,
                    'nuevo_operador' => $nuevoOperador->nombre_completo,
                    'motivo' => $validatedData['motivo_transferencia'],
                    'kilometraje' => $validatedData['kilometraje_transferencia'],
                ],
            ], $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            $error = 'Error al transferir la asignación: '.$e->getMessage();

            return $this->handleResponse($request, null, $error, 500);
        }
    }

    /**
     * Manejar respuesta híbrida (JSON para API, redirect para web)
     */
    private function handleResponse($request, $data = null, string $message = '', int $status = 200)
    {
        if ($request->expectsJson()) {
            $response = [
                'success' => $status < 400,
                'message' => $message,
            ];

            if ($data !== null) {
                $response['data'] = $data;
            }

            return response()->json($response, $status);
        }

        // Respuesta para web (Blade)
        if ($status >= 400) {
            return redirect()->back()->withErrors(['error' => $message]);
        }

        return redirect()->route('asignaciones.index')->with('success', $message);
    }
}
