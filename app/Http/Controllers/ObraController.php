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
     * Constructor - Aplicar middleware de au            $estatus = [
                [
                    'valor' => 'planificada',
                    'nombre' => 'Planificada',
                    'descripcion' => 'Obra en etapa de planificación'
                ],
                [
                    'valor' => 'en_progreso',
                    'nombre' => 'En Progreso',
                    'descripcion' => 'Obra en ejecución'
                ],
                [
                    'valor' => 'pausada',
                    'nombre' => 'Pausada',
                    'descripcion' => 'Obra temporalmente suspendida'
                ],
                [
                    'valor' => 'completada',
                    'nombre' => 'Completada',
                    'descripcion' => 'Obra finalizada exitosamente'
                ],
                [
                    'valor' => 'cancelada',
                    'nombre' => 'Cancelada',
                    'descripcion' => 'Obra cancelada'
                ],
            ];ción
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
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

            // Filtro por búsqueda en nombre (admite tanto 'search' como 'buscar')
            $searchTerm = $request->get('search') ?: $request->get('buscar');
            if ($searchTerm) {
                $query->where('nombre_obra', 'like', '%'.$searchTerm.'%');
            }

            // Filtro por rango de fechas de inicio
            if ($request->has('fecha_inicio_desde') && $request->fecha_inicio_desde !== '') {
                $query->where('fecha_inicio', '>=', $request->fecha_inicio_desde);
            }

            if ($request->has('fecha_inicio_hasta') && $request->fecha_inicio_hasta !== '') {
                $query->where('fecha_inicio', '<=', $request->fecha_inicio_hasta);
            }

            // Filtro por rango de fechas de fin
            if ($request->has('fecha_fin_desde') && $request->fecha_fin_desde !== '') {
                $query->where('fecha_fin', '>=', $request->fecha_fin_desde);
            }

            if ($request->has('fecha_fin_hasta') && $request->fecha_fin_hasta !== '') {
                $query->where('fecha_fin', '<=', $request->fecha_fin_hasta);
            }

            // Filtro por avance
            if ($request->has('avance_min') && $request->avance_min !== '') {
                $query->where('avance', '>=', $request->avance_min);
            }

            if ($request->has('avance_max') && $request->avance_max !== '') {
                $query->where('avance', '<=', $request->avance_max);
            }

            // Filtro obras activas (excluye canceladas)
            if ($request->boolean('solo_activas')) {
                $query->activas();
            }

            // Incluir obras eliminadas si se solicita
            if ($request->boolean('include_deleted')) {
                $query->withTrashed();
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'nombre_obra');
            $sortOrder = $request->get('sort_order', 'asc');

            $allowedSortFields = ['nombre_obra', 'estatus', 'avance', 'fecha_inicio', 'fecha_fin', 'fecha_creacion'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Paginación con validación
            $perPage = $request->get('per_page', 15);

            // Validar que per_page sea un número positivo
            if (! is_numeric($perPage) || $perPage < 1) {
                $perPage = 15; // Valor por defecto
            }

            $perPage = min((int) $perPage, 100); // Máximo 100 por página

            // Validar que page sea un número positivo
            $page = $request->get('page', 1);
            if (! is_numeric($page) || $page < 1) {
                $page = 1; // Valor por defecto
            }

            $obras = $query->paginate($perPage, ['*'], 'page', $page);

            // Log de acción
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'listar_obras',
                'tabla_afectada' => 'obras',
                'detalles' => 'Filtros aplicados: '.json_encode($request->only([
                    'estatus', 'search', 'fecha_inicio_desde', 'fecha_inicio_hasta',
                    'fecha_fin_desde', 'fecha_fin_hasta', 'avance_min', 'avance_max',
                    'include_deleted', 'sort_by', 'sort_order',
                ])),
            ]);

            return response()->json([
                'message' => 'Obras obtenidas exitosamente.',
                'data' => $obras,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las obras.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreObraRequest $request): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware,
            // pero mantenemos verificación adicional por seguridad
            if (! $request->user()->hasPermission('crear_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para crear obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            $obra = Obra::create($request->validated());

            // Log de acción
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'crear_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => 'Obra creada: '.$obra->nombre_obra,
            ]);

            return response()->json([
                'message' => 'Obra creada exitosamente.',
                'data' => $obra,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la obra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $obra
     */
    public function show($obra, Request $request): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware,
            // pero mantenemos verificación adicional por seguridad
            if (! $request->user()->hasPermission('ver_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para ver obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            // Buscar la obra por ID o lanzar excepción si no existe
            if (is_numeric($obra)) {
                $obraModel = Obra::findOrFail($obra);
            } else {
                throw new ModelNotFoundException('Obra no encontrada.');
            }

            // Log de acción
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'ver_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obraModel->id,
                'detalles' => 'Obra consultada: '.$obraModel->nombre_obra,
            ]);

            return response()->json([
                'message' => 'Obra obtenida exitosamente.',
                'data' => $obraModel,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Obra no encontrada.',
                'error' => 'Recurso no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener la obra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  mixed  $obra
     */
    public function update(UpdateObraRequest $request, $obra): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware,
            // pero mantenemos verificación adicional por seguridad
            if (! $request->user()->hasPermission('actualizar_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para actualizar obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            // Buscar la obra por ID o lanzar excepción si no existe
            if (is_numeric($obra)) {
                $obraModel = Obra::findOrFail($obra);
            } else {
                throw new ModelNotFoundException('Obra no encontrada.');
            }

            $datosAnteriores = $obraModel->toArray();
            $obraModel->update($request->validated());

            // Log de acción
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'actualizar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obraModel->id,
                'detalles' => 'Obra actualizada: '.$obraModel->nombre_obra.'. Datos anteriores: '.json_encode($datosAnteriores),
            ]);

            return response()->json([
                'message' => 'Obra actualizada exitosamente.',
                'data' => $obraModel,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Obra no encontrada.',
                'error' => 'Recurso no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la obra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param  mixed  $obra
     */
    public function destroy(Request $request, $obra): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware,
            // pero mantenemos verificación adicional por seguridad
            if (! $request->user()->hasPermission('eliminar_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para eliminar obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            // Buscar la obra por ID o lanzar excepción si no existe
            if (is_numeric($obra)) {
                $obraModel = Obra::findOrFail($obra);
            } else {
                throw new ModelNotFoundException('Obra no encontrada.');
            }

            $obraModel->delete();

            // Log de acción
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'eliminar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obraModel->id,
                'detalles' => 'Obra eliminada (soft delete): '.$obraModel->nombre_obra,
            ]);

            return response()->json([
                'message' => 'Obra eliminada exitosamente.',
                'data' => null,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Obra no encontrada.',
                'error' => 'Recurso no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la obra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore a soft-deleted resource.
     *
     * @param  mixed  $id
     */
    public function restore(Request $request, $id): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware,
            // pero mantenemos verificación adicional por seguridad
            if (! $request->user()->hasPermission('restaurar_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para restaurar obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            // Verificar si la obra existe (sin eliminar)
            if (is_numeric($id)) {
                $obraExistente = Obra::find($id);
                if ($obraExistente) {
                    return response()->json([
                        'message' => 'La obra no está eliminada.',
                        'error' => 'Solicitud inválida',
                    ], 400);
                }

                // Buscar la obra eliminada por ID o lanzar excepción si no existe
                $obraModel = Obra::onlyTrashed()->findOrFail($id);
            } else {
                throw new ModelNotFoundException('Obra eliminada no encontrada.');
            }

            $obraModel->restore();

            // Log de acción
            LogAccion::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'restaurar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obraModel->id,
                'detalles' => 'Obra restaurada: '.$obraModel->nombre_obra,
            ]);

            return response()->json([
                'message' => 'Obra restaurada exitosamente.',
                'data' => $obraModel,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Obra eliminada no encontrada.',
                'error' => 'Recurso no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al restaurar la obra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available status options for obras.
     */
    public function getEstatus(Request $request): JsonResponse
    {
        try {
            // Los permisos ya se verifican en el middleware,
            // pero mantenemos verificación adicional por seguridad
            if (! $request->user()->hasPermission('ver_obras')) {
                return response()->json([
                    'message' => 'No tienes permisos para ver información de obras.',
                    'error' => 'Acceso denegado',
                ], 403);
            }

            $estatus = [
                [
                    'valor' => 'planificada',
                    'nombre' => 'Planificada',
                    'descripcion' => 'Obra en etapa de planificación',
                ],
                [
                    'valor' => 'en_progreso',
                    'nombre' => 'En Progreso',
                    'descripcion' => 'Obra en ejecución',
                ],
                [
                    'valor' => 'pausada',
                    'nombre' => 'Pausada',
                    'descripcion' => 'Obra temporalmente suspendida',
                ],
                [
                    'valor' => 'completada',
                    'nombre' => 'Completada',
                    'descripcion' => 'Obra finalizada exitosamente',
                ],
                [
                    'valor' => 'cancelada',
                    'nombre' => 'Cancelada',
                    'descripcion' => 'Obra cancelada',
                ],
            ];

            return response()->json([
                'message' => 'Estatus de obras obtenidos exitosamente.',
                'data' => $estatus,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los estatus de obras.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
