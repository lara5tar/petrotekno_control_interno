@extends('pdf.layouts.base')

@section('title', 'Reporte de Kilometrajes')
@section('report-title', 'Reporte de Kilometrajes de Vehículos')
@section('report-subtitle', 'Reporte completo del registro de kilometrajes y consumo de combustible')

@section('content')
    <!-- Sección de Estadísticas -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Resumen Ejecutivo</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ number_format($estadisticas['total_registros']) }}</span>
                        <span class="stat-label">Total de Registros</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ number_format($estadisticas['vehiculos_con_kilometraje']) }}</span>
                        <span class="stat-label">Vehículos Activos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ number_format($estadisticas['kilometraje_promedio'], 0) }}</span>
                        <span class="stat-label">Km Promedio</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ number_format($kilometrajes->sum('cantidad_combustible'), 1) }}</span>
                        <span class="stat-label">Combustible Total (L)</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información de Filtros Aplicados -->
    <div class="pdf-info-section">
        <div class="info-grid">
            @if($vehiculoId)
                @php
                    $vehiculo = \App\Models\Vehiculo::find($vehiculoId);
                @endphp
                <div class="info-row">
                    <div class="info-label">Vehículo Filtrado:</div>
                    <div class="info-value">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})</div>
                </div>
            @endif
            @if($fechaInicio)
                <div class="info-row">
                    <div class="info-label">Fecha Inicio:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</div>
                </div>
            @endif
            @if($fechaFin)
                <div class="info-row">
                    <div class="info-label">Fecha Fin:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</div>
                </div>
            @endif
            <div class="info-row">
                <div class="info-label">Total de Registros:</div>
                <div class="info-value">{{ $kilometrajes->count() }} registros de kilometraje</div>
            </div>
        </div>
    </div>

    <!-- Espacio para separación entre secciones -->
    <div style="margin-bottom: 20px;"></div>

