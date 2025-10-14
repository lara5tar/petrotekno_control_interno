<?php

namespace App\Http\Controllers;

use App\Models\TipoActivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAccion;

class TipoActivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipoActivo::query();

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nombre', 'like', "%{$search}%");
        }

        // Filtro por tiene_kilometraje
        if ($request->filled('tiene_kilometraje')) {
            $query->where('tiene_kilometraje', $request->get('tiene_kilometraje'));
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'nombre');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $tiposActivos = $query->withCount('vehiculos')->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipos de activos obtenidos correctamente',
                'data' => $tiposActivos->toArray(),
            ]);
        }

        return view('tipos-activos.index', compact('tiposActivos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipos-activos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tiene_kilometraje' => 'boolean',
            'tiene_placa' => 'boolean',
            'tiene_numero_serie' => 'boolean'
        ]);

        $validated['tiene_kilometraje'] = $request->input('tiene_kilometraje', 0);
        $validated['tiene_placa'] = $request->input('tiene_placa', 1);
        $validated['tiene_numero_serie'] = $request->input('tiene_numero_serie', 1);

        $tipoActivo = TipoActivo::create($validated);

        // Log de auditoría
        LogAccion::create([
            'usuario_id' => Auth::id(),
            'accion' => 'crear_tipo_activo',
            'tabla_afectada' => 'tipo_activos',
            'registro_id' => $tipoActivo->id,
            'detalles' => "Tipo de activo creado: {$tipoActivo->nombre}",
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de activo creado exitosamente',
                'data' => $tipoActivo,
            ], 201);
        }

        return redirect()
            ->route('tipos-activos.index')
            ->with('success', 'Tipo de activo creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoActivo $tipoActivo)
    {
        $tipoActivo->loadCount('vehiculos');
        $tipoActivo->load(['vehiculos' => function($query) {
            $query->select('id', 'marca', 'modelo', 'placas', 'tipo_activo_id')
                  ->orderBy('marca')
                  ->limit(10);
        }]);

        return view('tipos-activos.show', compact('tipoActivo'));
    }

    /**
     * Get tipo activo info for AJAX requests
     */
    public function getInfo($id)
    {
        $tipoActivo = TipoActivo::find($id);
        
        if (!$tipoActivo) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de activo no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tipoActivo->id,
                'nombre' => $tipoActivo->nombre,
                'tiene_kilometraje' => $tipoActivo->tiene_kilometraje,
                'tiene_placa' => $tipoActivo->tiene_placa ?? true,
                'tiene_numero_serie' => $tipoActivo->tiene_numero_serie ?? true
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoActivo $tipoActivo)
    {
        return view('tipos-activos.edit', compact('tipoActivo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoActivo $tipoActivo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tiene_kilometraje' => 'boolean',
            'tiene_placa' => 'boolean',
            'tiene_numero_serie' => 'boolean'
        ]);

        $validated['tiene_kilometraje'] = $request->input('tiene_kilometraje', 0);
        $validated['tiene_placa'] = $request->input('tiene_placa', 1);
        $validated['tiene_numero_serie'] = $request->input('tiene_numero_serie', 1);

        $tipoActivo->update($validated);

        // Log de auditoría
        LogAccion::create([
            'usuario_id' => Auth::id(),
            'accion' => 'actualizar_tipo_activo',
            'tabla_afectada' => 'tipo_activos',
            'registro_id' => $tipoActivo->id,
            'detalles' => "Tipo de activo actualizado: {$tipoActivo->nombre}",
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de activo actualizado exitosamente',
                'data' => $tipoActivo,
            ]);
        }

        return redirect()
            ->route('tipos-activos.show', $tipoActivo)
            ->with('success', 'Tipo de activo actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoActivo $tipoActivo)
    {
        // Verificar si tiene vehículos asociados
        if ($tipoActivo->vehiculos()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el tipo de activo porque tiene vehículos asociados',
            ], 422);
        }

        $nombre = $tipoActivo->nombre;
        $tipoActivo->delete();

        // Log de auditoría
        LogAccion::create([
            'usuario_id' => Auth::id(),
            'accion' => 'eliminar_tipo_activo',
            'tabla_afectada' => 'tipo_activos',
            'registro_id' => $tipoActivo->id,
            'detalles' => "Tipo de activo eliminado: {$nombre}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de activo eliminado exitosamente',
        ]);
    }
}
