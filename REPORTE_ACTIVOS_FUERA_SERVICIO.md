# Reporte de Activos Fuera de Servicio - Filtrado Múltiple

## ✅ Funcionalidad Implementada

Se ha modificado el **Reporte de Activos Fuera de Servicio** para incluir tres estados de vehículos en lugar de solo uno.

## 🎯 Estados Incluidos en el Reporte

El reporte ahora filtra y muestra vehículos en los siguientes estados:

1. **FUERA_DE_SERVICIO** - Vehículos temporalmente no operativos
2. **EN_MANTENIMIENTO** - Vehículos que están recibiendo mantenimiento
3. **ASIGNADO** - Vehículos asignados a obras activas

## 📊 Comportamiento

### Antes
- El reporte solo mostraba vehículos con estado `FUERA_DE_SERVICIO`

### Ahora
- El reporte muestra vehículos en **tres estados diferentes**
- Las estadísticas del reporte reflejan la suma de los tres estados
- Los formatos de exportación (Excel y PDF) incluyen todos los vehículos filtrados

## 🔧 Implementación Técnica

### Archivos Modificados

#### 1. `app/Http/Controllers/ReporteController.php`

**Método: `vehiculosFueraServicio()`**
```php
public function vehiculosFueraServicio(Request $request)
{
    // Filtrar por múltiples estados: fuera de servicio, en mantenimiento y asignado
    $estadosFiltro = [
        EstadoVehiculo::FUERA_DE_SERVICIO->value,
        EstadoVehiculo::EN_MANTENIMIENTO->value,
        EstadoVehiculo::ASIGNADO->value
    ];
    
    $request->merge(['estatus_multiple' => $estadosFiltro]);
    return $this->inventarioVehiculos($request);
}
```

**Método: `inventarioVehiculos()` - Sección de filtros**
```php
// Aplicar filtros
if ($estatusMultiple && is_array($estatusMultiple)) {
    // Filtrar por múltiples estados
    $query->whereIn('vehiculos.estatus', $estatusMultiple);
} elseif ($estatus) {
    // Filtrar por un solo estado
    $query->where('vehiculos.estatus', $estatus);
}
```

#### 2. `resources/views/reportes/index.blade.php`

**Antes:**
```html
<p class="text-sm text-gray-500 mt-1">
    Activos temporalmente no operativos por diversas causas
</p>
```

**Ahora:**
```html
<p class="text-sm text-gray-500 mt-1">
    Activos en estados: Fuera de Servicio, En Mantenimiento y Asignados
</p>
```

#### 3. `resources/views/reportes/index_nuevo.blade.php`

**Antes:**
```html
<p class="text-xs text-gray-500">No operativo</p>
```

**Ahora:**
```html
<p class="text-xs text-gray-500">Fuera de servicio, mantenimiento y asignados</p>
```

## 📈 Casos de Uso

### Caso 1: Vehículos Fuera de Servicio
```
Estado: FUERA_DE_SERVICIO
Motivo: Daño mecánico, esperando refacciones
Incluido en reporte: ✅ SÍ
```

### Caso 2: Vehículos en Mantenimiento
```
Estado: EN_MANTENIMIENTO
Motivo: Mantenimiento preventivo activo (sin fecha_fin)
Incluido en reporte: ✅ SÍ
```

### Caso 3: Vehículos Asignados
```
Estado: ASIGNADO
Motivo: Asignado a obra activa
Incluido en reporte: ✅ SÍ
```

### Caso 4: Vehículos Disponibles
```
Estado: DISPONIBLE
Motivo: No está en ningún estado especial
Incluido en reporte: ❌ NO
```

### Caso 5: Vehículos de Baja
```
Estado: BAJA, BAJA_POR_VENTA, BAJA_POR_PERDIDA
Motivo: Vehículo dado de baja del inventario
Incluido en reporte: ❌ NO (tienen su propio reporte)
```

## 🔍 Lógica del Filtro

### Query SQL Generado

```sql
SELECT * FROM vehiculos 
WHERE estatus IN ('fuera_de_servicio', 'en_mantenimiento', 'asignado')
ORDER BY marca, modelo, anio
```

### Compatibilidad

El cambio es **compatible con versiones anteriores**:
- Si se usa `estatus` (filtro simple) → funciona como antes
- Si se usa `estatus_multiple` (filtro múltiple) → usa la nueva funcionalidad
- Los otros reportes (disponibles, baja, etc.) no se ven afectados

