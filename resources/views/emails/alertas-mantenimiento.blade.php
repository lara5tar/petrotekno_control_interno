<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $esTest ? '[TEST] ' : '' }}Alertas de Mantenimiento - Petrotekno</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
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
        .resumen {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #3b82f6;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .stat {
            text-align: center;
        }
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
            display: block;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .alerta {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .alerta-header {
            padding: 15px 20px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .alerta-critica { background-color: #fef2f2; border-left: 4px solid #ef4444; }
        .alerta-critica .alerta-header { color: #dc2626; }
        .alerta-alta { background-color: #fef3c7; border-left: 4px solid #f59e0b; }
        .alerta-alta .alerta-header { color: #d97706; }
        .alerta-media { background-color: #ecfdf5; border-left: 4px solid #10b981; }
        .alerta-media .alerta-header { color: #059669; }
        .urgencia-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .urgencia-critica { background-color: #dc2626; color: white; }
        .urgencia-alta { background-color: #f59e0b; color: white; }
        .urgencia-media { background-color: #10b981; color: white; }
        .alerta-body {
            padding: 15px 20px;
            background-color: white;
        }
        .alerta-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .alerta-detail strong {
            color: #374151;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #6b7280;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 15px;
        }
        .btn:hover {
            background-color: #2563eb;
        }
        @media (max-width: 600px) {
            .container { margin: 10px; border-radius: 0; }
            .content { padding: 20px; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($esTest)
                <div class="test-badge">🧪 CORREO DE PRUEBA</div>
            @endif
            <h1>
                {{ $esTest ? '🧪 [TEST] ' : '🚨 ' }}Alertas de Mantenimiento
            </h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">
                Sistema de Control Interno - Petrotekno
            </p>
        </div>

        <!-- Contenido -->
        <div class="content">
            @if($esTest)
                <div style="background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <p style="margin: 0; color: #92400e; font-weight: 500;">
                        🧪 <strong>Este es un correo de prueba</strong> para verificar que el sistema de alertas funciona correctamente.
                        No se requiere ninguna acción.
                    </p>
                </div>
            @endif

            <!-- Resumen -->
            <div class="resumen">
                <h3 style="margin: 0 0 15px 0; color: #1f2937;">📊 Resumen del Reporte</h3>
                <div class="stat-grid">
                    <div class="stat">
                        <span class="stat-number">{{ $resumen['total_alertas'] ?? 0 }}</span>
                        <span class="stat-label">Total Alertas</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">{{ $resumen['vehiculos_afectados'] ?? 0 }}</span>
                        <span class="stat-label">Vehículos</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">{{ ($resumen['por_urgencia']['critica'] ?? 0) + ($resumen['por_urgencia']['alta'] ?? 0) }}</span>
                        <span class="stat-label">Urgentes</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">{{ count(array_unique(array_column($alertas, 'vehiculo_info'))) }}</span>
                        <span class="stat-label">Requieren Acción</span>
                    </div>
                </div>
            </div>

            <!-- Alertas por Urgencia -->
            @if(!empty($resumen['por_urgencia']))
                <h3 style="color: #1f2937; margin-bottom: 15px;">⚡ Distribución por Urgencia</h3>
                <div style="display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap;">
                    @if(($resumen['por_urgencia']['critica'] ?? 0) > 0)
                        <div style="background: #fee2e2; color: #dc2626; padding: 8px 12px; border-radius: 6px; font-weight: 500; font-size: 13px;">
                            🔴 Críticas: {{ $resumen['por_urgencia']['critica'] }}
                        </div>
                    @endif
                    @if(($resumen['por_urgencia']['alta'] ?? 0) > 0)
                        <div style="background: #fef3c7; color: #d97706; padding: 8px 12px; border-radius: 6px; font-weight: 500; font-size: 13px;">
                            🟡 Altas: {{ $resumen['por_urgencia']['alta'] }}
                        </div>
                    @endif
                    @if(($resumen['por_urgencia']['media'] ?? 0) > 0)
                        <div style="background: #ecfdf5; color: #059669; padding: 8px 12px; border-radius: 6px; font-weight: 500; font-size: 13px;">
                            🟢 Medias: {{ $resumen['por_urgencia']['media'] }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Lista de Alertas -->
            @if(!empty($alertas))
                <h3 style="color: #1f2937; margin-bottom: 20px;">🚛 Detalles de Vehículos con Alertas</h3>
                
                @foreach($alertas as $alerta)
                    <div class="alerta alerta-{{ $alerta['urgencia'] }}">
                        <div class="alerta-header">
                            <span>
                                🚛 {{ $alerta['vehiculo_info']['marca'] ?? 'N/A' }} 
                                {{ $alerta['vehiculo_info']['modelo'] ?? 'N/A' }}
                                @if(!empty($alerta['vehiculo_info']['placas']))
                                    ({{ $alerta['vehiculo_info']['placas'] }})
                                @endif
                            </span>
                            <span class="urgencia-badge urgencia-{{ $alerta['urgencia'] }}">
                                {{ $alerta['urgencia'] }}
                            </span>
                        </div>
                        <div class="alerta-body">
                            <div class="alerta-detail">
                                <span><strong>Sistema:</strong></span>
                                <span>{{ $alerta['sistema'] }}</span>
                            </div>
                            <div class="alerta-detail">
                                <span><strong>Kilometraje Actual:</strong></span>
                                <span>{{ number_format($alerta['kilometraje_actual']) }} km</span>
                            </div>
                            <div class="alerta-detail">
                                <span><strong>Último Mantenimiento:</strong></span>
                                <span>{{ number_format($alerta['ultimo_mantenimiento']['kilometraje']) }} km</span>
                            </div>
                            <div class="alerta-detail">
                                <span><strong>Exceso de Kilometraje:</strong></span>
                                <span style="color: #dc2626; font-weight: 600;">+{{ number_format($alerta['km_vencido_por']) }} km</span>
                            </div>
                            @if(!empty($alerta['ultimo_mantenimiento']['fecha']) && $alerta['ultimo_mantenimiento']['fecha'] !== 'Nunca')
                                <div class="alerta-detail">
                                    <span><strong>Fecha Último Servicio:</strong></span>
                                    <span>{{ $alerta['ultimo_mantenimiento']['fecha'] }}</span>
                                </div>
                            @endif
                            <div class="alerta-detail">
                                <span><strong>Descripción:</strong></span>
                                <span style="font-style: italic;">
                                    El vehículo {{ $alerta['vehiculo_info']['nombre_completo'] }} 
                                    requiere mantenimiento del sistema {{ $alerta['sistema'] }}. 
                                    Ha excedido {{ number_format($alerta['km_vencido_por']) }} km del intervalo programado.
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 40px; color: #6b7280;">
                    <p style="font-size: 18px; margin: 0;">
                        @if($esTest)
                            🧪 <strong>Correo de prueba enviado exitosamente</strong><br>
                            No hay alertas reales en este momento.
                        @else
                            ✅ <strong>¡Excelente!</strong><br>
                            No hay alertas de mantenimiento pendientes.
                        @endif
                    </p>
                </div>
            @endif

            <!-- Acción Recomendada -->
            @if(!empty($alertas))
                <div style="background-color: #eff6ff; border: 1px solid #3b82f6; border-radius: 8px; padding: 20px; margin-top: 25px; text-align: center;">
                    <h4 style="margin: 0 0 10px 0; color: #1e40af;">⚡ Acción Recomendada</h4>
                    <p style="margin: 0 0 15px 0; color: #1e40af;">
                        Se recomienda programar los mantenimientos pendientes lo antes posible para evitar daños mayores.
                    </p>
                    <a href="{{ $sistemaUrl }}/mantenimientos" class="btn">
                        🔧 Gestionar Mantenimientos
                    </a>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Sistema de Control Interno - Petrotekno</strong></p>
            <p>Reporte generado automáticamente el {{ $fechaGeneracion }}</p>
            <p style="font-size: 11px; margin-top: 15px;">
                Este correo es generado automáticamente. 
                @if(!$esTest)
                    Para configurar las alertas, accede al panel de administración.
                @endif
            </p>
        </div>
    </div>
</body>
</html>
