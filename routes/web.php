<?php

use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\KilometrajeController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\MantenimientoAlertasController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\PersonalCompleteController;
use App\Http\Controllers\PersonalManagementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Personal;
use App\Models\User;
use App\Models\CategoriaPersonal;
use App\Models\Documento;
use App\Models\CatalogoTipoDocumento;
use App\Models\LogAccion;

// Ruta principal redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación (sin registro público)
Auth::routes(['register' => false]);

// Ruta del dashboard después de iniciar sesión
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

// Ruta para alertas de mantenimiento
Route::get('/alertas/mantenimiento', [MantenimientoAlertasController::class, 'index'])
    ->name('alertas.mantenimiento')
    ->middleware(['auth', 'permission:ver_mantenimientos']);

// Ruta para vista unificada de alertas
Route::get('/alertas/unificada', [MantenimientoAlertasController::class, 'unificada'])
    ->name('alertas.unificada')
    ->middleware(['auth', 'permission:ver_mantenimientos']);

// Ruta para centro de alertas (alias de unificada)
Route::get('/alertas', [MantenimientoAlertasController::class, 'unificada'])
    ->name('alertas.index')
    ->middleware(['auth', 'permission:ver_mantenimientos']);

// Rutas para Vehículos CRUD (usando VehiculoController)
Route::middleware('auth')->prefix('vehiculos')->name('vehiculos.')->group(function () {
    Route::get('/', [App\Http\Controllers\VehiculoController::class, 'index'])
        ->name('index')
        ->middleware('permission:ver_vehiculos');
    
    Route::get('/create', [App\Http\Controllers\VehiculoController::class, 'create'])
        ->name('create')
        ->middleware('permission:crear_vehiculos');
    
    Route::post('/', [App\Http\Controllers\VehiculoController::class, 'store'])
        ->name('store')
        ->middleware('permission:crear_vehiculos');
    
    Route::get('/{vehiculo}', [App\Http\Controllers\VehiculoController::class, 'show'])
        ->name('show')
        ->middleware('permission:ver_vehiculos');
    
    Route::get('/{vehiculo}/edit', [App\Http\Controllers\VehiculoController::class, 'edit'])
        ->name('edit')
        ->middleware('permission:editar_vehiculos');
    
    Route::put('/{vehiculo}', [App\Http\Controllers\VehiculoController::class, 'update'])
        ->name('update')
        ->middleware('permission:editar_vehiculos');
    
    Route::delete('/{vehiculo}', [App\Http\Controllers\VehiculoController::class, 'destroy'])
        ->name('destroy')
        ->middleware('permission:eliminar_vehiculos');

    // Rutas de kilometraje integradas en vehículos
    Route::get('/{vehiculo}/kilometrajes', [App\Http\Controllers\VehiculoController::class, 'kilometrajes'])
        ->name('kilometrajes.vehiculo')
        ->middleware('permission:ver_vehiculos');
        
    Route::get('/{vehiculo}/kilometrajes/create', [App\Http\Controllers\VehiculoController::class, 'createKilometraje'])
        ->name('kilometrajes.create.vehiculo')
        ->middleware('permission:crear_kilometrajes');
        
    Route::post('/{vehiculo}/kilometrajes', [App\Http\Controllers\VehiculoController::class, 'storeKilometraje'])
        ->name('kilometrajes.store.vehiculo')
        ->middleware('permission:crear_kilometrajes');
        
    Route::get('/{vehiculo}/kilometrajes/{kilometraje}', [App\Http\Controllers\VehiculoController::class, 'showKilometraje'])
        ->name('kilometrajes.show.vehiculo')
        ->middleware('permission:ver_vehiculos');
        
    Route::get('/{vehiculo}/kilometrajes/{kilometraje}/edit', [App\Http\Controllers\VehiculoController::class, 'editKilometraje'])
        ->name('kilometrajes.edit.vehiculo')
        ->middleware('permission:editar_kilometrajes');
        
    Route::put('/{vehiculo}/kilometrajes/{kilometraje}', [App\Http\Controllers\VehiculoController::class, 'updateKilometraje'])
        ->name('kilometrajes.update.vehiculo')
        ->middleware('permission:editar_kilometrajes');
        
    Route::delete('/{vehiculo}/kilometrajes/{kilometraje}', [App\Http\Controllers\VehiculoController::class, 'destroyKilometraje'])
        ->name('kilometrajes.destroy.vehiculo')
        ->middleware('permission:eliminar_kilometrajes');
        
    // Ruta para cambiar operador del vehículo
    Route::patch('/{vehiculo}/cambiar-operador', [App\Http\Controllers\VehiculoController::class, 'cambiarOperador'])
        ->name('cambiar-operador')
        ->middleware('permission:editar_vehiculos');
});

