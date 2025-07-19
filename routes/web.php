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
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Ruta para listar vehículos (vista estática)
Route::get('/vehiculos', function () {
    return view('vehiculos.index');
})->name('vehiculos.index');

// Rutas para Personal CRUD
Route::middleware('auth')->prefix('personal')->name('personal.')->group(function () {
    // Ruta para listar personal
    Route::get('/', function () {
        // Obtener categorías para el filtro
        $categorias = \App\Models\CategoriaPersonal::orderBy('nombre_categoria')->get();
        
        // Query base del personal con relaciones
        $query = \App\Models\Personal::with(['categoria', 'usuario']);
        
        // Aplicar filtros
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhereHas('categoria', function($q) use ($search) {
                      $q->where('nombre_categoria', 'like', "%{$search}%");
                  });
            });
        }
        
        if (request('categoria_id')) {
            $query->where('categoria_personal_id', request('categoria_id'));
        }
        
        if (request('estatus')) {
            $query->where('estatus', request('estatus'));
        }
        
        // Obtener resultados paginados
        $personal = $query->orderBy('nombre_completo')->paginate(15);
        
        return view('personal.index', compact('personal', 'categorias'));
    })->name('index');

    // Ruta para mostrar formulario de crear personal
    Route::get('/create', function () {
        $categorias = \App\Models\CategoriaPersonal::orderBy('nombre_categoria')->get();
        $usuarios = \App\Models\User::orderBy('nombre_usuario')->get();
        
        return view('personal.create', compact('categorias', 'usuarios'));
    })->name('create');

    // Ruta para guardar nuevo personal
    Route::post('/', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'curp' => 'required|string|size:18|unique:personal,curp',
            'categoria_personal_id' => 'required|exists:categorias_personal,id',
            'estatus' => 'required|in:activo,inactivo',
            'rfc' => 'nullable|string|max:13|unique:personal,rfc',
            'nss' => 'nullable|string|max:11',
            'direccion' => 'nullable|string|max:500',
            'puesto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'fecha_ingreso' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'notas' => 'nullable|string|max:1000'
        ]);

        $personal = \App\Models\Personal::create($request->all());

        return redirect()->route('personal.show', $personal->id)
            ->with('success', 'Personal creado exitosamente.');
    })->name('store');

    // Ruta para mostrar detalles de un personal
    Route::get('/{id}', function ($id) {
        $personal = Personal::with([
            'user', 
            'categoriaPersonal',
            'documentos.tipoDocumento'
        ])->findOrFail($id);
        
        // Organizar documentos por tipo
        $documentosPorTipo = $personal->documentos->keyBy(function($doc) {
            return $doc->tipoDocumento->nombre_tipo_documento ?? 'Sin Tipo';
        });
        
        return view('personal.show', compact('personal', 'documentosPorTipo'));
    })->name('show');

    // Ruta para subir documentos del personal
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
    })->name('documents.upload');

    // Ruta para mostrar formulario de editar personal
    Route::get('/{id}/edit', function ($id) {
        $personal = \App\Models\Personal::findOrFail($id);
        $categorias = \App\Models\CategoriaPersonal::orderBy('nombre_categoria')->get();
        $usuarios = \App\Models\User::orderBy('nombre_usuario')->get();
        
        return view('personal.edit', compact('personal', 'categorias', 'usuarios'));
    })->name('edit');

    // Ruta para actualizar personal
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
    })->name('update');

    // Ruta para eliminar personal
    Route::delete('/{id}', function ($id) {
        $personal = \App\Models\Personal::findOrFail($id);
        $nombre = $personal->nombre_completo;
        
        $personal->delete();

        return redirect()->route('personal.index')
            ->with('success', "Personal '{$nombre}' eliminado exitosamente.");
    })->name('destroy');
});

// Rutas web para obtener datos (proxy a los modelos)
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
    })->name('web-api.categorias-personal');

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
    })->name('web-api.personal');
});
