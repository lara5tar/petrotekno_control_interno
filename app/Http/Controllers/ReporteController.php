<?php

namespace App\Http\Controllers;

use App\Enums\EstadoVehiculo;
use App\Models\Vehiculo;
use App\Models\Kilometraje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Mostrar el índice de reportes disponibles
     */
    public function index()
    {
        $this->checkPermission('ver_reportes');

        return view('reportes.index');
    }

    /**
     * Reporte de inventario de vehículos con último kilometraje
     */
    public function inventarioVehiculos(Request $request)
    {
        $this->checkPermission('ver_reportes');

        // Obtener filtros
        $estatus = $request->get('estatus');
        $marca = $request->get('marca');
        $anio = $request->get('anio');
        $formato = $request->get('formato', 'html'); // html, excel, pdf

        // Query base para vehículos con su último kilometraje
        $query = Vehiculo::select([
            'vehiculos.id',
            'vehiculos.marca',
            'vehiculos.modelo',
            'vehiculos.anio',
            'vehiculos.placas',
            'vehiculos.n_serie',
            'vehiculos.estatus',
            'vehiculos.kilometraje_actual',
            'vehiculos.intervalo_km_motor',
            'vehiculos.intervalo_km_transmision',
            'vehiculos.intervalo_km_hidraulico',
            'vehiculos.created_at'
        ])
        ->leftJoin('kilometrajes as k', function($join) {
            $join->on('vehiculos.id', '=', 'k.vehiculo_id')
                 ->whereRaw('k.id = (SELECT MAX(id) FROM kilometrajes WHERE vehiculo_id = vehiculos.id)');
        })
        ->addSelect([
            'k.kilometraje as ultimo_kilometraje_registrado',
            'k.fecha_captura as fecha_ultimo_kilometraje',
            'k.observaciones as observaciones_ultimo_km'
        ]);

        // Aplicar filtros
        if ($estatus) {
            $query->where('vehiculos.estatus', $estatus);
        }

        if ($marca) {
            $query->where('vehiculos.marca', 'like', "%{$marca}%");
        }

        if ($anio) {
            $query->where('vehiculos.anio', $anio);
        }

        // Ordenar por marca, modelo y año
        $vehiculos = $query->orderBy('vehiculos.marca')
                          ->orderBy('vehiculos.modelo')
                          ->orderBy('vehiculos.anio')
                          ->get();

        // Obtener datos para filtros
        $marcasDisponibles = Vehiculo::distinct()
            ->pluck('marca')
            ->sort()
            ->values();

        $aniosDisponibles = Vehiculo::distinct()
            ->pluck('anio')
            ->sort()
            ->values();

        $estatusDisponibles = EstadoVehiculo::cases();

        // Estadísticas del reporte
        $estadisticas = [
            'total_vehiculos' => $vehiculos->count(),
            'por_estatus' => $vehiculos->groupBy('estatus')->map->count(),
            'kilometraje_total' => $vehiculos->sum('kilometraje_actual'),
            'kilometraje_promedio' => $vehiculos->avg('kilometraje_actual'),
            'vehiculos_con_kilometraje_registrado' => $vehiculos->whereNotNull('ultimo_kilometraje_registrado')->count(),
            'vehiculos_sin_kilometraje_registrado' => $vehiculos->whereNull('ultimo_kilometraje_registrado')->count(),
            // Agregar estadísticas específicas por estado para la vista PDF
            'vehiculos_disponibles' => $vehiculos->where('estatus', EstadoVehiculo::DISPONIBLE)->count(),
            'vehiculos_asignados' => $vehiculos->where('estatus', EstadoVehiculo::ASIGNADO)->count(),
            'vehiculos_mantenimiento' => $vehiculos->where('estatus', EstadoVehiculo::EN_MANTENIMIENTO)->count(),
            'vehiculos_fuera_servicio' => $vehiculos->where('estatus', EstadoVehiculo::FUERA_DE_SERVICIO)->count(),
            'vehiculos_baja' => $vehiculos->where('estatus', EstadoVehiculo::BAJA)->count(),
        ];

        // Procesar cada vehículo para agregar información adicional
        $vehiculos = $vehiculos->map(function($vehiculo) {
            // Determinar estado del enum
            $vehiculo->estado_enum = $vehiculo->estatus instanceof EstadoVehiculo 
                ? $vehiculo->estatus 
                : EstadoVehiculo::fromValue($vehiculo->estatus);
            
            // Convertir fecha_ultimo_kilometraje a Carbon si es string
            if ($vehiculo->fecha_ultimo_kilometraje && is_string($vehiculo->fecha_ultimo_kilometraje)) {
                $vehiculo->fecha_ultimo_kilometraje = \Carbon\Carbon::parse($vehiculo->fecha_ultimo_kilometraje);
            }
            
            // Calcular diferencia entre kilometraje actual y último registrado
            if ($vehiculo->ultimo_kilometraje_registrado) {
                $vehiculo->diferencia_kilometraje = $vehiculo->kilometraje_actual - $vehiculo->ultimo_kilometraje_registrado;
            } else {
                $vehiculo->diferencia_kilometraje = null;
            }

            // Determinar si necesita registro de kilometraje (más de 7 días sin registrar)
            $vehiculo->necesita_registro_km = false;
            if ($vehiculo->fecha_ultimo_kilometraje) {
                $diasSinRegistro = now()->diffInDays($vehiculo->fecha_ultimo_kilometraje);
                $vehiculo->necesita_registro_km = $diasSinRegistro > 7;
                $vehiculo->dias_sin_registro = $diasSinRegistro;
            } else {
                $vehiculo->necesita_registro_km = true;
                $vehiculo->dias_sin_registro = null;
            }

            return $vehiculo;
        });

        // Manejar diferentes formatos de respuesta
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'vehiculos' => $vehiculos,
                    'estadisticas' => $estadisticas,
                    'filtros' => [
                        'marcas' => $marcasDisponibles,
                        'anios' => $aniosDisponibles,
                        'estatus' => $estatusDisponibles,
                    ]
                ]
            ]);
        }

        // Exportar a Excel si se solicita
        if ($formato === 'excel') {
            return $this->exportarInventarioExcel($vehiculos, $estadisticas);
        }

        // Exportar a PDF si se solicita
        if ($formato === 'pdf') {
            return $this->exportarInventarioPdf($vehiculos, $estadisticas);
        }

        // Vista HTML por defecto
        return view('reportes.inventario-vehiculos', compact(
            'vehiculos',
            'estadisticas',
            'marcasDisponibles',
            'aniosDisponibles',
            'estatusDisponibles',
            'estatus',
            'marca',
            'anio'
        ));
    }

    /**
     * Exportar inventario a Excel
     */
    private function exportarInventarioExcel($vehiculos, $estadisticas)
    {
        // Esta funcionalidad se puede implementar con Laravel Excel
        // Por ahora devolvemos un CSV básico
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="inventario_vehiculos_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($vehiculos) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fputs($file, "\xEF\xBB\xBF");
            
            // Encabezados
            fputcsv($file, [
                'ID',
                'Marca',
                'Modelo',
                'Año',
                'Placas',
                'No. Serie',
                'Estatus',
                'Kilometraje Actual',
                'Último Km Registrado',
                'Fecha Último Registro',
                'Diferencia Km',
                'Días Sin Registro',
                'Necesita Registro'
            ], ';');

            // Datos
            foreach ($vehiculos as $vehiculo) {
                fputcsv($file, [
                    $vehiculo->id,
                    $vehiculo->marca,
                    $vehiculo->modelo,
                    $vehiculo->anio,
                    $vehiculo->placas,
                    $vehiculo->n_serie,
                    $vehiculo->estado_enum->nombre(),
                    number_format($vehiculo->kilometraje_actual, 0, ',', '.'),
                    $vehiculo->ultimo_kilometraje_registrado ? number_format($vehiculo->ultimo_kilometraje_registrado, 0, ',', '.') : 'Sin registro',
                    $vehiculo->fecha_ultimo_kilometraje ? $vehiculo->fecha_ultimo_kilometraje->format('d/m/Y') : 'Sin registro',
                    $vehiculo->diferencia_kilometraje !== null ? number_format($vehiculo->diferencia_kilometraje, 0, ',', '.') : 'N/A',
                    $vehiculo->dias_sin_registro ?? 'Sin registro',
                    $vehiculo->necesita_registro_km ? 'Sí' : 'No'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar inventario a PDF
     */
    private function exportarInventarioPdf($vehiculos, $estadisticas)
    {
        // Configurar DomPDF
        $pdf = Pdf::loadView('reportes.inventario-vehiculos-pdf', compact(
            'vehiculos',
            'estadisticas'
        ));
        
        // Configurar opciones del PDF
        $pdf->setPaper('A4', 'landscape'); // Horizontal para mejor visualización de la tabla
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial'
        ]);
        
        // Generar nombre del archivo con fecha
        $fecha = now()->format('Y-m-d_H-i-s');
        $nombreArchivo = "inventario_vehiculos_{$fecha}.pdf";
        
        // Descargar el PDF
        return $pdf->download($nombreArchivo);
    }

    /**
     * Reporte de vehículos disponibles
     */
    public function vehiculosDisponibles(Request $request)
    {
        $request->merge(['estatus' => EstadoVehiculo::DISPONIBLE->value]);
        return $this->inventarioVehiculos($request);
    }

    /**
     * Reporte de vehículos asignados
     */
    public function vehiculosAsignados(Request $request)
    {
        $request->merge(['estatus' => EstadoVehiculo::ASIGNADO->value]);
        return $this->inventarioVehiculos($request);
    }

    /**
     * Reporte de vehículos en mantenimiento
     */
    public function vehiculosEnMantenimiento(Request $request)
    {
        $request->merge(['estatus' => EstadoVehiculo::EN_MANTENIMIENTO->value]);
        return $this->inventarioVehiculos($request);
    }

    /**
     * Reporte de vehículos fuera de servicio
     */
    public function vehiculosFueraServicio(Request $request)
    {
        $request->merge(['estatus' => EstadoVehiculo::FUERA_DE_SERVICIO->value]);
        return $this->inventarioVehiculos($request);
    }

    /**
     * Reporte de vehículos dados de baja
     */
    public function vehiculosBaja(Request $request)
    {
        $request->merge(['estatus' => EstadoVehiculo::BAJA->value]);
        return $this->inventarioVehiculos($request);
    }

    /**
     * Verificar permisos de manera centralizada
     */
    private function checkPermission($ability)
    {
        if (!Auth::user()->hasPermission($ability)) {
            abort(403, 'No tienes permisos para acceder a los reportes.');
        }
    }
}
