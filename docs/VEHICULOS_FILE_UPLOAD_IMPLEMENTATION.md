# Implementación de Carga de Archivos para Vehículos

## Resumen
Se ha implementado exitosamente la funcionalidad de carga de archivos para los módulos de vehículos, reemplazando los campos de URL manual con un sistema automatizado que genera las URLs después de subir los archivos.

## Columnas Agregadas a la Tabla Vehículos

### Nuevas Columnas
- `poliza_url` (TEXT, nullable) - URL de la póliza de seguro
- `poliza_vencimiento` (DATE, nullable) - Fecha de vencimiento de la póliza
- `factura_url` (TEXT, nullable) - URL de la factura/pedimento
- `derecho_url` (TEXT, nullable) - URL del derecho vehicular
- `derecho_vencimiento` (DATE, nullable) - Fecha de vencimiento del derecho
- `url_imagen` (TEXT, nullable) - URL de la imagen del vehículo

### Migración
Archivo: `database/migrations/2025_08_17_084521_add_new_columns_to_vehiculos_table.php`
- ✅ Creada y ejecutada exitosamente
- Todas las columnas son nullable para compatibilidad con registros existentes

## Modelo Actualizado

### Archivo: `app/Models/Vehiculo.php`
```php
// Columnas agregadas al $fillable
'poliza_url', 'poliza_vencimiento', 'factura_url', 
'derecho_url', 'derecho_vencimiento', 'url_imagen'

// Casts agregados para fechas
'poliza_vencimiento' => 'date',
'derecho_vencimiento' => 'date',
```

## Sistema de Carga de Archivos

### Funcionalidad Implementada
1. **Validación de Archivos**: PDF, JPG, JPEG, PNG hasta 5MB
2. **Almacenamiento Organizado**: 
   - Documentos: `storage/app/public/vehiculos/documentos/`
   - Imágenes: `storage/app/public/vehiculos/imagenes/`
3. **Generación Automática de URLs**: Utilizando `Storage::url()`
4. **Reemplazo de Archivos**: Elimina automáticamente archivos anteriores al actualizar

### Estructura de Archivos
```
storage/app/public/vehiculos/
├── documentos/
│   ├── timestamp_poliza_vehiculo_id.pdf
│   ├── timestamp_derecho_vehiculo_id.pdf
│   └── timestamp_factura_vehiculo_id.pdf
└── imagenes/
    └── timestamp_imagen_vehiculo_id.jpg
```

## Formularios Actualizados

### Archivo: `resources/views/vehiculos/create.blade.php`
- ✅ Convertido a campos de carga de archivos
- Drag & drop interface implementada
- Preview de archivos seleccionados
- Validación JavaScript en tiempo real

### Archivo: `resources/views/vehiculos/edit.blade.php`  
- ✅ Muestra archivos existentes con enlaces de descarga
- Permite reemplazar archivos existentes
- Mantiene datos existentes si no se suben nuevos archivos

### Campos de Archivo Implementados
1. **Póliza de Seguro**: `poliza_file` / `poliza_seguro_file` (compatibilidad)
2. **Derecho Vehicular**: `derecho_file` / `derecho_vehicular_file` (compatibilidad)
3. **Factura/Pedimento**: `factura_file` / `factura_pedimento_file` (compatibilidad)
4. **Imagen del Vehículo**: `imagen_file` / `fotografia_file` (compatibilidad)

## Controlador Actualizado

### Archivo: `app/Http/Controllers/VehiculoController.php`

#### Validaciones Implementadas
```php
// Archivos con validación de tipo y tamaño
'poliza_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
'derecho_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
'factura_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
'imagen_file' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
```

#### Lógica de Almacenamiento
1. **Crear Vehículo** (`store()`):
   - Valida archivos
   - Genera nombres únicos con timestamp
   - Almacena en directorios apropiados
   - Genera y guarda URLs automáticamente

2. **Actualizar Vehículo** (`update()`):
   - Elimina archivos anteriores si existen
   - Almacena nuevos archivos
   - Actualiza URLs correspondientes

