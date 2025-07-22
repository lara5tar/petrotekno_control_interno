<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\LogAccion;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\Vehiculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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

            return redirect()->back()->withErrors(['error' => 'Error al obtener las asignaciones: ' . $e->getMessage()]);
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

            return redirect()->back()->withErrors(['error' => 'Error al cargar el formulario: ' . $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function store(Request $request)
    {
        // Verificar permisos
        if (! $this->hasPermission('crear_asignaciones')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para crear asignaciones'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para crear asignaciones']);
        }

        try {
            $validated = $request->validate([
                'vehiculo_id' => 'required|exists:vehiculos,id',
                'obra_id' => 'required|exists:obras,id',
                'personal_id' => 'required|exists:personal,id',
                'fecha_asignacion' => 'required|date',
                'kilometraje_inicial' => 'required|integer|min:0',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            // Validar que el vehículo no tenga asignación activa
            if (Asignacion::vehiculoTieneAsignacionActiva($validated['vehiculo_id'])) {
                $message = 'El vehículo ya tiene una asignación activa';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->back()->withErrors(['vehiculo_id' => $message])->withInput();
            }

            // Validar que el operador no tenga asignación activa
            if (Asignacion::operadorTieneAsignacionActiva($validated['personal_id'])) {
                $message = 'El operador ya tiene una asignación activa';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->back()->withErrors(['personal_id' => $message])->withInput();
            }

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
                    'message' => 'Error al crear la asignación',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al crear la asignación: ' . $e->getMessage()])->withInput();
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

            return redirect()->back()->withErrors(['error' => 'Error al obtener la asignación: ' . $e->getMessage()]);
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

            return redirect()->back()->withErrors(['error' => 'Error al cargar el formulario: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function update(Request $request, string $id)
    {
        // Verificar permisos
        if (! $this->hasPermission('editar_asignaciones')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para editar asignaciones'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para editar asignaciones']);
        }

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

            $validated = $request->validate([
                'obra_id' => 'sometimes|exists:obras,id',
                'personal_id' => [
                    'sometimes',
                    'exists:personal,id',
                    Rule::unique('asignaciones', 'personal_id')
                        ->where(function ($query) {
                            return $query->whereNull('fecha_liberacion');
                        })
                        ->ignore($asignacion->id),
                ],
                'kilometraje_inicial' => 'sometimes|integer|min:0',
                'observaciones' => 'nullable|string|max:1000',
            ]);

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
                    'message' => 'Error al actualizar la asignación',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al actualizar la asignación: ' . $e->getMessage()]);
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
                'kilometraje_final' => 'required|integer|min:' . $asignacion->kilometraje_inicial,
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

            return redirect()->back()->withErrors(['error' => 'Error al liberar la asignación: ' . $e->getMessage()]);
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

            return redirect()->back()->withErrors(['error' => 'Error al eliminar la asignación: ' . $e->getMessage()]);
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
}
