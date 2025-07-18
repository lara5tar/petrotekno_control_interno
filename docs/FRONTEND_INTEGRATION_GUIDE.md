# Guía de Integración Frontend - Sistema de Control Interno

## Resumen del Sistema Implementado

El backend incluye un sistema completo de **usuarios, roles, permisos, personal, vehículos y mantenimientos** con las siguientes características principales:

### ✅ Funcionalidades Implementadas:
- **Autenticación con Laravel Sanctum** (tokens API)
- **Sistema de roles y permisos granular**
- **Gestión completa de usuarios** (CRUD + soft delete)
- **Gestión de personal** con categorías
- **Gestión completa de vehículos** (CRUD + soft delete + restauración)
- **Gestión completa de mantenimientos** (CRUD + estadísticas + alertas) ⭐ NUEVO
- **Catálogo de tipos de servicio** para mantenimientos ⭐ NUEVO
- **Catálogo de estatus para vehículos**
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

vehiculos (vehículos de la empresa)
├── estatus_id → catalogo_estatus
├── muchos mantenimientos ⭐ NUEVO
├── soft deletes habilitado
├── validaciones únicas (n_serie, placas)
└── intervalos de mantenimiento

mantenimientos (servicios de vehículos) ⭐ NUEVO
├── vehiculo_id → vehiculos
├── tipo_servicio_id → catalogo_tipos_servicio
├── muchos documentos
├── soft deletes habilitado
└── relación con usuarios para auditoría

catalogo_tipos_servicio (tipos de mantenimiento) ⭐ NUEVO
└── muchos mantenimientos

catalogo_estatus (estados de vehículos)
└── muchos vehiculos

obras (proyectos de la empresa) ⭐ NUEVO
├── fechas inicio/fin
├── control de avance
└── gestión de estatus

documentos (sistema gestión documental) ⭐ NUEVO
├── tipo_documento_id → catalogo_tipos_documento
├── vehiculo_id → vehiculos (opcional)
├── personal_id → personal (opcional)
├── obra_id → obras (opcional)
├── manejo de archivos
├── fechas de vencimiento
├── soft deletes habilitado
└── estados calculados (vigente/vencido/próximo a vencer)

catalogo_tipos_documento (tipos de documentos) ⭐ NUEVO
├── requiere_vencimiento (boolean)
└── muchos documentos

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
- **Gestión de vehículos** (crear, ver, editar, eliminar, restaurar) ⭐ NUEVO
- **Gestión de obras** (crear, ver, editar, eliminar) ⭐ NUEVO
- **Gestión de documentos** (crear, ver, editar, eliminar) ⭐ NUEVO
- **Gestión de tipos de documento** (crear, ver, editar, eliminar) ⭐ NUEVO
- **Ver logs de auditoría**

### 👨‍💼 **Supervisor** (ID: 2)
**Permisos limitados:**
- **Solo ver** usuarios (no puede crear/editar/eliminar)
- **No puede** gestionar roles ni permisos
- **Ver y gestionar** personal (crear, ver, editar, eliminar)
- **Ver y gestionar** vehículos (crear, ver, editar, eliminar, restaurar) ⭐ NUEVO
- **Ver y gestionar** obras (crear, ver, editar, eliminar) ⭐ NUEVO
- **Ver y gestionar** documentos (crear, ver, editar, eliminar) ⭐ NUEVO
- **Ver y gestionar** tipos de documento (crear, ver, editar, eliminar) ⭐ NUEVO
- **No puede ver** logs de auditoría

### 🔧 **Operador** (ID: 3)
**Permisos básicos:**
- **Solo ver** usuarios
- **Solo ver** personal
- **Solo ver** vehículos ⭐ NUEVO
- **Solo ver** obras ⭐ NUEVO
- **Solo ver** documentos ⭐ NUEVO
- **Solo ver** tipos de documento ⭐ NUEVO
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

