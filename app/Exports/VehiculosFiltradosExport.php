<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class VehiculosFiltradosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithCustomStartCell, WithEvents
{
    protected $vehiculos;
    protected $filtros;
    protected $estadisticas;

    public function __construct($vehiculos, $filtros = [], $estadisticas = [])
    {
        $this->vehiculos = $vehiculos;
        $this->filtros = $filtros;
        $this->estadisticas = $estadisticas;
    }

    public function collection()
    {
        return $this->vehiculos;
    }

    public function startCell(): string
    {
        // Comenzar en la fila 8 para dejar espacio para el encabezado y filtros
        return 'A8';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Marca',
            'Modelo',
            'Año',
            'Placas',
            'No. Serie',
            'Tipo de Activo',
            'Estado',
            'Kilometraje Actual',
            'Ubicación',
            'Fecha Registro'
        ];
    }

    public function map($vehiculo): array
    {
        return [
            $vehiculo->id,
            $vehiculo->marca,
            $vehiculo->modelo,
            $vehiculo->anio,
            $vehiculo->placas ?: 'N/A',
            $vehiculo->n_serie ?: 'N/A',
            $vehiculo->tipoActivo ? $vehiculo->tipoActivo->nombre : 'Sin tipo',
            $vehiculo->estatus ? $vehiculo->estatus->nombre() : 'N/A',
            $vehiculo->kilometraje_actual ? number_format($vehiculo->kilometraje_actual) . ' km' : 'Sin registro',
            ($vehiculo->estado && $vehiculo->municipio) ? $vehiculo->estado . ', ' . $vehiculo->municipio : ($vehiculo->estado ?: ($vehiculo->municipio ?: 'Sin ubicación')),
            $vehiculo->created_at ? $vehiculo->created_at->format('d/m/Y') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Título principal
            1 => [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            // Subtítulo
            2 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            // Encabezados de la tabla
            8 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function title(): string
    {
        return 'Vehículos Filtrados';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Título principal
                $sheet->setCellValue('A1', 'REPORTE DE VEHÍCULOS FILTRADOS');
                $sheet->mergeCells('A1:K1');
                
                // Subtítulo con fecha
                $sheet->setCellValue('A2', 'Generado el ' . now()->format('d/m/Y H:i:s'));
                $sheet->mergeCells('A2:K2');
                
                // Información de filtros aplicados
                $row = 4;
                if (!empty($this->filtros)) {
                    $sheet->setCellValue('A3', 'FILTROS APLICADOS:');
                    $sheet->getStyle('A3')->getFont()->setBold(true);
                    
                    if (isset($this->filtros['buscar']) && $this->filtros['buscar']) {
                        $sheet->setCellValue('A' . $row, 'Búsqueda: ' . $this->filtros['buscar']);
                        $row++;
                    }
                    if (isset($this->filtros['estado']) && $this->filtros['estado']) {
                        $sheet->setCellValue('A' . $row, 'Estado: ' . ucfirst(str_replace('_', ' ', $this->filtros['estado'])));
                        $row++;
                    }
                    if (isset($this->filtros['anio']) && $this->filtros['anio']) {
                        $sheet->setCellValue('A' . $row, 'Año: ' . $this->filtros['anio']);
                        $row++;
                    }
                }
                
                // Estadísticas
                if (!empty($this->estadisticas)) {
                    $row++;
                    $sheet->setCellValue('A' . $row, 'ESTADÍSTICAS:');
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;
                    
                    $sheet->setCellValue('A' . $row, 'Total de vehículos: ' . ($this->estadisticas['total'] ?? count($this->vehiculos)));
                    $row++;
                    
                    if (isset($this->estadisticas['por_estado'])) {
                        foreach ($this->estadisticas['por_estado'] as $estado => $cantidad) {
                            if ($cantidad > 0) {
                                $sheet->setCellValue('A' . $row, ucfirst(str_replace('_', ' ', $estado)) . ': ' . $cantidad);
                                $row++;
                            }
                        }
                    }
                }
                
                // Ajustar ancho de columnas
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(8);
                $sheet->getColumnDimension('E')->setWidth(12);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(12);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(15);
                $sheet->getColumnDimension('K')->setWidth(12);
                
                // Aplicar bordes a la tabla
                $lastRow = 8 + count($this->vehiculos);
                $sheet->getStyle('A8:K' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                // Centrar contenido de ciertas columnas
                $sheet->getStyle('A8:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D8:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E8:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H8:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I8:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K8:K' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}