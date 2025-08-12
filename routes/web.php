<?php

use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\KilometrajeController;
use App\Http\Controllers\MantenimientoController;
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

// Rutas para Vehículos CRUD (usando datos reales de la base de datos)
Route::middleware('auth')->group(function () {
    // Rutas para vehículos (datos estáticos)
    Route::get('/vehiculos', function (\Illuminate\Http\Request $request) {
        // Obtener vehículos reales de la base de datos con sus estatus
        $query = \App\Models\Vehiculo::with('estatus');

        // Aplicar filtros si existen
        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('marca', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%")
                    ->orWhere('placas', 'like', "%{$search}%")
                    ->orWhere('n_serie', 'like', "%{$search}%");
            });
        }

        if ($request->has('estatus_id') && !empty($request->input('estatus_id'))) {
            $query->where('estatus_id', $request->input('estatus_id'));
        }

        if ($request->has('marca') && $request->input('marca') !== '') {
            $query->where('marca', 'like', "%{$request->input('marca')}%");
        }

        // Paginar resultados
        $vehiculos = $query->orderBy('created_at', 'desc')->paginate(15);

        // Obtener estatus para el filtro
        $estatus = \App\Models\CatalogoEstatus::where('activo', true)
            ->orderBy('nombre_estatus')
            ->get();

        return view('vehiculos.index', compact('vehiculos', 'estatus'));
    })->name('vehiculos.index')->middleware('permission:ver_vehiculos');

    Route::get('/vehiculos/create', function () {
        // Obtener estatus reales de la base de datos
        $estatus = \App\Models\CatalogoEstatus::where('activo', true)
            ->orderBy('nombre_estatus')
            ->get();

        // Obtener tipos de documento reales de la base de datos
        $tiposDocumento = \App\Models\CatalogoTipoDocumento::orderBy('nombre_tipo_documento')->get();

        return view('vehiculos.create', compact('estatus', 'tiposDocumento'));
    })->name('vehiculos.create')->middleware('permission:crear_vehiculos');

    // Ruta movida al grupo de personal para evitar conflictos

    Route::post('/vehiculos', function (\Illuminate\Http\Request $request) {
        try {
            // Validar los datos del formulario
            $validatedData = $request->validate([
                'marca' => 'required|string|max:100',
                'modelo' => 'required|string|max:100',
                'anio' => 'required|integer|min:1990|max:2025',
                'n_serie' => 'required|string|max:50|unique:vehiculos,n_serie',
                'placas' => 'required|string|max:20|unique:vehiculos,placas',
                'kilometraje_actual' => 'required|integer|min:0',
                'estatus_id' => 'required|exists:catalogo_estatus,id',
                'observaciones' => 'nullable|string|max:1000',
                'intervalo_km_motor' => 'nullable|integer|min:1000',
                'intervalo_km_transmision' => 'nullable|integer|min:5000',
                'intervalo_km_hidraulico' => 'nullable|integer|min:1000',
                // Campos de documentos (solo números, archivos no funcionarán)
                'no_tarjeta_circulacion' => 'nullable|string|max:50',
                'fecha_vencimiento_tarjeta' => 'nullable|date',
                'no_derecho_vehicular' => 'nullable|string|max:50',
                'fecha_vencimiento_derecho' => 'nullable|date',
                'no_poliza_seguro' => 'nullable|string|max:50',
                'fecha_vencimiento_seguro' => 'nullable|date',
                'aseguradora' => 'nullable|string|max:100',
                'no_factura_pedimento' => 'nullable|string|max:50',
            ]);

            // Crear el vehículo usando el modelo
            $vehiculo = \App\Models\Vehiculo::create($validatedData);

            return redirect()->route('vehiculos.index')->with('success', 'Vehículo creado exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el vehículo: ' . $e->getMessage())->withInput();
        }
    })->name('vehiculos.store')->middleware('permission:crear_vehiculos');

    Route::get('/vehiculos/{id}', function ($id) {
        // Obtener datos reales del vehículo de la base de datos con todas las relaciones necesarias
        $vehiculoReal = \App\Models\Vehiculo::with(['estatus', 'kilometrajes.usuarioCaptura', 'documentos.tipoDocumento'])->find($id);

        if (!$vehiculoReal) {
            abort(404, 'Vehículo no encontrado');
        }

        // Crear objeto con datos reales para campos generales y datos estáticos para el resto
        $vehiculo = (object) [
            'id' => $vehiculoReal->id,
            'placas' => $vehiculoReal->placas,
            'marca' => $vehiculoReal->marca,
            'modelo' => $vehiculoReal->modelo,
            'anio' => $vehiculoReal->anio,
            'n_serie' => $vehiculoReal->n_serie,
            'kilometraje_actual' => $vehiculoReal->kilometraje_actual,
            'estatus' => $vehiculoReal->estatus,
            'kilometrajes' => $vehiculoReal->kilometrajes,
            'documentos' => $vehiculoReal->documentos,
            // Campos estáticos para documentos y otros datos
            'derecho_vehicular' => 'DV-2025-001234',
            'poliza_seguro' => 'PS-2025-567890',
            // CORREGIDO: Usar el valor real del campo imagen en lugar de uno estático
            'imagen' => $vehiculoReal->imagen
        ];

        return view('vehiculos.show', compact('vehiculo'));
    })->name('vehiculos.show')->middleware('permission:ver_vehiculos');

    Route::get('/vehiculos/{id}/edit', function ($id) {
        // Obtener datos reales del vehículo de la base de datos
        $vehiculoReal = \App\Models\Vehiculo::with(['estatus', 'documentos.tipoDocumento'])->find($id);

        if (!$vehiculoReal) {
            abort(404, 'Vehículo no encontrado');
        }

        // Obtener estatus activos para el dropdown
        $estatusDisponibles = \App\Models\CatalogoEstatus::where('activo', true)
            ->orderBy('nombre_estatus')
            ->get();

        // Obtener tipos de documentos disponibles
        $tiposDocumento = \App\Models\CatalogoTipoDocumento::orderBy('nombre_tipo_documento')->get();

        // Crear objeto con datos reales para campos generales y datos estáticos para el resto
        $vehiculo = (object) [
            'id' => $vehiculoReal->id,
            'placas' => $vehiculoReal->placas,
            'marca' => $vehiculoReal->marca,
            'modelo' => $vehiculoReal->modelo,
            'anio' => $vehiculoReal->anio,
            'n_serie' => $vehiculoReal->n_serie,
            'kilometraje_actual' => $vehiculoReal->kilometraje_actual,
            'estatus_id' => $vehiculoReal->estatus_id,
            'estatus' => $vehiculoReal->estatus,
            'observaciones' => $vehiculoReal->observaciones,
            'documentos_adicionales' => $vehiculoReal->documentos_adicionales,
            'documentos' => $vehiculoReal->documentos,
            // Campos estáticos para intervalos de mantenimiento
            'intervalo_km_motor' => 5000,
            'intervalo_km_transmision' => 40000,
            'intervalo_km_hidraulico' => 10000
        ];

        return view('vehiculos.edit', compact('vehiculo', 'estatusDisponibles', 'tiposDocumento'));
    })->name('vehiculos.edit')->middleware('permission:editar_vehiculos');

    Route::put('/vehiculos/{id}', function (Request $request, $id) {
        // Buscar el vehículo
        $vehiculo = \App\Models\Vehiculo::find($id);

        if (!$vehiculo) {
            abort(404, 'Vehículo no encontrado');
        }

        // Validar los datos
        $request->validate([
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'n_serie' => 'required|string|max:50|unique:vehiculos,n_serie,' . $id,
            'placas' => 'required|string|max:20|unique:vehiculos,placas,' . $id,
            'kilometraje_actual' => 'required|integer|min:0',
            'estatus_id' => 'required|exists:catalogo_estatus,id',
            'observaciones' => 'nullable|string|max:1000',
            'documentos_adicionales.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240'
        ]);

        // Preparar datos para actualizar
        $datosVehiculo = [
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'anio' => $request->anio,
            'n_serie' => $request->n_serie,
            'placas' => $request->placas,
            'kilometraje_actual' => $request->kilometraje_actual,
            'estatus_id' => $request->estatus_id,
            'observaciones' => $request->observaciones
        ];

        // Manejar documentos adicionales si se subieron nuevos
        if ($request->hasFile('documentos_adicionales')) {
            // Eliminar documentos anteriores si existen
            if ($vehiculo->documentos_adicionales) {
                foreach ($vehiculo->documentos_adicionales as $rutaDoc) {
                    if (\Storage::disk('public')->exists($rutaDoc)) {
                        \Storage::disk('public')->delete($rutaDoc);
                    }
                }
            }

            // Subir nuevos documentos
            $documentosRutas = [];
            foreach ($request->file('documentos_adicionales') as $archivo) {
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                $ruta = $archivo->storeAs('vehiculos/documentos', $nombreArchivo, 'public');
                $documentosRutas[] = $ruta;
            }
            $datosVehiculo['documentos_adicionales'] = $documentosRutas;
        }

        // Actualizar el vehículo
        $vehiculo->update($datosVehiculo);

        return redirect()->route('vehiculos.show', $id)->with('success', 'Vehículo actualizado exitosamente.');
    })->name('vehiculos.update')->middleware('permission:editar_vehiculos');

    Route::delete('/vehiculos/{id}', function ($id) {
        return redirect()->route('vehiculos.index')->with('success', 'Vehículo eliminado exitosamente.');
    })->name('vehiculos.destroy')->middleware('permission:eliminar_vehiculos');
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

    // Ruta para mostrar formulario de crear personal (valores estáticos)
    Route::get('/create', function () {
        // Categorías estáticas
        $categorias = collect([
            (object)['id' => 1, 'nombre_categoria' => 'Administrador'],
            (object)['id' => 2, 'nombre_categoria' => 'Supervisor'],
            (object)['id' => 3, 'nombre_categoria' => 'Operador'],
            (object)['id' => 4, 'nombre_categoria' => 'Técnico'],
            (object)['id' => 5, 'nombre_categoria' => 'Mecánico'],
            (object)['id' => 6, 'nombre_categoria' => 'Jefe de Obra']
        ]);

        // Usuarios estáticos
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

    // Ruta para mostrar formulario de editar personal - CORREGIDO: Agregado middleware de permisos
    Route::get('/{id}/edit', function ($id) {
        $personal = \App\Models\Personal::findOrFail($id);

        // Categorías estáticas
        $categorias = collect([
            (object)['id' => 1, 'nombre_categoria' => 'Administrador'],
            (object)['id' => 2, 'nombre_categoria' => 'Supervisor'],
            (object)['id' => 3, 'nombre_categoria' => 'Operador'],
            (object)['id' => 4, 'nombre_categoria' => 'Técnico'],
            (object)['id' => 5, 'nombre_categoria' => 'Mecánico'],
            (object)['id' => 6, 'nombre_categoria' => 'Jefe de Obra']
        ]);

        // Usuarios estáticos
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

// Rutas para Kilometrajes (CRUD completo)
Route::middleware('auth')->prefix('kilometrajes')->name('kilometrajes.')->group(function () {
    Route::get('/', [KilometrajeController::class, 'index'])
        ->name('index');

    Route::get('/create', [KilometrajeController::class, 'create'])
        ->name('create');

    Route::post('/', [KilometrajeController::class, 'store'])
        ->name('store');

    Route::get('/{kilometraje}', [KilometrajeController::class, 'show'])
        ->name('show');

    Route::get('/{kilometraje}/edit', [KilometrajeController::class, 'edit'])
        ->name('edit');

    Route::put('/{kilometraje}', [KilometrajeController::class, 'update'])
        ->name('update');

    Route::delete('/{kilometraje}', [KilometrajeController::class, 'destroy'])
        ->name('destroy');

    // Rutas adicionales específicas
    Route::get('/vehiculo/{vehiculoId}/historial', [KilometrajeController::class, 'historialPorVehiculo'])
        ->name('historial');

    Route::get('/alertas/mantenimiento', [KilometrajeController::class, 'alertasMantenimiento'])
        ->name('alertas');
});

// Rutas para Mantenimientos (CRUD completo)
Route::middleware('auth')->prefix('mantenimientos')->name('mantenimientos.')->group(function () {
    Route::get('/', [MantenimientoController::class, 'index'])
        ->name('index')
        ->middleware('permission:ver_mantenimientos');

    Route::get('/create', [MantenimientoController::class, 'create'])
        ->name('create')
        ->middleware('permission:crear_mantenimientos');

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

    // Rutas adicionales específicas
    Route::get('/proximos/kilometraje', [MantenimientoController::class, 'proximosPorKilometraje'])
        ->name('proximos.kilometraje')
        ->middleware('permission:ver_mantenimientos');

    Route::get('/estadisticas/general', [MantenimientoController::class, 'estadisticas'])
        ->name('estadisticas')
        ->middleware('permission:ver_mantenimientos');
});

// Ruta para vista de usuario (datos estáticos)
Route::get('/usuarios/{id}', function ($id) {
    return view('usuarios.show');
})->name('usuarios.show');