## JavaScript Implementado

### Archivo: `resources/js/app.js`
```javascript
// Función global para manejo de archivos
window.handleFileInput = function(input, previewId)
window.clearFileInput = function(inputId, previewId)
```

### Funcionalidades JavaScript
- Validación de tipo de archivo en tiempo real
- Validación de tamaño (5MB máximo)
- Preview visual del archivo seleccionado
- Botón para limpiar selección
- Feedback visual con íconos y colores

## Configuración de Storage

### Requisitos
- ✅ Enlace simbólico: `public/storage -> storage/app/public`
- ✅ Directorios creados: `vehiculos/documentos/` y `vehiculos/imagenes/`
- ✅ Permisos de escritura configurados

### Comandos Ejecutados
```bash
php artisan storage:link
mkdir -p storage/app/public/vehiculos/documentos
mkdir -p storage/app/public/vehiculos/imagenes
```

## Compatibilidad y Mapeo de Campos

### Campos de Entrada Soportados
| Campo Nuevo | Campo Compatible | Columna URL Destino |
|-------------|------------------|-------------------|
| `poliza_file` | `poliza_seguro_file` | `poliza_url` |
| `derecho_file` | `derecho_vehicular_file` | `derecho_url` |
| `factura_file` | `factura_pedimento_file` | `factura_url` |
| `imagen_file` | `fotografia_file` | `url_imagen` |

### Fechas de Vencimiento
| Campo Nuevo | Campo Compatible | Columna Destino |
|-------------|------------------|----------------|
| `poliza_vencimiento` | `fecha_vencimiento_seguro` | `poliza_vencimiento` |
| `derecho_vencimiento` | `fecha_vencimiento_derecho` | `derecho_vencimiento` |

## Frontend Assets

### Build Completado
- ✅ `npm run build` ejecutado exitosamente
- ✅ JavaScript compilado y disponible
- ✅ CSS y assets actualizados

### Archivos Generados
- `public/build/assets/app-BSBtLcXz.js` - JavaScript con funciones de archivo
- `public/build/assets/app-BftzYWjb.css` - Estilos actualizados

## Testing y Validación

### Estados Verificados
- ✅ Migración ejecutada sin errores
- ✅ Modelo actualizado correctamente
- ✅ Controlador sin errores de sintaxis
- ✅ Vistas actualizadas y funcionales
- ✅ JavaScript compilado correctamente
- ✅ Storage configurado y accesible
- ✅ Rutas funcionando correctamente

### Casos de Uso Cubiertos
1. **Crear vehículo con archivos**: Subida y almacenamiento automático
2. **Editar vehículo**: Mostrar archivos existentes y permitir reemplazo
3. **Validación**: Tipos de archivo y tamaño validados
4. **Eliminación**: Archivos anteriores eliminados al actualizar
5. **URLs**: Generación automática y almacenamiento en base de datos

## Flujo de Funcionamiento

### 1. Usuario sube archivo
- JavaScript valida tipo y tamaño
- Muestra preview del archivo seleccionado

### 2. Envío del formulario
- Controlador valida archivos del lado del servidor
- Genera nombre único basado en timestamp + tipo + ID del vehículo

### 3. Almacenamiento
- Archivo guardado en directorio apropiado
- URL pública generada usando `Storage::url()`
- URL guardada en base de datos

### 4. Acceso posterior
- URLs directamente accesibles vía navegador
- Enlaces de descarga en formulario de edición
- Archivos protegidos en storage pero accesibles públicamente

## Resultado Final
El sistema ahora permite:
- ✅ Subir archivos en lugar de ingresar URLs manualmente
- ✅ Generar URLs automáticamente después de la subida
- ✅ Almacenamiento organizado y seguro de archivos
- ✅ Validación robusta de tipos y tamaños
- ✅ Interface de usuario intuitiva con drag & drop
- ✅ Compatibilidad con formularios existentes
- ✅ Manejo adecuado de archivos existentes en edición
