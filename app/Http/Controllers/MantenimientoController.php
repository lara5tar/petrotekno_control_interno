<?php

namespace App\Http\Controllers;

use App\Http\Requests\MantenimientoRequest;
use App\Models\Mantenimiento;
use Illuminate\Http\Request;

class MantenimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mantenimiento::with(['vehiculo', 'tipoServicio']);

        // Filtros de búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('proveedor', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhereHas('vehiculo', function ($vq) use ($buscar) {
                        $vq->where('marca', 'like', "%{$buscar}%")
                            ->orWhere('modelo', 'like', "%{$buscar}%")
                            ->orWhere('placas', 'like', "%{$buscar}%");
                    });
            });
        }

        // Filtro por vehículo
        if ($request->filled('vehiculo_id')) {
            $query->where('vehiculo_id', $request->vehiculo_id);
        }

        // Filtro por tipo de servicio
        if ($request->filled('tipo_servicio_id')) {
            $query->where('tipo_servicio_id', $request->tipo_servicio_id);
        }

        // Filtro por proveedor
        if ($request->filled('proveedor')) {
            $query->where('proveedor', 'like', "%{$request->proveedor}%");
        }

        // Filtros de fecha
        if ($request->filled('fecha_inicio_desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->fecha_inicio_desde);
        }

        if ($request->filled('fecha_inicio_hasta')) {
            $query->whereDate('fecha_inicio', '<=', $request->fecha_inicio_hasta);
        }

        // Filtros de kilometraje
        if ($request->filled('kilometraje_min')) {
            $query->where('kilometraje_servicio', '>=', $request->kilometraje_min);
        }

        if ($request->filled('kilometraje_max')) {
            $query->where('kilometraje_servicio', '<=', $request->kilometraje_max);
        }

        // Filtros de costo
        if ($request->filled('costo_min')) {
            $query->where('costo', '>=', $request->costo_min);
        }

        if ($request->filled('costo_max')) {
            $query->where('costo', '<=', $request->costo_max);
        }

        // Orden
        $query->orderBy('fecha_inicio', 'desc');

        // Paginación
        $perPage = $request->get('per_page', 15);
        $perPage = max(1, min($perPage, 100)); // Asegurar que esté entre 1 y 100

        $mantenimientos = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $mantenimientos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MantenimientoRequest $request)
    {
        $mantenimiento = Mantenimiento::create($request->validated());
        $mantenimiento->load(['vehiculo', 'tipoServicio']);

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento creado exitosamente',
            'data' => $mantenimiento,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mantenimiento $mantenimiento)
    {
        $mantenimiento->load(['vehiculo', 'tipoServicio', 'documentos']);

        return response()->json([
            'success' => true,
            'data' => $mantenimiento,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MantenimientoRequest $request, Mantenimiento $mantenimiento)
    {
        $mantenimiento->update($request->validated());
        $mantenimiento->load(['vehiculo', 'tipoServicio']);

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento actualizado exitosamente',
            'data' => $mantenimiento,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mantenimiento $mantenimiento)
    {
        $mantenimiento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento eliminado exitosamente',
        ]);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $mantenimiento = Mantenimiento::withTrashed()->findOrFail($id);
        $mantenimiento->restore();

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento restaurado exitosamente',
            'data' => $mantenimiento,
        ]);
    }

    /**
     * Get mantenimientos próximos (por kilometraje)
     */
    public function proximosPorKilometraje(Request $request)
    {
        $limite = $request->get('limite_km', 5000); // 5000 km por defecto

        $mantenimientos = Mantenimiento::with(['vehiculo', 'tipoServicio'])
            ->select('mantenimientos.*')
            ->join('vehiculos', 'mantenimientos.vehiculo_id', '=', 'vehiculos.id')
            ->whereRaw('(vehiculos.kilometraje_actual - mantenimientos.kilometraje_servicio) >= ?', [$limite])
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mantenimientos,
        ]);
    }

    /**
     * Get estadísticas de mantenimientos
     */
    public function estadisticas(Request $request)
    {
        $año = $request->get('año', now()->year);

        $estadisticas = [
            'total_mantenimientos' => Mantenimiento::whereYear('fecha_inicio', $año)->count(),
            'costo_total' => Mantenimiento::whereYear('fecha_inicio', $año)->sum('costo'),
            'mantenimientos_por_mes' => Mantenimiento::whereYear('fecha_inicio', $año)
                ->selectRaw('MONTH(fecha_inicio) as mes, COUNT(*) as total, SUM(costo) as costo_mes')
                ->groupBy('mes')
                ->orderBy('mes')
                ->get(),
            'top_proveedores' => Mantenimiento::whereYear('fecha_inicio', $año)
                ->selectRaw('proveedor, COUNT(*) as total_servicios, SUM(costo) as costo_total')
                ->groupBy('proveedor')
                ->orderBy('total_servicios', 'desc')
                ->limit(10)
                ->get(),
            'vehiculos_mas_mantenimientos' => Mantenimiento::with('vehiculo')
                ->whereYear('fecha_inicio', $año)
                ->selectRaw('vehiculo_id, COUNT(*) as total_mantenimientos, SUM(costo) as costo_total')
                ->groupBy('vehiculo_id')
                ->orderBy('total_mantenimientos', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $estadisticas,
        ]);
    }
}
