# Guía de Migración - Sistema de Control Interno v1.3

## 🔄 Cambios Estructurales Importantes

### Versión: 1.3.0
### Fecha: 19 de Julio de 2025

---

## ⚠️ BREAKING CHANGES

### 1. Eliminación del Campo `nombre_usuario`

#### ❌ Lo que ya NO funciona:
```php
// ❌ OBSOLETO - Ya no existe en la base de datos
$user = User::create([
    'nombre_usuario' => 'admin_user',  // ❌ Campo eliminado
    'email' => 'admin@petrotekno.com',
    'password' => 'password'
]);

// ❌ OBSOLETO - Consultas por nombre_usuario
$user = User::where('nombre_usuario', 'admin_user')->first();
```

#### ✅ Nuevas formas de trabajar:
```php
// ✅ CORRECTO - Solo usar email como identificador
$user = User::create([
    'email' => 'admin@petrotekno.com',  // ✅ Único identificador
    'password' => 'password',
    'rol_id' => 1,
    'personal_id' => 1
]);

// ✅ CORRECTO - Consultas por email
$user = User::where('email', 'admin@petrotekno.com')->first();
```

### 2. Login y Autenticación

#### ❌ Antes (v1.2):
```json
POST /api/login
{
    "nombre_usuario": "admin_user",
    "password": "password123"
}
```

#### ✅ Ahora (v1.3):
```json
POST /api/login
{
    "email": "admin@petrotekno.com",
    "password": "password123"
}
```

---

## ✨ NUEVAS FUNCIONALIDADES

### Campo `contenido` en Documentos

El nuevo campo JSON `contenido` permite almacenar información estructurada específica de cada documento:

#### Ejemplos de uso:

```php
// Para una licencia de conducir
$documento = Documento::create([
    'tipo_documento_id' => 1,
    'descripcion' => 'Licencia de Juan Pérez',
    'contenido' => [
        'numero_licencia' => 'L123456789',
        'clase' => 'B1',
        'restricciones' => ['Lentes correctivos'],
        'puntos_disponibles' => 20,
        'emisor' => 'ANT'
    ]
]);

// Para una póliza de seguro
$documento = Documento::create([
    'tipo_documento_id' => 2,
    'descripcion' => 'Póliza de seguro vehicular',
    'contenido' => [
        'numero_poliza' => 'P987654321',
        'aseguradora' => 'Seguros ABC',
        'cobertura' => ['Responsabilidad civil', 'Daños propios'],
        'deducible' => 500.00,
        'prima_anual' => 1200.50
    ]
]);
```

#### API Request:
```json
POST /api/documentos
{
    "tipo_documento_id": 1,
    "descripcion": "Licencia renovada",
    "contenido": {
        "numero_licencia": "L123456789",
        "clase": "B1",
        "restricciones": ["Lentes correctivos"],
        "metadata": {
            "renovacion": true
        }
    }
}
```

---

## 📋 Checklist para Desarrolladores Frontend

### ✅ Tareas Requeridas:

- [ ] **Actualizar formularios de login** para usar `email` en lugar de `nombre_usuario`
- [ ] **Remover referencias** a `nombre_usuario` en interfaces TypeScript
- [ ] **Actualizar formularios de usuario** para eliminar campo `nombre_usuario`
- [ ] **Actualizar tablas de usuarios** para mostrar solo `email`
- [ ] **Implementar manejo del campo `contenido`** en formularios de documentos
- [ ] **Agregar validaciones** para el campo `contenido` JSON
- [ ] **Actualizar interfaces** para incluir campo `contenido` opcional

### 🔧 Interfaces TypeScript Actualizadas:

```typescript
// ❌ OBSOLETO
interface User {
    id: number;
    nombre_usuario: string;  // ❌ Eliminado
    email: string;
    rol_id: number;
    personal_id: number;
}

// ✅ ACTUALIZADO
interface User {
    id: number;
    email: string;  // ✅ Único identificador
    rol_id: number;
    personal_id: number;
    created_at: string;
    updated_at: string;
    // Relaciones
    rol?: Role;
    personal?: Personal;
}

// ✅ NUEVO - Documento con contenido
interface Documento {
    id: number;
    tipo_documento_id: number;
    descripcion?: string;
    ruta_archivo?: string;
    fecha_vencimiento?: string;
    contenido?: object;  // ✅ Nuevo campo JSON
    vehiculo_id?: number;
    personal_id?: number;
    obra_id?: number;
    mantenimiento_id?: number;
    // ... resto de campos
}
```

---

## 🗄️ Migraciones Aplicadas

### 1. Eliminación de `nombre_usuario`
```php
// Migración: 2025_07_19_082632_remove_nombre_usuario_from_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn('nombre_usuario');
});
```

### 2. Agregado de `contenido`
```php
// Migración: 2025_07_19_082635_add_contenido_to_documentos_table.php
Schema::table('documentos', function (Blueprint $table) {
    $table->json('contenido')->nullable()->after('mantenimiento_id');
});
```

---

## 🧪 Validación de Cambios

### Tests Actualizados:
- ✅ **451 tests pasando** (0 fallando)
- ✅ **2,483 aserciones exitosas**
- ✅ Tiempo de ejecución: ~9.5 segundos

### Verificación Rápida:
```bash
# Verificar que las migraciones se aplicaron
php artisan migrate:status

# Ejecutar todos los tests
php artisan test

# Verificar estructura de usuario actual
php artisan tinker
>>> User::first()->toArray()
```

---

## 📞 Soporte

Para dudas sobre esta migración contactar al equipo de Backend Development.

**Documentación actualizada el**: 19 de Julio de 2025
