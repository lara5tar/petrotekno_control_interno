<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MantenimientosPendientesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $mantenimientos;

    public function __construct($mantenimientos)
    {
        $this->mantenimientos = $mantenimientos;
    }

    public function collection()
    {
        return $this->mantenimientos;
    }

    public function headings(): array
    {
        return [
            '#',
            'Fecha Inicio',
            'Tipo Servicio',
            'Activo',
            'Descripción',
            'Costo Estimado',
            'Proveedor',
            'Días Pendiente'
        ];
    }

    public function map($mantenimiento): array
    {
        return [
            $mantenimiento->id,
            $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('d/m/Y') : 'N/A',
            $mantenimiento->tipo_servicio ?? 'N/A',
            $mantenimiento->vehiculo ? 
                $mantenimiento->vehiculo->marca . ' ' . $mantenimiento->vehiculo->modelo . ' - ' . $mantenimiento->vehiculo->placas 
                : 'N/A',
            $mantenimiento->descripcion ?? 'N/A',
            $mantenimiento->costo ? '$' . number_format($mantenimiento->costo, 2) : 'N/A',
            $mantenimiento->proveedor ?? 'N/A',
            $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->diffInDays(now()) . ' días' : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Mantenimientos Pendientes';
    }
}
