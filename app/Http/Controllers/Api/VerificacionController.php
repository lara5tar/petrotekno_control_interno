<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obra;
use App\Models\AsignacionObra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificacionController extends Controller
{
    /**
     * Verificar que una obra se creó correctamente y sus relaciones
     */
    public function verificarObra(Request $request)
    {
        try {
            $nombreObra = $request->input('nombre_obra');
            
            if (!$nombreObra) {
                return response()->json(['error' => 'Nombre de obra requerido'], 400);
            }

            // Buscar la obra por nombre
            $obra = Obra::where('nombre_obra', $nombreObra)
                       ->with(['vehiculo', 'operador', 'encargado'])
                       ->first();

            if (!$obra) {
                return response()->json(['error' => 'Obra no encontrada'], 404);
            }

            // Buscar asignaciones de vehículos para esta obra
            $asignaciones = AsignacionObra::where('obra_id', $obra->id)
                                         ->with(['vehiculo', 'operador'])
                                         ->get();

            // Preparar respuesta
            $respuesta = [
                'obra' => [
                    'id' => $obra->id,
                    'nombre_obra' => $obra->nombre_obra,
                    'estatus' => $obra->estatus,
                    'encargado_id' => $obra->encargado_id,
                    'encargado_nombre' => $obra->encargado ? $obra->encargado->nombre_completo : null,
                    'vehiculo_directo_id' => $obra->vehiculo_id,
                    'operador_directo_id' => $obra->operador_id,
                ],
                'asignaciones' => $asignaciones->map(function($asignacion) {
                    return [
                        'id' => $asignacion->id,
                        'vehiculo_id' => $asignacion->vehiculo_id,
                        'vehiculo_info' => $asignacion->vehiculo ? [
                            'marca' => $asignacion->vehiculo->marca,
                            'modelo' => $asignacion->vehiculo->modelo,
                            'placas' => $asignacion->vehiculo->placas,
                        ] : null,
                        'operador_id' => $asignacion->operador_id,
                        'operador_nombre' => $asignacion->operador ? $asignacion->operador->nombre_completo : null,
                        'estado' => $asignacion->estado,
                        'fecha_asignacion' => $asignacion->fecha_asignacion,
                        'kilometraje_inicial' => $asignacion->kilometraje_inicial,
                    ];
                }),
                'verificaciones' => [
                    'obra_creada' => true,
                    'tiene_encargado' => !is_null($obra->encargado_id),
                    'tiene_asignaciones_vehiculo' => $asignaciones->count() > 0,
                    'asignaciones_activas' => $asignaciones->where('estado', 'activa')->count(),
                    'total_asignaciones' => $asignaciones->count(),
                ]
            ];

            Log::info('Verificación de obra completada', [
                'obra_id' => $obra->id,
                'nombre_obra' => $nombreObra,
                'total_asignaciones' => $asignaciones->count()
            ]);

            return response()->json($respuesta);

        } catch (\Exception $e) {
            Log::error('Error al verificar obra', [
                'nombre_obra' => $request->input('nombre_obra'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }
}