// Ruta para crear personal (fuera del grupo para evitar conflictos con PUT personal/{id})
Route::post('/personal', [App\Http\Controllers\PersonalManagementController::class, 'storeWeb'])
    ->name('personal.store')
    ->middleware(['auth', 'permission:crear_personal']);

// Rutas para Personal CRUD
Route::middleware('auth')->prefix('personal')->name('personal.')->group(function () {
    // Ruta para listar personal (datos reales de la base de datos)
    Route::get('/', function (\Illuminate\Http\Request $request) {
        // Obtener categorías reales de la base de datos
        $categorias = \App\Models\CategoriaPersonal::orderBy('nombre_categoria')->get();

        // Construir consulta para personal con filtros
        $query = \App\Models\Personal::with('categoria', 'usuario');

        // Aplicar filtros si existen
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                    ->orWhereHas('categoria', function ($cq) use ($search) {
                        $cq->where('nombre_categoria', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('categoria_id') && $request->input('categoria_id') !== '') {
            $query->where('categoria_id', $request->input('categoria_id'));
        }

        if ($request->has('estatus') && $request->input('estatus') !== '') {
            $query->where('estatus', $request->input('estatus'));
        }

        // Paginar resultados - ordenar por ID descendente
        $personal = $query->reorder('personal.id', 'asc')->paginate(15);

        return view('personal.index', compact('personal', 'categorias'));
    })->name('index')->middleware('permission:ver_personal');

    // Ruta para mostrar formulario de crear personal (usando categorías reales de BD)
    Route::get('/create', function () {
        // Obtener categorías reales de la base de datos
        $categorias = \App\Models\CategoriaPersonal::select('id', 'nombre_categoria')
            ->orderBy('nombre_categoria')
            ->get();

        // Usuarios estáticos (mantenemos estos)
        $usuarios = collect([
            (object)['id' => 1, 'nombre_usuario' => 'Administrador del Sistema'],
            (object)['id' => 2, 'nombre_usuario' => 'Juan Pérez Supervisor'],
            (object)['id' => 3, 'nombre_usuario' => 'Ana Patricia']
        ]);

        // Roles desde la base de datos
        $roles = \App\Models\Role::select('id', 'nombre_rol')
            ->orderBy('nombre_rol')
            ->get();

        return view('personal.create', compact('categorias', 'usuarios', 'roles'));
    })->name('create')->middleware('permission:crear_personal');

    // Ruta para guardar nuevo personal (movida fuera del grupo para evitar conflictos)
    // Route::post('/', [App\Http\Controllers\PersonalManagementController::class, 'storeWeb'])
    //     ->name('store')
    //     ->middleware('permission:crear_personal');

    // Rutas para creación completa de personal con documentos y usuario
    Route::get('/complete/create', [PersonalCompleteController::class, 'create'])
        ->name('complete.create')
        ->middleware('permission:crear_personal');

    Route::post('/complete', [PersonalCompleteController::class, 'store'])
        ->name('complete.store')
        ->middleware('permission:crear_personal');

    // Ruta para mostrar detalles de un personal (datos reales de la base de datos)
    Route::get('/{id}', function ($id) {
        // Obtener personal real de la base de datos con relaciones
        $personal = \App\Models\Personal::with([
            'categoria',
            'usuario.rol',  // Incluir la relación del rol del usuario
            'documentos' => function ($query) {
                $query->with('tipoDocumento')
                    ->select('id', 'tipo_documento_id', 'descripcion', 'fecha_vencimiento', 'personal_id', 'contenido', 'created_at', 'updated_at');
            }
        ])->findOrFail($id);

        // Organizar documentos por tipo con mapeo para compatibilidad con la vista
        $documentosPorTipo = $personal->documentos->groupBy(function ($documento) {
            if (!$documento->tipoDocumento) {
                return 'Sin tipo';
            }

            $tipoNombre = $documento->tipoDocumento->nombre_tipo_documento;

            // Mapear "Identificación Oficial" a "INE" para compatibilidad con la vista
            if ($tipoNombre === 'Identificación Oficial') {
                return 'INE';
            }

            return $tipoNombre;
        })->toArray();

        return view('personal.show', compact('personal', 'documentosPorTipo'));
    })->name('show')->middleware('permission:ver_personal');

    // Ruta para guardar documentos del personal (con archivos)
    Route::post('/{id}/documents/upload', function (Request $request, $id) {
        $personal = Personal::findOrFail($id);

        $request->validate([
            'tipo_documento' => 'required|string',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB máximo
            'descripcion' => 'nullable|string|max:500',
            'fecha_vencimiento' => 'nullable|date'
        ]);

        // Mapear "INE" a "Identificación Oficial" para buscar en la base de datos
        $tipoDocumentoBuscar = $request->tipo_documento;
        if ($tipoDocumentoBuscar === 'INE') {
            $tipoDocumentoBuscar = 'Identificación Oficial';
        }

        // Buscar el tipo de documento
        $tipoDocumento = CatalogoTipoDocumento::where('nombre_tipo_documento', $tipoDocumentoBuscar)->first();

        if (!$tipoDocumento) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de documento no válido.'
            ], 400);
        }

        try {
            // Manejar la subida del archivo
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $personal->id . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = $archivo->storeAs('personal/documentos', $nombreArchivo, 'private');

            // Verificar si ya existe un documento de este tipo para este personal
            $documentoExistente = Documento::where('personal_id', $personal->id)
                ->where('tipo_documento_id', $tipoDocumento->id)
                ->first();

            if ($documentoExistente) {
                // Eliminar archivo anterior si existe
                if ($documentoExistente->ruta_archivo && \Storage::disk('private')->exists($documentoExistente->ruta_archivo)) {
                    \Storage::disk('private')->delete($documentoExistente->ruta_archivo);
                }

                // Actualizar documento existente
                $documentoExistente->update([
                    'descripcion' => $request->descripcion ?? $tipoDocumentoBuscar,
                    'fecha_vencimiento' => $request->fecha_vencimiento,
                    'ruta_archivo' => $rutaArchivo,
                    'contenido' => $request->descripcion ?? $tipoDocumentoBuscar
                ]);
                $mensaje = 'Documento actualizado exitosamente.';
            } else {
                // Crear nuevo documento
                Documento::create([
                    'personal_id' => $personal->id,
                    'tipo_documento_id' => $tipoDocumento->id,
                    'descripcion' => $request->descripcion ?? $tipoDocumentoBuscar,
                    'fecha_vencimiento' => $request->fecha_vencimiento,
                    'ruta_archivo' => $rutaArchivo,
                    'contenido' => $request->descripcion ?? $tipoDocumentoBuscar
                ]);
                $mensaje = 'Documento guardado exitosamente.';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje
            ]);
        } catch (\Exception $e) {
            // Eliminar archivo si se subió pero falló la creación del registro
            if (isset($rutaArchivo) && \Storage::disk('private')->exists($rutaArchivo)) {
                \Storage::disk('private')->delete($rutaArchivo);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el documento: ' . $e->getMessage()
            ], 500);
        }
    })->name('documents.upload')->middleware('permission:editar_personal');

    // Ruta para mostrar formulario de editar personal - usando categorías reales de BD
    Route::get('/{id}/edit', function ($id) {
        $personal = \App\Models\Personal::findOrFail($id);

        // Obtener categorías reales de la base de datos
        $categorias = \App\Models\CategoriaPersonal::select('id', 'nombre_categoria')
            ->orderBy('nombre_categoria')
            ->get();

        // Usuarios estáticos (mantenemos estos)
        $usuarios = collect([
            (object)['id' => 1, 'nombre_usuario' => 'Administrador del Sistema'],
            (object)['id' => 2, 'nombre_usuario' => 'Juan Pérez Supervisor'],
            (object)['id' => 3, 'nombre_usuario' => 'Ana Patricia']
        ]);

        return view('personal.edit', compact('personal', 'categorias', 'usuarios'));
    })->name('edit')->middleware('permission:editar_personal');

    // Ruta para actualizar personal - Usando PersonalController para manejar archivos
    Route::put('/{id}', [\App\Http\Controllers\PersonalController::class, 'update'])
        ->name('update')
        ->middleware('permission:editar_personal');

    // Ruta para eliminar personal - CORREGIDO: Agregado middleware de permisos
    Route::delete('/{id}', function ($id) {
        $personal = \App\Models\Personal::findOrFail($id);
        $nombre = $personal->nombre_completo;

        $personal->delete();

        return redirect()->route('personal.index')
            ->with('success', "Personal '{$nombre}' eliminado exitosamente.");
    })->name('destroy')->middleware('permission:eliminar_personal');
});

