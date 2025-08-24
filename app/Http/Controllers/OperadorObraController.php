<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\HistorialOperadorVehiculo;
use App\Models\AsignacionObra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperadorObraController extends Controller
{
    /**
     * Mostrar vista principal de obras por operador
     */
    public function index()
    {
        // Obtener operadores que han tenido asignaciones de obra
        $operadoresConObras = Personal::whereHas('historialOperadorVehiculo', function ($query) {
            $query->whereNotNull('obra_id');
        })
        ->withCount(['historialOperadorVehiculo as total_asignaciones_obra' => function ($query) {
            $query->whereNotNull('obra_id');
        }])
        ->orderBy('nombre_completo')
        ->get();

        return view('operadores.obras-por-operador', compact('operadoresConObras'));
    }

    /**
     * Mostrar detalle de obras de un operador específico
     */
    public function show(Personal $operador)
    {
        // Obtener historial de obras del operador usando el método del modelo
        $historialObras = HistorialOperadorVehiculo::historialObrasPorOperador($operador->id);

        // Obtener estadísticas generales
        $estadisticas = [
            'total_obras' => $historialObras->count(),
            'total_asignaciones' => HistorialOperadorVehiculo::where('operador_nuevo_id', $operador->id)
                ->whereNotNull('obra_id')
                ->count(),
            'obra_actual' => $this->obtenerObraActual($operador->id),
            'vehiculo_actual' => $operador->vehiculoActual(),
        ];

        // Obtener historial detallado reciente (últimos 20 registros)
        $historialDetallado = HistorialOperadorVehiculo::where('operador_nuevo_id', $operador->id)
            ->whereNotNull('obra_id')
            ->with(['vehiculo', 'obra', 'usuarioAsigno'])
            ->orderBy('fecha_asignacion', 'desc')
            ->paginate(20);

        return view('operadores.detalle-obras-operador', compact(
            'operador', 
            'historialObras', 
            'estadisticas', 
            'historialDetallado'
        ));
    }

    /**
     * API: Obtener obras de un operador específico
     */
    public function apiObrasOperador(Personal $operador)
    {
        $historialObras = HistorialOperadorVehiculo::historialObrasPorOperador($operador->id);

        return response()->json([
            'success' => true,
            'operador' => [
                'id' => $operador->id,
                'nombre' => $operador->nombre_completo,
                'categoria' => $operador->categoria->nombre ?? 'Sin categoría',
            ],
            'obras' => $historialObras->map(function ($item) {
                return [
                    'obra_id' => $item['obra']->id,
                    'obra_nombre' => $item['obra']->nombre,
                    'obra_ubicacion' => $item['obra']->ubicacion,
                    'primera_asignacion' => $item['primera_asignacion']->format('d/m/Y H:i'),
                    'total_asignaciones' => $item['total_asignaciones'],
                    'vehiculos_usados' => $item['vehiculos_asignados']->count(),
                    'vehiculos_detalle' => $item['vehiculos_asignados']->map(function ($vehiculo) {
                        return [
                            'id' => $vehiculo->id,
                            'marca' => $vehiculo->marca,
                            'modelo' => $vehiculo->modelo,
                            'placas' => $vehiculo->placas,
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     * API: Obtener estadísticas de operador en obra específica
     */
    public function apiEstadisticasOperadorEnObra(Personal $operador, $obraId)
    {
        $estadisticas = HistorialOperadorVehiculo::estadisticasOperadorEnObra($operador->id, $obraId);

        return response()->json([
            'success' => true,
            'estadisticas' => $estadisticas
        ]);
    }

    /**
     * Filtrar operadores por obra específica
     */
    public function filtrarPorObra(Request $request)
    {
        $obraId = $request->input('obra_id');
        
        if (!$obraId) {
            return redirect()->route('operadores.obras-por-operador');
        }

        // Obtener operadores que han trabajado en esta obra específica
        $operadoresEnObra = Personal::whereHas('historialOperadorVehiculo', function ($query) use ($obraId) {
            $query->where('obra_id', $obraId);
        })
        ->withCount(['historialOperadorVehiculo as asignaciones_en_obra' => function ($query) use ($obraId) {
            $query->where('obra_id', $obraId);
        }])
        ->with(['historialOperadorVehiculo' => function ($query) use ($obraId) {
            $query->where('obra_id', $obraId)
                ->with(['vehiculo', 'obra'])
                ->orderBy('fecha_asignacion', 'desc');
        }])
        ->orderBy('nombre_completo')
        ->get();

        // Obtener información de la obra
        $obra = \App\Models\Obra::find($obraId);

        return view('operadores.operadores-por-obra', compact('operadoresEnObra', 'obra'));
    }

    /**
     * Obtener la obra actual de un operador
     */
    private function obtenerObraActual($operadorId)
    {
        // Buscar en asignaciones activas donde el operador esté asignado
        $asignacionActiva = AsignacionObra::where('operador_id', $operadorId)
            ->where('estado', 'activa')
            ->with('obra')
            ->first();

        return $asignacionActiva?->obra;
    }
}
