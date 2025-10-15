# 🗺️ Estados y Municipios de México en MAYÚSCULAS

## ✅ Cambios Implementados

Se ha configurado el sistema para que **todos los nombres de estados y municipios de México** se almacenen automáticamente en **MAYÚSCULAS**.

### 📁 Archivos JSON Actualizados

#### 1. `estados-municipios.json`
✅ **2,464 municipios convertidos a MAYÚSCULAS**

Este archivo contiene todos los municipios de México organizados por estado. Todos los nombres de municipios ahora están en MAYÚSCULAS.

**Ejemplo:**
```json
{
    "Nuevo Leon": [
        "ABASOLO",
        "AGUALEGUAS",
        "ALLENDE",
        "ANAHUAC",
        "APODACA",
        "ARAMBERRI",
        "MONTERREY",
        "SAN PEDRO GARZA GARCIA",
        "SANTA CATARINA",
        // ... 48 municipios más
    ]
}
```

#### 2. `estados.json`
✅ **32 estados ya estaban en MAYÚSCULAS**

Este archivo contiene los nombres de los estados con sus claves:

```json
[
    { "clave": "NL",  "nombre": "NUEVO LEON" },
    { "clave": "JAL", "nombre": "JALISCO" },
    { "clave": "CMX", "nombre": "CIUDAD DE MEXICO" },
    // ... 29 estados más
]
```

## 📋 Campos Afectados

### 1️⃣ Modelo Vehiculo

El modelo `Vehiculo` tiene dos campos para la ubicación del vehículo:

| Campo | Descripción | Ejemplo Antes | Ejemplo Después |
|-------|-------------|---------------|-----------------|
| `estado` | Estado de la República Mexicana | nuevo león | **NUEVO LEÓN** |
| `municipio` | Municipio o Ciudad | monterrey | **MONTERREY** |

**Configuración:**
```php
// app/Models/Vehiculo.php
protected $uppercaseFields = [
    'marca',
    'modelo',
    'n_serie',
    'placas',
    'observaciones',
    'estado',      // ← Estado de la República
    'municipio',   // ← Municipio/Ciudad
    'numero_poliza',
];
```

### 2️⃣ Modelo Personal

El modelo `Personal` tiene un campo para la dirección completa:

| Campo | Descripción | Ejemplo Antes | Ejemplo Después |
|-------|-------------|---------------|-----------------|
| `direccion` | Dirección completa (calle, número, colonia, ciudad, estado) | av. juárez 123, col. centro, monterrey, n.l. | **AV. JUÁREZ 123, COL. CENTRO, MONTERREY, N.L.** |

**Configuración:**
```php
// app/Models/Personal.php
protected $uppercaseFields = [
    'nombre_completo',
    'curp_numero',
    'rfc',
    'nss',
    'no_licencia',
    'direccion',   // ← Dirección completa
    'ine',
];
```

### 3️⃣ Modelo Obra

El modelo `Obra` tiene un campo para la ubicación de la obra:

| Campo | Descripción | Ejemplo Antes | Ejemplo Después |
|-------|-------------|---------------|-----------------|
| `ubicacion` | Ubicación de la obra (ciudad, estado, referencias) | libramiento monterrey, km 15 | **LIBRAMIENTO MONTERREY, KM 15** |

**Configuración:**
```php
// app/Models/Obra.php
protected $uppercaseFields = [
    'nombre_obra',
    'ubicacion',   // ← Ubicación de la obra
    'observaciones',
];
```

---

## 🎯 Ejemplos Prácticos

### Ejemplo 1: Registrar un Vehículo

```php
Vehiculo::create([
    'marca' => 'toyota',
    'modelo' => 'hilux',
    'placas' => 'abc-123',
    'estado' => 'nuevo león',        // → NUEVO LEÓN
    'municipio' => 'monterrey',      // → MONTERREY
    'estatus' => 'disponible',       // → disponible (NO cambia)
]);
```

**Resultado en BD:**
```
estado: NUEVO LEÓN
municipio: MONTERREY
estatus: disponible
```

