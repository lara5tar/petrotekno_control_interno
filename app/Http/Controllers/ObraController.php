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
            if (! $this->hasPermission('ver_obras')) {
                $message = 'No tienes permisos para ver las obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            // Aplicar filtros de búsqueda
            $query = Obra::query();

            if ($request->filled('buscar')) {
                $searchTerm = $request->buscar;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nombre_obra', 'like', "%{$searchTerm}%")
                        ->orWhere('estatus', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->filled('estatus')) {
                $query->where('estatus', $request->estatus);
            }

            if ($request->filled('solo_activas') && $request->solo_activas === 'true') {
                $query->activas();
            }

            // Paginación con validación
            $perPage = $request->get('per_page', 15);
            $perPage = max(1, min((int) $perPage, 100)); // Asegurar que esté entre 1 y 100

            $page = $request->get('page', 1);
            $page = max(1, (int) $page); // Asegurar que sea al menos 1

            $obras = $query->orderBy('fecha_inicio', 'desc')->paginate($perPage, ['*'], 'page', $page);

            // Log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'ver_obras',
                'tabla_afectada' => 'obras',
                'detalles' => 'Usuario consultó lista de obras',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Obras obtenidas exitosamente.',
                    'data' => $obras,
                ]);
            }

            $estatusOptions = $this->getEstatusOptions();

            return view('obras.index', compact('obras', 'estatusOptions'));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al obtener las obras.'], 500);
            }

            return redirect()->back()->with('error', 'Error al obtener las obras.');
        }
    }

    /**
     * Show the form for creating a new obra.
     */
    public function create(Request $request)
    {
        try {
            if (! $this->hasPermission('crear_obras')) {
                $message = 'No tienes permisos para crear obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            $estatusOptions = $this->getEstatusOptions();

            // Datos para asignaciones
            $vehiculosDisponibles = Vehiculo::disponibles()->get();
            $operadoresDisponibles = Personal::operadores()->get();
            $encargadosDisponibles = Personal::encargados()->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Formulario de creación de obra',
                    'data' => [
                        'estatus_options' => $estatusOptions,
                        'vehiculos_disponibles' => $vehiculosDisponibles,
                        'operadores_disponibles' => $operadoresDisponibles,
                        'encargados_disponibles' => $encargadosDisponibles,
                    ],
                ]);
            }

            return view('obras.create', compact(
                'estatusOptions',
                'vehiculosDisponibles',
                'operadoresDisponibles',
                'encargadosDisponibles'
            ));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al cargar el formulario.'], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar el formulario.');
        }
    }

    /**
     * Store a newly created obra in storage.
     */
    public function store(Request $request)
    {
        try {
            if (! $this->hasPermission('crear_obras')) {
                $message = 'No tienes permisos para crear obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message)->withInput();
            }

            $validatedData = $request->validate([
                'nombre_obra' => 'required|string|min:3|max:255|unique:obras,nombre_obra',
                'estatus' => 'required|string|in:' . implode(',', array_keys($this->getEstatusOptions())),
                'avance' => 'nullable|integer|min:0|max:100',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                // Campos de asignación (opcionales)
                'vehiculo_id' => 'nullable|exists:vehiculos,id',
                'operador_id' => 'nullable|exists:personal,id',
                'encargado_id' => 'nullable|exists:personal,id',
                'fecha_asignacion' => 'nullable|date',
                'fecha_liberacion' => 'nullable|date|after_or_equal:fecha_asignacion',
                // Campos de kilometraje
                'kilometraje_inicial' => 'nullable|integer|min:0',
                'kilometraje_final' => 'nullable|integer|min:0|gte:kilometraje_inicial',
                // Campos de combustible
                'combustible_inicial' => 'nullable|numeric|min:0',
                'combustible_final' => 'nullable|numeric|min:0',
                'combustible_suministrado' => 'nullable|numeric|min:0',
                'costo_combustible' => 'nullable|numeric|min:0',
                // Observaciones
                'observaciones' => 'nullable|string|max:1000',
                // Campos de archivos
                'archivo_contrato' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB máximo
                'archivo_fianza' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'archivo_acta_entrega_recepcion' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            ]);

            // Validaciones de negocio para asignaciones
            if (!empty($validatedData['vehiculo_id']) && empty($validatedData['operador_id'])) {
                throw new \InvalidArgumentException('Si asignas un vehículo, debes asignar un operador.');
            }

            if (!empty($validatedData['operador_id']) && empty($validatedData['vehiculo_id'])) {
                throw new \InvalidArgumentException('Si asignas un operador, debes asignar un vehículo.');
            }

            // Crear la obra primero sin archivos
            $obraData = Arr::except($validatedData, ['archivo_contrato', 'archivo_fianza', 'archivo_acta_entrega_recepcion']);
            $obra = Obra::create($obraData);

            // Manejar subida de archivos después de crear la obra
            if ($request->hasFile('archivo_contrato')) {
                $obra->subirContrato($request->file('archivo_contrato'));
            }

            if ($request->hasFile('archivo_fianza')) {
                $obra->subirFianza($request->file('archivo_fianza'));
            }

            if ($request->hasFile('archivo_acta_entrega_recepcion')) {
                $obra->subirActaEntregaRecepcion($request->file('archivo_acta_entrega_recepcion'));
            }

            // Log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Se creó la obra: {$obra->nombre_obra}",
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Obra creada exitosamente.',
                    'data' => $obra,
                ], 201);
            }

            return redirect()->route('obras.index')->with('success', 'Obra creada exitosamente.');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Error de validación.',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()->with('error', 'Error de validación.')->withErrors($e->errors())->withInput();
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Datos inválidos.',
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al crear la obra.'], 500);
            }

            return redirect()->back()->with('error', 'Error al crear la obra.')->withInput();
        }
    }

    /**
     * Display the specified obra.
     */
    public function show(Request $request, $id)
    {
        try {
            if (! $this->hasPermission('ver_obras')) {
                $message = 'No tienes permisos para ver esta obra.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            $obra = Obra::with(['vehiculo', 'operador', 'encargado'])->find($id);

            if (! $obra) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Obra no encontrada.'], 404);
                }

                return redirect()->back()->with('error', 'Obra no encontrada.');
            }

            // Log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'ver_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Usuario consultó la obra: {$obra->nombre_obra}",
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Obra obtenida exitosamente.',
                    'data' => $obra,
                ]);
            }

            return view('obras.show', compact('obra'));
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al obtener la obra.'], 500);
            }

            return redirect()->back()->with('error', 'Error al obtener la obra.');
        }
    }

    /**
     * Show the form for editing the specified obra.
     */
    public function edit(Request $request, $id)
    {
        try {
            if (! $this->hasPermission('actualizar_obras')) {
                $message = 'No tienes permisos para editar obras.';
                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->back()->with('error', $message);
            }

            $obra = Obra::with(['vehiculo', 'operador', 'encargado'])->find($id);

            if (! $obra) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Obra no encontrada.'], 404);
                }

                return redirect()->back()->with('error', 'Obra no encontrada.');
            }

            // Obtener datos para los formularios
            $estatusOptions = $this->getEstatusOptions();
            
            // Obtener vehículos disponibles (incluir el actual si está asignado)
            $vehiculos = Vehiculo::disponibles()->get();
            if ($obra->vehiculo_id && !$vehiculos->contains('id', $obra->vehiculo_id)) {
                $vehiculoActual = Vehiculo::find($obra->vehiculo_id);
                if ($vehiculoActual) {
                    $vehiculos->prepend($vehiculoActual);
                }
            }

            // Obtener operadores disponibles (incluir el actual si está asignado)
            $operadores = Personal::operadores()->disponibles()->get();
            if ($obra->operador_id && !$operadores->contains('id', $obra->operador_id)) {
                $operadorActual = Personal::find($obra->operador_id);
                if ($operadorActual) {
                    $operadores->prepend($operadorActual);
                }
            }

            // Obtener encargados disponibles (incluir el actual si está asignado)
            $encargados = User::with('personal')->get();
            if ($obra->encargado_id && !$encargados->contains('id', $obra->encargado_id)) {
                $encargadoActual = User::find($obra->encargado_id);
                if ($encargadoActual) {
                    $encargados->prepend($encargadoActual);
                }
            }

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
                        'vehiculos' => $vehiculos,
                        'operadores' => $operadores,
                        'encargados' => $encargados,
                    ],
                ]);
            }

            return view('obras.edit', compact(
                'obra', 
                'estatusOptions', 
                'vehiculos', 
                'operadores', 
                'encargados'
            ));
        } catch (Exception $e) {
            \Log::error('Error al cargar formulario de edición de obra: ' . $e->getMessage(), [
                'obra_id' => $id,
                'usuario_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al cargar el formulario.'], 500);
            }

            return redirect()->back()->with('error', 'Error al cargar el formulario.');
        }
    }

    /**
     * Update the specified obra in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            if (! $this->hasPermission('actualizar_obras')) {
                $message = 'No tienes permisos para editar obras.';
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

            // Validaciones para transiciones de estados
            if ($request->filled('estatus')) {
                $nuevoEstatus = $request->input('estatus');
                $estatusActual = $obra->estatus;

                // Validar transiciones según modelo
                $transicionesPermitidas = [
                    'planificada' => ['planificada', 'en_progreso', 'cancelada'],
                    'en_progreso' => ['en_progreso', 'suspendida', 'completada', 'cancelada'],
                    'suspendida' => ['suspendida', 'en_progreso', 'cancelada'],
                    'completada' => ['completada'], // Permitir mantener el mismo estado
                    'cancelada' => ['cancelada'], // Permitir mantener el mismo estado
                ];

                if (! in_array($nuevoEstatus, $transicionesPermitidas[$estatusActual] ?? [])) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => 'Error de validación.',
                            'errors' => ['estatus' => ["No se puede cambiar de '{$estatusActual}' a '{$nuevoEstatus}'"]],
                        ], 422);
                    }

                    return redirect()->back()->with('error', "No se puede cambiar de '{$estatusActual}' a '{$nuevoEstatus}'")->withInput();
                }
            }

            $validatedData = $request->validate([
                'nombre_obra' => 'sometimes|required|string|min:3|max:255|unique:obras,nombre_obra,' . $obra->id,
                'estatus' => 'sometimes|required|string|in:' . implode(',', array_keys($this->getEstatusOptions())),
                'avance' => 'sometimes|nullable|integer|min:0|max:100',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|nullable|date|after_or_equal:fecha_inicio',
                // Campos de asignación (opcionales)
                'vehiculo_id' => 'sometimes|nullable|exists:vehiculos,id',
                'operador_id' => 'sometimes|nullable|exists:personal,id',
                'encargado_id' => 'sometimes|nullable|exists:users,id',
                'fecha_asignacion' => 'sometimes|nullable|date',
                'fecha_liberacion' => 'sometimes|nullable|date|after_or_equal:fecha_asignacion',
                // Campos de kilometraje
                'kilometraje_inicial' => 'sometimes|nullable|integer|min:0',
                'kilometraje_final' => 'sometimes|nullable|integer|min:0|gte:kilometraje_inicial',
                // Campos de combustible
                'combustible_inicial' => 'sometimes|nullable|numeric|min:0',
                'combustible_final' => 'sometimes|nullable|numeric|min:0',
                'combustible_suministrado' => 'sometimes|nullable|numeric|min:0',
                'costo_combustible' => 'sometimes|nullable|numeric|min:0',
                // Observaciones
                'observaciones' => 'sometimes|nullable|string|max:1000',
                // Campos de archivos
                'archivo_contrato' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB máximo
                'archivo_fianza' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'archivo_acta_entrega_recepcion' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            ]);

            // Validaciones de negocio para asignaciones
            if (!empty($validatedData['vehiculo_id']) && empty($validatedData['operador_id'])) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Error de validación.',
                        'errors' => ['operador_id' => ['Si asignas un vehículo, debes asignar un operador.']],
                    ], 422);
                }
                return redirect()->back()->with('error', 'Si asignas un vehículo, debes asignar un operador.')->withInput();
            }

            if (!empty($validatedData['operador_id']) && empty($validatedData['vehiculo_id'])) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Error de validación.',
                        'errors' => ['vehiculo_id' => ['Si asignas un operador, debes asignar un vehículo.']],
                    ], 422);
                }
                return redirect()->back()->with('error', 'Si asignas un operador, debes asignar un vehículo.')->withInput();
            }

            $obraAnterior = $obra->toArray();
            
            // Actualizar datos básicos de la obra sin archivos
            $obraData = Arr::except($validatedData, ['archivo_contrato', 'archivo_fianza', 'archivo_acta_entrega_recepcion']);
            $obra->update($obraData);

            // Manejar subida de archivos si se proporcionaron nuevos
            if ($request->hasFile('archivo_contrato')) {
                $obra->subirContrato($request->file('archivo_contrato'));
            }

            if ($request->hasFile('archivo_fianza')) {
                $obra->subirFianza($request->file('archivo_fianza'));
            }

            if ($request->hasFile('archivo_acta_entrega_recepcion')) {
                $obra->subirActaEntregaRecepcion($request->file('archivo_acta_entrega_recepcion'));
            }

            // Log de acción detallado
            $cambios = [];
            foreach ($validatedData as $campo => $nuevoValor) {
                $valorAnterior = $obra->getOriginal($campo);
                if ($valorAnterior != $nuevoValor) {
                    $cambios[] = "{$campo}: '{$valorAnterior}' → '{$nuevoValor}'";
                }
            }

            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_obra',
                'tabla_afectada' => 'obras',
                'registro_id' => $obra->id,
                'detalles' => "Se actualizó la obra: {$obra->nombre_obra}" . (count($cambios) > 0 ? ". Cambios: " . implode(', ', $cambios) : ''),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Obra actualizada exitosamente.',
                    'data' => $obra->fresh(),
                ]);
            }

            return redirect()->route('obras.show', $obra->id)->with('success', 'Obra actualizada exitosamente.');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Error de validación.',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()->with('error', 'Error de validación.')->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al actualizar la obra.'], 500);
            }

            return redirect()->back()->with('error', 'Error al actualizar la obra.')->withInput();
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
