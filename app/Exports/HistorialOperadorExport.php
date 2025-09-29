<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistorialOperadorExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $asignaciones;

    public function __construct($asignaciones)
    {
        $this->asignaciones = $asignaciones;
    }

    public function collection()
    {
        return $this->asignaciones;
    }

    public function headings(): array
    {
        return [
            '#',
            'Fecha',
            'Obra',
            'Ubicación',
            'Descripción',
            'Observaciones'
        ];
    }

    public function map($obra): array
    {
        // Construir ubicación igual que en el PDF
        $ubicacion = '';
        if ($obra->vehiculo) {
            if ($obra->vehiculo->estado && $obra->vehiculo->municipio) {
                $ubicacion = $obra->vehiculo->estado . ', ' . $obra->vehiculo->municipio;
            } elseif ($obra->vehiculo->estado) {
                $ubicacion = $obra->vehiculo->estado;
            } elseif ($obra->vehiculo->municipio) {
                $ubicacion = $obra->vehiculo->municipio;
            } else {
                $ubicacion = 'Sin ubicación';
            }
        } else {
            $ubicacion = 'Sin ubicación';
        }

        return [
            $obra->id,
            $obra->fecha ? $obra->fecha->format('d/m/Y') : 'N/A',
            $obra->nombre ?? 'N/A',
            $ubicacion,
            $obra->descripcion ?? 'N/A',
            $obra->observaciones ?? 'Sin observaciones'
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
        return 'Historial Operadores';
    }
}