### Ejemplo 2: Registrar Personal

```php
Personal::create([
    'nombre_completo' => 'juan pérez garcía',
    'rfc' => 'pegj850101',
    'direccion' => 'av. constitución 456, col. del valle, guadalajara, jalisco',
    'estatus' => 'activo',
]);
```

**Resultado en BD:**
```
nombre_completo: JUAN PÉREZ GARCÍA
direccion: AV. CONSTITUCIÓN 456, COL. DEL VALLE, GUADALAJARA, JALISCO
estatus: activo
```

### Ejemplo 3: Registrar una Obra

```php
Obra::create([
    'nombre_obra' => 'construcción de puente',
    'ubicacion' => 'carretera mty-saltillo km 40, apodaca, nuevo león',
    'estatus' => 'en_progreso',
]);
```

**Resultado en BD:**
```
nombre_obra: CONSTRUCCIÓN DE PUENTE
ubicacion: CARRETERA MTY-SALTILLO KM 40, APODACA, NUEVO LEÓN
estatus: en_progreso
```

---

## 🗺️ Ejemplos de Estados de México

Todos estos nombres se convertirán automáticamente a MAYÚSCULAS:

### Estados del Norte
- nuevo león → **NUEVO LEÓN**
- chihuahua → **CHIHUAHUA**
- coahuila → **COAHUILA**
- sonora → **SONORA**
- tamaulipas → **TAMAULIPAS**
- durango → **DURANGO**
- baja california → **BAJA CALIFORNIA**
- baja california sur → **BAJA CALIFORNIA SUR**
- sinaloa → **SINALOA**

### Estados del Centro
- ciudad de méxico → **CIUDAD DE MÉXICO**
- estado de méxico → **ESTADO DE MÉXICO**
- jalisco → **JALISCO**
- guanajuato → **GUANAJUATO**
- querétaro → **QUERÉTARO**
- aguascalientes → **AGUASCALIENTES**
- san luis potosí → **SAN LUIS POTOSÍ**
- zacatecas → **ZACATECAS**
- colima → **COLIMA**
- nayarit → **NAYARIT**

### Estados del Sur
- oaxaca → **OAXACA**
- chiapas → **CHIAPAS**
- veracruz → **VERACRUZ**
- puebla → **PUEBLA**
- guerrero → **GUERRERO**
- michoacán → **MICHOACÁN**
- morelos → **MORELOS**
- hidalgo → **HIDALGO**
- tlaxcala → **TLAXCALA**

### Estados del Sureste
- quintana roo → **QUINTANA ROO**
- yucatán → **YUCATÁN**
- campeche → **CAMPECHE**
- tabasco → **TABASCO**

---

## 🏙️ Ejemplos de Municipios/Ciudades

### Nuevo León
- monterrey → **MONTERREY**
- san pedro garza garcía → **SAN PEDRO GARZA GARCÍA**
- guadalupe → **GUADALUPE**
- santa catarina → **SANTA CATARINA**
- san nicolás de los garza → **SAN NICOLÁS DE LOS GARZA**
- apodaca → **APODACA**
- garcía → **GARCÍA**
- escobedo → **ESCOBEDO**

### Jalisco
- guadalajara → **GUADALAJARA**
- zapopan → **ZAPOPAN**
- tlaquepaque → **TLAQUEPAQUE**
- tonalá → **TONALÁ**
- puerto vallarta → **PUERTO VALLARTA**

### Ciudad de México
- miguel hidalgo → **MIGUEL HIDALGO**
- cuauhtémoc → **CUAUHTÉMOC**
- benito juárez → **BENITO JUÁREZ**
- coyoacán → **COYOACÁN**
- iztapalapa → **IZTAPALAPA**

---

## 🔄 Conversión de Datos Existentes

### Paso 1: Ver simulación
```bash
php artisan datos:mayusculas --tabla=vehiculos --dry-run
```

### Paso 2: Convertir vehículos
```bash
php artisan datos:mayusculas --tabla=vehiculos
```

