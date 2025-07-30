<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonalRequest;
use App\Http\Requests\UpdatePersonalRequest;
use App\Models\CategoriaPersonal;
use App\Models\LogAccion;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
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

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                    ->orWhereHas('categoria', function ($cq) use ($search) {
                        $cq->where('nombre_categoria', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        // Orden
        $query->orderBy('nombre_completo');

        // Paginación
        $perPage = $request->get('per_page', 15);
        $perPage = max(1, min($perPage, 100)); // Asegurar que esté entre 1 y 100

        $personal = $query->paginate($perPage);

        // Respuesta híbrida
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Personal obtenido correctamente',
                'data' => $personal->toArray(),
            ]);
        }

        // Obtener opciones para filtros
        $categoriasOptions = CategoriaPersonal::select('id', 'nombre_categoria')
            ->orderBy('nombre_categoria')
            ->get();

        $estatusDisponibles = ['activo', 'inactivo'];

        return view('personal.index', compact(
            'personal',
            'categoriasOptions',
            'estatusDisponibles'
        ));
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

        // Respuesta híbrida
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'categorias' => $categorias,
                ],
            ]);
        }

        return view('personal.create', compact('categorias'));
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
            // Los datos ya vienen validados desde StorePersonalRequest
            $validated = $request->validated();

            // Crear el personal con los datos básicos y documentos
            $personal = Personal::create([
                'nombre_completo' => $validated['nombre_completo'],
                'estatus' => $validated['estatus'],
                'categoria_id' => $validated['categoria_personal_id'],
                'ine' => $validated['ine'] ?? null,
                'url_ine' => $validated['url_ine'] ?? null,
                'curp_numero' => $validated['curp_numero'] ?? null,
                'url_curp' => $validated['url_curp'] ?? null,
                'rfc' => $validated['rfc'] ?? null,
                'url_rfc' => $validated['url_rfc'] ?? null,
                'nss' => $validated['nss'] ?? null,
                'url_nss' => $validated['url_nss'] ?? null,
                'no_licencia' => $validated['no_licencia'] ?? null,
                'url_licencia' => $validated['url_licencia'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'url_comprobante_domicilio' => $validated['url_comprobante_domicilio'] ?? null,
            ]);

            $personal->load('categoria');

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_personal',
                'tabla_afectada' => 'personal',
                'registro_id' => $personal->id,
                'detalles' => "Personal creado: {$personal->nombre_completo} - {$personal->categoria->nombre_categoria}",
            ]);

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Personal creado exitosamente',
                    'data' => $personal,
                ], 201);
            }

            return redirect()
                ->route('personal.show', $personal)
                ->with('success', 'Personal creado exitosamente');
        } catch (\Exception $e) {
            // Respuesta de error híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el personal',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el personal: ' . $e->getMessage()]);
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
                
                // Mantener compatibilidad con nombres antiguos para la vista
                $tipoNombre = $documento->tipoDocumento->nombre_tipo_documento;
                if ($tipoNombre === 'Identificación Oficial') {
                    $documentosPorTipo['INE'] = $documento;
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
            $personal = Personal::with(['categoria', 'documentos.tipoDocumento'])->findOrFail($id);

            $categorias = CategoriaPersonal::select('id', 'nombre_categoria')
                ->orderBy('nombre_categoria')
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
                    ],
                ]);
            }

            return view('personal.edit', compact('personal', 'categorias', 'documentosPorTipo'));
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
     * Update the specified resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function update(UpdatePersonalRequest $request, $id)
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

        $validated = $request->validated();

        try {
            $personal = Personal::findOrFail($id);
            $oldData = $personal->toArray();

            // Actualizar datos básicos y documentos
            $personal->update([
                'nombre_completo' => $validated['nombre_completo'],
                'estatus' => $validated['estatus'],
                'categoria_id' => $validated['categoria_id'],
                'curp_numero' => $validated['curp_numero'] ?? $personal->curp_numero,
                'ine' => $validated['ine'] ?? $personal->ine,
                'url_ine' => $validated['url_ine'] ?? $personal->url_ine,
                'url_curp' => $validated['url_curp'] ?? $personal->url_curp,
                'rfc' => $validated['rfc'] ?? $personal->rfc,
                'url_rfc' => $validated['url_rfc'] ?? $personal->url_rfc,
                'nss' => $validated['nss'] ?? $personal->nss,
                'url_nss' => $validated['url_nss'] ?? $personal->url_nss,
                'no_licencia' => $validated['no_licencia'] ?? $personal->no_licencia,
                'url_licencia' => $validated['url_licencia'] ?? $personal->url_licencia,
                'direccion' => $validated['direccion'] ?? $personal->direccion,
                'url_comprobante_domicilio' => $validated['url_comprobante_domicilio'] ?? $personal->url_comprobante_domicilio,
            ]);

            // Manejar archivo CURP si se proporciona
            if ($request->hasFile('curp_file')) {
                $tipoDocumentoCurp = 9; // ID para documentos tipo CURP
                $archivo = $request->file('curp_file');
                $nombreArchivo = time() . '_' . $personal->id . '_' . $archivo->getClientOriginalName();
                $rutaArchivo = $archivo->storeAs('personal/documentos', $nombreArchivo, 'private');

                // Buscar si ya existe un documento CURP
                $documentoExistente = $personal->documentos()
                    ->where('tipo_documento_id', $tipoDocumentoCurp)
                    ->first();

                if ($documentoExistente) {
                    // Eliminar archivo anterior
                    if ($documentoExistente->ruta_archivo && \Storage::disk('private')->exists($documentoExistente->ruta_archivo)) {
                        \Storage::disk('private')->delete($documentoExistente->ruta_archivo);
                    }
                    
                    // Actualizar documento existente
                    $documentoExistente->update([
                        'ruta_archivo' => $rutaArchivo,
                        'contenido' => 'CURP'
                    ]);
                } else {
                    // Crear nuevo documento
                    $personal->documentos()->create([
                        'tipo_documento_id' => $tipoDocumentoCurp,
                        'ruta_archivo' => $rutaArchivo,
                        'contenido' => 'CURP'
                    ]);
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
            // Respuesta de error híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el personal',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el personal: ' . $e->getMessage()]);
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

            // Verificar si tiene usuario asociado
            if ($personal->usuario) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se puede eliminar el personal porque tiene un usuario asociado',
                    ], 400);
                }

                return redirect()
                    ->back()
                    ->withErrors(['error' => 'No se puede eliminar el personal porque tiene un usuario asociado']);
            }

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
}
