# API Documentation - Sistema de Control Interno

## Información General

- **Base URL:** `{APP_URL}/api`
- **Autenticación:** Laravel Sanctum (Bearer Token)
- **Formato de respuesta:** JSON
- **Versión:** 1.1
- **Módulos:** Usuarios/Roles, Personal, Vehículos, Mantenimientos ⭐ NUEVO

## Autenticación

### 1. Login
**Endpoint:** `POST /api/login`

**Request:**
```json
{
    "email": "admin@petrotekno.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Admin Principal",
        "email": "admin@petrotekno.com",
        "email_verified_at": null,
        "created_at": "2025-01-17T10:00:00.000000Z",
        "updated_at": "2025-01-17T10:00:00.000000Z",
        "role": {
            "id": 1,
            "nombre": "Administrador",
            "descripcion": "Acceso completo al sistema"
        },
        "personal": {
            "id": 1,
            "nombres": "Admin",
            "apellidos": "Principal",
            "cedula": "1234567890",
            "telefono": "0999999999",
            "email": "admin@petrotekno.com",
            "estado": "activo"
        }
    },
    "token": "1|abcd1234efgh5678ijkl9012mnop3456"
}
```

**Response (401):**
```json
{
    "message": "Invalid credentials"
}
```

### 2. Logout
**Endpoint:** `POST /api/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Logout successful"
}
```

### 3. Información del Usuario Autenticado
**Endpoint:** `GET /api/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "id": 1,
    "name": "Admin Principal",
    "email": "admin@petrotekno.com",
    "role": {
        "id": 1,
        "nombre": "Administrador",
        "descripcion": "Acceso completo al sistema",
        "permissions": [
            {
                "id": 1,
                "nombre": "crear_usuarios",
                "descripcion": "Crear nuevos usuarios"
            }
        ]
    },
    "personal": {
        "id": 1,
        "nombres": "Admin",
        "apellidos": "Principal",
        "cedula": "1234567890"
    }
}
```

