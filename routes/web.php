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

// Grupo de rutas para vehículos con autenticación
Route::middleware(['auth'])->group(function () {
    // Ruta para listar vehículos (vista estática)
    Route::get('/vehiculos', function () {
        return view('vehiculos.index');
    })->name('vehiculos.index')->middleware('permission:ver_vehiculos');

    // Ruta para mostrar formulario de crear vehículo (datos estáticos)
    Route::get('/vehiculos/create', function () {
        return view('vehiculos.create');
    })->name('vehiculos.create')->middleware('permission:crear_vehiculos');

    // Ruta para guardar nuevo vehículo (simulada con datos estáticos)
    Route::post('/vehiculos', function (\Illuminate\Http\Request $request) {
        // Simular validación básica sin tocar la base de datos
        $request->validate([
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'anio' => 'required|integer|min:1990|max:2025',
            'n_serie' => 'required|string|max:255',
            'placas' => 'required|string|max:255',
            'kilometraje_actual' => 'required|integer|min:0',
            'estatus_id' => 'required'
        ]);

        // Simular ID del vehículo creado
        $vehiculoId = rand(1, 100);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo creado exitosamente (simulación frontend).');
    })->name('vehiculos.store')->middleware('permission:crear_vehiculos');

    // Ruta para mostrar detalles de un vehículo (datos estáticos)
    Route::get('/vehiculos/{id}', function ($id) {
        // Crear objeto de vehículo con datos estáticos
        $vehiculo = (object) [
            'id' => $id,
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'anio' => 2022,
            'n_serie' => '1FTFW1ET5DFA12345',
            'placas' => 'ABC-123',
            'kilometraje_actual' => 45780,
            'estatus_id' => 1,
            'intervalo_km_motor' => 5000,
            'intervalo_km_transmision' => 40000,
            'intervalo_km_hidraulico' => 10000,
            'observaciones' => 'Vehículo en excelentes condiciones',
            'estatus' => (object) ['nombre' => 'Disponible']
        ];
        
        return view('vehiculos.show', compact('vehiculo'));
    })->name('vehiculos.show')->middleware('permission:ver_vehiculos');
});

// Ruta para mostrar formulario de editar vehículo (datos estáticos)
Route::middleware(['auth', 'permission:editar_vehiculos'])->get('/vehiculos/{id}/edit', function ($id) {
    // Crear objeto de vehículo con datos estáticos
    $vehiculo = (object) [
        'id' => $id,
        'marca' => 'Toyota',
        'modelo' => 'Hilux',
        'anio' => 2022,
        'n_serie' => '1FTFW1ET5DFA12345',
        'placas' => 'ABC-123',
        'kilometraje_actual' => 45780,
        'estatus_id' => 1,
        'intervalo_km_motor' => 5000,
        'intervalo_km_transmision' => 40000,
        'intervalo_km_hidraulico' => 10000,
        'observaciones' => 'Vehículo en excelentes condiciones'
    ];
    
    return view('vehiculos.edit', compact('vehiculo'));
})->name('vehiculos.edit');

