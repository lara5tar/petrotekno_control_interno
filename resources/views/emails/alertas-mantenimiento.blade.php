<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚧 Alerta de Mantenimiento - Sistema PetroTekno</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }
        .header {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .test-badge {
            background-color: #fbbf24;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .alert-item {
            background-color: #fff8f0;
            border: 2px solid #ff6b35;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            position: relative;
        }
        .alert-item::before {
            content: "⚠️";
            position: absolute;
            top: -10px;
            right: 20px;
            background-color: #fff;
            padding: 5px 10px;
            border-radius: 50%;
            font-size: 20px;
        }
        .vehicle-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .vehicle-title {
            font-size: 18px;
            font-weight: 600;
            color: #007bff;
            margin: 0 0 8px 0;
        }
        .vehicle-details {
            color: #6c757d;
            font-size: 14px;
        }
        .maintenance-info {
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }
        .maintenance-title {
            font-size: 16px;
            font-weight: 600;
            color: #856404;
            margin: 0 0 10px 0;
        }
        .interval-details {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        .interval-item {
            background: rgba(255, 255, 255, 0.7);
            padding: 12px;
            border-radius: 6px;
            text-align: center;
        }
        .interval-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .interval-value {
            font-size: 16px;
            font-weight: 600;
            color: #856404;
        }
        .history-section {
            background-color: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #0066cc;
        }
        .history-title {
            font-size: 16px;
            font-weight: 600;
            color: #0066cc;
            margin: 0 0 15px 0;
        }
        .maintenance-record {
            background: rgba(255, 255, 255, 0.8);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            border: 1px solid #cce7ff;
        }
        .maintenance-record:last-child {
            margin-bottom: 0;
        }
        .record-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .record-date {
            font-weight: 600;
            color: #0066cc;
        }
        .record-km {
            background-color: #0066cc;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .record-details {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }
        .no-history {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
        }
        .urgency-high {
            border-color: #dc3545 !important;
            background-color: #f8d7da !important;
        }
        .urgency-high::before {
            content: "🚨";
        }
        .urgency-medium {
            border-color: #ffc107 !important;
            background-color: #fff3cd !important;
        }
        .urgency-low {
            border-color: #28a745 !important;
            background-color: #d4edda !important;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .system-badge {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            .content {
                padding: 20px;
            }
            .interval-details {
                grid-template-columns: 1fr;
            }
            .record-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            @if($esTest ?? false)
                <div class="test-badge">🧪 CORREO DE PRUEBA</div>
            @endif
            <h1>� Alerta de Mantenimiento</h1>
            <p>Sistema de Gestión de Vehículos PetroTekno</p>
        </div>

        <div class="content">
            @if($esTest ?? false)
                <div style="background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <p style="margin: 0; color: #92400e; font-weight: 500;">
                        🧪 <strong>Este es un correo de prueba</strong> para verificar que el sistema de alertas funciona correctamente.
                        No se requiere ninguna acción.
                    </p>
                </div>
            @endif

            @if(!empty($alertas))
                @foreach($alertas as $alerta)
                <div class="alert-item urgency-{{ $alerta['urgencia'] }}">
                    
                    {{-- Información del Vehículo --}}
                    <div class="vehicle-info">
                        <div class="vehicle-title">
                            🚛 {{ $alerta['vehiculo_info']['nombre_completo'] }}
                        </div>
                        <div class="vehicle-details">
                            <div style="background-color: #007bff; color: white; padding: 12px 16px; border-radius: 8px; margin-top: 12px; text-align: center;">
                                <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">KILOMETRAJE ACTUAL</div>
                                <div style="font-size: 24px; font-weight: bold;">{{ $alerta['vehiculo_info']['kilometraje_actual'] }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Información del Sistema y Tipo de Mantenimiento --}}
                    <div class="maintenance-info">
                        <div class="system-badge">
                            {{ $alerta['sistema_mantenimiento']['nombre_sistema'] }}
                        </div>
                        <div class="maintenance-title">
                            {{ $alerta['sistema_mantenimiento']['tipo_mantenimiento'] }}
                        </div>
                        <p style="margin: 8px 0; color: #666; font-size: 14px;">
                            {{ $alerta['sistema_mantenimiento']['descripcion_sistema'] }}
                        </p>
                        
                        <div class="interval-details">
                            <div class="interval-item">
                                <div class="interval-label">Intervalo {{ ucfirst($alerta['sistema_mantenimiento']['nombre_sistema']) }}</div>
                                <div class="interval-value">{{ $alerta['sistema_mantenimiento']['intervalo_km'] }}</div>
                            </div>
                            @if($alerta['historial_mantenimientos']['cantidad_encontrada'] > 0)
                            <div class="interval-item">
                                <div class="interval-label">Último mantenimiento de este sistema</div>
                                <div class="interval-value">{{ number_format($alerta['intervalo_alcanzado']['kilometraje_base']) }} km</div>
                            </div>
                            @endif
                            <div class="interval-item" style="background-color: #dc3545; color: white;">
                                <div class="interval-label" style="color: white;">Kilometraje Actual</div>
                                <div class="interval-value" style="color: white; font-size: 20px;">{{ number_format($alerta['intervalo_alcanzado']['kilometraje_actual']) }} km</div>
                            </div>
                            <div class="interval-item" style="background-color: #fff3cd; border-left: 4px solid #ffc107;">
                                <div class="interval-label">Exceso</div>
                                <div class="interval-value">{{ number_format($alerta['intervalo_alcanzado']['km_exceso']) }} km</div>
                            </div>
                        </div>
                    </div>

                    {{-- Historial de Mantenimientos --}}
                    <div class="history-section">
                        <div class="history-title">
                            📋 Últimos Mantenimientos de {{ $alerta['sistema_mantenimiento']['nombre_sistema'] }}
                        </div>
                        
                        @if($alerta['historial_mantenimientos']['cantidad_encontrada'] > 0)
                            @foreach($alerta['historial_mantenimientos']['mantenimientos'] as $index => $mantenimiento)
                            <div class="maintenance-record">
                                <div class="record-header">
                                    <span class="record-date">
                                        {{ $index === 0 ? '🏷️ Último:' : '📅 Anterior:' }} {{ $mantenimiento['fecha'] }}
                                    </span>
                                    <span class="record-km">{{ number_format($mantenimiento['kilometraje']) }} km</span>
                                </div>
                                <div class="record-details">
                                    <strong>Tipo:</strong> {{ ucfirst(strtolower($mantenimiento['tipo_servicio'])) }}<br>
                                    <strong>Descripción:</strong> {{ $mantenimiento['descripcion'] }}<br>
                                    @if($mantenimiento['proveedor'] !== 'No especificado')
                                    <strong>Proveedor:</strong> {{ $mantenimiento['proveedor'] }}<br>
                                    @endif
                                    @if($mantenimiento['costo'] !== 'No especificado')
                                    <strong>Costo:</strong> {{ $mantenimiento['costo'] }}
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="no-history">
                                <p>📝 No se encontraron mantenimientos previos para este sistema</p>
                                <p style="font-size: 12px; margin-top: 10px;">
                                    Este será el primer mantenimiento registrado del sistema {{ strtolower($alerta['sistema_mantenimiento']['nombre_sistema']) }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Mensaje de Resumen --}}
                    <div style="margin-top: 20px; padding: 15px; background-color: #f1f3f4; border-radius: 8px; border-left: 4px solid #6c757d;">
                        <p style="margin: 0; font-size: 14px; color: #495057; line-height: 1.5;">
                            <strong>📝 Resumen:</strong> {{ $alerta['mensaje_resumen'] }}
                        </p>
                        <p style="margin: 8px 0 0 0; font-size: 12px; color: #6c757d;">
                            <strong>Detectado el:</strong> {{ $alerta['fecha_deteccion'] }}
                        </p>
                    </div>

                </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 40px; color: #6c757d;">
                    <p style="font-size: 18px; margin: 0;">
                        @if($esTest ?? false)
                            🧪 <strong>Correo de prueba enviado exitosamente</strong><br>
                            No hay alertas reales en este momento.
                        @else
                            ✅ <strong>¡Excelente!</strong><br>
                            No hay alertas de mantenimiento pendientes.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <div class="footer">
            <p><strong>Sistema de Gestión de Vehículos PetroTekno</strong></p>
            <p>Este es un mensaje automático. Para más información, contacta al administrador del sistema.</p>
            <p style="font-size: 12px; margin-top: 15px; color: #999;">
                📧 Email generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>
