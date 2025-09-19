@extends('pdf.layouts.base')

@section('title', 'Reporte de Kilometrajes')

@section('content')
<div class="header">
    <h1>Reporte de Kilometrajes de Activos</h1>
    <div class="report-info">
        <p><strong>Fecha de generación:</strong> {{ date('d/m/Y H:i') }}</p>
        @if($vehiculoId)
            <p><strong>Activo filtrado:</strong> 
                @php
                    $vehiculo = \App\Models\Vehiculo::find($vehiculoId);
                @endphp
                {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
            </p>
        @endif
        @if($fechaInicio)
            <p><strong>Fecha inicio:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</p>
        @endif
        @if($fechaFin)
            <p><strong>Fecha fin:</strong> {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
        @endif
    </div>
</div>

<!-- Estadísticas generales -->
<div class="statistics-section">
    <h2>Estadísticas Generales</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-label">Total de Registros:</span>
            <span class="stat-value">{{ number_format($estadisticas['total_registros']) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Activos con Kilometraje:</span>
            <span class="stat-value">{{ number_format($estadisticas['vehiculos_con_kilometraje']) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Kilometraje Promedio:</span>
            <span class="stat-value">{{ number_format($estadisticas['kilometraje_promedio'], 0) }} km</span>
        </div>

    </div>
</div>

@if($kilometrajes->count() > 0)
    <!-- Tabla de kilometrajes -->
    <div class="table-section">
        <h2>Detalle de Kilometrajes</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Activo</th>
                    <th>Placas</th>
                    <th>Ubicación</th>
                    <th>Fecha</th>
                    <th>Kilometraje</th>
                    <th>Obra</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kilometrajes as $kilometraje)
                    <tr>
                        <td>{{ $kilometraje->vehiculo->marca }} {{ $kilometraje->vehiculo->modelo }}</td>
                        <td>{{ $kilometraje->vehiculo->placas }}</td>
                        <td>
                            @if($kilometraje->vehiculo->estado || $kilometraje->vehiculo->municipio)
                                {{ $kilometraje->vehiculo->estado ?: 'Sin estado' }}
                                @if($kilometraje->vehiculo->municipio)
                                    <br>{{ $kilometraje->vehiculo->municipio }}
                                @endif
                            @else
                                Sin ubicación
                            @endif
                        </td>
                        <td>{{ $kilometraje->fecha_captura->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($kilometraje->kilometraje) }} km</td>
                        <td>
                            @if($kilometraje->obra)
                                {{ $kilometraje->obra->nombre }}
                            @else
                                Sin obra
                            @endif
                        </td>
                        <td>{{ $kilometraje->observaciones ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Resumen por vehículo -->
    <div class="summary-section">
        <h2>Resumen por Activo</h2>
        @php
            $resumenVehiculos = $kilometrajes->groupBy('vehiculo_id')->map(function($registros) {
                $vehiculo = $registros->first()->vehiculo;
                $primerKm = $registros->min('kilometraje');
                $totalRegistros = $registros->count();
                
                return [
                    'vehiculo' => $vehiculo,
                    'primer_km' => $primerKm,
                    'total_registros' => $totalRegistros
                ];
            });
        @endphp

        <table class="data-table">
            <thead>
                <tr>
                    <th>Activo</th>
                    <th>Placas</th>
                    <th>Ubicación</th>
                    <th>Primer Km</th>
                    <th>Registros</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumenVehiculos as $resumen)
                    <tr>
                        <td>{{ $resumen['vehiculo']->marca }} {{ $resumen['vehiculo']->modelo }}</td>
                        <td>{{ $resumen['vehiculo']->placas }}</td>
                        <td>
                            @if($resumen['vehiculo']->estado || $resumen['vehiculo']->municipio)
                                {{ $resumen['vehiculo']->estado ?: 'Sin estado' }}
                                @if($resumen['vehiculo']->municipio)
                                    <br>{{ $resumen['vehiculo']->municipio }}
                                @endif
                            @else
                                Sin ubicación
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($resumen['primer_km']) }} km</td>
                        <td class="text-center">{{ $resumen['total_registros'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="no-data">
        <p>No se encontraron registros de kilometrajes con los filtros aplicados.</p>
    </div>
@endif
@endsection

@section('styles')
<style>
    .statistics-section {
        margin: 20px 0;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 10px;
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 12px;
        background-color: white;
        border-radius: 4px;
        border-left: 4px solid #007bff;
    }
    
    .stat-label {
        font-weight: 500;
        color: #666;
    }
    
    .stat-value {
        font-weight: bold;
        color: #333;
    }
    
    .table-section {
        margin: 25px 0;
    }
    
    .summary-section {
        margin: 30px 0;
        page-break-inside: avoid;
    }
    
    .summary-section h2 {
        color: #2c5aa0;
        border-bottom: 2px solid #2c5aa0;
        padding-bottom: 5px;
    }
    
    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
    
    .no-data {
        text-align: center;
        padding: 40px;
        color: #666;
        font-style: italic;
    }
</style>
@endsection