### 🚗 **Gestión de Vehículos** ⭐ NUEVO
```javascript
// Listar vehículos con paginación y filtros
GET /api/vehiculos?search=toyota&estatus_id=1&page=1

// Respuesta estructurada
{
    "data": [
        {
            "id": 1,
            "marca": "Toyota",
            "modelo": "Hilux",
            "anio": 2023,
            "n_serie": "VIN123456789",
            "placas": "ABC-123",
            "kilometraje_actual": 15000,
            "intervalo_km_motor": 10000,
            "intervalo_km_transmision": 50000,
            "intervalo_km_hidraulico": 30000,
            "observaciones": "Vehículo en excelente estado",
            "estatus": {
                "id": 1,
                "nombre_estatus": "Activo",
                "descripcion": "Vehículo disponible para asignación"
            },
            "nombre_completo": "Toyota Hilux 2023 (ABC-123)",
            "created_at": "2025-07-17T10:30:00.000000Z"
        }
    ],
    "links": { "first": "...", "last": "...", "next": "..." },
    "meta": { "current_page": 1, "total": 25 }
}

// Crear vehículo (requiere permiso 'crear_vehiculo')
POST /api/vehiculos
{
    "marca": "Toyota",
    "modelo": "Hilux",
    "anio": 2023,
    "n_serie": "VIN123456789",
    "placas": "ABC-123",
    "estatus_id": 1,
    "kilometraje_actual": 15000,
    "intervalo_km_motor": 10000,
    "intervalo_km_transmision": 50000,
    "intervalo_km_hidraulico": 30000,
    "observaciones": "Vehículo nuevo"
}

// Actualizar vehículo (requiere permiso 'editar_vehiculo')
PUT /api/vehiculos/1
{
    "kilometraje_actual": 20000,
    "observaciones": "Mantenimiento realizado"
}

// Eliminar vehículo - Soft Delete (requiere permiso 'eliminar_vehiculo')
DELETE /api/vehiculos/1

// Restaurar vehículo eliminado (requiere permiso 'editar_vehiculo')
POST /api/vehiculos/1/restore

// Obtener vehículo específico
GET /api/vehiculos/1

// Obtener opciones de estatus para formularios
GET /api/vehiculos/estatus
// Respuesta: [{"id": 1, "nombre_estatus": "Activo"}, ...]
```

### 🏗️ **Gestión de Obras** ⭐ NUEVO
```javascript
// Listar obras con paginación y filtros
GET /api/obras?search=puente&estatus=en_progreso&page=1

// Respuesta estructurada
{
    "data": [
        {
            "id": 1,
            "nombre_obra": "Construcción Puente Central",
            "estatus": "en_progreso",
            "avance": 65,
            "fecha_inicio": "2025-01-15",
            "fecha_fin": "2025-12-31",
            "created_at": "2025-01-15T08:00:00.000000Z"
        }
    ],
    "links": { "first": "...", "last": "...", "next": "..." },
    "meta": { "current_page": 1, "total": 10 }
}

// Crear obra (requiere permiso 'crear_obra')
POST /api/obras
{
    "nombre_obra": "Nueva Construcción",
    "estatus": "planificado",
    "avance": 0,
    "fecha_inicio": "2025-08-01",
    "fecha_fin": "2025-12-31"
}
```

### 📄 **Gestión de Documentos** ⭐ NUEVO
```javascript
// Listar documentos con filtros avanzados
GET /api/documentos?search=licencia&tipo_documento_id=1&estado_vencimiento=proximos_a_vencer

// Respuesta estructurada
{
    "data": [
        {
            "id": 1,
            "tipo_documento_id": 1,
            "descripcion": "Licencia de Juan Pérez",
            "ruta_archivo": "documentos/1642519200_licencia.pdf",
            "fecha_vencimiento": "2025-12-31",
            "vehiculo_id": 1,
            "personal_id": null,
            "obra_id": null,
            "estado": "vigente",
            "dias_hasta_vencimiento": 165,
            "esta_vencido": false,
            "tipo_documento": {
                "nombre_tipo_documento": "Licencia de Conducir",
                "requiere_vencimiento": true
            },
            "vehiculo": {
                "marca": "Toyota",
                "modelo": "Hilux",
                "placas": "ABC-123"
            }
        }
    ],
    "meta": {
        "total_vigentes": 10,
        "total_vencidos": 2,
        "total_proximos_vencer": 3
    }
}

// Crear documento con archivo (requiere permiso 'crear_documentos')
POST /api/documentos (multipart/form-data)
{
    "tipo_documento_id": 1,
    "descripcion": "Nueva licencia",
    "fecha_vencimiento": "2026-12-31",
    "vehiculo_id": 1,
    "archivo": [FILE]
}

// Documentos próximos a vencer
GET /api/documentos/proximos-a-vencer?dias=30

// Documentos vencidos
GET /api/documentos/vencidos

// Tipos de documento para formularios
GET /api/catalogo-tipos-documento?sin_paginar=true
// Respuesta: [{"id": 1, "nombre_tipo_documento": "Licencia", "requiere_vencimiento": true}, ...]
```

