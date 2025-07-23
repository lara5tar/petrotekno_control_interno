<?php

namespace App\Http\Controllers;

use App\Models\CatalogoTipoServicio;
use Illuminate\Http\Request;

class CatalogoTipoServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CatalogoTipoServicio::query();

        // Filtro de búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where('nombre_tipo_servicio', 'like', "%{$buscar}%");
        }

        // Orden
        $query->orderBy('nombre_tipo_servicio');

        // Paginación
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100); // Limitar a máximo 100

        $tiposServicio = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tiposServicio,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_tipo_servicio' => 'required|string|max:255|unique:catalogo_tipos_servicio,nombre_tipo_servicio',
        ], [
            'nombre_tipo_servicio.required' => 'El nombre del tipo de servicio es requerido',
            'nombre_tipo_servicio.max' => 'El nombre no puede exceder 255 caracteres',
            'nombre_tipo_servicio.unique' => 'Ya existe un tipo de servicio con este nombre',
        ]);

        $tipoServicio = CatalogoTipoServicio::create([
            'nombre_tipo_servicio' => $request->nombre_tipo_servicio,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de servicio creado exitosamente',
            'data' => $tipoServicio,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CatalogoTipoServicio $catalogoTipoServicio)
    {
        return response()->json([
            'success' => true,
            'data' => $catalogoTipoServicio,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CatalogoTipoServicio $catalogoTipoServicio)
    {
        $request->validate([
            'nombre_tipo_servicio' => 'required|string|max:255|unique:catalogo_tipos_servicio,nombre_tipo_servicio,' . $catalogoTipoServicio->id,
        ], [
            'nombre_tipo_servicio.required' => 'El nombre del tipo de servicio es requerido',
            'nombre_tipo_servicio.max' => 'El nombre no puede exceder 255 caracteres',
            'nombre_tipo_servicio.unique' => 'Ya existe un tipo de servicio con este nombre',
        ]);

        $catalogoTipoServicio->update([
            'nombre_tipo_servicio' => $request->nombre_tipo_servicio,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de servicio actualizado exitosamente',
            'data' => $catalogoTipoServicio,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CatalogoTipoServicio $catalogoTipoServicio)
    {
        // Verificar si tiene mantenimientos asociados
        if ($catalogoTipoServicio->mantenimientos()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el tipo de servicio porque tiene mantenimientos asociados',
            ], 422);
        }

        $catalogoTipoServicio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipo de servicio eliminado exitosamente',
        ]);
    }
}
