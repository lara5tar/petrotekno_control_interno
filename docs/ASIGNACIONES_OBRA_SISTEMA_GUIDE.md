# Sistema de Asignaciones de Obra - Guía Completa

## Descripción General

El sistema de asignaciones de obra permite gestionar la asignación de vehículos y operadores a obras específicas. Este sistema integra completamente con el módulo de obras existente.

## Nuevas Funcionalidades Implementadas

### 1. Controlador AsignacionObraController

**Ubicación:** `app/Http/Controllers/AsignacionObraController.php`

#### Métodos Principales:
- `index()` - Lista todas las asignaciones con filtros
- `create()` - Formulario para crear nueva asignación
- `store()` - Guardar nueva asignación
- `show()` - Ver detalles de una asignación
- `liberar()` - Liberar una asignación activa
- `transferir()` - Transferir asignación a otro operador/vehículo
- `estadisticasGeneral()` - Estadísticas generales del sistema

### 2. Rutas del Sistema

```php
// Rutas principales
Route::get('asignaciones-obra', [AsignacionObraController::class, 'index'])->name('asignaciones-obra.index');
Route::get('asignaciones-obra/create', [AsignacionObraController::class, 'create'])->name('asignaciones-obra.create');
Route::post('asignaciones-obra', [AsignacionObraController::class, 'store'])->name('asignaciones-obra.store');
Route::get('asignaciones-obra/{id}', [AsignacionObraController::class, 'show'])->name('asignaciones-obra.show');

// Rutas de acciones
Route::post('asignaciones-obra/{id}/liberar', [AsignacionObraController::class, 'liberar'])->name('asignaciones-obra.liberar');
Route::post('asignaciones-obra/{id}/transferir', [AsignacionObraController::class, 'transferir'])->name('asignaciones-obra.transferir');

// Estadísticas
Route::get('asignaciones-obra/estadisticas/general', [AsignacionObraController::class, 'estadisticasGeneral'])->name('asignaciones-obra.estadisticas');
```

### 3. Vistas Implementadas

#### Index (`resources/views/asignaciones-obra/index.blade.php`)
- Lista todas las asignaciones con paginación
- Filtros por obra, vehículo, operador y estado
- Búsqueda general
- Estadísticas en tiempo real
- Acciones: Ver, Liberar, Transferir

#### Create (`resources/views/asignaciones-obra/create.blade.php`)
- Formulario para crear nueva asignación
- Validación frontend con Alpine.js
- Preselección automática de obra (cuando se viene desde una obra específica)
- Verificación de disponibilidad de vehículos y operadores

#### Show (`resources/views/asignaciones-obra/show.blade.php`)
- Detalles completos de la asignación
- Timeline de actividades
- Información del vehículo, obra y operador
- Acciones disponibles según el estado

#### Estadisticas (`resources/views/asignaciones-obra/estadisticas.blade.php`)
- Métricas del sistema
- Gráficos de rendimiento
- Top obras y operadores

### 4. Integración con Obras

#### Nuevos Botones en Vista Show de Obra
```blade
<!-- Botón para ver asignaciones de la obra -->
<a href="{{ route('asignaciones-obra.index') }}?obra_id={{ $obra->id }}">
    Asignaciones
</a>

<!-- Botón para crear nueva asignación para la obra -->
<a href="{{ route('asignaciones-obra.create') }}?obra_id={{ $obra->id }}">
    Nueva Asignación
</a>
```

#### Nuevo Botón en Vista Index de Obras
```blade
<!-- Acceso rápido a gestión general de asignaciones -->
<a href="{{ route('asignaciones-obra.index') }}">
    Gestionar Asignaciones
</a>
```

### 5. Modelos Actualizados

#### Vehiculo.php - Nuevos Scopes
```php
// Scope para vehículos activos
public function scopeActivos($query)
{
    return $query->where('estatus_id', 1);
}

// Scope para vehículos disponibles (activos y sin asignación)
public function scopeDisponibles($query)
{
    return $query->activos()
        ->whereDoesntHave('asignaciones', function ($q) {
            $q->whereNull('fecha_liberacion');
        });
}
```

#### Personal.php - Nuevos Scopes
```php
// Scope para personal activo
public function scopeActivos($query)
{
    return $query->where('estatus', 'activo');
}

// Scope para operadores (categoría específica)
public function scopeOperadores($query)
{
    return $query->whereHas('categoria', function ($q) {
        $q->where('nombre_categoria', 'like', '%operador%');
    });
}

// Scope para personal disponible
public function scopeDisponibles($query)
{
    return $query->activos()
        ->whereDoesntHave('asignacionesComoOperador', function ($q) {
            $q->whereNull('fecha_liberacion');
        });
}
```

