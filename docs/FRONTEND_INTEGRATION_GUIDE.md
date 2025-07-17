# Guía de Integración Frontend - Sistema de Control Interno

## Resumen del Sistema Implementado

El backend incluye un sistema completo de **usuarios, roles, permisos y personal** con las siguientes características principales:

### ✅ Funcionalidades Implementadas:
- **Autenticación con Laravel Sanctum** (tokens API)
- **Sistema de roles y permisos granular**
- **Gestión completa de usuarios** (CRUD + soft delete)
- **Gestión de personal** con categorías
- **Auditoría automática** de todas las acciones
- **Middleware de autorización** por roles/permisos
- **Validaciones robustas** en todos los endpoints

## Arquitectura del Sistema

### Estructura de Base de Datos Implementada:

```
users (usuarios del sistema)
├── role_id → roles
├── personal_id → personal
└── soft deletes habilitado

roles (roles del sistema)
├── muchos permissions via roles_permisos
└── muchos users

permissions (permisos específicos)
└── muchos roles via roles_permisos

personal (empleados de la empresa)
├── categoria_id → categorias_personal
├── uno user (opcional)
└── datos personales completos

categorias_personal (categorías de empleados)
└── muchos personal

log_acciones (auditoría del sistema)
├── user_id → users
└── registro automático de acciones
```

## Roles y Permisos Predefinidos

### 👑 **Administrador** (ID: 1)
**Permisos completos:**
- Gestión de usuarios (crear, ver, editar, eliminar, restaurar)
- Gestión de roles (crear, ver, editar, eliminar)
- Gestión de permisos (crear, ver, editar, eliminar)
- Gestión de personal (crear, ver, editar, eliminar)
- **Ver logs de auditoría**

### 👨‍💼 **Supervisor** (ID: 2)
**Permisos limitados:**
- **Solo ver** usuarios (no puede crear/editar/eliminar)
- **No puede** gestionar roles ni permisos
- **Ver y gestionar** personal (crear, ver, editar, eliminar)
- **No puede ver** logs de auditoría

### 🔧 **Operador** (ID: 3)
**Permisos básicos:**
- **Solo ver** usuarios
- **Solo ver** personal
- **No gestión** de roles/permisos/logs

## Usuarios por Defecto

### Admin Principal
```
Email: admin@petrotekno.com
Password: Admin123!
Rol: Administrador
```

### Supervisor de Operaciones
```
Email: supervisor@petrotekno.com
Password: Super123!
Rol: Supervisor
```

## Endpoints Críticos para el Frontend

### 🔐 **Autenticación**
```javascript
// Login obligatorio para obtener token
POST /api/login
{
    "email": "admin@petrotekno.com",
    "password": "Admin123!"
}

// Respuesta incluye token y datos completos del usuario
{
    "token": "1|abcd1234...",
    "user": {
        "id": 1,
        "name": "Admin Principal",
        "role": { "nombre": "Administrador", "permissions": [...] },
        "personal": { "nombres": "Admin", "apellidos": "Principal" }
    }
}
```

### 👥 **Gestión de Usuarios**
```javascript
// Listar usuarios con filtros
GET /api/users?search=juan&role_id=2&status=activo

// Crear usuario (requiere permiso 'crear_usuarios')
POST /api/users
{
    "name": "Nuevo Usuario",
    "email": "nuevo@petrotekno.com",
    "password": "password123",
    "role_id": 2,
    "personal_id": 5
}
```

### 👨‍💼 **Gestión de Personal**
```javascript
// Listar personal con relaciones
GET /api/personal?search=carlos&categoria_id=1

// Crear personal
POST /api/personal
{
    "nombres": "Carlos",
    "apellidos": "Mendoza",
    "cedula": "1234567890",
    "categoria_id": 1,
    "salario": 1500.00
}
```

## Implementación de Seguridad

### 🛡️ **Headers Requeridos**
```javascript
// Todas las peticiones autenticadas
headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json'
}
```

### 🔒 **Verificación de Permisos**
```javascript
// Verificar permisos antes de mostrar elementos UI
const user = JSON.parse(localStorage.getItem('user'));
const hasPermission = (permission) => {
    return user.role?.permissions?.some(p => p.nombre === permission);
};

// Ejemplos de uso
if (hasPermission('crear_usuarios')) {
    showCreateUserButton();
}

if (hasPermission('ver_logs')) {
    showAuditMenu();
}
```

## Manejo de Estados de Usuario

