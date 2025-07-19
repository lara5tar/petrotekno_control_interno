# 🔐 Nuevos Endpoints de Gestión - API Documentation

## 📋 Overview

Esta documentación cubre los nuevos endpoints completamente implementados para la gestión de permisos, personal y usuarios, disponibles para integración frontend.

---

## 🛡️ Permissions Management API

### Base URL: `/api/permissions`

#### Listar Permisos
```http
GET /api/permissions
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nombre_permiso": "gestionar_vehiculos",
            "descripcion": "Permite crear, editar y eliminar vehículos",
            "roles": [
                {
                    "id": 1,
                    "nombre_rol": "Administrador"
                }
            ]
        }
    ]
}
```

#### Crear Permiso
```http
POST /api/permissions
Authorization: Bearer {token}
Content-Type: application/json

{
    "nombre_permiso": "nuevo_permiso",
    "descripcion": "Descripción del permiso (opcional)"
}
```

#### Ver Permiso Específico
```http
GET /api/permissions/{id}
Authorization: Bearer {token}
```

#### Actualizar Permiso
```http
PUT /api/permissions/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "nombre_permiso": "permiso_actualizado",
    "descripcion": "Nueva descripción"
}
```

#### Eliminar Permiso
```http
DELETE /api/permissions/{id}
Authorization: Bearer {token}
```

**Nota**: No se puede eliminar un permiso que esté asignado a roles.

---

## 👥 Personal Management API

### Base URL: `/api/personal`

#### Listar Personal
```http
GET /api/personal?page=1&per_page=15&estatus=activo&search=Juan
Authorization: Bearer {token}
```

**Parámetros de Query:**
- `page` (opcional): Página actual (default: 1)
- `per_page` (opcional): Registros por página (default: 15, max: 100)  
- `estatus` (opcional): Filtrar por estatus (`activo`, `inactivo`)
- `search` (opcional): Búsqueda por nombre completo

**Respuesta:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "nombre_completo": "Juan Carlos Pérez",
                "estatus": "activo",
                "categoria_id": 1,
                "categoria": {
                    "id": 1,
                    "nombre_categoria": "Operador"
                },
                "created_at": "2025-07-15T10:00:00.000000Z",
                "updated_at": "2025-07-15T10:00:00.000000Z"
            }
        ],
        "first_page_url": "http://localhost:8000/api/personal?page=1",
        "from": 1,
        "last_page": 5,
        "last_page_url": "http://localhost:8000/api/personal?page=5",
        "next_page_url": "http://localhost:8000/api/personal?page=2",
        "path": "http://localhost:8000/api/personal",
        "per_page": 15,
        "prev_page_url": null,
        "to": 15,
        "total": 67
    }
}
```

#### Crear Personal
```http
POST /api/personal
Authorization: Bearer {token}
Content-Type: application/json

{
    "nombre_completo": "María González",
    "estatus": "activo",
    "categoria_id": 1
}
```

**Validaciones:**
- `nombre_completo`: requerido, string, max:255
- `estatus`: requerido, in:activo,inactivo
- `categoria_id`: requerido, debe existir en categorias_personal

#### Ver Personal Específico
```http
GET /api/personal/{id}
Authorization: Bearer {token}
```

#### Actualizar Personal
```http
PUT /api/personal/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "nombre_completo": "María González Actualizada",
    "estatus": "inactivo", 
    "categoria_id": 2
}
```

#### Eliminar Personal
```http
DELETE /api/personal/{id}
Authorization: Bearer {token}
```

**Restricción**: No se puede eliminar personal que tenga un usuario asociado.

---

## 👤 User Management API

### Base URL: `/api/users`

#### Listar Usuarios
```http
GET /api/users?page=1&per_page=15&rol_id=1&search=admin
Authorization: Bearer {token}
```

**Parámetros de Query:**
- `page` (opcional): Página actual
- `per_page` (opcional): Registros por página 
- `rol_id` (opcional): Filtrar por rol específico
- `search` (opcional): Búsqueda por nombre de usuario o email

**Respuesta:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "nombre_usuario": "admin",
                "email": "admin@petrotekno.com",
                "rol_id": 1,
                "personal_id": 1,
                "personal": {
                    "id": 1,
                    "nombre_completo": "Administrador Sistema",
                    "categoria": {
                        "nombre_categoria": "Administrador"
                    }
                },
                "rol": {
                    "id": 1,
                    "nombre_rol": "Administrador"
                },
                "created_at": "2025-07-15T10:00:00.000000Z",
                "updated_at": "2025-07-15T10:00:00.000000Z"
            }
        ],
        "per_page": 15,
        "total": 5
    }
}
```

