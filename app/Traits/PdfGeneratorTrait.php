<?php

namespace App\Traits;

use Barryvdh\DomPDF\Facade\Pdf;

trait PdfGeneratorTrait
{
    /**
     * Configuración estándar para todos los PDFs del sistema
     */
    protected function getStandardPdfOptions(): array
    {
        return [
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 150,
            'defaultMediaType' => 'print',
            'isFontSubsettingEnabled' => true,
            'isRemoteEnabled' => true
        ];
    }

    /**
     * Crear PDF con configuración estándar
     */
    protected function createStandardPdf(string $view, array $data, string $orientation = 'portrait'): \Barryvdh\DomPDF\PDF
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('A4', $orientation);
        $pdf->setOptions($this->getStandardPdfOptions());
        
        return $pdf;
    }

    /**
     * Generar nombre de archivo estándar para PDFs
     */
    protected function generatePdfFilename(string $type, array $identifiers = []): string
    {
        $fecha = now()->format('Y-m-d_H-i-s');
        $filename = $type;
        
        if (!empty($identifiers)) {
            $identifierString = implode('_', array_map(function($id) {
                return str_replace([' ', '-', '.', '/', '\\'], '_', $id);
            }, $identifiers));
            $filename .= "_{$identifierString}";
        }
        
        return "{$filename}_{$fecha}.pdf";
    }

    /**
     * Crear PDF de inventario de vehículos
     */
    protected function createInventarioPdf($vehiculos, $estadisticas, $filtros = [])
    {
        // Asegurar que cada vehículo tenga cargada la relación tipoActivo y el nombre del tipo
        $vehiculos = $vehiculos->map(function($vehiculo) {
            // Cargar la relación tipoActivo si no está cargada
            if (!$vehiculo->relationLoaded('tipoActivo')) {
                $vehiculo->load('tipoActivo');
            }
            
            // Asegurar que el tipo de activo esté disponible para el reporte
            if ($vehiculo->tipoActivo) {
                $vehiculo->tipo_activo_nombre = $vehiculo->tipoActivo->nombre;
            } else {
                $vehiculo->tipo_activo_nombre = 'Sin tipo';
            }
            
            return $vehiculo;
        });

        $pdf = $this->createStandardPdf('pdf.reportes.inventario-vehiculos', compact(
            'vehiculos', 'estadisticas', 'filtros'
        ), 'landscape');
        
        $filename = $this->generatePdfFilename('inventario_vehiculos');
        return $pdf->download($filename);
    }

    /**
     * Crear PDF de historial de obras por vehículo
     */
    protected function createHistorialObrasVehiculoPdf($asignaciones, $estadisticas, $filtros = [], $vehiculo = null)
    {
        $pdf = $this->createStandardPdf('pdf.reportes.historial-obras-vehiculo', compact(
            'asignaciones', 'estadisticas', 'filtros', 'vehiculo'
        ), 'landscape');
        
        $identifiers = [];
        if ($vehiculo) {
            $identifiers[] = $vehiculo->marca;
            $identifiers[] = $vehiculo->modelo;
            $identifiers[] = $vehiculo->placas ?: 'sin_placas';
        }
        
        $filename = $this->generatePdfFilename('historial_obras_vehiculo', $identifiers);
        return $pdf->download($filename);
    }

    /**
     * Crear PDF de historial de obras por operador
     */
    protected function createHistorialObrasOperadorPdf($asignaciones, $estadisticas, $filtros = [], $operador = null)
    {
        $pdf = $this->createStandardPdf('pdf.reportes.historial-obras-operador', compact(
            'asignaciones', 'estadisticas', 'filtros', 'operador'
        ), 'landscape');
        
        $identifiers = [];
        if ($operador) {
            $identifiers[] = $operador->nombre_completo;
        }
        
        $filename = $this->generatePdfFilename('historial_obras_operador', $identifiers);
        return $pdf->download($filename);
    }

    /**
     * Crear PDF de historial de mantenimientos por vehículo
     */
    protected function createHistorialMantenimientosPdf($mantenimientos, $estadisticas, $filtros = [], $vehiculo = null)
    {
        $pdf = $this->createStandardPdf('pdf.reportes.historial-mantenimientos-vehiculo', compact(
            'mantenimientos', 'estadisticas', 'filtros', 'vehiculo'
        ), 'portrait');
        
        $identifiers = [];
        if ($vehiculo) {
            $identifiers[] = $vehiculo->marca;
            $identifiers[] = $vehiculo->modelo;
            $identifiers[] = $vehiculo->placas ?: 'sin_placas';
        }
        
        $filename = $this->generatePdfFilename('historial_mantenimientos_vehiculo', $identifiers);
        return $pdf->download($filename);
    }

    /**
     * Crear PDF de alertas de mantenimiento
     */
    protected function createAlertasMantenimientoPdf($alertas, $resumen, $fechaGeneracion = null)
    {
        $fechaGeneracion = $fechaGeneracion ?: now()->format('d/m/Y H:i:s');
        
        $pdf = $this->createStandardPdf('pdf.reportes.alertas-mantenimiento', compact(
            'alertas', 'resumen', 'fechaGeneracion'
        ), 'portrait');
        
        $filename = $this->generatePdfFilename('alertas_mantenimiento');
        return $pdf->download($filename);
    }

    /**
     * Crear PDF de vehículos filtrados
     */
    protected function createVehiculosFiltradosPdf($vehiculos, $estadisticas, $filtros = [])
    {
        // Asegurar que cada vehículo tenga cargada la relación tipoActivo y el nombre del tipo
        $vehiculos = $vehiculos->map(function($vehiculo) {
            // Cargar la relación tipoActivo si no está cargada
            if (!$vehiculo->relationLoaded('tipoActivo')) {
                $vehiculo->load('tipoActivo');
            }
            
            // Asegurar que el tipo de activo esté disponible para el reporte
            if ($vehiculo->tipoActivo) {
                $vehiculo->tipo_activo_nombre = $vehiculo->tipoActivo->nombre;
            } else {
                $vehiculo->tipo_activo_nombre = 'Sin tipo';
            }
            
            return $vehiculo;
        });

        $pdf = $this->createStandardPdf('pdf.reportes.vehiculos-filtrados', compact(
            'vehiculos', 'estadisticas', 'filtros'
        ), 'landscape');
        
        $filename = $this->generatePdfFilename('vehiculos_filtrados');
        return $pdf;
    }

    /**
     * Obtener estadísticas estándar para vehículos
     */
    protected function getVehiculosEstadisticas($vehiculos): array
    {
        $total = $vehiculos->count();
        
        return [
            'total' => $total,
            'por_estado' => [
                'disponible' => $vehiculos->where('estatus', 'disponible')->count(),
                'asignado' => $vehiculos->where('estatus', 'asignado')->count(),
                'mantenimiento' => $vehiculos->where('estatus', 'mantenimiento')->count(),
                'fuera_servicio' => $vehiculos->where('estatus', 'fuera_servicio')->count(),
                'baja' => $vehiculos->where('estatus', 'baja')->count(),
            ],
            'con_kilometraje' => $vehiculos->whereNotNull('kilometraje_actual')->count(),
            'promedio_kilometraje' => $vehiculos->whereNotNull('kilometraje_actual')->avg('kilometraje_actual'),
            'mayor_kilometraje' => $vehiculos->whereNotNull('kilometraje_actual')->max('kilometraje_actual'),
            'menor_kilometraje' => $vehiculos->whereNotNull('kilometraje_actual')->min('kilometraje_actual'),
        ];
    }

    /**
     * Obtener estadísticas estándar para asignaciones
     */
    protected function getAsignacionesEstadisticas($asignaciones): array
    {
        return [
            'total_asignaciones' => $asignaciones->count(),
            'obras_activas' => $asignaciones->where('estado', 'activo')->count(),
            'obras_finalizadas' => $asignaciones->where('estado', 'finalizado')->count(),
            'vehiculos_distintos' => $asignaciones->pluck('vehiculo_id')->unique()->count(),
            'total_kilometros' => $asignaciones->whereNotNull('kilometraje_inicial')
                                              ->whereNotNull('kilometraje_final')
                                              ->sum(function($a) {
                                                  return $a->kilometraje_final - $a->kilometraje_inicial;
                                              }),
            'promedio_dias' => $asignaciones->whereNotNull('fecha_asignacion')->avg(function($a) {
                $fechaFin = $a->fecha_finalizacion ?: now();
                return floor(\Carbon\Carbon::parse($a->fecha_asignacion)->diffInDays($fechaFin));
            })
        ];
    }

    /**
     * Obtener estadísticas estándar para mantenimientos
     */
    protected function getMantenimientosEstadisticas($mantenimientos): array
    {
        return [
            'total_mantenimientos' => $mantenimientos->count(),
            'por_tipo' => [
                'motor' => $mantenimientos->where('tipo_mantenimiento', 'motor')->count(),
                'transmision' => $mantenimientos->where('tipo_mantenimiento', 'transmision')->count(),
                'hidraulico' => $mantenimientos->where('tipo_mantenimiento', 'hidraulico')->count(),
                'preventivo' => $mantenimientos->where('tipo_mantenimiento', 'preventivo')->count(),
                'correctivo' => $mantenimientos->where('tipo_mantenimiento', 'correctivo')->count(),
                'otros' => $mantenimientos->whereNotIn('tipo_mantenimiento', 
                    ['motor', 'transmision', 'hidraulico', 'preventivo', 'correctivo'])->count(),
            ],
            'costo_total' => $mantenimientos->whereNotNull('costo')->sum('costo'),
            'costo_promedio' => $mantenimientos->whereNotNull('costo')->avg('costo'),
            'por_estado' => [
                'completado' => $mantenimientos->where('estado', 'completado')->count(),
                'en_proceso' => $mantenimientos->where('estado', 'en_proceso')->count(),
                'programado' => $mantenimientos->where('estado', 'programado')->count(),
                'cancelado' => $mantenimientos->where('estado', 'cancelado')->count(),
            ]
        ];
    }
}