## 📊 Estadísticas del Reporte

El reporte incluye estadísticas detalladas:

```php
$estadisticas = [
    'total_vehiculos' => X,                    // Total de vehículos filtrados
    'vehiculos_fuera_servicio' => Y,          // Cuántos están fuera de servicio
    'vehiculos_mantenimiento' => Z,           // Cuántos están en mantenimiento
    'vehiculos_asignados' => W,               // Cuántos están asignados
    'kilometraje_total' => XXX,               // Suma de todos los kilometrajes
    'kilometraje_promedio' => YYY,            // Promedio de kilometrajes
    // ... más estadísticas
];
```

## 🚀 Cómo Usar el Reporte

### Desde la Interfaz Web

1. Ir a **Reportes** en el menú principal
2. Buscar la sección **"Activos Fuera de Servicio"**
3. Seleccionar el formato deseado:
   - **Ver**: Visualización en pantalla (HTML)
   - **Descargar Excel**: Exportar a Excel (.xlsx)
   - **Descargar PDF**: Exportar a PDF

### Ruta HTTP

```
GET /reportes/vehiculos-fuera-servicio?formato={html|excel|pdf}
```

### Ejemplo de Llamada

```bash
# Ver en navegador
https://tu-dominio.com/reportes/vehiculos-fuera-servicio

# Descargar PDF
https://tu-dominio.com/reportes/vehiculos-fuera-servicio?formato=pdf

# Descargar Excel
https://tu-dominio.com/reportes/vehiculos-fuera-servicio?formato=excel
```

## 📝 Notas Importantes

1. **Interpretación del Reporte**
   - El título sigue siendo "Activos Fuera de Servicio" por convención
   - El contenido incluye **tres tipos de estados**
   - La descripción en la interfaz indica claramente los estados incluidos

2. **Estadísticas**
   - Las estadísticas se calculan sobre **todos** los vehículos filtrados
   - Se desglosan por estado individual para mayor claridad

3. **Exportaciones**
   - Tanto Excel como PDF incluyen todos los vehículos de los tres estados
   - Las columnas y formatos permanecen iguales

4. **Rendimiento**
   - El uso de `whereIn()` con tres valores es eficiente
   - El índice de la columna `estatus` optimiza la consulta

## ⚙️ Configuración

No requiere configuración adicional. Los cambios son automáticos al actualizar el código.

## 🧪 Testing

Para verificar el funcionamiento:

```bash
# 1. Acceder al reporte desde la interfaz
# 2. Verificar que se muestren vehículos en los 3 estados
# 3. Descargar PDF y verificar contenido
# 4. Descargar Excel y verificar contenido
```

### Verificación Manual

```sql
-- Consulta SQL para verificar los vehículos que deben aparecer
SELECT 
    id,
    marca,
    modelo,
    numero_economico,
    estatus
FROM vehiculos
WHERE estatus IN ('fuera_de_servicio', 'en_mantenimiento', 'asignado')
ORDER BY marca, modelo, anio;
```

## 🎨 Interfaz de Usuario

### Vista de Reportes - Descripción Actualizada

**Antes:**
> "Activos temporalmente no operativos por diversas causas"

**Ahora:**
> "Activos en estados: Fuera de Servicio, En Mantenimiento y Asignados"

Esta descripción es más clara y específica sobre qué vehículos incluye el reporte.

## 📌 Resumen de Cambios

| Aspecto | Antes | Ahora |
|---------|-------|-------|
| Estados filtrados | 1 (FUERA_DE_SERVICIO) | 3 (FUERA_DE_SERVICIO, EN_MANTENIMIENTO, ASIGNADO) |
| Método de filtro | `where('estatus', $value)` | `whereIn('estatus', $array)` |
| Descripción UI | "No operativo" | "Fuera de servicio, mantenimiento y asignados" |
| Compatibilidad | - | Retrocompatible con filtro simple |

## 🔗 Reportes Relacionados

- **Inventario Completo**: Muestra todos los vehículos sin filtros
- **Activos Disponibles**: Solo vehículos en estado DISPONIBLE
- **Activos Dados de Baja**: Estados BAJA, BAJA_POR_VENTA, BAJA_POR_PERDIDA

---

**Fecha de Implementación**: 2025-10-18  
**Desarrollador**: GitHub Copilot  
**Estado**: ✅ Implementado y Documentado
