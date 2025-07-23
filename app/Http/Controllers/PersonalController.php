<?php

namespace App\Http\Controllers;

use App\Models\CategoriaPersonal;
use App\Models\LogAccion;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function store(Request $request)
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

        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'estatus' => ['required', Rule::in(['activo', 'inactivo'])],
            'categoria_id' => 'required|exists:categorias_personal,id',
        ]);

        try {
            $personal = Personal::create($request->only([
                'nombre_completo',
                'estatus',
                'categoria_id',
            ]));

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
                ->withErrors(['error' => 'Error al crear el personal: '.$e->getMessage()]);
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
            $personal = Personal::with(['categoria', 'usuario'])->findOrFail($id);

            // Respuesta híbrida
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Personal obtenido exitosamente',
                    'data' => $personal,
                ]);
            }

            return view('personal.show', compact('personal'));
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
            $personal = Personal::with('categoria')->findOrFail($id);

            $categorias = CategoriaPersonal::select('id', 'nombre_categoria')
                ->orderBy('nombre_categoria')
                ->get();

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

            return view('personal.edit', compact('personal', 'categorias'));
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
    public function update(Request $request, $id)
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

        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'estatus' => ['required', Rule::in(['activo', 'inactivo'])],
            'categoria_id' => 'required|exists:categorias_personal,id',
        ]);

        try {
            $personal = Personal::findOrFail($id);
            $oldData = $personal->toArray();

            $personal->update($request->only([
                'nombre_completo',
                'estatus',
                'categoria_id',
            ]));

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
                ->withErrors(['error' => 'Error al actualizar el personal: '.$e->getMessage()]);
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
                ->withErrors(['error' => 'Error al eliminar el personal: '.$e->getMessage()]);
        }
    }
}
