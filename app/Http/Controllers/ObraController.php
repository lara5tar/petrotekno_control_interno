<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Obra;
use App\Models\LogAccion;
use App\Http\Requests\StoreObraRequest;
use App\Http\Requests\UpdateObraRequest;

class ObraController extends Controller
{
    /**
     * Constructor - Aplicar middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware, 
            // pero mantenemos verificación adicional por seguridad
            if (!$request->user()->hasPermission('ver_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para ver las obras.',
                    'error' => 'Acceso denegado'
                ], 403);
            }

            $query = Obra::query();

            // Filtro por estatus
            if ($request->has('estatus') && $request->estatus !== '') {
                $query->where('estatus', $request->estatus);
            }

            // Filtro por búsqueda en nombre
            if ($request->has('buscar') && $request->buscar !== '') {
                $query->buscar($request->buscar);
            }

            // Filtro por rango de fechas de inicio
            if ($request->has('fecha_inicio_desde') && $request->has('fecha_inicio_hasta')) {
                $query->whereBetween('fecha_inicio', [
                    $request->fecha_inicio_desde,
                    $request->fecha_inicio_hasta
                ]);
            }

            // Filtro por avance mínimo
            if ($request->has('avance_minimo')) {
                $query->where('avance', '>=', $request->avance_minimo);
            }

            // Filtro por obras activas (no canceladas)
            if ($request->has('solo_activas') && $request->solo_activas === 'true') {
                $query->activas();
            }

            // Filtro por obras atrasadas
            if ($request->has('atrasadas') && $request->atrasadas === 'true') {
                $query->whereNotNull('fecha_fin')
                      ->where('fecha_fin', '<', now())
                      ->whereNotIn('estatus', [Obra::ESTATUS_COMPLETADA, Obra::ESTATUS_CANCELADA]);
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            $allowedSorts = ['nombre_obra', 'estatus', 'avance', 'fecha_inicio', 'fecha_fin', 'created_at'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Paginación
            $perPage = min($request->get('per_page', 15), 100);
            $obras = $query->paginate($perPage);

            // Agregar atributos calculados a cada obra
            $obras->getCollection()->transform(function ($obra) {
                return [
                    'id' => $obra->id,
                    'nombre_obra' => $obra->nombre_obra,
                    'estatus' => $obra->estatus,
                    'estatus_descripcion' => $obra->estatus_descripcion,
                    'avance' => $obra->avance,
                    'fecha_inicio' => $obra->fecha_inicio?->format('Y-m-d'),
                    'fecha_fin' => $obra->fecha_fin?->format('Y-m-d'),
                    'dias_transcurridos' => $obra->dias_transcurridos,
                    'dias_restantes' => $obra->dias_restantes,
                    'duracion_total' => $obra->duracion_total,
                    'porcentaje_tiempo_transcurrido' => $obra->porcentaje_tiempo_transcurrido,
                    'esta_atrasada' => $obra->esta_atrasada,
                    'created_at' => $obra->created_at,
                    'updated_at' => $obra->updated_at,
                ];
            });

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'listar_obras',
                'tabla_afectada' => 'obras',
                'detalles' => 'Listado de obras consultado con filtros: ' . json_encode($request->only(['estatus', 'buscar', 'solo_activas']))
            ]);

            return response()->json([
                'message' => 'Obras obtenidas exitosamente.',
                'data' => $obras->items(),
                'pagination' => [
                    'current_page' => $obras->currentPage(),
                    'last_page' => $obras->lastPage(),
                    'per_page' => $obras->perPage(),
                    'total' => $obras->total(),
                    'from' => $obras->firstItem(),
                    'to' => $obras->lastItem(),
                ],
                'filtros_aplicados' => $request->only(['estatus', 'buscar', 'fecha_inicio_desde', 'fecha_inicio_hasta', 'avance_minimo', 'solo_activas', 'atrasadas']),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las obras.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param StoreObraRequest $request
     * @return JsonResponse
     */
    public function store(StoreObraRequest $request): JsonResponse
    {
        try {
            $obra = Obra::create($request->validated());

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'crear_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Obra creada: {$obra->nombre_obra}"
            ]);

            return response()->json([
                'message' => 'Obra creada exitosamente.',
                'data' => [
                    'id' => $obra->id,
                    'nombre_obra' => $obra->nombre_obra,
                    'estatus' => $obra->estatus,
                    'estatus_descripcion' => $obra->estatus_descripcion,
                    'avance' => $obra->avance,
                    'fecha_inicio' => $obra->fecha_inicio?->format('Y-m-d'),
                    'fecha_fin' => $obra->fecha_fin?->format('Y-m-d'),
                    'dias_transcurridos' => $obra->dias_transcurridos,
                    'dias_restantes' => $obra->dias_restantes,
                    'duracion_total' => $obra->duracion_total,
                    'porcentaje_tiempo_transcurrido' => $obra->porcentaje_tiempo_transcurrido,
                    'esta_atrasada' => $obra->esta_atrasada,
                    'created_at' => $obra->created_at,
                    'updated_at' => $obra->updated_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la obra.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware
            if (!$request->user()->hasPermission('ver_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para ver esta obra.',
                    'error' => 'Acceso denegado'
                ], 403);
            }

            $obra = Obra::findOrFail($id);

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'ver_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Obra consultada: {$obra->nombre_obra}"
            ]);

