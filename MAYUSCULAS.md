# Sistema de Conversión Automática a MAYÚSCULAS

## 📋 Descripción General

Este sistema convierte automáticamente los campos de texto importantes a **MAYÚSCULAS** sin afectar la lógica del sistema, campos de control (estados, roles, catálogos, etc.) ni el diseño.

## 🎯 Objetivos

- ✅ Convertir datos importantes (nombres, placas, descripciones, etc.) a MAYÚSCULAS
- ✅ Mantener intactos los campos de control (estados, roles, tipos, etc.)
- ✅ Aplicar automáticamente en nuevos registros
- ✅ Convertir datos existentes con un comando
- ✅ No afectar la lógica del sistema
- ✅ Mantener compatibilidad con el código existente

## 🔧 Componentes del Sistema

### 1. Trait `UppercaseAttributes`

**Ubicación:** `app/Traits/UppercaseAttributes.php`

**Función:** Convierte automáticamente los campos especificados a mayúsculas cuando se crean o actualizan registros.

**Campos excluidos automáticamente:**
- Passwords y tokens
- Emails
- Estados y estatus (estatus, estado, status)
- Roles y tipos (rol, role, tipo, type)
- Categorías (categoria, category)
- Sistemas y tipos específicos (sistema_vehiculo, tipo_servicio, etc.)
- Fechas y timestamps
- URLs y rutas de archivos

### 2. Modelos Actualizados

Los siguientes modelos ahora usan el trait `UppercaseAttributes`:

#### **Vehiculo** (`app/Models/Vehiculo.php`)
Campos en mayúsculas:
- `marca`
- `modelo`
- `n_serie`
- `placas`
- `observaciones`
- `estado` (Estado de la República: Nuevo León, Jalisco, etc.)
- `municipio` (Ciudad/Municipio: Monterrey, Guadalajara, etc.)
- `numero_poliza`

#### **Personal** (`app/Models/Personal.php`)
Campos en mayúsculas:
- `nombre_completo`
- `curp_numero`
- `rfc`
- `nss`
- `no_licencia`
- `direccion`
- `ine`

#### **Obra** (`app/Models/Obra.php`)
Campos en mayúsculas:
- `nombre_obra`
- `ubicacion`
- `observaciones`

#### **Mantenimiento** (`app/Models/Mantenimiento.php`)
Campos en mayúsculas:
- `proveedor`
- `descripcion`

#### **Documento** (`app/Models/Documento.php`)
Campos en mayúsculas:
- `descripcion`

#### **AsignacionObra** (`app/Models/AsignacionObra.php`)
Campos en mayúsculas:
- `observaciones`

#### **Kilometraje** (`app/Models/Kilometraje.php`)
Campos en mayúsculas:
- `observaciones`

### 3. Comando de Conversión

**Ubicación:** `app/Console/Commands/ConvertirDatosAMayusculas.php`

**Comando:** `php artisan datos:mayusculas`

## 🚀 Uso

### Para Nuevos Registros

Los nuevos registros **automáticamente** convertirán los campos especificados a mayúsculas:

```php
// Ejemplo: Crear un vehículo
$vehiculo = Vehiculo::create([
    'marca' => 'toyota',          // Se guarda como 'TOYOTA'
    'modelo' => 'hilux',          // Se guarda como 'HILUX'
    'placas' => 'abc-123',        // Se guarda como 'ABC-123'
    'estatus' => 'disponible',    // Se mantiene como 'disponible' (excluido)
]);

// Ejemplo: Crear personal
$personal = Personal::create([
    'nombre_completo' => 'juan pérez',  // Se guarda como 'JUAN PÉREZ'
    'rfc' => 'pepj800101',              // Se guarda como 'PEPJ800101'
    'estatus' => 'activo',              // Se mantiene como 'activo' (excluido)
]);
```

### Para Datos Existentes

#### 1. **Simulación (Dry-Run)**

Primero, ejecuta una simulación para ver qué cambios se harían:

```bash
php artisan datos:mayusculas --dry-run
```

Esto mostrará:
- Cuántos registros se actualizarían
- Cuántos campos se modificarían
- Sin hacer cambios reales en la base de datos

#### 2. **Conversión de Todas las Tablas**

```bash
php artisan datos:mayusculas
```

#### 3. **Conversión de una Tabla Específica**

```bash
# Solo vehículos
php artisan datos:mayusculas --tabla=vehiculos

# Solo personal
php artisan datos:mayusculas --tabla=personal

# Solo obras
php artisan datos:mayusculas --tabla=obras
```

Tablas disponibles:
- `vehiculos`
- `personal`
- `obras`
- `mantenimientos`
- `documentos`
- `asignaciones_obra`
- `kilometrajes`