@if($kilometrajes->count() > 0)
        @php
            $kilometrajesPorVehiculo = $kilometrajes->groupBy('vehiculo_id');
        @endphp

        <!-- Tabla Principal de Kilometrajes -->
        <table class="pdf-table">
            <thead>
                <tr>
                    <th style="width: 8%">#</th>
                    <th style="width: 15%;">Kilometraje</th>
                    <th style="width: 15%;">Fecha</th>
                    <th style="width: 15%;">Combustible</th>
                    <th style="width: 20%;">Obra</th>
                    <th style="width: 15%;">Registrado por</th>
                    <th style="width: 12%;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @php $contador = 1; @endphp
                @foreach($kilometrajesPorVehiculo as $vehiculoId => $registrosVehiculo)
                    @php
                        $vehiculo = $registrosVehiculo->first()->vehiculo;
                        $totalRegistros = $registrosVehiculo->count();
                        $kmMinimo = $registrosVehiculo->min('kilometraje');
                        $kmMaximo = $registrosVehiculo->max('kilometraje');
                        $combustibleTotal = $registrosVehiculo->sum('cantidad_combustible');
                    @endphp
                    
                    <!-- Encabezado del vehículo en la tabla -->
                    <tr class="vehicle-header-row">
                        <td colspan="7" class="vehicle-header-cell">
                            <div class="vehicle-header-content">
                                <strong>{{ $vehiculo->marca }} {{ $vehiculo->modelo }} {{ $vehiculo->anio }}</strong>
                                <span class="vehicle-details-inline">
                                    - Placas: {{ $vehiculo->placas }} 
                                    - Ubicación: {{ $vehiculo->estado ?: 'Sin estado' }}{{ $vehiculo->municipio ? ', ' . $vehiculo->municipio : '' }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    
                    @foreach($registrosVehiculo->sortByDesc('created_at_registro') as $kilometraje)
                        <tr>
                            <td class="text-center">{{ $contador++ }}</td>
                            <td class="text-center">
                                <span class="text-bold">{{ number_format($kilometraje->kilometraje) }} km</span>
                            </td>
                            <td class="text-center">
                                <strong>{{ $kilometraje->fecha_captura->format('d/m/Y') }}</strong>
                                <br><small class="font-small">{{ $kilometraje->fecha_captura->format('H:i') }}</small>
                            </td>
                            <td class="text-center">
                                @if($kilometraje->cantidad_combustible)
                                    <span class="status-badge status-info">{{ number_format($kilometraje->cantidad_combustible, 2) }} L</span>
                                @else
                                    <span class="text-muted font-small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($kilometraje->obra)
                                    <span class="status-badge status-asignado">{{ $kilometraje->obra->nombre_obra }}</span>
                                @else
                                    <span class="text-muted font-small">Sin obra</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($kilometraje->usuarioCaptura)
                                    <span class="registrant-badge">{{ $kilometraje->usuarioCaptura->nombre_completo }}</span>
                                @else
                                    <span class="text-muted font-small">-</span>
                                @endif
                            </td>
                            <td class="font-small">
                                {{ $kilometraje->observaciones ?? 'Sin observaciones' }}
                            </td>
                        </tr>
                    @endforeach
                    
                    <!-- Resumen del vehículo -->
                    <tr class="vehicle-summary-row">
                        <td colspan="7" class="vehicle-summary-cell">
                            <div class="vehicle-summary-chips">
                                <span class="vehicle-summary-badge">
                                    <strong>{{ $totalRegistros }}</strong> Registros
                                </span>
                                <span class="vehicle-summary-badge">
                                    <strong>{{ number_format($kmMinimo) }} km</strong> Km Mínimo
                                </span>
                                <span class="vehicle-summary-badge">
                                    <strong>{{ number_format($kmMaximo) }} km</strong> Km Máximo
                                </span>
                                <span class="vehicle-summary-badge">
                                    <strong>{{ number_format($kmMaximo - $kmMinimo) }} km</strong> Diferencia
                                </span>
                                <span class="vehicle-summary-badge">
                                    <strong>{{ number_format($combustibleTotal, 2) }} L</strong> Combustible
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Resumen General de Kilometrajes -->
        @if(count($kilometrajesPorVehiculo) > 0)
            <div class="pdf-stats-section mt-20">
                <h3 class="stats-title">Resumen General de Kilometrajes</h3>
                <div class="summary-stat">
                    @php
                        $totalKilometrajes = $kilometrajes->count();
                        $promedioKm = $kilometrajes->avg('kilometraje');
                        $maxKm = $kilometrajes->max('kilometraje');
                        $minKm = $kilometrajes->min('kilometraje');
                        $totalCombustible = $kilometrajes->sum('cantidad_combustible');
                    @endphp
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-label">Km Máximo</div>
                            <div class="stat-value">{{ $maxKm ? number_format($maxKm, 0) : '0' }} km</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Diferencia</div>
                            <div class="stat-value">{{ $maxKm && $minKm ? number_format($maxKm - $minKm, 0) : '0' }} km</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Combustible Total</div>
                            <div class="stat-value">{{ number_format($totalCombustible, 1) }} L</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Total Registros</div>
                            <div class="stat-value">{{ $totalKilometrajes }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Promedio Km</div>
                            <div class="stat-value">{{ $promedioKm ? number_format($promedioKm, 0) : '0' }} km</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Mayor Km</div>
                            <div class="stat-value">{{ $maxKm ? number_format($maxKm, 0) : '0' }} km</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Menor Km</div>
                            <div class="stat-value">{{ $minKm ? number_format($minKm, 0) : '0' }} km</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Total Combustible (L)</div>
                            <div class="stat-value">{{ number_format($totalCombustible, 1) }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Vehículos</div>
                            <div class="stat-value">{{ count($kilometrajesPorVehiculo) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="no-data-section">
            <div class="no-data-content">
                <h3 class="no-data-title">No hay registros disponibles</h3>
                <p class="no-data-text">No se encontraron registros de kilometrajes con los filtros aplicados.</p>
            </div>
        </div>
    @endif
@endsection

@section('footer-info')
    Reporte de kilometrajes generado el {{ now()->format('d/m/Y H:i:s') }} - Total: {{ $kilometrajes->count() }} registros
@endsection

@section('styles')
<style>
    /* Chips de resumen general */
    .summary-chips-container {
        margin: 20px 0;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .summary-chips-row {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .summary-chip-badge {
        display: inline-block;
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
        padding: 8px 12px;
        border-radius: 15px;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        white-space: nowrap;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-align: center;
        min-width: 80px;
    }
    
    .summary-chip-badge strong {
        font-size: 11px;
        display: block;
        margin-bottom: 2px;
    }

    /* Estadísticas generales */
    .statistics-section {
        margin: 20px 0;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .statistics-section h2 {
        color: #2c5aa0;
        margin-bottom: 15px;
        font-size: 18px;
        border-bottom: 2px solid #2c5aa0;
        padding-bottom: 5px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 15px;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        padding: 12px;
        background-color: white;
        border-radius: 6px;
        border-left: 4px solid #007bff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .stat-item.primary { border-left-color: #007bff; }
    .stat-item.success { border-left-color: #28a745; }
    .stat-item.info { border-left-color: #17a2b8; }
    .stat-item.warning { border-left-color: #ffc107; }
    
    .stat-icon {
        font-size: 20px;
        margin-right: 10px;
    }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-label {
        display: block;
        font-size: 11px;
        color: #666;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        display: block;
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-top: 2px;
    }

    /* Sección de vehículos */
    .vehicles-section {
        margin: 25px 0;
    }
    
    .vehicles-section > h2 {
        color: #2c5aa0;
        font-size: 18px;
        margin-bottom: 20px;
        border-bottom: 2px solid #2c5aa0;
        padding-bottom: 5px;
    }

    /* Encabezado del vehículo */
    .vehicle-header {
        background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%);
        color: white;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 25px;
    }
    
    .vehicle-info h3 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: bold;
    }
    
    .vehicle-details {
        display: flex;
        gap: 20px;
        font-size: 11px;
    }
    
    .detail-item {
        opacity: 0.9;
    }
    
    .vehicle-stats {
        display: flex;
        gap: 15px;
    }
    
    .mini-stat {
        text-align: center;
        background: rgba(255,255,255,0.1);
        padding: 8px 12px;
        border-radius: 4px;
    }
    
    .mini-stat-value {
        display: block;
        font-size: 14px;
        font-weight: bold;
    }
    
    .mini-stat-label {
        display: block;
        font-size: 9px;
        opacity: 0.8;
        text-transform: uppercase;
    }

    /* Tabla de registros */
    .vehicle-records {
        background: white;
        border: 1px solid #dee2e6;
        border-top: none;
    }
    
    .vehicle-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    
    .vehicle-table th {
        background: #f8f9fa;
        padding: 8px 6px;
        text-align: center;
        font-weight: bold;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        font-size: 9px;
        text-transform: uppercase;
    }
    
    .vehicle-table td {
        padding: 8px 6px;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: top;
    }
    
    .vehicle-table tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    .text-center { text-align: center; }
    
    .km-cell {
        background-color: #e3f2fd !important;
        font-weight: bold;
        color: #1565c0;
    }
    
    .fuel-cell {
        background-color: #fff3e0 !important;
        color: #ef6c00;
    }
    
    .obra-cell {
        font-size: 9px;
    }
    
    .obra-badge {
        background: #e3f2fd;
        color: #1565c0;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 8px;
        font-weight: bold;
    }
    
    .obs-cell {
        font-size: 9px;
        line-height: 1.3;
    }
    
    .no-data {
        color: #999;
        font-style: italic;
        font-size: 9px;
    }

    /* Row del resumen del vehículo completamente centrado con fondo amarillo */
    .vehicle-summary-row {
        background-color: #fff3cd !important;
        border: none !important;
    }
    
    .vehicle-summary-row td {
        text-align: center !important;
        vertical-align: middle !important;
        background-color: #fff3cd !important;
        border: none !important;
        padding: 0 !important;
    }

    /* Celda del resumen del vehículo centrada */
    .vehicle-summary-cell {
        text-align: center !important;
        padding: 0 !important;
        width: 100% !important;
        vertical-align: middle !important;
        background-color: #fff3cd !important;
        border: none !important;
    }

    /* Resumen del vehículo con chips petroyellow centrados */
    .vehicle-summary-chips {
        background: #fff3cd !important;
        padding: 15px !important;
        border: 1px solid #f0c674 !important;
        border-top: none;
        border-radius: 0 0 8px 8px;
        display: block !important;
        text-align: center !important;
        margin: 0 auto !important;
        width: 100% !important;
    }
    
    .vehicle-summary-chips .status-badge {
        display: inline-block !important;
        margin: 4px !important;
        background: #f4c430 !important;
        color: #8b6914 !important;
        border: 1px solid #e6b800 !important;
        text-align: center !important;
    }
    
    .vehicle-summary-chips .vehicle-summary-badge {
        display: inline-block !important;
        margin: 4px !important;
        text-align: center !important;
    }
    
    .vehicle-summary-chips .status-badge {
        margin: 0;
        background: #f4c430 !important;
        color: #8b6914 !important;
        border: 1px solid #e6b800 !important;
    }
    
    /* Chips específicos para el resumen individual de vehículos */
    .vehicle-summary-badge {
        display: inline-block !important;
        margin: 4px !important;
        padding: 6px 10px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        white-space: nowrap;
        text-align: center !important;
        background: #28a745 !important;
        color: #ffffff !important;
        border: 1px solid #1e7e34 !important;
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
    }

    /* Chips específicos para quien registró el kilometraje */
    .registrant-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        white-space: nowrap;
        background: #6f42c1 !important;
        color: #ffffff !important;
        border: 1px solid #5a32a3 !important;
        box-shadow: 0 1px 3px rgba(111, 66, 193, 0.3);
    }

    /* Asegurar que todos los status-badge tengan el color petroyellow */
    .status-badge.status-info {
        background: #f4c430 !important;
        color: #8b6914 !important;
        border: 1px solid #e6b800 !important;
    }
    
    .chip-container {
        display: flex;
        justify-content: space-around;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .summary-chip {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 20px;
        border: 2px solid;
        min-width: 120px;
        font-size: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .chip-yellow {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-color: #f59e0b;
        color: #92400e;
    }
    
    .chip-yellow-highlight {
        background: linear-gradient(135deg, #fde68a 0%, #f59e0b 100%);
        border-color: #d97706;
        color: #ffffff;
        font-weight: bold;
    }
    
    .chip-icon {
        font-size: 14px;
        margin-right: 6px;
        opacity: 0.8;
    }
    
    .chip-content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    
    .chip-label {
        font-size: 8px;
        text-transform: uppercase;
        font-weight: bold;
        opacity: 0.8;
        margin-bottom: 2px;
    }
    
    .chip-value {
        font-size: 11px;
        font-weight: bold;
    }

    /* Resumen del vehículo (estilo anterior - mantener por compatibilidad) */
    .vehicle-summary {
        background: #f8f9fa;
        padding: 10px 15px;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 8px 8px;
        display: flex;
        justify-content: space-around;
        font-size: 10px;
    }
    
    .summary-item {
        text-align: center;
    }
    
    .summary-label {
        display: block;
        color: #666;
        font-size: 9px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    
    .summary-value {
        display: block;
        font-weight: bold;
        color: #333;
        font-size: 11px;
    }

    /* Separador entre vehículos */
    .vehicle-separator {
        height: 20px;
        border-bottom: 2px dashed #dee2e6;
        margin: 20px 0;
    }

    /* Sin datos */
    .no-data {
        text-align: center;
        padding: 40px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .no-data-icon {
        font-size: 48px;
        margin-bottom: 15px;
    }
    
    .no-data h3 {
        color: #666;
        margin-bottom: 10px;
    }
    
    .no-data p {
        color: #999;
        font-style: italic;
    }

    /* Ajustes para impresión */
    @media print {
        .vehicle-header {
            page-break-inside: avoid;
        }
        
        .vehicle-records {
            page-break-inside: auto;
        }
        
        .vehicle-summary {
            page-break-inside: avoid;
        }
    }
</style>
@endsection