### 4. Cambiar Contraseña
**Endpoint:** `POST /api/change-password`

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
    "current_password": "password123",
    "new_password": "newpassword456",
    "new_password_confirmation": "newpassword456"
}
```

**Response (200):**
```json
{
    "message": "Password changed successfully"
}
```

## Gestión de Usuarios

### 1. Listar Usuarios
**Endpoint:** `GET /api/users`

**Permisos requeridos:** `ver_usuarios`

**Query Parameters:**
- `search` (opcional): Buscar por nombre o email
- `role_id` (opcional): Filtrar por rol
- `status` (opcional): Filtrar por estado (activo/inactivo)
- `per_page` (opcional): Elementos por página (default: 15)

**Ejemplo:** `GET /api/users?search=juan&role_id=2&per_page=10`

**Response (200):**
```json
{
    "data": [
        {
            "id": 2,
            "name": "Juan Supervisor",
            "email": "supervisor@petrotekno.com",
            "email_verified_at": null,
            "created_at": "2025-01-17T10:00:00.000000Z",
            "updated_at": "2025-01-17T10:00:00.000000Z",
            "role": {
                "id": 2,
                "nombre": "Supervisor",
                "descripcion": "Supervisor de operaciones"
            },
            "personal": {
                "id": 2,
                "nombres": "Juan",
                "apellidos": "Supervisor",
                "cedula": "0987654321",
                "estado": "activo"
            }
        }
    ],
    "current_page": 1,
    "per_page": 15,
    "total": 2,
    "last_page": 1
}
```

### 2. Crear Usuario
**Endpoint:** `POST /api/users`

**Permisos requeridos:** `crear_usuarios`

**Request:**
```json
{
    "name": "Carlos Operador",
    "email": "carlos@petrotekno.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 3,
    "personal_id": 5
}
```

**Response (201):**
```json
{
    "message": "User created successfully",
    "user": {
        "id": 3,
        "name": "Carlos Operador",
        "email": "carlos@petrotekno.com",
        "role_id": 3,
        "personal_id": 5,
        "created_at": "2025-01-17T10:00:00.000000Z",
        "updated_at": "2025-01-17T10:00:00.000000Z"
    }
}
```

### 3. Actualizar Usuario
**Endpoint:** `PUT /api/users/{id}`

**Permisos requeridos:** `editar_usuarios`

**Request:**
```json
{
    "name": "Carlos Operador Actualizado",
    "email": "carlos.nuevo@petrotekno.com",
    "role_id": 2,
    "personal_id": 5
}
```

**Response (200):**
```json
{
    "message": "User updated successfully",
    "user": {
        "id": 3,
        "name": "Carlos Operador Actualizado",
        "email": "carlos.nuevo@petrotekno.com",
        "role_id": 2,
        "personal_id": 5,
        "updated_at": "2025-01-17T10:15:00.000000Z"
    }
}
```

### 4. Eliminar Usuario (Soft Delete)
**Endpoint:** `DELETE /api/users/{id}`

**Permisos requeridos:** `eliminar_usuarios`

**Response (200):**
```json
{
    "message": "User deleted successfully"
}
```

### 5. Restaurar Usuario
**Endpoint:** `POST /api/users/{id}/restore`

**Permisos requeridos:** `eliminar_usuarios`

**Response (200):**
```json
{
    "message": "User restored successfully"
}
```

## Gestión de Roles

### 1. Listar Roles
**Endpoint:** `GET /api/roles`

**Permisos requeridos:** `ver_roles`

**Response (200):**
```json
{
    "data": [
        {
            "id": 1,
            "nombre": "Administrador",
            "descripcion": "Acceso completo al sistema",
            "created_at": "2025-01-17T10:00:00.000000Z",
            "updated_at": "2025-01-17T10:00:00.000000Z",
            "permissions": [
                {
                    "id": 1,
                    "nombre": "crear_usuarios",
                    "descripcion": "Crear nuevos usuarios"
                }
            ],
            "users_count": 1
        }
    ]
}
```

### 2. Crear Rol
**Endpoint:** `POST /api/roles`

**Permisos requeridos:** `crear_roles`

**Request:**
```json
{
    "nombre": "Operador",
    "descripcion": "Usuario operativo del sistema",
    "permissions": [1, 2, 3]
}
```

**Response (201):**
```json
{
    "message": "Role created successfully",
    "role": {
        "id": 4,
        "nombre": "Operador",
        "descripcion": "Usuario operativo del sistema",
        "created_at": "2025-01-17T10:00:00.000000Z",
        "updated_at": "2025-01-17T10:00:00.000000Z"
    }
}
```

### 3. Actualizar Rol
**Endpoint:** `PUT /api/roles/{id}`

**Permisos requeridos:** `editar_roles`

**Request:**
```json
{
    "nombre": "Operador Senior",
    "descripcion": "Usuario operativo senior del sistema",
    "permissions": [1, 2, 3, 4]
}
```

### 4. Eliminar Rol
**Endpoint:** `DELETE /api/roles/{id}`

**Permisos requeridos:** `eliminar_roles`

**Nota:** No se puede eliminar un rol que tenga usuarios asignados.

**Response (200):**
```json
{
    "message": "Role deleted successfully"
}
```

## Gestión de Permisos

### 1. Listar Permisos
**Endpoint:** `GET /api/permissions`

**Permisos requeridos:** `ver_permisos`

**Response (200):**
```json
{
    "data": [
        {
            "id": 1,
            "nombre": "crear_usuarios",
            "descripcion": "Crear nuevos usuarios",
            "created_at": "2025-01-17T10:00:00.000000Z",
            "updated_at": "2025-01-17T10:00:00.000000Z",
            "roles_count": 2
        }
    ]
}
```

### 2. Crear Permiso
**Endpoint:** `POST /api/permissions`

**Permisos requeridos:** `crear_permisos`

**Request:**
```json
{
    "nombre": "generar_reportes",
    "descripcion": "Generar reportes del sistema"
}
```

### 3. Actualizar Permiso
**Endpoint:** `PUT /api/permissions/{id}`

**Permisos requeridos:** `editar_permisos`

### 4. Eliminar Permiso
**Endpoint:** `DELETE /api/permissions/{id}`

**Permisos requeridos:** `eliminar_permisos`

**Nota:** No se puede eliminar un permiso que esté asignado a roles.

## Gestión de Personal

### 1. Listar Personal
**Endpoint:** `GET /api/personal`

**Permisos requeridos:** `ver_personal`

**Query Parameters:**
- `search` (opcional): Buscar por nombres, apellidos o cédula
- `categoria_id` (opcional): Filtrar por categoría
- `estado` (opcional): Filtrar por estado
- `per_page` (opcional): Elementos por página

**Response (200):**
```json
{
    "data": [
        {
            "id": 1,
            "nombres": "Juan Carlos",
            "apellidos": "Pérez López",
            "cedula": "1234567890",
            "telefono": "0999999999",
            "email": "juan@petrotekno.com",
            "fecha_ingreso": "2024-01-15",
            "salario": "1500.00",
            "estado": "activo",
            "categoria": {
                "id": 1,
                "nombre": "Ingeniero",
                "descripcion": "Ingeniero de campo"
            },
            "user": {
                "id": 2,
                "name": "Juan Carlos Pérez",
                "email": "juan@petrotekno.com"
            }
        }
    ]
}
```

### 2. Crear Personal
**Endpoint:** `POST /api/personal`

**Permisos requeridos:** `crear_personal`

**Request:**
```json
{
    "nombres": "María Elena",
    "apellidos": "González Torres",
    "cedula": "0987654321",
    "telefono": "0988888888",
    "email": "maria@petrotekno.com",
    "fecha_ingreso": "2025-01-17",
    "salario": 1800.00,
    "categoria_id": 2,
    "estado": "activo"
}
```

### 3. Actualizar Personal
**Endpoint:** `PUT /api/personal/{id}`

**Permisos requeridos:** `editar_personal`

### 4. Eliminar Personal
**Endpoint:** `DELETE /api/personal/{id}`

**Permisos requeridos:** `eliminar_personal`

**Nota:** No se puede eliminar personal que tenga un usuario asociado.

## Auditoría y Logs

### 1. Ver Logs de Acciones
**Endpoint:** `GET /api/logs`

**Permisos requeridos:** `ver_logs`

**Query Parameters:**
- `user_id` (opcional): Filtrar por usuario
- `accion` (opcional): Filtrar por tipo de acción
- `fecha_desde` (opcional): Filtrar desde fecha (Y-m-d)
- `fecha_hasta` (opcional): Filtrar hasta fecha (Y-m-d)

**Response (200):**
```json
{
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "accion": "login",
            "descripcion": "Usuario inició sesión",
            "ip_address": "127.0.0.1",
            "user_agent": "Mozilla/5.0...",
            "created_at": "2025-01-17T10:00:00.000000Z",
            "user": {
                "id": 1,
                "name": "Admin Principal",
                "email": "admin@petrotekno.com"
            }
        }
    ]
}
```

## Códigos de Error

### Códigos HTTP Comunes:
- **200:** Éxito
- **201:** Creado exitosamente
- **400:** Solicitud incorrecta
- **401:** No autenticado
- **403:** Sin permisos
- **404:** No encontrado
- **422:** Error de validación
- **500:** Error interno del servidor

### Formato de Errores de Validación (422):
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email field is required."
        ],
        "password": [
            "The password must be at least 8 characters."
        ]
    }
}
```

