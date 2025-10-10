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

class OperadoresObrasFiltradosExport implements WithMultipleSheets
{
    protected $operadores;
    protected $filtros;
    protected $estadisticas;

    public function __construct($operadores, $filtros = [], $estadisticas = [])
    {
        $this->operadores = $operadores;
        $this->filtros = $filtros;
        $this->estadisticas = $estadisticas;
    }

    public function sheets(): array
    {
        return [
            new OperadoresObrasSheet($this->operadores, $this->filtros),
            new EstadisticasOperadoresObrasSheet($this->estadisticas, $this->filtros),
        ];
    }
}

class OperadoresObrasSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $operadores;
    protected $filtros;

    public function __construct($operadores, $filtros = [])
    {
        $this->operadores = $operadores;
        $this->filtros = $filtros;
    }

    public function collection()
    {
        return $this->operadores;
    }

    public function headings(): array
    {
        return [
            'ID Operador',
            'Nombre Completo',
            'CURP',
            'RFC',
            'NSS',
            'No. Licencia',
            'Estado',
            'Obra Asignada',
            'Ubicación Obra',
            'Estado Obra',
            'Avance Obra (%)',
            'Fecha Inicio Obra',
            'Fecha Fin Obra',
            'Responsable/Encargado',
            'Observaciones Obra',
            'Fecha Asignación'
        ];
    }

    public function map($operador): array
    {
        // Obtener información de la obra actual y del responsable
        $ultimaAsignacion = $operador->historialOperadorVehiculo
            ->where('obra_id', '!=', null)
            ->sortByDesc('fecha_asignacion')
            ->first();

        // Datos de la obra
        $nombreObra = 'N/A';
        $ubicacionObra = 'N/A';
        $estadoObra = 'N/A';
        $avanceObra = 'N/A';
        $fechaInicioObra = 'N/A';
        $fechaFinObra = 'N/A';
        $responsableObra = 'N/A';
        $observacionesObra = 'N/A';
        $fechaAsignacion = 'N/A';

        if ($ultimaAsignacion && $ultimaAsignacion->obra) {
            $obra = $ultimaAsignacion->obra;
            
            $nombreObra = $obra->nombre_obra ?? 'N/A';
            $ubicacionObra = $obra->ubicacion ?? 'N/A';
            $estadoObra = ucfirst($obra->estatus ?? 'N/A');
            $avanceObra = $obra->avance ? $obra->avance . '%' : 'N/A';
            $fechaInicioObra = $obra->fecha_inicio ? $obra->fecha_inicio->format('d/m/Y') : 'N/A';
            $fechaFinObra = $obra->fecha_fin ? $obra->fecha_fin->format('d/m/Y') : 'N/A';
            $observacionesObra = $obra->observaciones ?? 'N/A';
            $fechaAsignacion = $ultimaAsignacion->fecha_asignacion ? 
                $ultimaAsignacion->fecha_asignacion->format('d/m/Y H:i') : 'N/A';
            
            // Obtener responsable/encargado de la obra
            if ($obra->encargado) {
                $responsableObra = $obra->encargado->nombre_completo;
            } elseif ($obra->encargado_id) {
                try {
                    $encargado = \App\Models\Personal::find($obra->encargado_id);
                    $responsableObra = $encargado ? $encargado->nombre_completo : 'N/A';
                } catch (\Exception $e) {
                    $responsableObra = 'N/A';
                }
            }
        }

        return [
            $operador->id,
            $operador->nombre_completo ?? 'N/A',
            $operador->curp_numero ?? 'N/A',
            $operador->rfc ?? 'N/A',
            $operador->nss ?? 'N/A',
            $operador->no_licencia ?? 'N/A',
            ucfirst($operador->estatus ?? 'N/A'),
            $nombreObra,
            $ubicacionObra,
            $estadoObra,
            $avanceObra,
            $fechaInicioObra,
            $fechaFinObra,
            $responsableObra,
            $observacionesObra,
            $fechaAsignacion
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
            'A:P' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            // Columnas de números alineadas a la derecha
            'K:K' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
            ],
            // Columnas de fechas centradas
            'L:M' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'P:P' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Operadores con Obras';
    }
}

class EstadisticasOperadoresObrasSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
            'concepto' => 'Total de Operadores',
            'valor' => $this->estadisticas['total_operadores'] ?? 0
        ]);

        $datos->push((object)[
            'categoria' => 'RESUMEN GENERAL',
            'concepto' => 'Total de Asignaciones',
            'valor' => $this->estadisticas['total_asignaciones'] ?? 0
        ]);

        $datos->push((object)[
            'categoria' => 'RESUMEN GENERAL',
            'concepto' => 'Operadores Activos',
            'valor' => $this->estadisticas['operadores_activos'] ?? 0
        ]);

        $datos->push((object)[
            'categoria' => 'RESUMEN GENERAL',
            'concepto' => 'Promedio Asignaciones por Operador',
            'valor' => $this->estadisticas['promedio_asignaciones'] ?? 0
        ]);

        // Línea vacía
        $datos->push((object)['categoria' => '', 'concepto' => '', 'valor' => '']);

        // Por estado
        if (isset($this->estadisticas['por_estado'])) {
            $datos->push((object)[
                'categoria' => 'POR ESTADO',
                'concepto' => 'Activos',
                'valor' => $this->estadisticas['por_estado']['activo'] ?? 0
            ]);
            $datos->push((object)[
                'categoria' => 'POR ESTADO',
                'concepto' => 'Inactivos',
                'valor' => $this->estadisticas['por_estado']['inactivo'] ?? 0
            ]);
            $datos->push((object)[
                'categoria' => 'POR ESTADO',
                'concepto' => 'Suspendidos',
                'valor' => $this->estadisticas['por_estado']['suspendido'] ?? 0
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
                    'estado' => 'Estado',
                    'obra_id' => 'Obra ID',
                    'solo_activos' => 'Solo Activos',
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