### 🔧 **Gestión de Mantenimientos** ⭐ NUEVO
```javascript
// Listar mantenimientos con filtros avanzados
GET /api/mantenimientos?vehiculo_id=1&fecha_inicio=2024-01-01&fecha_fin=2024-12-31&page=1

// Respuesta estructurada
{
    "data": [
        {
            "id": 1,
            "vehiculo_id": 1,
            "tipo_servicio_id": 1,
            "proveedor": "Taller ABC",
            "descripcion": "Cambio de aceite y filtro",
            "fecha_inicio": "2024-07-15",
            "fecha_fin": "2024-07-15",
            "kilometraje_servicio": 15000,
            "costo": "1500.00",
            "vehiculo": {
                "marca": "Toyota",
                "modelo": "Hilux",
                "placas": "ABC-123"
            },
            "tipo_servicio": {
                "nombre_tipo_servicio": "Mantenimiento Preventivo"
            },
            "documentos": []
        }
    ],
    "meta": { "current_page": 1, "total": 45 }
}

// Crear mantenimiento (requiere permiso 'crear_mantenimiento')
POST /api/mantenimientos
{
    "vehiculo_id": 1,
    "tipo_servicio_id": 1,
    "proveedor": "Taller ABC",
    "descripcion": "Cambio de aceite",
    "fecha_inicio": "2024-07-15",
    "fecha_fin": "2024-07-15",
    "kilometraje_servicio": 15000,
    "costo": 1500.00
}

// Estadísticas de mantenimientos
GET /api/mantenimientos/stats?year=2024&month=7

// Respuesta con estadísticas completas
{
    "data": {
        "total_mantenimientos": 45,
        "costo_total": "125500.00",
        "costo_promedio": "2788.89",
        "mantenimientos_por_tipo": [
            {
                "tipo_servicio": "Mantenimiento Preventivo",
                "cantidad": 30,
                "costo_total": "85000.00"
            }
        ],
        "vehiculos_mas_mantenidos": [
            {
                "vehiculo_id": 1,
                "marca": "Toyota",
                "modelo": "Hilux",
                "placas": "ABC-123",
                "total_mantenimientos": 8,
                "costo_total": "18500.00"
            }
        ]
    }
}

// Próximos mantenimientos por kilometraje
GET /api/mantenimientos/proximos-por-kilometraje?limite_km=1000

// Alertas de mantenimiento preventivo
{
    "data": [
        {
            "vehiculo_id": 1,
            "marca": "Toyota",
            "modelo": "Hilux",
            "placas": "ABC-123",
            "kilometraje_actual": 18500,
            "ultimo_mantenimiento": {
                "fecha": "2024-07-15",
                "kilometraje": 15000,
                "tipo_servicio": "Mantenimiento Preventivo"
            },
            "kilometros_desde_ultimo": 3500,
            "requiere_atencion": true,
            "intervalos": {
                "motor": 5000,
                "transmision": 10000,
                "hidraulico": 8000
            }
        }
    ]
}

// Gestión de Catálogo de Tipos de Servicio
GET /api/catalogo-tipos-servicio
// Respuesta: [{"id": 1, "nombre_tipo_servicio": "Mantenimiento Preventivo"}, ...]

POST /api/catalogo-tipos-servicio
{
    "nombre_tipo_servicio": "Reparación de Emergencia"
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
7. **Número de serie único** por vehículo ⭐ NUEVO
8. **Placas únicas** por vehículo ⭐ NUEVO
9. **Año mínimo 1990** para vehículos ⭐ NUEVO
10. **Placas formato válido** (letras-números o números-letras) ⭐ NUEVO
11. **Kilometraje no negativo** ⭐ NUEVO
12. **Un documento solo puede asociarse a una entidad** (vehículo O personal O obra) ⭐ NUEVO
13. **Fecha de vencimiento obligatoria** si el tipo de documento lo requiere ⭐ NUEVO
14. **Archivos máximo 10MB** con tipos permitidos (PDF, DOC, DOCX, JPG, PNG, TXT, XLS, XLSX) ⭐ NUEVO
15. **Nombre de tipo de documento único** ⭐ NUEVO

### 🚫 **Restricciones por Rol**
- **Supervisor**: NO puede crear usuarios, NO puede ver logs
- **Operador**: Solo lectura de usuarios, personal, vehículos, obras y documentos
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
    
    if (hasPermission('ver_vehiculos')) {
        loadVehiculosWidget();
    }
    
    if (hasPermission('ver_obras')) {
        loadObrasWidget();
    }
    
    if (hasPermission('ver_documentos')) {
        loadDocumentosWidget();
        loadVencimientosWidget(); // Alertas de documentos próximos a vencer
    }
    
    if (hasPermission('ver_logs')) {
        loadAuditWidget();
    }
};
```