### Formato de Errores de Permisos (403):
```json
{
    "message": "Unauthorized action."
}
```

## Permisos del Sistema

### Lista completa de permisos disponibles:
- `crear_usuarios` - Crear nuevos usuarios
- `ver_usuarios` - Ver lista de usuarios
- `editar_usuarios` - Editar usuarios existentes
- `eliminar_usuarios` - Eliminar/restaurar usuarios
- `crear_roles` - Crear nuevos roles
- `ver_roles` - Ver lista de roles
- `editar_roles` - Editar roles existentes
- `eliminar_roles` - Eliminar roles
- `crear_permisos` - Crear nuevos permisos
- `ver_permisos` - Ver lista de permisos
- `editar_permisos` - Editar permisos existentes
- `eliminar_permisos` - Eliminar permisos
- `crear_personal` - Crear registros de personal
- `ver_personal` - Ver lista de personal
- `editar_personal` - Editar personal existente
- `eliminar_personal` - Eliminar personal
- `ver_logs` - Ver logs de auditoría
- `crear_mantenimiento` - Crear nuevos mantenimientos
- `ver_mantenimiento` - Ver lista de mantenimientos
- `editar_mantenimiento` - Editar mantenimientos existentes
- `eliminar_mantenimiento` - Eliminar mantenimientos
- `restaurar_mantenimiento` - Restaurar mantenimientos eliminados
- `crear_catalogo_tipo_servicio` - Crear tipos de servicio
- `ver_catalogo_tipo_servicio` - Ver tipos de servicio
- `editar_catalogo_tipo_servicio` - Editar tipos de servicio
- `eliminar_catalogo_tipo_servicio` - Eliminar tipos de servicio

