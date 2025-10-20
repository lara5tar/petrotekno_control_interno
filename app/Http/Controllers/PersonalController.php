<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonalRequest;
use App\Http\Requests\UpdatePersonalRequest;
use App\Models\CategoriaPersonal;
use App\Models\LogAccion;
use App\Models\Personal;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Verificar permisos
        if (! $request->user()->hasPermission('ver_personal')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "No tienes el permiso 'ver_personal' necesario para acceder a este recurso",
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $query = Personal::with('categoria');

        // Aplicar filtros - IGUAL QUE VEHICULOS
        if ($request->filled('buscar')) {
            $termino = $request->get('buscar');
            $query->buscar($termino);
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->get('estatus'));
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->get('categoria_id'));
        }

        $personal = $query->orderBy('id')->paginate(15);

        // Obtener opciones para filtros
        $categorias = CategoriaPersonal::select('id', 'nombre_categoria')
            ->orderBy('nombre_categoria')
            ->get();

        // Respuesta híbrida
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Personal obtenido correctamente',
                'data' => $personal->toArray(),
            ]);
        }

        return view('personal.index', compact('personal', 'categorias'));
    }

    /**
     * Show the form for creating a new resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function create(Request $request)
    {
        // Verificar permisos
        if (! $request->user()->hasPermission('crear_personal')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "No tienes el permiso 'crear_personal' necesario para acceder a este recurso",
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $categorias = CategoriaPersonal::select('id', 'nombre_categoria')
            ->orderBy('nombre_categoria')
            ->get();

        $roles = \App\Models\Role::select('id', 'nombre_rol')
            ->orderBy('nombre_rol')
            ->get();

        // Respuesta híbrida
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'categorias' => $categorias,
                    'roles' => $roles,
                ],
            ]);
        }

        return view('personal.create', compact('categorias', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function store(StorePersonalRequest $request)
    {
        // Verificar permisos
        if (! $request->user()->hasPermission('crear_personal')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "No tienes el permiso 'crear_personal' necesario para acceder a este recurso",
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección');
        }

        try {
            DB::beginTransaction();

            // Log temporal para debugging - datos del request antes de validación
            \Log::info('Datos raw del request:', [
                'all_data' => $request->all(),
                'has_crear_usuario' => $request->has('crear_usuario'),
                'crear_usuario_value' => $request->get('crear_usuario'),
            ]);

            // Los datos ya vienen validados desde StorePersonalRequest
            $validated = $request->validated();

            // Log para debugging
            \Log::info('Datos validados recibidos:', ['validated' => $validated]);
            \Log::info('Fecha inicio laboral:', ['fecha_inicio_laboral' => $validated['fecha_inicio_laboral'] ?? 'NO PRESENTE']);
            \Log::info('Fecha termino laboral:', ['fecha_termino_laboral' => $validated['fecha_termino_laboral'] ?? 'NO PRESENTE']);

            // Crear el personal con los datos básicos primero - usando nombres exactos de la BD
            $personal = Personal::create([
                'nombre_completo' => $validated['nombre_completo'],
                'estatus' => 'activo',
                'categoria_id' => $validated['categoria_personal_id'],
                'ine' => $validated['ine'] ?? null,
                'curp_numero' => $validated['curp_numero'] ?? null,
                'rfc' => $validated['rfc'] ?? null,
                'nss' => $validated['nss'] ?? null,
                'no_licencia' => $validated['no_licencia'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'cuenta_bancaria' => $validated['cuenta_bancaria'] ?? null,
                'fecha_inicio_laboral' => $validated['fecha_inicio_laboral'] ?? null,
                'fecha_termino_laboral' => $validated['fecha_termino_laboral'] ?? null,
            ]);

            \Log::info('Personal creado:', ['personal' => $personal->toArray()]);

            // Procesar archivos de documentos
            $archivosConfig = [
                'archivo_inicio_laboral' => ['tipo_id' => 17, 'campo_url' => 'url_inicio_laboral', 'contenido' => 'Documento de Inicio Laboral'],
                'archivo_termino_laboral' => ['tipo_id' => 18, 'campo_url' => 'url_termino_laboral', 'contenido' => 'Documento de Término Laboral'],
                'archivo_ine' => ['tipo_id' => 9, 'campo_url' => 'url_ine', 'contenido' => 'Identificación Oficial (INE)'],
                'archivo_curp' => ['tipo_id' => 10, 'campo_url' => 'url_curp', 'contenido' => 'CURP'],
                'archivo_rfc' => ['tipo_id' => 11, 'campo_url' => 'url_rfc', 'contenido' => 'RFC'],
                'archivo_nss' => ['tipo_id' => 12, 'campo_url' => 'url_nss', 'contenido' => 'NSS'],
                'archivo_licencia' => ['tipo_id' => 8, 'campo_url' => 'url_licencia', 'contenido' => 'Licencia de Conducir'],
                'archivo_comprobante_domicilio' => ['tipo_id' => 16, 'campo_url' => 'url_comprobante_domicilio', 'contenido' => 'Comprobante de Domicilio'],
                'archivo_cv' => ['tipo_id' => 1, 'campo_url' => null, 'contenido' => 'CV Profesional']
            ];

            // Log para depuración
            \Log::info('Archivos en request:', $request->allFiles());
            
            foreach ($archivosConfig as $campoArchivo => $config) {
                \Log::info("Verificando archivo: {$campoArchivo}", ['hasFile' => $request->hasFile($campoArchivo)]);
                
                if ($request->hasFile($campoArchivo)) {
                    \Log::info("Procesando archivo: {$campoArchivo}");
                    $archivo = $request->file($campoArchivo);
                    $nombreArchivo = time() . '_' . $personal->id . '_' . $archivo->getClientOriginalName();
                    $rutaArchivo = $archivo->storeAs('personal/documentos', $nombreArchivo, 'public');
                    \Log::info("Archivo guardado en: {$rutaArchivo}");

                    // Crear nuevo documento
                    $personal->documentos()->create([
                        'tipo_documento_id' => $config['tipo_id'],
                        'ruta_archivo' => $rutaArchivo,
                        'contenido' => $config['contenido']
                    ]);

                    // Actualizar URL en la tabla personal (solo si el campo existe)
                    if ($config['campo_url']) {
                        $personal->update([$config['campo_url'] => $rutaArchivo]);
                    }
                }
            }

            $personal->load('categoria');

            // Crear usuario del sistema si se solicitó
            $usuario = null;
            $mensajeUsuario = '';

            if (!empty($validated['crear_usuario']) && $validated['crear_usuario']) {
                \Log::info('Intentando crear usuario para personal', [
                    'crear_usuario' => $validated['crear_usuario'],
                    'email_usuario' => $validated['email_usuario'] ?? 'No proporcionado',
                    'rol_usuario' => $validated['rol_usuario'] ?? 'No proporcionado',
                    'tipo_password' => $validated['tipo_password'] ?? 'No proporcionado'
                ]);

                $usuarioService = new UsuarioService();

                try {
                    $datosUsuario = [
                        'email' => $validated['email_usuario'],
                        'rol_id' => $validated['rol_usuario'],
                        'tipo_password' => $validated['tipo_password'], // Siempre será 'aleatoria'
                    ];

                    $resultado = $usuarioService->crearUsuarioParaPersonal($personal, $datosUsuario);
                    $usuario = $resultado['usuario'];
                    $passwordGenerada = $resultado['password'];

                    // Log para debugging
                    \Log::info('Password generada:', ['password' => $passwordGenerada]);

                    // SIEMPRE mostrar la contraseña generada
                    $mensajeUsuario = " Usuario creado exitosamente.<br><div class='bg-yellow-50 border border-yellow-200 rounded p-3 mt-2'><strong>🔑 Contraseña generada:</strong> <span class='font-mono text-lg font-bold text-red-600'>{$passwordGenerada}</span><br><small class='text-gray-600'>⚠️ Guarde esta contraseña en un lugar seguro. También se ha enviado por email.</small></div>";

                    // Log adicional para creación de usuario
                    LogAccion::create([
                        'usuario_id' => Auth::id(),
                        'accion' => 'crear_usuario_personal',
                        'tabla_afectada' => 'users',
                        'registro_id' => $usuario->id,
                        'detalles' => "Usuario creado para personal: {$personal->nombre_completo} - Email: {$usuario->email}",
                    ]);
                } catch (\Exception $e) {
                    // Si falla la creación del usuario, continuar pero informar
                    $mensajeUsuario = ' Advertencia: ' . $e->getMessage();
                }
            }

            // Log de auditoría para personal
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_personal',
                'tabla_afectada' => 'personal',
                'registro_id' => $personal->id,
                'detalles' => "Personal creado: {$personal->nombre_completo} - {$personal->categoria->nombre_categoria}",
            ]);

            DB::commit();

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Personal creado exitosamente' . $mensajeUsuario,
                    'data' => [
                        'personal' => $personal,
                        'usuario' => $usuario,
                    ],
                ], 201);
            }

            return redirect()
                ->route('personal.show', $personal)
                ->with('success', 'Personal creado exitosamente' . $mensajeUsuario);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log del error técnico para debugging
            \App\Services\UserFriendlyErrorService::logTechnicalError($e, 'PersonalController@store');

            // Mensaje amigable para el usuario
            $userMessage = \App\Services\UserFriendlyErrorService::getOperationMessage('crear_personal', $e);

            // Respuesta de error híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $userMessage,
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $userMessage]);
        }
    }

    /**
     * Display the specified resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function show(Request $request, $id)
    {

        // Verificar permisos
        if (! $request->user()->hasPermission('ver_personal')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "No tienes el permiso 'ver_personal' necesario para acceder a este recurso",
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección');
        }

        try {
            $personal = Personal::with([
                'categoria',
                'usuario',
                'documentos' => function ($query) {
                    $query->with('tipoDocumento')
                        ->select('id', 'tipo_documento_id', 'descripcion', 'fecha_vencimiento', 'personal_id', 'contenido', 'created_at', 'updated_at');
                }
            ])->findOrFail($id);

            // Organizar documentos por tipo con mapeo para la vista
            $documentosPorTipo = [];
            $tiposDocumentoMap = [
                8 => 'identificacion',  // Identificación Oficial (INE)
                9 => 'curp',           // CURP
                10 => 'rfc',           // RFC
                11 => 'nss',           // NSS
                7 => 'licencia',       // Licencia de Conducir
                28 => 'cv',            // CV Profesional
                30 => 'domicilio',     // Comprobante Domicilio Original (campo dirección)
                16 => 'domicilio_actual' // Comprobante Domicilio Actual
            ];

            foreach ($personal->documentos as $documento) {
                $tipoId = $documento->tipo_documento_id;
                if (isset($tiposDocumentoMap[$tipoId])) {
                    $campo = $tiposDocumentoMap[$tipoId];
                    $documentosPorTipo[$campo] = $documento;
                }

                // Mantener compatibilidad con nombres antiguos para la vista
                $tipoNombre = $documento->tipoDocumento->nombre_tipo_documento;
                if ($tipoNombre === 'Identificación Oficial (INE)') {
                    $documentosPorTipo['INE'] = $documento;
                } elseif ($tipoNombre === 'CV Profesional') {
                    $documentosPorTipo['CV Profesional'] = $documento;
                } elseif ($tipoNombre === 'Licencia de Conducir') {
                    $documentosPorTipo['Licencia de Conducir'] = $documento;
                } elseif (str_contains($tipoNombre, 'Comprobante')) {
                    $documentosPorTipo['Comprobante de Domicilio'] = $documento;
                } else {
                    // Solo asignar si no existe ya para evitar arrays
                    if (!isset($documentosPorTipo[$tipoNombre])) {
                        $documentosPorTipo[$tipoNombre] = $documento;
                    }
                }
            }

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Personal obtenido exitosamente',
                    'data' => $personal,
                ]);
            }

            return view('personal.show', compact('personal', 'documentosPorTipo'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personal no encontrado',
                ], 404);
            }

            return redirect()
                ->route('personal.index')
                ->withErrors(['error' => 'Personal no encontrado']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function edit(Request $request, $id)
    {
        // Verificar permisos
        if (! $request->user()->hasPermission('editar_personal')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "No tienes el permiso 'editar_personal' necesario para acceder a este recurso",
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección');
        }

        try {
            $personal = Personal::with(['categoria', 'documentos.tipoDocumento', 'usuario.rol'])->findOrFail($id);

            $categorias = CategoriaPersonal::select('id', 'nombre_categoria')
                ->orderBy('nombre_categoria')
                ->get();

            // Obtener roles disponibles
            $roles = \App\Models\Role::select('id', 'nombre_rol')
                ->orderBy('nombre_rol')
                ->get();

            // Organizar documentos por tipo con mapeo para la vista
            $documentosPorTipo = [];
            $tiposDocumentoMap = [
                8 => 'identificacion',  // Identificación Oficial
                9 => 'curp',           // CURP
                10 => 'rfc',           // RFC
                11 => 'nss',           // NSS
                7 => 'licencia',       // Licencia de Conducir
                15 => 'cv',            // CV Profesional
                16 => 'domicilio'      // Comprobante de Domicilio
            ];

            foreach ($personal->documentos as $documento) {
                $tipoId = $documento->tipo_documento_id;
                if (isset($tiposDocumentoMap[$tipoId])) {
                    $campo = $tiposDocumentoMap[$tipoId];
                    $documentosPorTipo[$campo] = $documento;
                }
            }

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'personal' => $personal,
                        'categorias' => $categorias,
                        'roles' => $roles,
                    ],
                ]);
            }

            return view('personal.edit', compact('personal', 'categorias', 'documentosPorTipo', 'roles'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Personal no encontrado en edit:', ['id' => $id]);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personal no encontrado',
                ], 404);
            }

            return redirect()
                ->route('personal.index')
                ->withErrors(['error' => 'Personal no encontrado']);
        }
    }

    /**
     * Update the specified resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function update(UpdatePersonalRequest $request, $id)
    {
        \Log::info('PersonalController::update - Petición recibida', ['id' => $id, 'method' => $request->method()]);
        
        // Verificar permisos
        if (! $request->user()->hasPermission('editar_personal')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "No tienes el permiso 'editar_personal' necesario para acceder a este recurso",
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $validated = $request->validated();

        try {
            $personal = Personal::findOrFail($id);
            $oldData = $personal->toArray();

            // Actualizar datos básicos y documentos
            $personal->update([
                'nombre_completo' => $validated['nombre_completo'],
                'estatus' => $validated['estatus'],
                'categoria_id' => $validated['categoria_id'],
                'curp_numero' => $validated['curp'] ?? $personal->curp_numero,
                'ine' => $validated['numero_ine'] ?? $personal->ine,
                'rfc' => $validated['rfc'] ?? $personal->rfc,
                'nss' => $validated['nss'] ?? $personal->nss,
                'no_licencia' => $validated['numero_licencia'] ?? $personal->no_licencia,
                'direccion' => $validated['direccion_completa'] ?? $personal->direccion,
                'cuenta_bancaria' => $validated['cuenta_bancaria'] ?? $personal->cuenta_bancaria,
                'fecha_inicio_laboral' => $validated['fecha_inicio_laboral'] ?? $personal->fecha_inicio_laboral,
                'fecha_termino_laboral' => $validated['fecha_termino_laboral'] ?? $personal->fecha_termino_laboral,
            ]);

            // Manejar archivos de documentos
            $archivosConfig = [
                'archivo_inicio_laboral' => ['tipo_id' => 17, 'campo_url' => 'url_inicio_laboral', 'contenido' => 'Documento de Inicio Laboral'],
                'archivo_termino_laboral' => ['tipo_id' => 18, 'campo_url' => 'url_termino_laboral', 'contenido' => 'Documento de Término Laboral'],
                'archivo_ine' => ['tipo_id' => 9, 'campo_url' => 'url_ine', 'contenido' => 'Identificación Oficial (INE)'],
                'archivo_curp' => ['tipo_id' => 10, 'campo_url' => 'url_curp', 'contenido' => 'CURP'],
                'archivo_rfc' => ['tipo_id' => 11, 'campo_url' => 'url_rfc', 'contenido' => 'RFC'],
                'archivo_nss' => ['tipo_id' => 12, 'campo_url' => 'url_nss', 'contenido' => 'NSS'],
                'archivo_licencia' => ['tipo_id' => 8, 'campo_url' => 'url_licencia', 'contenido' => 'Licencia de Conducir'],
                'archivo_comprobante_domicilio' => ['tipo_id' => 16, 'campo_url' => 'url_comprobante_domicilio', 'contenido' => 'Comprobante de Domicilio'],
                'archivo_cv' => ['tipo_id' => 1, 'campo_url' => 'url_cv', 'contenido' => 'CV Profesional']
            ];

            // Log para depuración
            \Log::info('Archivos en request:', $request->allFiles());
            
            foreach ($archivosConfig as $campoArchivo => $config) {
                \Log::info("Verificando archivo: {$campoArchivo}", ['hasFile' => $request->hasFile($campoArchivo)]);
                
                if ($request->hasFile($campoArchivo)) {
                    \Log::info("Procesando archivo: {$campoArchivo}");
                    $archivo = $request->file($campoArchivo);
                    $nombreArchivo = time() . '_' . $personal->id . '_' . $archivo->getClientOriginalName();
                    $rutaArchivo = $archivo->storeAs('personal/documentos', $nombreArchivo, 'public');
                    \Log::info("Archivo guardado en: {$rutaArchivo}");

                    // Buscar si ya existe un documento de este tipo
                    $documentoExistente = $personal->documentos()
                        ->where('tipo_documento_id', $config['tipo_id'])
                        ->first();

                    if ($documentoExistente) {
                        // Eliminar archivo anterior
                        if ($documentoExistente->ruta_archivo && \Storage::disk('public')->exists($documentoExistente->ruta_archivo)) {
                            \Storage::disk('public')->delete($documentoExistente->ruta_archivo);
                        }

                        // Actualizar documento existente
                        $documentoExistente->update([
                            'ruta_archivo' => $rutaArchivo,
                            'contenido' => $config['contenido']
                        ]);
                    } else {
                        // Crear nuevo documento
                        $personal->documentos()->create([
                            'tipo_documento_id' => $config['tipo_id'],
                            'ruta_archivo' => $rutaArchivo,
                            'contenido' => $config['contenido']
                        ]);
                    }

                    // Actualizar URL en la tabla personal (solo si el campo existe)
                    if ($config['campo_url']) {
                        $personal->update([$config['campo_url'] => $rutaArchivo]);
                    }
                }
            }

            $personal->load('categoria');

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_personal',
                'tabla_afectada' => 'personal',
                'registro_id' => $personal->id,
                'detalles' => "Personal actualizado: {$personal->nombre_completo} - {$personal->categoria->nombre_categoria}",
            ]);

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Personal actualizado exitosamente',
                    'data' => $personal,
                ]);
            }

            return redirect()
                ->route('personal.show', $personal)
                ->with('success', 'Personal actualizado exitosamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personal no encontrado',
                ], 404);
            }

            return redirect()
                ->route('personal.index')
                ->withErrors(['error' => 'Personal no encontrado']);
        } catch (\Exception $e) {
            // Log del error técnico para debugging
            \App\Services\UserFriendlyErrorService::logTechnicalError($e, 'PersonalController@update');

            // Mensaje amigable para el usuario
            $userMessage = \App\Services\UserFriendlyErrorService::getOperationMessage('actualizar_personal', $e);

            // Respuesta de error híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $userMessage,
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $userMessage]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function destroy(Request $request, $id)
    {
        // Verificar permisos
        if (! $request->user()->hasPermission('eliminar_personal')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "No tienes el permiso 'eliminar_personal' necesario para acceder a este recurso",
                ], 403);
            }

            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección');
        }

        try {
            $personal = Personal::findOrFail($id);

            // El PersonalObserver se encarga de eliminar automáticamente el usuario asociado
            // por lo que ya no es necesario verificar si tiene usuario asociado

            // Guardamos información para el log antes de eliminar
            $infoPersonal = "{$personal->nombre_completo} - {$personal->categoria->nombre_categoria}";

            $personal->delete();

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_personal',
                'tabla_afectada' => 'personal',
                'registro_id' => $id,
                'detalles' => "Personal eliminado: {$infoPersonal}",
            ]);

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Personal eliminado exitosamente',
                ]);
            }

            return redirect()
                ->route('personal.index')
                ->with('success', 'Personal eliminado exitosamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personal no encontrado',
                ], 404);
            }

            return redirect()
                ->route('personal.index')
                ->withErrors(['error' => 'Personal no encontrado']);
        } catch (\Exception $e) {
            // Respuesta de error híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el personal',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withErrors(['error' => 'Error al eliminar el personal: ' . $e->getMessage()]);
        }
    }

    /**
     * Descargar reporte de personal filtrado en formato PDF
     */
    public function descargarReportePdf(Request $request)
    {
        // Verificar permisos
        if (! $request->user()->hasPermission('ver_personal')) {
            abort(403, 'No tienes permiso para descargar reportes de personal');
        }

        // Aplicar los mismos filtros que en el index
        $query = Personal::with(['categoria']);

        $buscar = $request->get('buscar');
        $categoriaId = $request->get('categoria_id');
        $estatus = $request->get('estatus');

        if (!empty($buscar) && trim($buscar) !== '') {
            $termino = trim($buscar);
            $query->where(function ($q) use ($termino) {
                $q->where('nombre_completo', 'like', "%{$termino}%")
                  ->orWhere('rfc', 'like', "%{$termino}%")
                  ->orWhere('curp', 'like', "%{$termino}%")
                  ->orWhere('nss', 'like', "%{$termino}%");
            });
        }

        if (!empty($categoriaId) && trim($categoriaId) !== '') {
            $query->where('categoria_id', trim($categoriaId));
        }

        if (!empty($estatus) && trim($estatus) !== '') {
            $query->where('estatus', trim($estatus));
        }

        // Obtener personal
        $personal = $query->orderBy('id')->limit(5000)->get();

        // Preparar estadísticas
        $estadisticas = [
            'total' => $personal->count(),
            'activos' => $personal->where('estatus', 'activo')->count(),
            'inactivos' => $personal->where('estatus', 'inactivo')->count(),
        ];

        // Preparar filtros aplicados
        $filtrosAplicados = [
            'buscar' => $request->get('buscar'),
            'categoria_id' => $request->get('categoria_id'),
            'estatus' => $request->get('estatus'),
        ];

        // Generar PDF usando DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reportes.personal-filtrado', [
            'personal' => $personal,
            'estadisticas' => $estadisticas,
            'filtros' => $filtrosAplicados,
        ]);

        return $pdf->download('reporte-personal-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    /**
     * Descargar reporte de personal filtrado en formato Excel
     */
    public function descargarReporteExcel(Request $request)
    {
        // Verificar permisos
        if (! $request->user()->hasPermission('ver_personal')) {
            abort(403, 'No tienes permiso para descargar reportes de personal');
        }

        // Aplicar los mismos filtros que en el index
        $query = Personal::with(['categoria']);

        $buscar = $request->get('buscar');
        $categoriaId = $request->get('categoria_id');
        $estatus = $request->get('estatus');

        if (!empty($buscar) && trim($buscar) !== '') {
            $termino = trim($buscar);
            $query->where(function ($q) use ($termino) {
                $q->where('nombre_completo', 'like', "%{$termino}%")
                  ->orWhere('rfc', 'like', "%{$termino}%")
                  ->orWhere('curp_numero', 'like', "%{$termino}%")
                  ->orWhere('nss', 'like', "%{$termino}%")
                  ->orWhere('ine', 'like', "%{$termino}%");
            });
        }

        if (!empty($categoriaId) && trim($categoriaId) !== '') {
            $query->where('categoria_id', trim($categoriaId));
        }

        if (!empty($estatus) && trim($estatus) !== '') {
            $query->where('estatus', trim($estatus));
        }

        // Obtener personal
        $personal = $query->orderBy('id')->limit(10000)->get();

        // Preparar datos para Excel
    $data = [];
    $data[] = ['id', 'Nombre Completo', 'Categoría', 'RFC', 'CURP', 'NSS', 'INE', 'Licencia', 'Dirección', 'Estado', 'Fecha Registro'];
        
        foreach ($personal as $persona) {
            $data[] = [
                $persona->id,
                $persona->nombre_completo,
                $persona->categoria->nombre_categoria ?? 'Sin categoría',
                $persona->rfc ?: 'N/A',
                $persona->curp_numero ?: 'N/A',
                $persona->nss ?: 'N/A',
                $persona->ine ?: 'N/A',
                $persona->no_licencia ?: 'N/A',
                $persona->direccion ?: 'N/A',
                ucfirst($persona->estatus),
                $persona->created_at->format('d/m/Y'),
            ];
        }

        // Crear archivo Excel simple
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Agregar título
        $sheet->setCellValue('A1', 'Reporte de Personal');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Agregar fecha
        $sheet->setCellValue('A2', 'Fecha: ' . now()->format('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:K2');
        
        // Agregar datos
        $row = 4;
        foreach ($data as $rowData) {
            $col = 'A';
            foreach ($rowData as $cellData) {
                $sheet->setCellValue($col . $row, $cellData);
                $col++;
            }
            $row++;
        }
        
        // Estilo de encabezados
        $sheet->getStyle('A4:K4')->getFont()->setBold(true);
        $sheet->getStyle('A4:K4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Ajustar ancho de columnas
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Descargar
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'reporte-personal-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
