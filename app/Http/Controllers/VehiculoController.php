<?php

namespace App\Http\Controllers;

use App\Enums\EstadoVehiculo;
use App\Http\Requests\StoreVehiculoRequest;
use App\Http\Requests\UpdateVehiculoRequest;
use App\Models\CategoriaPersonal;
use App\Models\CatalogoTipoDocumento;
use App\Models\Documento;
use App\Models\HistorialOperadorVehiculo;
use App\Models\LogAccion;
use App\Models\Personal;
use App\Models\TipoActivo;
use App\Models\Vehiculo;
use App\Exports\VehiculosFiltradosExport;
use App\Traits\PdfGeneratorTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class VehiculoController extends Controller
{
    use PdfGeneratorTrait;

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

        if ($request->filled('estado')) {
            $query->porEstado($request->get('estado'));
        }

        if ($request->filled('anio')) {
            $query->porAnio($request->get('anio'));
        }

        $vehiculos = $query->orderBy('id')->paginate(15);

        // Obtener valores únicos para filtros
        $estados = collect(EstadoVehiculo::cases())->mapWithKeys(function ($estado) {
            return [$estado->value => $estado->nombre()];
        });

        return view('vehiculos.index', compact('vehiculos', 'estados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('crear_vehiculos');

        // Obtener todo el personal activo (cualquiera puede ser operador/responsable)
        $operadores = Personal::activos()->orderBy('nombre_completo')->get();
        
        // Obtener todos los tipos de activo
        $tiposActivo = TipoActivo::orderBy('nombre')->get();

        return view('vehiculos.create', compact('operadores', 'tiposActivo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehiculoRequest $request)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
            // Crear el vehículo con estado inicial DISPONIBLE automáticamente
            $vehiculo = Vehiculo::create([
                'tipo_activo_id' => $validatedData['tipo_activo_id'],
                'marca' => $validatedData['marca'],
                'numero_poliza' => $validatedData['numero_poliza'] ?? null,
                'modelo' => $validatedData['modelo'],
                'anio' => $validatedData['anio'],
                'n_serie' => $validatedData['n_serie'],
                'placas' => $validatedData['placas'],
                'estatus' => EstadoVehiculo::DISPONIBLE->value, // Estatus automático como DISPONIBLE
                'kilometraje_actual' => $validatedData['kilometraje_actual'] ?? null, // Opcional según tipo de activo
                'intervalo_km_motor' => $validatedData['intervalo_km_motor'] ?? null,
                'estado' => $validatedData['estado'] ?? null, // Guardar el estado seleccionado
                'municipio' => $validatedData['municipio'] ?? null, // Guardar el municipio seleccionado
                'intervalo_km_transmision' => $validatedData['intervalo_km_transmision'] ?? null,
                'intervalo_km_hidraulico' => $validatedData['intervalo_km_hidraulico'] ?? null,
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

        } catch (ValidationException $e) {
            DB::rollBack();
            
            // Log del error de validación
            \App\Services\UserFriendlyErrorService::logTechnicalError($e, 'VehiculoController@store - Validation Error');
            
            // Obtener el primer mensaje de validación
            $firstError = collect($e->errors())->flatten()->first();
            
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', $firstError ?? 'Error de validación en los datos del vehículo.');
                
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
        
        // Obtener todos los tipos de activo
        $tiposActivo = TipoActivo::orderBy('nombre')->get();

        return view('vehiculos.edit', compact('vehiculo', 'operadores', 'estados', 'tiposDocumento', 'tiposActivo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehiculoRequest $request, Vehiculo $vehiculo)
    {
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
            $datosAnteriores = $vehiculo->toArray();

            // Preparar datos para actualizar (sin archivos)
            $datosActualizar = [
                'tipo_activo_id' => $validatedData['tipo_activo_id'],
                'marca' => $validatedData['marca'],
                'numero_poliza' => $validatedData['numero_poliza'] ?? null,
                'modelo' => $validatedData['modelo'],
                'anio' => $validatedData['anio'],
                'n_serie' => $validatedData['n_serie'],
                'placas' => $validatedData['placas'],
                'kilometraje_actual' => $validatedData['kilometraje_actual'] ?? null, // Opcional según tipo de activo
                'intervalo_km_motor' => $validatedData['intervalo_km_motor'] ?? null,
                'intervalo_km_transmision' => $validatedData['intervalo_km_transmision'] ?? null,
                'intervalo_km_hidraulico' => $validatedData['intervalo_km_hidraulico'] ?? null,
                'observaciones' => $validatedData['observaciones'],
                'estado' => $validatedData['estado'] ?? null,
                'municipio' => $validatedData['municipio'] ?? null,
                
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            // Obtener el primer mensaje de error de validación
            $firstError = collect($e->errors())->flatten()->first();
            
            return redirect()->back()
                ->withInput()
                ->with('error', $firstError ?? 'Error de validación al actualizar el vehículo.');
                
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
                    'label' => $estado->nombre()
                ];
            })
        ]);
    }

    /**
     * Búsqueda predictiva optimizada de vehículos para el diálogo de acceso rápido
     */
    public function busquedaPredictiva(Request $request)
    {
        // No se requiere autorización para esta función
        // $this->authorize('ver_vehiculos'); // Comentado para permitir acceso a todos los usuarios

        $query = trim($request->get('q', ''));
        $marca = trim($request->get('marca', ''));
        $modelo = trim($request->get('modelo', ''));
        $anioDesde = $request->get('anio_desde', '');
        $anioHasta = $request->get('anio_hasta', '');
        $precioDesde = $request->get('precio_desde', '');
        $precioHasta = $request->get('precio_hasta', '');
        $limit = min($request->get('limit', 10), 50); // Máximo 50 resultados

        // Generar clave de caché basada en parámetros
        $cacheKey = 'vehiculos_search_' . md5(serialize([
            'q' => $query,
            'marca' => $marca,
            'modelo' => $modelo,
            'anio_desde' => $anioDesde,
            'anio_hasta' => $anioHasta,
            'precio_desde' => $precioDesde,
            'precio_hasta' => $precioHasta,
            'limit' => $limit
        ]));

        // Intentar obtener desde caché (5 minutos)
        $resultadosCache = cache()->remember($cacheKey, 300, function() use (
            $query, $marca, $modelo, $anioDesde, $anioHasta, $precioDesde, $precioHasta, $limit
        ) {
            // Optimización: Si la consulta es muy corta, limitar resultados
            if (strlen($query) < 2 && empty($marca) && empty($modelo)) {
                $limit = min($limit, 20);
            }

            $vehiculosQuery = Vehiculo::select([
                'vehiculos.id',
                'vehiculos.marca',
                'vehiculos.modelo',
                'vehiculos.anio',
                'vehiculos.placas',
                'vehiculos.n_serie',
                'vehiculos.kilometraje_actual',
                'vehiculos.estatus'
                // 'precio_compra' // Columna no existe
            ])
            ->join('tipo_activos', 'vehiculos.tipo_activo_id', '=', 'tipo_activos.id')
            ->where('vehiculos.estatus', '!=', 'eliminado')
            ->where('tipo_activos.tiene_kilometraje', true);

            // Optimización: Usar índices compuestos para búsquedas más eficientes
            if (!empty($query)) {
                // Priorizar búsquedas exactas primero (más rápidas)
                $vehiculosQuery->where(function($q) use ($query) {
                    $q->where('vehiculos.placas', 'like', "{$query}%")
                      ->orWhere('vehiculos.n_serie', 'like', "{$query}%")
                      ->orWhere('vehiculos.id', 'like', "{$query}%")
                      ->orWhere('vehiculos.marca', 'like', "{$query}%")
                      ->orWhere('vehiculos.modelo', 'like', "{$query}%")
                      // Búsquedas con LIKE %query% solo si es necesario
                      ->orWhere('vehiculos.marca', 'like', "%{$query}%")
                      ->orWhere('vehiculos.modelo', 'like', "%{$query}%");
                });
            }

            // Filtros específicos optimizados
            if (!empty($marca)) {
                $vehiculosQuery->where('vehiculos.marca', 'like', "{$marca}%");
            }

            if (!empty($modelo)) {
                $vehiculosQuery->where('vehiculos.modelo', 'like', "{$modelo}%");
            }

            if (!empty($anioDesde)) {
                $vehiculosQuery->where('vehiculos.anio', '>=', $anioDesde);
            }

            if (!empty($anioHasta)) {
                $vehiculosQuery->where('vehiculos.anio', '<=', $anioHasta);
            }

            // Filtros de precio deshabilitados - columna precio_compra no existe
            // if (!empty($precioDesde)) {
            //     $vehiculosQuery->where('precio_compra', '>=', $precioDesde);
            // }

            // if (!empty($precioHasta)) {
            //     $vehiculosQuery->where('precio_compra', '<=', $precioHasta);
            // }

            // Optimización: Ordenamiento más eficiente
            if (!empty($query)) {
                $vehiculos = $vehiculosQuery
                    ->orderByRaw("CASE 
                        WHEN vehiculos.placas LIKE ? THEN 1
                        WHEN LOWER(CONCAT(vehiculos.marca, ' ', vehiculos.modelo)) LIKE LOWER(?) THEN 2
                        WHEN LOWER(vehiculos.marca) LIKE LOWER(?) THEN 3
                        WHEN LOWER(vehiculos.modelo) LIKE LOWER(?) THEN 4
                        ELSE 5
                    END", ["{$query}%", "%{$query}%", "{$query}%", "{$query}%"])
                    ->orderBy('vehiculos.marca')
                    ->orderBy('vehiculos.modelo')
                    ->orderBy('vehiculos.anio', 'desc')
                    ->limit($limit)
                    ->get();
            } else {
                $vehiculos = $vehiculosQuery
                    ->orderBy('vehiculos.marca')
                    ->orderBy('vehiculos.modelo')
                    ->orderBy('vehiculos.anio', 'desc')
                    ->limit($limit)
                    ->get();
            }

            return $vehiculos;
        });

        // Formatear resultados para la respuesta
        $resultados = $resultadosCache->map(function ($vehiculo) {
            return [
                'id' => $vehiculo->id,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                'anio' => $vehiculo->anio,
                'placas' => $vehiculo->placas,
                'n_serie' => $vehiculo->n_serie,
                'kilometraje_actual' => $vehiculo->kilometraje_actual ?? 0,
                'estatus' => $vehiculo->estatus,
                'precio_compra' => null, // Columna no existe
                'texto_completo' => "{$vehiculo->marca} {$vehiculo->modelo} ({$vehiculo->placas})",
                'info_adicional' => "ID: {$vehiculo->id} | KM: " . number_format($vehiculo->kilometraje_actual ?? 0) . " | Año: {$vehiculo->anio}"
            ];
        });

        // Obtener opciones para filtros si se solicitan (con caché)
        $filtros = [];
        if ($request->get('include_filters', false)) {
            $filtros = cache()->remember('vehiculos_filtros', 1800, function() { // 30 minutos
                return [
                    'marcas' => Vehiculo::where('estatus', '!=', 'eliminado')
                        ->distinct()
                        ->pluck('marca')
                        ->filter()
                        ->sort()
                        ->values(),
                    'modelos' => Vehiculo::where('estatus', '!=', 'eliminado')
                        ->distinct()
                        ->pluck('modelo')
                        ->filter()
                        ->sort()
                        ->values(),
                    'anios' => Vehiculo::where('estatus', '!=', 'eliminado')
                        ->distinct()
                        ->pluck('anio')
                        ->filter()
                        ->sortDesc()
                        ->values(),
                    'rango_precios' => [
                        'min' => Vehiculo::where('estatus', '!=', 'eliminado')->min('precio_compra') ?? 0,
                        'max' => Vehiculo::where('estatus', '!=', 'eliminado')->max('precio_compra') ?? 0
                    ]
                ];
            });
        }

        return response()->json([
            'success' => true,
            'data' => $resultados,
            'total' => $resultados->count(),
            'query' => $query,
            'filtros' => $filtros
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
            'cantidad_combustible' => 'nullable|numeric|min:0|max:9999.99',
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
                'cantidad_combustible' => $validatedData['cantidad_combustible'] ?? null,
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
            
            // Obtener la obra actual del vehículo (si tiene una)
            $asignacionActiva = $vehiculo->asignacionesObraActivas()->first();
            $obraActual = $asignacionActiva?->obra;
            
            // Actualizar el operador del vehículo
            $vehiculo->update([
                'operador_id' => $request->operador_id
            ]);
            
            // Actualizar la asignación de obra si existe
            if ($asignacionActiva) {
                $asignacionActiva->update([
                    'operador_id' => $request->operador_id
                ]);
            }

            // Determinar el tipo de movimiento
            $tipoMovimiento = $operadorAnterior 
                ? HistorialOperadorVehiculo::TIPO_CAMBIO_OPERADOR 
                : HistorialOperadorVehiculo::TIPO_ASIGNACION_INICIAL;

            // Registrar en el historial de operadores CON LA OBRA
            HistorialOperadorVehiculo::registrarMovimiento(
                vehiculoId: $vehiculo->id,
                operadorAnteriorId: $operadorAnterior?->id,
                operadorNuevoId: $nuevoOperador->id,
                usuarioAsignoId: Auth::id(),
                tipoMovimiento: $tipoMovimiento,
                obraId: $obraActual?->id, // ← NUEVA LÍNEA: Incluir obra actual
                observaciones: $request->observaciones,
                motivo: $request->motivo ?? 'Cambio manual de operador'
            );

            // Registrar la acción en los logs
            $mensaje = $operadorAnterior 
                ? "Operador cambiado de {$operadorAnterior->nombre_completo} a {$nuevoOperador->nombre_completo}"
                : "Operador asignado: {$nuevoOperador->nombre_completo}";
            
            // Incluir información de obra si existe
            if ($obraActual) {
                $mensaje .= " para obra: {$obraActual->nombre_obra}";
            }
            
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
                    'obra_actual' => $obraActual ? [
                        'id' => $obraActual->id,
                        'nombre' => $obraActual->nombre_obra
                    ] : null,
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
     * Remover el operador asignado a un vehículo
     */
    public function removerOperador(Request $request, Vehiculo $vehiculo)
    {
        $this->authorize('editar_vehiculos');

        // Validar la solicitud
        $request->validate([
            'observaciones' => 'nullable|string|max:500',
            'motivo' => 'nullable|string|max:255'
        ]);

        // Verificar que el vehículo tenga un operador asignado
        if (!$vehiculo->operador_id) {
            return response()->json([
                'success' => false,
                'error' => 'Este vehículo no tiene un operador asignado.'
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            $operadorAnterior = $vehiculo->operador;
            
            // Remover el operador del vehículo
            $vehiculo->update([
                'operador_id' => null
            ]);

            // Registrar en el historial de operadores
            HistorialOperadorVehiculo::registrarMovimiento(
                vehiculoId: $vehiculo->id,
                operadorAnteriorId: $operadorAnterior->id,
                operadorNuevoId: null,
                usuarioAsignoId: Auth::id(),
                tipoMovimiento: HistorialOperadorVehiculo::TIPO_REMOCION_OPERADOR,
                observaciones: $request->observaciones,
                motivo: $request->motivo ?? 'Remoción manual de operador'
            );

            // Registrar la acción en los logs
            $mensaje = "Operador removido: {$operadorAnterior->nombre_completo}";
            
            if ($request->observaciones) {
                $mensaje .= ". Observaciones: {$request->observaciones}";
            }

            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'remocion_operador_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => [
                    'mensaje' => $mensaje,
                    'operador_removido' => [
                        'id' => $operadorAnterior->id,
                        'nombre' => $operadorAnterior->nombre_completo
                    ],
                    'observaciones' => $request->observaciones,
                    'motivo' => $request->motivo
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Operador removido exitosamente',
                'redirect' => route('vehiculos.show', $vehiculo)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => 'Error al remover el operador: ' . $e->getMessage()
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

    /**
     * Descargar reporte de vehículos filtrados en formato PDF
     */
    public function descargarReportePdf(Request $request)
    {
        $this->authorize('ver_vehiculos');

        // Aplicar los mismos filtros que en el index con optimizaciones para grandes volúmenes
        $query = Vehiculo::select([
            'id', 'marca', 'modelo', 'anio', 'placas', 'n_serie', 
            'estado', 'municipio', 'estatus', 'kilometraje_actual', 'tipo_activo_id', 'created_at'
        ])->with(['tipoActivo:id,nombre']);

        // Aplicar filtros
        if ($request->filled('buscar')) {
            $termino = $request->get('buscar');
            $query->buscar($termino);
        }

        if ($request->filled('estado')) {
            $query->porEstado($request->get('estado'));
        }

        if ($request->filled('anio')) {
            $query->porAnio($request->get('anio'));
        }

        // Obtener vehículos con límite para evitar problemas de memoria
        $vehiculos = $query->orderBy('marca')->orderBy('modelo')->limit(5000)->get();

        // Preparar estadísticas de manera eficiente
        $estadisticas = [
            'total' => $vehiculos->count(),
            'por_estado' => [
                'disponible' => $vehiculos->where('estatus', EstadoVehiculo::DISPONIBLE)->count(),
                'asignado' => $vehiculos->where('estatus', EstadoVehiculo::ASIGNADO)->count(),
                'mantenimiento' => $vehiculos->where('estatus', EstadoVehiculo::EN_MANTENIMIENTO)->count(),
                'fuera_servicio' => $vehiculos->where('estatus', EstadoVehiculo::FUERA_DE_SERVICIO)->count(),
                'baja' => $vehiculos->where('estatus', EstadoVehiculo::BAJA)->count(),
            ],
        ];

        // Calcular estadísticas de kilometraje solo si hay vehículos con kilometraje
        $vehiculosConKm = $vehiculos->whereNotNull('kilometraje_actual');
        if ($vehiculosConKm->count() > 0) {
            $estadisticas['kilometraje_promedio'] = $vehiculosConKm->avg('kilometraje_actual');
            $estadisticas['vehiculos_con_kilometraje'] = $vehiculosConKm->count();
        }

        // Preparar filtros aplicados para mostrar en el reporte
        $filtrosAplicados = [
            'buscar' => $request->get('buscar'),
            'estado' => $request->get('estado'),
            'anio' => $request->get('anio'),
        ];

        // Procesar cada vehículo para agregar información adicional de manera eficiente
        $vehiculos = $vehiculos->map(function($vehiculo) {
            // Agregar nombre del tipo de activo
            $vehiculo->tipo_activo_nombre = $vehiculo->tipoActivo->nombre ?? 'Sin tipo';
            return $vehiculo;
        });

        // Generar PDF usando el trait
        $pdf = $this->createVehiculosFiltradosPdf($vehiculos, $estadisticas, $filtrosAplicados);

        return $pdf->download('reporte-vehiculos-filtrados-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    /**
     * Descargar reporte de vehículos filtrados en formato Excel
     */
    public function descargarReporteExcel(Request $request)
    {
        $this->authorize('ver_vehiculos');

        // Aplicar los mismos filtros que en el index con optimizaciones
        $query = Vehiculo::select([
            'id', 'marca', 'modelo', 'anio', 'placas', 'n_serie', 
            'estado', 'municipio', 'estatus', 'kilometraje_actual', 'tipo_activo_id', 'created_at'
        ])->with(['tipoActivo:id,nombre']);

        // Aplicar filtros
        if ($request->filled('buscar')) {
            $termino = $request->get('buscar');
            $query->buscar($termino);
        }

        if ($request->filled('estado')) {
            $query->porEstado($request->get('estado'));
        }

        if ($request->filled('anio')) {
            $query->porAnio($request->get('anio'));
        }

        // Para Excel, podemos manejar más registros pero con límite de seguridad
        $vehiculos = $query->orderBy('marca')->orderBy('modelo')->limit(10000)->get();

        // Preparar estadísticas de manera eficiente
        $estadisticas = [
            'total' => $vehiculos->count(),
            'por_estado' => [
                'disponible' => $vehiculos->where('estatus', EstadoVehiculo::DISPONIBLE)->count(),
                'asignado' => $vehiculos->where('estatus', EstadoVehiculo::ASIGNADO)->count(),
                'mantenimiento' => $vehiculos->where('estatus', EstadoVehiculo::EN_MANTENIMIENTO)->count(),
                'fuera_servicio' => $vehiculos->where('estatus', EstadoVehiculo::FUERA_DE_SERVICIO)->count(),
                'baja' => $vehiculos->where('estatus', EstadoVehiculo::BAJA)->count(),
            ],
        ];

        // Preparar filtros aplicados
        $filtrosAplicados = [
            'buscar' => $request->get('buscar'),
            'estado' => $request->get('estado'),
            'anio' => $request->get('anio'),
        ];

        // Usar la clase Export optimizada
        return Excel::download(
            new VehiculosFiltradosExport($vehiculos, $filtrosAplicados, $estadisticas),
            'reporte-vehiculos-filtrados-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
        );
    }
}