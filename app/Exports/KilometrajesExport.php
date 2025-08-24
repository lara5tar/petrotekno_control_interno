<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KilometrajesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $kilometrajes;

    public function __construct($kilometrajes)
    {
        $this->kilometrajes = $kilometrajes;
    }

    public function collection()
    {
        return $this->kilometrajes;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha Captura',
            'Vehículo',
            'Marca',
            'Modelo',
            'Placas',
            'Kilometraje',
            'Obra',
            'Operador',
            'Observaciones'
        ];
    }

    public function map($kilometraje): array
    {
        return [
            $kilometraje->id,
            $kilometraje->fecha_captura ? $kilometraje->fecha_captura->format('d/m/Y H:i') : 'N/A',
            $kilometraje->vehiculo ? 
                $kilometraje->vehiculo->marca . ' ' . $kilometraje->vehiculo->modelo 
                : 'N/A',
            $kilometraje->vehiculo->marca ?? 'N/A',
            $kilometraje->vehiculo->modelo ?? 'N/A',
            $kilometraje->vehiculo->placas ?? 'N/A',
            $kilometraje->kilometraje ? number_format($kilometraje->kilometraje, 0) . ' km' : 'N/A',
            $kilometraje->obra->nombre ?? 'N/A',
            $kilometraje->operador ?? 'N/A',
            $kilometraje->observaciones ?? 'Sin observaciones'
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
        return 'Reporte Kilometrajes';
    }
}