// Rutas para Documentos
Route::middleware('auth')->group(function () {
    Route::resource('documentos', DocumentoController::class);

    // Ruta para mostrar archivos de documentos en el navegador
    Route::get('documentos/{documento}/file', [DocumentoController::class, 'showFile'])
        ->name('documentos.file')
        ->middleware('permission:ver_documentos');

    // Rutas específicas para documentos de vehículos
    Route::post('vehiculos/{vehiculo}/documentos', [DocumentoController::class, 'storeForVehiculo'])
        ->name('vehiculos.documentos.store')
        ->middleware('permission:crear_documentos');

    Route::get('vehiculos/{vehiculo}/documentos', [DocumentoController::class, 'getByVehiculo'])
        ->name('vehiculos.documentos.index')
        ->middleware('permission:ver_documentos');
});

// Rutas web para obtener datos (proxy a los modelos) - CORREGIDO: Agregado middleware de permisos
Route::middleware('auth')->group(function () {
    Route::get('/web-api/categorias-personal', function () {
        try {
            $categorias = \App\Models\CategoriaPersonal::all();
            return response()->json([
                'success' => true,
                'data' => $categorias
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar categorías'
            ], 500);
        }
    })->name('web-api.categorias-personal')->middleware('permission:ver_personal');

    Route::get('/web-api/personal', function (\Illuminate\Http\Request $request) {
        try {
            $query = \App\Models\Personal::with(['categoria', 'usuario']);

            // Filtros
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nombre_completo', 'like', "%{$search}%")
                        ->orWhereHas('categoria', function ($cq) use ($search) {
                            $cq->where('nombre_categoria', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->has('categoria_id')) {
                $query->where('categoria_id', $request->input('categoria_id'));
            }

            if ($request->has('estatus')) {
                $query->where('estatus', $request->input('estatus'));
            }

            $personal = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $personal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar personal'
            ], 500);
        }
    })->name('web-api.personal')->middleware('permission:ver_personal');
});

