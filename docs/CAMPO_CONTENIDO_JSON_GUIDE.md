# Guía del Campo `contenido` JSON en Documentos

## 📋 Descripción General

El campo `contenido` es un campo JSON flexible que permite almacenar información estructurada específica para cada tipo de documento. Esto permite extensibilidad sin modificar la estructura de la base de datos.

## 🏗️ Estructura Base

```json
{
  "metadata": {
    "version": "1.0",
    "created_by": "sistema|usuario",
    "last_modified": "2025-07-23T10:30:00Z"
  },
  "data": {
    // Contenido específico por tipo de documento
  },
  "validation": {
    "checksum": "opcional",
    "signature": "opcional"
  }
}
```

## 📂 Ejemplos por Tipo de Documento

### 1. **Licencias de Conducir**
```json
{
  "metadata": {
    "version": "1.0",
    "created_by": "admin",
    "last_modified": "2025-07-23T10:30:00Z"
  },
  "data": {
    "numero_licencia": "12345678",
    "categoria": "A1",
    "restricciones": ["Lentes correctivos"],
    "puntos_actuales": 20,
    "historial_infracciones": [
      {
        "fecha": "2024-01-15",
        "infraccion": "Exceso de velocidad",
        "puntos_descontados": 3
      }
    ],
    "renovacion_automatica": true
  }
}
```

### 2. **Seguros de Vehículos**
```json
{
  "metadata": {
    "version": "1.0",
    "created_by": "sistema",
    "last_modified": "2025-07-23T10:30:00Z"
  },
  "data": {
    "poliza_numero": "POL-2025-001234",
    "aseguradora": "Seguros ABC",
    "cobertura": {
      "responsabilidad_civil": 500000,
      "danos_materiales": 200000,
      "robo_total": true,
      "gastos_medicos": 50000
    },
    "deducible": 5000,
    "beneficiarios": [
      {
        "nombre": "Empresa Petrotekno",
        "porcentaje": 100
      }
    ],
    "contacto_emergencia": {
      "telefono": "800-123-4567",
      "email": "siniestros@segurosab.com"
    }
  }
}
```

### 3. **Certificados de Capacitación**
```json
{
  "metadata": {
    "version": "1.0",
    "created_by": "rrhh",
    "last_modified": "2025-07-23T10:30:00Z"
  },
  "data": {
    "institucion": "Instituto Técnico Petrolero",
    "curso": "Manejo de Equipos Pesados",
    "nivel": "Avanzado",
    "calificacion": 95,
    "instructor": "Ing. Juan Pérez",
    "competencias": [
      "Operación de excavadoras",
      "Mantenimiento preventivo",
      "Seguridad industrial"
    ],
    "horas_academicas": 120,
    "practicas_realizadas": 40,
    "certificacion_vigente": true
  }
}
```

### 4. **Permisos Ambientales**
```json
{
  "metadata": {
    "version": "1.0", 
    "created_by": "legal",
    "last_modified": "2025-07-23T10:30:00Z"
  },
  "data": {
    "numero_permiso": "AMB-2025-789",
    "autoridad_expedidora": "Secretaría del Medio Ambiente",
    "tipo_actividad": "Perforación exploratoria",
    "coordenadas": {
      "latitud": "19.4326",
      "longitud": "-99.1332"
    },
    "restricciones": [
      "Horario: 6:00 AM - 6:00 PM",
      "No operar en temporada de lluvias",
      "Monitoreo semanal de agua"
    ],
    "monitoreo": {
      "frecuencia": "semanal",
      "parametros": ["pH", "turbiedad", "hidrocarburos"],
      "responsable": "Laboratorio Certificado XYZ"
    },
    "renovacion_requerida": true
  }
}
```

### 5. **Contratos de Mantenimiento**
```json
{
  "metadata": {
    "version": "1.0",
    "created_by": "compras", 
    "last_modified": "2025-07-23T10:30:00Z"
  },
  "data": {
    "proveedor": "Servicios Técnicos SA",
    "numero_contrato": "CONT-MANT-2025-001",
    "servicios_incluidos": [
      "Mantenimiento preventivo",
      "Reparaciones menores",
      "Suministro de refacciones básicas"
    ],
    "frecuencia": "cada_3_meses",
    "valor_total": 150000,
    "forma_pago": "mensual",
    "garantia": "12 meses en reparaciones",
    "penalizaciones": {
      "retraso": "1% por día",
      "no_cumplimiento": "10% del valor"
    },
    "contacto_tecnico": {
      "nombre": "Ing. Carlos Mendoza",
      "telefono": "555-0123",
      "email": "cmendoza@serviciotecnico.com"
    }
  }
}
```

## 🔧 Implementación en el Código

