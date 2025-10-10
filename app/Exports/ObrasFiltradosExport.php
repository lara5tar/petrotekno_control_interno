<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ObrasFiltradosExport implements WithMultipleSheets
{
    protected $obras;
    protected $filtros;
    protected $estadisticas;

    public function __construct($obras, $filtros = [], $estadisticas = [])
    {
        $this->obras = $obras;
        $this->filtros = $filtros;
        $this->estadisticas = $estadisticas;
    }

    public function sheets(): array
    {
        return [
            new ObrasSheet($this->obras, $this->filtros),
            new EstadisticasObrasSheet($this->estadisticas, $this->filtros),
        ];
    }
}

class ObrasSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $obras;
    protected $filtros;

    public function __construct($obras, $filtros = [])
    {
        $this->obras = $obras;
        $this->filtros = $filtros;
    }

    public function collection()
    {
        return $this->obras;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre de la Obra',
            'Fecha Inicio',
            'Fecha Fin',
            'Estatus',
            'Encargado',
            'Ubicación',
            'Descripción',
            'Avance (%)',
            'Observaciones',
            'Duración (días)',
            'Fecha Registro'
        ];
    }

    public function map($obra): array
    {
        // Calcular duración
        $duracion = 'N/A';
        if ($obra->fecha_inicio) {
            if ($obra->fecha_fin) {
                $duracion = $obra->fecha_inicio->diffInDays($obra->fecha_fin);
                $duracion = $duracion == 0 ? 1 : $duracion; // Mínimo 1 día
            } else {
                $duracion = $obra->fecha_inicio->diffInDays(now()) . ' (en progreso)';
            }
        }

        // Información del encargado
        $encargadoInfo = 'N/A';
        if ($obra->encargado) {
            $encargadoInfo = $obra->encargado->nombre_completo ?? 
                trim(($obra->encargado->nombre ?? '') . ' ' . ($obra->encargado->apellidos ?? ''));
        }

        // Ubicación
        $ubicacion = $obra->ubicacion ?? 'N/A';

        // Estado de la obra
        $estado = match($obra->estatus) {
            'activa' => 'Activa',
            'en_progreso' => 'En Progreso',
            'completada' => 'Completada',
            'suspendida' => 'Suspendida',
            default => ucfirst($obra->estatus ?? 'N/A')
        };

        // Avance
        $avance = $obra->avance ? $obra->avance . '%' : 'N/A';

        return [
            $obra->id,
            $obra->nombre_obra ?? 'N/A',
            $obra->fecha_inicio ? $obra->fecha_inicio->format('d/m/Y') : 'N/A',
            $obra->fecha_fin ? $obra->fecha_fin->format('d/m/Y') : 'En progreso',
            $estado,
            $encargadoInfo,
            $ubicacion,
            $obra->descripcion ?? 'N/A',
            $avance,
            $obra->observaciones ?? 'N/A',
            $duracion,
            $obra->created_at ? $obra->created_at->format('d/m/Y H:i') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '4472C4'],
                ],
            ],
            // Estilo para todas las celdas
            'A:L' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            // Columnas de fechas centradas
            'C:D' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Columna de avance centrada
            'I:I' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Columna de fecha registro centrada
            'L:L' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Obras Filtradas';
    }
}

class EstadisticasObrasSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $estadisticas;
    protected $filtros;

    public function __construct($estadisticas, $filtros = [])
    {
        $this->estadisticas = $estadisticas;
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $datos = collect();

        // Información general
        $datos->push((object)[
            'categoria' => 'RESUMEN GENERAL',
            'concepto' => 'Total de Obras',
            'valor' => $this->estadisticas['total'] ?? 0
        ]);

        if (isset($this->estadisticas['costo_total'])) {
            $datos->push((object)[
                'categoria' => 'RESUMEN GENERAL',
                'concepto' => 'Costo Total',
                'valor' => '$' . number_format($this->estadisticas['costo_total'], 2)
            ]);
        }

        if (isset($this->estadisticas['costo_promedio'])) {
            $datos->push((object)[
                'categoria' => 'RESUMEN GENERAL',
                'concepto' => 'Costo Promedio',
                'valor' => '$' . number_format($this->estadisticas['costo_promedio'], 2)
            ]);
        }

        // Línea vacía
        $datos->push((object)['categoria' => '', 'concepto' => '', 'valor' => '']);

        // Por estatus
        if (isset($this->estadisticas['por_estatus'])) {
            $datos->push((object)[
                'categoria' => 'POR ESTATUS',
                'concepto' => 'Activas',
                'valor' => $this->estadisticas['por_estatus']['activa'] ?? 0
            ]);
            $datos->push((object)[
                'categoria' => 'POR ESTATUS',
                'concepto' => 'En Progreso',
                'valor' => $this->estadisticas['por_estatus']['en_progreso'] ?? 0
            ]);
            $datos->push((object)[
                'categoria' => 'POR ESTATUS',
                'concepto' => 'Completadas',
                'valor' => $this->estadisticas['por_estatus']['completada'] ?? 0
            ]);
            $datos->push((object)[
                'categoria' => 'POR ESTATUS',
                'concepto' => 'Suspendidas',
                'valor' => $this->estadisticas['por_estatus']['suspendida'] ?? 0
            ]);
        }

        // Línea vacía
        $datos->push((object)['categoria' => '', 'concepto' => '', 'valor' => '']);

        // Filtros aplicados
        $datos->push((object)[
            'categoria' => 'FILTROS APLICADOS',
            'concepto' => 'Fecha de Generación',
            'valor' => now()->format('d/m/Y H:i:s')
        ]);

        foreach ($this->filtros as $filtro => $valor) {
            if ($valor && $valor !== '') {
                $nombreFiltro = match($filtro) {
                    'buscar' => 'Búsqueda',
                    'estatus' => 'Estatus',
                    'fecha_inicio' => 'Fecha de Inicio',
                    'solo_activas' => 'Solo Activas',
                    default => ucfirst(str_replace('_', ' ', $filtro))
                };

                $datos->push((object)[
                    'categoria' => 'FILTROS APLICADOS',
                    'concepto' => $nombreFiltro,
                    'valor' => is_bool($valor) ? ($valor ? 'Sí' : 'No') : $valor
                ]);
            }
        }

        return $datos;
    }

    public function headings(): array
    {
        return [
            'Categoría',
            'Concepto',
            'Valor'
        ];
    }

    public function map($item): array
    {
        return [
            $item->categoria,
            $item->concepto,
            $item->valor
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '70AD47'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Estadísticas y Filtros';
    }
}