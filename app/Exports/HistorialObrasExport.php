<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistorialObrasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
            'ID',
            'Vehículo',
            'Marca',
            'Modelo',
            'Placas',
            'Obra',
            'Operador',
            'Fecha Inicio',
            'Fecha Fin',
            'Estado',
            'Días Duración'
        ];
    }

    public function map($asignacion): array
    {
        return [
            $asignacion->id,
            $asignacion->vehiculo->marca . ' ' . $asignacion->vehiculo->modelo,
            $asignacion->vehiculo->marca,
            $asignacion->vehiculo->modelo,
            $asignacion->vehiculo->placas,
            $asignacion->obra->nombre ?? 'N/A',
            $asignacion->operador->nombre_completo ?? 'N/A',
            $asignacion->fecha_inicio ? $asignacion->fecha_inicio->format('d/m/Y') : 'N/A',
            $asignacion->fecha_fin ? $asignacion->fecha_fin->format('d/m/Y') : 'En curso',
            ucfirst($asignacion->estado ?? 'activo'),
            $asignacion->fecha_inicio && $asignacion->fecha_fin 
                ? $asignacion->fecha_inicio->diffInDays($asignacion->fecha_fin) 
                : ($asignacion->fecha_inicio ? $asignacion->fecha_inicio->diffInDays(now()) : 'N/A')
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
        return 'Historial Obras Vehículos';
    }
}
