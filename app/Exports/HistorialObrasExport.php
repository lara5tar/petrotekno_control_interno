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
            'Activo',
            'Ubicación',
            'Obra',
            'Operador',
            'Fechas Asignación',
            'Estado',
            'Kilometraje',
            'Duración',
            'ID'
        ];
    }

    public function map($asignacion): array
    {
        // Construir ubicación
        $ubicacion = '';
        if ($asignacion->vehiculo) {
            if ($asignacion->vehiculo->estado && $asignacion->vehiculo->municipio) {
                $ubicacion = $asignacion->vehiculo->estado . ', ' . $asignacion->vehiculo->municipio;
            } elseif ($asignacion->vehiculo->estado) {
                $ubicacion = $asignacion->vehiculo->estado;
            } elseif ($asignacion->vehiculo->municipio) {
                $ubicacion = $asignacion->vehiculo->municipio;
            } else {
                $ubicacion = 'Sin ubicación';
            }
        } else {
            $ubicacion = 'Sin ubicación';
        }

        // Construir fechas de asignación
        $fechasAsignacion = '';
        if ($asignacion->fecha_inicio) {
            $fechasAsignacion = $asignacion->fecha_inicio->format('d/m/Y');
            if ($asignacion->fecha_fin) {
                $fechasAsignacion .= ' - ' . $asignacion->fecha_fin->format('d/m/Y');
            } else {
                $fechasAsignacion .= ' - En curso';
            }
        } else {
            $fechasAsignacion = 'N/A';
        }

        // Construir kilometraje
        $kilometraje = '';
        if ($asignacion->kilometraje_inicial && $asignacion->kilometraje_final) {
            $kilometraje = number_format($asignacion->kilometraje_inicial) . ' - ' . number_format($asignacion->kilometraje_final) . ' km';
        } elseif ($asignacion->kilometraje_inicial) {
            $kilometraje = number_format($asignacion->kilometraje_inicial) . ' km (inicial)';
        } else {
            $kilometraje = 'Sin registro';
        }

        return [
            $asignacion->vehiculo ? $asignacion->vehiculo->marca . ' ' . $asignacion->vehiculo->modelo : 'N/A',
            $ubicacion,
            $asignacion->obra->nombre_obra ?? 'N/A',
            $asignacion->operador->nombre_completo ?? 'N/A',
            $fechasAsignacion,
            ucfirst($asignacion->estado ?? 'activo'),
            $kilometraje,
            $asignacion->fecha_inicio && $asignacion->fecha_fin 
                ? floor($asignacion->fecha_inicio->diffInDays($asignacion->fecha_fin)) . ' días'
                : ($asignacion->fecha_inicio ? floor($asignacion->fecha_inicio->diffInDays(now())) . ' días' : 'N/A'),
            $asignacion->id
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
        return 'Historial Obras Activos';
    }
}
