<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Enums\EstadoVehiculo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VehiculoSearchController extends Controller
{
    /**
     * Búsqueda en tiempo real de vehículos
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $estado = $request->get('estado', '');
        $anio = $request->get('anio', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Query de búsqueda requerido'
            ], 400);
        }

        try {
            $vehiculos = Vehiculo::with(['tipoActivo'])
                ->buscar($query)
                ->when($estado, function ($q) use ($estado) {
                    return $q->where('estatus', $estado);
                })
                ->when($anio, function ($q) use ($anio) {
                    return $q->porAnio($anio);
                })
                ->limit(10)
                ->get();

            $results = $vehiculos->map(function ($vehiculo) {
                return [
                    'id' => $vehiculo->id,
                    'marca' => $vehiculo->marca,
                    'modelo' => $vehiculo->modelo,
                    'anio' => $vehiculo->anio,
                    'placas' => $vehiculo->placas,
                    'n_serie' => $vehiculo->n_serie,
                    'estatus' => $vehiculo->estatus,
                    'estatus_nombre' => EstadoVehiculo::tryFrom($vehiculo->estatus)?->nombre() ?? 'N/A',
                    'tipo_activo' => $vehiculo->tipoActivo ? $vehiculo->tipoActivo->nombre : 'N/A',
                    'url' => route('vehiculos.show', $vehiculo->id)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $results,
                'count' => $results->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener sugerencias para autocompletado
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query muy corto'
            ], 400);
        }

        try {
            // Obtener marcas únicas
            $marcas = Vehiculo::where('marca', 'LIKE', "%{$query}%")
                ->distinct()
                ->pluck('marca')
                ->take(5);

            // Obtener modelos únicos
            $modelos = Vehiculo::where('modelo', 'LIKE', "%{$query}%")
                ->distinct()
                ->pluck('modelo')
                ->take(5);

            $suggestions = collect()
                ->merge($marcas->map(fn($marca) => ['type' => 'marca', 'value' => $marca]))
                ->merge($modelos->map(fn($modelo) => ['type' => 'modelo', 'value' => $modelo]))
                ->take(10);

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo sugerencias: ' . $e->getMessage()
            ], 500);
        }
    }
}