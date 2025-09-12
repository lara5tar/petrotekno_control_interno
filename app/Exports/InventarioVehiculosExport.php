<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventarioVehiculosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $vehiculos;

    public function __construct($vehiculos)
    {
        $this->vehiculos = $vehiculos;
    }

    public function collection()
    {
        return $this->vehiculos;
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
            'Tipo',
            'Estatus',
            'Kilometraje Actual'
        ];
    }

    public function map($vehiculo): array
    {
        return [
            $vehiculo->id,
            $vehiculo->marca,
            $vehiculo->modelo,
            $vehiculo->anio,
            $vehiculo->placas,
            $vehiculo->n_serie,
            $vehiculo->tipoActivo ? $vehiculo->tipoActivo->nombre : 'Sin tipo',
            $vehiculo->estatus ? $vehiculo->estatus->nombre() : 'N/A', // Usar el método nombre() del enum
            $vehiculo->kilometraje_actual
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Inventario Vehículos';
    }
}
