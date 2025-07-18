# API Documentation - Sistema de Gestión de Documentos (DMS)

## Tabla de Contenidos
1. [Autenticación](#autenticación)
2. [Endpoints de Catálogo de Tipos de Documento](#endpoints-de-catálogo-de-tipos-de-documento)
3. [Endpoints de Documentos](#endpoints-de-documentos)
4. [Modelos de Datos](#modelos-de-datos)
5. [Códigos de Error](#códigos-de-error)
6. [Ejemplos de Integración Frontend](#ejemplos-de-integración-frontend)

---

## Autenticación

Todos los endpoints requieren autenticación vía **Laravel Sanctum**. Incluye el token en el header:

```http
Authorization: Bearer {token}
```

### Permisos Requeridos

| Acción | Permiso Requerido |
|--------|-------------------|
| Crear tipos de documento | `crear_tipos_documento` |
| Ver tipos de documento | `ver_tipos_documento` |
| Editar tipos de documento | `editar_tipos_documento` |
| Eliminar tipos de documento | `eliminar_tipos_documento` |
| Crear documentos | `crear_documentos` |
| Ver documentos | `ver_documentos` |
| Editar documentos | `editar_documentos` |
| Eliminar documentos | `eliminar_documentos` |

---

## Endpoints de Catálogo de Tipos de Documento

### 1. Listar Tipos de Documento

```http
GET /api/catalogo-tipos-documento
```

**Query Parameters:**
- `per_page` (opcional): Número de elementos por página (default: 15)
- `page` (opcional): Página actual (default: 1)
- `sort_by` (opcional): Campo de ordenamiento (default: created_at)
- `sort_order` (opcional): Dirección de ordenamiento (asc|desc, default: desc)
- `search` (opcional): Búsqueda por nombre
- `requiere_vencimiento` (opcional): Filtrar por si requiere vencimiento (true|false)
- `sin_paginar` (opcional): Retornar todos los resultados sin paginación (true|false)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "nombre_tipo_documento": "Licencia de Conducir",
        "descripcion": "Documento legal para conducir vehículos",
        "requiere_vencimiento": true,
        "created_at": "2025-07-18T10:00:00.000000Z",
        "updated_at": "2025-07-18T10:00:00.000000Z",
        "documentos_count": 5
      }
    ],
    "first_page_url": "http://localhost/api/catalogo-tipos-documento?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/api/catalogo-tipos-documento?page=1",
    "next_page_url": null,
    "path": "http://localhost/api/catalogo-tipos-documento",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

### 2. Crear Tipo de Documento

```http
POST /api/catalogo-tipos-documento
```

**Request Body:**
```json
{
  "nombre_tipo_documento": "Póliza de Seguro",
  "descripcion": "Documento de cobertura de seguro vehicular",
  "requiere_vencimiento": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "Tipo de documento creado exitosamente",
  "data": {
    "id": 2,
    "nombre_tipo_documento": "Póliza de Seguro",
    "descripcion": "Documento de cobertura de seguro vehicular",
    "requiere_vencimiento": true,
    "created_at": "2025-07-18T10:00:00.000000Z",
    "updated_at": "2025-07-18T10:00:00.000000Z"
  }
}
```

### 3. Ver Tipo de Documento Específico

```http
GET /api/catalogo-tipos-documento/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nombre_tipo_documento": "Licencia de Conducir",
    "descripcion": "Documento legal para conducir vehículos",
    "requiere_vencimiento": true,
    "created_at": "2025-07-18T10:00:00.000000Z",
    "updated_at": "2025-07-18T10:00:00.000000Z",
    "documentos_count": 5
  }
}
```

### 4. Actualizar Tipo de Documento

```http
PUT /api/catalogo-tipos-documento/{id}
```

**Request Body:**
```json
{
  "nombre_tipo_documento": "Licencia de Conducir Actualizada",
  "descripcion": "Descripción actualizada",
  "requiere_vencimiento": false
}
```

**Response:**
```json
{
  "success": true,
  "message": "Tipo de documento actualizado exitosamente",
  "data": {
    "id": 1,
    "nombre_tipo_documento": "Licencia de Conducir Actualizada",
    "descripcion": "Descripción actualizada",
    "requiere_vencimiento": false,
    "created_at": "2025-07-18T10:00:00.000000Z",
    "updated_at": "2025-07-18T11:00:00.000000Z"
  }
}
```

### 5. Eliminar Tipo de Documento

```http
DELETE /api/catalogo-tipos-documento/{id}
```

**Response:**
```json
{
  "success": true,
  "message": "Tipo de documento eliminado exitosamente"
}
```

---

## Endpoints de Documentos

### 1. Listar Documentos

```http
GET /api/documentos
```

**Query Parameters:**
- `per_page` (opcional): Número de elementos por página (default: 15)
- `page` (opcional): Página actual (default: 1)
- `sort_by` (opcional): Campo de ordenamiento (default: created_at)
- `sort_order` (opcional): Dirección de ordenamiento (asc|desc, default: desc)
- `search` (opcional): Búsqueda por descripción o tipo de documento
- `tipo_documento_id` (opcional): Filtrar por tipo de documento
- `vehiculo_id` (opcional): Filtrar por vehículo
- `personal_id` (opcional): Filtrar por personal
- `obra_id` (opcional): Filtrar por obra
- `estado_vencimiento` (opcional): Filtrar por estado (vigentes|vencidos|proximos_a_vencer)
- `dias_vencimiento` (opcional): Días para considerar próximo a vencer (default: 30)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "tipo_documento_id": 1,
        "descripcion": "Licencia de Juan Pérez",
        "ruta_archivo": "documentos/1642519200_licencia_juan_perez.pdf",
        "fecha_vencimiento": "2025-12-31",
        "vehiculo_id": 1,
        "personal_id": null,
        "obra_id": null,
        "mantenimiento_id": null,
        "created_at": "2025-07-18T10:00:00.000000Z",
        "updated_at": "2025-07-18T10:00:00.000000Z",
        "deleted_at": null,
        "estado": "vigente",
        "dias_hasta_vencimiento": 165,
        "esta_vencido": false,
        "tipo_documento": {
          "id": 1,
          "nombre_tipo_documento": "Licencia de Conducir",
          "descripcion": "Documento legal para conducir vehículos",
          "requiere_vencimiento": true
        },
        "vehiculo": {
          "id": 1,
          "marca": "Toyota",
          "modelo": "Hilux",
          "placas": "ABC-123",
          "n_serie": "1234567890"
        },
        "personal": null,
        "obra": null
      }
    ],
    "first_page_url": "http://localhost/api/documentos?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost/api/documentos?page=1",
    "next_page_url": null,
    "path": "http://localhost/api/documentos",
    "per_page": 15,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  },
  "meta": {
    "total_vigentes": 10,
    "total_vencidos": 2,
    "total_proximos_vencer": 3
  }
}
```

### 2. Crear Documento

```http
POST /api/documentos
```

**Request Body (multipart/form-data):**
```json
{
  "tipo_documento_id": 1,
  "descripcion": "Licencia renovada de Juan Pérez",
  "fecha_vencimiento": "2026-12-31",
  "vehiculo_id": 1,
  "archivo": "[FILE]"
}
```

**Nota:** Solo puede asociarse a UNA entidad a la vez (vehiculo_id O personal_id O obra_id O mantenimiento_id).

**Response:**
```json
{
  "success": true,
  "message": "Documento creado exitosamente",
  "data": {
    "id": 2,
    "tipo_documento_id": 1,
    "descripcion": "Licencia renovada de Juan Pérez",
    "ruta_archivo": "documentos/1642522800_licencia_renovada.pdf",
    "fecha_vencimiento": "2026-12-31",
    "vehiculo_id": 1,
    "personal_id": null,
    "obra_id": null,
    "mantenimiento_id": null,
    "created_at": "2025-07-18T11:00:00.000000Z",
    "updated_at": "2025-07-18T11:00:00.000000Z",
    "estado": "vigente",
    "dias_hasta_vencimiento": 530,
    "tipo_documento": {
      "id": 1,
      "nombre_tipo_documento": "Licencia de Conducir",
      "requiere_vencimiento": true
    },
    "vehiculo": {
      "id": 1,
      "marca": "Toyota",
      "modelo": "Hilux",
      "placas": "ABC-123"
    },
    "personal": null,
    "obra": null
  }
}
```

### 3. Ver Documento Específico

```http
GET /api/documentos/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "tipo_documento_id": 1,
    "descripcion": "Licencia de Juan Pérez",
    "ruta_archivo": "documentos/1642519200_licencia_juan_perez.pdf",
    "fecha_vencimiento": "2025-12-31",
    "vehiculo_id": 1,
    "personal_id": null,
    "obra_id": null,
    "mantenimiento_id": null,
    "created_at": "2025-07-18T10:00:00.000000Z",
    "updated_at": "2025-07-18T10:00:00.000000Z",
    "deleted_at": null,
    "estado": "vigente",
    "dias_hasta_vencimiento": 165,
    "esta_vencido": false,
    "tipo_documento": {
      "id": 1,
      "nombre_tipo_documento": "Licencia de Conducir",
      "descripcion": "Documento legal para conducir vehículos",
      "requiere_vencimiento": true
    },
    "vehiculo": {
      "id": 1,
      "marca": "Toyota",
      "modelo": "Hilux",
      "placas": "ABC-123",
      "n_serie": "1234567890"
    },
    "personal": null,
    "obra": null
  }
}
```

### 4. Actualizar Documento

```http
PUT /api/documentos/{id}
```

**Request Body (multipart/form-data):**
```json
{
  "descripcion": "Descripción actualizada",
  "fecha_vencimiento": "2027-12-31",
  "archivo": "[FILE_OPCIONAL]"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Documento actualizado exitosamente",
  "data": {
    "id": 1,
    "tipo_documento_id": 1,
    "descripcion": "Descripción actualizada",
    "ruta_archivo": "documentos/1642526400_archivo_actualizado.pdf",
    "fecha_vencimiento": "2027-12-31",
    "vehiculo_id": 1,
    "personal_id": null,
    "obra_id": null,
    "mantenimiento_id": null,
    "created_at": "2025-07-18T10:00:00.000000Z",
    "updated_at": "2025-07-18T12:00:00.000000Z",
    "estado": "vigente",
    "dias_hasta_vencimiento": 895
  }
}
```

### 5. Eliminar Documento

```http
DELETE /api/documentos/{id}
```

**Response:**
```json
{
  "success": true,
  "message": "Documento eliminado exitosamente"
}
```

### 6. Documentos Próximos a Vencer

```http
GET /api/documentos/proximos-a-vencer
```

**Query Parameters:**
- `dias` (opcional): Días para considerar próximo a vencer (default: 30)
- `per_page` (opcional): Número de elementos por página (default: 15)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 3,
        "descripcion": "Póliza próxima a vencer",
        "fecha_vencimiento": "2025-08-15",
        "dias_hasta_vencimiento": 28,
        "estado": "proximo_a_vencer",
        "tipo_documento": {
          "nombre_tipo_documento": "Póliza de Seguro"
        },
        "vehiculo": {
          "marca": "Ford",
          "modelo": "Ranger",
          "placas": "XYZ-789"
        }
      }
    ]
  },
  "meta": {
    "dias_configurados": 30,
    "total_proximos_vencer": 1
  }
}
```