#### 4. **Simulación de una Tabla Específica**

```bash
php artisan datos:mayusculas --tabla=vehiculos --dry-run
```

## 📊 Ejemplo de Salida del Comando

```
========================================
CONVERSIÓN DE DATOS A MAYÚSCULAS
========================================

⚠️  MODO DRY-RUN: No se harán cambios reales en la base de datos

📋 Procesando tabla: vehiculos
   Registros encontrados: 150
   ✓ Registros actualizados: 145
   ✓ Campos actualizados: 580

📋 Procesando tabla: personal
   Registros encontrados: 80
   ✓ Registros actualizados: 78
   ✓ Campos actualizados: 312

========================================
✅ PROCESO COMPLETADO
========================================
Total de registros actualizados: 223
Total de campos actualizados: 892

ℹ️  Esto fue una simulación. Ejecuta sin --dry-run para hacer los cambios reales.
```

## 🔍 Campos que NO se Convierten

Estos campos **nunca** se convertirán a mayúsculas:

### Autenticación y Seguridad
- `password`
- `email`
- `remember_token`
- `api_token`

### Control del Sistema
- `estatus` / `estado` / `status`
- `rol` / `role`
- `tipo` / `type`
- `categoria` / `category`

### Campos Específicos
- `sistema_vehiculo` (valores: motor, transmision, hidraulico, general)
- `tipo_servicio` (valores: CORRECTIVO, PREVENTIVO)
- `tipo_documento`
- `tipo_activo`

### Técnicos
- Fechas y timestamps (`created_at`, `updated_at`, `deleted_at`)
- URLs y rutas (`url`, `path`, `ruta`, `archivo`, `file`)

## 🛠️ Personalización

### Agregar el Trait a un Nuevo Modelo

```php
<?php

namespace App\Models;

use App\Traits\UppercaseAttributes;
use Illuminate\Database\Eloquent\Model;

class MiModelo extends Model
{
    use UppercaseAttributes;

    /**
     * Campos que se convertirán a MAYÚSCULAS
     */
    protected $uppercaseFields = [
        'campo1',
        'campo2',
        'campo3',
    ];
}
```

### Agregar una Nueva Tabla al Comando de Conversión

Edita `app/Console/Commands/ConvertirDatosAMayusculas.php`:

```php
protected $configuracion = [
    // ... tablas existentes ...
    
    'mi_tabla' => [
        'model' => MiModelo::class,
        'campos' => ['campo1', 'campo2', 'campo3']
    ],
];
```

## ⚠️ Recomendaciones

1. **Antes de ejecutar en producción:**
   - Hacer un backup de la base de datos
   - Ejecutar primero con `--dry-run`
   - Revisar los resultados de la simulación

2. **Orden de ejecución sugerido:**
   ```bash
   # 1. Ver qué cambiaría
   php artisan datos:mayusculas --dry-run
   
   # 2. Probar con una tabla pequeña
   php artisan datos:mayusculas --tabla=documentos
   
   # 3. Si todo está bien, ejecutar completo
   php artisan datos:mayusculas
   ```

3. **Verificación post-conversión:**
   - Revisar la interfaz de usuario
   - Verificar búsquedas y filtros
   - Comprobar exports y reportes

## 🐛 Solución de Problemas

### El comando no encuentra el trait
```bash
# Limpiar caché de configuración y autoload
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

### No se están convirtiendo los nuevos registros

Verifica que:
1. El modelo tenga el trait `UppercaseAttributes`
2. El campo esté en el array `$uppercaseFields`
3. El campo no esté en la lista de excluidos

### Los datos no se guardan en mayúsculas

El trait se aplica antes de guardar, así que verifica:
```php
// Esto funcionará:
$vehiculo = new Vehiculo();
$vehiculo->marca = 'toyota';
$vehiculo->save(); // Se guarda como 'TOYOTA'

// Esto también funcionará:
Vehiculo::create(['marca' => 'toyota']); // Se guarda como 'TOYOTA'
```

## 📝 Notas Adicionales

- El sistema es **compatible** con mutators y accessors existentes
- No afecta las validaciones ni reglas de negocio
- Es **retrocompatible** con código existente
- Los caracteres especiales y acentos se manejan correctamente (UTF-8)
- No afecta el rendimiento significativamente

## 🎉 Beneficios

1. **Consistencia:** Todos los datos importantes se almacenan en el mismo formato
2. **Legibilidad:** Los datos se leen mejor en mayúsculas
3. **Búsqueda:** Facilita búsquedas case-insensitive
4. **Profesionalismo:** Apariencia más formal y profesional
5. **Automatización:** No requiere intervención manual
