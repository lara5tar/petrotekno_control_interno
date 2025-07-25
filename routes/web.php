<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Personal;
use App\Models\User;
use App\Models\CategoriaPersonal;
use App\Models\Documento;
use App\Models\CatalogoTipoDocumento;

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
    
    return view('vehiculos.create', compact('estatus'));
})->name('vehiculos.create')->middleware('permission:crear_vehiculos');

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
    // Obtener datos reales del vehículo de la base de datos
    $vehiculoReal = \App\Models\Vehiculo::with('estatus')->find($id);
    
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
        // Campos estáticos para documentos y otros datos
        'derecho_vehicular' => 'DV-2025-001234',
        'poliza_seguro' => 'PS-2025-567890',
        'imagen' => '/images/placeholder-vehicle.jpg'
    ];
    
    return view('vehiculos.show', compact('vehiculo'));
})->name('vehiculos.show')->middleware('permission:ver_vehiculos');

Route::get('/vehiculos/{id}/edit', function ($id) {
    // Obtener datos reales del vehículo de la base de datos
    $vehiculoReal = \App\Models\Vehiculo::with('estatus')->find($id);
    
    if (!$vehiculoReal) {
        abort(404, 'Vehículo no encontrado');
    }
    
    // Obtener estatus activos para el dropdown
    $estatusDisponibles = \App\Models\CatalogoEstatus::where('activo', true)
        ->orderBy('nombre_estatus')
        ->get();
    
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
        // Campos estáticos para intervalos de mantenimiento
        'intervalo_km_motor' => 5000,
        'intervalo_km_transmision' => 40000,
        'intervalo_km_hidraulico' => 10000
    ];
    
    return view('vehiculos.edit', compact('vehiculo', 'estatusDisponibles'));
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
        'observaciones' => 'nullable|string|max:1000'
    ]);
    
    // Actualizar solo los campos generales
    $vehiculo->update([
        'marca' => $request->marca,
        'modelo' => $request->modelo,
        'anio' => $request->anio,
        'n_serie' => $request->n_serie,
        'placas' => $request->placas,
        'kilometraje_actual' => $request->kilometraje_actual,
        'estatus_id' => $request->estatus_id,
        'observaciones' => $request->observaciones
    ]);
    
    return redirect()->route('vehiculos.show', $id)->with('success', 'Vehículo actualizado exitosamente.');
})->name('vehiculos.update')->middleware('permission:editar_vehiculos');

Route::delete('/vehiculos/{id}', function ($id) {
    return redirect()->route('vehiculos.index')->with('success', 'Vehículo eliminado exitosamente.');
})->name('vehiculos.destroy')->middleware('permission:eliminar_vehiculos');
});

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
        
        // Paginar resultados
        $personal = $query->orderBy('created_at', 'desc')->paginate(15);
        
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
        
        return view('personal.create', compact('categorias', 'usuarios'));
    })->name('create')->middleware('permission:crear_personal');

    // Ruta para guardar nuevo personal
    Route::post('/', [App\Http\Controllers\PersonalManagementController::class, 'storeWeb'])
        ->name('store')
        ->middleware('permission:crear_personal');

    // Ruta para mostrar detalles de un personal (datos reales de la base de datos)
    Route::get('/{id}', function ($id) {
        // Obtener personal real de la base de datos con relaciones
        $personal = \App\Models\Personal::with([
            'categoria', 
            'usuario',
            'documentos' => function ($query) {
                $query->with('tipoDocumento')
                      ->select('id', 'tipo_documento_id', 'descripcion', 'fecha_vencimiento', 'personal_id', 'contenido', 'created_at', 'updated_at');
            }
        ])->findOrFail($id);
        
        // Organizar documentos por tipo
        $documentosPorTipo = $personal->documentos->groupBy(function ($documento) {
            return $documento->tipoDocumento ? $documento->tipoDocumento->nombre_tipo_documento : 'Sin tipo';
        })->toArray();
        
        return view('personal.show', compact('personal', 'documentosPorTipo'));
    })->name('show')->middleware('permission:ver_personal');

    // Ruta para guardar datos de documentos del personal (sin archivos)
    Route::post('/{id}/documents/upload', function (Request $request, $id) {
        $personal = Personal::findOrFail($id);
        
        $request->validate([
            'tipo_documento' => 'required|string',
            'descripcion' => 'required|string|max:500',
            'fecha_vencimiento' => 'nullable|date'
        ]);
        
        // Buscar el tipo de documento
        $tipoDocumento = CatalogoTipoDocumento::where('nombre_tipo_documento', $request->tipo_documento)->first();
        
        if (!$tipoDocumento) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de documento no válido.'
            ], 400);
        }
        
        // Verificar si ya existe un documento de este tipo para este personal
        $documentoExistente = Documento::where('personal_id', $personal->id)
                                       ->where('tipo_documento_id', $tipoDocumento->id)
                                       ->first();
        
        try {
            if ($documentoExistente) {
                // Actualizar documento existente
                $documentoExistente->update([
                    'descripcion' => $request->descripcion,
                    'fecha_vencimiento' => $request->fecha_vencimiento,
                    'contenido' => $request->descripcion // Guardar los datos en el campo contenido
                ]);
                $mensaje = 'Datos del documento actualizados exitosamente.';
            } else {
                // Crear nuevo documento
                Documento::create([
                    'personal_id' => $personal->id,
                    'tipo_documento_id' => $tipoDocumento->id,
                    'descripcion' => $request->descripcion,
                    'fecha_vencimiento' => $request->fecha_vencimiento,
                    'contenido' => $request->descripcion // Guardar los datos en el campo contenido
                ]);
                $mensaje = 'Datos del documento guardados exitosamente.';
            }
            
            return response()->json([
                'success' => true,
                'message' => $mensaje
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar los datos del documento: ' . $e->getMessage()
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

    // Ruta para actualizar personal - CORREGIDO: Agregado middleware de permisos
    Route::put('/{id}', function (\Illuminate\Http\Request $request, $id) {
        $personal = \App\Models\Personal::findOrFail($id);
        
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'curp' => 'required|string|size:18|unique:personal,curp,' . $personal->id,
            'categoria_personal_id' => 'required|exists:categorias_personal,id',
            'estatus' => 'required|in:activo,inactivo',
            'rfc' => 'nullable|string|max:13|unique:personal,rfc,' . $personal->id,
            'nss' => 'nullable|string|max:11',
            'direccion' => 'nullable|string|max:500',
            'puesto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'fecha_ingreso' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'notas' => 'nullable|string|max:1000'
        ]);

        $personal->update($request->all());

        return redirect()->route('personal.show', $personal->id)
            ->with('success', 'Personal actualizado exitosamente.');
    })->name('update')->middleware('permission:editar_personal');

    // Ruta para eliminar personal - CORREGIDO: Agregado middleware de permisos
    Route::delete('/{id}', function ($id) {
        $personal = \App\Models\Personal::findOrFail($id);
        $nombre = $personal->nombre_completo;
        
        $personal->delete();

        return redirect()->route('personal.index')
            ->with('success', "Personal '{$nombre}' eliminado exitosamente.");
    })->name('destroy')->middleware('permission:eliminar_personal');
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

// Ruta para vista de usuario (datos estáticos)
Route::get('/usuarios/{id}', function ($id) {
    return view('usuarios.show');
})->name('usuarios.show');
