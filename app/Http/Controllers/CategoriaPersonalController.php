<?php

namespace App\Http\Controllers;

use App\Models\CategoriaPersonal;
use App\Models\LogAccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoriaPersonalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Use different permissions for different actions
        $this->middleware('permission:ver_catalogos')->only(['index', 'show']);
        $this->middleware('permission:crear_catalogos')->only(['create', 'store']);
        $this->middleware('permission:editar_catalogos')->only(['edit', 'update']);
        $this->middleware('permission:eliminar_catalogos')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CategoriaPersonal::withCount('personal');
        
        // Filtro de búsqueda
        if ($request->filled('search')) {
            $query->where('nombre_categoria', 'like', '%' . $request->search . '%');
        }
        
        $categorias = $query->orderBy('id', 'asc')->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $categorias
            ]);
        }

        return view('categorias-personal.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Formulario de creación listo'
            ]);
        }

        return view('categorias-personal.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre_categoria' => 'required|string|max:255|unique:categorias_personal,nombre_categoria'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            DB::beginTransaction();

            $categoria = CategoriaPersonal::create([
                'nombre_categoria' => $validated['nombre_categoria']
            ]);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_categoria_personal',
                'tabla_afectada' => 'categorias_personal',
                'registro_id' => $categoria->id,
                'detalles' => "Categoría creada: {$categoria->nombre_categoria}"
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoría creada exitosamente',
                    'data' => $categoria
                ], 201);
            }

            return redirect()
                ->route('categorias-personal.index')
                ->with('success', 'Categoría creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la categoría',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear la categoría: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, CategoriaPersonal $categoriaPersonal)
    {
        $categoriaPersonal->loadCount('personal');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $categoriaPersonal
            ]);
        }

        return view('categorias-personal.show', compact('categoriaPersonal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, CategoriaPersonal $categoriaPersonal)
    {
        // Load the count of associated staff
        $categoriaPersonal->loadCount('personal');
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $categoriaPersonal
            ]);
        }

        return view('categorias-personal.edit', compact('categoriaPersonal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoriaPersonal $categoriaPersonal)
    {
        try {
            $validated = $request->validate([
                'nombre_categoria' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categorias_personal', 'nombre_categoria')->ignore($categoriaPersonal->id)
                ]
            ]);

            DB::beginTransaction();

            $categoriaPersonal->update([
                'nombre_categoria' => $validated['nombre_categoria']
            ]);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_categoria_personal',
                'tabla_afectada' => 'categorias_personal',
                'registro_id' => $categoriaPersonal->id,
                'detalles' => "Categoría actualizada: {$categoriaPersonal->nombre_categoria}"
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoría actualizada exitosamente',
                    'data' => $categoriaPersonal
                ]);
            }

            return redirect()
                ->route('categorias-personal.index')
                ->with('success', 'Categoría actualizada exitosamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la categoría',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar la categoría: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CategoriaPersonal $categoriaPersonal)
    {
        \Log::info('Attempting to delete category', [
            'category_id' => $categoriaPersonal->id,
            'category_name' => $categoriaPersonal->nombre_categoria,
            'request_method' => $request->method(),
            'user_id' => auth()->id()
        ]);

        try {
            // Verificar si tiene personal asociado
            $personalCount = $categoriaPersonal->personal()->count();
            
            \Log::info('Checking personal count for category', [
                'category_id' => $categoriaPersonal->id,
                'personal_count' => $personalCount
            ]);

            if ($personalCount > 0) {
                \Log::warning('Cannot delete category - has associated staff', [
                    'category_id' => $categoriaPersonal->id,
                    'personal_count' => $personalCount
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se puede eliminar la categoría porque tiene personal asociado'
                    ], 400);
                }

                return redirect()
                    ->back()
                    ->withErrors(['error' => 'No se puede eliminar la categoría porque tiene personal asociado']);
            }

            DB::beginTransaction();

            $nombreCategoria = $categoriaPersonal->nombre_categoria;
            $categoriaPersonal->delete();

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_categoria_personal',
                'tabla_afectada' => 'categorias_personal',
                'registro_id' => $categoriaPersonal->id,
                'detalles' => "Categoría eliminada: {$nombreCategoria}"
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoría eliminada exitosamente'
                ]);
            }

            return redirect()
                ->route('categorias-personal.index')
                ->with('success', 'Categoría eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la categoría',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withErrors(['error' => 'Error al eliminar la categoría: ' . $e->getMessage()]);
        }
    }
}