// Ruta para actualizar vehículo (simulada con datos estáticos)
Route::middleware(['auth', 'permission:editar_vehiculos'])->put('/vehiculos/{id}', function (\Illuminate\Http\Request $request, $id) {
    // Simular validación básica sin tocar la base de datos
    $request->validate([
        'marca' => 'required|string|max:255',
        'modelo' => 'required|string|max:255',
        'anio' => 'required|integer|min:1990|max:2025',
        'n_serie' => 'required|string|max:255',
        'placas' => 'required|string|max:255',
        'kilometraje_actual' => 'required|integer|min:0',
        'estatus_id' => 'required'
    ]);

    return redirect()->route('vehiculos.show', $id)
        ->with('success', 'Vehículo actualizado exitosamente (simulación frontend).');
})->name('vehiculos.update');

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

    // Ruta para mostrar formulario de crear personal (datos estáticos)
    Route::get('/create', function () {
        // Categorías estáticas
        $categorias = collect([
            (object) ['id' => 1, 'nombre_categoria' => 'Técnico Especializado'],
            (object) ['id' => 2, 'nombre_categoria' => 'Operador'],
            (object) ['id' => 3, 'nombre_categoria' => 'Supervisor'],
            (object) ['id' => 4, 'nombre_categoria' => 'Administrador']
        ]);
        
        // Usuarios estáticos
        $usuarios = collect([
            (object) ['id' => 1, 'nombre_usuario' => 'admin'],
            (object) ['id' => 2, 'nombre_usuario' => 'supervisor01'],
            (object) ['id' => 3, 'nombre_usuario' => 'operador01']
        ]);
        
        return view('personal.create', compact('categorias', 'usuarios'));
    })->name('create')->middleware('permission:crear_personal');

    // Ruta para guardar nuevo personal
    Route::post('/', [App\Http\Controllers\PersonalManagementController::class, 'storeWeb'])
        ->name('store')
        ->middleware('permission:crear_personal');

    // Ruta para mostrar detalles de un personal (datos estáticos) - CORREGIDO: Agregado middleware de permisos
    Route::get('/{id}', function ($id) {
        // Crear objeto de personal con datos estáticos
        $personal = (object) [
            'id' => $id,
            'nombre_completo' => 'Marco Delgado Reyes',
            'estatus' => 'activo',
            'categoria' => (object) [
                'nombre_categoria' => 'Técnico Especializado'
            ],
            'user' => null,
            'documentos' => collect([])
        ];
        
        // Documentos estáticos organizados por tipo
        $documentosPorTipo = collect([]);
        
        return view('personal.show', compact('personal', 'documentosPorTipo'));
    })->name('show')->middleware('permission:ver_personal');

    // Ruta para subir documentos del personal - CORREGIDO: Agregado middleware de permisos
    Route::post('/{id}/documents/upload', function (Request $request, $id) {
        $personal = Personal::findOrFail($id);
        
        $request->validate([
            'archivo' => 'required|file|max:10240', // 10MB máximo
            'tipo_documento' => 'required|string',
            'descripcion' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date'
        ]);
        
        // Buscar el tipo de documento
        $tipoDocumento = CatalogoTipoDocumento::where('nombre_tipo_documento', $request->tipo_documento)->first();
        
        if (!$tipoDocumento) {
            return back()->with('error', 'Tipo de documento no válido.');
        }
        
        // Verificar si ya existe un documento de este tipo para este personal
        $documentoExistente = Documento::where('personal_id', $personal->id)
                                       ->where('tipo_documento_id', $tipoDocumento->id)
                                       ->first();
        
        try {
            // Subir archivo
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaArchivo = $archivo->storeAs('documentos/personal/' . $personal->id, $nombreArchivo, 'public');
            
            if ($documentoExistente) {
                // Actualizar documento existente
                $documentoExistente->update([
                    'ruta_archivo' => $rutaArchivo,
                    'descripcion' => $request->descripcion,
                    'fecha_vencimiento' => $request->fecha_vencimiento
                ]);
                $mensaje = 'Documento actualizado exitosamente.';
            } else {
                // Crear nuevo documento
                Documento::create([
                    'personal_id' => $personal->id,
                    'tipo_documento_id' => $tipoDocumento->id,
                    'ruta_archivo' => $rutaArchivo,
                    'descripcion' => $request->descripcion,
                    'fecha_vencimiento' => $request->fecha_vencimiento
                ]);
                $mensaje = 'Documento subido exitosamente.';
            }
            
            return back()->with('success', $mensaje);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al subir el documento: ' . $e->getMessage());
        }
    })->name('documents.upload')->middleware('permission:editar_personal');

    // Ruta para mostrar formulario de editar personal - CORREGIDO: Agregado middleware de permisos
    Route::get('/{id}/edit', function ($id) {
        $personal = \App\Models\Personal::findOrFail($id);
        $categorias = \App\Models\CategoriaPersonal::orderBy('nombre_categoria')->get();
        $usuarios = \App\Models\User::orderBy('nombre_usuario')->get();
        
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
