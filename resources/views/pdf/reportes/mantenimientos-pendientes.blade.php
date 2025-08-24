@extends('pdf.layouts.base')

@section('title', 'Reporte de Mantenimientos Pendientes')

@section('content')
<div class="header">
    <h1>Reporte de Mantenimientos Pendientes</h1>
    <div class="report-info">
        <p><strong>Fecha de generación:</strong> {{ date('d/m/Y H:i') }}</p>
        @if($vehiculoId)
            <p><strong>Vehículo filtrado:</strong> 
                @php
                    $vehiculo = \App\Models\Vehiculo::find($vehiculoId);
                @endphp
                {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
            </p>
        @endif
        @if($tipoServicio)
            <p><strong>Tipo de servicio:</strong> {{ $tipoServicio }}</p>
        @endif
        @if($sistemaVehiculo)
            <p><strong>Sistema:</strong> {{ ucfirst($sistemaVehiculo) }}</p>
        @endif
        @if($proveedor)
            <p><strong>Proveedor:</strong> {{ $proveedor }}</p>
        @endif
    </div>
</div>

<!-- Estadísticas generales -->
<div class="statistics-section">
    <h2>Estadísticas Generales</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-label">Total Pendientes:</span>
            <span class="stat-value">{{ number_format($estadisticas['total_pendientes']) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Mantenimientos Correctivos:</span>
            <span class="stat-value">{{ number_format($estadisticas['mantenimientos_correctivos']) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Mantenimientos Preventivos:</span>
            <span class="stat-value">{{ number_format($estadisticas['mantenimientos_preventivos']) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Vehículos Afectados:</span>
            <span class="stat-value">{{ number_format($estadisticas['vehiculos_en_mantenimiento']) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Costo Total Estimado:</span>
            <span class="stat-value">${{ number_format($estadisticas['costo_estimado'], 2) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Días Promedio Pendiente:</span>
            <span class="stat-value">{{ number_format($estadisticas['dias_promedio_pendiente'], 1) }} días</span>
        </div>
    </div>
</div>

@if($mantenimientos->count() > 0)
    <!-- Tabla de mantenimientos pendientes -->
    <div class="table-section">
        <h2>Detalle de Mantenimientos Pendientes</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Vehículo</th>
                    <th>Placas</th>
                    <th>Tipo</th>
                    <th>Sistema</th>
                    <th>Descripción</th>
                    <th>Proveedor</th>
                    <th>Fecha Inicio</th>
                    <th>Días Pendiente</th>
                    <th>Costo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mantenimientos as $mantenimiento)
                    @php
                        $diasPendiente = $mantenimiento->fecha_inicio->diffInDays(now());
                    @endphp
                    <tr>
                        <td>{{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}</td>
                        <td>{{ $mantenimiento->vehiculo->placas }}</td>
                        <td>{{ $mantenimiento->tipo_servicio }}</td>
                        <td>{{ ucfirst($mantenimiento->sistema_vehiculo) }}</td>
                        <td>{{ $mantenimiento->descripcion }}</td>
                        <td>{{ $mantenimiento->proveedor ?? 'Sin asignar' }}</td>
                        <td>{{ $mantenimiento->fecha_inicio->format('d/m/Y') }}</td>
                        <td class="text-center {{ $diasPendiente > 30 ? 'alert-high' : ($diasPendiente > 15 ? 'alert-medium' : 'alert-low') }}">
                            {{ $diasPendiente }} días
                        </td>
                        <td class="text-right">
                            @if($mantenimiento->costo)
                                ${{ number_format($mantenimiento->costo, 2) }}
                            @else
                                Sin costo
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Resumen por tipo de servicio -->
    <div class="summary-section">
        <h2>Resumen por Tipo de Servicio</h2>
        @php
            $resumenTipos = $mantenimientos->groupBy('tipo_servicio')->map(function($registros, $tipo) {
                $costoTotal = $registros->sum('costo');
                $diasPromedio = $registros->avg(function($item) {
                    return $item->fecha_inicio->diffInDays(now());
                });
                
                return [
                    'tipo' => $tipo,
                    'cantidad' => $registros->count(),
                    'costo_total' => $costoTotal,
                    'dias_promedio' => $diasPromedio,
                    'vehiculos_afectados' => $registros->unique('vehiculo_id')->count()
                ];
            });
        @endphp

        <table class="data-table">
            <thead>
                <tr>
                    <th>Tipo de Servicio</th>
                    <th>Cantidad</th>
                    <th>Vehículos Afectados</th>
                    <th>Días Promedio Pendiente</th>
                    <th>Costo Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumenTipos as $resumen)
                    <tr>
                        <td>{{ $resumen['tipo'] }}</td>
                        <td class="text-center">{{ $resumen['cantidad'] }}</td>
                        <td class="text-center">{{ $resumen['vehiculos_afectados'] }}</td>
                        <td class="text-center">{{ number_format($resumen['dias_promedio'], 1) }} días</td>
                        <td class="text-right">${{ number_format($resumen['costo_total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Resumen por sistema de vehículo -->
    <div class="summary-section">
        <h2>Resumen por Sistema de Vehículo</h2>
        @php
            $resumenSistemas = $mantenimientos->groupBy('sistema_vehiculo')->map(function($registros, $sistema) {
                $costoTotal = $registros->sum('costo');
                $diasPromedio = $registros->avg(function($item) {
                    return $item->fecha_inicio->diffInDays(now());
                });
                
                return [
                    'sistema' => $sistema,
                    'cantidad' => $registros->count(),
                    'costo_total' => $costoTotal,
                    'dias_promedio' => $diasPromedio
                ];
            });
        @endphp

        <table class="data-table">
            <thead>
                <tr>
                    <th>Sistema</th>
                    <th>Cantidad</th>
                    <th>Días Promedio Pendiente</th>
                    <th>Costo Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumenSistemas as $resumen)
                    <tr>
                        <td>{{ ucfirst($resumen['sistema']) }}</td>
                        <td class="text-center">{{ $resumen['cantidad'] }}</td>
                        <td class="text-center">{{ number_format($resumen['dias_promedio'], 1) }} días</td>
                        <td class="text-right">${{ number_format($resumen['costo_total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Alertas y recomendaciones -->
    <div class="alert-section">
        <h2>Alertas y Recomendaciones</h2>
        @php
            $mantenimientosUrgentes = $mantenimientos->filter(function($m) {
                return $m->fecha_inicio->diffInDays(now()) > 30;
            });
            
            $mantenimientosAtencion = $mantenimientos->filter(function($m) {
                $dias = $m->fecha_inicio->diffInDays(now());
                return $dias > 15 && $dias <= 30;
            });
        @endphp

        @if($mantenimientosUrgentes->count() > 0)
            <div class="alert-box alert-high">
                <h3>🚨 URGENTE - Más de 30 días pendientes ({{ $mantenimientosUrgentes->count() }} mantenimientos)</h3>
                <ul>
                    @foreach($mantenimientosUrgentes->take(5) as $mantenimiento)
                        <li>{{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }} ({{ $mantenimiento->vehiculo->placas }}) - {{ $mantenimiento->descripcion }}</li>
                    @endforeach
                    @if($mantenimientosUrgentes->count() > 5)
                        <li><em>... y {{ $mantenimientosUrgentes->count() - 5 }} más</em></li>
                    @endif
                </ul>
            </div>
        @endif

        @if($mantenimientosAtencion->count() > 0)
            <div class="alert-box alert-medium">
                <h3>⚠️ ATENCIÓN - Entre 15 y 30 días pendientes ({{ $mantenimientosAtencion->count() }} mantenimientos)</h3>
                <p>Se recomienda hacer seguimiento cercano para evitar que pasen a estado urgente.</p>
            </div>
        @endif
    </div>
@else
    <div class="no-data">
        <p>No se encontraron mantenimientos pendientes con los filtros aplicados.</p>
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
        grid-template-columns: repeat(3, 1fr);
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
    
    .table-section, .summary-section {
        margin: 25px 0;
        page-break-inside: avoid;
    }
    
    .summary-section h2 {
        color: #2c5aa0;
        border-bottom: 2px solid #2c5aa0;
        padding-bottom: 5px;
    }
    
    .alert-section {
        margin: 25px 0;
        page-break-inside: avoid;
    }
    
    .alert-box {
        margin: 15px 0;
        padding: 15px;
        border-radius: 5px;
        border-left: 5px solid;
    }
    
    .alert-high {
        background-color: #fef2f2;
        border-left-color: #dc2626;
        color: #7f1d1d;
    }
    
    .alert-medium {
        background-color: #fffbeb;
        border-left-color: #d97706;
        color: #92400e;
    }
    
    .alert-low {
        background-color: #f0fdf4;
        border-left-color: #16a34a;
        color: #14532d;
    }
    
    .alert-high td {
        background-color: #fef2f2 !important;
        color: #7f1d1d;
        font-weight: bold;
    }
    
    .alert-medium td {
        background-color: #fffbeb !important;
        color: #92400e;
        font-weight: bold;
    }
    
    .alert-low td {
        background-color: #f0fdf4 !important;
        color: #14532d;
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
