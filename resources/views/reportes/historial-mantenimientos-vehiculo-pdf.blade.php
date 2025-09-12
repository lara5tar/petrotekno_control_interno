<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Mantenimientos por Vehículo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ea580c;
            padding-bottom: 10px;
        }
        
        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #ea580c;
            margin-bottom: 5px;
        }
        
        .titulo {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 3px;
        }
        
        .subtitulo {
            font-size: 10px;
            color: #6b7280;
        }
        
        .info-report {
            margin-bottom: 15px;
            background-color: #f9fafb;
            padding: 8px;
            border-radius: 4px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 2px 10px 2px 0;
            width: 25%;
        }
        
        .info-value {
            display: table-cell;
            padding: 2px 0;
        }
        
        .estadisticas {
            margin-bottom: 15px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 8px;
            border: 1px solid #e5e7eb;
            background-color: #f8fafc;
        }
        
        .stat-number {
            font-size: 14px;
            font-weight: bold;
            color: #ea580c;
        }
        
        .stat-label {
            font-size: 8px;
            color: #6b7280;
            margin-top: 2px;
        }
        
        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .tabla th {
            background-color: #ea580c;
            color: white;
            font-weight: bold;
            padding: 6px 4px;
            text-align: left;
            font-size: 9px;
            border: 1px solid #dc2626;
        }
        
        .tabla td {
            padding: 5px 4px;
            border: 1px solid #e5e7eb;
            font-size: 8px;
            vertical-align: top;
        }
        
        .tabla tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .tabla tr:hover {
            background-color: #f3f4f6;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            min-width: 50px;
        }
        
        .badge-preventivo {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .badge-correctivo {
            background-color: #fecaca;
            color: #991b1b;
        }
        
        .badge-default {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .observaciones {
            background-color: #fef3c7;
            padding: 4px;
            margin-top: 2px;
            border-radius: 2px;
            font-size: 7px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">PETROTEKNO</div>
        <div class="titulo">Historial de Mantenimientos por Vehículo</div>
        <div class="subtitulo">Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>

    <!-- Información del reporte -->
    <div class="info-report">
        <h3 style="margin-bottom: 8px; color: #ea580c;">Información del Reporte</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Vehículo:</div>
                <div class="info-value">
                    @if($vehiculoInfo)
                        {{ $vehiculoInfo->marca }} {{ $vehiculoInfo->modelo }} - {{ $vehiculoInfo->placas }}
                    @else
                        Todos los vehículos
                    @endif
                </div>
            </div>
            @if($fechaInicio)
                <div class="info-row">
                    <div class="info-label">Fecha inicio:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</div>
                </div>
            @endif
            @if($fechaFin)
                <div class="info-row">
                    <div class="info-label">Fecha fin:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</div>
                </div>
            @endif
            @if($tipoServicio)
                <div class="info-row">
                    <div class="info-label">Tipo:</div>
                    <div class="info-value">{{ ucfirst($tipoServicio) }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="estadisticas">
        <h3 style="margin-bottom: 8px; color: #ea580c;">Resumen Estadístico</h3>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-number">{{ number_format($estadisticas['total_mantenimientos']) }}</div>
                    <div class="stat-label">Total Mantenimientos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ number_format($estadisticas['mantenimiento_preventivo']) }}</div>
                    <div class="stat-label">Preventivos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ number_format($estadisticas['mantenimiento_correctivo']) }}</div>
                    <div class="stat-label">Correctivos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${{ number_format($estadisticas['costo_total'], 2) }}</div>
                    <div class="stat-label">Costo Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${{ number_format($estadisticas['costo_promedio'], 2) }}</div>
                    <div class="stat-label">Costo Promedio</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de datos -->
    @if($mantenimientos->count() > 0)
        <h3 style="margin-bottom: 8px; color: #ea580c;">Detalle de Mantenimientos ({{ $mantenimientos->count() }} registros)</h3>
        
        <table class="tabla">
            <thead>
                <tr>
                    <th style="width: 10%;">Fecha</th>
                    <th style="width: 15%;">Vehículo</th>
                    <th style="width: 15%;">Ubicación</th>
                    <th style="width: 10%;">Tipo</th>
                    <th style="width: 20%;">Descripción</th>
                    <th style="width: 10%;">Costo</th>
                    <th style="width: 8%;">Km</th>
                    <th style="width: 12%;">Responsable</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mantenimientos as $index => $mantenimiento)
                    <tr>
                        <td>
                            {{ $mantenimiento->fecha_mantenimiento ? $mantenimiento->fecha_mantenimiento->format('d/m/Y') : 'Sin fecha' }}
                        </td>
                        <td>
                            @if($mantenimiento->vehiculo)
                                <strong>{{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}</strong><br>
                                <span style="color: #6b7280;">{{ $mantenimiento->vehiculo->placas }}</span>
                            @else
                                <span style="color: #9ca3af;">Sin vehículo</span>
                            @endif
                        </td>
                        <td>
                            @if($mantenimiento->vehiculo)
                                {{ $mantenimiento->vehiculo->ubicacion }}
                            @else
                                <span style="color: #9ca3af;">Sin ubicación</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($mantenimiento->tipo_mantenimiento == 'preventivo')
                                <span class="badge badge-preventivo">Preventivo</span>
                            @elseif($mantenimiento->tipo_mantenimiento == 'correctivo')
                                <span class="badge badge-correctivo">Correctivo</span>
                            @else
                                <span class="badge badge-default">Sin tipo</span>
                            @endif
                        </td>
                        <td>
                            {{ $mantenimiento->descripcion ?? 'Sin descripción' }}
                            @if($mantenimiento->observaciones)
                                <div class="observaciones">
                                    <strong>Obs:</strong> {{ Str::limit($mantenimiento->observaciones, 80) }}
                                </div>
                            @endif
                        </td>
                        <td class="text-right">
                            <strong>${{ number_format($mantenimiento->costo, 2) }}</strong>
                        </td>
                        <td class="text-right">
                            {{ $mantenimiento->kilometraje ? number_format($mantenimiento->kilometraje) : 'N/A' }}
                        </td>
                        <td>
                            {{ $mantenimiento->responsable ? $mantenimiento->responsable->nombre_completo : 'Sin responsable' }}
                        </td>
                    </tr>
                    
                    @if(($index + 1) % 25 == 0 && $index + 1 < $mantenimientos->count())
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">Fecha</th>
                                    <th style="width: 20%;">Vehículo</th>
                                    <th style="width: 12%;">Tipo</th>
                                    <th style="width: 25%;">Descripción</th>
                                    <th style="width: 10%;">Costo</th>
                                    <th style="width: 8%;">Km</th>
                                    <th style="width: 15%;">Responsable</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <h3>No hay mantenimientos registrados</h3>
            <p>No se encontraron mantenimientos con los filtros aplicados.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>PETROTEKNO - Sistema de Control Interno | Página <span class="pagenum"></span> | Generado: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_text(520, 820, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 8, array(0,0,0));
        }
    </script>
</body>
</html>
