<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Vehículos - Petrotekno</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            border-bottom: 2px solid #f1c40f;
            padding-bottom: 20px;
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }
        
        .logo-section {
            display: table-cell;
            width: 150px;
            vertical-align: middle;
        }
        
        .logo {
            max-width: 140px;
            height: auto;
        }
        
        .company-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .report-date {
            font-size: 12px;
            color: #95a5a6;
        }
        
        .estadisticas {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .estadisticas h3 {
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
        }
        
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 8px;
            border-right: 1px solid #dee2e6;
        }
        
        .stat-item:last-child {
            border-right: none;
        }
        
        .stat-number {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            display: block;
        }
        
        .stat-label {
            font-size: 10px;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        
        .vehiculos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .vehiculos-table th,
        .vehiculos-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        
        .vehiculos-table th {
            background-color: #f1c40f;
            color: #2c3e50;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        .vehiculos-table td {
            font-size: 10px;
        }
        
        .vehiculos-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-disponible {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-asignado {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-mantenimiento {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-fuera-servicio {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-baja {
            background-color: #f5c6cb;
            color: #721c24;
        }
        
        .km-diferencia {
            font-weight: bold;
        }
        
        .km-diferencia.positiva {
            color: #28a745;
        }
        
        .km-diferencia.negativa {
            color: #dc3545;
        }
        
        .sin-registro {
            color: #6c757d;
            font-style: italic;
        }
        
        .necesita-registro {
            background-color: #fff3cd;
            color: #856404;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 9px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
        
        .filtros-aplicados {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        
        .filtros-aplicados h4 {
            color: #495057;
            font-size: 12px;
            margin-bottom: 8px;
            border-bottom: 1px solid #adb5bd;
            padding-bottom: 3px;
        }
        
        .filtro-item {
            display: inline-block;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            padding: 3px 8px;
            margin-right: 8px;
            margin-bottom: 5px;
            font-size: 10px;
            color: #495057;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('logo.jpeg'))) }}" alt="Logo Petrotekno" class="logo">
        </div>
        <div class="company-info">
            <div class="company-name">PETROTEKNO</div>
            <div class="report-title">Inventario de Vehículos</div>
            <div class="report-date">Generado el {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>

    <div class="estadisticas">
        <h3>Resumen Ejecutivo</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">{{ $estadisticas['total_vehiculos'] }}</span>
                <span class="stat-label">Total Vehículos</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $estadisticas['vehiculos_disponibles'] }}</span>
                <span class="stat-label">Disponibles</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $estadisticas['vehiculos_asignados'] }}</span>
                <span class="stat-label">Asignados</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $estadisticas['vehiculos_mantenimiento'] }}</span>
                <span class="stat-label">En Mantenimiento</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $estadisticas['vehiculos_con_kilometraje_registrado'] }}</span>
                <span class="stat-label">Con Kilometraje</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $estadisticas['vehiculos_sin_kilometraje_registrado'] }}</span>
                <span class="stat-label">Sin Kilometraje</span>
            </div>
        </div>
    </div>

    @php
        $filtrosActivos = collect($filtros ?? [])->filter(function($valor) {
            return !empty($valor) && $valor !== null;
        });
    @endphp

    @if($filtrosActivos->count() > 0)
        <div class="filtros-aplicados">
            <h4>Filtros Aplicados</h4>
            @foreach($filtrosActivos as $tipo => $valor)
                @if($tipo === 'estatus' || $tipo === 'estado')
                    <span class="filtro-item">
                        <strong>Estado:</strong> 
                        @switch($valor)
                            @case('disponible')
                                Disponible
                                @break
                            @case('asignado')
                                Asignado
                                @break
                            @case('en_mantenimiento')
                                En Mantenimiento
                                @break
                            @case('fuera_de_servicio')
                                Fuera de Servicio
                                @break
                            @case('baja')
                                Dado de Baja
                                @break
                            @default
                                {{ ucfirst(str_replace('_', ' ', $valor)) }}
                        @endswitch
                    </span>
                @elseif($tipo === 'marca')
                    <span class="filtro-item"><strong>Marca:</strong> {{ $valor }}</span>
                @elseif($tipo === 'anio' || $tipo === 'año')
                    <span class="filtro-item"><strong>Año:</strong> {{ $valor }}</span>
                @else
                    <span class="filtro-item"><strong>{{ ucfirst(str_replace('_', ' ', $tipo)) }}:</strong> {{ $valor }}</span>
                @endif
            @endforeach
            <div style="clear: both; margin-top: 8px; font-size: 9px; color: #6c757d; font-style: italic;">
                Los datos mostrados en este reporte están filtrados según los criterios indicados arriba.
            </div>
        </div>
    @endif

    <table class="vehiculos-table">
        <thead>
            <tr>
                <th>Marca/Modelo</th>
                <th>Año</th>
                <th>Placas</th>
                <th>Serie</th>
                <th>Estado</th>
                <th>Ubicación</th>
                <th>KM Actual</th>
                <th>Estado KM</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehiculos as $vehiculo)
                <tr>
                    <td>
                        <strong>{{ $vehiculo->marca }}</strong><br>
                        {{ $vehiculo->modelo }}
                    </td>
                    <td>{{ $vehiculo->anio }}</td>
                    <td>{{ $vehiculo->placas ?: 'Sin placas' }}</td>
                    <td>{{ $vehiculo->n_serie ?: 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ str_replace('_', '-', $vehiculo->estado_enum->value) }}">
                            {{ $vehiculo->estado_enum->nombre() }}
                        </span>
                    </td>
                    <td>
                        @if($vehiculo->estado || $vehiculo->municipio)
                            {{ $vehiculo->estado ?: 'Sin estado' }}
                            @if($vehiculo->municipio)
                                <br>{{ $vehiculo->municipio }}
                            @endif
                        @else
                            <span class="sin-registro">Sin ubicación</span>
                        @endif
                    </td>
                    <td>{{ number_format($vehiculo->kilometraje_actual) }} km</td>
                    <td>
                        @if($vehiculo->necesita_registro_km)
                            <span class="necesita-registro">
                                Requiere actualización
                                @if($vehiculo->dias_sin_registro)
                                    <br>({{ $vehiculo->dias_sin_registro }} días)
                                @endif
                            </span>
                        @else
                            Al día
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px; color: #6c757d;">
                        No se encontraron vehículos con los filtros aplicados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>
            <strong>PETROTEKNO S.A. de C.V.</strong> - Sistema de Control Interno<br>
            Reporte generado automáticamente el {{ now()->format('d \d\e F \d\e Y \a \l\a\s H:i:s') }}
        </p>
    </div>
</body>
</html>
