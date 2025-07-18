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
     * Constructor para aplicar middleware de autenticación
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
            $query = Vehiculo::with('estatus');

            // Filtros
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

            // Incluir eliminados si se solicita
            if ($request->boolean('incluir_eliminados')) {
                $query->withTrashed();
            }

            // Ordenamiento
            $ordenPor = $request->get('orden_por', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            $query->orderBy($ordenPor, $direccion);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $vehiculos = $query->paginate($perPage);

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'listar_vehiculos',
                'tabla_afectada' => 'vehiculos',
                'detalles' => json_encode([
                    'filtros' => $request->only(['marca', 'modelo', 'estatus_id', 'buscar']),
                    'total_resultados' => $vehiculos->total(),
                ]),
                'fecha_hora' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehículos obtenidos exitosamente',
                'data' => $vehiculos->items(),
                'pagination' => [
                    'current_page' => $vehiculos->currentPage(),
                    'last_page' => $vehiculos->lastPage(),
                    'per_page' => $vehiculos->perPage(),
                    'total' => $vehiculos->total(),
                    'from' => $vehiculos->firstItem(),
                    'to' => $vehiculos->lastItem(),
                ],
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

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'vehiculo_creado' => $vehiculo->toArray(),
                ]),
                'fecha_hora' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo creado exitosamente',
                'data' => $vehiculo->load('estatus'),
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
    public function show(string $id): JsonResponse
    {
        try {
            $vehiculo = Vehiculo::with('estatus')->findOrFail($id);

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'ver_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'vehiculo_consultado' => $vehiculo->id,
                ]),
                'fecha_hora' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehículo obtenido exitosamente',
                'data' => $vehiculo,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vehículo no encontrado',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehiculoRequest $request, string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::findOrFail($id);
            $datosAnteriores = $vehiculo->toArray();

            $vehiculo->update($request->validated());

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'datos_anteriores' => $datosAnteriores,
                    'datos_nuevos' => $vehiculo->fresh()->toArray(),
                ]),
                'fecha_hora' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo actualizado exitosamente',
                'data' => $vehiculo->load('estatus'),
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
    public function destroy(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::findOrFail($id);
            $datosVehiculo = $vehiculo->toArray();

            $vehiculo->delete();

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'vehiculo_eliminado' => $datosVehiculo,
                ]),
                'fecha_hora' => now(),
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
     * Restore a soft deleted resource.
     */
    public function restore(string $id): JsonResponse
    {
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

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'restaurar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'vehiculo_restaurado' => $vehiculo->toArray(),
                ]),
                'fecha_hora' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo restaurado exitosamente',
                'data' => $vehiculo->load('estatus'),
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
     * Get all status options for dropdowns
     */
    public function getEstatus(): JsonResponse
    {
        try {
            $estatus = CatalogoEstatus::activos()->get();

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
