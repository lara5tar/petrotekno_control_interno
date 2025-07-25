# 🚨 **SISTEMA DE ALERTAS DE MANTENIMIENTO AUTOMATIZADO**

## 📋 **RESUMEN**

Se ha implementado un sistema completo y automatizado de alertas de mantenimiento que:

- ✅ **Detecta automáticamente** cuando los vehículos requieren mantenimiento (motor, transmisión, hidráulico)
- ✅ **Actualiza automáticamente** el kilometraje de vehículos al registrar mantenimien---

## 📧 **SISTEMA DE EMAILS IMPLEMENTADO Y VALIDADO**

### **Estado del Sistema de Correos**
- ✅ **Mailable configurado** con headers anti-spam
- ✅ **Job asíncrono** para envío de alertas
- ✅ **Plantillas HTML y texto** profesionales
- ✅ **Comando de prueba** funcional
- ✅ **API endpoint** para testing
- ✅ **Anti-spam configurado** correctamente

### **Documentación Completa**
Ver: `docs/SISTEMA_ALERTAS_EMAIL_VALIDATION_REPORT.md`

---

**¡El sistema está completamente listo para producción! 🎉**

*Sistema de alertas backend + emails + APIs = 100% funcional para integración frontend.*

```  
- ✅ **Envía alertas por email** de forma inteligente evitando spam
- ✅ **Es completamente configurable** desde el frontend
- ✅ **Genera reportes en PDF** con detalles de las alertas
- ✅ **Funciona de manera reactiva** a cambios en kilometrajes y mantenimientos

---

## 🎯 **NUEVA FUNCIONALIDAD EN MANTENIMIENTOS**

### **Campo Agregado: `sistema_vehiculo`**

```json
{
  "sistema_vehiculo": "motor|transmision|hidraulico|general"
}
```

**Validaciones:**
- ✅ Campo **obligatorio** en crear/editar mantenimientos
- ✅ Solo acepta valores: `motor`, `transmision`, `hidraulico`, `general`
- ✅ Valida que el kilometraje sea coherente con mantenimientos previos del mismo sistema

### **Comportamiento Automático:**
- 🔄 **Al crear/editar mantenimiento:** Si `kilometraje_servicio > kilometraje_actual` del vehículo → se actualiza automáticamente el kilometraje del vehículo
- 🔄 **Al eliminar mantenimiento:** Se recalcula el kilometraje basado en registros restantes
- 🔄 **Recálculo de alertas:** Se ejecuta automáticamente en background

---

## 🌐 **NUEVOS ENDPOINTS API**

### **📊 Configuración de Alertas**

#### **1. Obtener configuraciones**
```http
GET /api/configuracion-alertas
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "general": {
      "alerta_inmediata": {"valor": true, "descripcion": "..."},
      "recordatorios_activos": {"valor": true, "descripcion": "..."},
      "cooldown_horas": {"valor": 4, "descripcion": "..."}
    },
    "horarios": {
      "hora_envio_diario": {"valor": "08:00", "descripcion": "..."},
      "dias_semana": {"valor": ["lunes","martes",...], "descripcion": "..."}
    },
    "destinatarios": {
      "emails_principales": {"valor": ["email@example.com"], "descripcion": "..."},
      "emails_copia": {"valor": [], "descripcion": "..."}
    }
  }
}
```

#### **2. Actualizar configuración general**
```http
PUT /api/configuracion-alertas/general
Authorization: Bearer {token}
Content-Type: application/json

{
  "alerta_inmediata": true,
  "recordatorios_activos": true, 
  "cooldown_horas": 4
}
```

#### **3. Actualizar horarios**
```http
PUT /api/configuracion-alertas/horarios
Authorization: Bearer {token}
Content-Type: application/json

{
  "hora_envio_diario": "08:00",
  "dias_semana": ["lunes", "martes", "miercoles", "jueves", "viernes"]
}
```

#### **4. Actualizar destinatarios**
```http
PUT /api/configuracion-alertas/destinatarios
Authorization: Bearer {token}
Content-Type: application/json

