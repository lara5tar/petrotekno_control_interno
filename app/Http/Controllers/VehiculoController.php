<?php

namespace App\Http\Controllers;

use App\Enums\EstadoVehiculo;
use App\Models\CategoriaPersonal;
use App\Models\CatalogoTipoDocumento;
use App\Models\Documento;
use App\Models\LogAccion;
use App\Models\Personal;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('ver_vehiculos');

        $query = Vehiculo::with(['obras.operador']);

        // Aplicar filtros
        if ($request->filled('buscar')) {
            $termino = $request->get('buscar');
            $query->buscar($termino);
        }

        if ($request->filled('marca')) {
            $query->porMarca($request->get('marca'));
        }

        if ($request->filled('estado')) {
            $query->porEstado($request->get('estado'));
        }

        if ($request->filled('anio')) {
            $query->porAnio($request->get('anio'));
        }

        $vehiculos = $query->orderBy('marca')->orderBy('modelo')->paginate(15);

        // Obtener valores únicos para filtros
        $marcas = Vehiculo::distinct()->pluck('marca')->sort();
        $estados = collect(EstadoVehiculo::cases())->mapWithKeys(function ($estado) {
            return [$estado->value => $estado->nombre()];
        });

        return view('vehiculos.index', compact('vehiculos', 'marcas', 'estados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('crear_vehiculos');

        // Obtener todo el personal activo (cualquiera puede ser operador/responsable)
        $operadores = Personal::activos()->orderBy('nombre_completo')->get();

        return view('vehiculos.create', compact('operadores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('crear_vehiculos');

        $validatedData = $request->validate([
            'marca' => 'required|string|max:50',
            'numero_poliza' => 'nullable|string|max:20',
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'n_serie' => 'required|string|max:100|unique:vehiculos,n_serie',
            'placas' => 'required|string|max:20|unique:vehiculos,placas',
            'kilometraje_actual' => 'required|integer|min:0',
            'intervalo_km_motor' => 'nullable|integer|min:1000',
            'intervalo_km_transmision' => 'nullable|integer|min:5000',
            'intervalo_km_hidraulico' => 'nullable|integer|min:1000',
            'observaciones' => 'nullable|string|max:1000',
            'operador_id' => 'nullable|exists:personal,id',
            
            // Fechas de vencimiento para nuevas columnas
            'poliza_vencimiento' => 'nullable|date',
            'derecho_vencimiento' => 'nullable|date',
            
            // Archivos de documentos (nuevos nombres)
            'poliza_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'derecho_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'factura_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'imagen_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            
            // Archivos de documentos (compatibilidad con nombres anteriores)
            'poliza_seguro_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'derecho_vehicular_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'factura_pedimento_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fotografia_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            
            // Fechas de vencimiento (compatibilidad)
            'fecha_vencimiento_seguro' => 'nullable|date',
            'fecha_vencimiento_derecho' => 'nullable|date',
        ], [
            'marca.required' => 'La marca es obligatoria.',
            'modelo.required' => 'El modelo es obligatorio.',
            'anio.required' => 'El año es obligatorio.',
            'anio.min' => 'El año debe ser mayor a 1990.',
            'anio.max' => 'El año no puede ser mayor al año siguiente.',
            'n_serie.required' => 'El número de serie es obligatorio.',
            'n_serie.unique' => 'Este número de serie ya está registrado.',
            'placas.required' => 'Las placas son obligatorias.',
            'placas.unique' => 'Estas placas ya están registradas.',
            'kilometraje_actual.required' => 'El kilometraje actual es obligatorio.',
            'kilometraje_actual.min' => 'El kilometraje no puede ser negativo.',
            'operador_id.exists' => 'El operador seleccionado no es válido.',
            
            // Validaciones para archivos
            'poliza_file.max' => 'La póliza de seguro no puede ser mayor a 5MB.',
            'derecho_file.max' => 'El derecho vehicular no puede ser mayor a 5MB.',
            'factura_file.max' => 'La factura no puede ser mayor a 5MB.',
            'imagen_file.max' => 'La imagen no puede ser mayor a 5MB.',
            
            // Validaciones para archivos (compatibilidad)
            'poliza_seguro_file.max' => 'La póliza de seguro no puede ser mayor a 5MB.',
            'derecho_vehicular_file.max' => 'El derecho vehicular no puede ser mayor a 5MB.',
            'factura_pedimento_file.max' => 'La factura/pedimento no puede ser mayor a 5MB.',
            'fotografia_file.max' => 'La fotografía no puede ser mayor a 5MB.',
        ]);

        DB::beginTransaction();

        try {
            // Crear el vehículo con estado inicial DISPONIBLE (sin las URLs primero)
            $vehiculo = Vehiculo::create([
                'marca' => $validatedData['marca'],
                'numero_poliza' => $validatedData['numero_poliza'] ?? null,
                'modelo' => $validatedData['modelo'],
                'anio' => $validatedData['anio'],
                'n_serie' => $validatedData['n_serie'],
                'placas' => $validatedData['placas'],
                'estatus' => EstadoVehiculo::DISPONIBLE->value, // Usar 'estatus' en lugar de 'estado'
                'kilometraje_actual' => $validatedData['kilometraje_actual'],
                'intervalo_km_motor' => $validatedData['intervalo_km_motor'],
                'intervalo_km_transmision' => $validatedData['intervalo_km_transmision'],
                'intervalo_km_hidraulico' => $validatedData['intervalo_km_hidraulico'],
                'observaciones' => $validatedData['observaciones'],
                'operador_id' => $validatedData['operador_id'], // Agregar el operador_id
                
                // Fechas de vencimiento
                'poliza_vencimiento' => $validatedData['poliza_vencimiento'] ?? $validatedData['fecha_vencimiento_seguro'] ?? null,
                'derecho_vencimiento' => $validatedData['derecho_vencimiento'] ?? $validatedData['fecha_vencimiento_derecho'] ?? null,
            ]);

            // Procesar archivos usando el mismo sistema descriptivo que personal
            $urlsGeneradas = [];
            $documentosCreados = [];

            // Mapeo de archivos con sus respectivas columnas URL, descripción y tipo de documento
            $archivosMapping = [
                'poliza_file' => [
                    'url' => 'poliza_url', 
                    'descripcion' => $validatedData['placas'],
                    'tipo_documento_nombre' => 'Póliza de Seguro'
                ],
                'poliza_seguro_file' => [
                    'url' => 'poliza_url', 
                    'descripcion' => $validatedData['placas'],
                    'tipo_documento_nombre' => 'Póliza de Seguro'
                ], // compatibilidad
                'derecho_file' => [
                    'url' => 'derecho_url', 
                    'descripcion' => $validatedData['placas'],
                    'tipo_documento_nombre' => 'Derecho Vehicular'
                ],
                'derecho_vehicular_file' => [
                    'url' => 'derecho_url', 
                    'descripcion' => $validatedData['placas'],
                    'tipo_documento_nombre' => 'Derecho Vehicular'
                ], // compatibilidad
                'factura_file' => [
                    'url' => 'factura_url', 
                    'descripcion' => $validatedData['n_serie'],
                    'tipo_documento_nombre' => 'Factura'
                ],
                'factura_pedimento_file' => [
                    'url' => 'factura_url', 
                    'descripcion' => $validatedData['n_serie'],
                    'tipo_documento_nombre' => 'Factura'
                ], // compatibilidad
                'imagen_file' => [
                    'url' => 'url_imagen', 
                    'descripcion' => $validatedData['marca'] . $validatedData['modelo'],
                    'tipo_documento_nombre' => 'Fotografía Vehículo'
                ],
                'fotografia_file' => [
                    'url' => 'url_imagen', 
                    'descripcion' => $validatedData['marca'] . $validatedData['modelo'],
                    'tipo_documento_nombre' => 'Fotografía Vehículo'
                ], // compatibilidad
            ];

            // Procesar TODOS los archivos que se suban (remover break)
            foreach ($archivosMapping as $campoArchivo => $config) {
                if ($request->hasFile($campoArchivo)) {
                    $archivo = $request->file($campoArchivo);
                    $descripcion = $config['descripcion'];
                    
                    // Verificar que no se procese el mismo tipo de archivo dos veces
                    // (para evitar duplicados con compatibilidad)
                    if (isset($urlsGeneradas[$config['url']])) {
                        continue; // Ya procesamos este tipo de archivo
                    }
                    
                    // Usar el método handleDocumentUpload para naming descriptivo
                    $rutaArchivo = $this->handleDocumentUpload($archivo, $vehiculo->id, $campoArchivo, $descripcion);
                    
                    // Generar URL para acceso público
                    $urlPublica = Storage::url($rutaArchivo);
                    
                    // Guardar URL en el array para actualizar después
                    $urlsGeneradas[$config['url']] = $urlPublica;
                    
                    // Crear registro en tabla documentos
                    $tipoDocumento = $this->getOrCreateTipoDocumento($config['tipo_documento_nombre']);
                    
                    $documento = Documento::create([
                        'vehiculo_id' => $vehiculo->id,
                        'tipo_documento_id' => $tipoDocumento->id,
                        'descripcion' => $config['tipo_documento_nombre'] . ' - ' . $descripcion,
                        'ruta_archivo' => $rutaArchivo,
                        'fecha_vencimiento' => $this->getFechaVencimiento($campoArchivo, $validatedData),
                    ]);
                    
                    $documentosCreados[] = $documento;
                    
                    \Log::info("Vehicle document created", [
                        'vehiculo_id' => $vehiculo->id,
                        'documento_id' => $documento->id,
                        'tipo' => $config['tipo_documento_nombre'],
                        'archivo' => basename($rutaArchivo)
                    ]);
                    
                    // NO usar break aquí - procesar todos los archivos
                }
            }

            // Actualizar el vehículo con las URLs generadas
            if (!empty($urlsGeneradas)) {
                $vehiculo->update($urlsGeneradas);
            }

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear',
                'tabla' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'datos_anteriores' => null,
                'datos_nuevos' => $vehiculo->fresh()->toArray(),
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Vehículo creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log del error técnico para debugging
            \App\Services\UserFriendlyErrorService::logTechnicalError($e, 'VehiculoController@store');

            // Mensaje amigable para el usuario
            $userMessage = \App\Services\UserFriendlyErrorService::getOperationMessage('crear_vehiculo', $e);
            
            return redirect()->back()
                ->withInput()
                ->with('error', $userMessage);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehiculo $vehiculo)
    {
        $this->authorize('ver_vehiculos');

        // Cargar las relaciones a través del nuevo sistema de asignaciones
        $vehiculo->load([
            'asignacionesObra.obra.operador', 
            'asignacionesObra.obra.encargado', 
            'asignacionesObra.operador', 
            'kilometrajes', 
            'mantenimientos', 
            'documentos'
        ]);

        return view('vehiculos.show', compact('vehiculo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehiculo $vehiculo)
    {
        $this->authorize('editar_vehiculos');

        // Obtener todo el personal activo (cualquiera puede ser operador/responsable)
        $operadores = Personal::activos()->orderBy('nombre_completo')->get();
        $estados = EstadoVehiculo::cases();
        $tiposDocumento = \App\Models\CatalogoTipoDocumento::all();

        return view('vehiculos.edit', compact('vehiculo', 'operadores', 'estados', 'tiposDocumento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        $this->authorize('editar_vehiculos');

        $validatedData = $request->validate([
            'marca' => 'required|string|max:50',
            'numero_poliza' => 'nullable|string|max:20',
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'n_serie' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vehiculos', 'n_serie')->ignore($vehiculo->id)
            ],
            'placas' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehiculos', 'placas')->ignore($vehiculo->id)
            ],
            // Removido: 'estatus' ya no es requerido - tiene valor por defecto
            'kilometraje_actual' => 'required|integer|min:0',
            'intervalo_km_motor' => 'nullable|integer|min:1000',
            'intervalo_km_transmision' => 'nullable|integer|min:5000',
            'intervalo_km_hidraulico' => 'nullable|integer|min:1000',
            'observaciones' => 'nullable|string|max:1000',
            
            // Fechas de vencimiento para nuevas columnas
            'poliza_vencimiento' => 'nullable|date',
            'derecho_vencimiento' => 'nullable|date',
            
            // Archivos de documentos (nuevos nombres)
            'poliza_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'derecho_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'factura_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'imagen_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            
            // Archivos de documentos (compatibilidad con nombres anteriores)
            'poliza_seguro_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'derecho_vehicular_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'factura_pedimento_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fotografia_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            
            // Fechas de vencimiento (compatibilidad)
            'fecha_vencimiento_seguro' => 'nullable|date',
            'fecha_vencimiento_derecho' => 'nullable|date',
        ], [
            'marca.required' => 'La marca es obligatoria.',
            'modelo.required' => 'El modelo es obligatorio.',
            'anio.required' => 'El año es obligatorio.',
            'anio.min' => 'El año debe ser mayor a 1990.',
            'anio.max' => 'El año no puede ser mayor al año siguiente.',
            'n_serie.required' => 'El número de serie es obligatorio.',
            'n_serie.unique' => 'Este número de serie ya está registrado.',
            'placas.required' => 'Las placas son obligatorias.',
            'placas.unique' => 'Estas placas ya están registradas.',
            'kilometraje_actual.required' => 'El kilometraje actual es obligatorio.',
            'kilometraje_actual.min' => 'El kilometraje no puede ser negativo.',
            
            // Validaciones para archivos
            'poliza_file.max' => 'La póliza de seguro no puede ser mayor a 5MB.',
            'derecho_file.max' => 'El derecho vehicular no puede ser mayor a 5MB.',
            'factura_file.max' => 'La factura no puede ser mayor a 5MB.',
            'imagen_file.max' => 'La imagen no puede ser mayor a 5MB.',
            
            // Validaciones para archivos (compatibilidad)
            'poliza_seguro_file.max' => 'La póliza de seguro no puede ser mayor a 5MB.',
            'derecho_vehicular_file.max' => 'El derecho vehicular no puede ser mayor a 5MB.',
            'factura_pedimento_file.max' => 'La factura/pedimento no puede ser mayor a 5MB.',
            'fotografia_file.max' => 'La fotografía no puede ser mayor a 5MB.',
        ]);

        DB::beginTransaction();

        try {
            $datosAnteriores = $vehiculo->toArray();

            // Preparar datos para actualizar (sin archivos)
            $datosActualizar = [
                'marca' => $validatedData['marca'],
                'numero_poliza' => $validatedData['numero_poliza'] ?? null,
                'modelo' => $validatedData['modelo'],
                'anio' => $validatedData['anio'],
                'n_serie' => $validatedData['n_serie'],
                'placas' => $validatedData['placas'],
                'kilometraje_actual' => $validatedData['kilometraje_actual'],
                'intervalo_km_motor' => $validatedData['intervalo_km_motor'],
                'intervalo_km_transmision' => $validatedData['intervalo_km_transmision'],
                'intervalo_km_hidraulico' => $validatedData['intervalo_km_hidraulico'],
                'observaciones' => $validatedData['observaciones'],
                
                // Fechas de vencimiento
                'poliza_vencimiento' => $validatedData['poliza_vencimiento'] ?? $validatedData['fecha_vencimiento_seguro'] ?? null,
                'derecho_vencimiento' => $validatedData['derecho_vencimiento'] ?? $validatedData['fecha_vencimiento_derecho'] ?? null,
            ];

            // Procesar archivos con naming descriptivo como en PersonalManagementController
            $archivosMapping = [
                'poliza_file' => [
                    'url' => 'poliza_url', 
                    'descripcion' => $validatedData['placas'],
                    'tipo_documento_nombre' => 'Póliza de Seguro'
                ],
                'poliza_seguro_file' => [
                    'url' => 'poliza_url', 
                    'descripcion' => $validatedData['placas'],
                    'tipo_documento_nombre' => 'Póliza de Seguro'
                ], // compatibilidad
                'derecho_file' => [
                    'url' => 'derecho_url', 
                    'descripcion' => $validatedData['placas'],
                    'tipo_documento_nombre' => 'Derecho Vehicular'
                ],
                'derecho_vehicular_file' => [
                    'url' => 'derecho_url', 
                    'descripcion' => $validatedData['placas'],
                    'tipo_documento_nombre' => 'Derecho Vehicular'
                ], // compatibilidad
                'factura_file' => [
                    'url' => 'factura_url', 
                    'descripcion' => $validatedData['n_serie'],
                    'tipo_documento_nombre' => 'Factura'
                ],
                'factura_pedimento_file' => [
                    'url' => 'factura_url', 
                    'descripcion' => $validatedData['n_serie'],
                    'tipo_documento_nombre' => 'Factura'
                ], // compatibilidad
                'imagen_file' => [
                    'url' => 'url_imagen', 
                    'descripcion' => $validatedData['marca'] . $validatedData['modelo'],
                    'tipo_documento_nombre' => 'Fotografía Vehículo'
                ],
                'fotografia_file' => [
                    'url' => 'url_imagen', 
                    'descripcion' => $validatedData['marca'] . $validatedData['modelo'],
                    'tipo_documento_nombre' => 'Fotografía Vehículo'
                ], // compatibilidad
            ];

            $archivosActualizados = [];
            foreach ($archivosMapping as $campoArchivo => $config) {
                if ($request->hasFile($campoArchivo)) {
                    
                    // Verificar que no se procese el mismo tipo de archivo dos veces
                    if (isset($archivosActualizados[$config['url']])) {
                        continue; // Ya procesamos este tipo de archivo
                    }
                    
                    // Eliminar archivo anterior si existe
                    $urlAnterior = $vehiculo->{$config['url']};
                    if ($urlAnterior) {
                        $rutaAnterior = str_replace('/storage/', '', $urlAnterior);
                        if (Storage::disk('public')->exists($rutaAnterior)) {
                            Storage::disk('public')->delete($rutaAnterior);
                        }
                    }
                    
                    $archivo = $request->file($campoArchivo);
                    $descripcion = $config['descripcion'];
                    
                    // Usar el método handleDocumentUpload para naming descriptivo
                    $rutaArchivo = $this->handleDocumentUpload($archivo, $vehiculo->id, $campoArchivo, $descripcion);
                    
                    // Generar URL para acceso público
                    $urlPublica = Storage::url($rutaArchivo);
                    
                    // Agregar URL al array de datos a actualizar
                    $datosActualizar[$config['url']] = $urlPublica;
                    $archivosActualizados[$config['url']] = true;
                    
                    // Eliminar documento anterior de la tabla documentos y crear nuevo
                    Documento::where('vehiculo_id', $vehiculo->id)
                            ->whereHas('tipoDocumento', function($query) use ($config) {
                                $query->where('nombre_tipo_documento', $config['tipo_documento_nombre']);
                            })
                            ->delete();
                    
                    // Crear nuevo registro en tabla documentos
                    $tipoDocumento = $this->getOrCreateTipoDocumento($config['tipo_documento_nombre']);
                    
                    $documento = Documento::create([
                        'vehiculo_id' => $vehiculo->id,
                        'tipo_documento_id' => $tipoDocumento->id,
                        'descripcion' => $config['tipo_documento_nombre'] . ' - ' . $descripcion,
                        'ruta_archivo' => $rutaArchivo,
                        'fecha_vencimiento' => $this->getFechaVencimiento($campoArchivo, $validatedData),
                    ]);
                    
                    \Log::info("Vehicle document updated", [
                        'vehiculo_id' => $vehiculo->id,
                        'documento_id' => $documento->id,
                        'tipo' => $config['tipo_documento_nombre'],
                        'archivo' => basename($rutaArchivo)
                    ]);
                    
                    // NO usar break aquí - procesar todos los archivos
                }
            }

            // Actualizar el vehículo con todos los datos
            $vehiculo->update($datosActualizar);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar',
                'tabla' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $vehiculo->fresh()->toArray(),
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Vehículo actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log del error técnico para debugging
            \App\Services\UserFriendlyErrorService::logTechnicalError($e, 'VehiculoController@update');

            // Mensaje amigable para el usuario
            $userMessage = \App\Services\UserFriendlyErrorService::getOperationMessage('actualizar_vehiculo', $e);
            
            return redirect()->back()
                ->withInput()
                ->with('error', $userMessage);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehiculo $vehiculo)
    {
        $this->authorize('eliminar_vehiculos');

        // Verificar que no tenga obras activas
        if ($vehiculo->tieneObraActual()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar un vehículo que tiene obras activas.');
        }

        DB::beginTransaction();

        try {
            $datosAnteriores = $vehiculo->toArray();

            $vehiculo->delete(); // Soft delete

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar',
                'tabla' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => null,
                'fecha_hora' => now(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.index')
                ->with('success', 'Vehículo eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el vehículo: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted vehicle.
     */
    public function restore($id)
    {
        $this->authorize('restaurar_vehiculos');

        $vehiculo = Vehiculo::withTrashed()->findOrFail($id);

        DB::beginTransaction();

        try {
            $vehiculo->restore();

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'restaurar',
                'tabla' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'datos_anteriores' => null,
                'datos_nuevos' => $vehiculo->fresh()->toArray(),
                'fecha_hora' => now(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Vehículo restaurado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al restaurar el vehículo: ' . $e->getMessage());
        }
    }

    /**
     * Get status options for API
     */
    public function estatusOptions()
    {
        return response()->json([
            'success' => true,
            'data' => collect(EstadoVehiculo::cases())->map(function ($estado) {
                return [
                    'value' => $estado->value,
                    'label' => $estado->getLabel()
                ];
            })
        ]);
    }

    // ================== MÉTODOS DE KILOMETRAJE ==================

    /**
     * Display a listing of kilometrajes for a specific vehicle.
     */
    public function kilometrajes(Request $request, Vehiculo $vehiculo)
    {
        $this->authorize('ver_vehiculos');

        $query = $vehiculo->kilometrajes()->with(['usuarioCaptura']);

        // Aplicar filtros
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_captura', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_captura', '<=', $request->get('fecha_hasta'));
        }

        $kilometrajes = $query->orderBy('fecha_captura', 'desc')->paginate(15);

        return view('vehiculos.kilometrajes.index', compact('vehiculo', 'kilometrajes'));
    }

    /**
     * Show the form for creating a new kilometraje for a vehicle.
     */
    public function createKilometraje(Vehiculo $vehiculo)
    {
        $this->authorize('crear_kilometrajes');

        return view('vehiculos.kilometrajes.create', compact('vehiculo'));
    }

    /**
     * Store a newly created kilometraje for a vehicle.
     */
    public function storeKilometraje(Request $request, Vehiculo $vehiculo)
    {
        $this->authorize('crear_kilometrajes');

        $validatedData = $request->validate([
            'kilometraje' => [
                'required',
                'integer',
                'min:0',
                'gt:' . ($vehiculo->kilometraje_actual ?? 0)
            ],
            'fecha_captura' => 'required|date|before_or_equal:today',
            'observaciones' => 'nullable|string|max:1000',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ], [
            'kilometraje.required' => 'El kilometraje es obligatorio.',
            'kilometraje.gt' => 'El kilometraje debe ser mayor al actual (' . number_format($vehiculo->kilometraje_actual ?? 0) . ').',
            'fecha_captura.required' => 'La fecha de captura es obligatoria.',
            'fecha_captura.before_or_equal' => 'La fecha no puede ser futura.',
        ]);

        DB::beginTransaction();

        try {
            // Procesar imagen si existe
            $rutaImagen = null;
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_km_' . $vehiculo->id . '.' . $imagen->getClientOriginalExtension();
                $rutaImagen = $imagen->storeAs('kilometrajes', $nombreImagen, 'public');
            }

            // Obtener la obra actual del vehículo para asignarla automáticamente
            $obraActual = $vehiculo->obraActual()->first();

            // Crear el registro de kilometraje
            $kilometraje = $vehiculo->kilometrajes()->create([
                'kilometraje' => $validatedData['kilometraje'],
                'fecha_captura' => $validatedData['fecha_captura'],
                'observaciones' => $validatedData['observaciones'],
                'imagen' => $rutaImagen,
                'usuario_captura_id' => Auth::id(),
                'obra_id' => $obraActual ? $obraActual->id : null,
            ]);

            // Actualizar el kilometraje actual del vehículo
            $vehiculo->update(['kilometraje_actual' => $validatedData['kilometraje']]);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_kilometraje',
                'tabla' => 'kilometrajes',
                'registro_id' => $kilometraje->id,
                'datos_anteriores' => null,
                'datos_nuevos' => $kilometraje->toArray(),
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Kilometraje registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar el kilometraje: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified kilometraje.
     */
    public function showKilometraje(Vehiculo $vehiculo, $kilometrajeId)
    {
        $this->authorize('ver_vehiculos');

        $kilometraje = $vehiculo->kilometrajes()
            ->with(['usuarioCaptura.personal'])
            ->findOrFail($kilometrajeId);

        return view('vehiculos.kilometrajes.show', compact('vehiculo', 'kilometraje'));
    }

    /**
     * Show the form for editing the specified kilometraje.
     */
    public function editKilometraje(Vehiculo $vehiculo, $kilometrajeId)
    {
        $this->authorize('editar_kilometrajes');

        $kilometraje = $vehiculo->kilometrajes()->findOrFail($kilometrajeId);

        return view('vehiculos.kilometrajes.edit', compact('vehiculo', 'kilometraje'));
    }

    /**
     * Update the specified kilometraje.
     */
    public function updateKilometraje(Request $request, Vehiculo $vehiculo, $kilometrajeId)
    {
        $this->authorize('editar_kilometrajes');

        $kilometraje = $vehiculo->kilometrajes()->findOrFail($kilometrajeId);

        $validatedData = $request->validate([
            'fecha_captura' => 'required|date|before_or_equal:today',
            'observaciones' => 'nullable|string|max:1000',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ], [
            'fecha_captura.required' => 'La fecha de captura es obligatoria.',
            'fecha_captura.before_or_equal' => 'La fecha no puede ser futura.',
        ]);

        DB::beginTransaction();

        try {
            $datosAnteriores = $kilometraje->toArray();

            // Procesar nueva imagen si existe
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($kilometraje->imagen && Storage::disk('public')->exists($kilometraje->imagen)) {
                    Storage::disk('public')->delete($kilometraje->imagen);
                }

                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_km_' . $vehiculo->id . '.' . $imagen->getClientOriginalExtension();
                $validatedData['imagen'] = $imagen->storeAs('kilometrajes', $nombreImagen, 'public');
            }

            $kilometraje->update($validatedData);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_kilometraje',
                'tabla' => 'kilometrajes',
                'registro_id' => $kilometraje->id,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $kilometraje->fresh()->toArray(),
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.kilometrajes.show', [$vehiculo->id, $kilometraje->id])
                ->with('success', 'Kilometraje actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el kilometraje: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified kilometraje.
     */
    public function destroyKilometraje(Request $request, Vehiculo $vehiculo, $kilometrajeId)
    {
        $this->authorize('eliminar_kilometrajes');

        $kilometraje = $vehiculo->kilometrajes()->findOrFail($kilometrajeId);

        DB::beginTransaction();

        try {
            $datosAnteriores = $kilometraje->toArray();

            // Eliminar imagen si existe
            if ($kilometraje->imagen && Storage::disk('public')->exists($kilometraje->imagen)) {
                Storage::disk('public')->delete($kilometraje->imagen);
            }

            $kilometraje->delete();

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_kilometraje',
                'tabla' => 'kilometrajes',
                'registro_id' => $kilometrajeId,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => null,
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Kilometraje eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el kilometraje: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar el operador asignado a un vehículo
     */
    public function cambiarOperador(Request $request, Vehiculo $vehiculo)
    {
        $this->authorize('editar_vehiculos');

        // Validar la solicitud
        $request->validate([
            'operador_id' => 'required|exists:personal,id',
            'observaciones' => 'nullable|string|max:500'
        ]);

        // Verificar que el operador esté activo (cualquier personal puede ser operador)
        $nuevoOperador = Personal::where('id', $request->operador_id)
            ->where('estatus', 'activo')
            ->first();

        if (!$nuevoOperador) {
            return response()->json([
                'success' => false,
                'error' => 'El personal seleccionado no es válido o no está activo.'
            ], 400);
        }

        // Verificar que no sea el mismo operador actual
        if ($vehiculo->operador_id == $request->operador_id) {
            return response()->json([
                'success' => false,
                'error' => 'El operador seleccionado ya está asignado a este vehículo.'
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            $operadorAnterior = $vehiculo->operador;
            
            // Actualizar el operador del vehículo
            $vehiculo->update([
                'operador_id' => $request->operador_id
            ]);

            // Registrar la acción en los logs
            $mensaje = $operadorAnterior 
                ? "Operador cambiado de {$operadorAnterior->nombre_completo} a {$nuevoOperador->nombre_completo}"
                : "Operador asignado: {$nuevoOperador->nombre_completo}";
            
            if ($request->observaciones) {
                $mensaje .= ". Observaciones: {$request->observaciones}";
            }

            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => $operadorAnterior ? 'cambio_operador_vehiculo' : 'asignacion_operador_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => [
                    'mensaje' => $mensaje,
                    'operador_anterior' => $operadorAnterior ? [
                        'id' => $operadorAnterior->id,
                        'nombre' => $operadorAnterior->nombre_completo
                    ] : null,
                    'operador_nuevo' => [
                        'id' => $nuevoOperador->id,
                        'nombre' => $nuevoOperador->nombre_completo
                    ],
                    'observaciones' => $request->observaciones
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $operadorAnterior 
                    ? 'Operador cambiado exitosamente' 
                    : 'Operador asignado exitosamente',
                'redirect' => route('vehiculos.show', $vehiculo)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => 'Error al cambiar el operador: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manejar la subida de documentos con naming descriptivo (igual que PersonalManagementController)
     */
    private function handleDocumentUpload(\Illuminate\Http\UploadedFile $file, int|string $vehiculoId, string $fileType = null, string $descripcion = null): string
    {
        // Generar nombre de archivo con formato: ID_TIPODOCUMENTO_DESCRIPCION.extension
        $extension = $file->getClientOriginalExtension();
        
        // Mapear tipos de archivo a nombres legibles
        $tipoDocumentoNombres = [
            'poliza_file' => 'POLIZA',
            'poliza_seguro_file' => 'POLIZA',
            'derecho_file' => 'DERECHO',
            'derecho_vehicular_file' => 'DERECHO',
            'factura_file' => 'FACTURA',
            'factura_pedimento_file' => 'FACTURA',
            'imagen_file' => 'IMAGEN',
            'fotografia_file' => 'IMAGEN'
        ];
        
        $tipoNombre = $tipoDocumentoNombres[$fileType] ?? 'DOCUMENTO';
        
        // Limpiar la descripción: solo caracteres alfanuméricos, máximo 15 caracteres
        $descripcionLimpia = '';
        if ($descripcion) {
            $descripcionLimpia = preg_replace('/[^A-Za-z0-9]/', '', $descripcion);
            $descripcionLimpia = substr($descripcionLimpia, 0, 15);
        }
        
        // Construir nombre: ID_TIPO_DESCRIPCION.extension
        $nombreArchivo = $vehiculoId . '_' . $tipoNombre;
        if (!empty($descripcionLimpia)) {
            $nombreArchivo .= '_' . $descripcionLimpia;
        }
        $nombreArchivo .= '.' . $extension;
        
        \Log::info("Generated vehicle filename: {$nombreArchivo} for fileType: {$fileType}, vehiculoId: {$vehiculoId}");
        
        // Determinar carpeta según tipo de archivo
        $carpeta = str_contains($tipoNombre, 'IMAGEN') ? 'vehiculos/imagenes' : 'vehiculos/documentos';
        
        return $file->storeAs($carpeta, $nombreArchivo, 'public');
    }

    /**
     * Obtener o crear tipo de documento en el catálogo
     */
    private function getOrCreateTipoDocumento(string $nombre): CatalogoTipoDocumento
    {
        return CatalogoTipoDocumento::firstOrCreate(
            ['nombre_tipo_documento' => $nombre],
            [
                'descripcion' => 'Tipo de documento para vehículos: ' . $nombre,
                'requiere_vencimiento' => in_array($nombre, ['Póliza de Seguro', 'Derecho Vehicular'])
            ]
        );
    }

    /**
     * Obtener fecha de vencimiento según el tipo de documento
     */
    private function getFechaVencimiento(string $campoArchivo, array $validatedData): ?string
    {
        if (str_contains($campoArchivo, 'poliza')) {
            return $validatedData['poliza_vencimiento'] ?? $validatedData['fecha_vencimiento_seguro'] ?? null;
        }
        
        if (str_contains($campoArchivo, 'derecho')) {
            return $validatedData['derecho_vencimiento'] ?? $validatedData['fecha_vencimiento_derecho'] ?? null;
        }
        
        return null; // Facturas e imágenes normalmente no tienen vencimiento
    }
}