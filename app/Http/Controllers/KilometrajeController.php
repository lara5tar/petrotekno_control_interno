<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKilometrajeRequest;
use App\Http\Requests\UpdateKilometrajeRequest;
use App\Models\Kilometraje;
use App\Models\Obra;
use App\Models\Vehiculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class KilometrajeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:ver_kilometrajes')->only(['index', 'show']);
        $this->middleware('can:crear_kilometrajes')->only(['create', 'store']);
        $this->middleware('can:editar_kilometrajes')->only(['edit', 'update']);
        $this->middleware('can:eliminar_kilometrajes')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Kilometraje::with(['vehiculo', 'usuarioCaptura'])
            ->orderedByFecha();

        // Filtros
        if ($request->filled('vehiculo_id')) {
            $query->byVehiculo($request->vehiculo_id);
        }

        if ($request->filled('fecha_inicio')) {
            $fechaFin = $request->filled('fecha_fin') ? $request->fecha_fin : null;
            $query->byFechas($request->fecha_inicio, $fechaFin);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_captura_id', $request->usuario_id);
        }

        // Búsqueda por texto en observaciones
        if ($request->filled('buscar')) {
            $termino = $request->buscar;
            $query->where(function ($q) use ($termino) {
                $q->where('observaciones', 'like', "%{$termino}%")
                    ->orWhereHas('vehiculo', function ($vq) use ($termino) {
                        $vq->where('marca', 'like', "%{$termino}%")
                            ->orWhere('modelo', 'like', "%{$termino}%")
                            ->orWhere('placas', 'like', "%{$termino}%");
                    });
            });
        }

        $kilometrajes = $query->paginate(15)->appends($request->query());

        // Para API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $kilometrajes->items(),
                'meta' => [
                    'current_page' => $kilometrajes->currentPage(),
                    'last_page' => $kilometrajes->lastPage(),
                    'per_page' => $kilometrajes->perPage(),
                    'total' => $kilometrajes->total(),
                ],
            ]);
        }

        // Para Blade
        $vehiculos = Vehiculo::select('id', 'marca', 'modelo', 'placas')
            ->orderBy('marca')
            ->orderBy('modelo')
            ->get();

        /** @phpstan-ignore-next-line */
        return view('kilometrajes.index', compact('kilometrajes', 'vehiculos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|JsonResponse
    {
        $vehiculos = Vehiculo::select('id', 'marca', 'modelo', 'placas', 'kilometraje_actual', 'estatus')
            ->where('estatus', '!=', 'fuera_servicio') // No mostrar vehículos fuera de servicio
            ->orderBy('marca')
            ->orderBy('modelo')
            ->get();

        // Para API - devolver datos del formulario
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'vehiculos' => $vehiculos,
                ],
            ]);
        }

        // Para Blade
        /** @phpstan-ignore-next-line */
        return view('kilometrajes.create', compact('vehiculos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKilometrajeRequest $request): JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $kilometraje = Kilometraje::create($request->validated());

            // Actualizar kilometraje actual del vehículo si es mayor
            $vehiculo = $kilometraje->vehiculo;
            if ($kilometraje->kilometraje > $vehiculo->kilometraje_actual) {
                $vehiculo->update(['kilometraje_actual' => $kilometraje->kilometraje]);
            }

            // Log de auditoría
            Log::info('Kilometraje creado', [
                'usuario_id' => Auth::id(),
                'kilometraje_id' => $kilometraje->id,
                'vehiculo_id' => $kilometraje->vehiculo_id,
                'kilometraje' => $kilometraje->kilometraje,
            ]);

            DB::commit();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kilometraje registrado exitosamente',
                    'data' => $kilometraje->load(['vehiculo', 'usuarioCaptura']),
                ], 201);
            }

            // Para Blade
            return redirect()->route('kilometrajes.index')
                ->with('success', 'Kilometraje registrado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear kilometraje', [
                'usuario_id' => Auth::id(),
                'error' => $e->getMessage(),
                'datos' => $request->validated(),
            ]);

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar el kilometraje',
                    'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor',
                ], 500);
            }

            // Para Blade
            return redirect()->back()
                ->withErrors(['error' => 'Error al registrar el kilometraje'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Kilometraje $kilometraje): View|JsonResponse
    {
        $kilometraje->load(['vehiculo', 'usuarioCaptura']);

        // Calcular próximos mantenimientos
        $alertasMantenimiento = $kilometraje->calcularProximosMantenimientos();

        // Para API
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'kilometraje' => $kilometraje,
                    'alertas_mantenimiento' => $alertasMantenimiento,
                ],
            ]);
        }

        // Para Blade
        /** @phpstan-ignore-next-line */
        return view('kilometrajes.show', compact('kilometraje', 'alertasMantenimiento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kilometraje $kilometraje): View|JsonResponse
    {
        $kilometraje->load(['vehiculo']);

        // Para API
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'kilometraje' => $kilometraje,
                ],
            ]);
        }

        // Para Blade
        /** @phpstan-ignore-next-line */
        return view('kilometrajes.edit', compact('kilometraje'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKilometrajeRequest $request, Kilometraje $kilometraje): JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $oldKilometraje = $kilometraje->kilometraje;
            $kilometraje->update($request->validated());

            // Si se actualizó el kilometraje y es el más reciente del vehículo
            if ($request->filled('kilometraje') && $request->kilometraje !== $oldKilometraje) {
                $ultimoKilometraje = Kilometraje::getUltimoKilometraje($kilometraje->vehiculo_id);
                if ($ultimoKilometraje && $ultimoKilometraje->id === $kilometraje->id) {
                    $kilometraje->vehiculo->update(['kilometraje_actual' => $kilometraje->kilometraje]);
                }
            }

            // Log de auditoría
            Log::info('Kilometraje actualizado', [
                'usuario_id' => Auth::id(),
                'kilometraje_id' => $kilometraje->id,
                'cambios' => $request->validated(),
            ]);

            DB::commit();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kilometraje actualizado exitosamente',
                    'data' => $kilometraje->fresh()->load(['vehiculo', 'usuarioCaptura']),
                ]);
            }

            // Para Blade
            return redirect()->route('kilometrajes.index')
                ->with('success', 'Kilometraje actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar kilometraje', [
                'usuario_id' => Auth::id(),
                'kilometraje_id' => $kilometraje->id,
                'error' => $e->getMessage(),
            ]);

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el kilometraje',
                    'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor',
                ], 500);
            }

            // Para Blade
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar el kilometraje'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kilometraje $kilometraje): JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Verificar si es el kilometraje más reciente del vehículo
            $ultimoKilometraje = Kilometraje::getUltimoKilometraje($kilometraje->vehiculo_id);
            $esUltimo = $ultimoKilometraje && $ultimoKilometraje->id === $kilometraje->id;

            $vehiculoId = $kilometraje->vehiculo_id;
            $kilometraje->delete();

            // Si era el último, actualizar el kilometraje actual del vehículo
            if ($esUltimo) {
                $nuevoUltimo = Kilometraje::getUltimoKilometraje($vehiculoId);
                $nuevoKilometraje = $nuevoUltimo ? $nuevoUltimo->kilometraje : 0;

                Vehiculo::find($vehiculoId)->update(['kilometraje_actual' => $nuevoKilometraje]);
            }

            // Log de auditoría
            Log::info('Kilometraje eliminado', [
                'usuario_id' => Auth::id(),
                'kilometraje_id' => $kilometraje->id,
                'vehiculo_id' => $vehiculoId,
            ]);

            DB::commit();

            // Para API
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kilometraje eliminado exitosamente',
                ]);
            }

            // Para Blade
            return redirect()->route('kilometrajes.index')
                ->with('success', 'Kilometraje eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar kilometraje', [
                'usuario_id' => Auth::id(),
                'kilometraje_id' => $kilometraje->id,
                'error' => $e->getMessage(),
            ]);

            // Para API
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el kilometraje',
                    'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor',
                ], 500);
            }

            // Para Blade
            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar el kilometraje']);
        }
    }

    /**
     * Obtener historial de kilometrajes por vehículo
     */
    public function historialPorVehiculo(Request $request, int $vehiculoId): JsonResponse|View
    {
        $vehiculo = Vehiculo::findOrFail($vehiculoId);

        $kilometrajes = Kilometraje::with(['usuarioCaptura'])
            ->byVehiculo($vehiculoId)
            ->orderedByFecha()
            ->paginate(20);

        // Para API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'vehiculo' => $vehiculo,
                    'kilometrajes' => $kilometrajes->items(),
                    'meta' => [
                        'current_page' => $kilometrajes->currentPage(),
                        'last_page' => $kilometrajes->lastPage(),
                        'per_page' => $kilometrajes->perPage(),
                        'total' => $kilometrajes->total(),
                    ],
                ],
            ]);
        }

        // Para Blade
        /** @phpstan-ignore-next-line */
        return view('kilometrajes.historial', compact('vehiculo', 'kilometrajes'));
    }

    /**
     * Obtener alertas de mantenimiento preventivo
     */
    public function alertasMantenimiento(Request $request): JsonResponse|View
    {
        $alertas = collect();

        // Obtener últimos kilometrajes de todos los vehículos activos
        $vehiculos = Vehiculo::with(['kilometrajes' => function ($query) {
            $query->orderBy('kilometraje', 'desc')->limit(1);
        }])->where('estatus', '!=', 'fuera_servicio')->get(); // No incluir fuera de servicio

        foreach ($vehiculos as $vehiculo) {
            $ultimoKilometraje = $vehiculo->kilometrajes->first();

            if ($ultimoKilometraje) {
                /** @var \App\Models\Kilometraje $ultimoKilometraje */
                $alertasVehiculo = $ultimoKilometraje->calcularProximosMantenimientos();

                foreach ($alertasVehiculo as $alerta) {
                    $alertas->push([
                        'vehiculo' => $vehiculo,
                        'ultimo_kilometraje' => $ultimoKilometraje,
                        'alerta' => $alerta,
                    ]);
                }
            }
        }

        // Ordenar por urgencia y km restantes
        $alertas = $alertas->sortBy([
            ['alerta.urgente', 'desc'],
            ['alerta.km_restantes', 'asc'],
        ])->values();

        // Para API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $alertas,
            ]);
        }

        // Para Blade
        /** @phpstan-ignore-next-line */
        return view('kilometrajes.alertas', compact('alertas'));
    }
}
