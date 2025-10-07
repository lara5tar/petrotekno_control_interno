# Componente Modal de Eliminación Reutilizable

## Descripción
Este componente Blade proporciona un modal de confirmación reutilizable para eliminar entidades en el sistema. Es completamente configurable y puede ser usado en cualquier vista que requiera confirmación de eliminación.

## Ubicación del Componente
- **Archivo**: `resources/views/components/delete-confirmation-modal.blade.php`

## Características
- ✅ **Reutilizable**: Se puede usar en múltiples vistas (vehículos, personal, mantenimientos, etc.)
- ✅ **Configurable**: Parámetros personalizables para diferentes tipos de entidades
- ✅ **Responsive**: Diseñado con Tailwind CSS
- ✅ **Funcionalidad AJAX**: Manejo de eliminación con feedback visual
- ✅ **Accesible**: Cierre con ESC, clic fuera del modal, botón cancelar

## Uso Básico

### 1. Incluir el Componente en tu Vista

```blade
<x-delete-confirmation-modal 
    id="modal-eliminar"
    entity="el vehículo"
    entityIdField="vehiculo-id"
    entityDisplayField="vehiculo-placas"
    routeName="vehiculos"
    additionalText="Esta acción no se puede deshacer."
/>
```

### 2. Configurar los Botones de Eliminación

Los botones de eliminación deben tener:
- Clase: `btn-eliminar`
- Atributos de datos correspondientes a `entityIdField` y `entityDisplayField`

```blade
<button 
    data-vehiculo-id="{{ $vehiculo->id }}" 
    data-vehiculo-placas="{{ $vehiculo->placas }}" 
    class="btn-eliminar text-red-600 hover:text-red-900" 
    title="Eliminar vehículo">
    <svg><!-- Icono --></svg>
</button>
```

### 3. Inicializar el JavaScript

En la sección `@push('scripts')` de tu vista:

```javascript
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.initDeleteModal === 'function') {
        window.initDeleteModal({
            modalId: 'modal-eliminar',
            entityIdField: 'vehiculo-id',
            entityDisplayField: 'vehiculo-placas',
            deleteButtonSelector: '.btn-eliminar',
            baseUrl: '{{ url("vehiculos") }}'
        });
    }
});
```

## Parámetros del Componente

| Parámetro | Tipo | Por Defecto | Descripción |
|-----------|------|-------------|-------------|
| `id` | string | `'modal-eliminar'` | ID único del modal |
| `entity` | string | `'elemento'` | Nombre de la entidad (ej: "el vehículo", "el empleado") |
| `entityIdField` | string | `'id'` | Campo que contiene el ID de la entidad |
| `entityDisplayField` | string | `'nombre'` | Campo que contiene el texto descriptivo |
| `routeName` | string | `''` | Nombre base de la ruta (ej: "vehiculos") |
| `additionalText` | string | `'Esta acción no se puede deshacer.'` | Texto adicional en el modal |

## Parámetros de Configuración JavaScript

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `modalId` | string | ID del modal (debe coincidir con el parámetro `id` del componente) |
| `entityIdField` | string | Campo del ID de la entidad (debe coincidir con `data-*` del botón) |
| `entityDisplayField` | string | Campo del texto descriptivo (debe coincidir con `data-*` del botón) |
| `deleteButtonSelector` | string | Selector CSS para los botones de eliminar |
| `baseUrl` | string | URL base para las peticiones de eliminación |

## Ejemplos de Uso

### Para Vehículos
```blade
<x-delete-confirmation-modal 
    id="modal-eliminar-vehiculo"
    entity="el vehículo"
    entityIdField="vehiculo-id"
    entityDisplayField="vehiculo-placas"
    additionalText="Esta acción eliminará el vehículo permanentemente."
/>

<!-- Botón -->
<button 
    data-vehiculo-id="{{ $vehiculo->id }}" 
    data-vehiculo-placas="{{ $vehiculo->placas }}" 
    class="btn-eliminar">
    Eliminar
</button>

<!-- JavaScript -->
<script>
window.initDeleteModal({
    modalId: 'modal-eliminar-vehiculo',
    entityIdField: 'vehiculo-id',
    entityDisplayField: 'vehiculo-placas',
    deleteButtonSelector: '.btn-eliminar',
    baseUrl: '{{ url("vehiculos") }}'
});
</script>
```

