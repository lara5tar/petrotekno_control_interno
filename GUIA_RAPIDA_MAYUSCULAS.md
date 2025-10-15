# 🔤 GUÍA RÁPIDA: Sistema de Mayúsculas

## ✅ ¿Qué se implementó?

### 1️⃣ Trait Automático (`UppercaseAttributes`)
- Convierte automáticamente campos a MAYÚSCULAS al crear/actualizar
- Ubicación: `app/Traits/UppercaseAttributes.php`
- Se aplica automáticamente, no requiere código adicional

### 2️⃣ Modelos Actualizados (7 modelos)

| Modelo | Campos en Mayúsculas |
|--------|----------------------|
| **Vehiculo** | marca, modelo, n_serie, placas, observaciones, estado, municipio, numero_poliza |
| **Personal** | nombre_completo, curp_numero, rfc, nss, no_licencia, direccion, ine |
| **Obra** | nombre_obra, ubicacion, observaciones |
| **Mantenimiento** | proveedor, descripcion |
| **Documento** | descripcion |
| **AsignacionObra** | observaciones |
| **Kilometraje** | observaciones |

### 3️⃣ Comando de Conversión
- Comando: `php artisan datos:mayusculas`
- Convierte datos existentes en la base de datos

## 🚀 Uso Inmediato

### Para Nuevos Registros
✨ **¡Automático!** No necesitas hacer nada especial.

```php
// Antes
Vehiculo::create(['marca' => 'toyota', 'modelo' => 'hilux']);

// Después - Automáticamente se guarda en mayúsculas
Vehiculo::create(['marca' => 'toyota', 'modelo' => 'hilux']);
// Se guarda como: TOYOTA, HILUX
```

### Para Datos Existentes

#### Paso 1: Ver qué se va a cambiar (simulación)
```bash
php artisan datos:mayusculas --dry-run
```

#### Paso 2: Convertir una tabla específica
```bash
php artisan datos:mayusculas --tabla=vehiculos
```

#### Paso 3: Convertir todas las tablas
```bash
php artisan datos:mayusculas
```

## 🛡️ Campos Protegidos (NO se convierten)

### ✅ Campos que SE mantienen como están:
- ✅ **Estados:** `estatus`, `estado`, `status`
- ✅ **Roles:** `rol`, `role`
- ✅ **Tipos:** `tipo`, `type`, `tipo_servicio`, `sistema_vehiculo`
- ✅ **Categorías:** `categoria`, `category`
- ✅ **Seguridad:** `password`, `email`, `token`
- ✅ **Fechas:** `created_at`, `updated_at`, `deleted_at`
- ✅ **Archivos:** `url`, `path`, `ruta`, `archivo`

### Ejemplos:
```php
// ✅ CORRECTO - Estos NO se convierten
Vehiculo::create([
    'marca' => 'toyota',           // → TOYOTA (se convierte)
    'estatus' => 'disponible',     // → disponible (NO se convierte)
]);

Personal::create([
    'nombre_completo' => 'juan',   // → JUAN (se convierte)
    'estatus' => 'activo',         // → activo (NO se convierte)
]);

Mantenimiento::create([
    'descripcion' => 'cambio',     // → CAMBIO (se convierte)
    'tipo_servicio' => 'PREVENTIVO', // → PREVENTIVO (NO se convierte)
    'sistema_vehiculo' => 'motor', // → motor (NO se convierte)
]);
```

## 📋 Checklist de Implementación

- [x] Trait `UppercaseAttributes` creado
- [x] Modelo `Vehiculo` actualizado
- [x] Modelo `Personal` actualizado
- [x] Modelo `Obra` actualizado
- [x] Modelo `Mantenimiento` actualizado
- [x] Modelo `Documento` actualizado
- [x] Modelo `AsignacionObra` actualizado
- [x] Modelo `Kilometraje` actualizado
- [x] Comando `datos:mayusculas` creado
- [x] Documentación completa creada
- [x] Sistema probado y funcionando

## 🎯 Próximos Pasos Recomendados

### 1. Backup (IMPORTANTE)
```bash
# Hacer backup de la base de datos antes de convertir
```

### 2. Simulación
```bash
php artisan datos:mayusculas --dry-run
```

### 3. Conversión
```bash
# Opción A: Convertir todo de una vez
php artisan datos:mayusculas

# Opción B: Ir tabla por tabla
php artisan datos:mayusculas --tabla=vehiculos
php artisan datos:mayusculas --tabla=personal
php artisan datos:mayusculas --tabla=obras
# etc...
```

### 4. Verificación
- Revisar la interfaz web
- Probar búsquedas
- Verificar reportes

## 💡 Ejemplos Prácticos

### Ejemplo 1: Crear un Vehículo
```php
$vehiculo = Vehiculo::create([
    'marca' => 'nissan',                    // → NISSAN
    'modelo' => 'np300',                    // → NP300
    'placas' => 'xyz-789',                  // → XYZ-789
    'n_serie' => 'vin123456',               // → VIN123456
    'estatus' => 'disponible',              // → disponible (no cambia)
    'observaciones' => 'vehículo nuevo',    // → VEHÍCULO NUEVO
]);
```

### Ejemplo 2: Crear Personal
```php
$personal = Personal::create([
    'nombre_completo' => 'maría lópez',     // → MARÍA LÓPEZ
    'rfc' => 'lomj900101',                  // → LOMJ900101
    'curp_numero' => 'lomj900101hdflpr09',  // → LOMJ900101HDFLPR09
    'estatus' => 'activo',                  // → activo (no cambia)
]);
```

### Ejemplo 3: Crear Obra
```php
$obra = Obra::create([
    'nombre_obra' => 'construcción puente', // → CONSTRUCCIÓN PUENTE
    'ubicacion' => 'monterrey, n.l.',       // → MONTERREY, N.L.
    'estatus' => 'en_progreso',             // → en_progreso (no cambia)
    'observaciones' => 'obra prioritaria',  // → OBRA PRIORITARIA
]);
```

## 🔧 Solución de Problemas

### Problema: Los nuevos registros no se guardan en mayúsculas
**Solución:**
```bash
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

### Problema: Quiero excluir un campo específico
**Solución:** Edita `app/Traits/UppercaseAttributes.php` y agrega el campo a `getExcludedFromUppercase()`

### Problema: Quiero agregar más campos a un modelo
**Solución:** Edita el modelo y agrega el campo al array `$uppercaseFields`

```php
protected $uppercaseFields = [
    'campo_existente',
    'nuevo_campo', // ← Agregar aquí
];
```

## 📚 Documentación Completa

Ver el archivo `MAYUSCULAS.md` para documentación detallada.

## ✨ Ventajas del Sistema

1. **🤖 Automático:** No requiere cambios en controllers o formularios
2. **🛡️ Seguro:** No afecta campos de control ni lógica del sistema
3. **🔄 Retrocompatible:** Funciona con código existente
4. **📊 Consistente:** Todos los datos en el mismo formato
5. **🎯 Selectivo:** Solo convierte los campos especificados
6. **🌍 UTF-8:** Maneja correctamente acentos y caracteres especiales

---

**¿Preguntas?** Consulta `MAYUSCULAS.md` para más detalles.
