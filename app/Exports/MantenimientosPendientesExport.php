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
            'ID',
            'Fecha Inicio',
            'Tipo Servicio',
            'Activo',
            'Marca',
            'Modelo', 
            'Placas',
            'Sistema Activo',
            'Descripción',
            'Costo Estimado',
            'Proveedor',
            'Días Pendiente',
            'Observaciones'
        ];
    }

    public function map($mantenimiento): array
    {
        $diasPendiente = $mantenimiento->fecha_inicio ? 
            $mantenimiento->fecha_inicio->diffInDays(now()) : 'N/A';

        return [
            $mantenimiento->id,
            $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('d/m/Y') : 'N/A',
            $mantenimiento->tipo_servicio ?? 'N/A',
            $mantenimiento->vehiculo ? 
                $mantenimiento->vehiculo->marca . ' ' . $mantenimiento->vehiculo->modelo 
                : 'N/A',
            $mantenimiento->vehiculo->marca ?? 'N/A',
            $mantenimiento->vehiculo->modelo ?? 'N/A',
            $mantenimiento->vehiculo->placas ?? 'N/A',
            $mantenimiento->sistema_vehiculo ?? 'N/A',
            $mantenimiento->descripcion ?? 'Sin descripción',
            $mantenimiento->costo ? '$' . number_format($mantenimiento->costo, 2) : 'N/A',
            $mantenimiento->proveedor ?? 'Sin proveedor',
            $diasPendiente,
            $mantenimiento->observaciones ?? 'Sin observaciones'
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