### 7. Documentos Vencidos

```http
GET /api/documentos/vencidos
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 4,
        "descripcion": "Documento vencido",
        "fecha_vencimiento": "2025-06-01",
        "dias_hasta_vencimiento": -47,
        "estado": "vencido",
        "esta_vencido": true,
        "tipo_documento": {
          "nombre_tipo_documento": "Certificado"
        },
        "personal": {
          "nombre_completo": "María García"
        }
      }
    ]
  },
  "meta": {
    "total_vencidos": 1
  }
}
```

---

## Modelos de Datos

### CatalogoTipoDocumento

```typescript
interface CatalogoTipoDocumento {
  id: number;
  nombre_tipo_documento: string;
  descripcion?: string;
  requiere_vencimiento: boolean;
  created_at: string;
  updated_at: string;
  documentos_count?: number;
}
```

### Documento

```typescript
interface Documento {
  id: number;
  tipo_documento_id: number;
  descripcion?: string;
  ruta_archivo?: string;
  fecha_vencimiento?: string;
  vehiculo_id?: number;
  personal_id?: number;
  obra_id?: number;
  mantenimiento_id?: number;
  created_at: string;
  updated_at: string;
  deleted_at?: string;
  
  // Campos calculados
  estado: 'vigente' | 'proximo_a_vencer' | 'vencido';
  dias_hasta_vencimiento?: number;
  esta_vencido: boolean;
  
  // Relaciones
  tipo_documento?: CatalogoTipoDocumento;
  vehiculo?: Vehiculo;
  personal?: Personal;
  obra?: Obra;
}
```