{
  "emails_principales": ["admin@empresa.com", "mantenimiento@empresa.com"],
  "emails_copia": ["supervisor@empresa.com"]
}
```

#### **5. Obtener resumen de alertas actuales**
```http
GET /api/configuracion-alertas/resumen-alertas
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "resumen": {
      "total_alertas": 5,
      "vehiculos_afectados": 3,
      "por_urgencia": {"critica": 1, "alta": 2, "normal": 2},
      "por_sistema": {"Motor": 3, "Transmision": 1, "Hidraulico": 1}
    },
    "alertas": [
      {
        "vehiculo_id": 1,
        "sistema": "Motor",
        "vehiculo_info": {
          "marca": "Toyota",
          "modelo": "Hilux", 
          "placas": "ABC-123",
          "nombre_completo": "Toyota Hilux - ABC-123"
        },
        "kilometraje_actual": 25000,
        "intervalo_configurado": 10000,
        "ultimo_mantenimiento": {
          "fecha": "15/06/2025",
          "kilometraje": 15000,
          "descripcion": "Cambio de aceite"
        },
        "proximo_mantenimiento_km": 25000,
        "km_vencido_por": 0,
        "urgencia": "normal|alta|critica",
        "porcentaje_sobrepaso": 0.0,
        "fecha_deteccion": "23/07/2025 15:30:45"
      }
    ]
  }
}
```

#### **6. Probar envío de alertas (simulación)**
```http
POST /api/configuracion-alertas/probar-envio
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Simulación de envío completada",
  "data": {
    "alertas_count": 5,
    "vehiculos_afectados": 3,
    "emails_destino": {
      "to": ["admin@empresa.com"],
      "cc": []
    },
    "alertas_preview": [...]
  }
}
```

---

## 🔐 **PERMISOS REQUERIDOS**

Para usar estos endpoints, el usuario debe tener los siguientes permisos:

- `ver_configuraciones` - Ver configuraciones de alertas
- `editar_configuraciones` - Modificar configuraciones
- `ver_alertas_mantenimiento` - Ver resumen de alertas
- `gestionar_alertas_mantenimiento` - Probar envío de alertas

---

## 🎨 **COMPONENTES FRONTEND SUGERIDOS**

### **1. Página de Configuración de Alertas**

**Secciones:**
- 📧 **Destinatarios**: Configurar emails principales y copia
- ⏰ **Horarios**: Hora de envío y días activos  
- ⚙️ **General**: Activar/desactivar alertas inmediatas y recordatorios
- 📊 **Vista Previa**: Mostrar alertas actuales y probar envío

### **2. Dashboard de Alertas**

**Widgets sugeridos:**
- 🚨 **Contador de alertas críticas**
- 📈 **Gráfico por urgencia** (crítica, alta, normal)
- 🚛 **Lista de vehículos con alertas**
- 🔧 **Alertas por sistema** (motor, transmisión, hidráulico)

### **3. Formulario de Mantenimiento Actualizado**

**Campo nuevo:**
```html
<select name="sistema_vehiculo" required>
  <option value="">Seleccionar sistema</option>
  <option value="motor">Motor</option>
  <option value="transmision">Transmisión</option>
  <option value="hidraulico">Hidráulico</option>
  <option value="general">General</option>
</select>
```

**Información adicional:**
- ⚠️ Mostrar advertencia si el kilometraje actualizará el del vehículo
- 📊 Mostrar último mantenimiento del mismo sistema
- ✅ Indicar que se recalcularán alertas automáticamente

---

## 🔄 **FLUJO DE TRABAJO AUTOMÁTICO**

### **Cuando se registra un mantenimiento:**

1. 👤 **Usuario envía formulario** con nuevo mantenimiento
2. ✅ **Validaciones** verifican datos y consistencia
3. 💾 **Se guarda** el mantenimiento en BD
4. 🔄 **Observer detecta** creación automáticamente
5. 📊 **Se actualiza kilometraje** del vehículo (si es mayor)
6. ⚡ **Job en background** recalcula alertas del vehículo
7. 📧 **Si hay alertas nuevas** y están habilitadas → envía email inmediato
8. 📝 **Se registra** en logs de auditoría

### **Envío diario programado:**

1. 🕗 **Todos los días a las 08:00** (configurable)
2. 🔍 **Se verifican** todos los vehículos activos
3. 📊 **Se generan alertas** para todos los sistemas
4. 📄 **Se crea reporte PDF** con detalles
5. 📧 **Se envía email** con PDF adjunto a destinatarios configurados
6. 📝 **Se registra** actividad en logs

---

## 🛠️ **COMANDOS ARTISAN DISPONIBLES**

### **Envío manual de alertas:**
```bash
# Envío normal (respeta configuración)
php artisan alertas:enviar-diarias

