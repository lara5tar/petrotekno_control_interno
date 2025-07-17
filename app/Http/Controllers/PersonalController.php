<?php

namespace App\Http\Controllers;

use App\Models\LogAccion;
use App\Models\Personal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Personal::with('categoria');

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

        $personal = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $personal,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'estatus' => 'required|in:activo,inactivo',
            'categoria_id' => 'required|exists:categorias_personal,id',
        ]);

        $personal = Personal::create($request->only([
            'nombre_completo',
            'estatus',
            'categoria_id',
        ]));

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'crear_personal',
            'tabla_afectada' => 'personal',
            'registro_id' => $personal->id,
            'detalles' => ['personal_creado' => $personal->nombre_completo],
        ]);

        $personal->load('categoria');

        return response()->json([
            'success' => true,
            'message' => 'Personal creado exitosamente',
            'data' => $personal,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $personal = Personal::with(['categoria', 'usuario'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $personal,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $personal = Personal::findOrFail($id);

        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'estatus' => 'required|in:activo,inactivo',
            'categoria_id' => 'required|exists:categorias_personal,id',
        ]);

        $oldData = $personal->toArray();

        $personal->update($request->only([
            'nombre_completo',
            'estatus',
            'categoria_id',
        ]));

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'actualizar_personal',
            'tabla_afectada' => 'personal',
            'registro_id' => $personal->id,
            'detalles' => [
                'personal_actualizado' => $personal->nombre_completo,
                'cambios' => array_diff_assoc($request->only(['nombre_completo', 'estatus', 'categoria_id']), $oldData),
            ],
        ]);

        $personal->load('categoria');

        return response()->json([
            'success' => true,
            'message' => 'Personal actualizado exitosamente',
            'data' => $personal,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $personal = Personal::findOrFail($id);

        // Verificar si tiene usuario asociado
        if ($personal->usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el personal porque tiene un usuario asociado',
            ], 400);
        }

        $personalName = $personal->nombre_completo;
        $personal->delete();

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'eliminar_personal',
            'tabla_afectada' => 'personal',
            'registro_id' => $id,
            'detalles' => ['personal_eliminado' => $personalName],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Personal eliminado exitosamente',
        ]);
    }
}