### Vehiculo (referencia)

```typescript
interface Vehiculo {
  id: number;
  marca: string;
  modelo: string;
  placas: string;
  n_serie?: string;
}
```

### Personal (referencia)

```typescript
interface Personal {
  id: number;
  nombre_completo: string;
  categoria_id: number;
}
```

### Obra (referencia)

```typescript
interface Obra {
  id: number;
  nombre_obra: string;
  estatus: string;
  fecha_inicio: string;
  fecha_fin?: string;
}
```

---

## Códigos de Error

### Errores de Validación (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "tipo_documento_id": [
      "El tipo de documento es obligatorio."
    ],
    "fecha_vencimiento": [
      "La fecha de vencimiento es obligatoria para este tipo de documento."
    ],
    "multiple_associations": [
      "Un documento no puede estar asociado a múltiples entidades al mismo tiempo."
    ]
  }
}
```

### Errores de Permisos (403)

```json
{
  "success": false,
  "message": "No tienes permisos para realizar esta acción",
  "required_permission": "crear_documentos"
}
```

### Errores de Recurso No Encontrado (404)

```json
{
  "success": false,
  "message": "Recurso no encontrado"
}
```

### Errores del Servidor (500)

```json
{
  "success": false,
  "message": "Error interno del servidor",
  "error": "Detalles del error en modo debug"
}
```

---

## Ejemplos de Integración Frontend

### React/TypeScript Example

```typescript
// services/documentosApi.ts
import axios from 'axios';

