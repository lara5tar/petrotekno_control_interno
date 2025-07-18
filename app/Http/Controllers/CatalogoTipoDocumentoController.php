<?php

namespace App\Http\Controllers;

use App\Models\CatalogoTipoDocumento;
use App\Http\Requests\StoreCatalogoTipoDocumentoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CatalogoTipoDocumentoController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('permission:ver_catalogos')->only(['index', 'show']);
        $this->middleware('permission:crear_catalogos')->only('store');
        $this->middleware('permission:editar_catalogos')->only('update');
        $this->middleware('permission:eliminar_catalogos')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = CatalogoTipoDocumento::query();

            // Filtro por si requiere vencimiento
            if ($request->filled('requiere_vencimiento')) {
                if ($request->boolean('requiere_vencimiento')) {
                    $query->queRequierenVencimiento();
                } else {
                    $query->queNoRequierenVencimiento();
                }
            }

            // Búsqueda por nombre
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('nombre_tipo_documento', 'like', "%{$search}%");
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'nombre_tipo_documento');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Incluir conteo de documentos si se solicita
            if ($request->boolean('with_counts')) {
                $query->withCount('documentos');
            }

            // Paginación
            if ($request->boolean('paginate', true)) {
                $perPage = $request->get('per_page', 15);
                $tipos = $query->paginate($perPage);
            } else {
                $tipos = $query->get();
            }

            return response()->json([
                'success' => true,
                'data' => $tipos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCatalogoTipoDocumentoRequest $request): JsonResponse
    {
        try {
            $tipoDocumento = CatalogoTipoDocumento::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Tipo de documento creado exitosamente',
                'data' => $tipoDocumento
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $catalogoTipoDocumento = CatalogoTipoDocumento::findOrFail($id);
            
            // Cargar documentos relacionados si se solicita
            if (request()->boolean('with_documentos')) {
                $catalogoTipoDocumento->load([
                    'documentos' => function ($query) {
                        $query->with(['vehiculo:id,marca,modelo,placas', 'personal:id,nombre_completo', 'obra:id,nombre_obra'])
                              ->orderBy('created_at', 'desc');
                    }
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $catalogoTipoDocumento
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el tipo de documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCatalogoTipoDocumentoRequest $request, $id): JsonResponse
    {
        try {
            $catalogoTipoDocumento = CatalogoTipoDocumento::findOrFail($id);
            $catalogoTipoDocumento->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Tipo de documento actualizado exitosamente',
                'data' => $catalogoTipoDocumento
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $catalogoTipoDocumento = CatalogoTipoDocumento::findOrFail($id);
            
            // Verificar si tiene documentos asociados
            if ($catalogoTipoDocumento->documentos()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el tipo de documento porque tiene documentos asociados'
                ], 422);
            }

            $catalogoTipoDocumento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de documento eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
