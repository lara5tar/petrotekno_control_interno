<?php

namespace App\Http\Controllers;

use App\Enums\EstadoVehiculo;
use App\Models\AsignacionObra;
use App\Models\LogAccion;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AsignacionObraController extends Controller
{
    /**
     * Verificar permisos del usuario autenticado
     */
    private function hasPermission(string $permission): bool
    {
        $user = Auth::user();

        return $user ? $user->hasPermission($permission) : false;
    }

    /**
     * Mostrar todas las obras/asignaciones con filtros
     */
    public function index(Request $request)
    {
        try {
            if (! $this->hasPermission('ver_asignaciones')) {
                $message = 'No tienes permisos para ver las asignaciones.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            $query = AsignacionObra::with([
                'obra',
                'vehiculo:id,marca,modelo,placas,kilometraje_actual',
                'operador:id,nombre_completo',
                'obra.encargado:id,nombre_completo',
            ])
                ->where('estado', 'activa')
                ->whereNull('fecha_liberacion');

            // Aplicar filtros de búsqueda
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

            if ($request->filled('estado')) {
                if ($request->estado === 'activa') {
                    $query->where('estado', 'activa')->whereNull('fecha_liberacion');
                } elseif ($request->estado === 'liberada') {
                    $query->where('estado', 'liberada')->whereNotNull('fecha_liberacion');
                }
            }

            if ($request->filled('vehiculo_id')) {
                $query->where('vehiculo_id', $request->vehiculo_id);
            }

            if ($request->filled('personal_id')) {
                $query->where('operador_id', $request->personal_id);
            }

            if ($request->filled('fecha_desde')) {
                $query->where('fecha_asignacion', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->where('fecha_asignacion', '<=', $request->fecha_hasta);
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'fecha_asignacion');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $perPage = max(1, min((int) $perPage, 100));
            $asignaciones = $query->paginate($perPage);

            // Estadísticas usando AsignacionObra en lugar de Obra
            $estadisticas = [
                'total' => AsignacionObra::count(),
                'activas' => AsignacionObra::where('estado', 'activa')->whereNull('fecha_liberacion')->count(),
                'liberadas' => AsignacionObra::where('estado', 'liberada')->whereNotNull('fecha_liberacion')->count(),
                'este_mes' => AsignacionObra::whereMonth('fecha_asignacion', now()->month)
                    ->whereYear('fecha_asignacion', now()->year)
                    ->count(),
            ];

            // Datos para filtros
            $obras = Obra::activas()->orderBy('nombre_obra')->get(['id', 'nombre_obra']);
            $vehiculos = Vehiculo::activos()->orderBy('marca')->orderBy('modelo')->get(['id', 'marca', 'modelo', 'placas']);
            $operadores = Personal::activos()->operadores()->orderBy('nombre_completo')->get(['id', 'nombre_completo']);

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $asignaciones->items(),
                    'pagination' => [
                        'current_page' => $asignaciones->currentPage(),
                        'last_page' => $asignaciones->lastPage(),
                        'per_page' => $asignaciones->perPage(),
                        'total' => $asignaciones->total(),
                    ],
                    'estadisticas' => $estadisticas,
                    'filtros' => [
                        'obras' => $obras,
                        'vehiculos' => $vehiculos,
                        'operadores' => $operadores,
                    ],
                ]);
            }

            return view('asignaciones-obra.index', compact(
                'asignaciones',
                'estadisticas',
                'obras',
                'vehiculos',
                'operadores'
            ));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al cargar las asignaciones.'], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar las asignaciones.');
        }
    }

    /**
     * Mostrar formulario para crear nueva asignación
     */
    public function create(Request $request)
    {
        try {
            // Los permisos son verificados por el middleware

            // Obtener datos para el formulario
            $obras = Obra::activas()->orderBy('nombre_obra')->get(['id', 'nombre_obra', 'estatus']);

            // Cargar vehículos disponibles primero
            $vehiculosDisponibles = Vehiculo::disponibles()
                ->with('estatus')
                ->orderBy('marca')
                ->orderBy('modelo')
                ->get()
                ->map(function ($vehiculo) {
                    return [
                        'id' => $vehiculo->id,
                        'nombre_completo' => $vehiculo->nombre_completo,
                        'disponible' => true,
                        'texto_estado' => ''
                    ];
                });

            // Cargar vehículos asignados para mostrarlos al final como no seleccionables
            $vehiculosAsignados = Vehiculo::whereHas('asignacionesObraActivas')
                ->with(['estatus', 'asignacionesObraActivas.obra'])
                ->orderBy('marca')
                ->orderBy('modelo')
                ->get()
                ->map(function ($vehiculo) {
                    $obraAsignada = $vehiculo->asignacionesObraActivas->first();
                    $textoEstado = $obraAsignada ? " (Asignado a: {$obraAsignada->obra->nombre_obra})" : " (No disponible)";
                    
                    return [
                        'id' => $vehiculo->id,
                        'nombre_completo' => $vehiculo->nombre_completo . $textoEstado,
                        'disponible' => false,
                        'texto_estado' => $textoEstado
                    ];
                });

            // Combinar ambas listas: disponibles primero, asignados después
            $vehiculos = $vehiculosDisponibles->concat($vehiculosAsignados);

            $operadores = Personal::activos()->operadores()->orderBy('nombre_completo')->get([
                'id',
                'nombre_completo',
                'categoria_id',
            ]);

            // Verificar si hay obras disponibles
            $mensaje_obras = null;
            if ($obras->isEmpty()) {
                $mensaje_obras = 'No hay obras disponibles para asignar. Todas las obras activas ya tienen asignaciones.';
            }

            // Verificar si hay vehículos disponibles
            $mensaje_vehiculos = null;
            if ($vehiculos->isEmpty()) {
                $mensaje_vehiculos = 'No hay vehículos disponibles para asignar. Libera una asignación existente o agrega nuevos vehículos.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $mensaje_vehiculos], 400);
                }
            }

            // Verificar si hay operadores disponibles
            $mensaje_operadores = null;
            if ($operadores->isEmpty()) {
                $mensaje_operadores = 'No hay operadores disponibles para asignar.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $mensaje_operadores], 400);
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Formulario de creación de asignación',
                    'data' => [
                        'obras' => $obras,
                        'vehiculos' => $vehiculos,
                        'operadores' => $operadores,
                        'obra_preseleccionada' => $request->obra_id,
                        'mensaje_obras' => $mensaje_obras,
                        'mensaje_vehiculos' => $mensaje_vehiculos,
                        'mensaje_operadores' => $mensaje_operadores,
                    ],
                ]);
            }

            return view('asignaciones-obra.create', compact('obras', 'vehiculos', 'operadores'))
                ->with('obra_preseleccionada', $request->obra_id)
                ->with('mensaje_obras', $mensaje_obras)
                ->with('mensaje_vehiculos', $mensaje_vehiculos)
                ->with('mensaje_operadores', $mensaje_operadores);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al cargar el formulario.'], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar el formulario.');
        }
    }

    /**
     * Almacenar nueva asignación
     */
    public function store(Request $request)
    {
        try {
            if (! $this->hasPermission('crear_asignaciones')) {
                $message = 'No tienes permisos para crear asignaciones.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            // Validación
            $validator = Validator::make($request->all(), [
                'vehiculo_id' => 'required|integer|exists:vehiculos,id',
                'obra_id' => 'required|integer|exists:obras,id',
                'operador_id' => 'required|integer|exists:personal,id',
                'kilometraje_inicial' => 'required|integer|min:0',
                'combustible_inicial' => 'nullable|numeric|min:0|max:1000',
                'observaciones' => 'nullable|string|max:1000',
                'encargado_id' => 'nullable|integer|exists:personal,id', // Agregamos validación para encargado_id
            ], [
                'vehiculo_id.required' => 'Debe seleccionar un vehículo.',
                'vehiculo_id.exists' => 'El vehículo seleccionado no existe.',
                'obra_id.required' => 'Debe seleccionar una obra.',
                'obra_id.exists' => 'La obra seleccionada no existe.',
                'operador_id.required' => 'Debe seleccionar un operador.',
                'operador_id.exists' => 'El operador seleccionado no existe.',
                'kilometraje_inicial.required' => 'El kilometraje inicial es obligatorio.',
                'kilometraje_inicial.min' => 'El kilometraje inicial no puede ser negativo.',
                'combustible_inicial.max' => 'El combustible inicial no puede exceder 1000 litros.',
                'observaciones.max' => 'Las observaciones no pueden exceder 1000 caracteres.',
                'encargado_id.exists' => 'El encargado seleccionado no existe.',
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Datos de validación incorrectos.',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            // Verificaciones adicionales - usando la tabla asignaciones_obra
            $obraConAsignacion = AsignacionObra::where('obra_id', $request->obra_id)
                ->where('estado', 'activa')
                ->whereNull('fecha_liberacion')
                ->first();

            if ($obraConAsignacion) {
                $message = 'La obra seleccionada ya tiene una asignación activa.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message)->withInput();
            }

            $vehiculoAsignado = AsignacionObra::where('vehiculo_id', $request->vehiculo_id)
                ->where('estado', 'activa')
                ->whereNull('fecha_liberacion')
                ->first();

            if ($vehiculoAsignado) {
                $message = 'El vehículo seleccionado ya tiene una asignación activa.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message)->withInput();
            }

            $operadorAsignado = AsignacionObra::where('operador_id', $request->operador_id)
                ->where('estado', 'activa')
                ->whereNull('fecha_liberacion')
                ->first();

            if ($operadorAsignado) {
                $message = 'El operador seleccionado ya tiene una asignación activa.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message)->withInput();
            }

            // Crear la asignación en la tabla asignaciones_obra
            $asignacion = AsignacionObra::create([
                'obra_id' => $request->obra_id,
                'vehiculo_id' => $request->vehiculo_id,
                'operador_id' => $request->operador_id,
                'fecha_asignacion' => now(),
                'kilometraje_inicial' => $request->kilometraje_inicial,
                'observaciones' => $request->observaciones,
                'estado' => 'activa',
            ]);

            // Actualizar el estatus del vehículo a "Asignado"
            $vehiculo = Vehiculo::find($request->vehiculo_id);
            if ($vehiculo) {
                $vehiculo->update([
                    'estatus' => EstadoVehiculo::ASIGNADO->value,
                    'kilometraje_actual' => $request->kilometraje_inicial,
                ]);
            }

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_asignacion',
                'tabla_afectada' => 'asignaciones_obra',
                'registro_id' => $asignacion->id,
                'detalles' => "Asignación creada: {$asignacion->vehiculo->marca} {$asignacion->vehiculo->modelo} ({$asignacion->vehiculo->placas}) asignado a {$asignacion->obra->nombre_obra} - Operador: {$asignacion->operador->nombre_completo}",
            ]);

            DB::commit();

            $message = 'Asignación creada exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'data' => $asignacion->fresh(['vehiculo', 'operador', 'obra']),
                ], 201);
            }

            return redirect()->route('asignaciones-obra.index')->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al crear la asignación.'], 500);
            }

            return redirect()->back()->with('error', 'Error al crear la asignación.')->withInput();
        }
    }

    /**
     * Mostrar detalles de una asignación
     */
    public function show(Request $request, int $id)
    {
        try {
            if (! $this->hasPermission('ver_asignaciones')) {
                $message = 'No tienes permisos para ver esta asignación.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            $obra = Obra::with([
                'vehiculo',
                'operador',
                'encargado', // CORREGIDO: Cambiar de 'encargado.personal' a solo 'encargado'
            ])
                ->whereNotNull('vehiculo_id')
                ->whereNotNull('operador_id')
                ->findOrFail($id);

            // Calcular estadísticas adicionales
            $estadisticas = [
                'duracion_dias' => $obra->fecha_asignacion && $obra->fecha_liberacion
                    ? Carbon::parse($obra->fecha_asignacion)->diffInDays(Carbon::parse($obra->fecha_liberacion))
                    : ($obra->fecha_asignacion ? Carbon::parse($obra->fecha_asignacion)->diffInDays(now()) : null),
                'kilometraje_recorrido' => $obra->kilometraje_recorrido,
                'combustible_consumido' => $obra->combustible_consumido,
                'esta_activa' => is_null($obra->fecha_liberacion),
                'esta_liberada' => ! is_null($obra->fecha_liberacion),
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $obra,
                    'estadisticas' => $estadisticas,
                ]);
            }

            return view('asignaciones-obra.show', compact('asignacion', 'estadisticas'));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Asignación no encontrada.'], 404);
            }

            return redirect()->back()->with('error', 'Asignación no encontrada.');
        }
    }

    /**
     * Liberar una asignación activa
     */
    public function liberar(Request $request, int $id)
    {
        try {
            if (! $this->hasPermission('editar_asignaciones')) {
                $message = 'No tienes permisos para liberar asignaciones.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            $asignacion = AsignacionObra::where('estado', 'activa')
                ->whereNull('fecha_liberacion')
                ->findOrFail($id);

            if ($asignacion->fecha_liberacion) {
                $message = 'Esta asignación ya ha sido liberada.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message);
            }

            if (is_null($asignacion->fecha_liberacion)) {
                $message = 'Esta asignación está activa y puede ser liberada.';
            } else {
                $message = 'Esta asignación no está activa.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message);
            }

            // Validación
            $validator = Validator::make($request->all(), [
                'kilometraje_final' => 'required|integer|gte:' . ($asignacion->kilometraje_inicial ?? 0),
                'observaciones_liberacion' => 'nullable|string|max:1000',
            ], [
                'kilometraje_final.required' => 'El kilometraje final es obligatorio.',
                'kilometraje_final.gte' => 'El kilometraje final debe ser mayor o igual al inicial (' . ($asignacion->kilometraje_inicial ?? 0) . ' km).',
                'observaciones_liberacion.max' => 'Las observaciones no pueden exceder 1000 caracteres.',
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Datos de validación incorrectos.',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            // Liberar la asignación
            $asignacion->update([
                'fecha_liberacion' => now(),
                'kilometraje_final' => $request->kilometraje_final,
                'estado' => 'liberada',
                'observaciones' => $asignacion->observaciones
                    ? $asignacion->observaciones . "\n\nLiberación: " . $request->observaciones_liberacion
                    : 'Liberación: ' . $request->observaciones_liberacion,
            ]);

            // Actualizar el vehículo
            if ($asignacion->vehiculo) {
                $asignacion->vehiculo->update([
                    'estatus' => EstadoVehiculo::DISPONIBLE->value,
                    'kilometraje_actual' => $request->kilometraje_final,
                ]);
            }

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'liberar_asignacion',
                'tabla_afectada' => 'asignaciones_obra',
                'registro_id' => $asignacion->id,
                'detalles' => "Asignación liberada: {$asignacion->vehiculo->marca} {$asignacion->vehiculo->modelo} ({$asignacion->vehiculo->placas}) - Kilometraje: {$request->kilometraje_final} km",
            ]);

            DB::commit();

            $message = 'Asignación liberada exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'data' => $asignacion->fresh(['vehiculo', 'operador', 'obra']),
                ]);
            }

            return redirect()->route('asignaciones-obra.index')->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al liberar la asignación.'], 500);
            }

            return redirect()->back()->with('error', 'Error al liberar la asignación.');
        }
    }

    /**
     * Obtener estadísticas generales de asignaciones
     */
    public function estadisticas(Request $request)
    {
        try {
            if (! $this->hasPermission('ver_asignaciones')) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'No tienes permisos para ver estadísticas.'], 403);
                }

                return redirect()->back()->with('error', 'No tienes permisos para ver estadísticas.');
            }

            $estadisticas = [
                // Contadores generales
                'total_asignaciones' => AsignacionObra::count(),
                'asignaciones_activas' => AsignacionObra::where('estado', 'activa')->whereNull('fecha_liberacion')->count(),
                'asignaciones_liberadas' => AsignacionObra::where('estado', 'liberada')->whereNotNull('fecha_liberacion')->count(),

                // Estadísticas por período
                'este_mes' => AsignacionObra::whereMonth('fecha_asignacion', now()->month)
                    ->whereYear('fecha_asignacion', now()->year)->count(),
                'mes_anterior' => AsignacionObra::whereMonth('fecha_asignacion', now()->subMonth()->month)
                    ->whereYear('fecha_asignacion', now()->subMonth()->year)->count(),

                // Vehículos más utilizados
                'vehiculos_mas_utilizados' => AsignacionObra::with('vehiculo')
                    ->selectRaw('vehiculo_id, COUNT(*) as total_asignaciones')
                    ->groupBy('vehiculo_id')
                    ->orderByDesc('total_asignaciones')
                    ->limit(5)
                    ->get(),

                // Operadores más activos
                'operadores_mas_activos' => AsignacionObra::with('operador')
                    ->selectRaw('operador_id, COUNT(*) as total_asignaciones')
                    ->groupBy('operador_id')
                    ->orderByDesc('total_asignaciones')
                    ->limit(5)
                    ->get(),

                // Duración promedio de asignaciones
                'duracion_promedio' => AsignacionObra::whereNotNull('fecha_liberacion')
                    ->selectRaw('AVG(DATEDIFF(fecha_liberacion, fecha_asignacion)) as promedio')
                    ->value('promedio'),

                // Kilometraje total recorrido
                'kilometraje_total' => AsignacionObra::whereNotNull('fecha_liberacion')
                    ->selectRaw('SUM(kilometraje_final - kilometraje_inicial) as total')
                    ->value('total'),
            ];

            if ($request->expectsJson()) {
                return response()->json(['data' => $estadisticas]);
            }

            return view('asignaciones-obra.estadisticas', compact('estadisticas'));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al obtener estadísticas.'], 500);
            }

            return redirect()->back()->with('error', 'Error al obtener estadísticas.');
        }
    }

    /**
     * Cambiar la obra asignada a un vehículo
     */
    public function cambiarObra(Request $request, int $vehiculoId)
    {
        try {
            if (!$this->hasPermission('crear_asignaciones')) {
                $message = 'No tienes permisos para cambiar asignaciones de obra.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }
                return redirect()->back()->with('error', $message);
            }

            // Validar datos de entrada
            $validator = Validator::make($request->all(), [
                'obra_id' => 'required|exists:obras,id',
                'operador_id' => 'required|exists:personal,id',
                'kilometraje_inicial' => 'nullable|integer|min:0',
                'observaciones' => 'nullable|string|max:1000',
            ], [
                'obra_id.required' => 'Debe seleccionar una obra.',
                'obra_id.exists' => 'La obra seleccionada no existe.',
                'operador_id.required' => 'Debe seleccionar un operador.',
                'operador_id.exists' => 'El operador seleccionado no existe.',
                'kilometraje_inicial.integer' => 'El kilometraje inicial debe ser un número entero.',
                'kilometraje_inicial.min' => 'El kilometraje inicial no puede ser negativo.',
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            // Obtener el vehículo
            $vehiculo = Vehiculo::findOrFail($vehiculoId);

            // Liberar asignación actual si existe
            $asignacionActual = $vehiculo->asignacionObraActual();
            if ($asignacionActual) {
                $asignacionActual->update([
                    'fecha_liberacion' => now(),
                    'kilometraje_final' => $vehiculo->kilometraje_actual ?? 0,
                    'observaciones' => ($asignacionActual->observaciones ?? '') . ' | Liberado para cambio de obra',
                    'estado' => 'liberada'
                ]);

                // Log de liberación
                LogAccion::create([
                    'usuario_id' => Auth::id(),
                    'accion' => 'liberar_asignacion_obra',
                    'tabla' => 'asignaciones_obra',
                    'registro_id' => $asignacionActual->id,
                    'datos_anteriores' => $asignacionActual->getOriginal(),
                    'datos_nuevos' => $asignacionActual->toArray(),
                    'fecha_hora' => now(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            // Crear nueva asignación
            $nuevaAsignacion = AsignacionObra::create([
                'obra_id' => $request->obra_id,
                'vehiculo_id' => $vehiculo->id,
                'operador_id' => $request->operador_id,
                'fecha_asignacion' => now(),
                'kilometraje_inicial' => $request->kilometraje_inicial ?? $vehiculo->kilometraje_actual ?? 0,
                'observaciones' => $request->observaciones ?? 'Cambio de obra desde vehículo',
                'estado' => 'activa',
            ]);

            // Actualizar operador del vehículo
            $vehiculo->update([
                'estatus' => EstadoVehiculo::ASIGNADO->value,
                'operador_id' => $request->operador_id,
            ]);

            // Log de nueva asignación
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_asignacion_obra',
                'tabla' => 'asignaciones_obra',
                'registro_id' => $nuevaAsignacion->id,
                'datos_anteriores' => null,
                'datos_nuevos' => $nuevaAsignacion->toArray(),
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Obra cambiada exitosamente',
                    'data' => [
                        'vehiculo' => $vehiculo->fresh(),
                        'nueva_asignacion' => $nuevaAsignacion->fresh(),
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Obra cambiada exitosamente');

        } catch (Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al cambiar la obra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al cambiar la obra: ' . $e->getMessage())
                ->withInput();
        }
    }
}