### Validación en Request Classes

```php
// En StoreDocumentoRequest.php
public function rules(): array
{
    $rules = [
        'tipo_documento_id' => 'required|exists:catalogo_tipos_documento,id',
        'descripcion' => 'nullable|string|max:1000',
        'contenido' => 'nullable|array',
    ];

    // Validaciones específicas por tipo de documento
    if ($this->input('tipo_documento_id') == 1) { // Licencias
        $rules['contenido.data.numero_licencia'] = 'required|string|max:20';
        $rules['contenido.data.categoria'] = 'required|string|in:A1,A2,B1,B2,C';
        $rules['contenido.data.puntos_actuales'] = 'required|integer|min:0|max:20';
    }

    return $rules;
}
```

### Accessor en el Modelo

```php
// En el modelo Documento.php
protected $casts = [
    'contenido' => 'array',
    'fecha_vencimiento' => 'date',
];

public function getContenidoFormateadoAttribute(): string
{
    if (!$this->contenido) return 'Sin contenido';
    
    return match($this->tipoDocumento->nombre_tipo_documento) {
        'Licencia de Conducir' => $this->formatearLicencia(),
        'Seguro' => $this->formatearSeguro(),
        'Certificado' => $this->formatearCertificado(),
        default => 'Contenido no estructurado'
    };
}

private function formatearLicencia(): string
{
    $data = $this->contenido['data'] ?? [];
    return sprintf(
        "Licencia %s - Categoría %s - Puntos: %d",
        $data['numero_licencia'] ?? 'N/A',
        $data['categoria'] ?? 'N/A', 
        $data['puntos_actuales'] ?? 0
    );
}
```

## 📊 Consultas Útiles

### Buscar por contenido específico

```php
// Buscar licencias con pocos puntos
$licenciasRiesgo = Documento::whereJsonContains('contenido->data->puntos_actuales', '<', 5)
    ->whereHas('tipoDocumento', fn($q) => $q->where('nombre_tipo_documento', 'Licencia de Conducir'))
    ->get();

// Buscar seguros próximos a vencer
$segurosVencimiento = Documento::where('fecha_vencimiento', '<=', now()->addDays(30))
    ->whereJsonContains('contenido->data->renovacion_automatica', false)
    ->get();

// Buscar certificados por competencia
$certificadosExcavadora = Documento::whereJsonContains(
    'contenido->data->competencias', 
    'Operación de excavadoras'
)->get();
```

## 🛡️ Buenas Prácticas

### 1. **Validación de Estructura**
```php
// Siempre validar la estructura antes de guardar
if (isset($contenido['data'])) {
    $this->validarEstructuraData($contenido['data'], $tipoDocumento);
}
```

### 2. **Versionado**
```php
// Incluir siempre version en metadata
$contenido['metadata']['version'] = '1.0';
$contenido['metadata']['last_modified'] = now()->toISOString();
```

### 3. **Migración de Datos**
```php
// Para migrar datos existentes
public function migrarContenidoExistente()
{
    $documentos = Documento::whereNull('contenido')->get();
    
    foreach ($documentos as $documento) {
        $contenido = $this->construirContenidoDesdeDescripcion($documento);
        $documento->update(['contenido' => $contenido]);
    }
}
```

### 4. **Indexación para Consultas**
```sql
-- Crear índices para consultas frecuentes en JSON
CREATE INDEX idx_documento_contenido_licencia 
ON documentos USING GIN ((contenido->'data'->>'numero_licencia'));

CREATE INDEX idx_documento_contenido_puntos 
ON documentos USING GIN ((contenido->'data'->>'puntos_actuales'));
```

## 🔍 Ejemplos de Uso en Frontend

### Blade Templates
```php
@if($documento->contenido && isset($documento->contenido['data']))
    <div class="documento-detalle">
        @switch($documento->tipoDocumento->nombre_tipo_documento)
            @case('Licencia de Conducir')
                @include('documentos.partials.licencia', ['data' => $documento->contenido['data']])
                @break
            @case('Seguro')
                @include('documentos.partials.seguro', ['data' => $documento->contenido['data']])
                @break
        @endswitch
    </div>
@endif
```

### JavaScript/API
```javascript
// Actualizar contenido específico
const actualizarPuntosLicencia = async (documentoId, nuevosPuntos) => {
    const response = await fetch(`/api/documentos/${documentoId}`, {
        method: 'PATCH',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            'contenido.data.puntos_actuales': nuevosPuntos,
            'contenido.metadata.last_modified': new Date().toISOString()
        })
    });
    return response.json();
};
```

---

**Creado**: 23 de Julio de 2025  
**Versión**: 1.0  
**Responsable**: Backend Development Team