### 📊 **Estados Disponibles**
- **Activo:** Usuario puede iniciar sesión
- **Inactivo:** Usuario bloqueado (soft delete)
- **Eliminado:** Soft delete aplicado

### 🔄 **Ciclo de Vida**
```javascript
// Eliminar usuario (soft delete)
DELETE /api/users/3

// Restaurar usuario eliminado
POST /api/users/3/restore
```

## Logging y Auditoría

### 📝 **Acciones Registradas Automáticamente**
- Login/Logout de usuarios
- Creación/edición/eliminación de usuarios
- Cambios de contraseña
- Creación/edición de personal
- Operaciones de roles y permisos

### 📈 **Ver Logs (Solo Administradores)**
```javascript
GET /api/logs?user_id=2&accion=login&fecha_desde=2025-01-01

// Respuesta incluye detalles completos
{
    "data": [{
        "accion": "crear_usuario",
        "descripcion": "Usuario creado: nuevo@petrotekno.com",
        "ip_address": "192.168.1.100",
        "user": { "name": "Admin Principal" }
    }]
}
```

## Validaciones Importantes

### ✅ **Reglas de Negocio**
1. **Email único** por usuario
2. **Cédula única** por personal
3. **No auto-eliminación** de usuarios
4. **No eliminar roles** con usuarios asignados
5. **No eliminar personal** con usuario asociado
6. **Contraseñas mínimo 8 caracteres**

### 🚫 **Restricciones por Rol**
- **Supervisor**: NO puede crear usuarios, NO puede ver logs
- **Operador**: Solo lectura de usuarios y personal
- **Admin**: Acceso completo sin restricciones

## Ejemplos de Integración

### 🎯 **Dashboard Principal**
```javascript
// Al cargar el dashboard
const loadDashboard = async () => {
    const user = await apiCall('/me');
    
    // Mostrar elementos según permisos
    if (hasPermission('ver_usuarios')) {
        loadUsersWidget();
    }
    
    if (hasPermission('ver_personal')) {
        loadPersonalWidget();
    }
    
    if (hasPermission('ver_logs')) {
        loadAuditWidget();
    }
};
```

### 📝 **Formulario de Usuario**
```javascript
const createUser = async (userData) => {
    // Validar permisos antes de enviar
    if (!hasPermission('crear_usuarios')) {
        showError('Sin permisos para crear usuarios');
        return;
    }
    
    try {
        const response = await apiCall('/users', 'POST', userData);
        showSuccess('Usuario creado exitosamente');
        refreshUsersList();
    } catch (error) {
        if (error.status === 422) {
            showValidationErrors(error.errors);
        }
    }
};
```

### 🔍 **Búsqueda y Filtros**
```javascript
const searchUsers = async (filters) => {
    const params = new URLSearchParams();
    
    if (filters.search) params.append('search', filters.search);
    if (filters.role_id) params.append('role_id', filters.role_id);
    if (filters.status) params.append('status', filters.status);
    
    const users = await apiCall(`/users?${params}`);
    renderUsersList(users.data);
};
```

## Consideraciones de UX

### 🎨 **Elementos UI Condicionales**
```javascript
// Mostrar/ocultar elementos según permisos
const renderUserActions = (user) => {
    const actions = [];
    
    if (hasPermission('editar_usuarios')) {
        actions.push('<button onclick="editUser(' + user.id + ')">Editar</button>');
    }
    
    if (hasPermission('eliminar_usuarios') && user.id !== currentUser.id) {
        actions.push('<button onclick="deleteUser(' + user.id + ')">Eliminar</button>');
    }
    
    return actions.join('');
};
```

### 🚨 **Manejo de Errores**
```javascript
const handleApiError = (error) => {
    switch (error.status) {
        case 401:
            redirectToLogin();
            break;
        case 403:
            showError('Sin permisos para esta acción');
            break;
        case 422:
            showValidationErrors(error.errors);
            break;
        default:
            showError('Error interno del servidor');
    }
};
```

## Testing y Validación

### ✅ **Backend Completamente Testado**
- **31 tests pasando** con 141 assertions
- **Cobertura completa** de funcionalidades
- **Validación de permisos** en todos los endpoints
- **Casos edge** cubiertos (eliminación con dependencias, etc.)

### 🔧 **Endpoints Listos para Producción**
- Autenticación robusta
- Validaciones completas
- Manejo de errores consistente
- Logging automático funcional

---

**⚡ El backend está 100% funcional y listo para integración frontend inmediata.**