### Para Personal
```blade
<x-delete-confirmation-modal 
    id="modal-eliminar-empleado"
    entity="el empleado"
    entityIdField="empleado-id"
    entityDisplayField="empleado-nombre"
    additionalText="Esta acción eliminará al empleado y toda su información."
/>

<!-- Botón -->
<button 
    data-empleado-id="{{ $empleado->id }}" 
    data-empleado-nombre="{{ $empleado->nombre }}" 
    class="btn-eliminar-empleado">
    Eliminar
</button>

<!-- JavaScript -->
<script>
window.initDeleteModal({
    modalId: 'modal-eliminar-empleado',
    entityIdField: 'empleado-id',
    entityDisplayField: 'empleado-nombre',
    deleteButtonSelector: '.btn-eliminar-empleado',
    baseUrl: '{{ url("personal") }}'
});
</script>
```

### Para Mantenimientos
```blade
<x-delete-confirmation-modal 
    id="modal-eliminar-mantenimiento"
    entity="el mantenimiento"
    entityIdField="mantenimiento-id"
    entityDisplayField="mantenimiento-descripcion"
    additionalText="Esta acción eliminará el registro de mantenimiento."
/>

<!-- Botón -->
<button 
    data-mantenimiento-id="{{ $mantenimiento->id }}" 
    data-mantenimiento-descripcion="{{ $mantenimiento->descripcion }}" 
    class="btn-eliminar-mant">
    Eliminar
</button>

<!-- JavaScript -->
<script>
window.initDeleteModal({
    modalId: 'modal-eliminar-mantenimiento',
    entityIdField: 'mantenimiento-id',
    entityDisplayField: 'mantenimiento-descripcion',
    deleteButtonSelector: '.btn-eliminar-mant',
    baseUrl: '{{ url("mantenimientos") }}'
});
</script>
```

## Funcionalidades Incluidas

### 1. **Confirmación Visual**
- Modal centrado con overlay
- Icono de advertencia
- Mensaje personalizable
- Información específica de la entidad

### 2. **Manejo de Estados**
- Botón de loading durante la eliminación
- Feedback visual de progreso
- Manejo de errores con alertas

### 3. **Accesibilidad**
- Cierre con tecla ESC
- Cierre al hacer clic fuera del modal
- Botón de cancelar explícito
- Focus management

### 4. **Peticiones AJAX**
- Envío asíncrono del formulario
- Manejo de respuestas JSON y redirecciones
- Recarga automática en caso de éxito
- Manejo de errores con rollback del estado

## Ventajas del Componente

1. **DRY (Don't Repeat Yourself)**: Evita duplicar código de modales en múltiples vistas
2. **Mantenibilidad**: Cambios centralizados afectan todas las implementaciones
3. **Consistencia**: UX uniforme en todo el sistema
4. **Flexibilidad**: Altamente configurable para diferentes casos de uso
5. **Escalabilidad**: Fácil de implementar en nuevos módulos

## Requisitos del Controlador

El controlador debe manejar las peticiones DELETE correctamente:

```php
public function destroy($id)
{
    try {
        $entity = Model::findOrFail($id);
        $entity->delete();
        
        // Para peticiones AJAX
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        // Para peticiones normales
        return redirect()->route('entities.index')
            ->with('success', 'Entidad eliminada correctamente');
    } catch (\Exception $e) {
        if (request()->ajax()) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar']);
        }
        
        return redirect()->back()->with('error', 'Error al eliminar la entidad');
    }
}
```

## Notas Importantes

1. **CSRF Token**: El componente maneja automáticamente el token CSRF
2. **Rutas**: Asegúrate de que las rutas DELETE estén definidas correctamente
3. **Permisos**: Implementa validación de permisos en los controladores
4. **Validación**: Siempre valida que la entidad existe antes de eliminar
5. **Logs**: Considera agregar logging para auditoría de eliminaciones

## Estado Actual

✅ **Implementado y Probado**: El componente está funcionando correctamente en la vista de vehículos
✅ **Listo para Uso**: Puede ser implementado inmediatamente en otros módulos
✅ **Documentado**: Documentación completa disponible

## Próximos Pasos Sugeridos

1. Implementar en módulo de Personal
2. Implementar en módulo de Mantenimientos  
3. Implementar en módulo de Obras
4. Agregar animaciones de transición (opcional)
5. Considerar variantes para diferentes tipos de acciones (desactivar, archivar, etc.)