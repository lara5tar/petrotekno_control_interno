<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehiculoRequest;
use App\Http\Requests\UpdateVehiculoRequest;
use App\Models\CatalogoEstatus;
use App\Models\LogAccion;
use App\Models\Vehiculo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

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

            return view('vehiculos.create', compact('estatusOptions'));
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
    public function store(StoreVehiculoRequest $request): JsonResponse|RedirectResponse
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

            $vehiculo = Vehiculo::create($request->validated());

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
        } catch (\Exception $e) {
            DB::rollBack();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear vehículo: ' . $e->getMessage(),
                ], 500);
            }

            // Para Blade
            return redirect()->back()
                ->withErrors(['error' => 'Error al crear vehículo'])
                ->withInput();
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

            // Para solicitudes API - devolver datos del formulario
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Datos del formulario obtenidos correctamente',
                    'data' => [
                        'vehiculo' => $vehiculo,
                        'estatus_options' => $estatusOptions,
                    ],
                ]);
            }

            // Para solicitudes Web (Blade)
            return view('vehiculos.edit', compact('vehiculo', 'estatusOptions'));
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

            $vehiculo->update($request->validated());

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
            return redirect()->back()
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

            // Verificar si el vehículo está en uso (tiene asignaciones activas)
            // TODO: Implementar esta verificación cuando se tenga el modelo Asignacion

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
}