## Flujo de Trabajo

### Crear Nueva Asignación

1. **Desde Index General:**
   - Ir a `obras` → Click en "Gestionar Asignaciones"
   - Click en "Nueva Asignación"

2. **Desde Obra Específica:**
   - Ir a `obras/{id}` → Click en "Nueva Asignación"
   - La obra se preselecciona automáticamente

3. **Validaciones Automáticas:**
   - Verifica vehículos disponibles
   - Verifica operadores disponibles
   - Valida kilometraje inicial

### Gestionar Asignaciones Existentes

1. **Filtrar por Obra:**
   - Usar parámetro `?obra_id={id}` en la URL
   - O usar el filtro en la interfaz

2. **Acciones Disponibles:**
   - **Ver:** Detalles completos de la asignación
   - **Liberar:** Terminar asignación activa
   - **Transferir:** Cambiar operador/vehículo

### Estadísticas

- Acceso desde el menú principal de asignaciones
- Métricas en tiempo real
- Reportes por período
- Top performers

## API Endpoints

Todos los endpoints soportan respuestas JSON mediante el header `Accept: application/json`

### Listar Asignaciones
```http
GET /asignaciones-obra
```

**Parámetros de Query:**
- `obra_id` - Filtrar por obra específica
- `vehiculo_id` - Filtrar por vehículo
- `estado` - Filtrar por estado (activa/liberada)
- `buscar` - Búsqueda general

**Respuesta:**
```json
{
    "data": [...],
    "pagination": {...},
    "estadisticas": {...},
    "filtros": {...}
}
```

### Crear Asignación
```http
POST /asignaciones-obra
```

**Payload:**
```json
{
    "obra_id": 1,
    "vehiculo_id": 2,
    "personal_id": 3,
    "kilometraje_inicial": 15000,
    "observaciones": "Opcional"
}
```

### Liberar Asignación
```http
POST /asignaciones-obra/{id}/liberar
```

**Payload:**
```json
{
    "kilometraje_final": 15500,
    "observaciones_liberacion": "Trabajo completado"
}
```

### Transferir Asignación
```http
POST /asignaciones-obra/{id}/transferir
```

**Payload:**
```json
{
    "nuevo_personal_id": 4,
    "nuevo_vehiculo_id": 5, // Opcional
    "observaciones": "Cambio de operador"
}
```

## Validaciones

### Frontend (Alpine.js)
- Verificación de campos requeridos
- Validación de kilometraje
- Estados de disponibilidad

### Backend (Laravel)
- Validación de existencia de registros
- Verificación de disponibilidad
- Validaciones de negocio

## Permisos Requeridos

- `ver_asignaciones` - Ver listado y detalles
- `crear_asignaciones` - Crear nuevas asignaciones
- `editar_asignaciones` - Liberar y transferir
- `eliminar_asignaciones` - Eliminar asignaciones

## Notas de Implementación

1. **Preselección de Obra:** Cuando se accede desde una obra específica, la obra se preselecciona automáticamente en el formulario.

2. **Filtros Inteligentes:** El sistema mantiene los filtros activos en la navegación entre páginas.

3. **Validación de Disponibilidad:** Solo se muestran vehículos y operadores disponibles para nuevas asignaciones.

4. **Manejo de Estados:** El sistema diferencia claramente entre asignaciones activas y liberadas.

5. **Integración Completa:** Navegación fluida entre obras y asignaciones.

## Archivos Modificados

### Nuevos Archivos:
- `app/Http/Controllers/AsignacionObraController.php`
- `resources/views/asignaciones-obra/index.blade.php`
- `resources/views/asignaciones-obra/create.blade.php`
- `resources/views/asignaciones-obra/show.blade.php`
- `resources/views/asignaciones-obra/estadisticas.blade.php`

### Archivos Modificados:
- `routes/web.php` - Nuevas rutas
- `app/Models/Vehiculo.php` - Nuevos scopes
- `app/Models/Personal.php` - Nuevos scopes
- `resources/views/obras/show.blade.php` - Botones de asignación
- `resources/views/obras/index.blade.php` - Acceso rápido

## Consideraciones de Performance

- Uso de `with()` para eager loading en consultas
- Paginación en todas las listas
- Índices en campos de filtrado
- Caché de estadísticas cuando sea necesario

## Testing

Se recomienda probar:
1. Creación de asignaciones desde obras específicas
2. Filtrado por diferentes criterios
3. Acciones de liberar y transferir
4. Validaciones de disponibilidad
5. Navegación entre módulos

## Próximas Mejoras

- Notificaciones automáticas
- Alertas de vencimiento
- Reportes PDF
- Dashboard de métricas
- Integración con calendario
