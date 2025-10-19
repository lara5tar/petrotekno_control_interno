<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MantenimientosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
            'Fecha',
            'Activo',
            'Ubicación',
            'Tipo',
            'Descripción',
            'Costo',
            'Km',
            'Responsable'
        ];
    }

    public function map($mantenimiento): array
    {
        // Construir ubicación
        $ubicacion = '';
        if ($mantenimiento->vehiculo) {
            if ($mantenimiento->vehiculo->estado && $mantenimiento->vehiculo->municipio) {
                $ubicacion = $mantenimiento->vehiculo->estado . ', ' . $mantenimiento->vehiculo->municipio;
            } elseif ($mantenimiento->vehiculo->estado) {
                $ubicacion = $mantenimiento->vehiculo->estado;
            } elseif ($mantenimiento->vehiculo->municipio) {
                $ubicacion = $mantenimiento->vehiculo->municipio;
            } else {
                $ubicacion = 'Sin ubicación';
            }
        } else {
            $ubicacion = 'Sin ubicación';
        }

        return [
            $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('d/m/Y') : 'N/A',
            $mantenimiento->vehiculo ? $mantenimiento->vehiculo->marca . ' ' . $mantenimiento->vehiculo->modelo : 'N/A',
            $ubicacion,
            $mantenimiento->tipo_servicio ?? 'N/A',
            $mantenimiento->descripcion ?? 'N/A',
            $mantenimiento->costo ? '$' . number_format($mantenimiento->costo, 2) : 'N/A',
            $mantenimiento->kilometraje ? number_format($mantenimiento->kilometraje, 0) . ' km' : 'N/A',
            $mantenimiento->responsable ?? 'N/A'
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
        return 'Historial Mantenimientos';
    }
}
