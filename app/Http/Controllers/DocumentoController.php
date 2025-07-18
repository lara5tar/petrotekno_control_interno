<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Http\Requests\StoreDocumentoRequest;
use App\Http\Requests\UpdateDocumentoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentoController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:ver_documentos')->only(['index', 'show']);
        $this->middleware('permission:crear_documentos')->only('store');
        $this->middleware('permission:editar_documentos')->only('update');
        $this->middleware('permission:eliminar_documentos')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Documento::with([
                'tipoDocumento:id,nombre_tipo_documento,requiere_vencimiento',
                'vehiculo:id,marca,modelo,placas',
                'personal:id,nombre_completo',
                'obra:id,nombre_obra'
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

            // Agregar información adicional a cada documento
            $documentos->getCollection()->transform(function ($documento) {
                $documento->estado = $documento->estado;
                $documento->dias_hasta_vencimiento = $documento->dias_hasta_vencimiento;
                $documento->esta_vencido = $documento->esta_vencido;
                return $documento;
            });

            return response()->json([
                'success' => true,
                'data' => $documentos,
                'meta' => [
                    'total_documentos' => $documentos->total(),
                    'filtros_aplicados' => $request->except(['page', 'per_page'])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los documentos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentoRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Manejo de archivo si se proporciona
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . Str::slug($archivo->getClientOriginalName(), '_');
                $rutaArchivo = $archivo->storeAs('documentos', $nombreArchivo, 'public');
                $data['ruta_archivo'] = $rutaArchivo;
            }

            $documento = Documento::create($data);
            $documento->load([
                'tipoDocumento:id,nombre_tipo_documento,requiere_vencimiento',
                'vehiculo:id,marca,modelo,placas',
                'personal:id,nombre_completo',
                'obra:id,nombre_obra'
            ]);

            // Agregar información adicional
            $documento->estado = $documento->estado;
            $documento->dias_hasta_vencimiento = $documento->dias_hasta_vencimiento;

            return response()->json([
                'success' => true,
                'message' => 'Documento creado exitosamente',
                'data' => $documento
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Documento $documento): JsonResponse
    {
        try {
            $documento->load([
                'tipoDocumento:id,nombre_tipo_documento,descripcion,requiere_vencimiento',
                'vehiculo:id,marca,modelo,placas,n_serie',
                'personal:id,nombre_completo,categoria_id',
                'obra:id,nombre_obra,estatus,fecha_inicio,fecha_fin'
            ]);

            // Agregar información adicional
            $documento->estado = $documento->estado;
            $documento->dias_hasta_vencimiento = $documento->dias_hasta_vencimiento;
            $documento->esta_vencido = $documento->esta_vencido;

            return response()->json([
                'success' => true,
                'data' => $documento
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentoRequest $request, Documento $documento): JsonResponse
    {
        try {
            $data = $request->validated();

            // Manejo de archivo si se proporciona
            if ($request->hasFile('archivo')) {
                // Eliminar archivo anterior si existe
                if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                    Storage::disk('public')->delete($documento->ruta_archivo);
                }

                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . Str::slug($archivo->getClientOriginalName(), '_');
                $rutaArchivo = $archivo->storeAs('documentos', $nombreArchivo, 'public');
                $data['ruta_archivo'] = $rutaArchivo;
            }

            $documento->update($data);
            $documento->load([
                'tipoDocumento:id,nombre_tipo_documento,requiere_vencimiento',
                'vehiculo:id,marca,modelo,placas',
                'personal:id,nombre_completo',
                'obra:id,nombre_obra'
            ]);

            // Agregar información adicional
            $documento->estado = $documento->estado;
            $documento->dias_hasta_vencimiento = $documento->dias_hasta_vencimiento;

            return response()->json([
                'success' => true,
                'message' => 'Documento actualizado exitosamente',
                'data' => $documento
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Documento $documento): JsonResponse
    {
        try {
            // Eliminar archivo físico si existe
            if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }

            $documento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Documento eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener documentos próximos a vencer
     */
    public function proximosAVencer(Request $request): JsonResponse
    {
        try {
            $dias = $request->get('dias', 30);
            
            $documentos = Documento::with([
                'tipoDocumento:id,nombre_tipo_documento',
                'vehiculo:id,marca,modelo,placas',
                'personal:id,nombre_completo',
                'obra:id,nombre_obra'
            ])
            ->proximosAVencer($dias)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

            // Agregar información adicional
            $documentos->transform(function ($documento) {
                $documento->dias_hasta_vencimiento = $documento->dias_hasta_vencimiento;
                return $documento;
            });

            return response()->json([
                'success' => true,
                'data' => $documentos,
                'meta' => [
                    'total' => $documentos->count(),
                    'dias_filtro' => $dias
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener documentos próximos a vencer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener documentos vencidos
     */
    public function vencidos(): JsonResponse
    {
        try {
            $documentos = Documento::with([
                'tipoDocumento:id,nombre_tipo_documento',
                'vehiculo:id,marca,modelo,placas',
                'personal:id,nombre_completo',
                'obra:id,nombre_obra'
            ])
            ->vencidos()
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

            // Agregar información adicional
            $documentos->transform(function ($documento) {
                $documento->dias_vencido = abs($documento->dias_hasta_vencimiento);
                return $documento;
            });

            return response()->json([
                'success' => true,
                'data' => $documentos,
                'meta' => [
                    'total' => $documentos->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener documentos vencidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
