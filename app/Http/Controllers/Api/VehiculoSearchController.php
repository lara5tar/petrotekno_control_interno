<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Enums\EstadoVehiculo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class VehiculoSearchController extends Controller
{
    /**
     * Constructor - Verificar autenticación
     */
    public function __construct()
    {
        // Remover el middleware de autenticación ya que las rutas están en web.php con auth
    }

    /**
     * Búsqueda en tiempo real de vehículos
     */
    public function search(Request $request): JsonResponse
    {
        // Verificar autorización
        if (!auth()->check() || !auth()->user()->hasPermission('ver_vehiculos')) {
            return response()->json([
                'vehiculos' => [],
                'total' => 0,
                'mensaje' => 'No tienes permisos para ver vehículos'
            ], 403);
        }

        $termino = $request->get('q', $request->get('buscar', ''));
        $estado = $request->get('estado');
        $anio = $request->get('anio');
        $limit = $request->get('limit', 10);

        // Si no hay término de búsqueda, devolver array vacío
        if (empty(trim($termino))) {
            return response()->json([
                'vehiculos' => [],
                'total' => 0,
                'mensaje' => 'Ingrese un término de búsqueda'
            ]);
        }

        // Query base
        $query = Vehiculo::with(['tipoActivo']);

        // Aplicar búsqueda
        $query->buscar($termino);

        // Aplicar filtros adicionales
        if ($estado) {
            $query->porEstado($estado);
        }

        if ($anio) {
            $query->porAnio($anio);
        }

        // Obtener resultados limitados
        $vehiculos = $query->orderBy('marca')
                          ->orderBy('modelo')
                          ->limit($limit)
                          ->get();

        // Formatear resultados para la respuesta
        $resultados = $vehiculos->map(function ($vehiculo) {
            return [
                'id' => $vehiculo->id,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                'anio' => $vehiculo->anio,
                'placas' => $vehiculo->placas,
                'n_serie' => $vehiculo->n_serie,
                'estatus' => $vehiculo->estatus,
                'estatus_nombre' => $vehiculo->estatus ? 
                    ($vehiculo->estatus instanceof EstadoVehiculo ? 
                        $vehiculo->estatus->nombre() : 
                        EstadoVehiculo::fromValue($vehiculo->estatus)->nombre()) : 'Sin estado',
                'tipo_activo' => $vehiculo->tipoActivo?->nombre ?? 'Sin tipo',
                'nombre_completo' => $vehiculo->nombre_completo,
                'url' => route('vehiculos.show', $vehiculo->id)
            ];
        });

        return response()->json([
            'vehiculos' => $resultados,
            'total' => $vehiculos->count(),
            'limite_alcanzado' => $vehiculos->count() >= $limit,
            'mensaje' => $vehiculos->count() > 0 
                ? "Se encontraron {$vehiculos->count()} vehículos" 
                : 'No se encontraron vehículos con ese término'
        ]);
    }

    /**
     * Sugerencias de búsqueda
     */
    public function suggestions(Request $request): JsonResponse
    {
        $termino = $request->get('q', $request->get('buscar', ''));
        
        if (strlen($termino) < 2) {
            return response()->json(['sugerencias' => []]);
        }

        // Obtener sugerencias de marcas y modelos únicos
        $marcas = Vehiculo::select('marca')
                         ->where('marca', 'like', "%{$termino}%")
                         ->distinct()
                         ->limit(5)
                         ->pluck('marca');

        $modelos = Vehiculo::select('modelo')
                          ->where('modelo', 'like', "%{$termino}%")
                          ->distinct()
                          ->limit(5)
                          ->pluck('modelo');

        $sugerencias = collect()
            ->merge($marcas->map(fn($marca) => ['tipo' => 'marca', 'valor' => $marca]))
            ->merge($modelos->map(fn($modelo) => ['tipo' => 'modelo', 'valor' => $modelo]))
            ->take(8);

        return response()->json(['sugerencias' => $sugerencias]);
    }
}