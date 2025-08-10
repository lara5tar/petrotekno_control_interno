<?php

namespace App\Http\Controllers;

use App\Models\AsignacionObra;
use App\Models\Obra;
use App\Models\Vehiculo;
use App\Models\Personal;
use App\Models\LogAccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class AsignacionObraMultipleController extends Controller
{
    /**
     * Listar todas las asignaciones con filtros
     */
    public function index(Request $request)
    {
        try {
            $query = AsignacionObra::with([
                'obra:id,nombre_obra,estatus',
                'vehiculo:id,marca,modelo,placas,kilometraje_actual',
                'operador:id,nombre_completo',
                'encargado:id,name'
            ]);

            // Filtros
            if ($request->filled('obra_id')) {
                $query->where('obra_id', $request->obra_id);
            }

            if ($request->filled('vehiculo_id')) {
                $query->where('vehiculo_id', $request->vehiculo_id);
            }

            if ($request->filled('operador_id')) {
                $query->where('operador_id', $request->operador_id);
            }

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('fecha_desde')) {
                $query->where('fecha_asignacion', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->where('fecha_asignacion', '<=', $request->fecha_hasta);
            }

            // Búsqueda general
            if ($request->filled('buscar')) {
                $searchTerm = $request->buscar;
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('obra', function ($oq) use ($searchTerm) {
                        $oq->where('nombre_obra', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('vehiculo', function ($vq) use ($searchTerm) {
                        $vq->where('marca', 'like', "%{$searchTerm}%")
                          ->orWhere('modelo', 'like', "%{$searchTerm}%")
                          ->orWhere('placas', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('operador', function ($pq) use ($searchTerm) {
                        $pq->where('nombre_completo', 'like', "%{$searchTerm}%");
                    });
                });
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'fecha_asignacion');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginación
            $perPage = min(max((int) $request->get('per_page', 15), 1), 100);
            $asignaciones = $query->paginate($perPage);

            // Estadísticas
            $estadisticas = [
                'total' => AsignacionObra::count(),
                'activas' => AsignacionObra::activas()->count(),
                'liberadas' => AsignacionObra::liberadas()->count(),
                'este_mes' => AsignacionObra::whereMonth('fecha_asignacion', now()->month)
                    ->whereYear('fecha_asignacion', now()->year)->count(),
            ];

            // Datos para filtros
            $obras = Obra::disponiblesParaAsignacion()->orderBy('nombre_obra')->get(['id', 'nombre_obra']);
            $vehiculos = Vehiculo::activos()->orderBy('marca')->orderBy('modelo')->get(['id', 'marca', 'modelo', 'placas']);
            $operadores = Personal::activos()->operadores()->orderBy('nombre_completo')->get(['id', 'nombre_completo']);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $asignaciones,
                    'estadisticas' => $estadisticas,
                    'filtros' => [
                        'obras' => $obras,
                        'vehiculos' => $vehiculos,
                        'operadores' => $operadores,
                    ],
                ]);
            }

            return view('asignaciones-obra-multiple.index', compact(
                'asignaciones', 'estadisticas', 'obras', 'vehiculos', 'operadores'
            ));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al cargar asignaciones.'], 500);
            }
            return redirect()->back()->with('error', 'Error al cargar asignaciones.');
        }
    }

    /**
     * Mostrar formulario para crear nueva asignación
     */
    public function create(Request $request)
    {
        try {
            // Obtener obra preseleccionada si existe
            $obraPreseleccionada = null;
            if ($request->filled('obra_id')) {
                $obraPreseleccionada = Obra::findOrFail($request->obra_id);
                
                // Verificar que la obra puede recibir nuevas asignaciones
                if (!$obraPreseleccionada->puede_recibir_nuevas_asignaciones) {
                    $message = 'La obra seleccionada no puede recibir nuevas asignaciones.';
                    if ($request->expectsJson()) {
                        return response()->json(['error' => $message], 400);
                    }
                    return redirect()->back()->with('error', $message);
                }
            }

            // Obtener datos para el formulario
            $obras = Obra::disponiblesParaAsignacion()
                ->where(function ($q) {
                    $q->where('permite_multiples_asignaciones', true)
                      ->orWhereDoesntHave('asignacionesActivas');
                })
                ->orderBy('nombre_obra')->get(['id', 'nombre_obra', 'estatus', 'permite_multiples_asignaciones', 'max_vehiculos']);

            $vehiculos = Vehiculo::whereDoesntHave('asignacionesObraActivas')
                ->whereHas('estatus', function ($q) {
                    $q->where('nombre_estatus', 'Disponible')
                      ->orWhere('nombre_estatus', 'Activo');
                })
                ->orderBy('marca')->orderBy('modelo')
                ->get(['id', 'marca', 'modelo', 'placas', 'kilometraje_actual']);

            $operadores = Personal::whereDoesntHave('asignacionesObraActivas')
                ->activos()->operadores()
                ->orderBy('nombre_completo')
                ->get(['id', 'nombre_completo', 'categoria_id']);

            // Mensajes informativos
            $mensajes = [];
            if ($obras->isEmpty()) {
                $mensajes['obras'] = 'No hay obras disponibles para nuevas asignaciones.';
            }
            if ($vehiculos->isEmpty()) {
                $mensajes['vehiculos'] = 'No hay vehículos disponibles para asignar.';
            }
            if ($operadores->isEmpty()) {
                $mensajes['operadores'] = 'No hay operadores disponibles para asignar.';
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => [
                        'obras' => $obras,
                        'vehiculos' => $vehiculos,
                        'operadores' => $operadores,
                        'obra_preseleccionada' => $obraPreseleccionada,
                        'mensajes' => $mensajes,
                    ],
                ]);
            }

            return view('asignaciones-obra-multiple.create', compact(
                'obras', 'vehiculos', 'operadores', 'obraPreseleccionada', 'mensajes'
            ));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al cargar formulario.'], 500);
            }
            return redirect()->back()->with('error', 'Error al cargar formulario.');
        }
    }

    /**
     * Crear nueva asignación
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'obra_id' => 'required|exists:obras,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'operador_id' => 'required|exists:personal,id',
            'kilometraje_inicial' => 'required|integer|min:0',
            'combustible_inicial' => 'nullable|numeric|min:0|max:1000',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'obra_id.required' => 'Debe seleccionar una obra.',
            'vehiculo_id.required' => 'Debe seleccionar un vehículo.',
            'operador_id.required' => 'Debe seleccionar un operador.',
            'kilometraje_inicial.required' => 'El kilometraje inicial es obligatorio.',
            'kilometraje_inicial.min' => 'El kilometraje inicial no puede ser negativo.',
            'combustible_inicial.max' => 'El combustible inicial no puede exceder 1000 litros.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Datos de validación incorrectos.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Verificar que la obra puede recibir la asignación
            $obra = Obra::findOrFail($request->obra_id);
            if (!$obra->puede_recibir_nuevas_asignaciones) {
                throw new Exception('La obra no puede recibir nuevas asignaciones.');
            }

            // Verificar disponibilidad del vehículo - REGLA ESTRICTA: Un vehículo = Una obra
            AsignacionObra::validarAsignacionUnicaVehiculo($request->vehiculo_id);

            // NOTA: Los operadores SÍ pueden tener múltiples asignaciones (pueden manejar varios vehículos)
            // No validamos operador duplicado

            // Crear la asignación usando el método del modelo Obra
            $asignacion = $obra->asignarVehiculoYOperador(
                $request->vehiculo_id,
                $request->operador_id,
                Auth::id(),
                [
                    'kilometraje_inicial' => $request->kilometraje_inicial,
                    'combustible_inicial' => $request->combustible_inicial,
                    'observaciones' => $request->observaciones,
                ]
            );

            // Actualizar estatus del vehículo
            $vehiculo = Vehiculo::find($request->vehiculo_id);
            if ($vehiculo) {
                // Buscar estatus "Asignado"
                $estatusAsignado = \App\Models\CatalogoEstatus::where('nombre_estatus', 'Asignado')->first();
                if ($estatusAsignado) {
                    $vehiculo->update([
                        'estatus_id' => $estatusAsignado->id,
                        'kilometraje_actual' => $request->kilometraje_inicial,
                    ]);
                }
            }

            DB::commit();

            $message = 'Asignación creada exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'data' => $asignacion->load(['obra', 'vehiculo', 'operador', 'encargado']),
                ], 201);
            }

            return redirect()->route('asignaciones-obra-multiple.index')
                ->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Mostrar detalles de una asignación
     */
    public function show(Request $request, string $id)
    {
        try {
            $asignacion = AsignacionObra::with([
                'obra',
                'vehiculo.estatus',
                'operador.categoria',
                'encargado'
            ])->findOrFail($id);

            // Estadísticas de la asignación
            $estadisticas = [
                'duracion_dias' => $asignacion->duracion_en_dias,
                'kilometraje_recorrido' => $asignacion->kilometraje_recorrido,
                'combustible_consumido' => $asignacion->combustible_consumido,
                'eficiencia_combustible' => $asignacion->eficiencia_combustible,
                'costo_total' => $asignacion->costo_combustible,
                'total_recargas' => count($asignacion->historial_combustible ?? []),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $asignacion,
                    'estadisticas' => $estadisticas,
                ]);
            }

            return view('asignaciones-obra-multiple.show', compact('asignacion', 'estadisticas'));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Asignación no encontrada.'], 404);
            }
            return redirect()->back()->with('error', 'Asignación no encontrada.');
        }
    }

    /**
     * Liberar una asignación
     */
    public function liberar(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'kilometraje_final' => 'required|integer|min:0',
            'combustible_final' => 'nullable|numeric|min:0|max:1000',
            'observaciones_liberacion' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Datos de validación incorrectos.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $asignacion = AsignacionObra::findOrFail($id);

            if (!$asignacion->esta_activa) {
                throw new Exception('La asignación ya está liberada.');
            }

            // Validar kilometraje final
            if ($request->kilometraje_final < $asignacion->kilometraje_inicial) {
                throw new Exception('El kilometraje final debe ser mayor o igual al inicial.');
            }

            DB::beginTransaction();

            // Liberar la asignación
            $asignacion->liberar(
                $request->kilometraje_final,
                $request->combustible_final,
                $request->observaciones_liberacion
            );

            // Actualizar estatus del vehículo a "Disponible"
            $vehiculo = $asignacion->vehiculo;
            if ($vehiculo) {
                $estatusDisponible = \App\Models\CatalogoEstatus::where('nombre_estatus', 'Disponible')->first();
                if ($estatusDisponible) {
                    $vehiculo->update([
                        'estatus_id' => $estatusDisponible->id,
                        'kilometraje_actual' => $request->kilometraje_final,
                    ]);
                }
            }

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'liberar_asignacion_obra',
                'tabla_afectada' => 'asignaciones_obra',
                'registro_id' => $asignacion->id,
                'detalles' => "Asignación liberada: {$asignacion->vehiculo->nombre_completo} -> {$asignacion->obra->nombre_obra} | Km final: {$request->kilometraje_final}",
            ]);

            DB::commit();

            $message = 'Asignación liberada exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'data' => $asignacion->fresh(['obra', 'vehiculo', 'operador']),
                ]);
            }

            return redirect()->route('asignaciones-obra-multiple.index')
                ->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Transferir asignación a otro operador
     */
    public function transferir(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nuevo_operador_id' => 'required|exists:personal,id',
            'kilometraje_transferencia' => 'required|integer|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Datos de validación incorrectos.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $asignacion = AsignacionObra::findOrFail($id);

            if (!$asignacion->esta_activa) {
                throw new Exception('Solo se pueden transferir asignaciones activas.');
            }

            if ($request->nuevo_operador_id == $asignacion->operador_id) {
                throw new Exception('No se puede transferir al mismo operador.');
            }

            // Verificar que el nuevo operador esté disponible
            if (AsignacionObra::operadorTieneAsignacionActiva($request->nuevo_operador_id)) {
                throw new Exception('El nuevo operador ya tiene una asignación activa.');
            }

            DB::beginTransaction();

            $operadorAnterior = $asignacion->operador;
            $nuevoOperador = Personal::findOrFail($request->nuevo_operador_id);

            // Transferir la asignación
            $asignacion->transferir(
                $request->nuevo_operador_id,
                $request->kilometraje_transferencia,
                $request->observaciones
            );

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'transferir_asignacion_obra',
                'tabla_afectada' => 'asignaciones_obra',
                'registro_id' => $asignacion->id,
                'detalles' => "Asignación transferida: De {$operadorAnterior->nombre_completo} a {$nuevoOperador->nombre_completo} | Km: {$request->kilometraje_transferencia}",
            ]);

            DB::commit();

            $message = 'Asignación transferida exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'data' => $asignacion->fresh(['obra', 'vehiculo', 'operador']),
                ]);
            }

            return redirect()->route('asignaciones-obra-multiple.index')
                ->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Obtener asignaciones por obra
     */
    public function porObra(Request $request, string $obraId)
    {
        try {
            $obra = Obra::with(['asignacionesActivas.vehiculo', 'asignacionesActivas.operador'])
                ->findOrFail($obraId);

            $resumen = $obra->getResumenAsignaciones();

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => [
                        'obra' => $obra,
                        'resumen' => $resumen,
                    ],
                ]);
            }

            return view('asignaciones-obra-multiple.por-obra', compact('obra', 'resumen'));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Obra no encontrada.'], 404);
            }
            return redirect()->back()->with('error', 'Obra no encontrada.');
        }
    }

    /**
     * Obtener estadísticas generales
     */
    public function estadisticas(Request $request)
    {
        try {
            $estadisticas = [
                'totales' => [
                    'asignaciones' => AsignacionObra::count(),
                    'activas' => AsignacionObra::activas()->count(),
                    'liberadas' => AsignacionObra::liberadas()->count(),
                ],
                'por_mes' => AsignacionObra::selectRaw('MONTH(fecha_asignacion) as mes, COUNT(*) as total')
                    ->whereYear('fecha_asignacion', now()->year)
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get(),
                'obras_con_mas_asignaciones' => Obra::withCount('asignacionesObra')
                    ->having('asignaciones_obra_count', '>', 0)
                    ->orderByDesc('asignaciones_obra_count')
                    ->limit(10)
                    ->get(['id', 'nombre_obra']),
                'vehiculos_mas_utilizados' => Vehiculo::withCount('asignacionesObra')
                    ->having('asignaciones_obra_count', '>', 0)
                    ->orderByDesc('asignaciones_obra_count')
                    ->limit(10)
                    ->get(['id', 'marca', 'modelo', 'placas']),
            ];

            if ($request->expectsJson()) {
                return response()->json(['data' => $estadisticas]);
            }

            return view('asignaciones-obra-multiple.estadisticas', compact('estadisticas'));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al obtener estadísticas.'], 500);
            }
            return redirect()->back()->with('error', 'Error al obtener estadísticas.');
        }
    }
}
