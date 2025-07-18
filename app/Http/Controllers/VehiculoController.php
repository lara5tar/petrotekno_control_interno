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

        if ($request->filled('anio_inicio') && $request->filled('anio_fin')) {
            $query->porAnio($request->anio_inicio, $request->anio_fin);
        }

        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        // Incluir eliminados si se solicita
        if ($request->boolean('incluir_eliminados')) {
            $query->withTrashed();
        }

        $vehiculos = $query->paginate($request->get('per_page', 15));

        return response()->json($vehiculos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehiculoRequest $request): JsonResponse
    {
        $vehiculo = Vehiculo::create($request->validated());
        $vehiculo->load('estatus');

        // Log de acción
        LogAccion::create([
            'usuario_id' => Auth::id(),
            'accion' => 'crear_vehiculo',
            'tabla_afectada' => 'vehiculos',
            'registro_id' => $vehiculo->id,
            'detalles' => 'Vehículo creado: ' . $vehiculo->nombre_completo,
        ]);

        return response()->json([
            'message' => 'Vehículo creado exitosamente',
            'vehiculo' => $vehiculo,
        ], 201);
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

        $vehiculo->load('estatus');

        return response()->json($vehiculo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehiculoRequest $request, Vehiculo $vehiculo): JsonResponse
    {
        $datosOriginales = $vehiculo->toArray();
        $vehiculo->update($request->validated());
        $vehiculo->load('estatus');

        // Log de acción
        LogAccion::create([
            'usuario_id' => Auth::id(),
            'accion' => 'editar_vehiculo',
            'tabla_afectada' => 'vehiculos',
            'registro_id' => $vehiculo->id,
            'detalles' => 'Vehículo actualizado: ' . $vehiculo->nombre_completo,
        ]);

        return response()->json([
            'message' => 'Vehículo actualizado exitosamente',
            'vehiculo' => $vehiculo,
        ]);
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

        $nombreCompleto = $vehiculo->nombre_completo;
        $vehiculo->delete();

        // Log de acción
        LogAccion::create([
            'usuario_id' => Auth::id(),
            'accion' => 'eliminar_vehiculo',
            'tabla_afectada' => 'vehiculos',
            'registro_id' => $vehiculo->id,
            'detalles' => 'Vehículo eliminado: ' . $nombreCompleto,
        ]);

        return response()->json([
            'message' => 'Vehículo eliminado exitosamente',
        ]);
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

        $vehiculo = Vehiculo::withTrashed()->findOrFail($id);
        $vehiculo->restore();
        $vehiculo->load('estatus');

        // Log de acción
        LogAccion::create([
            'usuario_id' => Auth::id(),
            'accion' => 'restaurar_vehiculo',
            'tabla_afectada' => 'vehiculos',
            'registro_id' => $vehiculo->id,
            'detalles' => 'Vehículo restaurado: ' . $vehiculo->nombre_completo,
        ]);

        return response()->json([
            'message' => 'Vehículo restaurado exitosamente',
            'vehiculo' => $vehiculo,
        ]);
    }

    /**
     * Get estatus options for vehicles.
     */
    public function estatusOptions(): JsonResponse
    {
        $estatus = CatalogoEstatus::activos()->get(['id', 'nombre_estatus']);

        return response()->json($estatus);
    }
}