// Rutas para Obras
Route::middleware('auth')->group(function () {
    Route::get('/obras', [\App\Http\Controllers\ObraController::class, 'index'])
        ->name('obras.index')
        ->middleware('permission:ver_obras');

    Route::get('/obras/create', [\App\Http\Controllers\ObraController::class, 'create'])
        ->name('obras.create')
        ->middleware('permission:crear_obras');

    Route::post('/obras', [\App\Http\Controllers\ObraController::class, 'store'])
        ->name('obras.store')
        ->middleware('permission:crear_obras');

    Route::get('/obras/{obra}', [\App\Http\Controllers\ObraController::class, 'show'])
        ->name('obras.show')
        ->middleware('permission:ver_obras');

    Route::get('/obras/{obra}/edit', [\App\Http\Controllers\ObraController::class, 'edit'])
        ->name('obras.edit')
        ->middleware('permission:actualizar_obras');

    Route::put('/obras/{obra}', [\App\Http\Controllers\ObraController::class, 'update'])
        ->name('obras.update')
        ->middleware('permission:actualizar_obras');

    Route::delete('/obras/{obra}', [\App\Http\Controllers\ObraController::class, 'destroy'])
        ->name('obras.destroy')
        ->middleware('permission:eliminar_obras');

    Route::post('/obras/{id}/restore', [\App\Http\Controllers\ObraController::class, 'restore'])
        ->name('obras.restore')
        ->middleware('permission:restaurar_obras');
});

// Rutas para Kilometrajes (Vista general independiente + integración en vehículos)
Route::middleware('auth')->prefix('kilometrajes')->name('kilometrajes.')->group(function () {
    Route::get('/', [KilometrajeController::class, 'index'])
        ->name('index')
        ->middleware('permission:ver_kilometrajes');
    Route::get('/create', [KilometrajeController::class, 'create'])
        ->name('create')
        ->middleware('permission:crear_kilometrajes');
    Route::post('/', [KilometrajeController::class, 'store'])
        ->name('store')
        ->middleware('permission:crear_kilometrajes');
    Route::get('/vehiculo/{vehiculoId}/historial', [KilometrajeController::class, 'historialPorVehiculo'])
        ->name('historial')
        ->middleware('permission:ver_kilometrajes');
});

