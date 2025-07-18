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

            // Filtros opcionales
            if ($request->filled('marca')) {
                $query->where('marca', 'like', '%' . $request->marca . '%');
            }

            if ($request->filled('modelo')) {
                $query->where('modelo', 'like', '%' . $request->modelo . '%');
            }

            if ($request->filled('estatus_id')) {
                $query->where('estatus_id', $request->estatus_id);
            }

            if ($request->filled('anio')) {
                $query->where('anio', $request->anio);
            }

            // Paginación
            $perPage = $request->get('per_page', 15);
            $vehiculos = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Vehículos obtenidos exitosamente',
                'data' => $vehiculos,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los vehículos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehiculoRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::create($request->validated());
            $vehiculo->load('estatus');

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => 'Vehículo creado: ' . $vehiculo->nombre_completo,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo creado exitosamente',
                'data' => $vehiculo,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el vehículo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehiculo $vehiculo): JsonResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('ver_vehiculos')) {
            return response()->json([
                'message' => 'No tienes permisos para ver vehículos',
            ], 403);
        }

        try {
            $vehiculo->load('estatus');

            return response()->json([
                'success' => true,
                'message' => 'Vehículo obtenido exitosamente',
                'data' => $vehiculo,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el vehículo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehiculoRequest $request, Vehiculo $vehiculo): JsonResponse
    {
        try {
            DB::beginTransaction();

            $datosOriginales = $vehiculo->toArray();
            $vehiculo->update($request->validated());
            $vehiculo->load('estatus');

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => 'Vehículo actualizado: ' . $vehiculo->nombre_completo,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo actualizado exitosamente',
                'data' => $vehiculo,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el vehículo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehiculo $vehiculo): JsonResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('eliminar_vehiculo')) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar vehículos',
            ], 403);
        }

        try {
            DB::beginTransaction();

            $vehiculo->delete();

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => 'Vehículo eliminado: ' . $vehiculo->nombre_completo,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo eliminado exitosamente',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el vehículo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id): JsonResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('editar_vehiculo')) {
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
            $vehiculo->load('estatus');

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'restaurar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => 'Vehículo restaurado: ' . $vehiculo->nombre_completo,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo restaurado exitosamente',
                'data' => $vehiculo,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar el vehículo',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get estatus options for vehicles.
     */
    public function estatusOptions(): JsonResponse
    {
        try {
            $estatus = CatalogoEstatus::activos()->get(['id', 'nombre_estatus']);

            return response()->json([
                'success' => true,
                'message' => 'Estatus obtenidos exitosamente',
                'data' => $estatus,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los estatus',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