            return response()->json([
                'message' => 'Obra obtenida exitosamente.',
                'data' => [
                    'id' => $obra->id,
                    'nombre_obra' => $obra->nombre_obra,
                    'estatus' => $obra->estatus,
                    'estatus_descripcion' => $obra->estatus_descripcion,
                    'avance' => $obra->avance,
                    'fecha_inicio' => $obra->fecha_inicio?->format('Y-m-d'),
                    'fecha_fin' => $obra->fecha_fin?->format('Y-m-d'),
                    'dias_transcurridos' => $obra->dias_transcurridos,
                    'dias_restantes' => $obra->dias_restantes,
                    'duracion_total' => $obra->duracion_total,
                    'porcentaje_tiempo_transcurrido' => $obra->porcentaje_tiempo_transcurrido,
                    'esta_atrasada' => $obra->esta_atrasada,
                    'created_at' => $obra->created_at,
                    'updated_at' => $obra->updated_at,
                ]
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Obra no encontrada.',
                'error' => 'La obra especificada no existe.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener la obra.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param UpdateObraRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateObraRequest $request, int $id): JsonResponse
    {
        try {
            $obra = Obra::findOrFail($id);
            $datosAnteriores = $obra->toArray();
            
            $obra->update($request->validated());

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'actualizar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Obra actualizada: {$obra->nombre_obra}. Campos modificados: " . implode(', ', array_keys($request->validated()))
            ]);

            return response()->json([
                'message' => 'Obra actualizada exitosamente.',
                'data' => [
                    'id' => $obra->id,
                    'nombre_obra' => $obra->nombre_obra,
                    'estatus' => $obra->estatus,
                    'estatus_descripcion' => $obra->estatus_descripcion,
                    'avance' => $obra->avance,
                    'fecha_inicio' => $obra->fecha_inicio?->format('Y-m-d'),
                    'fecha_fin' => $obra->fecha_fin?->format('Y-m-d'),
                    'dias_transcurridos' => $obra->dias_transcurridos,
                    'dias_restantes' => $obra->dias_restantes,
                    'duracion_total' => $obra->duracion_total,
                    'porcentaje_tiempo_transcurrido' => $obra->porcentaje_tiempo_transcurrido,
                    'esta_atrasada' => $obra->esta_atrasada,
                    'created_at' => $obra->created_at,
                    'updated_at' => $obra->updated_at,
                ]
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Obra no encontrada.',
                'error' => 'La obra especificada no existe.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la obra.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware
            if (!$request->user()->hasPermission('eliminar_obra')) {
                return response()->json([
                    'message' => 'No tienes permisos para eliminar obras.',
                    'error' => 'Acceso denegado'
                ], 403);
            }

            $obra = Obra::findOrFail($id);
            $nombreObra = $obra->nombre_obra;
            
            $obra->delete(); // Soft delete

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'eliminar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Obra eliminada (soft delete): {$nombreObra}"
            ]);

            return response()->json([
                'message' => 'Obra eliminada exitosamente.',
                'data' => [
                    'id' => $obra->id,
                    'nombre_obra' => $nombreObra,
                    'eliminada_en' => $obra->deleted_at
                ]
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Obra no encontrada.',
                'error' => 'La obra especificada no existe.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la obra.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a soft deleted resource.
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function restore(int $id, Request $request): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware
            if (!$request->user()->hasPermission('editar_obra')) {
                return response()->json([
                    'message' => 'No tienes permisos para restaurar obras.',
                    'error' => 'Acceso denegado'
                ], 403);
            }

            $obra = Obra::withTrashed()->findOrFail($id);

            if (!$obra->trashed()) {
                return response()->json([
                    'message' => 'La obra no está eliminada.',
                    'error' => 'No se puede restaurar una obra que no está eliminada.'
                ], 400);
            }

            $obra->restore();

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'restaurar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Obra restaurada: {$obra->nombre_obra}"
            ]);

            return response()->json([
                'message' => 'Obra restaurada exitosamente.',
                'data' => [
                    'id' => $obra->id,
                    'nombre_obra' => $obra->nombre_obra,
                    'estatus' => $obra->estatus,
                    'restaurada_en' => now()
                ]
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Obra no encontrada.',
                'error' => 'La obra especificada no existe.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al restaurar la obra.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available statuses for obras.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function estatus(Request $request): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware
            if (!$request->user()->hasPermission('ver_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para ver los estatus.',
                    'error' => 'Acceso denegado'
                ], 403);
            }

            $estatusDisponibles = collect(Obra::ESTADOS_VALIDOS)->map(function ($estatus) {
                $descripciones = [
                    Obra::ESTATUS_PLANIFICADA => 'Obra programada pero no iniciada',
                    Obra::ESTATUS_EN_PROGRESO => 'Obra activa en desarrollo',
                    Obra::ESTATUS_SUSPENDIDA => 'Obra temporalmente detenida',
                    Obra::ESTATUS_COMPLETADA => 'Obra finalizada exitosamente',
                    Obra::ESTATUS_CANCELADA => 'Obra cancelada antes de completarse',
                ];

                return [
                    'valor' => $estatus,
                    'nombre' => ucwords(str_replace('_', ' ', $estatus)),
                    'descripcion' => $descripciones[$estatus] ?? 'Sin descripción'
                ];
            });

            return response()->json([
                'message' => 'Estatus obtenidos exitosamente.',
                'data' => $estatusDisponibles
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los estatus.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
