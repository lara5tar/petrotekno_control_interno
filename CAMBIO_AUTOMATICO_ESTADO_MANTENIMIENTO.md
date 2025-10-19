# Cambio Automático de Estado por Mantenimiento

## ✅ Funcionalidad Implementada

Se ha implementado el cambio automático del estado de los vehículos basándose en sus mantenimientos activos (sin `fecha_fin`).

## 🎯 Comportamiento

### Estado EN_MANTENIMIENTO
Un vehículo cambiará automáticamente a estado `EN_MANTENIMIENTO` cuando:
- Se crea un nuevo mantenimiento **sin** `fecha_fin` (mantenimiento activo)
- El vehículo no está en estado de baja (BAJA, BAJA_POR_VENTA, BAJA_POR_PERDIDA)

### Estado DISPONIBLE o ASIGNADO
Un vehículo cambiará automáticamente cuando:
- Se completa un mantenimiento (se establece `fecha_fin`)
- No quedan otros mantenimientos activos
- Resultado:
  - Si tiene **obras activas** → estado `ASIGNADO`
  - Si **no** tiene obras activas → estado `DISPONIBLE`

### Protección de Estados de Baja
Los vehículos en los siguientes estados **NO** cambiarán automáticamente:
- `BAJA`
- `BAJA_POR_VENTA`
- `BAJA_POR_PERDIDA`

## 🔧 Implementación Técnica

### Archivo Modificado
- **`app/Observers/MantenimientoObserver.php`**

### Métodos del Observer

#### 1. `created(Mantenimiento $mantenimiento)`
Cuando se crea un mantenimiento:
- Actualiza el kilometraje del vehículo
- **Llama a `actualizarEstadoVehiculoPorMantenimiento()`**
- Recalcula alertas

#### 2. `updated(Mantenimiento $mantenimiento)`
Cuando se actualiza un mantenimiento:
- Si cambió el kilometraje, actualiza el vehículo
- **Si cambió `fecha_fin`, llama a `actualizarEstadoVehiculoPorMantenimiento()`**
- Si cambió el sistema, recalcula alertas

#### 3. `deleted(Mantenimiento $mantenimiento)`
Cuando se elimina un mantenimiento:
- Recalcula el kilometraje del vehículo
- **Llama a `actualizarEstadoVehiculoPorMantenimiento()`**
- Recalcula alertas

#### 4. `restored(Mantenimiento $mantenimiento)`
Cuando se restaura un mantenimiento eliminado:
- Actualiza el kilometraje
- **Llama a `actualizarEstadoVehiculoPorMantenimiento()`**
- Recalcula alertas

### Método Principal: `actualizarEstadoVehiculoPorMantenimiento()`

```php
private function actualizarEstadoVehiculoPorMantenimiento(Mantenimiento $mantenimiento, string $action): void
```

#### Lógica:
1. Obtiene el vehículo asociado al mantenimiento
2. **Protección**: Si el vehículo está en estado de baja, no hace cambios
3. Cuenta mantenimientos activos (sin `fecha_fin`):
   - Si `$action === 'deleted'`, excluye el mantenimiento actual del conteo
4. Determina el nuevo estado:
   - **Si hay mantenimientos activos** → `EN_MANTENIMIENTO`
   - **Si NO hay mantenimientos activos**:
     - Verifica si tiene asignaciones de obra activas
     - **Con obras activas** → `ASIGNADO`
     - **Sin obras activas** → `DISPONIBLE`
5. Solo actualiza si el estado cambió
6. Registra todo en los logs

## 📊 Ejemplos de Uso

### Caso 1: Mantenimiento Simple
```
Vehículo: DISPONIBLE
  ↓ (crear mantenimiento sin fecha_fin)
Vehículo: EN_MANTENIMIENTO
  ↓ (completar mantenimiento, establecer fecha_fin)
Vehículo: DISPONIBLE
```

### Caso 2: Múltiples Mantenimientos
```
Vehículo: DISPONIBLE
  ↓ (crear mantenimiento #1)
Vehículo: EN_MANTENIMIENTO
  ↓ (crear mantenimiento #2)
Vehículo: EN_MANTENIMIENTO (sin cambios)
  ↓ (completar mantenimiento #1)
Vehículo: EN_MANTENIMIENTO (aún hay #2 activo)
  ↓ (completar mantenimiento #2)
Vehículo: DISPONIBLE (ya no hay activos)
```

### Caso 3: Vehículo Asignado a Obra
```
Vehículo: ASIGNADO (en obra activa)
  ↓ (crear mantenimiento)
Vehículo: EN_MANTENIMIENTO
  ↓ (completar mantenimiento)
Vehículo: ASIGNADO (vuelve porque tiene obra activa)
```

### Caso 4: Vehículo de Baja
```
Vehículo: BAJA_POR_VENTA
  ↓ (crear mantenimiento)
Vehículo: BAJA_POR_VENTA (sin cambios - protegido)
  ↓ (completar mantenimiento)
Vehículo: BAJA_POR_VENTA (sin cambios - protegido)
```

## 🧪 Tests Realizados

### ✅ Test 1: Vehículo Disponible
- Crear mantenimiento → Cambia a EN_MANTENIMIENTO ✅
- Completar mantenimiento → Vuelve a DISPONIBLE ✅

### ✅ Test 2: Múltiples Mantenimientos
- Crear dos mantenimientos → EN_MANTENIMIENTO ✅
- Completar el primero → Sigue EN_MANTENIMIENTO ✅
- Completar el segundo → DISPONIBLE ✅

### ✅ Test 3: Vehículo en Estado de Baja
- Crear mantenimiento en vehículo BAJA → No cambia de estado ✅

## 📝 Logs

El sistema registra cada cambio de estado con información detallada:

```
MantenimientoObserver: Estado del vehículo actualizado {
    "action": "created",
    "mantenimiento_id": 123,
    "vehiculo_id": 45,
    "estado_anterior": "disponible",
    "estado_nuevo": "en_mantenimiento",
    "mantenimientos_activos": 1,
    "fecha_fin_mantenimiento": null
}
```

## 🔍 Relaciones Utilizadas

- `Vehiculo::mantenimientos()` - HasMany
- `Vehiculo::asignacionesObra()` - HasMany
- `AsignacionObra::ESTADO_ACTIVA` - Constante para identificar obras activas

## ⚙️ Configuración

No requiere configuración adicional. El observer se activa automáticamente gracias al registro en `app/Providers/EventServiceProvider.php` o auto-discovery de Laravel.

## 🚀 Beneficios

1. **Automatización**: No es necesario cambiar manualmente el estado del vehículo
2. **Consistencia**: El estado siempre refleja la realidad operativa
3. **Auditoría**: Todos los cambios quedan registrados en logs
4. **Integridad**: Protección de estados de baja
5. **Precisión**: Considera obras activas al determinar DISPONIBLE vs ASIGNADO

## 📌 Notas Importantes

- El cambio de estado ocurre **automáticamente** en el Observer
- No requiere modificaciones en controladores
- Los estados de baja están **protegidos** contra cambios automáticos
- El sistema verifica **todas** las obras activas, no solo la cantidad
- Los logs se encuentran en `storage/logs/laravel.log`

---

**Fecha de Implementación**: 2025-10-19  
**Desarrollador**: GitHub Copilot  
**Estado**: ✅ Implementado y Probado
