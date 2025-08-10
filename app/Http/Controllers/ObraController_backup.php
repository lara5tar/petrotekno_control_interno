<?php

namespace App\Http\Controllers;

use App\Models\LogAccion;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\User;
use App\Models\Vehiculo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ObraController extends Controller
{
    /**
     * Display a listing of obras with hybrid response.
     */
    public function index(Request $request)
    {
        try {
            // Log inicial para debug
            \Log::info('=== INICIO ObraController@index ===', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'url' => $request->fullUrl()
            ]);

            if (! $this->hasPermission('ver_obras')) {
                \Log::warning('Usuario sin permisos para ver obras', [
                    'user_id' => Auth::id(),
                    'permissions' => Auth::user()?->permissions ?? 'No user'
                ]);
                $message = 'No tienes permisos para ver las obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            // Log antes de query
            \Log::info('Construyendo query de obras');
            
            // Aplicar filtros de búsqueda
            $query = Obra::query();

            if ($request->filled('buscar') || $request->filled('search')) {
                $searchTerm = $request->buscar ?? $request->search;
                \Log::info('Aplicando filtro de búsqueda', ['search_term' => $searchTerm]);
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nombre_obra', 'like', "%{$searchTerm}%")
                        ->orWhere('estatus', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->filled('estatus')) {
                \Log::info('Aplicando filtro de estatus', ['estatus' => $request->estatus]);
                $query->where('estatus', $request->estatus);
            }

            if ($request->filled('fecha_inicio')) {
                \Log::info('Aplicando filtro de fecha', ['fecha_inicio' => $request->fecha_inicio]);
                $query->whereDate('fecha_inicio', '>=', $request->fecha_inicio);
            }

            if ($request->filled('solo_activas') && $request->solo_activas === 'true') {
                \Log::info('Aplicando filtro solo activas');
                $query->activas();
            }

            // Paginación con validación
            $perPage = $request->get('per_page', 15);
            $perPage = max(1, min((int) $perPage, 100)); // Asegurar que esté entre 1 y 100

            $page = $request->get('page', 1);
            $page = max(1, (int) $page); // Asegurar que sea al menos 1

            \Log::info('Ejecutando paginación', ['per_page' => $perPage, 'page' => $page]);

            $obras = $query->orderBy('id', 'asc')->paginate($perPage, ['*'], 'page', $page);

            \Log::info('Query ejecutada exitosamente', ['total_obras' => $obras->total()]);

            // Log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'ver_obras',
                'tabla_afectada' => 'obras',
                'detalles' => 'Usuario consultó lista de obras',
            ]);

            if ($request->expectsJson()) {
                \Log::info('Retornando respuesta JSON');
                return response()->json([
                    'message' => 'Obras obtenidas exitosamente.',
                    'data' => $obras,
                ]);
            }

            \Log::info('Obteniendo opciones de estatus');
            $estatusOptions = $this->getEstatusOptions();
            
            \Log::info('Calculando estadísticas');
            // Calcular estadísticas
            $estadisticas = [
                'total' => Obra::count(),
                'activas' => Obra::where('estatus', 'activa')->count(),
                'en_progreso' => Obra::where('estatus', 'en_progreso')->count(),
                'finalizadas' => Obra::where('estatus', 'completada')->count(),
            ];

            \Log::info('Renderizando vista obras.index', [
                'total_obras' => $obras->total(),
                'estadisticas' => $estadisticas
            ]);

            return view('obras.index', compact('obras', 'estatusOptions', 'estadisticas'));
        } catch (Exception $e) {
            \Log::error('=== ERROR EN ObraController@index ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            // Imprimir también en consola con dd() para debug inmediato
            if (app()->environment('local')) {
                dump([
                    'ERROR_OBRAS_INDEX' => [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'user_id' => Auth::id()
                    ]
                ]);
            }
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al obtener las obras: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Error al obtener las obras: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new obra.
     */
    public function create(Request $request)
    {
        try {
            // Log inicial para debug
            \Log::info('=== INICIO ObraController@create ===', [
                'user_id' => Auth::id(),
                'url' => request()->fullUrl()
            ]);

            if (! $this->hasPermission('crear_obras')) {
                \Log::warning('Usuario sin permisos para crear obras', [
                    'user_id' => Auth::id()
                ]);
                $message = 'No tienes permisos para crear obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            \Log::info('Obteniendo datos para formulario de creación');
            // Remover la referencia a Cliente::all() que no existe
            $estados = ['planificada', 'en_progreso', 'suspendida', 'completada', 'cancelada'];

            \Log::info('Renderizando vista obras.create', [
                'estados_count' => count($estados)
            ]);

            return view('obras.create', compact('estados'));
        } catch (Exception $e) {
            \Log::error('=== ERROR EN ObraController@create ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            // Imprimir también en consola con dd() para debug inmediato
            if (app()->environment('local')) {
                dump([
                    'ERROR_OBRAS_CREATE' => [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'user_id' => Auth::id()
                    ]
                ]);
            }

            if (request()->expectsJson()) {
                return response()->json(['error' => 'Error al cargar formulario de creación: ' . $e->getMessage()], 500);
            }

            return redirect()->route('obras.index')->with('error', 'Error al cargar formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created obra in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log inicial para debug
            \Log::info('=== INICIO ObraController@store ===', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            if (! $this->hasPermission('crear_obras')) {
                \Log::warning('Usuario sin permisos para crear obras en store', [
                    'user_id' => Auth::id()
                ]);
                $message = 'No tienes permisos para crear obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message)->withInput();
            }

            \Log::info('Validando datos de entrada para nueva obra');
            $validatedData = $request->validate([
                'nombre_obra' => 'required|string|max:200|unique:obras,nombre_obra',
                'estatus' => 'required|in:planificada,en_progreso,suspendida,completada,cancelada',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'avance' => 'nullable|integer|min:0|max:100',
                'observaciones' => 'nullable|string',
            ]);

            \Log::info('Datos validados correctamente', ['validated_data' => $validatedData]);

            $obra = Obra::create($validatedData);
            
            \Log::info('Obra creada exitosamente', [
                'obra_id' => $obra->id,
                'obra_nombre' => $obra->nombre_obra
            ]);

            // Log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => 'Usuario creó nueva obra: ' . $obra->nombre_obra,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Obra creada exitosamente.',
                    'data' => $obra
                ], 201);
            }

            return redirect()->route('obras.index')->with('success', 'Obra creada exitosamente.');
        } catch (ValidationException $e) {
            \Log::warning('Error de validación en store', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            
            if (app()->environment('local')) {
                dump([
                    'VALIDATION_ERROR_OBRAS_STORE' => [
                        'errors' => $e->errors(),
                        'user_id' => Auth::id()
                    ]
                ]);
            }
            
            throw $e;
        } catch (Exception $e) {
            \Log::error('=== ERROR EN ObraController@store ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            // Imprimir también en consola con dd() para debug inmediato
            if (app()->environment('local')) {
                dump([
                    'ERROR_OBRAS_STORE' => [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'user_id' => Auth::id(),
                        'request_data' => $request->all()
                    ]
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al crear obra: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Error al crear obra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified obra.
     */
    public function show(Request $request, $id)
    {
        try {
            // Log inicial para debug
            \Log::info('=== INICIO ObraController@show ===', [
                'obra_id' => $id,
                'user_id' => Auth::id(),
                'url' => request()->fullUrl()
            ]);

            if (! $this->hasPermission('ver_obras')) {
                \Log::warning('Usuario sin permisos para ver obra específica', [
                    'user_id' => Auth::id(),
                    'obra_id' => $id
                ]);
                $message = 'No tienes permisos para ver esta obra.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            \Log::info('Buscando obra por ID');
            $obra = Obra::find($id);

            if (! $obra) {
                \Log::warning('Obra no encontrada', ['obra_id' => $id]);
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Obra no encontrada.'], 404);
                }

                return redirect()->back()->with('error', 'Obra no encontrada.');
            }

            \Log::info('Obra encontrada exitosamente', [
                'obra_id' => $obra->id,
                'obra_nombre' => $obra->nombre
            ]);

            // Log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'ver_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => 'Usuario visualizó obra: ' . $obra->nombre,
            ]);

            \Log::info('Renderizando vista obras.show');
            return view('obras.show', compact('obra'));
        } catch (Exception $e) {
            \Log::error('=== ERROR EN ObraController@show ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'obra_id' => $id,
                'user_id' => Auth::id()
            ]);
            
            // Imprimir también en consola con dd() para debug inmediato
            if (app()->environment('local')) {
                dump([
                    'ERROR_OBRAS_SHOW' => [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'obra_id' => $id,
                        'user_id' => Auth::id()
                    ]
                ]);
            }

            if (request()->expectsJson()) {
                return response()->json(['error' => 'Error al obtener la obra: ' . $e->getMessage()], 500);
            }

            return redirect()->route('obras.index')->with('error', 'Error al obtener la obra: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified obra.
     */
    public function edit(Request $request, $id)
    {
        try {
            // Log inicial para debug
            \Log::info('=== INICIO ObraController@edit ===', [
                'obra_id' => $id,
                'user_id' => Auth::id()
            ]);

            if (! $this->hasPermission('editar_obras')) {
                \Log::warning('Usuario sin permisos para editar obras', [
                    'user_id' => Auth::id(),
                    'obra_id' => $id
                ]);
                $message = 'No tienes permisos para editar obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            \Log::info('Buscando obra para edición');
            $obra = Obra::find($id);

            if (! $obra) {
                \Log::warning('Obra no encontrada', ['obra_id' => $id]);
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Obra no encontrada.'], 404);
                }

                return redirect()->back()->with('error', 'Obra no encontrada.');
            }

            \Log::info('Obra encontrada para edición', [
                'obra_id' => $obra->id,
                'obra_nombre' => $obra->nombre
            ]);

            // Obtener datos para los formularios
            $estatusOptions = $this->getEstatusOptions();
            
            // Obtener vehículos disponibles (sin asignaciones activas)
            $vehiculosDisponibles = Vehiculo::whereDoesntHave('asignacionesObraActivas')
                ->whereHas('estatus', function ($q) {
                    $q->where('nombre_estatus', 'Disponible')
                      ->orWhere('nombre_estatus', 'Activo');
                })
                ->orderBy('marca')->orderBy('modelo')
                ->get(['id', 'marca', 'modelo', 'placas']);

            // Obtener operadores disponibles (que no tengan restricciones)
            $operadoresDisponibles = Personal::activos()->operadores()
                ->orderBy('nombre_completo')
                ->get(['id', 'nombre_completo', 'categoria_id']);

            // Obtener usuarios para encargados
            $encargadosDisponibles = User::with('personal')->get();

            \Log::info('Renderizando vista de edición de obra', [
                'obra_id' => $obra->id,
                'estatus_options' => $estatusOptions,
                'vehiculos_disponibles' => $vehiculosDisponibles->count(),
                'operadores_disponibles' => $operadoresDisponibles->count(),
                'encargados_disponibles' => $encargadosDisponibles->count(),
            ]);

            // Log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'editar_obra_formulario',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Usuario accedió al formulario de edición de la obra: {$obra->nombre_obra}",
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Formulario de edición de obra.',
                    'data' => [
                        'obra' => $obra,
                        'estatus_options' => $estatusOptions,
                        'vehiculos_disponibles' => $vehiculosDisponibles,
                        'operadores_disponibles' => $operadoresDisponibles,
                        'encargados_disponibles' => $encargadosDisponibles,
                    ],
                ]);
            }

            return view('obras.edit', compact(
                'obra', 
                'estatusOptions', 
                'vehiculosDisponibles', 
                'operadoresDisponibles', 
                'encargadosDisponibles'
            ));
        } catch (Exception $e) {
            \Log::error('=== ERROR EN ObraController@edit ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'obra_id' => $id,
                'user_id' => Auth::id()
            ]);
            
            // Imprimir también en consola con dd() para debug inmediato
            if (app()->environment('local')) {
                dump([
                    'ERROR_OBRAS_EDIT' => [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'obra_id' => $id,
                        'user_id' => Auth::id()
                    ]
                ]);
            }

            return redirect()->route('obras.index')->with('error', 'Error al cargar la edición de obra: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified obra in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info("=== INICIO UPDATE OBRA ===");
            Log::info("ID recibido: " . $id);
            Log::info("Datos del request: " . json_encode($request->all()));
            
            $obra = Obra::findOrFail($id);
            Log::info("Obra encontrada: " . json_encode($obra->toArray()));
            
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'estado' => 'required|in:pendiente,en_progreso,completada,cancelada'
            ]);
            Log::info("Datos validados: " . json_encode($validatedData));

            $obra->update($validatedData);
            Log::info("Obra actualizada exitosamente");
            
            return redirect()->route('obras.index')->with('success', 'Obra actualizada exitosamente.');
        } catch (Exception $e) {
            Log::error("=== ERROR EN UPDATE OBRA ===");
            Log::error("Mensaje de error: " . $e->getMessage());
            Log::error("Archivo: " . $e->getFile());
            Log::error("Línea: " . $e->getLine());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return redirect()->back()->withErrors(['error' => 'Error al actualizar la obra: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified obra from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            if (! $this->hasPermission('eliminar_obras')) {
                $message = 'No tienes permisos para eliminar obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            $obra = Obra::find($id);

            if (! $obra) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Obra no encontrada.'], 404);
                }

                return redirect()->back()->with('error', 'Obra no encontrada.');
            }

            $nombreObra = $obra->nombre_obra;
            $obraId = $obra->id;

            // Soft delete
            $obra->delete();

            // Log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obraId,
                'detalles' => "Se eliminó la obra: {$nombreObra}",
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Obra eliminada exitosamente.',
                ]);
            }

            return redirect()->route('obras.index')->with('success', 'Obra eliminada exitosamente.');
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al eliminar la obra.'], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar la obra.');
        }
    }

    /**
     * Restore a soft deleted obra.
     */
    public function restore(Request $request, $id)
    {
        try {
            if (! $this->hasPermission('restaurar_obras')) {
                $message = 'No tienes permisos para restaurar obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            $obra = Obra::withTrashed()->find($id);

            if (! $obra) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Obra no encontrada.'], 404);
                }

                return redirect()->back()->with('error', 'Obra no encontrada.');
            }

            if (! $obra->trashed()) {
                $message = 'La obra no está eliminada, no se puede restaurar.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 400);
                }

                return redirect()->back()->with('error', $message);
            }

            $obra->restore();

            // Log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'restaurar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Se restauró la obra: {$obra->nombre_obra}",
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Obra restaurada exitosamente.',
                    'data' => $obra,
                ]);
            }

            return redirect()->route('obras.index')->with('success', 'Obra restaurada exitosamente.');
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al restaurar la obra.'], 500);
            }

            return redirect()->back()->with('error' => 'Error al restaurar la obra.');
        }
    }

    /**
     * Get status options for obras.
     */
    public function getEstatusOptions()
    {
        return [
            'planificada' => 'Planificada',
            'en_progreso' => 'En Progreso',
            'suspendida' => 'Suspendida',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
        ];
    }

    /**
     * API endpoint to get status options.
     */
    public function status(Request $request)
    {
        try {
            if (! $this->hasPermission('ver_obras')) {
                return response()->json(['error' => 'No tienes permisos para ver los estatus.'], 403);
            }

            $options = $this->getEstatusOptions();

            // Convertir el array asociativo a array de objetos con la estructura esperada
            $estatusArray = [];
            foreach ($options as $valor => $nombre) {
                $estatusArray[] = [
                    'valor' => $valor,
                    'nombre' => $nombre,
                    'descripcion' => $nombre, // Por ahora usamos el mismo nombre como descripción
                ];
            }

            return response()->json([
                'message' => 'Opciones de estatus obtenidas exitosamente.',
                'data' => $estatusArray,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener opciones de estatus.'], 500);
        }
    }

    /**
     * Check if user has permission.
     */
    private function hasPermission($permission)
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return $user->hasPermission($permission);
    }
}