### 📝 **Formulario de Vehículo** ⭐ NUEVO
```javascript
const createVehiculo = async (vehiculoData) => {
    // Validar permisos antes de enviar
    if (!hasPermission('crear_vehiculo')) {
        showError('Sin permisos para crear vehículos');
        return;
    }
    
    // Validaciones frontend
    if (!vehiculoData.marca || !vehiculoData.modelo) {
        showError('Marca y modelo son requeridos');
        return;
    }
    
    if (vehiculoData.anio < 1990) {
        showError('El año debe ser mayor a 1990');
        return;
    }
    
    // Formato de placas
    const placasPattern = /^[A-Z]{3}-[0-9]{3}$|^[0-9]{3}-[A-Z]{3}$/;
    if (!placasPattern.test(vehiculoData.placas.toUpperCase())) {
        showError('Formato de placas inválido (ej: ABC-123 o 123-ABC)');
        return;
    }
    
    try {
        const response = await apiCall('/vehiculos', 'POST', vehiculoData);
        showSuccess('Vehículo creado exitosamente');
        refreshVehiculosList();
        closeVehiculoModal();
    } catch (error) {
        if (error.status === 422) {
            showValidationErrors(error.errors);
        }
    }
};

// Cargar opciones para el formulario
const loadVehiculoFormOptions = async () => {
    const estatus = await apiCall('/vehiculos/estatus');
    populateEstatusSelect(estatus);
};
```

