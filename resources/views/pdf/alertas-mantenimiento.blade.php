<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Alertas de Mantenimiento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #e74c3c;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .header .subtitle {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .resumen {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .resumen h2 {
            color: #2c3e50;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        
        .resumen-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .resumen-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #bdc3c7;
        }
        
        .resumen-item:last-child {
            border-bottom: none;
        }
        
        .resumen-label {
            font-weight: bold;
            color: #34495e;
        }
        
        .resumen-valor {
            color: #2c3e50;
        }
        
        .alertas-section {
            margin-bottom: 25px;
        }
        
        .alertas-section h2 {
            color: #2c3e50;
            margin: 0 0 20px 0;
            font-size: 18px;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 10px;
        }
        
        .alerta {
            border: 1px solid #e9ecef;
            border-radius: 5px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .alerta-header {
            background-color: #fff3cd;
            padding: 15px;
            border-bottom: 1px solid #ffeaa7;
        }
        
        .alerta-header.critica {
            background-color: #f8d7da;
            border-bottom-color: #f5c6cb;
        }
        
        .alerta-header.alta {
            background-color: #ffecb5;
            border-bottom-color: #ffeaa7;
        }
        
        .vehiculo-info {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .urgencia {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .urgencia.normal {
            background-color: #d4edda;
            color: #155724;
        }
        
        .urgencia.alta {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .urgencia.critica {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alerta-body {
            padding: 15px;
        }
        
        .detalle-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .detalle-seccion h4 {
            margin: 0 0 10px 0;
            color: #34495e;
            font-size: 14px;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 5px;
        }
        
        .detalle-item {
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .detalle-label {
            font-weight: bold;
            color: #7f8c8d;
            display: inline-block;
            width: 130px;
        }
        
        .detalle-valor {
            color: #2c3e50;
        }
        
        .historial-mantenimientos {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }
        
        .historial-mantenimientos h4 {
            margin: 0 0 10px 0;
            color: #34495e;
            font-size: 14px;
        }
        
        .mantenimiento-item {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 3px;
            font-size: 11px;
        }
        
        .mantenimiento-fecha {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .no-alertas {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚨 REPORTE DE ALERTAS DE MANTENIMIENTO</h1>
        <div class="subtitle">
            Sistema de Control Interno - Solupatch<br>
            Generado el {{ $fechaGeneracion }}
        </div>
    </div>

    <div class="resumen">
        <h2>📊 Resumen Ejecutivo</h2>
        <div class="resumen-grid">
            <div>
                <div class="resumen-item">
                    <span class="resumen-label">Total de Alertas:</span>
                    <span class="resumen-valor">{{ $resumen['total_alertas'] }}</span>
                </div>
                <div class="resumen-item">
                    <span class="resumen-label">Vehículos Afectados:</span>
                    <span class="resumen-valor">{{ $resumen['vehiculos_afectados'] }}</span>
                </div>
            </div>
            <div>
                <div class="resumen-item">
                    <span class="resumen-label">Alertas Críticas:</span>
                    <span class="resumen-valor">{{ $resumen['por_urgencia']['critica'] }}</span>
                </div>
                <div class="resumen-item">
                    <span class="resumen-label">Alertas Altas:</span>
                    <span class="resumen-valor">{{ $resumen['por_urgencia']['alta'] }}</span>
                </div>
                <div class="resumen-item">
                    <span class="resumen-label">Alertas Normales:</span>
                    <span class="resumen-valor">{{ $resumen['por_urgencia']['normal'] }}</span>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 15px;">
            <strong>Distribución por Sistema:</strong>
            Motor: {{ $resumen['por_sistema']['Motor'] ?? 0 }} | 
            Transmisión: {{ $resumen['por_sistema']['Transmision'] ?? 0 }} | 
            Hidráulico: {{ $resumen['por_sistema']['Hidraulico'] ?? 0 }}
        </div>
    </div>

    @if(count($alertas) > 0)
        <div class="alertas-section">
            <h2>🔧 Detalle de Alertas</h2>
            
            @foreach($alertas as $index => $alerta)
                <div class="alerta">
                    <div class="alerta-header {{ $alerta['urgencia'] }}">
                        <div class="vehiculo-info">
                            {{ $alerta['vehiculo_info']['nombre_completo'] }}
                        </div>
                        <div>
                            Sistema: <strong>{{ $alerta['sistema_mantenimiento']['nombre_sistema'] }}</strong>
                            <span class="urgencia {{ $alerta['urgencia'] }}">{{ ucfirst($alerta['urgencia']) }}</span>
                        </div>
                    </div>
                    
                    <div class="alerta-body">
                        <div class="detalle-grid">
                            <div class="detalle-seccion">
                                <h4>📋 Información del Vehículo</h4>
                                <div class="detalle-item">
                                    <span class="detalle-label">Kilometraje Actual:</span>
                                    <span class="detalle-valor">{{ $alerta['vehiculo_info']['kilometraje_actual'] }}</span>
                                </div>
                                <div class="detalle-item">
                                    <span class="detalle-label">Marca/Modelo:</span>
                                    <span class="detalle-valor">{{ $alerta['vehiculo_info']['marca'] }} {{ $alerta['vehiculo_info']['modelo'] }}</span>
                                </div>
                                <div class="detalle-item">
                                    <span class="detalle-label">Placas:</span>
                                    <span class="detalle-valor">{{ $alerta['vehiculo_info']['placas'] }}</span>
                                </div>
                            </div>
                            
                            <div class="detalle-seccion">
                                <h4>⚙️ Información del Mantenimiento</h4>
                                <div class="detalle-item">
                                    <span class="detalle-label">Intervalo Configurado:</span>
                                    <span class="detalle-valor">{{ $alerta['sistema_mantenimiento']['intervalo_km'] }}</span>
                                </div>
                                <div class="detalle-item">
                                    <span class="detalle-label">Vencido por:</span>
                                    <span class="detalle-valor">{{ number_format($alerta['intervalo_alcanzado']['km_exceso']) }} km</span>
                                </div>
                                <div class="detalle-item">
                                    <span class="detalle-label">Porcentaje Sobrepaso:</span>
                                    <span class="detalle-valor">{{ $alerta['intervalo_alcanzado']['porcentaje_sobrepaso'] }}</span>
                                </div>
                                <div class="detalle-item">
                                    <span class="detalle-label">Próximo Mantenimiento:</span>
                                    <span class="detalle-valor">{{ number_format($alerta['intervalo_alcanzado']['proximo_mantenimiento_esperado']) }} km</span>
                                </div>
                            </div>
                        </div>
                        
                        @if($alerta['historial_mantenimientos']['cantidad_encontrada'] > 0)
                            <div class="historial-mantenimientos">
                                <h4>🔧 Historial de Mantenimientos ({{ $alerta['sistema_mantenimiento']['nombre_sistema'] }})</h4>
                                @foreach($alerta['historial_mantenimientos']['mantenimientos'] as $mantenimiento)
                                    <div class="mantenimiento-item">
                                        <div class="mantenimiento-fecha">{{ $mantenimiento['fecha'] }} - {{ number_format($mantenimiento['kilometraje']) }} km</div>
                                        <div><strong>Tipo:</strong> {{ $mantenimiento['tipo_servicio'] }}</div>
                                        <div><strong>Descripción:</strong> {{ $mantenimiento['descripcion'] }}</div>
                                        <div><strong>Proveedor:</strong> {{ $mantenimiento['proveedor'] }} | <strong>Costo:</strong> {{ $mantenimiento['costo'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="historial-mantenimientos">
                                <h4>🔧 Historial de Mantenimientos</h4>
                                <div style="font-style: italic; color: #7f8c8d;">No se encontraron mantenimientos previos de este sistema.</div>
                            </div>
                        @endif
                        
                        <div style="margin-top: 15px; padding: 10px; background-color: #e8f4fd; border-radius: 3px;">
                            <strong>Resumen:</strong> {{ $alerta['mensaje_resumen'] }}
                        </div>
                    </div>
                </div>
                
                @if($index < count($alertas) - 1 && ($index + 1) % 2 === 0)
                    <div class="page-break"></div>
                @endif
            @endforeach
        </div>
    @else
        <div class="no-alertas">
            <h2>✅ No hay alertas de mantenimiento pendientes</h2>
            <p>Todos los vehículos están al día con sus mantenimientos programados.</p>
        </div>
    @endif

    <div class="footer">
        <p><strong>Sistema de Control Interno - Solupatch</strong></p>
        <p>Reporte generado automáticamente | Fecha: {{ $fechaGeneracion }}</p>
        <p>Este documento contiene información confidencial de la empresa</p>
    </div>
</body>
</html>
