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
            'id',
            'Fecha',
            'Tipo',
            'Activo',
            'Operador',
            'Obra',
            'Km Inicial',
            'Km Final',
            'Fecha Creación'
        ];
    }

    public function map($asignacion): array
    {
        return [
            $asignacion->id,
            $asignacion->created_at ? $asignacion->created_at->format('d/m/Y') : 'N/A',
            'Asignación',
            $asignacion->vehiculo ? $asignacion->vehiculo->marca . ' ' . $asignacion->vehiculo->modelo : 'N/A',
            $asignacion->operador ? $asignacion->operador->nombre_completo : 'N/A',
            $asignacion->obra ? $asignacion->obra->nombre_obra : 'N/A',
            $asignacion->kilometraje_inicial ? number_format($asignacion->kilometraje_inicial) . ' km' : 'N/A',
            $asignacion->kilometraje_final ? number_format($asignacion->kilometraje_final) . ' km' : 'N/A',
            $asignacion->created_at ? $asignacion->created_at->format('d/m/Y H:i') : 'N/A'
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
