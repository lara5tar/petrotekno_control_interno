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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KilometrajesExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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

            // Obtener datos validados
            $data = $request->validated();
            
            // Obtener el vehículo para conseguir la obra actual
            $vehiculo = Vehiculo::findOrFail($data['vehiculo_id']);
            $obraActual = $vehiculo->obraActual()->first();
            
            // Agregar obra_id si existe una obra actual
            if ($obraActual) {
                $data['obra_id'] = $obraActual->id;
            }

            $kilometraje = Kilometraje::create($data);

            // Actualizar kilometraje actual del vehículo si es mayor
            if ($kilometraje->kilometraje > $vehiculo->kilometraje_actual) {
                $vehiculo->update(['kilometraje_actual' => $kilometraje->kilometraje]);
            }

            // Log de auditoría
            Log::info('Kilometraje creado', [
                'usuario_id' => Auth::id(),
                'kilometraje_id' => $kilometraje->id,
                'vehiculo_id' => $kilometraje->vehiculo_id,
                'obra_id' => $kilometraje->obra_id,
                'kilometraje' => $kilometraje->kilometraje,
            ]);

            DB::commit();

            // Para API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kilometraje registrado exitosamente',
                    'data' => $kilometraje->load(['vehiculo', 'obra', 'usuarioCaptura']),
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
     * Mostrar formulario de carga masiva
     */
    public function cargaMasiva(): View
    {
        $vehiculos = Vehiculo::select('id', 'marca', 'modelo', 'placas', 'kilometraje_actual')
            ->where('estatus', '!=', 'fuera_servicio')
            ->orderBy('marca')
            ->orderBy('modelo')
            ->get();

        return view('kilometrajes.carga-masiva', compact('vehiculos'));
    }

    /**
     * Procesar carga masiva de kilometrajes desde Excel
     */
    public function procesarCargaMasiva(Request $request): JsonResponse
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB máximo
            'modalidad' => 'required|in:vehiculos,registros',
        ]);

        try {
            DB::beginTransaction();

            $archivo = $request->file('archivo');
            $modalidad = $request->modalidad;
            
            // Leer el archivo Excel
            $data = Excel::toArray([], $archivo)[0]; // Primera hoja
            
            // Remover la fila de encabezados
            array_shift($data);
            
            $registrosExitosos = [];
            $registrosFallidos = [];
            $totalProcesados = 0;

            foreach ($data as $index => $fila) {
                $totalProcesados++;
                $numeroFila = $index + 2; // +2 porque empezamos desde la fila 2 (después del header)

                try {
                    if ($modalidad === 'vehiculos') {
                        $resultado = $this->procesarFilaVehiculos($fila, $numeroFila);
                    } else {
                        $resultado = $this->procesarFilaRegistros($fila, $numeroFila);
                    }

                    if ($resultado['exito']) {
                        $registrosExitosos[] = $resultado['data'];
                    } else {
                        $registrosFallidos[] = $resultado;
                    }
                } catch (\Exception $e) {
                    $registrosFallidos[] = [
                        'fila' => $numeroFila,
                        'error' => 'Error inesperado: ' . $e->getMessage(),
                        'data' => $fila
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Carga masiva procesada exitosamente',
                'data' => [
                    'total_procesados' => $totalProcesados,
                    'exitosos' => count($registrosExitosos),
                    'fallidos' => count($registrosFallidos),
                    'registros_exitosos' => $registrosExitosos,
                    'registros_fallidos' => $registrosFallidos
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en carga masiva de kilometrajes', [
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la carga masiva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar fila para modalidad de múltiples vehículos
     */
    private function procesarFilaVehiculos(array $fila, int $numeroFila): array
    {
        // Formato esperado: [vehiculo_id, fecha, kilometraje, cantidad_combustible, observaciones]
        if (count($fila) < 3) {
            return [
                'exito' => false,
                'fila' => $numeroFila,
                'error' => 'Faltan columnas requeridas (mínimo: ID vehículo, fecha, kilometraje)',
                'data' => $fila
            ];
        }

        $vehiculoId = trim($fila[0]);
        $fecha = $fila[1];
        $kilometraje = $fila[2];
        $cantidadCombustible = isset($fila[3]) && !empty(trim($fila[3])) ? (float) trim($fila[3]) : null;
        $observaciones = isset($fila[4]) ? trim($fila[4]) : null;

        // Validar datos
        if (empty($vehiculoId) || empty($fecha) || empty($kilometraje)) {
            return [
                'exito' => false,
                'fila' => $numeroFila,
                'error' => 'Campos requeridos vacíos (ID vehículo, fecha, kilometraje)',
                'data' => $fila
            ];
        }

        // Buscar vehículo por ID
        $vehiculo = Vehiculo::find($vehiculoId);
        if (!$vehiculo) {
            return [
                'exito' => false,
                'fila' => $numeroFila,
                'error' => "Vehículo con ID '{$vehiculoId}' no encontrado",
                'data' => $fila
            ];
        }

        // Convertir fecha
        try {
            if (is_numeric($fecha)) {
                // Fecha de Excel (número serial)
                $fechaCaptura = Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($fecha - 2);
            } else {
                $fechaCaptura = Carbon::parse($fecha);
            }
        } catch (\Exception $e) {
            return [
                'exito' => false,
                'fila' => $numeroFila,
                'error' => "Formato de fecha inválido: {$fecha}",
                'data' => $fila
            ];
        }

        // Validar kilometraje
        if (!is_numeric($kilometraje) || $kilometraje < 0) {
            return [
                'exito' => false,
                'fila' => $numeroFila,
                'error' => "Kilometraje inválido: {$kilometraje}",
                'data' => $fila
            ];
        }

        // Crear registro
        $obraActual = $vehiculo->obraActual()->first();
        
        $kilometrajeRecord = Kilometraje::create([
            'vehiculo_id' => $vehiculo->id,
            'obra_id' => $obraActual ? $obraActual->id : null,
            'kilometraje' => (int) $kilometraje,
            'fecha_captura' => $fechaCaptura,
            'usuario_captura_id' => Auth::id(),
            'observaciones' => $observaciones,
            'cantidad_combustible' => $cantidadCombustible,
        ]);

        // Actualizar kilometraje actual del vehículo si es mayor
        if ($kilometraje > $vehiculo->kilometraje_actual) {
            $vehiculo->update(['kilometraje_actual' => $kilometraje]);
        }

        return [
            'exito' => true,
            'data' => [
                'id' => $kilometrajeRecord->id,
                'vehiculo' => $vehiculo->placas,
                'kilometraje' => $kilometraje,
                'fecha' => $fechaCaptura->format('d/m/Y')
            ]
        ];
    }

    /**
     * Procesar fila para modalidad de múltiples registros de un vehículo
     */
    private function procesarFilaRegistros(array $fila, int $numeroFila): array
    {
        // Formato esperado: [vehiculo_id, fecha, kilometraje, cantidad_combustible, observaciones]
        if (count($fila) < 3) {
            return [
                'exito' => false,
                'fila' => $numeroFila,
                'error' => 'Faltan columnas requeridas (mínimo: vehículo_id, fecha, kilometraje)',
                'data' => $fila
            ];
        }

        $vehiculoId = $fila[0];
        $fecha = $fila[1];
        $kilometraje = $fila[2];
        $cantidadCombustible = isset($fila[3]) && !empty(trim($fila[3])) ? (float) trim($fila[3]) : null;
        $observaciones = isset($fila[4]) ? trim($fila[4]) : null;

        // Validar datos
        if (empty($vehiculoId) || empty($fecha) || empty($kilometraje)) {
            return [
                'exito' => false,
                'fila' => $numeroFila,
                'error' => 'Campos requeridos vacíos (vehículo_id, fecha, kilometraje)',
                'data' => $fila
            ];
        }

        // Buscar vehículo
        $vehiculo = Vehiculo::find($vehiculoId);
        if (!$vehiculo) {
            return [
                'exito' => false,
                'fila' => $numeroFila,
                'error' => "Vehículo con ID '{$vehiculoId}' no encontrado",
                'data' => $fila
            ];
        }

        // Convertir fecha
        try {
            if (is_numeric($fecha)) {
                $fechaCaptura = Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($fecha - 2);
            } else {
                $fechaCaptura = Carbon::parse($fecha);
            }
        } catch (\Exception $e) {
            return [
                'exito' => false,
                'fila' => $numeroFila,
                'error' => "Formato de fecha inválido: {$fecha}",
                'data' => $fila
            ];
        }

        // Validar kilometraje
        if (!is_numeric($kilometraje) || $kilometraje < 0) {
            return [
                'exito' => false,
                'fila' => $numeroFila,
                'error' => "Kilometraje inválido: {$kilometraje}",
                'data' => $fila
            ];
        }

        // Crear registro
        $obraActual = $vehiculo->obraActual()->first();
        
        $kilometrajeRecord = Kilometraje::create([
            'vehiculo_id' => $vehiculo->id,
            'obra_id' => $obraActual ? $obraActual->id : null,
            'kilometraje' => (int) $kilometraje,
            'fecha_captura' => $fechaCaptura,
            'usuario_captura_id' => Auth::id(),
            'observaciones' => $observaciones,
            'cantidad_combustible' => $cantidadCombustible,
        ]);

        // Actualizar kilometraje actual del vehículo si es mayor
        if ($kilometraje > $vehiculo->kilometraje_actual) {
            $vehiculo->update(['kilometraje_actual' => $kilometraje]);
        }

        return [
            'exito' => true,
            'data' => [
                'id' => $kilometrajeRecord->id,
                'vehiculo' => $vehiculo->placas,
                'kilometraje' => $kilometraje,
                'fecha' => $fechaCaptura->format('d/m/Y')
            ]
        ];
    }

    /**
     * Descargar plantilla Excel para carga masiva
     */
    public function descargarPlantilla(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $modalidad = $request->get('modalidad', 'vehiculos');
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        if ($modalidad === 'vehiculos') {
            // Plantilla para múltiples vehículos
            $sheet->setCellValue('A1', 'ID Vehículo');
            $sheet->setCellValue('B1', 'Fecha (DD/MM/AAAA)');
            $sheet->setCellValue('C1', 'Kilometraje');
            $sheet->setCellValue('D1', 'Cantidad Combustible (L)');
            $sheet->setCellValue('E1', 'Observaciones');
            
            // Ejemplos
            $sheet->setCellValue('A2', '1');
            $sheet->setCellValue('B2', '01/01/2024');
            $sheet->setCellValue('C2', '15000');
            $sheet->setCellValue('D2', '50.5');
            $sheet->setCellValue('E2', 'Mantenimiento preventivo');
            
            $sheet->setCellValue('A3', '2');
            $sheet->setCellValue('B3', '02/01/2024');
            $sheet->setCellValue('C3', '25000');
            $sheet->setCellValue('D3', '45.0');
            $sheet->setCellValue('E3', 'Revisión general');
            
            $filename = 'plantilla_kilometrajes_vehiculos.xlsx';
        } else {
            // Plantilla para múltiples registros de un vehículo
            $sheet->setCellValue('A1', 'ID Vehículo');
            $sheet->setCellValue('B1', 'Fecha (DD/MM/AAAA)');
            $sheet->setCellValue('C1', 'Kilometraje');
            $sheet->setCellValue('D1', 'Cantidad Combustible (L)');
            $sheet->setCellValue('E1', 'Observaciones');
            
            // Ejemplos
            $sheet->setCellValue('A2', '1');
            $sheet->setCellValue('B2', '01/01/2024');
            $sheet->setCellValue('C2', '15000');
            $sheet->setCellValue('D2', '48.2');
            $sheet->setCellValue('E2', 'Primer registro');
            
            $sheet->setCellValue('A3', '1');
            $sheet->setCellValue('B3', '15/01/2024');
            $sheet->setCellValue('C3', '15500');
            $sheet->setCellValue('D3', '52.1');
            $sheet->setCellValue('E3', 'Segundo registro');
            
            $filename = 'plantilla_kilometrajes_registros.xlsx';
        }
        
        // Aplicar estilos a los encabezados
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE6E6E6');
        
        // Ajustar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(30);
        
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'plantilla_kilometrajes');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Carga manual individual desde el widget
     */
    public function cargaManual(Request $request): JsonResponse
    {
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha_captura' => 'required|date',
            'kilometraje' => 'required|integer|min:0',
            'observaciones' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::findOrFail($request->vehiculo_id);
            $obraActual = $vehiculo->obraActual()->first();

            $kilometraje = Kilometraje::create([
                'vehiculo_id' => $request->vehiculo_id,
                'obra_id' => $obraActual ? $obraActual->id : null,
                'kilometraje' => $request->kilometraje,
                'fecha_captura' => $request->fecha_captura,
                'usuario_captura_id' => Auth::id(),
                'observaciones' => $request->observaciones
            ]);

            // Actualizar kilometraje actual del vehículo si es mayor
            if ($request->kilometraje > $vehiculo->kilometraje_actual) {
                $vehiculo->update(['kilometraje_actual' => $request->kilometraje]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kilometraje registrado exitosamente',
                'data' => [
                    'id' => $kilometraje->id,
                    'vehiculo' => $vehiculo->marca . ' ' . $vehiculo->modelo . ' (' . $vehiculo->placas . ')',
                    'kilometraje' => number_format($kilometraje->kilometraje),
                    'fecha' => $kilometraje->fecha_captura->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en carga manual de kilometraje', [
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
                'vehiculo_id' => $request->vehiculo_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el kilometraje: ' . $e->getMessage()
            ], 500);
        }
    }
}