// Rutas para Mantenimientos (CRUD completo)
Route::middleware('auth')->prefix('mantenimientos')->name('mantenimientos.')->group(function () {
    Route::get('/', [MantenimientoController::class, 'index'])
        ->name('index')
        ->middleware('permission:ver_mantenimientos');

    Route::get('/create', [MantenimientoController::class, 'create'])
        ->name('create')
        ->middleware('permission:crear_mantenimientos');

    // Rutas adicionales específicas (DEBEN IR ANTES de las rutas con parámetros)
    Route::get('/alertas', [MantenimientoController::class, 'alertas'])
        ->name('alertas')
        ->middleware('permission:ver_mantenimientos');

    Route::get('/proximos/kilometraje', [MantenimientoController::class, 'proximosPorKilometraje'])
        ->name('proximos.kilometraje')
        ->middleware('permission:ver_mantenimientos');

    Route::get('/estadisticas/general', [MantenimientoController::class, 'estadisticas'])
        ->name('estadisticas')
        ->middleware('permission:ver_mantenimientos');

    Route::post('/', [MantenimientoController::class, 'store'])
        ->name('store')
        ->middleware('permission:crear_mantenimientos');

    Route::get('/{id}', [MantenimientoController::class, 'show'])
        ->name('show')
        ->middleware('permission:ver_mantenimientos');

    Route::get('/{id}/edit', [MantenimientoController::class, 'edit'])
        ->name('edit')
        ->middleware('permission:actualizar_mantenimientos');

    Route::put('/{id}', [MantenimientoController::class, 'update'])
        ->name('update')
        ->middleware('permission:actualizar_mantenimientos');

    Route::delete('/{id}', [MantenimientoController::class, 'destroy'])
        ->name('destroy')
        ->middleware('permission:eliminar_mantenimientos');

    Route::post('/{id}/restore', [MantenimientoController::class, 'restore'])
        ->name('restore')
        ->middleware('permission:restaurar_mantenimientos');
});

// Rutas para Categorías de Personal (solo para administradores)
Route::middleware('auth')->prefix('categorias-personal')->name('categorias-personal.')->group(function () {
    Route::get('/', [App\Http\Controllers\CategoriaPersonalController::class, 'index'])
        ->name('index')
        ->middleware('permission:ver_catalogos');

    Route::get('/create', [App\Http\Controllers\CategoriaPersonalController::class, 'create'])
        ->name('create')
        ->middleware('permission:crear_catalogos');

    Route::post('/', [App\Http\Controllers\CategoriaPersonalController::class, 'store'])
        ->name('store')
        ->middleware('permission:crear_catalogos');

    Route::get('/{categoriaPersonal}', [App\Http\Controllers\CategoriaPersonalController::class, 'show'])
        ->name('show')
        ->middleware('permission:ver_catalogos');

    Route::get('/{categoriaPersonal}/edit', [App\Http\Controllers\CategoriaPersonalController::class, 'edit'])
        ->name('edit')
        ->middleware('permission:editar_catalogos');

    Route::put('/{categoriaPersonal}', [App\Http\Controllers\CategoriaPersonalController::class, 'update'])
        ->name('update')
        ->middleware('permission:editar_catalogos');

    Route::delete('/{categoriaPersonal}', [App\Http\Controllers\CategoriaPersonalController::class, 'destroy'])
        ->name('destroy')
        ->middleware('permission:eliminar_catalogos');
});

// Rutas para Asignaciones de Obra
Route::middleware('auth')->prefix('asignaciones-obra')->name('asignaciones-obra.')->group(function () {
    Route::get('/', [App\Http\Controllers\AsignacionObraController::class, 'index'])
        ->name('index')
        ->middleware('permission:ver_asignaciones');
    
    Route::get('/create', [App\Http\Controllers\AsignacionObraController::class, 'create'])
        ->name('create')
        ->middleware('permission:crear_asignaciones');
    
    Route::post('/', [App\Http\Controllers\AsignacionObraController::class, 'store'])
        ->name('store')
        ->middleware('permission:crear_asignaciones');
    
    Route::get('/{id}', [App\Http\Controllers\AsignacionObraController::class, 'show'])
        ->name('show')
        ->middleware('permission:ver_asignaciones');
    
    Route::post('/{id}/liberar', [App\Http\Controllers\AsignacionObraController::class, 'liberar'])
        ->name('liberar')
        ->middleware('permission:crear_asignaciones');
    
    Route::post('/vehiculos/{vehiculo}/cambiar-obra', [App\Http\Controllers\AsignacionObraController::class, 'cambiarObra'])
        ->name('cambiar-obra')
        ->middleware('permission:crear_asignaciones');
    
    Route::get('/estadisticas', [App\Http\Controllers\AsignacionObraController::class, 'estadisticas'])
        ->name('estadisticas')
        ->middleware('permission:ver_asignaciones');
});