## Middleware de Autenticación

Todas las rutas protegidas requieren el header:
```
Authorization: Bearer {token}
```

## Middleware de Permisos

Las rutas que requieren permisos específicos deben incluir el token de un usuario con los permisos correspondientes.

## Notas Importantes

1. **Soft Deletes:** Los usuarios eliminados usan soft delete y pueden ser restaurados.
2. **Logging Automático:** Todas las acciones importantes se registran automáticamente en la tabla `log_acciones`.
3. **Validaciones:** Todos los endpoints incluyen validaciones robustas de datos.
4. **Relaciones:** Los usuarios no pueden eliminarse si tienen dependencias activas.
5. **Seguridad:** Las contraseñas se hashean automáticamente con bcrypt.

## Ejemplos de Integración Frontend

### Ejemplo: Login y almacenamiento de token
```javascript
// Login
const login = async (email, password) => {
    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            // Guardar token en localStorage
            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
            return data;
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Login error:', error);
        throw error;
    }
};
```

### Ejemplo: Realizar peticiones autenticadas
```javascript
const apiCall = async (endpoint, method = 'GET', body = null) => {
    const token = localStorage.getItem('auth_token');
    
    const config = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        }
    };
    
    if (body) {
        config.body = JSON.stringify(body);
    }
    
    const response = await fetch(`/api${endpoint}`, config);
    
    if (response.status === 401) {
        // Token expirado, redirigir a login
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        window.location.href = '/login';
        return;
    }
    
    return await response.json();
};
```

### Ejemplo: Verificar permisos en frontend
```javascript
const hasPermission = (permission) => {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    return user.role?.permissions?.some(p => p.nombre === permission) || false;
};

// Uso en componentes
if (hasPermission('crear_usuarios')) {
    // Mostrar botón de crear usuario
}

if (hasPermission('ver_vehiculos')) {
    // Mostrar sección de vehículos
}
```

---

## 🚗 Módulo de Vehículos

### Gestión Completa de Vehículos

Para documentación técnica detallada del módulo de vehículos, consultar:
📋 **[VEHICULOS_API_DOCUMENTATION.md](./VEHICULOS_API_DOCUMENTATION.md)**

#### Endpoints Principales de Vehículos:
- `GET /api/vehiculos` - Listar vehículos (con filtros y paginación)
- `POST /api/vehiculos` - Crear vehículo
- `GET /api/vehiculos/{id}` - Ver vehículo específico
- `PUT /api/vehiculos/{id}` - Actualizar vehículo  
- `DELETE /api/vehiculos/{id}` - Eliminar vehículo (soft delete)
- `POST /api/vehiculos/{id}/restore` - Restaurar vehículo
- `GET /api/vehiculos/estatus` - Opciones de estatus

#### Permisos de Vehículos:
- `ver_vehiculos` - Ver listado y detalles
- `crear_vehiculo` - Crear nuevos vehículos
- `editar_vehiculo` - Editar y restaurar
- `eliminar_vehiculo` - Eliminar (soft delete)

