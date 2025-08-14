<?php

namespace App\Http\Controllers;

use App\Enums\EstadoVehiculo;
use App\Models\LogAccion;
use App\Models\Personal;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('ver_vehiculos');

        $query = Vehiculo::with(['obras.operador']);

        // Aplicar filtros
        if ($request->filled('buscar')) {
            $termino = $request->get('buscar');
            $query->buscar($termino);
        }

        if ($request->filled('marca')) {
            $query->porMarca($request->get('marca'));
        }

        if ($request->filled('estado')) {
            $query->porEstado($request->get('estado'));
        }

        if ($request->filled('anio')) {
            $query->porAnio($request->get('anio'));
        }

        $vehiculos = $query->orderBy('marca')->orderBy('modelo')->paginate(15);

        // Obtener valores únicos para filtros
        $marcas = Vehiculo::distinct()->pluck('marca')->sort();
        $estados = collect(EstadoVehiculo::cases())->mapWithKeys(function ($estado) {
            return [$estado->value => $estado->nombre()];
        });

        return view('vehiculos.index', compact('vehiculos', 'marcas', 'estados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('crear_vehiculos');

        // Obtener operadores disponibles (personal activo)
        $operadores = Personal::activos()->orderBy('nombre_completo')->get();

        return view('vehiculos.create', compact('operadores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('crear_vehiculos');

        $validatedData = $request->validate([
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'n_serie' => 'required|string|max:100|unique:vehiculos,n_serie',
            'placas' => 'required|string|max:20|unique:vehiculos,placas',
            'kilometraje_actual' => 'required|integer|min:0',
            'intervalo_km_motor' => 'nullable|integer|min:1000',
            'intervalo_km_transmision' => 'nullable|integer|min:5000',
            'intervalo_km_hidraulico' => 'nullable|integer|min:1000',
            'observaciones' => 'nullable|string|max:1000',
            'operador_id' => 'nullable|exists:personal,id',
            
            // Archivos de documentos
            'poliza_seguro_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'derecho_vehicular_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'factura_pedimento_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fotografia_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            
            // Fechas de vencimiento
            'fecha_vencimiento_seguro' => 'nullable|date|after:today',
            'fecha_vencimiento_derecho' => 'nullable|date|after:today',
        ], [
            'marca.required' => 'La marca es obligatoria.',
            'modelo.required' => 'El modelo es obligatorio.',
            'anio.required' => 'El año es obligatorio.',
            'anio.min' => 'El año debe ser mayor a 1990.',
            'anio.max' => 'El año no puede ser mayor al año siguiente.',
            'n_serie.required' => 'El número de serie es obligatorio.',
            'n_serie.unique' => 'Este número de serie ya está registrado.',
            'placas.required' => 'Las placas son obligatorias.',
            'placas.unique' => 'Estas placas ya están registradas.',
            'kilometraje_actual.required' => 'El kilometraje actual es obligatorio.',
            'kilometraje_actual.min' => 'El kilometraje no puede ser negativo.',
            'operador_id.exists' => 'El operador seleccionado no es válido.',
            'poliza_seguro_file.max' => 'La póliza de seguro no puede ser mayor a 5MB.',
            'derecho_vehicular_file.max' => 'El derecho vehicular no puede ser mayor a 5MB.',
            'factura_pedimento_file.max' => 'La factura/pedimento no puede ser mayor a 5MB.',
            'fotografia_file.max' => 'La fotografía no puede ser mayor a 5MB.',
            'fecha_vencimiento_seguro.after' => 'La fecha de vencimiento del seguro debe ser posterior a hoy.',
            'fecha_vencimiento_derecho.after' => 'La fecha de vencimiento del derecho debe ser posterior a hoy.',
        ]);

        DB::beginTransaction();

        try {
            // Crear el vehículo con estado inicial DISPONIBLE
            $vehiculo = Vehiculo::create([
                'marca' => $validatedData['marca'],
                'modelo' => $validatedData['modelo'],
                'anio' => $validatedData['anio'],
                'n_serie' => $validatedData['n_serie'],
                'placas' => $validatedData['placas'],
                'estatus' => EstadoVehiculo::DISPONIBLE->value, // Usar 'estatus' en lugar de 'estado'
                'kilometraje_actual' => $validatedData['kilometraje_actual'],
                'intervalo_km_motor' => $validatedData['intervalo_km_motor'],
                'intervalo_km_transmision' => $validatedData['intervalo_km_transmision'],
                'intervalo_km_hidraulico' => $validatedData['intervalo_km_hidraulico'],
                'observaciones' => $validatedData['observaciones'],
                'operador_id' => $validatedData['operador_id'], // Agregar el operador_id
            ]);

            // Procesar documentos
            $documentos = [];

            // Procesar fotografía del vehículo (se guarda en 'imagen')
            if ($request->hasFile('fotografia_file')) {
                $fotografia = $request->file('fotografia_file');
                $nombreFotografia = time() . '_fotografia_' . $vehiculo->id . '.' . $fotografia->getClientOriginalExtension();
                $rutaFotografia = $fotografia->storeAs('vehiculos/imagenes', $nombreFotografia, 'public');
                
                $vehiculo->update(['imagen' => $rutaFotografia]);
            }

            // Procesar otros documentos (se guardan en documentos_adicionales)
            $tiposDocumentos = [
                'poliza_seguro_file' => 'poliza_seguro',
                'derecho_vehicular_file' => 'derecho_vehicular', 
                'factura_pedimento_file' => 'factura_pedimento',
            ];

            foreach ($tiposDocumentos as $campo => $tipo) {
                if ($request->hasFile($campo)) {
                    $archivo = $request->file($campo);
                    $nombreArchivo = time() . '_' . $tipo . '_' . $vehiculo->id . '.' . $archivo->getClientOriginalExtension();
                    $rutaArchivo = $archivo->storeAs('vehiculos/documentos', $nombreArchivo, 'public');
                    
                    $documentos[$tipo] = [
                        'nombre' => $archivo->getClientOriginalName(),
                        'ruta' => $rutaArchivo,
                        'tipo' => $tipo,
                        'fecha_subida' => now()->toDateTimeString()
                    ];

                    // Agregar fecha de vencimiento si existe
                    if ($tipo === 'poliza_seguro' && $request->filled('fecha_vencimiento_seguro')) {
                        $documentos[$tipo]['fecha_vencimiento'] = $request->get('fecha_vencimiento_seguro');
                    }
                    if ($tipo === 'derecho_vehicular' && $request->filled('fecha_vencimiento_derecho')) {
                        $documentos[$tipo]['fecha_vencimiento'] = $request->get('fecha_vencimiento_derecho');
                    }
                }
            }

            // Actualizar documentos si existen
            if (!empty($documentos)) {
                $vehiculo->update(['documentos_adicionales' => $documentos]);
            }

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear',
                'tabla' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'datos_anteriores' => null,
                'datos_nuevos' => $vehiculo->toArray(),
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Vehículo creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el vehículo: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehiculo $vehiculo)
    {
        $this->authorize('ver_vehiculos');

        // Cargar las relaciones a través del nuevo sistema de asignaciones
        $vehiculo->load([
            'asignacionesObra.obra.operador', 
            'asignacionesObra.operador', 
            'kilometrajes', 
            'mantenimientos', 
            'documentos'
        ]);

        return view('vehiculos.show', compact('vehiculo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehiculo $vehiculo)
    {
        $this->authorize('editar_vehiculos');

        $operadores = Personal::activos()->orderBy('nombre_completo')->get();
        $estados = EstadoVehiculo::cases();

        return view('vehiculos.edit', compact('vehiculo', 'operadores', 'estados'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        $this->authorize('editar_vehiculos');

        $validatedData = $request->validate([
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'n_serie' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vehiculos', 'n_serie')->ignore($vehiculo->id)
            ],
            'placas' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehiculos', 'placas')->ignore($vehiculo->id)
            ],
            'estatus' => ['required', Rule::in(collect(EstadoVehiculo::cases())->pluck('value')->toArray())],
            'kilometraje_actual' => 'required|integer|min:0',
            'intervalo_km_motor' => 'nullable|integer|min:1000',
            'intervalo_km_transmision' => 'nullable|integer|min:5000',
            'intervalo_km_hidraulico' => 'nullable|integer|min:1000',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $datosAnteriores = $vehiculo->toArray();

            $vehiculo->update($validatedData);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar',
                'tabla' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $vehiculo->fresh()->toArray(),
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Vehículo actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el vehículo: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehiculo $vehiculo)
    {
        $this->authorize('eliminar_vehiculos');

        // Verificar que no tenga obras activas
        if ($vehiculo->tieneObraActual()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar un vehículo que tiene obras activas.');
        }

        DB::beginTransaction();

        try {
            $datosAnteriores = $vehiculo->toArray();

            $vehiculo->delete(); // Soft delete

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar',
                'tabla' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => null,
                'fecha_hora' => now(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.index')
                ->with('success', 'Vehículo eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el vehículo: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft deleted vehicle.
     */
    public function restore($id)
    {
        $this->authorize('restaurar_vehiculos');

        $vehiculo = Vehiculo::withTrashed()->findOrFail($id);

        DB::beginTransaction();

        try {
            $vehiculo->restore();

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'restaurar',
                'tabla' => 'vehiculos',
                'registro_id' => $vehiculo->id,
                'datos_anteriores' => null,
                'datos_nuevos' => $vehiculo->fresh()->toArray(),
                'fecha_hora' => now(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Vehículo restaurado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al restaurar el vehículo: ' . $e->getMessage());
        }
    }

    /**
     * Get status options for API
     */
    public function estatusOptions()
    {
        return response()->json([
            'success' => true,
            'data' => collect(EstadoVehiculo::cases())->map(function ($estado) {
                return [
                    'value' => $estado->value,
                    'label' => $estado->getLabel()
                ];
            })
        ]);
    }

    // ================== MÉTODOS DE KILOMETRAJE ==================

    /**
     * Display a listing of kilometrajes for a specific vehicle.
     */
    public function kilometrajes(Request $request, Vehiculo $vehiculo)
    {
        $this->authorize('ver_vehiculos');

        $query = $vehiculo->kilometrajes()->with(['usuarioCaptura']);

        // Aplicar filtros
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_captura', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_captura', '<=', $request->get('fecha_hasta'));
        }

        $kilometrajes = $query->orderBy('fecha_captura', 'desc')->paginate(15);

        return view('vehiculos.kilometrajes.index', compact('vehiculo', 'kilometrajes'));
    }

    /**
     * Show the form for creating a new kilometraje for a vehicle.
     */
    public function createKilometraje(Vehiculo $vehiculo)
    {
        $this->authorize('crear_kilometrajes');

        return view('vehiculos.kilometrajes.create', compact('vehiculo'));
    }

    /**
     * Store a newly created kilometraje for a vehicle.
     */
    public function storeKilometraje(Request $request, Vehiculo $vehiculo)
    {
        $this->authorize('crear_kilometrajes');

        $validatedData = $request->validate([
            'kilometraje' => [
                'required',
                'integer',
                'min:0',
                'gt:' . ($vehiculo->kilometraje_actual ?? 0)
            ],
            'fecha_captura' => 'required|date|before_or_equal:today',
            'observaciones' => 'nullable|string|max:1000',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ], [
            'kilometraje.required' => 'El kilometraje es obligatorio.',
            'kilometraje.gt' => 'El kilometraje debe ser mayor al actual (' . number_format($vehiculo->kilometraje_actual ?? 0) . ').',
            'fecha_captura.required' => 'La fecha de captura es obligatoria.',
            'fecha_captura.before_or_equal' => 'La fecha no puede ser futura.',
        ]);

        DB::beginTransaction();

        try {
            // Procesar imagen si existe
            $rutaImagen = null;
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_km_' . $vehiculo->id . '.' . $imagen->getClientOriginalExtension();
                $rutaImagen = $imagen->storeAs('kilometrajes', $nombreImagen, 'public');
            }

            // Crear el registro de kilometraje
            $kilometraje = $vehiculo->kilometrajes()->create([
                'kilometraje' => $validatedData['kilometraje'],
                'fecha_captura' => $validatedData['fecha_captura'],
                'observaciones' => $validatedData['observaciones'],
                'imagen' => $rutaImagen,
                'usuario_captura_id' => Auth::id(),
            ]);

            // Actualizar el kilometraje actual del vehículo
            $vehiculo->update(['kilometraje_actual' => $validatedData['kilometraje']]);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'crear_kilometraje',
                'tabla' => 'kilometrajes',
                'registro_id' => $kilometraje->id,
                'datos_anteriores' => null,
                'datos_nuevos' => $kilometraje->toArray(),
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Kilometraje registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar el kilometraje: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified kilometraje.
     */
    public function showKilometraje(Vehiculo $vehiculo, $kilometrajeId)
    {
        $this->authorize('ver_vehiculos');

        $kilometraje = $vehiculo->kilometrajes()
            ->with(['usuarioCaptura'])
            ->findOrFail($kilometrajeId);

        return view('vehiculos.kilometrajes.show', compact('vehiculo', 'kilometraje'));
    }

    /**
     * Show the form for editing the specified kilometraje.
     */
    public function editKilometraje(Vehiculo $vehiculo, $kilometrajeId)
    {
        $this->authorize('editar_kilometrajes');

        $kilometraje = $vehiculo->kilometrajes()->findOrFail($kilometrajeId);

        return view('vehiculos.kilometrajes.edit', compact('vehiculo', 'kilometraje'));
    }

    /**
     * Update the specified kilometraje.
     */
    public function updateKilometraje(Request $request, Vehiculo $vehiculo, $kilometrajeId)
    {
        $this->authorize('editar_kilometrajes');

        $kilometraje = $vehiculo->kilometrajes()->findOrFail($kilometrajeId);

        $validatedData = $request->validate([
            'fecha_captura' => 'required|date|before_or_equal:today',
            'observaciones' => 'nullable|string|max:1000',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ], [
            'fecha_captura.required' => 'La fecha de captura es obligatoria.',
            'fecha_captura.before_or_equal' => 'La fecha no puede ser futura.',
        ]);

        DB::beginTransaction();

        try {
            $datosAnteriores = $kilometraje->toArray();

            // Procesar nueva imagen si existe
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($kilometraje->imagen && Storage::disk('public')->exists($kilometraje->imagen)) {
                    Storage::disk('public')->delete($kilometraje->imagen);
                }

                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_km_' . $vehiculo->id . '.' . $imagen->getClientOriginalExtension();
                $validatedData['imagen'] = $imagen->storeAs('kilometrajes', $nombreImagen, 'public');
            }

            $kilometraje->update($validatedData);

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'actualizar_kilometraje',
                'tabla' => 'kilometrajes',
                'registro_id' => $kilometraje->id,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $kilometraje->fresh()->toArray(),
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.kilometrajes.show', [$vehiculo->id, $kilometraje->id])
                ->with('success', 'Kilometraje actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el kilometraje: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified kilometraje.
     */
    public function destroyKilometraje(Request $request, Vehiculo $vehiculo, $kilometrajeId)
    {
        $this->authorize('eliminar_kilometrajes');

        $kilometraje = $vehiculo->kilometrajes()->findOrFail($kilometrajeId);

        DB::beginTransaction();

        try {
            $datosAnteriores = $kilometraje->toArray();

            // Eliminar imagen si existe
            if ($kilometraje->imagen && Storage::disk('public')->exists($kilometraje->imagen)) {
                Storage::disk('public')->delete($kilometraje->imagen);
            }

            $kilometraje->delete();

            // Log de auditoría
            LogAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => 'eliminar_kilometraje',
                'tabla' => 'kilometrajes',
                'registro_id' => $kilometrajeId,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => null,
                'fecha_hora' => now(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('vehiculos.show', $vehiculo->id)
                ->with('success', 'Kilometraje eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el kilometraje: ' . $e->getMessage());
        }
    }
}