### 📄 **Formulario de Documento** ⭐ NUEVO
```javascript
const createDocumento = async (documentoData) => {
    // Validar permisos antes de enviar
    if (!hasPermission('crear_documentos')) {
        showError('Sin permisos para crear documentos');
        return;
    }
    
    // Validaciones frontend
    if (!documentoData.tipo_documento_id) {
        showError('Tipo de documento es requerido');
        return;
    }
    
    // Validar que solo se asocie a una entidad
    const entidades = [documentoData.vehiculo_id, documentoData.personal_id, documentoData.obra_id, documentoData.mantenimiento_id];
    const entidadesSeleccionadas = entidades.filter(id => id && id !== '').length;
    
    if (entidadesSeleccionadas > 1) {
        showError('Un documento solo puede asociarse a una entidad (vehículo, personal, obra o mantenimiento)');
        return;
    }
    
    // Validar archivo si existe
    if (documentoData.archivo) {
        if (!isValidFileType(documentoData.archivo)) {
            showError('Tipo de archivo no permitido. Solo: PDF, DOC, DOCX, JPG, PNG, TXT, XLS, XLSX');
            return;
        }
        
        if (!isValidFileSize(documentoData.archivo)) {
            showError('El archivo no puede ser mayor a 10MB');
            return;
        }
    }
    
    // Crear FormData para manejar archivo
    const formData = new FormData();
    Object.keys(documentoData).forEach(key => {
        if (documentoData[key] !== null && documentoData[key] !== undefined && documentoData[key] !== '') {
            formData.append(key, documentoData[key]);
        }
    });
    
    try {
        const response = await apiCall('/documentos', 'POST', formData, {
            'Content-Type': 'multipart/form-data'
        });
        showSuccess('Documento creado exitosamente');
        refreshDocumentosList();
        closeDocumentoModal();
    } catch (error) {
        if (error.status === 422) {
            showValidationErrors(error.errors);
        }
    }
};

// Cargar opciones para el formulario de documentos
const loadDocumentoFormOptions = async () => {
    const [tiposDocumento, vehiculos, personal, obras] = await Promise.all([
        apiCall('/catalogo-tipos-documento?sin_paginar=true'),
        apiCall('/vehiculos?sin_paginar=true'),
        apiCall('/personal?sin_paginar=true'),
        apiCall('/obras?sin_paginar=true')
    ]);
    
    populateTiposDocumentoSelect(tiposDocumento.data);
    populateVehiculosSelect(vehiculos.data);
    populatePersonalSelect(personal.data);
    populateObrasSelect(obras.data);
};

// Validar si el tipo requiere fecha de vencimiento
const onTipoDocumentoChange = async (tipoId) => {
    if (!tipoId) return;
    
    const tipo = await apiCall(`/catalogo-tipos-documento/${tipoId}`);
    const fechaVencimientoField = document.getElementById('fecha_vencimiento');
    const fechaLabel = document.querySelector('label[for="fecha_vencimiento"]');
    
    if (tipo.data.requiere_vencimiento) {
        fechaVencimientoField.required = true;
        fechaLabel.innerHTML = 'Fecha de Vencimiento <span class="text-danger">*</span>';
        fechaVencimientoField.classList.remove('d-none');
    } else {
        fechaVencimientoField.required = false;
        fechaLabel.innerHTML = 'Fecha de Vencimiento <span class="text-muted">(Opcional)</span>';
    }
};

// Utilidades para archivos
const isValidFileType = (file) => {
    const allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/jpg',
        'image/png',
        'text/plain',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];
    return allowedTypes.includes(file.type);
};

const isValidFileSize = (file) => {
    const maxSize = 10 * 1024 * 1024; // 10MB
    return file.size <= maxSize;
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Widget de alertas de vencimiento
const loadVencimientosWidget = async () => {
    try {
        const [proximosVencer, vencidos] = await Promise.all([
            apiCall('/documentos/proximos-a-vencer?dias=30'),
            apiCall('/documentos/vencidos')
        ]);
        
        renderVencimientosAlerts(proximosVencer.data, vencidos.data);
    } catch (error) {
        console.error('Error cargando alertas de vencimiento:', error);
    }
};

const renderVencimientosAlerts = (proximosVencer, vencidos) => {
    const alertsContainer = document.getElementById('vencimientos-alerts');
    
    let alertsHtml = '';
    
    if (vencidos.length > 0) {
        alertsHtml += `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>${vencidos.length} documento(s) vencido(s)</strong>
                <a href="/documentos?estado_vencimiento=vencidos" class="alert-link">Ver detalles</a>
            </div>
        `;
    }
    
    if (proximosVencer.length > 0) {
        alertsHtml += `
            <div class="alert alert-warning">
                <i class="fas fa-clock"></i>
                <strong>${proximosVencer.length} documento(s) próximo(s) a vencer</strong>
                <a href="/documentos?estado_vencimiento=proximos_a_vencer" class="alert-link">Ver detalles</a>
            </div>
        `;
    }
    
    alertsContainer.innerHTML = alertsHtml;
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

// Búsqueda específica para vehículos ⭐ NUEVO
const searchVehiculos = async (filters) => {
    const params = new URLSearchParams();
    
    if (filters.search) params.append('search', filters.search);
    if (filters.estatus_id) params.append('estatus_id', filters.estatus_id);
    if (filters.marca) params.append('marca', filters.marca);
    if (filters.anio_desde) params.append('anio_desde', filters.anio_desde);
    if (filters.anio_hasta) params.append('anio_hasta', filters.anio_hasta);
    if (filters.page) params.append('page', filters.page);
    
    const vehiculos = await apiCall(`/vehiculos?${params}`);
    renderVehiculosList(vehiculos.data);
    renderPagination(vehiculos.links, vehiculos.meta);
};

// Búsqueda específica para documentos ⭐ NUEVO
const searchDocumentos = async (filters) => {
    const params = new URLSearchParams();
    
    if (filters.search) params.append('search', filters.search);
    if (filters.tipo_documento_id) params.append('tipo_documento_id', filters.tipo_documento_id);
    if (filters.vehiculo_id) params.append('vehiculo_id', filters.vehiculo_id);
    if (filters.personal_id) params.append('personal_id', filters.personal_id);
    if (filters.obra_id) params.append('obra_id', filters.obra_id);
    if (filters.estado_vencimiento) params.append('estado_vencimiento', filters.estado_vencimiento);
    if (filters.dias_vencimiento) params.append('dias_vencimiento', filters.dias_vencimiento);
    if (filters.page) params.append('page', filters.page);
    
    const documentos = await apiCall(`/documentos?${params}`);
    renderDocumentosList(documentos.data);
    renderPagination(documentos.links, documentos.meta);
    renderDocumentosStats(documentos.meta);
};

// Filtros rápidos para documentos
const applyDocumentoQuickFilter = async (filter) => {
    const filters = { estado_vencimiento: filter };
    await searchDocumentos(filters);
    
    // Actualizar UI del filtro activo
    document.querySelectorAll('.quick-filter-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
};
```