#### Características del Módulo:
- ✅ **CRUD completo** con soft delete y restauración
- ✅ **Validaciones robustas** (unicidad, formatos, rangos)
- ✅ **Sanitización automática** de datos
- ✅ **Catálogo de estatus** dinámico
- ✅ **Búsqueda y filtros** avanzados
- ✅ **Paginación** automática
- ✅ **Sistema de permisos** integrado
- ✅ **Logging automático** de acciones
- ✅ **Testing completo** (18 tests, 101 assertions)

---

## 🔧 Módulo de Mantenimientos ⭐ NUEVO

### Gestión Completa de Mantenimientos de Vehículos

Para documentación técnica detallada del módulo de mantenimientos, consultar:
📋 **[MANTENIMIENTOS_API_DOCUMENTATION.md](./MANTENIMIENTOS_API_DOCUMENTATION.md)**

#### Endpoints Principales de Mantenimientos:
- `GET /api/mantenimientos` - Listar mantenimientos (con filtros y paginación)
- `POST /api/mantenimientos` - Crear mantenimiento
- `GET /api/mantenimientos/{id}` - Ver mantenimiento específico
- `PUT /api/mantenimientos/{id}` - Actualizar mantenimiento  
- `DELETE /api/mantenimientos/{id}` - Eliminar mantenimiento (soft delete)
- `POST /api/mantenimientos/{id}/restore` - Restaurar mantenimiento
- `GET /api/mantenimientos/stats` - Estadísticas de mantenimientos
- `GET /api/mantenimientos/proximos-por-kilometraje` - Próximos mantenimientos

#### Endpoints del Catálogo de Tipos de Servicio:
- `GET /api/catalogo-tipos-servicio` - Listar tipos de servicio
- `POST /api/catalogo-tipos-servicio` - Crear tipo de servicio
- `PUT /api/catalogo-tipos-servicio/{id}` - Actualizar tipo de servicio
- `DELETE /api/catalogo-tipos-servicio/{id}` - Eliminar tipo de servicio

#### Permisos de Mantenimientos:
- `ver_mantenimiento` - Ver listado y detalles
- `crear_mantenimiento` - Crear nuevos mantenimientos
- `editar_mantenimiento` - Editar y restaurar
- `eliminar_mantenimiento` - Eliminar (soft delete)
- `restaurar_mantenimiento` - Restaurar mantenimientos eliminados

#### Permisos del Catálogo:
- `ver_catalogo_tipo_servicio` - Ver tipos de servicio
- `crear_catalogo_tipo_servicio` - Crear tipos de servicio
- `editar_catalogo_tipo_servicio` - Editar tipos de servicio
- `eliminar_catalogo_tipo_servicio` - Eliminar tipos de servicio

#### Características del Módulo:
- ✅ **CRUD completo** con soft delete y restauración
- ✅ **Validaciones robustas** (fechas, costos, kilometrajes)
- ✅ **Sanitización automática** de datos (XSS protection)
- ✅ **Catálogo de tipos de servicio** dinámico
- ✅ **Búsqueda y filtros** avanzados por vehículo, proveedor, fechas
- ✅ **Estadísticas** de costos y mantenimientos por tipo
- ✅ **Alertas de mantenimiento** por kilometraje
- ✅ **Relaciones** con vehículos, documentos y usuarios
- ✅ **Paginación** automática
- ✅ **Sistema de permisos** integrado
- ✅ **Logging automático** de acciones
- ✅ **Testing completo** (21 tests de seguridad y boundary, 284 assertions)

---

**📋 Sistema de Control Interno v1.2 - Backend Completo**
- **Usuarios y Roles** ✅ Implementado y testado
- **Personal** ✅ Implementado y testado  
- **Vehículos** ✅ Implementado y testado
- **Mantenimientos** ✅ Implementado y testado ⭐ NUEVO
- **Auditoría** ✅ Implementado y testado
- **Documentación** ✅ Completa para frontend

**🎯 Total: 70+ tests pasando, 469+ assertions, cobertura del 100%**