#### Crear Usuario
```http
POST /api/users
Authorization: Bearer {token}
Content-Type: application/json

{
    "nombre_usuario": "nuevo_usuario",
    "email": "nuevo@petrotekno.com",
    "password": "Password123!",
    "password_confirmation": "Password123!",
    "rol_id": 2,
    "personal_id": 5
}
```

**⚠️ Validaciones Obligatorias:**
- `nombre_usuario`: requerido, único, string, max:255
- `email`: requerido, único, email válido
- `password`: requerido, min:8, max:255, confirmado
- `password_confirmation`: requerido, debe coincidir con password
- `rol_id`: requerido, debe existir en roles
- `personal_id`: opcional, debe existir en personal

#### Ver Usuario Específico
```http
GET /api/users/{id}
Authorization: Bearer {token}
```

#### Actualizar Usuario
```http
PUT /api/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "nombre_usuario": "usuario_actualizado",
    "email": "actualizado@petrotekno.com",
    "password": "NuevoPassword123!",
    "password_confirmation": "NuevoPassword123!",
    "rol_id": 2,
    "personal_id": 5
}
```

**Notas:**
- Los campos `password` y `password_confirmation` son opcionales en actualización
- Si se envía `password`, debe incluir `password_confirmation`

#### Eliminar Usuario
```http
DELETE /api/users/{id}
Authorization: Bearer {token}
```

**Restricción**: Un usuario no puede eliminarse a sí mismo.

---

## 🔐 Matriz de Autorización

| Endpoint | Administrador | Supervisor | Operador |
|----------|---------------|------------|----------|
| `GET /api/permissions` | ✅ | ❌ | ❌ |
| `POST /api/permissions` | ✅ | ❌ | ❌ |
| `PUT /api/permissions/{id}` | ✅ | ❌ | ❌ |
| `DELETE /api/permissions/{id}` | ✅ | ❌ | ❌ |
| `GET /api/personal` | ✅ | ✅ | ❌ |
| `POST /api/personal` | ✅ | ❌ | ❌ |
| `PUT /api/personal/{id}` | ✅ | ❌ | ❌ |
| `DELETE /api/personal/{id}` | ✅ | ❌ | ❌ |
| `GET /api/users` | ✅ | ❌ | ❌ |
| `POST /api/users` | ✅ | ❌ | ❌ |
| `PUT /api/users/{id}` | ✅ | ❌ | ❌ |
| `DELETE /api/users/{id}` | ✅ | ❌ | ❌ |

---

## 📊 Logging Automático

Todas las operaciones CRUD registran automáticamente las acciones en la tabla `log_acciones`:

```json
{
    "usuario_id": 1,
    "accion": "crear_usuario",
    "tabla_afectada": "users", 
    "registro_id": 15,
    "detalles": null,
    "fecha_hora": "2025-07-18T15:30:00.000000Z"
}
```

### Acciones Registradas:
- **Permisos**: `crear_permiso`, `actualizar_permiso`, `eliminar_permiso`
- **Personal**: `crear_personal`, `actualizar_personal`, `eliminar_personal`  
- **Usuarios**: `crear_usuario`, `actualizar_usuario`, `eliminar_usuario`

---

## 🚀 Ejemplos de Integración Frontend

