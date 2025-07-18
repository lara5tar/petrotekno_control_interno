<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehiculoRequest;
use App\Http\Requests\UpdateVehiculoRequest;
use App\Models\CatalogoEstatus;
use App\Models\LogAccion;
use App\Models\Vehiculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('ver_vehiculos')) {
            return response()->json([
                'message' => 'No tienes permisos para ver vehículos',
            ], 403);
        }

        try {
            $query = Vehiculo::with('estatus');

            // Aplicar filtros
            if ($request->filled('marca')) {
                $query->porMarca($request->marca);
            }

            if ($request->filled('modelo')) {
                $query->porModelo($request->modelo);
            }

            if ($request->filled('estatus_id')) {
                $query->porEstatus($request->estatus_id);
            }

            if ($request->filled('anio_inicio')) {
                $query->porAnio($request->anio_inicio, $request->anio_fin);
            }

            // Búsqueda general
            if ($request->filled('buscar')) {
                $query->buscar($request->buscar);
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'id');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $vehiculos = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Vehículos obtenidos correctamente',
                'data' => $vehiculos,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener vehículos: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehiculoRequest $request): JsonResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('crear_vehiculos')) {
            return response()->json([
                'message' => 'No tienes permisos para crear vehículos',
            ], 403);
        }

        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::create($request->validated());

            // Registrar log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'vehiculo' => $vehiculo->toArray(),
                ]),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo creado correctamente',
                'data' => $vehiculo->load('estatus'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear vehículo: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('ver_vehiculos')) {
            return response()->json([
                'message' => 'No tienes permisos para ver vehículos',
            ], 403);
        }

        try {
            $vehiculo = Vehiculo::with('estatus')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Vehículo obtenido correctamente',
                'data' => $vehiculo,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vehículo no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener vehículo: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehiculoRequest $request, string $id): JsonResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('editar_vehiculos')) {
            return response()->json([
                'message' => 'No tienes permisos para editar vehículos',
            ], 403);
        }

        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::findOrFail($id);
            $datosOriginales = $vehiculo->toArray();

            $vehiculo->update($request->validated());

            // Registrar log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'datos_originales' => $datosOriginales,
                    'datos_nuevos' => $vehiculo->toArray(),
                ]),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo actualizado correctamente',
                'data' => $vehiculo->load('estatus'),
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Vehículo no encontrado',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar vehículo: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('eliminar_vehiculos')) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar vehículos',
            ], 403);
        }

        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::findOrFail($id);

            // Verificar si el vehículo está en uso (tiene asignaciones activas)
            // TODO: Implementar esta verificación cuando se tenga el modelo Asignacion

            $vehiculo->delete();

            // Registrar log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'vehiculo_eliminado' => $vehiculo->toArray(),
                ]),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo eliminado correctamente',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Vehículo no encontrado',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar vehículo: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $id): JsonResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('restaurar_vehiculos')) {
            return response()->json([
                'message' => 'No tienes permisos para restaurar vehículos',
            ], 403);
        }

        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::withTrashed()->findOrFail($id);

            if (! $vehiculo->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El vehículo no está eliminado',
                ], 400);
            }

            $vehiculo->restore();

            // Registrar log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'restaurar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'vehiculo_restaurado' => $vehiculo->toArray(),
                ]),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo restaurado correctamente',
                'data' => $vehiculo->load('estatus'),
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Vehículo no encontrado',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar vehículo: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available status options for vehicles.
     */
    public function estatusOptions(): JsonResponse
    {
        try {
            $estatus = CatalogoEstatus::select('id', 'nombre_estatus')->get();

            return response()->json([
                'success' => true,
                'message' => 'Opciones de estatus obtenidas correctamente',
                'data' => $estatus,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener opciones de estatus: '.$e->getMessage(),
            ], 500);
        }
    }
}
