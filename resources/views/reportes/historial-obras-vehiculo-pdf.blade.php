<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Obras por Vehículo - Petrotekno</title>
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
        
        .asignaciones-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .asignaciones-table th,
        .asignaciones-table td {
            border: 1px solid #dee2e6;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        
        .asignaciones-table th {
            background-color: #f1c40f;
            color: #2c3e50;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        
        .asignaciones-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .estado-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .estado-activa {
            background-color: #d4edda;
            color: #155724;
        }
        
        .estado-liberada {
            background-color: #f8f9fa;
            color: #495057;
        }
        
        .estado-transferida {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .vehiculo-info {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .vehiculo-details {
            color: #6c757d;
            font-size: 9px;
        }
        
        .obra-info {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .encargado-info {
            color: #6c757d;
            font-size: 9px;
        }
        
        .km-destacado {
            font-weight: bold;
            color: #0ea5e9;
        }
        
        .sin-registro {
            color: #6c757d;
            font-style: italic;
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
        
        .resumen-adicional {
            margin-top: 25px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
        }
        
        .resumen-adicional h3 {
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
        }
        
        .resumen-grid {
            display: table;
            width: 100%;
        }
        
        .resumen-column {
            display: table-cell;
            vertical-align: top;
            padding-right: 20px;
            width: 33.33%;
        }
        
        .resumen-column:last-child {
            padding-right: 0;
        }
        
        .resumen-item {
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px dotted #dee2e6;
        }
        
        .resumen-item:last-child {
            border-bottom: none;
        }
        
        .resumen-label {
            font-size: 10px;
            color: #6c757d;
            display: block;
        }
        
        .resumen-value {
            font-size: 12px;
            color: #2c3e50;
            font-weight: bold;
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
            <div class="report-title">
                @if($vehiculo)
                    Historial de Obras - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                @else
                    Historial de Obras por Vehículo
                @endif
            </div>
            <div class="report-date">
                @if($vehiculo)
                    Vehículo: {{ $vehiculo->placas }} ({{ $vehiculo->anio }}) | Generado el {{ now()->format('d/m/Y H:i:s') }}
                @else
                    Generado el {{ now()->format('d/m/Y H:i:s') }}
                @endif
            </div>
        </div>
    </div>

    @if($vehiculo)
        <div class="estadisticas">
            <h3>Información del Vehículo</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">{{ $vehiculo->marca }}</span>
                    <span class="stat-label">Marca</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $vehiculo->modelo }}</span>
                    <span class="stat-label">Modelo</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $vehiculo->anio }}</span>
                    <span class="stat-label">Año</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $vehiculo->placas }}</span>
                    <span class="stat-label">Placas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $vehiculo->n_serie ?? 'N/A' }}</span>
                    <span class="stat-label">N° Serie</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">
                        @if($vehiculo->estatus)
                            @if($vehiculo->estatus instanceof \App\Enums\EstadoVehiculo)
                                {{ ucfirst(str_replace('_', ' ', $vehiculo->estatus->value)) }}
                            @else
                                {{ ucfirst(str_replace('_', ' ', $vehiculo->estatus)) }}
                            @endif
                        @else
                            N/A
                        @endif
                    </span>
                    <span class="stat-label">Estado Actual</span>
                </div>
            </div>
        </div>
    @endif

    <div class="estadisticas">
        <h3>
            @if($vehiculo)
                Resumen de Asignaciones - {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
            @else
                Resumen Ejecutivo
            @endif
        </h3>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['total_asignaciones']) }}</span>
                <span class="stat-label">Total Asignaciones</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['asignaciones_activas']) }}</span>
                <span class="stat-label">Activas</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['asignaciones_liberadas']) }}</span>
                <span class="stat-label">Liberadas</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['asignaciones_transferidas']) }}</span>
                <span class="stat-label">Transferidas</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['vehiculos_involucrados']) }}</span>
                <span class="stat-label">Vehículos</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['obras_involucradas']) }}</span>
                <span class="stat-label">Obras</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['kilometraje_total_recorrido']) }}</span>
                <span class="stat-label">KM Recorridos</span>
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
                @if($tipo === 'vehiculo_id')
                    @php
                        $vehiculo = \App\Models\Vehiculo::find($valor);
                    @endphp
                    @if($vehiculo)
                        <span class="filtro-item">
                            <strong>Vehículo:</strong> {{ $vehiculo->marca }} {{ $vehiculo->modelo }} {{ $vehiculo->anio }} - {{ $vehiculo->placas }}
                        </span>
                    @endif
                @elseif($tipo === 'obra_id')
                    @php
                        $obra = \App\Models\Obra::find($valor);
                    @endphp
                    @if($obra)
                        <span class="filtro-item"><strong>Obra:</strong> {{ $obra->nombre_obra }}</span>
                    @endif
                @elseif($tipo === 'estado_asignacion')
                    <span class="filtro-item"><strong>Estado:</strong> {{ ucfirst($valor) }}</span>
                @elseif($tipo === 'fecha_inicio')
                    <span class="filtro-item"><strong>Desde:</strong> {{ \Carbon\Carbon::parse($valor)->format('d/m/Y') }}</span>
                @elseif($tipo === 'fecha_fin')
                    <span class="filtro-item"><strong>Hasta:</strong> {{ \Carbon\Carbon::parse($valor)->format('d/m/Y') }}</span>
                @else
                    <span class="filtro-item"><strong>{{ ucfirst(str_replace('_', ' ', $tipo)) }}:</strong> {{ $valor }}</span>
                @endif
            @endforeach
            <div style="clear: both; margin-top: 8px; font-size: 9px; color: #6c757d; font-style: italic;">
                Los datos mostrados en este reporte están filtrados según los criterios indicados arriba.
            </div>
        </div>
    @endif

    <table class="asignaciones-table">
        <thead>
            <tr>
                <th style="width: 18%;">Vehículo</th>
                <th style="width: 18%;">Obra</th>
                <th style="width: 12%;">Operador</th>
                <th style="width: 14%;">Fechas Asignación</th>
                <th style="width: 8%;">Estado</th>
                <th style="width: 16%;">Kilometraje</th>
                <th style="width: 8%;">Duración</th>
                <th style="width: 6%;">ID</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asignaciones as $asignacion)
                <tr>
                    <td>
                        <div class="vehiculo-info">
                            {{ $asignacion->vehiculo ? "{$asignacion->vehiculo->marca} {$asignacion->vehiculo->modelo}" : 'Sin vehículo' }}
                        </div>
                        <div class="vehiculo-details">
                            {{ $asignacion->vehiculo ? "{$asignacion->vehiculo->anio} - {$asignacion->vehiculo->placas}" : 'Sin datos' }}
                        </div>
                    </td>
                    
                    <td>
                        <div class="obra-info">
                            {{ $asignacion->obra->nombre_obra ?? 'Sin obra' }}
                        </div>
                        @if($asignacion->obra && $asignacion->obra->encargado)
                            <div class="encargado-info">
                                Enc: {{ $asignacion->obra->encargado->nombre }} {{ $asignacion->obra->encargado->apellido_paterno }}
                            </div>
                        @endif
                    </td>
                    
                    <td>
                        {{ $asignacion->operador ? "{$asignacion->operador->nombre} {$asignacion->operador->apellido_paterno}" : 'Sin operador' }}
                    </td>
                    
                    <td>
                        <div><strong>Inicio:</strong> {{ $asignacion->fecha_asignacion ? $asignacion->fecha_asignacion->format('d/m/Y') : 'Sin fecha' }}</div>
                        @if($asignacion->fecha_liberacion)
                            <div class="vehiculo-details"><strong>Fin:</strong> {{ $asignacion->fecha_liberacion->format('d/m/Y') }}</div>
                        @endif
                    </td>
                    
                    <td style="text-align: center;">
                        <span class="estado-badge estado-{{ $asignacion->estado }}">
                            {{ $asignacion->estado_formateado }}
                        </span>
                    </td>
                    
                    <td>
                        @if($asignacion->kilometraje_inicial || $asignacion->kilometraje_final)
                            <div><strong>Inicial:</strong> {{ $asignacion->kilometraje_inicial ? number_format($asignacion->kilometraje_inicial) : 'N/A' }}</div>
                            <div><strong>Final:</strong> {{ $asignacion->kilometraje_final ? number_format($asignacion->kilometraje_final) : 'N/A' }}</div>
                            @if($asignacion->kilometraje_recorrido)
                                <div class="km-destacado"><strong>Recorrido:</strong> {{ number_format($asignacion->kilometraje_recorrido) }} km</div>
                            @endif
                        @else
                            <span class="sin-registro">Sin registro</span>
                        @endif
                    </td>
                    
                    <td style="text-align: center;">
                        @if($asignacion->duracion_dias !== null)
                            {{ $asignacion->duracion_dias }} día{{ $asignacion->duracion_dias != 1 ? 's' : '' }}
                        @else
                            <span class="sin-registro">N/A</span>
                        @endif
                    </td>
                    
                    <td style="text-align: center;">
                        {{ $asignacion->id }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #6c757d;">
                        No se encontraron asignaciones con los filtros aplicados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($asignaciones->count() > 0)
        <div class="resumen-adicional">
            <h3>Análisis Detallado</h3>
            <div class="resumen-grid">
                <div class="resumen-column">
                    <h4 style="font-size: 12px; margin-bottom: 10px; color: #2c3e50;">Distribución por Estado</h4>
                    <div class="resumen-item">
                        <span class="resumen-label">Asignaciones Activas</span>
                        <span class="resumen-value">{{ number_format($estadisticas['asignaciones_activas']) }} ({{ $estadisticas['total_asignaciones'] > 0 ? round(($estadisticas['asignaciones_activas'] / $estadisticas['total_asignaciones']) * 100, 1) : 0 }}%)</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">Asignaciones Liberadas</span>
                        <span class="resumen-value">{{ number_format($estadisticas['asignaciones_liberadas']) }} ({{ $estadisticas['total_asignaciones'] > 0 ? round(($estadisticas['asignaciones_liberadas'] / $estadisticas['total_asignaciones']) * 100, 1) : 0 }}%)</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">Asignaciones Transferidas</span>
                        <span class="resumen-value">{{ number_format($estadisticas['asignaciones_transferidas']) }} ({{ $estadisticas['total_asignaciones'] > 0 ? round(($estadisticas['asignaciones_transferidas'] / $estadisticas['total_asignaciones']) * 100, 1) : 0 }}%)</span>
                    </div>
                </div>

                <div class="resumen-column">
                    <h4 style="font-size: 12px; margin-bottom: 10px; color: #2c3e50;">Recursos Involucrados</h4>
                    <div class="resumen-item">
                        <span class="resumen-label">Vehículos Únicos</span>
                        <span class="resumen-value">{{ number_format($estadisticas['vehiculos_involucrados']) }}</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">Obras Únicas</span>
                        <span class="resumen-value">{{ number_format($estadisticas['obras_involucradas']) }}</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">Total Kilómetros</span>
                        <span class="resumen-value">{{ number_format($estadisticas['kilometraje_total_recorrido']) }} km</span>
                    </div>
                </div>

                <div class="resumen-column">
                    <h4 style="font-size: 12px; margin-bottom: 10px; color: #2c3e50;">Métricas de Rendimiento</h4>
                    <div class="resumen-item">
                        <span class="resumen-label">Promedio Días por Asignación</span>
                        <span class="resumen-value">{{ $estadisticas['promedio_dias_asignacion'] ? round($estadisticas['promedio_dias_asignacion'], 1) : '0' }} días</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">Registros Analizados</span>
                        <span class="resumen-value">{{ number_format($asignaciones->count()) }}</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">Fecha de Generación</span>
                        <span class="resumen-value">{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="footer">
        <p>
            <strong>PETROTEKNO S.A. de C.V.</strong> - Sistema de Control Interno<br>
            Reporte generado automáticamente el {{ now()->format('d \d\e F \d\e Y \a \l\a\s H:i:s') }}
        </p>
    </div>
</body>
</html>
