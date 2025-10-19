<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PersonalSearchController extends Controller
{
    /**
     * Constructor - Verificar autenticación
     */
    public function __construct()
    {
        // Las rutas están en web.php con auth middleware
    }

    /**
     * Búsqueda en tiempo real de personal
     */
    public function search(Request $request): JsonResponse
    {
        // Verificar autorización
        if (!auth()->check() || !auth()->user()->hasPermission('ver_personal')) {
            return response()->json([
                'personal' => [],
                'total' => 0,
                'mensaje' => 'No tienes permisos para ver personal'
            ], 403);
        }

        $termino = $request->get('q', $request->get('buscar', ''));
        $estatus = $request->get('estatus');
        $categoria_id = $request->get('categoria_id');
        $limit = $request->get('limit', 10);

        // Si no hay término de búsqueda, devolver array vacío
        if (empty(trim($termino))) {
            return response()->json([
                'personal' => [],
                'total' => 0,
                'mensaje' => 'Ingrese un término de búsqueda'
            ]);
        }

        // Query base
        $query = Personal::with(['categoria']);

        // Aplicar búsqueda
        $query->where(function ($q) use ($termino) {
            $q->where('nombre_completo', 'like', "%{$termino}%")
              ->orWhere('rfc', 'like', "%{$termino}%")
              ->orWhere('nss', 'like', "%{$termino}%")
              ->orWhere('ine', 'like', "%{$termino}%")
              ->orWhere('curp_numero', 'like', "%{$termino}%")
              ->orWhere('no_licencia', 'like', "%{$termino}%")
              ->orWhereHas('categoria', function ($categoryQuery) use ($termino) {
                  $categoryQuery->where('nombre_categoria', 'like', "%{$termino}%");
              });
        });

        // Aplicar filtros adicionales
        if ($estatus) {
            $query->where('estatus', $estatus);
        }

        if ($categoria_id) {
            $query->where('categoria_id', $categoria_id);
        }

        // Obtener resultados limitados
        $personal = $query->orderBy('nombre_completo')
                         ->limit($limit)
                         ->get();

        // Formatear resultados para la respuesta
        $resultados = $personal->map(function ($persona) {
            return [
                'id' => $persona->id,
                'nombre_completo' => $persona->nombre_completo,
                'rfc' => $persona->rfc,
                'nss' => $persona->nss,
                'ine' => $persona->ine,
                'curp_numero' => $persona->curp_numero,
                'estatus' => $persona->estatus,
                'categoria' => $persona->categoria?->nombre_categoria ?? 'Sin categoría',
                'categoria_id' => $persona->categoria_id,
                'created_at' => $persona->created_at?->format('d/m/Y'),
                'url' => route('personal.show', $persona->id)
            ];
        });

        return response()->json([
            'personal' => $resultados,
            'total' => $personal->count(),
            'limite_alcanzado' => $personal->count() >= $limit,
            'mensaje' => $personal->count() > 0 
                ? "Se encontraron {$personal->count()} personas" 
                : 'No se encontraron personas con ese término'
        ]);
    }

    /**
     * Sugerencias de búsqueda
     */
    public function suggestions(Request $request): JsonResponse
    {
        $termino = $request->get('q', $request->get('buscar', ''));
        
        if (strlen($termino) < 2) {
            return response()->json(['sugerencias' => []]);
        }

        // Obtener sugerencias de nombres y categorías únicos
        $nombres = Personal::select('nombre_completo')
                          ->where('nombre_completo', 'like', "%{$termino}%")
                          ->distinct()
                          ->limit(5)
                          ->pluck('nombre_completo');

        $categorias = Personal::with('categoria')
                            ->whereHas('categoria', function ($query) use ($termino) {
                                $query->where('nombre_categoria', 'like', "%{$termino}%");
                            })
                            ->distinct()
                            ->limit(5)
                            ->get()
                            ->pluck('categoria.nombre_categoria')
                            ->unique();

        $sugerencias = collect()
            ->merge($nombres->map(fn($nombre) => ['tipo' => 'nombre', 'valor' => $nombre]))
            ->merge($categorias->map(fn($categoria) => ['tipo' => 'categoria', 'valor' => $categoria]))
            ->take(8);

        return response()->json(['sugerencias' => $sugerencias]);
    }
}