### React/Vue.js Service Class
```javascript
class GestionAPI {
    constructor(baseURL, token) {
        this.baseURL = baseURL;
        this.token = token;
        this.headers = {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
    }

    // PERMISOS
    async getPermisos() {
        const response = await fetch(`${this.baseURL}/api/permissions`, {
            headers: this.headers
        });
        return response.json();
    }

    async createPermiso(data) {
        const response = await fetch(`${this.baseURL}/api/permissions`, {
            method: 'POST',
            headers: this.headers,
            body: JSON.stringify(data)
        });
        return response.json();
    }

    // PERSONAL
    async getPersonal(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${this.baseURL}/api/personal?${params}`, {
            headers: this.headers
        });
        return response.json();
    }

    async createPersonal(data) {
        const response = await fetch(`${this.baseURL}/api/personal`, {
            method: 'POST', 
            headers: this.headers,
            body: JSON.stringify(data)
        });
        return response.json();
    }

    // USUARIOS
    async getUsuarios(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${this.baseURL}/api/users?${params}`, {
            headers: this.headers
        });
        return response.json();
    }

    async createUsuario(data) {
        // Asegurar password_confirmation
        if (data.password && !data.password_confirmation) {
            data.password_confirmation = data.password;
        }
        
        const response = await fetch(`${this.baseURL}/api/users`, {
            method: 'POST',
            headers: this.headers, 
            body: JSON.stringify(data)
        });
        return response.json();
    }

    // Método genérico para eliminar
    async delete(endpoint, id) {
        const response = await fetch(`${this.baseURL}/api/${endpoint}/${id}`, {
            method: 'DELETE',
            headers: this.headers
        });
        return response.json();
    }
}

// Uso
const api = new GestionAPI('http://localhost:8000', userToken);

// Ejemplos
const permisos = await api.getPermisos();
const personal = await api.getPersonal({ estatus: 'activo', page: 1 });
const usuarios = await api.getUsuarios({ rol_id: 1 });
```

### Componente de Formulario (React)
```jsx
const UserForm = ({ onSubmit, initialData = {} }) => {
    const [formData, setFormData] = useState({
        nombre_usuario: '',
        email: '',
        password: '',
        password_confirmation: '',
        rol_id: '',
        personal_id: '',
        ...initialData
    });

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        // Validar password confirmation
        if (formData.password !== formData.password_confirmation) {
            alert('Las contraseñas no coinciden');
            return;
        }

        try {
            await onSubmit(formData);
        } catch (error) {
            console.error('Error:', error);
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <input 
                type="text"
                placeholder="Nombre de usuario"
                value={formData.nombre_usuario}
                onChange={(e) => setFormData({...formData, nombre_usuario: e.target.value})}
                required
            />
            <input 
                type="email"
                placeholder="Email"
                value={formData.email}
                onChange={(e) => setFormData({...formData, email: e.target.value})}
                required
            />
            <input 
                type="password"
                placeholder="Contraseña"
                value={formData.password}
                onChange={(e) => setFormData({...formData, password: e.target.value})}
                required
            />
            <input 
                type="password"
                placeholder="Confirmar contraseña"
                value={formData.password_confirmation}
                onChange={(e) => setFormData({...formData, password_confirmation: e.target.value})}
                required
            />
            <select 
                value={formData.rol_id}
                onChange={(e) => setFormData({...formData, rol_id: e.target.value})}
                required
            >
                <option value="">Seleccionar rol...</option>
                {/* Opciones de roles */}
            </select>
            <button type="submit">Guardar Usuario</button>
        </form>
    );
};
```

---

## ❌ Errores Comunes y Soluciones

### 1. Error 422 - Validation Failed
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "password": ["The password confirmation does not match."]
    }
}
```

**Solución**: Asegurar que `password_confirmation` coincida exactamente con `password`.

### 2. Error 403 - Forbidden  
```json
{
    "message": "This action is unauthorized."
}
```

**Solución**: Verificar que el usuario tenga los permisos necesarios para el endpoint.

### 3. Error 400 - Cannot Delete  
```json
{
    "success": false,
    "message": "No se puede eliminar el permiso porque está asignado a uno o más roles"
}
```

**Solución**: Remover las relaciones antes de eliminar el registro.

---

## 🔄 Estados de Respuesta

### Códigos HTTP Esperados
- `200` - Operación exitosa (GET, PUT, DELETE)
- `201` - Recurso creado exitosamente (POST)
- `400` - Error de lógica de negocio
- `401` - No autenticado
- `403` - No autorizado  
- `404` - Recurso no encontrado
- `422` - Error de validación

---

*Documentación actualizada: Julio 2025*  
*Versión: Issue #16 - Management Endpoints Implementation*
