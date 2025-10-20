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
            'id',
            'Marca/Modelo',
            'Año',
            'Placas',
            'Serie',
            'Valor Comercial',
            'Propietario',
            'Estado',
            'Ubicación',
            'KM Actual',
            'Estado KM'
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

        // Determinar estado del kilometraje
        $estadoKm = 'Sin registro';
        if ($vehiculo->kilometraje_actual) {
            $estadoKm = 'Actualizado';
        }

        return [
            $vehiculo->id,
            $vehiculo->marca . ' ' . $vehiculo->modelo,
            $vehiculo->anio,
            $vehiculo->placas ?: 'N/A',
            $vehiculo->n_serie ?: 'N/A',
            $vehiculo->valor_comercial ? '$' . number_format($vehiculo->valor_comercial, 2) . ' MXN' : 'N/A',
            $vehiculo->propietario ?: 'N/A',
            $vehiculo->estatus ? $vehiculo->estatus->nombre() : 'N/A',
            $ubicacion,
            $vehiculo->kilometraje_actual ? number_format($vehiculo->kilometraje_actual) . ' km' : 'Sin registro',
            $estadoKm
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
