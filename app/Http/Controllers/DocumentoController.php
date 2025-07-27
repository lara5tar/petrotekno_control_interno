<?php

namespace App\Http\Controllers;

use App\Models\CatalogoTipoDocumento;
use App\Models\Documento;
use App\Models\LogAccion;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\Vehiculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DocumentoController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['index', 'store', 'show', 'update', 'destroy', 'proximosAVencer', 'vencidos']);
        $this->middleware('auth')->only(['create', 'edit']);
        $this->middleware('permission:ver_documentos')->only(['index', 'show']);
        $this->middleware('permission:crear_documentos')->only(['create', 'store']);
        $this->middleware('permission:editar_documentos')->only(['edit', 'update']);
        $this->middleware('permission:eliminar_documentos')->only('destroy');
    }

    /**
     * Verificar permisos del usuario autenticado
     */
    private function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        // Cargar la relación de rol y permisos si no está cargada
        if (! $user->relationLoaded('rol')) {
            $user->load('rol.permisos');
        } elseif ($user->rol && ! $user->rol->relationLoaded('permisos')) {
            $user->rol->load('permisos');
        }

        return $user->hasPermission($permission);
    }

    /**
     * Display a listing of the resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function index(Request $request): JsonResponse|View|RedirectResponse
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_documentos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para ver documentos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para ver documentos']);
        }

        try {
            $query = Documento::with([
                'tipoDocumento:id,nombre_tipo_documento,requiere_vencimiento',
                'vehiculo:id,marca,modelo,placas',
                'personal:id,nombre_completo',
                'obra:id,nombre_obra',
            ]);

            // Filtros
            if ($request->filled('tipo_documento_id')) {
                $query->porTipo($request->tipo_documento_id);
            }

            if ($request->filled('vehiculo_id')) {
                $query->deVehiculo($request->vehiculo_id);
            }

            if ($request->filled('personal_id')) {
                $query->dePersonal($request->personal_id);
            }

            if ($request->filled('obra_id')) {
                $query->deObra($request->obra_id);
            }

            if ($request->filled('estado')) {
                switch ($request->estado) {
                    case 'vencidos':
                        $query->vencidos();
                        break;
                    case 'proximos_a_vencer':
                        $dias = $request->get('dias_vencimiento', 30);
                        $query->proximosAVencer($dias);
                        break;
                }
            }

            // Búsqueda por descripción
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('descripcion', 'like', "%{$search}%")
                        ->orWhereHas('tipoDocumento', function ($tq) use ($search) {
                            $tq->where('nombre_tipo_documento', 'like', "%{$search}%");
                        });
                });
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $documentos = $query->paginate($perPage);

            // Datos para vistas Blade
            $tiposDocumento = CatalogoTipoDocumento::all();
            $vehiculos = Vehiculo::select('id', 'marca', 'modelo', 'placas')->get();
            $personal = Personal::select('id', 'nombre_completo')->where('estatus', 'activo')->get();
            $obras = Obra::select('id', 'nombre_obra')->where('estatus', 'activa')->get();

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                // Agregar información adicional a cada documento
                $documentos->getCollection()->transform(function ($documento) {
                    // Crear un array con las propiedades calculadas
                    $docArray = $documento->toArray();
                    $docArray['estado'] = $documento->estado;
                    $docArray['dias_hasta_vencimiento'] = $documento->dias_hasta_vencimiento;
                    $docArray['esta_vencido'] = $documento->esta_vencido;

                    return $docArray;
                });

                return response()->json([
                    'success' => true,
                    'data' => $documentos,
                    'meta' => [
                        'total_documentos' => $documentos->total(),
                        'filtros_aplicados' => $request->except(['page', 'per_page']),
                    ],
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return view('documentos.index', compact(
                'documentos',
                'tiposDocumento',
                'vehiculos',
                'personal',
                'obras'
            ));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener los documentos',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al obtener los documentos: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * Patrón Híbrido: Solo Blade (View)
     */
    public function create(): View|RedirectResponse
    {
        // Verificar permisos
        if (! $this->hasPermission('crear_documentos')) {
            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para crear documentos']);
        }

        try {
            // Datos necesarios para el formulario
            $tiposDocumento = CatalogoTipoDocumento::all();
            $vehiculos = Vehiculo::select('id', 'marca', 'modelo', 'placas')->get();
            $personal = Personal::select('id', 'nombre_completo')->where('estatus', 'activo')->get();
            $obras = Obra::select('id', 'nombre_obra')->where('estatus', 'activa')->get();

            return view('documentos.create', compact(
                'tiposDocumento',
                'vehiculos',
                'personal',
                'obras'
            ));
        } catch (\Exception $e) {
            return redirect()->route('documentos.index')->withErrors(['error' => 'Error al cargar el formulario: ' . $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (! $this->hasPermission('crear_documentos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para crear documentos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para crear documentos']);
        }

        try {
            // Validación manual porque no tenemos StoreDocumentoRequest híbrido
            $validated = $request->validate([
                'tipo_documento_id' => 'required|exists:catalogo_tipos_documento,id',
                'descripcion' => 'nullable|string|max:1000',
                'fecha_vencimiento' => 'nullable|date|after_or_equal:today',
                'vehiculo_id' => 'nullable|exists:vehiculos,id',
                'personal_id' => 'nullable|exists:personal,id',
                'obra_id' => 'nullable|exists:obras,id',
                'mantenimiento_id' => 'nullable|exists:mantenimientos,id',
                'ruta_archivo' => 'nullable|string|max:500',
                'archivo' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,txt,xls,xlsx',
                'contenido' => 'nullable|json',
            ]);

            // Validación de múltiples asociaciones
            $associations = collect(['vehiculo_id', 'personal_id', 'obra_id', 'mantenimiento_id'])
                ->filter(fn ($key) => ! empty($validated[$key]))
                ->count();

            if ($associations > 1) {
                throw ValidationException::withMessages([
                    'multiple_associations' => 'Un documento solo puede asociarse a una entidad (vehículo, personal, obra o mantenimiento).',
                ]);
            }

            // Validación adicional: si el tipo de documento requiere vencimiento
            $tipoDocumento = CatalogoTipoDocumento::find($validated['tipo_documento_id']);
            if ($tipoDocumento && $tipoDocumento->requiere_vencimiento && empty($validated['fecha_vencimiento'])) {
                throw ValidationException::withMessages([
                    'fecha_vencimiento' => 'La fecha de vencimiento es requerida para este tipo de documento.',
                ]);
            }

            // Manejo de archivo si se proporciona
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . Str::slug($archivo->getClientOriginalName(), '_');
                $rutaArchivo = $archivo->storeAs('documentos', $nombreArchivo, 'public');
                $validated['ruta_archivo'] = $rutaArchivo;
            }

            $documento = Documento::create($validated);
            $documento->load([
                'tipoDocumento:id,nombre_tipo_documento,requiere_vencimiento',
                'vehiculo:id,marca,modelo,placas',
                'personal:id,nombre_completo',
                'obra:id,nombre_obra',
            ]);

            // Registrar en log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_documento',
                'tabla_afectada' => 'documentos',
                'registro_id' => $documento->id,
                'detalles' => json_encode([
                    'tipo_documento' => $documento->tipoDocumento->nombre_tipo_documento ?? 'N/A',
                    'tiene_archivo' => ! empty($documento->ruta_archivo),
                ]),
            ]);

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                // Crear un array con las propiedades calculadas
                $docArray = $documento->toArray();
                $docArray['estado'] = $documento->estado;
                $docArray['dias_hasta_vencimiento'] = $documento->dias_hasta_vencimiento;

                return response()->json([
                    'success' => true,
                    'message' => 'Documento creado exitosamente',
                    'data' => $docArray,
                ], 201);
            }

            // Si es solicitud web (navegador tradicional)
            return redirect()->route('documentos.show', $documento->id)->with('success', 'Documento creado exitosamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Limpiar archivo si se subió y hubo error
            if (isset($rutaArchivo)) {
                Storage::disk('public')->delete($rutaArchivo);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el documento',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al crear el documento: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     * Patrón Híbrido: API (JSON) + Blade (View)
     */
    public function show(Request $request, $id): JsonResponse|View|RedirectResponse
    {
        // Verificar permisos
        if (! $this->hasPermission('ver_documentos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para ver documentos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para ver documentos']);
        }

        try {
            $documento = Documento::with([
                'tipoDocumento:id,nombre_tipo_documento,descripcion,requiere_vencimiento',
                'vehiculo:id,marca,modelo,placas,n_serie',
                'personal:id,nombre_completo,categoria_id',
                'obra:id,nombre_obra,estatus,fecha_inicio,fecha_fin',
            ])->findOrFail($id);

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                // Crear un array con las propiedades calculadas
                $docArray = $documento->toArray();
                $docArray['estado'] = $documento->estado;
                $docArray['dias_hasta_vencimiento'] = $documento->dias_hasta_vencimiento;
                $docArray['esta_vencido'] = $documento->esta_vencido;

                return response()->json([
                    'success' => true,
                    'data' => $docArray,
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return view('documentos.show', compact('documento'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado',
                ], 404);
            }

            return redirect()->route('documentos.index')->withErrors(['error' => 'Documento no encontrado']);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener el documento',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al obtener el documento: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Patrón Híbrido: Solo Blade (View)
     */
    public function edit(Request $request, $id): View|RedirectResponse
    {
        // Verificar permisos
        if (! $this->hasPermission('editar_documentos')) {
            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para editar documentos']);
        }

        try {
            $documento = Documento::with([
                'tipoDocumento',
                'vehiculo',
                'personal',
                'obra',
            ])->findOrFail($id);

            // Datos necesarios para el formulario
            $tiposDocumento = CatalogoTipoDocumento::all();
            $vehiculos = Vehiculo::select('id', 'marca', 'modelo', 'placas')->get();
            $personal = Personal::select('id', 'nombre_completo')->where('estatus', 'activo')->get();
            $obras = Obra::select('id', 'nombre_obra')->where('estatus', 'activa')->get();

            return view('documentos.edit', compact(
                'documento',
                'tiposDocumento',
                'vehiculos',
                'personal',
                'obras'
            ));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('documentos.index')->withErrors(['error' => 'Documento no encontrado']);
        } catch (\Exception $e) {
            return redirect()->route('documentos.index')->withErrors(['error' => 'Error al cargar el formulario: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function update(Request $request, $id): JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (! $this->hasPermission('editar_documentos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para editar documentos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para editar documentos']);
        }

        try {
            $documento = Documento::findOrFail($id);

            $validated = $request->validate([
                'tipo_documento_id' => 'sometimes|exists:catalogo_tipos_documento,id',
                'descripcion' => 'nullable|string|max:1000',
                'fecha_vencimiento' => 'nullable|date|after_or_equal:today',
                'vehiculo_id' => 'nullable|exists:vehiculos,id',
                'personal_id' => 'nullable|exists:personal,id',
                'obra_id' => 'nullable|exists:obras,id',
                'mantenimiento_id' => 'nullable|exists:mantenimientos,id',
                'ruta_archivo' => 'nullable|string|max:500',
                'archivo' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,txt,xls,xlsx',
                'contenido' => 'nullable|json',
            ]);

            // Validación de múltiples asociaciones
            $associations = collect(['vehiculo_id', 'personal_id', 'obra_id', 'mantenimiento_id'])
                ->filter(fn ($key) => ! empty($validated[$key]))
                ->count();

            if ($associations > 1) {
                throw ValidationException::withMessages([
                    'multiple_associations' => 'Un documento solo puede asociarse a una entidad (vehículo, personal, obra o mantenimiento).',
                ]);
            }

            // Manejo de archivo si se proporciona
            if ($request->hasFile('archivo')) {
                // Eliminar archivo anterior si existe
                if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                    Storage::disk('public')->delete($documento->ruta_archivo);
                }

                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . Str::slug($archivo->getClientOriginalName(), '_');
                $rutaArchivo = $archivo->storeAs('documentos', $nombreArchivo, 'public');
                $validated['ruta_archivo'] = $rutaArchivo;
            }

            $documento->update($validated);
            $documento->load([
                'tipoDocumento:id,nombre_tipo_documento,requiere_vencimiento',
                'vehiculo:id,marca,modelo,placas',
                'personal:id,nombre_completo',
                'obra:id,nombre_obra',
            ]);

            // Registrar en log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_documento',
                'tabla_afectada' => 'documentos',
                'registro_id' => $documento->id,
                'detalles' => json_encode($validated),
            ]);

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                // Crear un array con las propiedades calculadas
                $docArray = $documento->toArray();
                $docArray['estado'] = $documento->estado;
                $docArray['dias_hasta_vencimiento'] = $documento->dias_hasta_vencimiento;

                return response()->json([
                    'success' => true,
                    'message' => 'Documento actualizado exitosamente',
                    'data' => $docArray,
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return redirect()->route('documentos.show', $id)->with('success', 'Documento actualizado exitosamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado',
                ], 404);
            }

            return redirect()->route('documentos.index')->withErrors(['error' => 'Documento no encontrado']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el documento',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al actualizar el documento: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Patrón Híbrido: API (JSON) + Blade (Redirect)
     */
    public function destroy(Request $request, $id): JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (! $this->hasPermission('eliminar_documentos')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permisos para eliminar documentos'], 403);
            }

            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para eliminar documentos']);
        }

        try {
            $documento = Documento::findOrFail($id);

            // Registrar en log antes de eliminar
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_documento',
                'tabla_afectada' => 'documentos',
                'registro_id' => $documento->id,
                'detalles' => json_encode([
                    'tipo_documento' => $documento->tipoDocumento->nombre_tipo_documento ?? 'N/A',
                    'descripcion' => $documento->descripcion ?? 'N/A',
                ]),
            ]);

            // Eliminar archivo físico si existe
            if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }

            $documento->delete();

            // Si es solicitud API (AJAX/fetch con JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento eliminado exitosamente',
                ]);
            }

            // Si es solicitud web (navegador tradicional)
            return redirect()->route('documentos.index')->with('success', 'Documento eliminado exitosamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado',
                ], 404);
            }

            return redirect()->route('documentos.index')->withErrors(['error' => 'Documento no encontrado']);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el documento',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al eliminar el documento: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtener documentos próximos a vencer.
     * Solo API
     */
    public function proximosAVencer(Request $request): JsonResponse
    {
        try {
            $dias = $request->get('dias', 30);
            $documentos = Documento::with([
                'tipoDocumento:id,nombre_tipo_documento',
                'vehiculo:id,marca,modelo,placas',
                'personal:id,nombre_completo',
                'obra:id,nombre_obra',
            ])
                ->proximosAVencer($dias)
                ->get();

            // Agregar información adicional
            $documentos->transform(function ($documento) {
                $docArray = $documento->toArray();
                $docArray['estado'] = $documento->estado;
                $docArray['dias_hasta_vencimiento'] = $documento->dias_hasta_vencimiento;
                $docArray['esta_vencido'] = $documento->esta_vencido;

                return $docArray;
            });

            return response()->json([
                'success' => true,
                'data' => $documentos,
                'meta' => [
                    'total' => $documentos->count(),
                    'dias_evaluados' => $dias,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener documentos próximos a vencer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener documentos vencidos.
     * Solo API
     */
    public function vencidos(): JsonResponse
    {
        try {
            $documentos = Documento::with([
                'tipoDocumento:id,nombre_tipo_documento',
                'vehiculo:id,marca,modelo,placas',
                'personal:id,nombre_completo',
                'obra:id,nombre_obra',
            ])
                ->vencidos()
                ->get();

            // Agregar información adicional
            $documentos->transform(function ($documento) {
                $docArray = $documento->toArray();
                $docArray['estado'] = $documento->estado;
                $docArray['dias_hasta_vencimiento'] = $documento->dias_hasta_vencimiento;
                $docArray['esta_vencido'] = $documento->esta_vencido;

                return $docArray;
            });

            return response()->json([
                'success' => true,
                'data' => $documentos,
                'meta' => [
                    'total' => $documentos->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener documentos vencidos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
