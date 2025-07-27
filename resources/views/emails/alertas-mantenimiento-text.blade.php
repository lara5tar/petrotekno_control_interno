{{ $esTest ? '[PRUEBA] ' : '' }}SISTEMA DE ALERTAS DE MANTENIMIENTO - PETROTEKNO
================================================================

@if($esTest)
🧪 CORREO DE PRUEBA
Este es un correo de prueba para verificar que el sistema de alertas funciona correctamente.
No se requiere ninguna acción.

@endif
@if(!empty($alertas))
� ALERTAS DE MANTENIMIENTO DETECTADAS
================================================================

@foreach($alertas as $alerta)
----------------------------------------------------------------
🚛 VEHÍCULO: {{ $alerta['vehiculo_info']['nombre_completo'] }}
URGENCIA: {{ strtoupper($alerta['urgencia']) }}

Sistema: {{ $alerta['sistema_mantenimiento']['nombre_sistema'] }}
Tipo de Mantenimiento: {{ $alerta['sistema_mantenimiento']['tipo_mantenimiento'] }}
Descripción: {{ $alerta['sistema_mantenimiento']['descripcion_sistema'] }}

KILOMETRAJE:
- Actual: {{ $alerta['vehiculo_info']['kilometraje_actual'] }}
- Intervalo: {{ $alerta['sistema_mantenimiento']['intervalo_km'] }}
- Exceso: {{ number_format($alerta['intervalo_alcanzado']['km_exceso']) }} km
- Sobrepaso: {{ $alerta['intervalo_alcanzado']['porcentaje_sobrepaso'] }}

@if($alerta['historial_mantenimientos']['cantidad_encontrada'] > 0)
HISTORIAL DE MANTENIMIENTOS ({{ $alerta['sistema_mantenimiento']['nombre_sistema'] }}):
@foreach($alerta['historial_mantenimientos']['mantenimientos'] as $index => $mantenimiento)
  {{ $index === 0 ? '🏷️ Último:' : '📅 Anterior:' }} {{ $mantenimiento['fecha'] }} - {{ number_format($mantenimiento['kilometraje']) }} km
  Tipo: {{ ucfirst(strtolower($mantenimiento['tipo_servicio'])) }}
  Descripción: {{ $mantenimiento['descripcion'] }}
@if($mantenimiento['proveedor'] !== 'No especificado')
  Proveedor: {{ $mantenimiento['proveedor'] }}
@endif
@if($mantenimiento['costo'] !== 'No especificado')
  Costo: {{ $mantenimiento['costo'] }}
@endif

@endforeach
@else
📝 No se encontraron mantenimientos previos para este sistema.
Este será el primer mantenimiento registrado del sistema {{ strtolower($alerta['sistema_mantenimiento']['nombre_sistema']) }}.

@endif
RESUMEN:
{{ $alerta['mensaje_resumen'] }}
Detectado el: {{ $alerta['fecha_deteccion'] }}

@endforeach
================================================================

⚡ ACCIÓN RECOMENDADA
Se recomienda programar los mantenimientos pendientes lo antes posible para evitar daños mayores.

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
Reporte generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}

Este correo es generado automáticamente.
Para más información, contacta al administrador del sistema.

Sistema: PetroTekno Control Interno
================================================================
