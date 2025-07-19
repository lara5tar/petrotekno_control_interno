# 🧪 Documentación de Tests - Integración Frontend

## 📋 Resumen de Cambios

Este documento describe los nuevos tests implementados y los cambios relacionados que pueden afectar la integración frontend.

---

## 🔧 Cambios en Controladores

### PersonalController
- **Nuevo endpoint de paginación personalizada**: Ahora acepta el parámetro `per_page`
- **Endpoint**: `GET /api/personal?per_page={number}`
- **Default**: 15 registros por página
- **Rango permitido**: 1-100

```javascript
// Ejemplo de uso en frontend
const response = await fetch('/api/personal?per_page=25&page=1');
```

---

## 🔐 Validación de Usuarios

### StoreUserRequest - Password Confirmation
Se agregó validación obligatoria de confirmación de contraseña para la creación de usuarios.

**⚠️ CAMBIO OBLIGATORIO**: Todos los formularios de creación de usuarios deben incluir el campo `password_confirmation`

```javascript
// ANTES (ya no funciona)
const userData = {
    nombre_usuario: "usuario",
    email: "user@petrotekno.com", 
    password: "password123",
    rol_id: 1
};

// AHORA (requerido)
const userData = {
    nombre_usuario: "usuario",
    email: "user@petrotekno.com",
    password: "password123",
    password_confirmation: "password123", // ← OBLIGATORIO
    rol_id: 1
};
```

---

## 🚗 Modelo Mantenimiento - Nuevas Funciones

### Nuevos Scopes Disponibles
```php
// Equivalencia para uso en frontend via API
```

#### 1. `scopePorVehiculo()` - Alias de compatibilidad
- **Endpoint**: `GET /api/mantenimientos?vehiculo_id={id}`

#### 2. `scopePorFecha()` - Filtrar por fecha específica  
- **Endpoint**: `GET /api/mantenimientos?fecha={YYYY-MM-DD}`

#### 3. `scopeEntreFechas()` - Filtrar por rango de fechas
- **Endpoint**: `GET /api/mantenimientos?fecha_inicio={YYYY-MM-DD}&fecha_fin={YYYY-MM-DD}`

### Nuevo Accessor: `costo_formateado`
**Formato automático de moneda**

```javascript
// Respuesta de la API incluirá:
{
    "id": 1,
    "costo": 1500.50,
    "costo_formateado": "$1,500.50"  // ← Nuevo campo calculado
}
```

---

## 👤 Usuarios de Prueba Agregados

### Nuevo Usuario Operador
Para pruebas de integración, se agregó un usuario operador:
- **Usuario**: `operador`
- **Email**: `operador@petrotekno.com`  
- **Contraseña**: `password123`
- **Rol**: Operador

---

## 🆕 Nuevos Controladores de Tests

### PermissionController
- **Funcionalidades completas**: CRUD de permisos
- **Endpoints**:
  - `GET /api/permissions` - Listar permisos
  - `POST /api/permissions` - Crear permiso
  - `GET /api/permissions/{id}` - Ver permiso específico
  - `PUT /api/permissions/{id}` - Actualizar permiso  
  - `DELETE /api/permissions/{id}` - Eliminar permiso

### PersonalController  
- **Funcionalidades**: Gestión completa de personal
- **Filtros disponibles**:
  - `estatus` - Filtrar por estado (activo/inactivo)
  - `search` - Búsqueda por nombre
  - `per_page` - Paginación personalizada

### UserController
- **Funcionalidades**: Gestión completa de usuarios
- **⚠️ Importante**: Requiere `password_confirmation` obligatorio
- **Filtros**:
  - `rol_id` - Filtrar por rol
  - `search` - Búsqueda por nombre/email

---

## 📊 Logging Automático

### Nuevas Acciones Registradas
Todas las operaciones CRUD de los siguientes módulos ahora se registran automáticamente en `log_acciones`:

- **Permisos**: `crear_permiso`, `actualizar_permiso`, `eliminar_permiso`  
- **Personal**: `crear_personal`, `actualizar_personal`, `eliminar_personal`
- **Usuarios**: `crear_usuario`, `actualizar_usuario`, `eliminar_usuario`

```javascript
// Para consultar logs via API
const logs = await fetch('/api/logs?tabla_afectada=users&accion=crear_usuario');
```

---

## 🔒 Autorización y Permisos

### Matriz de Permisos Actualizada

| Rol | Permisos | Personal | Usuarios |
|-----|----------|----------|----------|
| **Administrador** | ✅ CRUD | ✅ CRUD | ✅ CRUD |
| **Supervisor** | ❌ | ✅ Ver | ❌ |
| **Operador** | ❌ | ❌ | ❌ |

---

## 🚨 Breaking Changes para Frontend

### 1. Validación de Password (CRÍTICO)
```javascript
// ❌ ESTO FALLARÁ AHORA
POST /api/users {
    "password": "123456"
}

// ✅ FORMATO CORRECTO REQUERIDO  
POST /api/users {
    "password": "123456",
    "password_confirmation": "123456"
}
```

### 2. Endpoints Eliminados
Los siguientes controllers vacíos fueron eliminados:
- `CatalogoTipoServicioController` 
- `MantenimientoController`

**⚠️ Acción requerida**: Verificar que el frontend no esté llamando estos endpoints.

---

## 🧪 Endpoints de Test Disponibles

Para desarrollo y pruebas, los siguientes endpoints están completamente implementados:

```bash
# Permisos
GET|POST /api/permissions
GET|PUT|DELETE /api/permissions/{id}

# Personal  
GET|POST /api/personal
GET|PUT|DELETE /api/personal/{id}

# Usuarios
GET|POST /api/users  
GET|PUT|DELETE /api/users/{id}
```

---

## 📝 Ejemplos de Integración

### Crear Usuario con Validación
```javascript
const createUser = async (userData) => {
    const response = await fetch('/api/users', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
            ...userData,
            password_confirmation: userData.password // OBLIGATORIO
        })
    });
    return response.json();
};
```

### Paginación Personalizada
```javascript
const getPersonal = async (page = 1, perPage = 15) => {
    const response = await fetch(`/api/personal?page=${page}&per_page=${perPage}`);
    return response.json();
};
```

### Filtrado de Mantenimientos
```javascript  
const getMantenimientosByDateRange = async (fechaInicio, fechaFin) => {
    const params = new URLSearchParams({
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin
    });
    const response = await fetch(`/api/mantenimientos?${params}`);
    return response.json();
};
```

---

## 🔍 Tests Coverage

Los siguientes módulos tienen cobertura completa de tests:

### Unit Tests
- ✅ CatalogoEstatus (161 líneas)
- ✅ CatalogoTipoServicio (83 líneas)  
- ✅ CategoriaPersonal (94 líneas)
- ✅ LogAccion (158 líneas)
- ✅ Mantenimiento (110 líneas)

### Feature Tests  
- ✅ PermissionController (474 líneas)
- ✅ PersonalController (310 líneas)
- ✅ UserController (377 líneas)
- ✅ AuditLogging & Boundary Tests (actualizados)

---

## ⚡ Próximos Pasos para Frontend

1. **Actualizar formularios de usuario** para incluir `password_confirmation`
2. **Implementar paginación personalizada** en listados de personal  
3. **Agregar filtros de fecha** para mantenimientos
4. **Utilizar campo `costo_formateado`** para mostrar precios
5. **Verificar endpoints eliminados** y actualizar rutas si es necesario

---

*Documentación actualizada: Julio 2025*  
*Versión: Issue #16 - Tests Implementation*
