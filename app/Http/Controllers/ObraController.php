<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreObraRequest;
use App\Http\Requests\UpdateObraRequest;
use App\Models\LogAccion;
use App\Models\Obra;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware,
            // pero mantenemos verificación adicional por seguridad
            if (! $request->user()->hasPermission('ver_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para ver las obras.',
                    'error' => 'Acceso denegado',
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

            // Filtro por rango de fechas
            if ($request->has('fecha_inicio') && $request->fecha_inicio !== '') {
                $query->where('fecha_inicio', '>=', $request->fecha_inicio);
            }

            if ($request->has('fecha_fin') && $request->fecha_fin !== '') {
                $query->where('fecha_fin', '<=', $request->fecha_fin);
            }

            // Filtro específico por fechas entre rango
            if ($request->has('desde') && $request->has('hasta')) {
                $query->entreFechas($request->desde, $request->hasta);
            }

            // Aplicar ordenamiento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');

            $allowedSorts = ['nombre_obra', 'estatus', 'avance', 'fecha_inicio', 'fecha_fin', 'created_at'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortDirection === 'asc' ? 'asc' : 'desc');
            }

            // Incluir o excluir eliminadas
            if ($request->get('incluir_eliminadas') === 'true' &&
                $request->user()->hasPermission('ver_obras_eliminadas')) {
                $query->withTrashed();
            }

            // Solo obras eliminadas
            if ($request->get('solo_eliminadas') === 'true' &&
                $request->user()->hasPermission('ver_obras_eliminadas')) {
                $query->onlyTrashed();
            }

            // Paginación
            $perPage = $request->get('per_page', 15);
            $perPage = is_numeric($perPage) && $perPage > 0 ? min($perPage, 100) : 15;
            $obras = $query->paginate($perPage);

            // Agregar atributos calculados a cada obra
            $obras->getCollection()->transform(function ($obra) {
                return [
                    'id' => $obra->id,
                    'nombre_obra' => $obra->nombre_obra,
                    'estatus' => $obra->estatus,
                    'estatus_descripcion' => $obra->estatus_descripcion,
                    'avance' => $obra->avance,
                    'fecha_inicio' => $obra->fecha_inicio->format('Y-m-d'),
                    'fecha_fin' => $obra->fecha_fin?->format('Y-m-d'),
                    'dias_transcurridos' => $obra->dias_transcurridos,
                    'dias_restantes' => $obra->dias_restantes,
                    'duracion_total' => $obra->duracion_total,
                    'porcentaje_tiempo_transcurrido' => $obra->porcentaje_tiempo_transcurrido,
                    'esta_atrasada' => $obra->esta_atrasada,
                    'created_at' => $obra->created_at,
                    'updated_at' => $obra->updated_at,
                    'deleted_at' => $obra->deleted_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Obras obtenidas exitosamente.',
                'data' => $obras,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las obras.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreObraRequest  $request
     * @return JsonResponse
     */
    public function store(StoreObraRequest $request): JsonResponse
    {
        try {
            if (! $request->user()->hasPermission('crear_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para crear obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            // Sanitizar datos de entrada
            $data = $request->validated();
            $data['nombre_obra'] = strip_tags(trim($data['nombre_obra']));

            $obra = Obra::create($data);

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'crear_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => 'Obra creada: '.$obra->nombre_obra,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Obra creada exitosamente.',
                'data' => [
                    'id' => $obra->id,
                    'nombre_obra' => $obra->nombre_obra,
                    'estatus' => $obra->estatus,
                    'estatus_descripcion' => $obra->estatus_descripcion,
                    'avance' => $obra->avance,
                    'fecha_inicio' => $obra->fecha_inicio->format('Y-m-d'),
                    'fecha_fin' => $obra->fecha_fin?->format('Y-m-d'),
                    'dias_transcurridos' => $obra->dias_transcurridos,
                    'dias_restantes' => $obra->dias_restantes,
                    'duracion_total' => $obra->duracion_total,
                    'porcentaje_tiempo_transcurrido' => $obra->porcentaje_tiempo_transcurrido,
                    'esta_atrasada' => $obra->esta_atrasada,
                    'created_at' => $obra->created_at,
                    'updated_at' => $obra->updated_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la obra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $obra
     * @param  Request  $request
     * @return JsonResponse
     */
    public function show($obra, Request $request): JsonResponse
    {
        try {
            // Verificar permisos
            if (! $request->user()->hasPermission('ver_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para ver esta obra.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            // Obtener la obra por ID
            $obraModel = \App\Models\Obra::findOrFail($obra);

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'ver_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obraModel->id,
                'detalles' => 'Vista de obra: '.$obraModel->nombre_obra,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Obra obtenida exitosamente.',
                'data' => [
                    'id' => $obraModel->id,
                    'nombre_obra' => $obraModel->nombre_obra,
                    'estatus' => $obraModel->estatus,
                    'estatus_descripcion' => $obraModel->estatus_descripcion,
                    'avance' => $obraModel->avance,
                    'fecha_inicio' => $obraModel->fecha_inicio->format('Y-m-d'),
                    'fecha_fin' => $obraModel->fecha_fin?->format('Y-m-d'),
                    'dias_transcurridos' => $obraModel->dias_transcurridos,
                    'dias_restantes' => $obraModel->dias_restantes,
                    'duracion_total' => $obraModel->duracion_total,
                    'porcentaje_tiempo_transcurrido' => $obraModel->porcentaje_tiempo_transcurrido,
                    'esta_atrasada' => $obraModel->esta_atrasada,
                    'created_at' => $obraModel->created_at,
                    'updated_at' => $obraModel->updated_at,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Obra no encontrada.',
                'error' => 'Recurso no existe',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la obra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateObraRequest  $request
     * @param  mixed  $obra
     * @return JsonResponse
     */
    public function update(UpdateObraRequest $request, $obra): JsonResponse
    {
        try {
            if (! $request->user()->hasPermission('editar_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para editar obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            $obraModel = \App\Models\Obra::findOrFail($obra);
            $estadoAnterior = $obraModel->estatus;

            // Sanitizar datos de entrada
            $data = $request->validated();
            if (isset($data['nombre_obra'])) {
                $data['nombre_obra'] = strip_tags(trim($data['nombre_obra']));
            }

            $obraModel->update($data);

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'actualizar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obraModel->id,
                'detalles' => 'Obra actualizada: '.$obraModel->nombre_obra.'. Estado anterior: '.$estadoAnterior,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Obra actualizada exitosamente.',
                'data' => [
                    'id' => $obraModel->id,
                    'nombre_obra' => $obraModel->nombre_obra,
                    'estatus' => $obraModel->estatus,
                    'estatus_descripcion' => $obraModel->estatus_descripcion,
                    'avance' => $obraModel->avance,
                    'fecha_inicio' => $obraModel->fecha_inicio->format('Y-m-d'),
                    'fecha_fin' => $obraModel->fecha_fin?->format('Y-m-d'),
                    'dias_transcurridos' => $obraModel->dias_transcurridos,
                    'dias_restantes' => $obraModel->dias_restantes,
                    'duracion_total' => $obraModel->duracion_total,
                    'porcentaje_tiempo_transcurrido' => $obraModel->porcentaje_tiempo_transcurrido,
                    'esta_atrasada' => $obraModel->esta_atrasada,
                    'created_at' => $obraModel->created_at,
                    'updated_at' => $obraModel->updated_at,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Obra no encontrada.',
                'error' => 'Recurso no existe',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la obra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param  Request  $request
     * @param  mixed  $obra
     * @return JsonResponse
     */
    public function destroy(Request $request, $obra): JsonResponse
    {
        try {
            if (! $request->user()->hasPermission('eliminar_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para eliminar obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            $obraModel = \App\Models\Obra::findOrFail($obra);
            $obraModel->delete();

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'eliminar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obraModel->id,
                'detalles' => 'Obra eliminada: '.$obraModel->nombre_obra,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Obra eliminada exitosamente.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Obra no encontrada.',
                'error' => 'Recurso no existe',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la obra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore a soft deleted obra.
     *
     * @param  Request  $request
     * @param  mixed  $obra
     * @return JsonResponse
     */
    public function restore(Request $request, $obra): JsonResponse
    {
        try {
            if (! $request->user()->hasPermission('restaurar_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para restaurar obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            $obraModel = \App\Models\Obra::withTrashed()->findOrFail($obra);

            if (! $obraModel->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La obra no está eliminada.',
                    'error' => 'Estado inválido',
                ], 400);
            }

            $obraModel->restore();

            // Registrar acción en log
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'fecha_hora' => now(),
                'accion' => 'restaurar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obraModel->id,
                'detalles' => 'Obra restaurada: '.$obraModel->nombre_obra,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Obra restaurada exitosamente.',
                'data' => [
                    'id' => $obraModel->id,
                    'nombre_obra' => $obraModel->nombre_obra,
                    'estatus' => $obraModel->estatus,
                    'avance' => $obraModel->avance,
                    'fecha_inicio' => $obraModel->fecha_inicio->format('Y-m-d'),
                    'fecha_fin' => $obraModel->fecha_fin?->format('Y-m-d'),
                    'created_at' => $obraModel->created_at,
                    'updated_at' => $obraModel->updated_at,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Obra no encontrada.',
                'error' => 'Recurso no existe',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar la obra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get status options for obras.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getEstatus(Request $request): JsonResponse
    {
        try {
            if (! $request->user()->hasPermission('ver_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para ver los estatus de obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            $estatus = [
                \App\Models\Obra::ESTADO_PLANIFICACION => 'Planificación',
                \App\Models\Obra::ESTADO_EN_PROGRESO => 'En Progreso',
                \App\Models\Obra::ESTADO_PAUSADA => 'Pausada',
                \App\Models\Obra::ESTADO_COMPLETADA => 'Completada',
                \App\Models\Obra::ESTADO_CANCELADA => 'Cancelada',
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estatus obtenidos exitosamente.',
                'data' => $estatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los estatus.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
