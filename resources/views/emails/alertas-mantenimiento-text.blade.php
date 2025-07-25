{{ $esTest ? '[PRUEBA] ' : '' }}SISTEMA DE ALERTAS DE MANTENIMIENTO - PETROTEKNO
================================================================

@if($esTest)
🧪 CORREO DE PRUEBA
Este es un correo de prueba para verificar que el sistema de alertas funciona correctamente.
No se requiere ninguna acción.

@endif
📊 RESUMEN DEL REPORTE
================================================================
Total de Alertas: {{ $resumen['total_alertas'] ?? 0 }}
Vehículos Afectados: {{ $resumen['vehiculos_afectados'] ?? 0 }}
Alertas Urgentes: {{ ($resumen['por_urgencia']['critica'] ?? 0) + ($resumen['por_urgencia']['alta'] ?? 0) }}
Requieren Acción: {{ count(array_unique(array_column($alertas, 'vehiculo_info'))) }}

@if(!empty($resumen['por_urgencia']))
⚡ DISTRIBUCIÓN POR URGENCIA
================================================================
@if(($resumen['por_urgencia']['critica'] ?? 0) > 0)
🔴 Críticas: {{ $resumen['por_urgencia']['critica'] }}
@endif
@if(($resumen['por_urgencia']['alta'] ?? 0) > 0)
🟡 Altas: {{ $resumen['por_urgencia']['alta'] }}
@endif
@if(($resumen['por_urgencia']['media'] ?? 0) > 0)
🟢 Medias: {{ $resumen['por_urgencia']['media'] }}
@endif

@endif
@if(!empty($alertas))
🚛 DETALLES DE VEHÍCULOS CON ALERTAS
================================================================

@foreach($alertas as $alerta)
----------------------------------------------------------------
🚛 VEHÍCULO: {{ $alerta['vehiculo_info']['marca'] ?? 'N/A' }} {{ $alerta['vehiculo_info']['modelo'] ?? 'N/A' }}@if(!empty($alerta['vehiculo_info']['placas'])) ({{ $alerta['vehiculo_info']['placas'] }})@endif

URGENCIA: {{ strtoupper($alerta['urgencia']) }}
Sistema: {{ $alerta['sistema'] }}
Kilometraje Actual: {{ number_format($alerta['kilometraje_actual']) }} km
Último Mantenimiento: {{ number_format($alerta['ultimo_mantenimiento']['kilometraje']) }} km
Exceso de Kilometraje: +{{ number_format($alerta['km_vencido_por']) }} km
@if(!empty($alerta['ultimo_mantenimiento']['fecha']) && $alerta['ultimo_mantenimiento']['fecha'] !== 'Nunca')
Fecha Último Servicio: {{ $alerta['ultimo_mantenimiento']['fecha'] }}
@endif

DESCRIPCIÓN:
El vehículo {{ $alerta['vehiculo_info']['nombre_completo'] }} requiere mantenimiento del sistema {{ $alerta['sistema'] }}. Ha excedido {{ number_format($alerta['km_vencido_por']) }} km del intervalo programado.

@endforeach
================================================================

⚡ ACCIÓN RECOMENDADA
Se recomienda programar los mantenimientos pendientes lo antes posible para evitar daños mayores.

Gestionar Mantenimientos: {{ $sistemaUrl }}/mantenimientos

@else
================================================================
@if($esTest)
🧪 CORREO DE PRUEBA ENVIADO EXITOSAMENTE
No hay alertas reales en este momento.
@else
✅ ¡EXCELENTE!
No hay alertas de mantenimiento pendientes.
@endif
================================================================

@endif

================================================================
SISTEMA DE CONTROL INTERNO - PETROTEKNO
Reporte generado automáticamente el {{ $fechaGeneracion }}

Este correo es generado automáticamente.
@if(!$esTest)
Para configurar las alertas, accede al panel de administración.
@endif

Soporte Técnico: soporte@110694.xyz
Sistema: {{ $sistemaUrl }}
================================================================