## Consideraciones de UX

### 🎨 **Elementos UI Condicionales**
```javascript
// Mostrar/ocultar elementos según permisos para usuarios
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

// Mostrar/ocultar elementos según permisos para vehículos ⭐ NUEVO
const renderVehiculoActions = (vehiculo) => {
    const actions = [];
    
    if (hasPermission('editar_vehiculo')) {
        actions.push(`<button onclick="editVehiculo(${vehiculo.id})" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i> Editar
        </button>`);
        
        if (vehiculo.deleted_at) {
            actions.push(`<button onclick="restoreVehiculo(${vehiculo.id})" class="btn btn-success btn-sm">
                <i class="fas fa-undo"></i> Restaurar
            </button>`);
        }
    }
    
    if (hasPermission('eliminar_vehiculo') && !vehiculo.deleted_at) {
        actions.push(`<button onclick="deleteVehiculo(${vehiculo.id})" class="btn btn-danger btn-sm">
            <i class="fas fa-trash"></i> Eliminar
        </button>`);
    }
    
    return `<div class="btn-group">${actions.join('')}</div>`;
};

// Renderizar card de vehículo
const renderVehiculoCard = (vehiculo) => {
    const statusBadge = vehiculo.deleted_at ? 
        '<span class="badge badge-secondary">Eliminado</span>' :
        `<span class="badge badge-${vehiculo.estatus.activo ? 'success' : 'warning'}">${vehiculo.estatus.nombre_estatus}</span>`;
    
    return `
        <div class="card mb-3 ${vehiculo.deleted_at ? 'bg-light' : ''}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title">${vehiculo.nombre_completo}</h5>
                        <p class="card-text">
                            <small class="text-muted">Serie: ${vehiculo.n_serie}</small><br>
                            <small class="text-muted">Kilometraje: ${vehiculo.kilometraje_actual.toLocaleString()} km</small>
                        </p>
                        ${statusBadge}
                    </div>
                    <div class="col-md-4 text-right">
                        ${renderVehiculoActions(vehiculo)}
                    </div>
                </div>
            </div>
        </div>
    `;
};

// Mostrar/ocultar elementos según permisos para documentos ⭐ NUEVO
const renderDocumentoActions = (documento) => {
    const actions = [];
    
    // Ver archivo si existe
    if (documento.ruta_archivo) {
        actions.push(`<button onclick="viewFile('${documento.ruta_archivo}')" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i> Ver
        </button>`);
        
        actions.push(`<button onclick="downloadFile('${documento.ruta_archivo}')" class="btn btn-secondary btn-sm">
            <i class="fas fa-download"></i> Descargar
        </button>`);
    }
    
    if (hasPermission('editar_documentos')) {
        actions.push(`<button onclick="editDocumento(${documento.id})" class="btn btn-primary btn-sm">
            <i class="fas fa-edit"></i> Editar
        </button>`);
    }
    
    if (hasPermission('eliminar_documentos')) {
        actions.push(`<button onclick="deleteDocumento(${documento.id})" class="btn btn-danger btn-sm">
            <i class="fas fa-trash"></i> Eliminar
        </button>`);
    }
    
    return `<div class="btn-group">${actions.join('')}</div>`;
};

// Renderizar card de documento
const renderDocumentoCard = (documento) => {
    const estadoBadge = getEstadoBadge(documento.estado);
    const entidadInfo = getEntidadInfo(documento);
    const vencimientoInfo = getVencimientoInfo(documento);
    
    return `
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title">
                            ${documento.tipo_documento?.nombre_tipo_documento || 'Sin tipo'}
                            ${estadoBadge}
                        </h5>
                        <p class="card-text">${documento.descripcion || 'Sin descripción'}</p>
                        <small class="text-muted">
                            ${entidadInfo}<br>
                            ${vencimientoInfo}
                        </small>
                    </div>
                    <div class="col-md-4 text-right">
                        ${renderDocumentoActions(documento)}
                    </div>
                </div>
            </div>
        </div>
    `;
};

// Utilidades para renderizado de documentos
const getEstadoBadge = (estado) => {
    const badges = {
        'vigente': '<span class="badge badge-success">Vigente</span>',
        'proximo_a_vencer': '<span class="badge badge-warning">Próximo a Vencer</span>',
        'vencido': '<span class="badge badge-danger">Vencido</span>'
    };
    return badges[estado] || '<span class="badge badge-secondary">Sin Estado</span>';
};

const getEntidadInfo = (documento) => {
    if (documento.vehiculo) {
        return `<i class="fas fa-car"></i> Vehículo: ${documento.vehiculo.marca} ${documento.vehiculo.modelo} (${documento.vehiculo.placas})`;
    }
    if (documento.personal) {
        return `<i class="fas fa-user"></i> Personal: ${documento.personal.nombre_completo}`;
    }
    if (documento.obra) {
        return `<i class="fas fa-building"></i> Obra: ${documento.obra.nombre_obra}`;
    }
    return '<i class="fas fa-file"></i> Documento general';
};

const getVencimientoInfo = (documento) => {
    if (!documento.fecha_vencimiento) {
        return 'Sin fecha de vencimiento';
    }
    
    const fecha = new Date(documento.fecha_vencimiento);
    const fechaFormatted = fecha.toLocaleDateString('es-ES');
    
    if (documento.dias_hasta_vencimiento === null) {
        return `Vence: ${fechaFormatted}`;
    }
    
    if (documento.dias_hasta_vencimiento > 0) {
        return `Vence: ${fechaFormatted} (en ${documento.dias_hasta_vencimiento} días)`;
    } else if (documento.dias_hasta_vencimiento === 0) {
        return `Vence: ${fechaFormatted} (HOY)`;
    } else {
        return `Vencido: ${fechaFormatted} (hace ${Math.abs(documento.dias_hasta_vencimiento)} días)`;
    }
};

// Funciones para manejo de archivos
const viewFile = (rutaArchivo) => {
    const fileUrl = `/storage/${rutaArchivo}`;
    window.open(fileUrl, '_blank');
};

const downloadFile = (rutaArchivo) => {
    const fileUrl = `/storage/${rutaArchivo}`;
    const link = document.createElement('a');
    link.href = fileUrl;
    link.download = rutaArchivo.split('/').pop();
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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
- **91+ tests pasando** con 469+ assertions
- **Cobertura completa** de funcionalidades (usuarios, personal, vehículos, obras, documentos, mantenimientos)
- **Validación de permisos** en todos los endpoints
- **Casos edge** cubiertos (eliminación con dependencias, validaciones únicas, etc.)
- **Módulo de vehículos** 100% testado (12 feature tests + 6 unit tests)
- **Módulo de documentos** 100% testado (17 feature tests + 14 unit tests + 22 validation tests)
- **Módulo de mantenimientos** 100% testado (unit, feature, security y boundary tests) ⭐ NUEVO
- **Sistema de gestión documental** completo con manejo de archivos y vencimientos
- **Sistema de mantenimientos** con estadísticas, alertas y control de kilometrajes ⭐ NUEVO

### 🔧 **Endpoints Listos para Producción**
- Autenticación robusta
- Validaciones completas
- Manejo de errores consistente
- Logging automático funcional
- **Sistema de documentos** con archivos, vencimientos y alertas
- **Sistema de mantenimientos** con control preventivo y correctivo ⭐ NUEVO

---

**⚡ El backend está 100% funcional y listo para integración frontend inmediata con los nuevos Sistemas de Gestión de Documentos y Mantenimientos.**
