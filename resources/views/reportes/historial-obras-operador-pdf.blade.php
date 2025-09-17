<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Obras por Operador - Petrotekno</title>
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
            text-align: center;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            color: #34495e;
            margin-bottom: 5px;
        }
        
        .report-date {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .estadisticas {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .estadisticas h3 {
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 10px;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            border-right: 1px solid #ecf0f1;
            width: 16.66%;
        }
        
        .stat-item:last-child {
            border-right: none;
        }
        
        .stat-number {
            display: block;
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 10px;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        
        .info-operador {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-operador h3 {
            font-size: 14px;
            color: #1976d2;
            margin-bottom: 10px;
        }
        
        .info-operador-grid {
            display: table;
            width: 100%;
        }
        
        .info-operador-item {
            display: table-cell;
            width: 33.33%;
            padding: 5px 10px;
        }
        
        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .tabla th,
        .tabla td {
            border: 1px solid #bdc3c7;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        .tabla th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .tabla tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .tabla tbody tr:hover {
            background-color: #e9ecef;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }
        
        .badge-asignacion {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-cambio {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-remocion {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .filtros-aplicados {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .filtros-aplicados h4 {
            font-size: 12px;
            color: #856404;
            margin-bottom: 8px;
        }
        
        .filtro-item {
            display: inline-block;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 3px 8px;
            margin: 2px;
            font-size: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
            font-size: 10px;
            color: #7f8c8d;
        }
        
        .detalle-row {
            background-color: #f1f3f4 !important;
        }
        
        .detalle-row td {
            font-style: italic;
            color: #666;
            border-top: none !important;
            padding-top: 5px !important;
            padding-bottom: 5px !important;
        }
        
        .sin-datos {
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
            padding: 40px;
            background-color: #f8f9fa;
            border: 1px dashed #bdc3c7;
            border-radius: 5px;
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
                @if($operador)
                    Historial de Obras - {{ $operador->nombre_completo }}
                @else
                    Historial de Obras por Operador
                @endif
            </div>
            <div class="report-date">
                @if($operador)
                    Operador: {{ $operador->nombre_completo }} | Generado el {{ now()->format('d/m/Y H:i:s') }}
                @else
                    Generado el {{ now()->format('d/m/Y H:i:s') }}
                @endif
            </div>
        </div>
    </div>

    @if($operador)
        <div class="info-operador">
            <h3>Información del Operador</h3>
            <div class="info-operador-grid">
                <div class="info-operador-item">
                    <span class="stat-number">{{ $operador->nombre_completo }}</span>
                    <span class="stat-label">Nombre Completo</span>
                </div>
                <div class="info-operador-item">
                    <span class="stat-number">{{ $operador->estatus ?? 'N/A' }}</span>
                    <span class="stat-label">Estatus</span>
                </div>
                <div class="info-operador-item">
                    <span class="stat-number">{{ $operador->categoria->nombre ?? 'N/A' }}</span>
                    <span class="stat-label">Categoría</span>
                </div>
            </div>
        </div>
    @endif

    <div class="estadisticas">
        <h3>
            @if($operador)
                Estadísticas del Operador: {{ $operador->nombre_completo }}
            @else
                Estadísticas Generales
            @endif
        </h3>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['total_movimientos']) }}</span>
                <span class="stat-label">Total Movimientos</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['asignaciones_iniciales']) }}</span>
                <span class="stat-label">Asignaciones Iniciales</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['cambios_operador']) }}</span>
                <span class="stat-label">Cambios de Operador</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['remociones']) }}</span>
                <span class="stat-label">Remociones</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['vehiculos_diferentes']) }}</span>
                <span class="stat-label">Activos Diferentes</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ number_format($estadisticas['obras_diferentes']) }}</span>
                <span class="stat-label">Obras Diferentes</span>
            </div>
        </div>
    </div>

    @php
        $filtrosActivos = collect($filtros)->filter(function($valor, $tipo) {
            return !empty($valor) && $valor !== null;
        });
    @endphp

    @if($filtrosActivos->count() > 0)
        <div class="filtros-aplicados">
            <h4>Filtros Aplicados</h4>
            @foreach($filtrosActivos as $tipo => $valor)
                @if($tipo === 'operador_id')
                    @php
                        $operador = \App\Models\Personal::find($valor);
                    @endphp
                    @if($operador)
                        <span class="filtro-item">
                            <strong>Operador:</strong> {{ $operador->nombre_completo }}
                        </span>
                    @endif
                @elseif($tipo === 'obra_id')
                    @php
                        $obra = \App\Models\Obra::find($valor);
                    @endphp
                    @if($obra)
                        <span class="filtro-item"><strong>Obra:</strong> {{ $obra->nombre_obra }}</span>
                    @endif
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

    @if($asignaciones->count() > 0)
        <table class="tabla">
            <thead>
                <tr>
                    <th style="width: 10%;">Fecha</th>
                    <th style="width: 15%;">Tipo Movimiento</th>
                    <th style="width: 18%;">Activo</th>
                    <th style="width: 15%;">Operador Anterior</th>
                    <th style="width: 15%;">Operador Nuevo</th>
                    <th style="width: 15%;">Obra</th>
                    <th style="width: 12%;">Usuario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asignaciones as $asignacion)
                    <tr>
                        <td>
                            {{ $asignacion->fecha_asignacion->format('d/m/Y') }}<br>
                            <small style="color: #666;">{{ $asignacion->fecha_asignacion->format('H:i') }}</small>
                        </td>
                        <td>
                            @php
                                $badgeClass = 'badge ';
                                switch($asignacion->tipo_movimiento) {
                                    case 'asignacion_inicial':
                                        $badgeClass .= 'badge-asignacion';
                                        $texto = 'Asignación Inicial';
                                        break;
                                    case 'cambio_operador':
                                        $badgeClass .= 'badge-cambio';
                                        $texto = 'Cambio de Operador';
                                        break;
                                    case 'remocion_operador':
                                        $badgeClass .= 'badge-remocion';
                                        $texto = 'Remoción';
                                        break;
                                    default:
                                        $badgeClass .= 'badge-asignacion';
                                        $texto = ucfirst(str_replace('_', ' ', $asignacion->tipo_movimiento));
                                }
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $texto }}</span>
                        </td>
                        <td>
                            @if($asignacion->vehiculo)
                                <strong>{{ $asignacion->vehiculo->marca }} {{ $asignacion->vehiculo->modelo }}</strong><br>
                                <small>{{ $asignacion->vehiculo->placas }} ({{ $asignacion->vehiculo->anio }})</small>
                            @else
                                <em style="color: #999;">Sin activo</em>
                            @endif
                        </td>
                        <td>
                            @if($asignacion->operadorAnterior)
                                {{ $asignacion->operadorAnterior->nombre_completo }}
                            @else
                                <em style="color: #999;">Sin operador anterior</em>
                            @endif
                        </td>
                        <td>
                            @if($asignacion->operadorNuevo)
                                {{ $asignacion->operadorNuevo->nombre_completo }}
                            @else
                                <em style="color: #999;">Sin operador nuevo</em>
                            @endif
                        </td>
                        <td>
                            @if($asignacion->obra)
                                {{ $asignacion->obra->nombre_obra }}
                            @else
                                <em style="color: #999;">Sin obra</em>
                            @endif
                        </td>
                        <td>
                            {{ $asignacion->usuarioAsigno ? $asignacion->usuarioAsigno->email : 'Sistema' }}
                        </td>
                    </tr>

                    @if($asignacion->motivo || $asignacion->observaciones)
                        <tr class="detalle-row">
                            <td colspan="7">
                                @if($asignacion->motivo)
                                    <strong>Motivo:</strong> {{ $asignacion->motivo }}<br>
                                @endif
                                @if($asignacion->observaciones)
                                    <strong>Observaciones:</strong> {{ $asignacion->observaciones }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <div class="sin-datos">
            <h3>No se encontraron movimientos</h3>
            <p>No hay registros de movimientos de operadores que coincidan con los filtros aplicados.</p>
        </div>
    @endif

    <!-- Resumen final -->
    <div style="margin-top: 30px; border-top: 1px solid #bdc3c7; padding-top: 15px;">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%;">
                <h4 style="font-size: 12px; color: #2c3e50; margin-bottom: 5px;">Resumen del Reporte</h4>
                <div style="font-size: 10px; color: #7f8c8d;">
                    <div>Total de registros: <strong>{{ number_format($asignaciones->count()) }}</strong></div>
                    <div>Fecha de generación: <strong>{{ now()->format('d/m/Y H:i:s') }}</strong></div>
                    @if($operador)
                        <div>Operador: <strong>{{ $operador->nombre_completo }}</strong></div>
                    @endif
                </div>
            </div>
            <div style="display: table-cell; width: 50%; text-align: right; vertical-align: top;">
                <div style="font-size: 10px; color: #7f8c8d;">
                    <div>Sistema de Control Interno</div>
                    <div>PETROTEKNO</div>
                    <div>{{ now()->format('Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        Página <span class="pagenum"></span> de <span class="pagecount"></span> | Historial de Obras por Operador | PETROTEKNO {{ now()->format('Y') }}
    </div>
</body>
</html>
