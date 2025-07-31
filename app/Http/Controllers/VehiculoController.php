<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehiculoRequest;
use App\Http\Requests\UpdateVehiculoRequest;
use App\Models\CatalogoEstatus;
use App\Models\CatalogoTipoDocumento;
use App\Models\LogAccion;
use App\Models\Vehiculo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('ver_vehiculos')) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para ver vehículos',
                ], 403);
            }

            // Para Blade - redirigir con error
            return redirect()->route('home')->withErrors(['error' => 'No tienes permisos para ver vehículos']);
        }

        try {
            $query = Vehiculo::with('estatus');

            // Aplicar filtros
            if ($request->filled('marca')) {
                $query->porMarca($request->marca);
            }

            if ($request->filled('modelo')) {
                $query->porModelo($request->modelo);
            }

            if ($request->filled('estatus_id')) {
                $query->porEstatus($request->estatus_id);
            }

            if ($request->filled('anio_inicio')) {
                $query->porAnio($request->anio_inicio, $request->anio_fin);
            }

            // Búsqueda general
            if ($request->filled('buscar')) {
                $query->buscar($request->buscar);
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'id');
            $sortDirection = $request->get('sort_direction', 'asc');

            // Asegurar que el ordenamiento por ID sea numérico
            if ($sortBy === 'id') {
                $query->orderBy('id', $sortDirection);
            } else {
                $query->orderBy($sortBy, $sortDirection)->orderBy('id', 'asc');
            }

            // Paginación
            $perPage = $request->get('per_page', 15);
            $vehiculos = $query->paginate($perPage)->appends($request->query());

            // Para solicitudes API (JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehículos obtenidos correctamente',
                    'data' => $vehiculos->items(),
                    'meta' => [
                        'current_page' => $vehiculos->currentPage(),
                        'last_page' => $vehiculos->lastPage(),
                        'per_page' => $vehiculos->perPage(),
                        'total' => $vehiculos->total(),
                    ],
                ]);
            }

            // Para solicitudes Web (Blade) - datos adicionales para formularios
            $estatusOptions = CatalogoEstatus::select('id', 'nombre_estatus')->get();
            $marcasDisponibles = Vehiculo::select('marca')->distinct()->pluck('marca');
            $modelosDisponibles = Vehiculo::select('modelo')->distinct()->pluck('modelo');

            return view('vehiculos.index', compact(
                'vehiculos',
                'estatusOptions',
                'marcasDisponibles',
                'modelosDisponibles'
            ));
        } catch (\Exception $e) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener vehículos: ' . $e->getMessage(),
                ], 500);
            }

            // Para Blade
            return redirect()->route('home')->withErrors(['error' => 'Error al obtener vehículos']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View|JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('crear_vehiculos')) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para crear vehículos',
                ], 403);
            }

            // Para Blade - redirigir con error
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'No tienes permisos para crear vehículos']);
        }

        try {
            // Para solicitudes API - devolver datos necesarios para formulario
            if ($request->expectsJson()) {
                $estatusOptions = CatalogoEstatus::select('id', 'nombre_estatus')->get();

                return response()->json([
                    'success' => true,
                    'message' => 'Datos del formulario obtenidos correctamente',
                    'data' => [
                        'estatus_options' => $estatusOptions,
                    ],
                ]);
            }

            // Para solicitudes Web (Blade) - datos para formulario
            $estatusOptions = CatalogoEstatus::select('id', 'nombre_estatus')->get();
            $tiposDocumento = CatalogoTipoDocumento::select('id', 'nombre_tipo_documento')
                ->orderBy('nombre_tipo_documento')
                ->get();

            return view('vehiculos.create', compact('estatusOptions', 'tiposDocumento'));
        } catch (\Exception $e) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener datos del formulario: ' . $e->getMessage(),
                ], 500);
            }

            // Para Blade
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'Error al cargar formulario']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehiculoRequest $request): JsonResponse|RedirectResponse|View
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('crear_vehiculos')) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para crear vehículos',
                ], 403);
            }

            // Para Blade - redirigir con error
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'No tienes permisos para crear vehículos']);
        }

        try {
            DB::beginTransaction();

            $datosVehiculo = $request->validated();

            // Manejar fotografía del vehículo
            if ($request->hasFile('fotografia_file')) {
                $archivo = $request->file('fotografia_file');
                $nombreArchivo = time() . '_foto_' . $archivo->getClientOriginalName();
                $rutaArchivo = $archivo->storeAs('vehiculos/fotos', $nombreArchivo, 'public');
                $datosVehiculo['foto_frontal'] = $rutaArchivo;
            }

            // Manejar documentos adicionales
            if ($request->hasFile('documentos_adicionales')) {
                $documentosRutas = [];
                foreach ($request->file('documentos_adicionales') as $archivo) {
                    $nombreArchivo = time() . '_doc_' . $archivo->getClientOriginalName();
                    $rutaArchivo = $archivo->storeAs('vehiculos/documentos', $nombreArchivo, 'public');
                    $documentosRutas[] = $rutaArchivo;
                }
                $datosVehiculo['documentos_adicionales'] = $documentosRutas;
            }

            $vehiculo = Vehiculo::create($datosVehiculo);

            // Manejar documentos estructurados
            $this->procesarDocumentosEstructurados($request, $vehiculo->id);

            // Manejar documentos adicionales estructurados del formulario
            $this->procesarDocumentosAdicionalesEstructurados($request, $vehiculo->id);

            // Registrar log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'vehiculo' => $vehiculo->toArray(),
                ]),
            ]);

            DB::commit();

            // Para solicitudes API (JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehículo creado correctamente',
                    'data' => $vehiculo->load('estatus'),
                ], 201);
            }

            // Para solicitudes Web (Blade)
            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Vehículo creado correctamente');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            // Manejar errores específicos de base de datos
            $errorMessage = 'Error al crear vehículo';

            // Detectar errores de duplicados
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'placas')) {
                    $errorMessage = 'Las placas ingresadas ya están registradas en el sistema';
                } elseif (str_contains($e->getMessage(), 'n_serie')) {
                    $errorMessage = 'El número de serie ingresado ya está registrado en el sistema';
                } else {
                    $errorMessage = 'Ya existe un vehículo con los datos ingresados';
                }
            }

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['database' => [$errorMessage]]
                ], 422);
            }

            // Para Blade - retornar vista directamente con datos necesarios
            $estatusOptions = CatalogoEstatus::select('id', 'nombre_estatus')->get();
            $tiposDocumento = CatalogoTipoDocumento::select('id', 'nombre_tipo_documento')
                ->orderBy('nombre_tipo_documento')
                ->get();

            return view('vehiculos.create', compact('estatusOptions', 'tiposDocumento'))
                ->with('errors', collect(['error' => $errorMessage]))
                ->with('_old', $request->input());
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $e->errors()
                ], 422);
            }

            // Para Blade - retornar vista directamente con datos necesarios
            $estatusOptions = CatalogoEstatus::select('id', 'nombre_estatus')->get();
            $tiposDocumento = CatalogoTipoDocumento::select('id', 'nombre_tipo_documento')
                ->orderBy('nombre_tipo_documento')
                ->get();

            return view('vehiculos.create', compact('estatusOptions', 'tiposDocumento'))
                ->with('errors', $e->errors())
                ->with('_old', $request->input())
                ->with('error', 'Por favor revise los datos ingresados y corrija los errores señalados');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log del error para debugging
            \Log::error('Error al crear vehículo: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'exception' => $e
            ]);

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno del servidor al crear el vehículo',
                    'errors' => ['server' => ['Ocurrió un error inesperado. Por favor intente nuevamente o contacte al administrador.']]
                ], 500);
            }

            // Para Blade - retornar vista directamente con datos necesarios
            $estatusOptions = CatalogoEstatus::select('id', 'nombre_estatus')->get();
            $tiposDocumento = CatalogoTipoDocumento::select('id', 'nombre_tipo_documento')
                ->orderBy('nombre_tipo_documento')
                ->get();

            return view('vehiculos.create', compact('estatusOptions', 'tiposDocumento'))
                ->with('errors', collect(['error' => 'Ocurrió un error inesperado al crear el vehículo. Por favor intente nuevamente.']))
                ->with('_old', $request->input());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): View|JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('ver_vehiculos')) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para ver vehículos',
                ], 403);
            }

            // Para Blade - redirigir con error
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'No tienes permisos para ver vehículos']);
        }

        try {
            $vehiculo = Vehiculo::with('estatus')->findOrFail($id);

            // Para solicitudes API (JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehículo obtenido correctamente',
                    'data' => $vehiculo,
                ]);
            }

            // Para solicitudes Web (Blade)
            return view('vehiculos.show', compact('vehiculo'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehículo no encontrado',
                ], 404);
            }

            // Para Blade
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'Vehículo no encontrado']);
        } catch (\Exception $e) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener vehículo: ' . $e->getMessage(),
                ], 500);
            }

            // Para Blade
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'Error al obtener vehículo']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id): View|JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('editar_vehiculos')) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para editar vehículos',
                ], 403);
            }

            // Para Blade - redirigir con error
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'No tienes permisos para editar vehículos']);
        }

        try {
            $vehiculo = Vehiculo::with('estatus')->findOrFail($id);
            $estatusOptions = CatalogoEstatus::select('id', 'nombre_estatus')->get();
            $tiposDocumento = CatalogoTipoDocumento::select('id', 'nombre_tipo_documento', 'requiere_vencimiento')
                ->orderBy('nombre_tipo_documento')
                ->get();

            // Para solicitudes API - devolver datos del formulario
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Datos del formulario obtenidos correctamente',
                    'data' => [
                        'vehiculo' => $vehiculo,
                        'estatus_options' => $estatusOptions,
                        'tipos_documento' => $tiposDocumento,
                    ],
                ]);
            }

            // Para solicitudes Web (Blade)
            return view('vehiculos.edit', compact('vehiculo', 'estatusOptions', 'tiposDocumento'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehículo no encontrado',
                ], 404);
            }

            // Para Blade
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'Vehículo no encontrado']);
        } catch (\Exception $e) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener datos del formulario: ' . $e->getMessage(),
                ], 500);
            }

            // Para Blade
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'Error al cargar formulario de edición']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehiculoRequest $request, string $id): JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('editar_vehiculos')) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para editar vehículos',
                ], 403);
            }

            // Para Blade - redirigir con error
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'No tienes permisos para editar vehículos']);
        }

        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::findOrFail($id);
            $datosOriginales = $vehiculo->toArray();

            $datosVehiculo = $request->validated();

            // Manejar documentos adicionales (solo si se envían nuevos)
            if ($request->hasFile('documentos_adicionales')) {
                // Eliminar documentos anteriores si existen
                if ($vehiculo->documentos_adicionales) {
                    foreach ($vehiculo->documentos_adicionales as $rutaDoc) {
                        if (Storage::disk('public')->exists($rutaDoc)) {
                            Storage::disk('public')->delete($rutaDoc);
                        }
                    }
                }

                // Subir nuevos documentos
                $documentosRutas = [];
                foreach ($request->file('documentos_adicionales') as $archivo) {
                    $nombreArchivo = time() . '_doc_' . $archivo->getClientOriginalName();
                    $rutaArchivo = $archivo->storeAs('vehiculos/documentos', $nombreArchivo, 'public');
                    $documentosRutas[] = $rutaArchivo;
                }
                $datosVehiculo['documentos_adicionales'] = $documentosRutas;
            }

            $vehiculo->update($datosVehiculo);

            // Registrar log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'datos_originales' => $datosOriginales,
                    'datos_nuevos' => $vehiculo->toArray(),
                ]),
            ]);

            DB::commit();

            // Para solicitudes API (JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehículo actualizado correctamente',
                    'data' => $vehiculo->load('estatus'),
                ]);
            }

            // Para solicitudes Web (Blade)
            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Vehículo actualizado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehículo no encontrado',
                ], 404);
            }

            // Para Blade
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'Vehículo no encontrado']);
        } catch (\Exception $e) {
            DB::rollBack();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar vehículo: ' . $e->getMessage(),
                ], 500);
            }

            // Para Blade
            return redirect()->route('vehiculos.edit', $id)
                ->withErrors(['error' => 'Error al actualizar vehículo'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('eliminar_vehiculos')) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para eliminar vehículos',
                ], 403);
            }

            // Para Blade - redirigir con error
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'No tienes permisos para eliminar vehículos']);
        }

        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::findOrFail($id);

            // Verificar si el vehículo está en uso (tiene obras activas)
            $asignacionesActivas = $vehiculo->obras()->whereNull('fecha_liberacion')->count();

            if ($asignacionesActivas > 0) {
                DB::rollBack();
                $message = 'No se puede eliminar el vehículo porque tiene obras activas.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 422);
                }

                return redirect()->route('vehiculos.index')
                    ->with('error', $message);
            }

            $vehiculo->delete();

            // Registrar log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'vehiculo_eliminado' => $vehiculo->toArray(),
                ]),
            ]);

            DB::commit();

            // Para solicitudes API (JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehículo eliminado correctamente',
                ]);
            }

            // Para solicitudes Web (Blade)
            return redirect()->route('vehiculos.index')
                ->with('success', 'Vehículo eliminado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehículo no encontrado',
                ], 404);
            }

            // Para Blade
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'Vehículo no encontrado']);
        } catch (\Exception $e) {
            DB::rollBack();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar vehículo: ' . $e->getMessage(),
                ], 500);
            }

            // Para Blade
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'Error al eliminar vehículo']);
        }
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(Request $request, string $id): JsonResponse|RedirectResponse
    {
        // Verificar permisos
        if (! Auth::user()->hasPermission('restaurar_vehiculos')) {
            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para restaurar vehículos',
                ], 403);
            }

            // Para Blade - redirigir con error
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'No tienes permisos para restaurar vehículos']);
        }

        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::withTrashed()->findOrFail($id);

            if (! $vehiculo->trashed()) {
                // Para API
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El vehículo no está eliminado',
                    ], 400);
                }

                // Para Blade
                return redirect()->route('vehiculos.index')->withErrors(['error' => 'El vehículo no está eliminado']);
            }

            $vehiculo->restore();

            // Registrar log de acción
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'restaurar_vehiculo',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'detalles' => json_encode([
                    'vehiculo_restaurado' => $vehiculo->toArray(),
                ]),
            ]);

            DB::commit();

            // Para solicitudes API (JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehículo restaurado correctamente',
                    'data' => $vehiculo->load('estatus'),
                ]);
            }

            // Para solicitudes Web (Blade)
            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Vehículo restaurado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehículo no encontrado',
                ], 404);
            }

            // Para Blade
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'Vehículo no encontrado']);
        } catch (\Exception $e) {
            DB::rollBack();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al restaurar vehículo: ' . $e->getMessage(),
                ], 500);
            }

            // Para Blade
            return redirect()->route('vehiculos.index')->withErrors(['error' => 'Error al restaurar vehículo']);
        }
    }

    /**
     * Get available status options for vehicles.
     */
    public function estatusOptions(Request $request): JsonResponse
    {
        try {
            $estatus = CatalogoEstatus::select('id', 'nombre_estatus')->get();

            // Este método solo es útil para API, ya que para Blade se obtienen
            // los datos directamente en los métodos create() y edit()
            return response()->json([
                'success' => true,
                'message' => 'Opciones de estatus obtenidas correctamente',
                'data' => $estatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener opciones de estatus: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Manejar la subida de un archivo de documento
     */
    private function handleVehicleDocumentUpload(\Illuminate\Http\UploadedFile $file, int $vehiculoId): string
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs("vehiculos/{$vehiculoId}/documentos", $fileName, 'public');
    }

    /**
     * Crear documentos asociados al vehículo
     */
    private function createVehicleDocuments(Vehiculo $vehiculo, array $documentosData): array
    {
        $documentos = [];

        foreach ($documentosData as $docData) {
            $documento = \App\Models\Documento::create([
                'vehiculo_id' => $vehiculo->id,
                'tipo_documento_id' => $docData['tipo_documento_id'],
                'descripcion' => $docData['descripcion'] ?? null,
                'fecha_vencimiento' => $docData['fecha_vencimiento'] ?? null,
                'ruta_archivo' => $docData['ruta_archivo'] ?? null,
                'contenido' => $docData['contenido'] ?? null,
            ]);

            $documentos[] = $documento;
        }

        return $documentos;
    }

    /**
     * Procesar documentos estructurados del vehículo
     */
    private function procesarDocumentosEstructurados($request, $vehiculoId)
    {
        // Mapeo de campos de archivo a tipos de documento
        $documentosEstructurados = [
            'tarjeta_circulacion_file' => [
                'tipo' => 'Tarjeta de Circulación',
                'numero_campo' => 'no_tarjeta_circulacion',
                'fecha_campo' => 'fecha_vencimiento_tarjeta'
            ],
            'tenencia_vehicular_file' => [
                'tipo' => 'Tenencia Vehicular',
                'numero_campo' => 'no_tenencia_vehicular',
                'fecha_campo' => 'fecha_vencimiento_tenencia'
            ],
            'verificacion_vehicular_file' => [
                'tipo' => 'Verificación Vehicular',
                'numero_campo' => 'no_verificacion_vehicular',
                'fecha_campo' => 'fecha_vencimiento_verificacion'
            ],
            'poliza_seguro_file' => [
                'tipo' => 'Póliza de Seguro',
                'numero_campo' => 'no_poliza_seguro',
                'fecha_campo' => 'fecha_vencimiento_seguro',
                'extra_campo' => 'aseguradora'
            ],
            'factura_compra_file' => [
                'tipo' => 'Factura de Compra',
                'numero_campo' => 'no_factura_compra',
                'fecha_campo' => null
            ],
            'manual_vehiculo_file' => [
                'tipo' => 'Manual del Vehículo',
                'numero_campo' => null,
                'fecha_campo' => null
            ]
        ];

        $documentosData = [];
        $vehiculo = Vehiculo::find($vehiculoId);

        foreach ($documentosEstructurados as $campoArchivo => $config) {
            if ($request->hasFile($campoArchivo)) {
                // Obtener el tipo de documento
                $tipoDocumento = \App\Models\CatalogoTipoDocumento::where('nombre_tipo_documento', $config['tipo'])->first();

                if ($tipoDocumento) {
                    // Usar el método auxiliar para subir el archivo
                    $archivo = $request->file($campoArchivo);
                    $rutaArchivo = $this->handleVehicleDocumentUpload($archivo, $vehiculoId);

                    // Preparar contenido adicional
                    $contenido = [];
                    if ($config['numero_campo'] && $request->filled($config['numero_campo'])) {
                        $contenido['numero'] = $request->input($config['numero_campo']);
                    }
                    if (isset($config['extra_campo']) && $request->filled($config['extra_campo'])) {
                        $contenido['aseguradora'] = $request->input($config['extra_campo']);
                    }

                    // Preparar datos del documento
                    $documentosData[] = [
                        'tipo_documento_id' => $tipoDocumento->id,
                        'descripcion' => $config['tipo'] . ' del vehículo',
                        'ruta_archivo' => $rutaArchivo,
                        'fecha_vencimiento' => $config['fecha_campo'] && $request->filled($config['fecha_campo'])
                            ? $request->input($config['fecha_campo'])
                            : null,
                        'contenido' => !empty($contenido) ? $contenido : null,
                    ];
                }
            }
        }

        // Crear todos los documentos usando el método auxiliar
        if (!empty($documentosData)) {
            $this->createVehicleDocuments($vehiculo, $documentosData);
        }
    }

    /**
     * Procesar documentos adicionales estructurados del formulario
     */
    private function procesarDocumentosAdicionalesEstructurados($request, $vehiculoId)
    {
        // Obtener los datos de documentos adicionales del request
        $tiposDocumento = $request->input('documentos_adicionales_tipos', []);
        $descripciones = $request->input('documentos_adicionales_descripciones', []);
        $fechasVencimiento = $request->input('documentos_adicionales_fechas_vencimiento', []);

        // Procesar archivos de documentos adicionales
        if ($request->hasFile('documentos_adicionales_archivos')) {
            $archivos = $request->file('documentos_adicionales_archivos');
            $documentosData = [];

            foreach ($archivos as $index => $archivo) {
                if ($archivo && isset($tiposDocumento[$index])) {
                    // Usar el método auxiliar para subir el archivo
                    $rutaArchivo = $this->handleVehicleDocumentUpload($archivo, $vehiculoId);

                    // Preparar datos del documento
                    $documentosData[] = [
                        'tipo_documento_id' => $tiposDocumento[$index],
                        'descripcion' => $descripciones[$index] ?? 'Documento adicional del vehículo',
                        'fecha_vencimiento' => !empty($fechasVencimiento[$index]) ? $fechasVencimiento[$index] : null,
                        'ruta_archivo' => $rutaArchivo,
                        'contenido' => null,
                    ];
                }
            }

            // Crear todos los documentos usando el método auxiliar
            if (!empty($documentosData)) {
                $vehiculo = Vehiculo::find($vehiculoId);
                $this->createVehicleDocuments($vehiculo, $documentosData);
            }
        }
    }
}
