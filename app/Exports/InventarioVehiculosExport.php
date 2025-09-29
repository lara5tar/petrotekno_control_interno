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
            '#',
            'Marca/Modelo',
            'Tipo',
            'Año',
            'Placas',
            'No. Serie',
            'Ubicación',
            'Estado',
            'Km Actual'
        ];
    }

    public function map($vehiculo): array
    {
        // Construir ubicación igual que en el PDF
        $ubicacion = '';
        if ($vehiculo->estado && $vehiculo->municipio) {
            $ubicacion = $vehiculo->estado . ', ' . $vehiculo->municipio;
        } elseif ($vehiculo->estado) {
            $ubicacion = $vehiculo->estado;
        } elseif ($vehiculo->municipio) {
            $ubicacion = $vehiculo->municipio;
        } else {
            $ubicacion = 'Sin ubicación';
        }

        return [
            $vehiculo->id,
            $vehiculo->marca . ' ' . $vehiculo->modelo,
            $vehiculo->tipoActivo ? $vehiculo->tipoActivo->nombre : 'Sin tipo',
            $vehiculo->anio,
            $vehiculo->placas ?: 'N/A',
            $vehiculo->n_serie ?: 'N/A',
            $ubicacion,
            $vehiculo->estatus ? $vehiculo->estatus->nombre() : 'N/A',
            $vehiculo->kilometraje_actual ? number_format($vehiculo->kilometraje_actual) . ' km' : 'Sin registro'
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
        return 'Inventario Activos';
    }
}
