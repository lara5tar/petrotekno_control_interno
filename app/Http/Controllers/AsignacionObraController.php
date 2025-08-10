<?php

namespace App\Http\Controllers;

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

            $query = Obra::with([
                'vehiculo:id,marca,modelo,placas,kilometraje_actual',
                'operador:id,nombre_completo',
                'encargado:id,personal_id',
                'encargado.personal:id,nombre_completo',
            ])
                ->whereNotNull('vehiculo_id')  // Solo obras que tienen asignación
                ->whereNotNull('operador_id'); // Solo obras que tienen operador asignado

            // Aplicar filtros de búsqueda
            if ($request->filled('buscar')) {
                $searchTerm = $request->buscar;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nombre_obra', 'like', "%{$searchTerm}%")
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
                    $query->whereNull('fecha_liberacion');
                } elseif ($request->estado === 'liberada') {
                    $query->whereNotNull('fecha_liberacion');
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

            // Estadísticas usando Obra en lugar de Asignacion
            $estadisticas = [
                'total' => Obra::whereNotNull('vehiculo_id')->whereNotNull('operador_id')->count(),
                'activas' => Obra::whereNotNull('vehiculo_id')->whereNotNull('operador_id')->whereNull('fecha_liberacion')->count(),
                'liberadas' => Obra::whereNotNull('vehiculo_id')->whereNotNull('operador_id')->whereNotNull('fecha_liberacion')->count(),
                'este_mes' => Obra::whereNotNull('vehiculo_id')->whereNotNull('operador_id')
                    ->whereMonth('fecha_asignacion', now()->month)
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

            $vehiculos = Vehiculo::disponibles()->orderBy('marca')->orderBy('modelo')->get([
                'id',
                'marca',
                'modelo',
                'placas',
                'kilometraje_actual',
                'estatus_id',
            ]);
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

            // Verificaciones adicionales - ahora usando la tabla obras unificada
            $obraConAsignacion = Obra::where('id', $request->obra_id)
                ->whereNotNull('vehiculo_id')
                ->whereNotNull('operador_id')
                ->whereNull('fecha_liberacion')
                ->first();

            if ($obraConAsignacion) {
                $message = 'La obra seleccionada ya tiene una asignación activa.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message)->withInput();
            }

            $vehiculoAsignado = Obra::where('vehiculo_id', $request->vehiculo_id)
                ->whereNotNull('operador_id')
                ->whereNull('fecha_liberacion')
                ->first();

            if ($vehiculoAsignado) {
                $message = 'El vehículo seleccionado ya tiene una asignación activa.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message)->withInput();
            }

            $operadorAsignado = Obra::where('operador_id', $request->operador_id)
                ->whereNotNull('vehiculo_id')
                ->whereNull('fecha_liberacion')
                ->first();

            if ($operadorAsignado) {
                $message = 'El operador seleccionado ya tiene una asignación activa.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message)->withInput();
            }

            // Actualizar la obra con los datos de asignación
            $obra = Obra::findOrFail($request->obra_id);
            $obra->update([
                'vehiculo_id' => $request->vehiculo_id,
                'operador_id' => $request->operador_id,
                'encargado_id' => Auth::id(),
                'fecha_asignacion' => now(), // Fecha automática del sistema
                'kilometraje_inicial' => $request->kilometraje_inicial,
                'combustible_inicial' => $request->combustible_inicial,
                'observaciones' => $request->observaciones,
            ]);

            // Actualizar el estatus del vehículo a "Asignado"
            $vehiculo = Vehiculo::find($request->vehiculo_id);
            if ($vehiculo) {
                $vehiculo->update([
                    'estatus_id' => $this->getEstatusAsignado(),
                    'kilometraje_actual' => $request->kilometraje_inicial,
                ]);
            }

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_asignacion',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Asignación creada: {$obra->vehiculo->marca} {$obra->vehiculo->modelo} ({$obra->vehiculo->placas}) asignado a {$obra->nombre_obra} - Operador: {$obra->operador->nombre_completo}",
            ]);

            DB::commit();

            $message = 'Asignación creada exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'data' => $obra->fresh(['vehiculo', 'operador', 'encargado']),
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
                'encargado.personal',
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

            $obra = Obra::whereNotNull('vehiculo_id')->whereNotNull('operador_id')->findOrFail($id);

            if ($obra->fecha_liberacion) {
                $message = 'Esta obra ya ha sido liberada.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message);
            }

            if (is_null($obra->fecha_liberacion)) {
                $message = 'Esta obra está activa y puede ser liberada.';
            } else {
                $message = 'Esta obra no está activa.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message);
            }

            // Validación
            $validator = Validator::make($request->all(), [
                'kilometraje_final' => 'required|integer|gte:' . ($obra->kilometraje_inicial ?? 0),
                'combustible_final' => 'nullable|numeric|min:0|max:1000',
                'combustible_suministrado' => 'nullable|numeric|min:0|max:1000',
                'costo_combustible' => 'nullable|numeric|min:0',
                'observaciones_liberacion' => 'nullable|string|max:1000',
            ], [
                'kilometraje_final.required' => 'El kilometraje final es obligatorio.',
                'kilometraje_final.gte' => 'El kilometraje final debe ser mayor o igual al inicial (' . ($obra->kilometraje_inicial ?? 0) . ' km).',
                'combustible_final.max' => 'El combustible final no puede exceder 1000 litros.',
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

            // Liberar la obra/asignación
            $obra->update([
                'fecha_liberacion' => now(),
                'kilometraje_final' => $request->kilometraje_final,
                'combustible_final' => $request->combustible_final,
                'combustible_suministrado' => $request->combustible_suministrado,
                'costo_combustible' => $request->costo_combustible,
                'observaciones' => $obra->observaciones
                    ? $obra->observaciones . "\n\nLiberación: " . $request->observaciones_liberacion
                    : 'Liberación: ' . $request->observaciones_liberacion,
            ]);

            // Actualizar el vehículo
            if ($obra->vehiculo) {
                $obra->vehiculo->update([
                    'estatus_id' => $this->getEstatusDisponible(),
                    'kilometraje_actual' => $request->kilometraje_final,
                ]);
            }

            // Log de la acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'liberar_asignacion',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Obra liberada: {$obra->vehiculo->marca} {$obra->vehiculo->modelo} ({$obra->vehiculo->placas}) - Kilometraje: {$request->kilometraje_final} km",
            ]);

            DB::commit();

            $message = 'Obra liberada exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'data' => $obra->fresh(['vehiculo', 'operador', 'encargado']),
                ]);
            }

            return redirect()->route('asignaciones-obra.show', $obra->id)->with('success', $message);
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
                'total_asignaciones' => Obra::whereNotNull('vehiculo_id')->whereNotNull('operador_id')->count(),
                'asignaciones_activas' => Obra::whereNotNull('vehiculo_id')->whereNotNull('operador_id')->whereNull('fecha_liberacion')->count(),
                'asignaciones_liberadas' => Obra::whereNotNull('vehiculo_id')->whereNotNull('operador_id')->whereNotNull('fecha_liberacion')->count(),

                // Estadísticas por período
                'este_mes' => Obra::whereNotNull('vehiculo_id')->whereNotNull('operador_id')
                    ->whereMonth('fecha_asignacion', now()->month)
                    ->whereYear('fecha_asignacion', now()->year)->count(),
                'mes_anterior' => Obra::whereNotNull('vehiculo_id')->whereNotNull('operador_id')
                    ->whereMonth('fecha_asignacion', now()->subMonth()->month)
                    ->whereYear('fecha_asignacion', now()->subMonth()->year)->count(),

                // Vehículos más utilizados
                'vehiculos_mas_utilizados' => Obra::with('vehiculo')
                    ->whereNotNull('vehiculo_id')->whereNotNull('operador_id')
                    ->selectRaw('vehiculo_id, COUNT(*) as total_asignaciones')
                    ->groupBy('vehiculo_id')
                    ->orderByDesc('total_asignaciones')
                    ->limit(5)
                    ->get(),

                // Operadores más activos
                'operadores_mas_activos' => Obra::with('operador')
                    ->whereNotNull('vehiculo_id')->whereNotNull('operador_id')
                    ->selectRaw('operador_id, COUNT(*) as total_asignaciones')
                    ->groupBy('operador_id')
                    ->orderByDesc('total_asignaciones')
                    ->limit(5)
                    ->get(),

                // Duración promedio de asignaciones
                'duracion_promedio' => Obra::whereNotNull('fecha_liberacion')
                    ->whereNotNull('vehiculo_id')->whereNotNull('operador_id')
                    ->selectRaw('AVG(DATEDIFF(fecha_liberacion, fecha_asignacion)) as promedio')
                    ->value('promedio'),

                // Kilometraje total recorrido
                'kilometraje_total' => Obra::whereNotNull('fecha_liberacion')
                    ->whereNotNull('vehiculo_id')->whereNotNull('operador_id')
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
     * Obtener ID del estatus "Asignado"
     */
    private function getEstatusAsignado(): int
    {
        // Buscar en la tabla catalogo_estatus el ID correspondiente a "Asignado"
        return DB::table('catalogo_estatus')
            ->where('nombre_estatus', 'Asignado')
            ->value('id') ?? 3; // Fallback si no existe
    }

    /**
     * Obtener ID del estatus "Disponible"
     */
    private function getEstatusDisponible(): int
    {
        // Buscar en la tabla catalogo_estatus el ID correspondiente a "Disponible"
        return DB::table('catalogo_estatus')
            ->where('nombre_estatus', 'Disponible')
            ->value('id') ?? 5; // Fallback si no existe
    }
}
