<?php

namespace App\Http\Controllers;

use App\Models\AsignacionObra;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraController extends Controller
{
    /**
     * Display a listing of obras with hybrid response.
     */
    public function index(Request $request)
    {
        try {
            Log::info('=== INICIO ObraController@index ===', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'url' => $request->fullUrl()
            ]);

            if (! $this->hasPermission('ver_obras')) {
                Log::warning('Usuario sin permisos para ver obras', [
                    'user_id' => Auth::id(),
                    'permissions' => Auth::user()?->permissions ?? 'No user'
                ]);
                $message = 'No tienes permisos para ver las obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            Log::info('Construyendo query de obras');
            
            $query = Obra::query();

            if ($request->filled('buscar') || $request->filled('search')) {
                $searchTerm = $request->buscar ?? $request->search;
                Log::info('Aplicando filtro de búsqueda', ['search_term' => $searchTerm]);
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nombre_obra', 'like', "%{$searchTerm}%")
                        ->orWhere('estatus', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->filled('estatus')) {
                Log::info('Aplicando filtro de estatus', ['estatus' => $request->estatus]);
                $query->where('estatus', $request->estatus);
            }

            if ($request->filled('fecha_inicio')) {
                Log::info('Aplicando filtro de fecha', ['fecha_inicio' => $request->fecha_inicio]);
                $query->whereDate('fecha_inicio', '>=', $request->fecha_inicio);
            }

            if ($request->filled('solo_activas') && $request->solo_activas === 'true') {
                Log::info('Aplicando filtro solo activas');
                $query->activas();
            }

            $perPage = $request->get('per_page', 15);
            $perPage = max(1, min((int) $perPage, 100));

            $page = $request->get('page', 1);
            $page = max(1, (int) $page);

            Log::info('Ejecutando paginación', ['per_page' => $perPage, 'page' => $page]);

            $obras = $query->orderBy('id', 'asc')->paginate($perPage, ['*'], 'page', $page);

            Log::info('Query ejecutada exitosamente', ['total_obras' => $obras->total()]);

            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'ver_obras',
                'tabla_afectada' => 'obras',
                'detalles' => 'Usuario consultó lista de obras',
            ]);

            if ($request->expectsJson()) {
                Log::info('Retornando respuesta JSON');
                return response()->json([
                    'message' => 'Obras obtenidas exitosamente.',
                    'data' => $obras,
                ]);
            }

            Log::info('Obteniendo opciones de estatus');
            $estatusOptions = $this->getEstatusOptions();
            
            Log::info('Calculando estadísticas');
            $estadisticas = [
                'total' => Obra::count(),
                'activas' => Obra::where('estatus', 'activa')->count(),
                'en_progreso' => Obra::where('estatus', 'en_progreso')->count(),
                'finalizadas' => Obra::where('estatus', 'completada')->count(),
            ];

            Log::info('Renderizando vista obras.index', [
                'total_obras' => $obras->total(),
                'estadisticas' => $estadisticas
            ]);

            return view('obras.index', compact('obras', 'estatusOptions', 'estadisticas'));
        } catch (Exception $e) {
            Log::error('=== ERROR EN ObraController@index ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
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
            Log::info('=== INICIO ObraController@create ===', [
                'user_id' => Auth::id(),
                'url' => request()->fullUrl()
            ]);

            if (! $this->hasPermission('crear_obras')) {
                Log::warning('Usuario sin permisos para crear obras', [
                    'user_id' => Auth::id()
                ]);
                $message = 'No tienes permisos para crear obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            Log::info('Obteniendo datos para formulario de creación');
            
            // Obtener estados disponibles para obras
            $estados = ['planificada', 'en_progreso', 'suspendida', 'completada', 'cancelada'];
            
            // Obtener encargados SOLO de la tabla personal
            Log::info('=== OBTENIENDO ENCARGADOS (SOLO PERSONAL) ===');
            try {
                // CORREGIDO: Solo obtener personal encargado, sin usuarios
                $encargados = collect();
                if (class_exists(Personal::class)) {
                    $encargados = Personal::encargados()
                        ->whereNotNull('nombre_completo') // Solo personal con nombre completo
                        ->where('nombre_completo', '!=', '') // Evitar nombres vacíos
                        ->with('categoria:id,nombre_categoria')
                        ->orderBy('nombre_completo')
                        ->get(['id', 'nombre_completo', 'categoria_id'])
                        ->map(function($personal) {
                            return [
                                'id' => $personal->id,
                                'nombre_completo' => $personal->nombre_completo,
                                'categoria' => $personal->categoria ? $personal->categoria->nombre_categoria : 'Sin categoría'
                            ];
                        });
                }
                
                Log::info('Encargados (solo personal) obtenidos exitosamente', [
                    'total_count' => $encargados->count(),
                    'encargados' => $encargados->toArray()
                ]);
                
                if ($encargados->isEmpty()) {
                    Log::warning('No se encontraron encargados disponibles en personal');
                }
                
            } catch (Exception $e) {
                Log::error('Error al obtener encargados de personal', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $encargados = collect();
            }
            
            // Obtener vehículos disponibles
            Log::info('=== OBTENIENDO VEHICULOS PARA OBRA ===');
            $vehiculos = collect();
            if (class_exists(Vehiculo::class)) {
                try {
                    Log::info('Iniciando carga de TODOS los vehículos');
                    
                    // Obtener TODOS los vehículos
                    $todosVehiculos = Vehiculo::with('estatus:id,nombre_estatus')
                        ->orderBy('marca')
                        ->orderBy('modelo')
                        ->get(['id', 'marca', 'modelo', 'anio', 'placas', 'kilometraje_actual', 'estatus_id']);
                    
                    // CORREGIDO: Obtener vehículos que ya están asignados a obras activas
                    $vehiculosAsignados = [];
                    try {
                        $asignaciones = AsignacionObra::where('estado', AsignacionObra::ESTADO_ACTIVA)
                            ->with('obra:id,nombre_obra')
                            ->get(['vehiculo_id', 'obra_id']);
                        
                        foreach ($asignaciones as $asignacion) {
                            if ($asignacion->obra) {
                                $vehiculosAsignados[$asignacion->vehiculo_id] = $asignacion->obra->nombre_obra;
                            }
                        }
                    } catch (Exception $e) {
                        Log::error('Error al obtener vehículos asignados', [
                            'message' => $e->getMessage()
                        ]);
                        $vehiculosAsignados = [];
                    }
                    
                    // Agregar información de asignación a cada vehículo
                    $todosVehiculos = $todosVehiculos->map(function($vehiculo) use ($vehiculosAsignados) {
                        $vehiculo->esta_asignado = isset($vehiculosAsignados[$vehiculo->id]);
                        $vehiculo->obra_asignada = $vehiculosAsignados[$vehiculo->id] ?? null;
                        
                        return $vehiculo;
                    });
                    
                    // NUEVO: Separar vehículos en disponibles y asignados
                    $vehiculosDisponibles = $todosVehiculos->filter(function($vehiculo) {
                        return !$vehiculo->esta_asignado;
                    });
                    
                    $vehiculosNoDisponibles = $todosVehiculos->filter(function($vehiculo) {
                        return $vehiculo->esta_asignado;
                    });
                    
                    // NUEVO: Combinar los arreglos poniendo disponibles primero y no disponibles después
                    $vehiculos = $vehiculosDisponibles->concat($vehiculosNoDisponibles);
                    
                    Log::info('TODOS los vehículos cargados exitosamente con información de asignación', [
                        'count' => $vehiculos->count(),
                        'disponibles' => $vehiculosDisponibles->count(),
                        'no_disponibles' => $vehiculosNoDisponibles->count(),
                        'vehiculos' => $vehiculos->map(function($vehiculo) {
                            return [
                                'id' => $vehiculo->id,
                                'placas' => $vehiculo->placas,
                                'marca' => $vehiculo->marca ?? 'N/A',
                                'modelo' => $vehiculo->modelo ?? 'N/A',
                                'anio' => $vehiculo->anio ?? 'N/A',
                                'esta_asignado' => $vehiculo->esta_asignado,
                                'obra_asignada' => $vehiculo->obra_asignada
                            ];
                        })->toArray()
                    ]);
                    
                    if ($vehiculos->isEmpty()) {
                        Log::warning('No se encontraron vehículos en el sistema');
                    }
                    
                } catch (Exception $e) {
                    Log::error('=== ERROR AL OBTENER VEHICULOS ===', [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $vehiculos = collect();
                }
            } else {
                Log::warning('La clase Vehiculo no existe en el sistema');
            }

            Log::info('Renderizando vista obras.create', [
                'estados_count' => count($estados),
                'encargados_count' => $encargados->count(),
                'vehiculos_count' => $vehiculos->count()
            ]);

            return view('obras.create', compact('estados', 'encargados', 'vehiculos'));
        } catch (Exception $e) {
            Log::error('=== ERROR EN ObraController@create ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
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
        Log::info('=== INICIO ObraController@store ===', [
            'user_id' => auth()->id(),
            'datos_recibidos' => $request->all()
        ]);

        try {
            // Validación de los datos de entrada
            $validated = $request->validate([
                'nombre_obra' => 'required|string|max:255',
                'estatus' => 'required|in:planificada,en_progreso,suspendida,completada,cancelada',
                'avance' => 'nullable|integer|min:0|max:100',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'observaciones' => 'nullable|string|max:1000',
                'encargado_id' => [
                    'required',
                    'numeric',
                    'exists:personal,id' // Solo validar que exista en personal
                ],
                
                // Validación simplificada de vehículos seleccionados con checkboxes
                'vehiculos_seleccionados' => 'nullable|array',
                'vehiculos_seleccionados.*' => 'exists:vehiculos,id',
                
                // Validación de archivos
                'archivo_contrato' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'archivo_fianza' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'archivo_acta_entrega_recepcion' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            ]);

            Log::info('Datos validados correctamente', ['validated' => $validated]);

            DB::beginTransaction();

            // Crear la obra
            $obra = Obra::create([
                'nombre_obra' => $validated['nombre_obra'],
                'estatus' => $validated['estatus'],
                'avance' => $validated['avance'] ?? 0,
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $validated['fecha_fin'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'encargado_id' => $validated['encargado_id'],
            ]);

            Log::info('Obra creada exitosamente', [
                'obra_id' => $obra->id,
                'nombre_obra' => $obra->nombre_obra
            ]);

            // Procesar archivos si se subieron
            if ($request->hasFile('archivo_contrato')) {
                $obra->subirContrato($request->file('archivo_contrato'));
                Log::info('Contrato subido exitosamente');
            }

            if ($request->hasFile('archivo_fianza')) {
                $obra->subirFianza($request->file('archivo_fianza'));
                Log::info('Fianza subida exitosamente');
            }

            if ($request->hasFile('archivo_acta_entrega_recepcion')) {
                $obra->subirActaEntregaRecepcion($request->file('archivo_acta_entrega_recepcion'));
                Log::info('Acta de entrega-recepción subida exitosamente');
            }

            // Procesar vehículos seleccionados (método simplificado)
            if (!empty($validated['vehiculos_seleccionados'])) {
                Log::info('Procesando vehículos seleccionados', [
                    'total_vehiculos' => count($validated['vehiculos_seleccionados'])
                ]);

                foreach ($validated['vehiculos_seleccionados'] as $vehiculoId) {
                    // Obtener el vehículo para usar su kilometraje actual como inicial
                    $vehiculo = Vehiculo::find($vehiculoId);
                    
                    if ($vehiculo) {
                        // Crear asignación simple
                        $asignacion = AsignacionObra::create([
                            'obra_id' => $obra->id,
                            'vehiculo_id' => $vehiculo->id,
                            'fecha_asignacion' => now(),
                            'kilometraje_inicial' => $vehiculo->kilometraje_actual,
                            'observaciones' => 'Vehículo asignado desde formulario de creación de obra',
                            'estado' => AsignacionObra::ESTADO_ACTIVA,
                        ]);

                        Log::info('Asignación de vehículo creada', [
                            'asignacion_id' => $asignacion->id,
                            'vehiculo_id' => $asignacion->vehiculo_id,
                            'obra_id' => $asignacion->obra_id,
                            'kilometraje_inicial' => $asignacion->kilometraje_inicial
                        ]);
                    }
                }
            }

            DB::commit();

            $totalVehiculos = isset($validated['vehiculos_seleccionados']) ? count($validated['vehiculos_seleccionados']) : 0;
            $mensaje = 'Obra creada exitosamente';
            if ($totalVehiculos > 0) {
                $mensaje .= ' con ' . $totalVehiculos . ' vehículo' . ($totalVehiculos > 1 ? 's' : '') . ' asignado' . ($totalVehiculos > 1 ? 's' : '');
            }

            Log::info('Obra y asignaciones creadas exitosamente', [
                'obra_id' => $obra->id,
                'total_asignaciones' => $totalVehiculos
            ]);

            return redirect()->route('obras.index')->with('success', $mensaje . '.');
            
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación', ['errors' => $e->errors()]);
            
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear obra', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Error al crear obra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified obra.
     */
    public function show(Request $request, $id)
    {
        try {
            Log::info('=== INICIO ObraController@show ===', [
                'obra_id' => $id,
                'user_id' => Auth::id(),
                'url' => request()->fullUrl()
            ]);

            if (! $this->hasPermission('ver_obras')) {
                Log::warning('Usuario sin permisos para ver obra específica', [
                    'user_id' => Auth::id(),
                    'obra_id' => $id
                ]);
                $message = 'No tienes permisos para ver esta obra.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            Log::info('Buscando obra por ID');
            $obra = Obra::find($id);

            if (! $obra) {
                Log::warning('Obra no encontrada', ['obra_id' => $id]);
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Obra no encontrada.'], 404);
                }

                return redirect()->back()->with('error', 'Obra no encontrada.');
            }

            Log::info('Obra encontrada exitosamente', [
                'obra_id' => $obra->id,
                'obra_nombre' => $obra->nombre_obra ?? 'N/A'
            ]);

            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'ver_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => 'Usuario visualizó obra: ' . ($obra->nombre_obra ?? 'ID: ' . $obra->id),
            ]);

            Log::info('Renderizando vista obras.show');
            return view('obras.show', compact('obra'));
        } catch (Exception $e) {
            Log::error('=== ERROR EN ObraController@show ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'obra_id' => $id,
                'user_id' => Auth::id()
            ]);
            
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
            Log::info('=== INICIO ObraController@edit ===', [
                'obra_id' => $id,
                'user_id' => Auth::id()
            ]);

            if (! $this->hasPermission('actualizar_obras')) {
                Log::warning('Usuario sin permisos para editar obras', [
                    'user_id' => Auth::id(),
                    'obra_id' => $id
                ]);
                $message = 'No tienes permisos para editar obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            Log::info('Buscando obra para edición');
            $obra = Obra::with([
                'vehiculo:id,marca,modelo,placas,anio',
                'operador:id,nombre_completo',
                'encargado:id,email',
                'encargado.personal:id,nombre_completo',
                'asignacionesActivas'
            ])->find($id);

            if (! $obra) {
                Log::warning('Obra no encontrada', ['obra_id' => $id]);
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Obra no encontrada.'], 404);
                }

                return redirect()->back()->with('error', 'Obra no encontrada.');
            }

            Log::info('Obra encontrada para edición', [
                'obra_id' => $obra->id,
                'obra_nombre' => $obra->nombre_obra ?? 'N/A'
            ]);

            $estatusOptions = $this->getEstatusOptions();

            // Obtener vehículos disponibles
            Log::info('=== OBTENIENDO VEHICULOS PARA EDICION ===');
            $vehiculos = collect();
            if (class_exists(Vehiculo::class)) {
                try {
                    Log::info('Iniciando carga de vehículos disponibles para edición');
                    $vehiculos = Vehiculo::disponibles()
                        ->with('estatus:id,nombre_estatus')
                        ->orderBy('marca')
                        ->orderBy('modelo')
                        ->get(['id', 'marca', 'modelo', 'anio', 'placas', 'kilometraje_actual', 'estatus_id']);
                    
                    Log::info('Vehículos cargados exitosamente para edición', [
                        'count' => $vehiculos->count()
                    ]);
                    
                } catch (Exception $e) {
                    Log::error('=== ERROR AL OBTENER VEHICULOS PARA EDICION ===', [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    $vehiculos = collect();
                }
            } else {
                Log::warning('La clase Vehiculo no existe en el sistema');
            }

            // Obtener operadores disponibles
            Log::info('=== OBTENIENDO OPERADORES PARA EDICION ===');
            $operadores = collect();
            if (class_exists(Personal::class)) {
                try {
                    $operadores = Personal::operadores()
                        ->whereNotNull('nombre_completo')
                        ->where('nombre_completo', '!=', '')
                        ->orderBy('nombre_completo')
                        ->get(['id', 'nombre_completo']);
                    
                    Log::info('Operadores cargados exitosamente para edición', [
                        'count' => $operadores->count()
                    ]);
                    
                } catch (Exception $e) {
                    Log::error('Error al obtener operadores para edición', [
                        'message' => $e->getMessage()
                    ]);
                    $operadores = collect();
                }
            }

            // Obtener encargados disponibles (usuarios + personal)
            Log::info('=== OBTENIENDO ENCARGADOS PARA EDICION ===');
            try {
                // Obtener usuarios con roles administrativos (con su personal relacionado)
                $usuariosEncargados = User::whereHas('rol', function($query) {
                    $query->whereIn('nombre_rol', ['Admin', 'Supervisor', 'Jefe de Obra']);
                })
                ->whereHas('personal', function($query) {
                    $query->whereNotNull('nombre_completo')
                          ->where('nombre_completo', '!=', '');
                })
                ->with('personal:id,nombre_completo')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'nombre_usuario' => $user->nombre_completo, // CORREGIDO: Usar nombre_completo consistentemente
                        'tipo' => 'usuario',
                        'rol' => $user->rol ? $user->rol->nombre_rol : 'Sin rol'
                    ];
                });

                // Obtener personal encargado (que no sean usuarios del sistema)
                $personalEncargados = collect();
                if (class_exists(Personal::class)) {
                    $personalEncargados = Personal::encargados()
                        ->whereDoesntHave('usuario') // Excluir personal que ya es usuario
                        ->with('categoria:id,nombre_categoria')
                        ->whereNotNull('nombre_completo') // Solo personal con nombre completo
                        ->where('nombre_completo', '!=', '') // Evitar nombres vacíos
                        ->orderBy('nombre_completo')
                        ->get(['id', 'nombre_completo', 'categoria_id'])
                        ->map(function($personal) {
                            return [
                                'id' => $personal->id,
                                'nombre_usuario' => $personal->nombre_completo, // CORREGIDO: Usar nombre_usuario consistentemente
                                'tipo' => 'personal',
                                'categoria' => $personal->categoria ? $personal->categoria->nombre_categoria : 'Sin categoría'
                            ];
                        });
                }

                // Combinar ambos tipos de encargados
                $encargados = $usuariosEncargados->concat($personalEncargados);
                
                Log::info('Encargados obtenidos exitosamente para edición', [
                    'usuarios_count' => $usuariosEncargados->count(),
                    'personal_count' => $personalEncargados->count(),
                    'total_count' => $encargados->count()
                ]);
                
            } catch (Exception $e) {
                Log::error('Error al obtener encargados para edición', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $encargados = collect();
            }

            Log::info('Renderizando vista de edición de obra', [
                'obra_id' => $obra->id,
                'estatus_options' => $estatusOptions,
                'vehiculos_count' => $vehiculos->count(),
                'operadores_count' => $operadores->count(),
                'encargados_count' => $encargados->count()
            ]);

            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'editar_obra_formulario',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Usuario accedió al formulario de edición de la obra: " . ($obra->nombre_obra ?? 'ID: ' . $obra->id),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Formulario de edición de obra.',
                    'data' => [
                        'obra' => $obra,
                        'estatus_options' => $estatusOptions,
                        'vehiculos' => $vehiculos,
                        'operadores' => $operadores,
                        'encargados' => $encargados,
                    ],
                ]);
            }

            return view('obras.edit', compact('obra', 'estatusOptions', 'vehiculos', 'operadores', 'encargados'));
        } catch (Exception $e) {
            Log::error('=== ERROR EN ObraController@edit ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'obra_id' => $id,
                'user_id' => Auth::id()
            ]);
            
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
            if (! $this->hasPermission('actualizar_obras')) {
                $message = 'No tienes permisos para actualizar obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }
                return redirect()->back()->with('error', $message);
            }

            Log::info("=== INICIO UPDATE OBRA ===");
            Log::info("ID recibido: " . $id);
            Log::info("Datos del request: " . json_encode($request->all()));
            
            $obra = Obra::findOrFail($id);
            Log::info("Obra encontrada: " . json_encode($obra->toArray()));
            
            $validatedData = $request->validate([
                'nombre_obra' => 'required|string|max:255',
                'estatus' => 'required|in:planificada,en_progreso,suspendida,completada,cancelada',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'avance' => 'nullable|integer|min:0|max:100',
                'observaciones' => 'nullable|string'
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

            $nombreObra = $obra->nombre_obra ?? 'ID: ' . $obra->id;
            $obraId = $obra->id;

            $obra->delete();

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

            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'restaurar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Se restauró la obra: " . ($obra->nombre_obra ?? 'ID: ' . $obra->id),
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

            return redirect()->back()->with('error', 'Error al restaurar la obra.');
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

            $estatusArray = [];
            foreach ($options as $valor => $nombre) {
                $estatusArray[] = [
                    'valor' => $valor,
                    'nombre' => $nombre,
                    'descripcion' => $nombre,
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