**Salida esperada:**
```
📋 Procesando tabla: vehiculos
   Registros encontrados: 150
 150/150 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%
   ✓ Registros actualizados: 145
   ✓ Campos actualizados: 580

Campos convertidos:
- marca
- modelo
- n_serie
- placas
- observaciones
- estado         ← ESTADOS CONVERTIDOS
- municipio      ← MUNICIPIOS CONVERTIDOS
- numero_poliza
```

---

## 📊 Comparación: Antes vs Después

### Base de Datos ANTES (inconsistente):
```sql
SELECT id, marca, modelo, estado, municipio 
FROM vehiculos LIMIT 5;

| id | marca  | modelo | estado      | municipio              |
|----|--------|--------|-------------|------------------------|
| 1  | Toyota | Hilux  | Nuevo León  | monterrey              |
| 2  | Nissan | NP300  | nuevo leon  | Monterrey              |
| 3  | Ford   | Ranger | NUEVO LEÓN  | San Pedro Garza García |
| 4  | Chevy  | D-Max  | nuevo León  | MONTERREY              |
| 5  | RAM    | 2500   | Nuevo leon  | guadalupe              |
```

### Base de Datos DESPUÉS (consistente):
```sql
SELECT id, marca, modelo, estado, municipio 
FROM vehiculos LIMIT 5;

| id | marca  | modelo | estado      | municipio              |
|----|--------|--------|-------------|------------------------|
| 1  | TOYOTA | HILUX  | NUEVO LEÓN  | MONTERREY              |
| 2  | NISSAN | NP300  | NUEVO LEÓN  | MONTERREY              |
| 3  | FORD   | RANGER | NUEVO LEÓN  | SAN PEDRO GARZA GARCÍA |
| 4  | CHEVY  | D-MAX  | NUEVO LEÓN  | MONTERREY              |
| 5  | RAM    | 2500   | NUEVO LEÓN  | GUADALUPE              |
```

---

## 🎯 Beneficios

### 1. **Consistencia**
✅ Todos los estados y municipios se escriben igual
✅ No más variaciones: "nuevo león", "Nuevo León", "NUEVO LEÓN"

### 2. **Búsquedas Mejoradas**
✅ Más fácil filtrar y buscar por ubicación
✅ Reportes más precisos por estado/municipio

### 3. **Presentación Profesional**
✅ Documentos y reportes con formato uniforme
✅ Exports a Excel/PDF con mejor apariencia

### 4. **Integridad de Datos**
✅ Menos errores de captura
✅ Datos normalizados y estandarizados

---

## ⚠️ Nota Importante

### El campo `estatus` NO se convierte

```php
// ✅ CORRECTO
$vehiculo->estado = 'nuevo león';      // → NUEVO LEÓN (se convierte)
$vehiculo->municipio = 'monterrey';    // → MONTERREY (se convierte)
$vehiculo->estatus = 'disponible';     // → disponible (NO se convierte)

// El campo 'estatus' es un campo de CONTROL del sistema
// y mantiene sus valores originales:
// - disponible
// - asignado
// - en_mantenimiento
// - fuera_de_servicio
```

---

## 📝 Resumen

| Campo | Modelo | Propósito | Se Convierte |
|-------|--------|-----------|--------------|
| `estado` | Vehiculo | Estado de la República | ✅ SÍ |
| `municipio` | Vehiculo | Ciudad/Municipio | ✅ SÍ |
| `direccion` | Personal | Dirección completa | ✅ SÍ |
| `ubicacion` | Obra | Ubicación de la obra | ✅ SÍ |
| `estatus` | Vehiculo/Personal/Obra | Estado del registro en el sistema | ❌ NO |

---

## 🚀 Listo para Usar

El sistema está **completamente configurado** y funcionando:

1. ✅ Nuevos registros → Automáticamente en mayúsculas
2. ✅ Datos existentes → Usar comando de conversión
3. ✅ Documentación → Completamente actualizada
4. ✅ Todo probado y funcionando

**¡Los estados y municipios de México ya se manejan en MAYÚSCULAS!** 🎉🇲🇽