# Forzar envío (ignora días/horarios configurados)  
php artisan alertas:enviar-diarias --force

# Simulación (no envía emails reales)
php artisan alertas:enviar-diarias --dry-run

# Forzar + simulación
php artisan alertas:enviar-diarias --force --dry-run
```

---

## 📧 **FORMATO DE EMAILS**

### **Email de Alerta Inmediata:**
```
🚨 ALERTA: Mantenimiento Requerido

🚛 Vehículo: Toyota Hilux - ABC-123
📊 Kilometraje actual: 25,000 km
⚠️ Sistemas que requieren mantenimiento:

🔧 MOTOR
• Último mantenimiento: 15,000 km (hace 10,000 km)
• Intervalo configurado: 10,000 km  
• Estado: VENCIDO por 0 km

📅 Fecha de detección: 23/07/2025 15:30
```

### **Email de Reporte Diario:**
```
📊 REPORTE DIARIO - Alertas de Mantenimiento

📈 Resumen:
• Total de alertas: 8
• Vehículos afectados: 5
• Alertas críticas: 2
• Alertas altas: 3  
• Alertas normales: 3

📎 Ver reporte detallado en PDF adjunto

🔧 Próximas acciones recomendadas:
• Revisar vehículos con alertas críticas
• Programar mantenimientos preventivos
• Actualizar intervalos si es necesario
```

---

## 🚀 **IMPLEMENTACIÓN RECOMENDADA**

### **Fase 1: Configuración Básica (1-2 días)**
1. Crear página de configuración de alertas
2. Implementar formularios para emails y horarios
3. Conectar con API endpoints de configuración

### **Fase 2: Dashboard de Alertas (2-3 días)**  
1. Crear dashboard con widgets de alertas
2. Implementar vista de resumen con gráficos
3. Agregar funcionalidad de "probar envío"

### **Fase 3: Integración con Mantenimientos (1 día)**
1. Actualizar formulario de mantenimientos
2. Agregar campo `sistema_vehiculo`
3. Mostrar información adicional sobre alertas

### **Fase 4: Mejoras y Optimización (opcional)**
1. Notificaciones en tiempo real (WebSockets)
2. Filtros avanzados en dashboard
3. Exportar alertas a Excel/CSV

---

## ⚠️ **CONSIDERACIONES IMPORTANTES**

1. **Permisos**: Verificar que los usuarios tengan los permisos adecuados
2. **Validación**: El campo `sistema_vehiculo` es obligatorio en mantenimientos nuevos
3. **Migración**: Los mantenimientos existentes se marcaron como `general` por defecto
4. **Performance**: Las alertas se calculan en background para no afectar la UI
5. **Configuración**: Por defecto está configurado para `ebravotube@gmail.com`

---

## 🆘 **SOPORTE Y DEBUGGING**

### **Logs importantes:**
- `storage/logs/laravel.log` - Errores generales
- Log de acciones en tabla `log_acciones` - Alertas enviadas

### **Commands útiles para debugging:**
```bash
# Ver configuración actual
php artisan tinker
>>> App\Services\ConfiguracionAlertasService::obtenerTodas()

# Verificar alertas de un vehículo específico  
>>> App\Services\AlertasMantenimientoService::verificarVehiculo(1)

# Ver todas las alertas actuales
>>> App\Services\AlertasMantenimientoService::verificarTodosLosVehiculos()
```

---

**¡El sistema está listo para integración frontend! 🎉**

*Cualquier duda o ajuste necesario, el código está bien documentado y es fácilmente extensible.*