const API_BASE = '/api';

interface DocumentoCreateRequest {
  tipo_documento_id: number;
  descripcion?: string;
  fecha_vencimiento?: string;
  vehiculo_id?: number;
  personal_id?: number;
  obra_id?: number;
  archivo?: File;
}

export const documentosApi = {
  // Listar documentos
  async getDocumentos(params: {
    page?: number;
    per_page?: number;
    search?: string;
    tipo_documento_id?: number;
    estado_vencimiento?: string;
  }) {
    const response = await axios.get(`${API_BASE}/documentos`, { params });
    return response.data;
  },

  // Crear documento
  async createDocumento(data: DocumentoCreateRequest) {
    const formData = new FormData();
    
    Object.entries(data).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value);
      }
    });

    const response = await axios.post(`${API_BASE}/documentos`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  // Actualizar documento
  async updateDocumento(id: number, data: Partial<DocumentoCreateRequest>) {
    const formData = new FormData();
    
    Object.entries(data).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        formData.append(key, value);
      }
    });

    const response = await axios.put(`${API_BASE}/documentos/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  // Eliminar documento
  async deleteDocumento(id: number) {
    const response = await axios.delete(`${API_BASE}/documentos/${id}`);
    return response.data;
  },

  // Documentos próximos a vencer
  async getProximosAVencer(dias: number = 30) {
    const response = await axios.get(`${API_BASE}/documentos/proximos-a-vencer`, {
      params: { dias }
    });
    return response.data;
  },

  // Documentos vencidos
  async getVencidos() {
    const response = await axios.get(`${API_BASE}/documentos/vencidos`);
    return response.data;
  }
};

// Tipos de documento
export const tiposDocumentoApi = {
  async getTipos(params: { sin_paginar?: boolean } = {}) {
    const response = await axios.get(`${API_BASE}/catalogo-tipos-documento`, { params });
    return response.data;
  },

  async createTipo(data: {
    nombre_tipo_documento: string;
    descripcion?: string;
    requiere_vencimiento: boolean;
  }) {
    const response = await axios.post(`${API_BASE}/catalogo-tipos-documento`, data);
    return response.data;
  }
};
```

### Vue.js/Composables Example

```typescript
// composables/useDocumentos.ts
import { ref, computed } from 'vue';
import { documentosApi } from '@/services/documentosApi';

export function useDocumentos() {
  const documentos = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const documentosVencidos = computed(() => 
    documentos.value.filter(doc => doc.esta_vencido)
  );

  const documentosProximosVencer = computed(() => 
    documentos.value.filter(doc => doc.estado === 'proximo_a_vencer')
  );

  async function loadDocumentos(filters = {}) {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await documentosApi.getDocumentos(filters);
      documentos.value = response.data.data;
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al cargar documentos';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function createDocumento(documentoData) {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await documentosApi.createDocumento(documentoData);
      // Recargar lista después de crear
      await loadDocumentos();
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Error al crear documento';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  return {
    documentos,
    loading,
    error,
    documentosVencidos,
    documentosProximosVencer,
    loadDocumentos,
    createDocumento
  };
}
```

### Manejo de Archivos

```typescript
// utils/fileUtils.ts
export const fileUtils = {
  // Validar tipo de archivo
  isValidFileType(file: File): boolean {
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
  },

  // Validar tamaño de archivo (máximo 10MB)
  isValidFileSize(file: File): boolean {
    const maxSize = 10 * 1024 * 1024; // 10MB en bytes
    return file.size <= maxSize;
  },

  // Generar URL para descargar archivo
  getFileDownloadUrl(rutaArchivo: string): string {
    return `/storage/${rutaArchivo}`;
  },

  // Formatear tamaño de archivo
  formatFileSize(bytes: number): string {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }
};
```

### Componente de Estados

```typescript
// components/DocumentoEstado.vue
<template>
  <div>
    <span :class="estadoClass">
      {{ estadoText }}
    </span>
    <small v-if="documento.dias_hasta_vencimiento !== null" class="text-muted">
      {{ diasText }}
    </small>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  documento: Documento;
}

const props = defineProps<Props>();

const estadoClass = computed(() => {
  switch (props.documento.estado) {
    case 'vigente':
      return 'badge bg-success';
    case 'proximo_a_vencer':
      return 'badge bg-warning';
    case 'vencido':
      return 'badge bg-danger';
    default:
      return 'badge bg-secondary';
  }
});

const estadoText = computed(() => {
  switch (props.documento.estado) {
    case 'vigente':
      return 'Vigente';
    case 'proximo_a_vencer':
      return 'Próximo a Vencer';
    case 'vencido':
      return 'Vencido';
    default:
      return 'Sin Estado';
  }
});

const diasText = computed(() => {
  const dias = props.documento.dias_hasta_vencimiento;
  if (dias === null) return '';
  
  if (dias > 0) {
    return `Vence en ${dias} días`;
  } else if (dias === 0) {
    return 'Vence hoy';
  } else {
    return `Vencido hace ${Math.abs(dias)} días`;
  }
});
</script>
```

---

## Notas Importantes

1. **Archivos**: Se almacenan en `storage/app/public/documentos/` y son accesibles vía `/storage/documentos/`
2. **Soft Deletes**: Los documentos eliminados mantienen `deleted_at` para auditoría
3. **Validaciones**: Un documento solo puede asociarse a una entidad (vehiculo, personal, obra o mantenimiento)
4. **Fechas de Vencimiento**: Solo obligatorias si el tipo de documento lo requiere
5. **Permisos**: Todos los endpoints requieren autenticación y permisos específicos
6. **Estados Calculados**: Los estados se calculan automáticamente basados en la fecha de vencimiento

---

## Changelog

### v1.0.0 (2025-07-18)
- ✅ Implementación inicial del Sistema de Gestión de Documentos
- ✅ CRUD completo para tipos de documento y documentos
- ✅ Manejo de archivos con validaciones
- ✅ Sistema de vencimientos con alertas
- ✅ Filtros y búsquedas avanzadas
- ✅ Integración con entidades existentes (vehículos, personal, obras)
- ✅ Permisos granulares por acción
- ✅ Soft deletes para auditoría
