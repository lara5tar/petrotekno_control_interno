<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\CategoriaPersonal;
use Illuminate\Http\Request;

class TestPersonalController extends Controller
{
    public function testFiltros(Request $request)
    {
        // Mostrar información de debug
        $debug = [
            'all_parameters' => $request->all(),
            'query_parameters' => $request->query(),
            'filled_checks' => [
                'buscar' => $request->filled('buscar'),
                'estatus' => $request->filled('estatus'),
                'categoria_id' => $request->filled('categoria_id'),
            ],
            'specific_values' => [
                'buscar' => $request->get('buscar'),
                'estatus' => $request->get('estatus'),
                'categoria_id' => $request->get('categoria_id'),
            ]
        ];

        // Crear query base
        $query = Personal::with('categoria');
        $aplicados = [];

        // Aplicar filtros exactamente como en PersonalController
        if ($request->filled('buscar') || $request->filled('search')) {
            $search = $request->get('buscar', $request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                    ->orWhereHas('categoria', function ($cq) use ($search) {
                        $cq->where('nombre_categoria', 'like', "%{$search}%");
                    });
            });
            $aplicados[] = "búsqueda: '$search'";
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
            $aplicados[] = "categoria_id: " . $request->categoria_id;
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
            $aplicados[] = "estatus: " . $request->estatus;
        }

        // Obtener resultados
        $personal = $query->get();
        
        // Obtener categorías para mostrar
        $categorias = CategoriaPersonal::all();

        return response()->json([
            'debug' => $debug,
            'filtros_aplicados' => $aplicados,
            'total_resultados' => $personal->count(),
            'resultados' => $personal->map(function($p) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre_completo,
                    'estatus' => $p->estatus,
                    'categoria' => $p->categoria ? $p->categoria->nombre_categoria : null,
                    'categoria_id' => $p->categoria_id
                ];
            }),
            'categorias_disponibles' => $categorias->map(function($c) {
                return [
                    'id' => $c->id,
                    'nombre' => $c->nombre_categoria
                ];
            })
        ], 200, [], JSON_PRETTY_PRINT);
    }
}