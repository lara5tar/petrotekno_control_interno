<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Personal;
use App\Models\Vehiculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AsignacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
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

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'fecha_asignacion');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $asignaciones = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Asignaciones obtenidas exitosamente',
                'data' => $asignaciones,
                'meta' => [
                    'total_activas' => Asignacion::activas()->count(),
                    'total_liberadas' => Asignacion::liberadas()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
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
                return response()->json([
                    'success' => false,
                    'message' => 'El vehículo ya tiene una asignación activa',
                ], 422);
            }

            // Validar que el operador no tenga asignación activa
            if (Asignacion::operadorTieneAsignacionActiva($validated['personal_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El operador ya tiene una asignación activa',
                ], 422);
            }

            // Agregar usuario que crea la asignación
            $validated['creado_por_id'] = Auth::id();

            $asignacion = Asignacion::create($validated);
            $asignacion->load(['vehiculo', 'obra', 'personal', 'creadoPor']);

            return response()->json([
                'success' => true,
                'message' => 'Asignación creada exitosamente',
                'data' => $asignacion,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la asignación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $asignacion = Asignacion::with(['vehiculo', 'obra', 'personal', 'creadoPor'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Asignación obtenida exitosamente',
                'data' => $asignacion,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Asignación no encontrada',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la asignación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $asignacion = Asignacion::findOrFail($id);

            // Solo permitir actualizar si está activa
            if (! $asignacion->esta_activa) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede modificar una asignación liberada',
                ], 422);
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

            return response()->json([
                'success' => true,
                'message' => 'Asignación actualizada exitosamente',
                'data' => $asignacion,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Asignación no encontrada',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la asignación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Liberar una asignación
     */
    public function liberar(Request $request, string $id): JsonResponse
    {
        try {
            $asignacion = Asignacion::findOrFail($id);

            if (! $asignacion->esta_activa) {
                return response()->json([
                    'success' => false,
                    'message' => 'La asignación ya está liberada',
                ], 422);
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

            return response()->json([
                'success' => true,
                'message' => 'Asignación liberada exitosamente',
                'data' => $asignacion,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Asignación no encontrada',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al liberar la asignación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $asignacion = Asignacion::findOrFail($id);

            // Solo permitir eliminar si está activa y no tiene mucho tiempo
            if (! $asignacion->esta_activa) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una asignación liberada',
                ], 422);
            }

            $asignacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asignación eliminada exitosamente',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Asignación no encontrada',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la asignación',
                'error' => $e->getMessage(),
            ], 500);
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