// Ruta para vista de usuario (datos estáticos)
Route::get('/usuarios/{id}', function ($id) {
    return view('usuarios.show');
})->name('usuarios.show');

// Ruta de test para campanita
Route::get('/test-campanita', function () {
    return view('test-campanita');
})->middleware('auth');

// Ruta de debug para ViewComposer
Route::get('/debug-composer', function () {
    $estadisticas = App\Http\Controllers\MantenimientoAlertasController::getEstadisticasAlertas();
    return response()->json([
        'metodo_estatico' => $estadisticas,
        'timestamp' => now()
    ]);
});

// Ruta de debug para comparar ambos métodos
Route::get('/debug-comparacion', function () {
    $controller = new App\Http\Controllers\MantenimientoAlertasController();
    $vista = $controller->unificada();
    $datosVista = $vista->getData();
    $metodoEstatico = App\Http\Controllers\MantenimientoAlertasController::getEstadisticasAlertas();
    
    return response()->json([
        'vista_unificada' => $datosVista['estadisticas'],
        'metodo_estatico' => $metodoEstatico,
        'alertas_unificadas_count' => count($datosVista['alertasUnificadas']),
        'timestamp' => now()
    ]);
});

// Ruta de debug para conteo detallado
Route::get('/debug-conteo', function () {
    $controller = new App\Http\Controllers\MantenimientoAlertasController();
    
    // Obtener datos del método unificada
    $vista = $controller->unificada();
    $datosVista = $vista->getData();
    $alertasUnificadas = $datosVista['alertasUnificadas'];
    $estadisticasVista = $datosVista['estadisticas'];
    
    // Obtener datos del método estático
    $estadisticasEstatico = App\Http\Controllers\MantenimientoAlertasController::getEstadisticasAlertas();
    
    return response()->json([
        'metodo_unificada' => [
            'total_alertas_array' => count($alertasUnificadas),
            'estadisticas' => $estadisticasVista,
            'alertas_detalle' => collect($alertasUnificadas)->map(function($alerta) {
                return [
                    'tipo' => $alerta['tipo'],
                    'estado' => $alerta['estado'],
                    'vehiculo' => $alerta['vehiculo_info']['placas'] ?? 'N/A',
                    'descripcion' => substr($alerta['descripcion'], 0, 50) . '...'
                ];
            })
        ],
        'metodo_estatico' => $estadisticasEstatico,
        'diferencia' => [
            'vista_total' => $estadisticasVista['total'],
            'estatico_total' => $estadisticasEstatico['alertasCount'],
            'diferencia' => $estadisticasEstatico['alertasCount'] - $estadisticasVista['total']
        ]
    ], JSON_PRETTY_PRINT);
});

// ================================
// GESTIÓN DE ROLES Y PERMISOS
// ================================

// Rutas para gestión de roles (solo administradores)
Route::middleware(['auth', 'permission:ver_roles'])->prefix('admin')->name('admin.')->group(function () {
    // Gestión de Roles
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class)->parameters([
        'roles' => 'role'
    ]);
    
    // Rutas adicionales para permisos
    Route::get('roles/{role}/permissions', [App\Http\Controllers\Admin\RoleController::class, 'permissions'])
        ->name('roles.permissions')
        ->middleware('permission:editar_roles');
        
    Route::put('roles/{role}/permissions', [App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])
        ->name('roles.permissions.update')
        ->middleware('permission:editar_roles');
        
    // Ruta AJAX para obtener usuarios de un rol
    Route::get('roles/{role}/users', [App\Http\Controllers\Admin\RoleController::class, 'users'])
        ->name('roles.users')
        ->middleware('permission:ver_roles');
        
    // Ruta AJAX para obtener información rápida de un rol (para configuración)
    Route::get('roles/{role}/quick-info', [App\Http\Controllers\Admin\RoleController::class, 'quickInfo'])
        ->name('roles.quick-info')
        ->middleware('permission:ver_roles');
});

// Ruta para la sección de configuración (menú principal)
Route::middleware(['auth', App\Http\Middleware\CanAccessConfiguration::class])->group(function () {
    Route::get('/configuracion', function () {
        return view('admin.configuracion.index');
    })->name('configuracion.index');
});
