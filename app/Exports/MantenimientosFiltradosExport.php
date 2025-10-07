<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MantenimientosFiltradosExport implements WithMultipleSheets
{
    protected $mantenimientos;
    protected $filtros;
    protected $estadisticas;

    public function __construct($mantenimientos, $filtros = [], $estadisticas = [])
    {
        $this->mantenimientos = $mantenimientos;
        $this->filtros = $filtros;
        $this->estadisticas = $estadisticas;
    }

    public function sheets(): array
    {
        return [
            new MantenimientosSheet($this->mantenimientos, $this->filtros),
            new EstadisticasSheet($this->estadisticas, $this->filtros),
        ];
    }
}

class MantenimientosSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $mantenimientos;
    protected $filtros;

    public function __construct($mantenimientos, $filtros = [])
    {
        $this->mantenimientos = $mantenimientos;
        $this->filtros = $filtros;
    }

    public function collection()
    {
        return $this->mantenimientos;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha Inicio',
            'Fecha Fin',
            'Vehículo',
            'Placas',
            'Tipo Servicio',
            'Sistema',
            'Descripción',
            'Proveedor',
            'Kilometraje',
            'Costo',
            'Estado',
            'Duración (días)',
            'Ubicación Vehículo',
            'Fecha Registro'
        ];
    }

    public function map($mantenimiento): array
    {
        // Calcular duración
        $duracion = 'N/A';
        if ($mantenimiento->fecha_inicio) {
            if ($mantenimiento->fecha_fin) {
                $duracion = $mantenimiento->fecha_inicio->diffInDays($mantenimiento->fecha_fin);
                $duracion = $duracion == 0 ? 1 : $duracion; // Mínimo 1 día
            } else {
                $duracion = $mantenimiento->fecha_inicio->diffInDays(now()) . ' (en proceso)';
            }
        }

        // Información del vehículo
        $vehiculoInfo = 'N/A';
        $placas = 'N/A';
        $ubicacion = 'N/A';
        
        if ($mantenimiento->vehiculo) {
            $vehiculoInfo = trim($mantenimiento->vehiculo->marca . ' ' . $mantenimiento->vehiculo->modelo);
            $placas = $mantenimiento->vehiculo->placas ?: 'N/A';
            
            // Construir ubicación
            if ($mantenimiento->vehiculo->estado && $mantenimiento->vehiculo->municipio) {
                $ubicacion = $mantenimiento->vehiculo->estado . ', ' . $mantenimiento->vehiculo->municipio;
            } elseif ($mantenimiento->vehiculo->estado) {
                $ubicacion = $mantenimiento->vehiculo->estado;
            } elseif ($mantenimiento->vehiculo->municipio) {
                $ubicacion = $mantenimiento->vehiculo->municipio;
            } else {
                $ubicacion = 'Sin ubicación';
            }
        }

        // Estado del mantenimiento
        $estado = $mantenimiento->fecha_fin ? 'Completado' : 'En proceso';

        return [
            $mantenimiento->id,
            $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('d/m/Y') : 'N/A',
            $mantenimiento->fecha_fin ? $mantenimiento->fecha_fin->format('d/m/Y') : 'En proceso',
            $vehiculoInfo,
            $placas,
            strtoupper($mantenimiento->tipo_servicio ?? 'N/A'),
            ucfirst($mantenimiento->sistema_vehiculo ?? 'N/A'),
            $mantenimiento->descripcion ?? 'N/A',
            $mantenimiento->proveedor ?? 'N/A',
            $mantenimiento->kilometraje_servicio ? number_format($mantenimiento->kilometraje_servicio) . ' km' : 'N/A',
            $mantenimiento->costo ? '$' . number_format($mantenimiento->costo, 2) : 'N/A',
            $estado,
            $duracion,
            $ubicacion,
            $mantenimiento->created_at ? $mantenimiento->created_at->format('d/m/Y H:i') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '4472C4'],
                ],
            ],
            // Estilo para todas las celdas
            'A:O' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            // Columnas de números alineadas a la derecha
            'K:K' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
            ],
            // Columnas de fechas centradas
            'B:C' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Mantenimientos Filtrados';
    }
}

class EstadisticasSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $estadisticas;
    protected $filtros;

    public function __construct($estadisticas, $filtros = [])
    {
        $this->estadisticas = $estadisticas;
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $datos = collect();

        // Información general
        $datos->push((object)[
            'categoria' => 'RESUMEN GENERAL',
            'concepto' => 'Total de Mantenimientos',
            'valor' => $this->estadisticas['total'] ?? 0
        ]);

        if (isset($this->estadisticas['costo_total'])) {
            $datos->push((object)[
                'categoria' => 'RESUMEN GENERAL',
                'concepto' => 'Costo Total',
                'valor' => '$' . number_format($this->estadisticas['costo_total'], 2)
            ]);
        }

        if (isset($this->estadisticas['costo_promedio'])) {
            $datos->push((object)[
                'categoria' => 'RESUMEN GENERAL',
                'concepto' => 'Costo Promedio',
                'valor' => '$' . number_format($this->estadisticas['costo_promedio'], 2)
            ]);
        }

        // Línea vacía
        $datos->push((object)['categoria' => '', 'concepto' => '', 'valor' => '']);

        // Por tipo de servicio
        if (isset($this->estadisticas['por_tipo_servicio'])) {
            $datos->push((object)[
                'categoria' => 'POR TIPO DE SERVICIO',
                'concepto' => 'Preventivos',
                'valor' => $this->estadisticas['por_tipo_servicio']['PREVENTIVO'] ?? 0
            ]);
            $datos->push((object)[
                'categoria' => 'POR TIPO DE SERVICIO',
                'concepto' => 'Correctivos',
                'valor' => $this->estadisticas['por_tipo_servicio']['CORRECTIVO'] ?? 0
            ]);
        }

        // Línea vacía
        $datos->push((object)['categoria' => '', 'concepto' => '', 'valor' => '']);

        // Por sistema
        if (isset($this->estadisticas['por_sistema'])) {
            $datos->push((object)[
                'categoria' => 'POR SISTEMA DE VEHÍCULO',
                'concepto' => 'Motor',
                'valor' => $this->estadisticas['por_sistema']['motor'] ?? 0
            ]);
            $datos->push((object)[
                'categoria' => 'POR SISTEMA DE VEHÍCULO',
                'concepto' => 'Transmisión',
                'valor' => $this->estadisticas['por_sistema']['transmision'] ?? 0
            ]);
            $datos->push((object)[
                'categoria' => 'POR SISTEMA DE VEHÍCULO',
                'concepto' => 'Hidráulico',
                'valor' => $this->estadisticas['por_sistema']['hidraulico'] ?? 0
            ]);
            $datos->push((object)[
                'categoria' => 'POR SISTEMA DE VEHÍCULO',
                'concepto' => 'General',
                'valor' => $this->estadisticas['por_sistema']['general'] ?? 0
            ]);
        }

        // Línea vacía
        $datos->push((object)['categoria' => '', 'concepto' => '', 'valor' => '']);

        // Por estado
        if (isset($this->estadisticas['por_estado'])) {
            $datos->push((object)[
                'categoria' => 'POR ESTADO',
                'concepto' => 'Completados',
                'valor' => $this->estadisticas['por_estado']['completados'] ?? 0
            ]);
            $datos->push((object)[
                'categoria' => 'POR ESTADO',
                'concepto' => 'En Proceso',
                'valor' => $this->estadisticas['por_estado']['en_proceso'] ?? 0
            ]);
        }

        // Línea vacía
        $datos->push((object)['categoria' => '', 'concepto' => '', 'valor' => '']);

        // Filtros aplicados
        $datos->push((object)[
            'categoria' => 'FILTROS APLICADOS',
            'concepto' => 'Fecha de Generación',
            'valor' => now()->format('d/m/Y H:i:s')
        ]);

        foreach ($this->filtros as $filtro => $valor) {
            if ($valor && $valor !== '') {
                $nombreFiltro = match($filtro) {
                    'buscar' => 'Búsqueda',
                    'vehiculo_id' => 'Vehículo ID',
                    'tipo_servicio' => 'Tipo de Servicio',
                    'sistema_vehiculo' => 'Sistema',
                    'proveedor' => 'Proveedor',
                    'fecha_inicio_desde' => 'Fecha Desde',
                    'fecha_inicio_hasta' => 'Fecha Hasta',
                    'kilometraje_min' => 'Kilometraje Mínimo',
                    'kilometraje_max' => 'Kilometraje Máximo',
                    'costo_min' => 'Costo Mínimo',
                    'costo_max' => 'Costo Máximo',
                    default => ucfirst(str_replace('_', ' ', $filtro))
                };

                $datos->push((object)[
                    'categoria' => 'FILTROS APLICADOS',
                    'concepto' => $nombreFiltro,
                    'valor' => $valor
                ]);
            }
        }

        return $datos;
    }

    public function headings(): array
    {
        return [
            'Categoría',
            'Concepto',
            'Valor'
        ];
    }

    public function map($item): array
    {
        return [
            $item->categoria,
            $item->concepto,
            $item->valor
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '70AD47'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Estadísticas y Filtros';
    }
}