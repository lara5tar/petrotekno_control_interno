# API de Mantenimientos - Documentación para Frontend

## Descripción General

El módulo de Mantenimientos permite gestionar el historial de servicios y mantenimientos realizados a los vehículos de la flota. Esta documentación proporciona todos los detalles necesarios para integrar el frontend con la API de mantenimientos.

## Endpoints Disponibles

### Base URL
```
/api/mantenimientos
/api/catalogo-tipos-servicio
```

## 🚗 Mantenimientos

### 1. Listar Mantenimientos
**GET** `/api/mantenimientos`

Obtiene una lista paginada de mantenimientos con filtros opcionales.

#### Headers Requeridos
```http
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

#### Parámetros de Consulta (Query Parameters)
```javascript
{
  // Paginación
  "page": 1,              // Número de página (opcional, default: 1)
  "per_page": 15,         // Elementos por página (opcional, default: 15, max: 100)
  
  // Filtros
  "vehiculo_id": 123,     // ID del vehículo (opcional)
  "tipo_servicio_id": 1,  // ID del tipo de servicio (opcional)
  "proveedor": "Taller X", // Nombre del proveedor (búsqueda parcial, opcional)
  "fecha_inicio": "2024-01-01", // Filtro por fecha de inicio (opcional)
  "fecha_fin": "2024-12-31",    // Filtro por fecha de fin (opcional)
  
  // Ordenamiento
  "sort_by": "fecha_inicio",    // Campo de ordenamiento (opcional)
  "sort_direction": "desc"      // Dirección: asc|desc (opcional, default: desc)
}
```

#### Respuesta Exitosa (200)
```json
{
  "data": [
    {
      "id": 1,
      "vehiculo_id": 123,
      "tipo_servicio_id": 1,
      "proveedor": "Taller Automotriz ABC",
      "descripcion": "Cambio de aceite y filtro",
      "fecha_inicio": "2024-07-15",
      "fecha_fin": "2024-07-15",
      "kilometraje_servicio": 15000,
      "costo": "1500.00",
      "fecha_creacion": "2024-07-15T10:30:00.000000Z",
      "fecha_actualizacion": "2024-07-15T10:30:00.000000Z",
      "vehiculo": {
        "id": 123,
        "marca": "Toyota",
        "modelo": "Hilux",
        "anio": 2020,
        "placas": "ABC-123"
      },
      "tipo_servicio": {
        "id": 1,
        "nombre_tipo_servicio": "Mantenimiento Preventivo"
      },
      "documentos": [
        {
          "id": 456,
          "descripcion": "Factura de servicio",
          "ruta_archivo": "/storage/documentos/factura_servicio_1.pdf"
        }
      ]
    }
  ],
  "links": {
    "first": "/api/mantenimientos?page=1",
    "last": "/api/mantenimientos?page=5",
    "prev": null,
    "next": "/api/mantenimientos?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 67
  }
}
```

### 2. Obtener Mantenimiento Específico
**GET** `/api/mantenimientos/{id}`

#### Respuesta Exitosa (200)
```json
{
  "data": {
    "id": 1,
    "vehiculo_id": 123,
    "tipo_servicio_id": 1,
    "proveedor": "Taller Automotriz ABC",
    "descripcion": "Cambio de aceite y filtro",
    "fecha_inicio": "2024-07-15",
    "fecha_fin": "2024-07-15",
    "kilometraje_servicio": 15000,
    "costo": "1500.00",
    "fecha_creacion": "2024-07-15T10:30:00.000000Z",
    "fecha_actualizacion": "2024-07-15T10:30:00.000000Z",
    "vehiculo": {
      "id": 123,
      "marca": "Toyota",
      "modelo": "Hilux",
      "anio": 2020,
      "placas": "ABC-123",
      "kilometraje_actual": 18500
    },
    "tipo_servicio": {
      "id": 1,
      "nombre_tipo_servicio": "Mantenimiento Preventivo"
    },
    "documentos": []
  }
}
```

### 3. Crear Nuevo Mantenimiento
**POST** `/api/mantenimientos`

#### Request Body
```json
{
  "vehiculo_id": 123,
  "tipo_servicio_id": 1,
  "proveedor": "Taller Automotriz ABC",
  "descripcion": "Cambio de aceite y filtro",
  "fecha_inicio": "2024-07-15",
  "fecha_fin": "2024-07-15",        // Opcional
  "kilometraje_servicio": 15000,
  "costo": 1500.00                  // Opcional
}
```

#### Validaciones
- `vehiculo_id`: Requerido, debe existir en la tabla vehículos
- `tipo_servicio_id`: Requerido, debe existir en catálogo de tipos de servicio
- `proveedor`: Opcional, máximo 255 caracteres
- `descripcion`: Requerido, texto detallado del servicio
- `fecha_inicio`: Requerida, formato YYYY-MM-DD
- `fecha_fin`: Opcional, formato YYYY-MM-DD, debe ser mayor o igual a fecha_inicio
- `kilometraje_servicio`: Requerido, número entero positivo
- `costo`: Opcional, número decimal con hasta 2 decimales

#### Respuesta Exitosa (201)
```json
{
  "data": {
    "id": 1,
    "vehiculo_id": 123,
    "tipo_servicio_id": 1,
    "proveedor": "Taller Automotriz ABC",
    "descripcion": "Cambio de aceite y filtro",
    "fecha_inicio": "2024-07-15",
    "fecha_fin": "2024-07-15",
    "kilometraje_servicio": 15000,
    "costo": "1500.00",
    "fecha_creacion": "2024-07-15T10:30:00.000000Z",
    "fecha_actualizacion": "2024-07-15T10:30:00.000000Z"
  },
  "message": "Mantenimiento creado exitosamente"
}
```

### 4. Actualizar Mantenimiento
**PUT** `/api/mantenimientos/{id}`

#### Request Body
Misma estructura que el POST, todos los campos son opcionales excepto aquellos que tengan validaciones específicas.

#### Respuesta Exitosa (200)
```json
{
  "data": {
    // ... datos actualizados del mantenimiento
  },
  "message": "Mantenimiento actualizado exitosamente"
}
```

### 5. Eliminar Mantenimiento (Soft Delete)
**DELETE** `/api/mantenimientos/{id}`

#### Respuesta Exitosa (200)
```json
{
  "message": "Mantenimiento eliminado exitosamente"
}
```

### 6. Restaurar Mantenimiento
**POST** `/api/mantenimientos/{id}/restore`

#### Respuesta Exitosa (200)
```json
{
  "data": {
    // ... datos del mantenimiento restaurado
  },
  "message": "Mantenimiento restaurado exitosamente"
}
```

### 7. Estadísticas de Mantenimientos
**GET** `/api/mantenimientos/stats`

#### Parámetros de Consulta Opcionales
```javascript
{
  "vehiculo_id": 123,     // Filtrar por vehículo específico
  "year": 2024,           // Filtrar por año
  "month": 7              // Filtrar por mes (requiere year)
}
```

#### Respuesta Exitosa (200)
```json
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
      },
      {
        "tipo_servicio": "Reparación Correctiva",
        "cantidad": 15,
        "costo_total": "40500.00"
      }
    ],
    "vehiculos_mas_mantenidos": [
      {
        "vehiculo_id": 123,
        "marca": "Toyota",
        "modelo": "Hilux",
        "placas": "ABC-123",
        "total_mantenimientos": 8,
        "costo_total": "18500.00"
      }
    ]
  }
}
```

### 8. Próximos Mantenimientos por Kilometraje
**GET** `/api/mantenimientos/proximos-por-kilometraje`

#### Parámetros de Consulta
```javascript
{
  "vehiculo_id": 123,     // Opcional, filtrar por vehículo
  "limite_km": 1000       // Opcional, límite de kilómetros para alertas (default: 1000)
}
```

#### Respuesta Exitosa (200)
```json
{
  "data": [
    {
      "vehiculo_id": 123,
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
```

## 🔧 Catálogo de Tipos de Servicio

### 1. Listar Tipos de Servicio
**GET** `/api/catalogo-tipos-servicio`

#### Respuesta Exitosa (200)
```json
{
  "data": [
    {
      "id": 1,
      "nombre_tipo_servicio": "Mantenimiento Preventivo",
      "fecha_creacion": "2024-07-15T10:30:00.000000Z",
      "fecha_actualizacion": "2024-07-15T10:30:00.000000Z"
    },
    {
      "id": 2,
      "nombre_tipo_servicio": "Reparación Correctiva",
      "fecha_creacion": "2024-07-15T10:30:00.000000Z",
      "fecha_actualizacion": "2024-07-15T10:30:00.000000Z"
    }
  ]
}
```

### 2. Crear Tipo de Servicio
**POST** `/api/catalogo-tipos-servicio`

#### Request Body
```json
{
  "nombre_tipo_servicio": "Mantenimiento de Emergencia"
}
```

#### Respuesta Exitosa (201)
```json
{
  "data": {
    "id": 3,
    "nombre_tipo_servicio": "Mantenimiento de Emergencia",
    "fecha_creacion": "2024-07-15T10:30:00.000000Z",
    "fecha_actualizacion": "2024-07-15T10:30:00.000000Z"
  },
  "message": "Tipo de servicio creado exitosamente"
}
```

### 3. Actualizar Tipo de Servicio
**PUT** `/api/catalogo-tipos-servicio/{id}`

### 4. Eliminar Tipo de Servicio
**DELETE** `/api/catalogo-tipos-servicio/{id}`

## 🔒 Permisos Requeridos

### Mantenimientos
- `crear_mantenimiento`: Para crear nuevos mantenimientos
- `ver_mantenimiento`: Para listar y ver mantenimientos
- `editar_mantenimiento`: Para actualizar mantenimientos
- `eliminar_mantenimiento`: Para eliminar mantenimientos
- `restaurar_mantenimiento`: Para restaurar mantenimientos eliminados

### Catálogo Tipos de Servicio
- `crear_catalogo_tipo_servicio`: Para crear tipos de servicio
- `ver_catalogo_tipo_servicio`: Para listar tipos de servicio
- `editar_catalogo_tipo_servicio`: Para actualizar tipos de servicio
- `eliminar_catalogo_tipo_servicio`: Para eliminar tipos de servicio

## ❌ Códigos de Error

### 400 - Bad Request
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "vehiculo_id": ["El campo vehiculo id es obligatorio."],
    "fecha_inicio": ["El campo fecha inicio no es una fecha válida."]
  }
}
```

### 401 - Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 - Forbidden
```json
{
  "message": "No tienes permisos para realizar esta acción."
}
```

### 404 - Not Found
```json
{
  "message": "Mantenimiento no encontrado."
}
```

### 422 - Unprocessable Entity
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "costo": ["El campo costo debe ser un número."],
    "kilometraje_servicio": ["El campo kilometraje servicio debe ser mayor que 0."]
  }
}
```

### 500 - Internal Server Error
```json
{
  "message": "Error interno del servidor."
}
```

## 📝 Ejemplos de Integración Frontend

### JavaScript/Fetch API

#### Listar Mantenimientos con Filtros
```javascript
const getMantenimientos = async (filters = {}) => {
  const queryParams = new URLSearchParams();
  
  if (filters.vehiculo_id) queryParams.append('vehiculo_id', filters.vehiculo_id);
  if (filters.fecha_inicio) queryParams.append('fecha_inicio', filters.fecha_inicio);
  if (filters.fecha_fin) queryParams.append('fecha_fin', filters.fecha_fin);
  if (filters.page) queryParams.append('page', filters.page);
  
  try {
    const response = await fetch(`/api/mantenimientos?${queryParams}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error fetching mantenimientos:', error);
    throw error;
  }
};
```

#### Crear Mantenimiento
```javascript
const createMantenimiento = async (mantenimientoData) => {
  try {
    const response = await fetch('/api/mantenimientos', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(mantenimientoData)
    });
    
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Error creating mantenimiento');
    }
    
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error creating mantenimiento:', error);
    throw error;
  }
};

// Ejemplo de uso
const nuevoMantenimiento = {
  vehiculo_id: 123,
  tipo_servicio_id: 1,
  proveedor: "Taller ABC",
  descripcion: "Cambio de aceite",
  fecha_inicio: "2024-07-15",
  kilometraje_servicio: 15000,
  costo: 1500.00
};

createMantenimiento(nuevoMantenimiento);
```

### React/TypeScript Example

```typescript
interface Mantenimiento {
  id: number;
  vehiculo_id: number;
  tipo_servicio_id: number;
  proveedor?: string;
  descripcion: string;
  fecha_inicio: string;
  fecha_fin?: string;
  kilometraje_servicio: number;
  costo?: number;
  vehiculo?: {
    id: number;
    marca: string;
    modelo: string;
    placas: string;
  };
  tipo_servicio?: {
    id: number;
    nombre_tipo_servicio: string;
  };
}

interface MantenimientoResponse {
  data: Mantenimiento[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

const MantenimientosList: React.FC = () => {
  const [mantenimientos, setMantenimientos] = useState<Mantenimiento[]>([]);
  const [loading, setLoading] = useState(true);
  const [currentPage, setCurrentPage] = useState(1);

  useEffect(() => {
    fetchMantenimientos();
  }, [currentPage]);

  const fetchMantenimientos = async () => {
    setLoading(true);
    try {
      const response = await getMantenimientos({ page: currentPage });
      setMantenimientos(response.data);
    } catch (error) {
      console.error('Error:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      {/* Render mantenimientos list */}
    </div>
  );
};
```

## 🔄 Consideraciones de Estado y Sincronización

### Soft Deletes
- Los mantenimientos eliminados no aparecen en las consultas normales
- Usar el endpoint `/restore` para recuperar registros eliminados
- Los registros eliminados mantienen sus relaciones intactas

### Validación de Datos
- Todos los inputs de texto se sanitizan automáticamente (XSS protection)
- Las fechas deben estar en formato ISO (YYYY-MM-DD)
- Los costos aceptan hasta 2 decimales
- El kilometraje debe ser un número entero positivo

### Paginación
- Por defecto se muestran 15 elementos por página
- Máximo 100 elementos por página
- La respuesta incluye metadata de paginación completa

### Filtros y Búsqueda
- Los filtros se pueden combinar
- La búsqueda por proveedor es parcial (LIKE)
- Los filtros por fecha son inclusivos

## 🔗 Relaciones con Otros Módulos

### Dependencias
- **Vehículos**: Cada mantenimiento pertenece a un vehículo
- **Catálogo Tipos de Servicio**: Define el tipo de mantenimiento
- **Usuarios**: Tracking de quién crea/modifica registros
- **Documentos**: Los mantenimientos pueden tener documentos asociados

### APIs Relacionadas
- `/api/vehiculos` - Para obtener lista de vehículos
- `/api/documentos` - Para gestionar documentos de mantenimiento
- `/api/usuarios` - Para información de usuarios

## 📋 Notas Importantes para el Desarrollo

1. **Autenticación**: Todos los endpoints requieren autenticación via Bearer token
2. **Permisos**: Verificar permisos específicos antes de mostrar opciones en el UI
3. **Validación**: Implementar validación del lado cliente que coincida con las reglas del backend
4. **Error Handling**: Manejar apropiadamente todos los códigos de error HTTP
5. **Loading States**: Implementar estados de carga para mejorar UX
6. **Sanitización**: El backend sanitiza automáticamente, pero considerar validación adicional en frontend

## 🚀 Roadmap de Integraciones Futuras

### Próximas Funcionalidades
- Notificaciones automáticas de mantenimientos próximos
- Integración con sistema de inventario de repuestos
- Reportes avanzados y analytics
- Integración con calendarios para agendar servicios
- Sistema de evaluación de proveedores

Esta documentación será actualizada conforme se agreguen nuevas funcionalidades al módulo de mantenimientos.
